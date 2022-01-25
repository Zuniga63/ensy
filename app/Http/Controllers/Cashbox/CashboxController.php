<?php

namespace App\Http\Controllers\Cashbox;

use App\Http\Controllers\Controller;
use App\Models\Cashbox;
use App\Models\CashboxTransaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class CashboxController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    /**
     * Se recuperan las cajas con el ultimo cierre si lo tuviere.
     * De los cierres solo se recuperan la base y la fecha.
     */
    $boxs = Cashbox::orderBy('order')
      ->with(['closures' => function ($query) {
        $query->orderBy('closing_date', 'DESC')
          ->select(['id', 'cashbox_id', 'closing_date as closingDate', 'new_base as base'])
          ->limit(1);
      }])
      ->get(['id', 'name', 'code', 'slug']);

    /**
     * Se transforman cada uno de los elementos para agregar
     * los datos requeridos por la vista
     */
    $boxs->map(function ($box, $key) {
      $box->base = 0;
      $box->lastClosure = null;
      $box->balanceIsWrong = false;

      $this->getCashAmount($box);
      $this->formatCashProperties($box);
      $this->validateBoxBalance($box);

      //Se elimina el arreglo de los closures
      unset($box->closures);
      return $box;
    });

    return Inertia::render('Cashbox/Index', compact('boxs'));
  }


  /**
   * Este metodo realiza las consultas requeridas para calular
   * el saldo de la caja, la suma de los ingresos y los egresos
   * @param \App\Models\Cashbox $box Instacia del modelo a mutar
   * @return \App\Models\Cashbox
   */
  protected function getCashAmount(Cashbox $box)
  {
    //Se recupera el saldo global
    $box->loadSum('transactions as balance', 'amount');

    if ($box->closures->isEmpty()) {
      /**
       * Si el arreglo de los cierres está vacío, se suman los
       * ingresos y los egresos desde el origen de los tiempos.
       */
      $box->loadSum([
        'transactions as incomes' => function (Builder $query) {
          $query->where('amount', '>=', 0);
        },
        'transactions as expenses' => function (Builder $query) {
          $query->where('amount', '<', 0);
        }
      ], 'amount');
    } else {
      /**
       * Si el arreglo no está vacío, entonces solo se recupera la suma
       * de los ingresos y de los egresos desde la fecha del ultimo cierre.
       */
      $box->base = $box->closures[0]->base;
      $box->lastClosure = $box->closures[0]->closingDate;

      $date = $box->lastClosure;

      $box->loadSum([
        'transactions as incomes' => function (Builder $query) use ($date) {
          $query->where('amount', '>=', 0)
            ->where('transaction_date', '>=', $date);
        },
        'transactions as expenses' => function (Builder $query) use ($date) {
          $query->where('amount', '<', 0)
            ->where('transaction_date', '>=', $date);;
        }
      ], 'amount');
    }

    return $box;
  }

  /**
   * Se encarga de convertir en float los valores del saldo, los ingresos y los egresos
   * de la caja.
   * @param \App\Models\Cashbox $box Instacia del modelo a mutar
   * @return \App\Models\Cashbox
   */
  protected function formatCashProperties(Cashbox $box)
  {
    $box->base = $box->base ? floatval($box->base) : 0;
    $box->balance = $box->balance ? floatval($box->balance) : 0;
    $box->incomes = $box->incomes ? floatval($box->incomes) : 0;
    $box->expenses = $box->expenses ? floatval($box->expenses) : 0;

    return $box;
  }

  /**
   * Verifica que el saldo real de la caja coincida con el saldo calculado.
   * @param \App\Models\Cashbox $box Instacia del modelo a mutar
   * @return \App\Models\Cashbox
   */
  protected function validateBoxBalance(Cashbox $box)
  {
    $balanceCalulated = $box->base + $box->incomes + $box->expenses;
    $diff = abs($box->balance - $balanceCalulated);
    $box->balanceIsWrong = $diff > 0.00001;

    return $box;
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return Inertia::render('Cashbox/Create');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $rules = [
      'name' => 'required|string|min:3|max:50|unique:cashbox,name',
      'slug' => 'required|string|min:3|max:50|unique:cashbox,slug',
      'code' => 'nullable|string|max:20|unique:cashbox,code',
    ];

    $attributes = [
      'name' => 'nombre',
      'slug' => 'slug',
      'code' => 'codigo'
    ];

    $request->validate($rules, [], $attributes);
    $inputs = $request->all();
    //Se recupera el numero de cajas
    $order = Cashbox::count();
    $order = $order ? $order + 1 : 1;
    $inputs = array_merge($inputs, ['order' => $order]);
    Cashbox::create($inputs);

    return Redirect::route('cashbox.index')->with('message', "Caja Creada");
  }

  public function storeTransaction(Request $request, Cashbox $cashbox)
  {
    $type = 'ingreso';
    //Se recuperan los campos dl formulario
    $inputs = $request->all();
    $rules = [
      'description' => 'required|string|min:3|max:255',
      'amount' => 'required|numeric|min:100',
      'type' => 'required|in:expense,income',
      'setDate' => 'required|boolean',
      'setTime' => 'required|boolean'
    ];

    //Se establecen los nombres de los atributos
    $attributes = [
      'date' => 'fecha',
      'time' => 'hora',
      'description' => 'descripción',
      'amount' => 'importe',
      'type' => 'tipo'
    ];

    if($inputs['setDate']){
      $rules['date'] = 'required|date_format:Y-m-d';
      if($inputs['setTime']){
        $rules['time'] = 'required|date_format:H:s';
      }
    }

    $request->validate($rules, [], $attributes);

    $transaction = new CashboxTransaction();
    //Se establece la fecha de la transacción
    if($inputs['setDate']){
      $date = $inputs['date'];
      if($inputs['setTime']){
        $time = $inputs['time'];
        $transaction->transaction_date = Carbon::createFromFormat('Y-m-d H:s', "$date $time");
      }else{
        $transaction->transaction_date = Carbon::createFromFormat('Y-m-d', $date)->endOfDay();
      }
    }else{
      $transaction->transaction_date = Carbon::now();
    }

    $transaction->description = $inputs['description'];
    $transaction->amount = floatval($inputs['amount']);
    
    if($inputs['type'] === 'expense'){
      $transaction->amount = $transaction->amount * -1;
      $type = "egreso";
    }

    $cashbox->transactions()->save($transaction);


    $message = "Se ha guardado el $type en la caja \"$cashbox->name\"";                            //Mensaje para el usuario
    return Redirect::route('cashbox.show', $cashbox->slug)->with('message', $message);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Cashbox  $cashbox
   * @return \Illuminate\Http\Response
   */
  public function show(Cashbox $cashbox)
  {
    $cashbox->load(['transactions' => function ($query) {
      $query->select([
        'id',
        'cashbox_id',
        'transaction_date as date',
        'description',
        'amount',
        'code',
        'transfer',
        'blocked',
        'created_at as createdAt',
        'updated_at as updatedAt',
      ])
        ->orderBy('transaction_date', 'asc')
        ->orderBy('created_at', 'asc');
    }])->loadSum('transactions as balance', 'amount');

    $balance = 0;
    $cashbox->transactions->map(function ($item) use (&$balance) {
      $item->amount = floatval($item->amount);
      $item->balance = $balance + $item->amount;
      $balance += $item->amount;
      return $item;
    });
    return Inertia::render('Cashbox/Show', compact('cashbox'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Cashbox  $cashbox
   * @return \Illuminate\Http\Response
   */
  public function edit(Cashbox $cashbox)
  {
    return Inertia::render('Cashbox/Edit', compact('cashbox'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Cashbox  $cashbox
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Cashbox $cashbox)
  {
    $rules = [
      'name' => 'required|string|min:3|max:50|unique:cashbox,name,' . $cashbox->id,
      'slug' => 'required|string|min:3|max:50|unique:cashbox,slug,' . $cashbox->id,
      'code' => 'nullable|string|max:20|unique:cashbox,code,' . $cashbox->id,
      'order' => 'required|integer|min:1|max:255'
    ];

    $attributes = [
      'name' => 'nombre',
      'slug' => 'slug',
      'code' => 'codigo',
      'order' => 'orden'
    ];

    $request->validate($rules, [], $attributes);
    $inputs = $request->all();

    //Recupero el numero de cajas almacenada
    $count = Cashbox::count();
    $order = $inputs['order'];

    DB::beginTransaction();
    if ($count && $order && $order !== $cashbox->order) {
      $order = $order > $count ? $count : $order;
      //Todas las cajas con numero de ordenacion posterior al actual deben bajar uno
      Cashbox::orderBy('order')->where('order', '>', $cashbox->order)->decrement('order');
      //Todas las cajas con numero de ordenacion posterior al nuevo orden deben incrementar uno
      Cashbox::orderBy('order')->where('order', '>=', $order)->increment('order');

      $cashbox->order = $order;
    }

    //Se actualiza la caja
    $cashbox->name = $inputs['name'];
    $cashbox->slug = $inputs['slug'];
    $cashbox->code = $inputs['code'];
    $cashbox->save();

    DB::commit();

    return Redirect::route('cashbox.index')->with('message', "Caja Actualizada");
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Cashbox  $cashbox
   * @return \Illuminate\Http\Response
   */
  public function destroy(Cashbox $cashbox)
  {
    $message = null;
    $status = false;
    //Se recupera el saldo de la caja
    try {
      $cashbox->loadSum('transactions as balance', 'amount');
      $canDelete = intval($cashbox->balance) === 0 ? true : false;
      if ($canDelete) {
        /**
         * TODO: Agregar codigo que disminuye en uno todos los
         * order de las cajas posteriores
         */
        DB::beginTransaction();
        //Se actualiza el orden de la cajas que van despues
        Cashbox::where('order', '>', $cashbox->order)->decrement('order');

        //Se elimina la caja actual
        $cashbox->delete();
        DB::commit();

        $message = "La caja <span class=\"font-bold\">\"$cashbox->name\"</span> se ha borrado de la base de datos y sus transacciones solo son visibles desde el ambito general.";
        $status = true;
      } else {
        $message = "No se puede eliminar cajas con fondos.";
      }
    } catch (\Throwable $th) {
      $message = "Errores internos, comuniquese con el adminsitrador.";
    }

    $result = [
      'ok' => $status,
      'message' => $message
    ];

    return Redirect::route('cashbox.index')->with('message', $result);
  }

  public function destroyTransaction(Cashbox $cashbox, CashboxTransaction $cashbox_transaction){
    $ok = false;
    $message = null;

    if($cashbox_transaction->blocked){
      $message = "Esta transacción no se puede eliminar porque está bloqueda.";
    }else{
      try {
        $cashbox_transaction->delete();
        $ok = true;
      } catch (\Throwable $th) {
        $message = "Por problemas internos no se pudo eliminar. Intentelo nuevamente mas tarde.";
      }

    }
    $result = [
      'ok' => $ok,
      'message' => $message
    ];
    return Redirect::route('cashbox.show', $cashbox->slug)->with('message', $result);
  }
}
