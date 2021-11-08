<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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
    //
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
    //
  }
}
