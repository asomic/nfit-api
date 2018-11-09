<?php

namespace App\Http\Controllers\Users;

use Auth;
use App\Models\Users\User;
use App\Models\Clases\Reservation;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class UserController extends ApiController
{
    /** [__construct description] */
    public function __construct()
    {
      parent::__construct();
      $this->middleware('can:view,user')->only('show');
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

    /**
     * Request for the auth user profile
     * @return [json] [return authenticated user]
     */
    public function profile()
    {
        $user = Auth::user();
        return $this->showOne($user, 200);
    }

    // *
    //  * Display the specified resource.
    //  *
    //  * @param  \App\Models\Users\User  $user
    //  * @return \Illuminate\Http\Response
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
     * Revoke the token to the auth user
     * @return json with good or bad response
     */
    public function logout()
    {
      if (Auth::check()) {
          Auth::user()->token()->revoke();
          return response()->json(['success' =>'Sesion finalizada'], 200);
      }else{
          return response()->json(['error' =>'api.something_went_wrong'], 500);
      }
    }

    public function clases()
    {
      $clases = Auth::user()->clases->where('date','<=',today());
      return $this->showAll($clases);
    }

    public function alerts()
    {
      $alerts = [];
      $confirmation = [];
      $has_confirmation = (bool)false;

      $clase = Auth::user()->clases->where('date',today())->first();
      if($clase)
      {
        $reservation = Reservation::where('user_id',Auth::user()->id)->where('clase_id',$clase->id)->where('reservation_status_id',1)->first();
        if($reservation) {
          $confirmation = [
            'start' => (string)$clase->start_at,
            'end' => (string)$clase->finish_at,
            'clase_id' => (string)$clase->id

          ];
          $has_confirmation = (bool)true;
        }
      }


      $alerts = [
        'has_confirmation' => $has_confirmation,
        'confirmation' => $confirmation,
      ];

      return response()->json(['data' => $alerts ], 200);
    }
}

  // public function profile()
  // {
  //     $user = Auth::user();
  //     return response()->json(compact('user'), 200);
  // }
