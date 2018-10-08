<?php

namespace App\Http\Controllers\Users;

use Auth;
use App\Models\Users\User;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class UserController extends ApiController
{
    /** [__construct description] */
    public function __construct()
    {
      parent::__construct();
      // $this->middleware('can:view,user')->only('show');
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $users = User::all();
      return $this->showAll($users);
    }

    public function profile()
    {
        $user = Auth::user();
        return $this->showOne($user, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Users\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
      return $this->showOne($user, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Users\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
      $user->update($request->all());
      return $this->showOne($user, 200);
    }

    /**
     * [logout description]
     * @return [type] [description]
     */
    public function logout()
    {
      if (Auth::check()) {
          Auth::user()->token()->revoke();
          return $this->successResponse('Sesion finalizada', 200);
          // return response()->json(['success' =>'logout_success'],200);
      }else{
          return $this->errorResponse('Error al intentar cerrar sesion', 500);
          // return response()->json(['error' =>'api.something_went_wrong'], 500);
      }
    }
}

  // public function profile()
  // {
  //     $user = Auth::user();
  //     return response()->json(compact('user'), 200);
  // }
