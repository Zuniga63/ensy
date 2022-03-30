<?php

namespace App\Http\Controllers;

use App\Models\DailyActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DailyActivityController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $dailyActivities = DailyActivity::orderBy('start_date', 'DESC')
      ->where('start_date', '>=', Carbon::now()->subDay()->startOfDay())
      ->get();
    return Inertia::render("DailyActivity/Index", compact('dailyActivities'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $rules = $this->getRules();
    $attr = $this->getAttr();
    $messages = $this->getMessages();
    $request->validate($rules, $messages, $attr);

    /**
     * @var DailyActivity
     */
    $activity = new DailyActivity();
    $activity->start_date = $request->input('fromDate');
    $activity->end_date = $request->input('toDate');
    $activity->title = $request->input('title');
    $activity->description = $request->input('description');
    $activity->save();

    return $activity;
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\DailyActivity  $dailyActivity
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, DailyActivity $dailyActivity)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\DailyActivity  $dailyActivity
   * @return \Illuminate\Http\Response
   */
  public function destroy(DailyActivity $dailyActivity)
  {
    $dailyActivity->delete();
    return ['ok' => true];
  }

  /**
   * Metodo para la obtención de las reglas de validación
   */
  protected function getRules()
  {
    return [
      'fromDate' => 'required|string|date_format:Y-m-d H:i',
      'toDate' => 'required|string|date_format:Y-m-d H:i|after:fromDate',
      'title' => 'required|string|min:3|max:45',
      'description' => 'nullable|string|max:255',
    ];
  }

  /**
   * Establece los nombres personalizados de los atributos del
   * formulario.
   */
  protected function getAttr()
  {
    return [
      'fromDate' => 'inicio de actividad',
      'toDate' => 'finalización',
      'title' => 'titulo',
      'description' => 'descripción'
    ];
  }

  protected function getMessages()
  {
    return [
      'fromDate.date_format' => 'La fecha tiene un formato invalido',
      'toDate.date_format' => 'La fecha tiene un formato invalido',
    ];
  }
}
