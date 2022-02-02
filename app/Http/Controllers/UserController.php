<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class UserController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    /** @var User */
    $users = User::orderBy('name')->with([
      'sessions' => function ($query){
        $query->orderBy('last_activity', 'DESC')->select(['user_id', 'last_activity as lastActivity']);
      }
    ])->get();
    return Inertia::render('Users/Index', compact('users'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
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
      'name' => 'required|string|min:3|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8|confirmed'
    ];

    $parameters = [
      'name' => 'nombre',
      'email' => 'correo electronico',
      'password' => 'contraseña',
      'password_confirmation' => 'confirmación de contraseña'
    ];

    $request->validate($rules, [], $parameters);
    $inputs = $request->all();
    $user = new User;

    $user->name = $inputs['name'];
    $user->email = $inputs['email'];
    $user->password = Hash::make($inputs['password']);
    $user->save();

    $message = [
      'userName' => $user->name,
      'email' => $user->email
    ];

    return Redirect::route('users.index')->with('message', $message);
    
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\user  $user
   * @return \Illuminate\Http\Response
   */
  public function show(user $user)
  {
    dd($user);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\user  $user
   * @return \Illuminate\Http\Response
   */
  public function edit(user $user)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\user  $user
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, user $user)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\user  $user
   * @return \Illuminate\Http\Response
   */
  public function destroy(user $user)
  {
    $actualUSer = Auth::user();
    $message = null;

    if($actualUSer->id === 1 && $actualUSer->id !== $user->id){
      $user->deleteProfilePhoto();
      $user->tokens->each->delete();
      $user->delete();
    }else if($user->id === 1){
      $message = "Este usaurio no se puede eliminar por este medio.";
    }else{
      $message = "No puedes eliminar usuarios.";
    }

    return Redirect::route('users.index')->with('message', $message);
  }
}
