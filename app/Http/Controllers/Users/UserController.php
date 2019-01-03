<?php

namespace App\Http\Controllers\Users;

use Auth;
use App\Models\Users\User;
use App\Models\Wods\Wod;
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

    public function image(Request $request)
    {

      $user = Auth::user();

      if ($request->hasFile('image')) {

          request()->file('image')->storeAs('public/users', $user->id.$user->first_name.'.jpg');
          $user->avatar = url('/').'/storage/users/'.$user->id.$user->first_name.'.jpg';
          $user->save();
          return response()->json(['success' =>'Sesion finalizada'], 200);
      }
      else {
        return response()->json(['error' =>'nooooooooooooooo'], 400);
      }

    }

    public function assistance()
    {
    //  $reservations = Auth::user()->reservations(3)->get();
    $reservations = Auth::user()->assistence()->whereRaw('MONTH(date) = 2')->count();
    //dd($reservations);

      // foreach ($reservations as $key => $value) {
      //   $year =
      // }
      return response()->json([
        'label' => ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
        'data'  => [
          Auth::user()->assistence()->whereRaw('MONTH(date) = 1')->count(),
          Auth::user()->assistence()->whereRaw('MONTH(date) = 2')->count(),
          Auth::user()->assistence()->whereRaw('MONTH(date) = 3')->count(),
          Auth::user()->assistence()->whereRaw('MONTH(date) = 4')->count(),
          Auth::user()->assistence()->whereRaw('MONTH(date) = 5')->count(),
          Auth::user()->assistence()->whereRaw('MONTH(date) = 6')->count(),
          Auth::user()->assistence()->whereRaw('MONTH(date) = 7')->count(),
          Auth::user()->assistence()->whereRaw('MONTH(date) = 8')->count(),
          Auth::user()->assistence()->whereRaw('MONTH(date) = 9')->count(),
          Auth::user()->assistence()->whereRaw('MONTH(date) = 10')->count(),
          Auth::user()->assistence()->whereRaw('MONTH(date) = 11')->count(),
          Auth::user()->assistence()->whereRaw('MONTH(date) = 12')->count(),
         ],

      ], 200);
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

    public function plans()
    {
      $user_plans = Auth::user()->plan_users;
      return $this->showAll($user_plans, 200);
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
            'start' => (string)date('H:i', strtotime($clase->start_at)),
            'end' => (string)date('H:i', strtotime($clase->finish_at)),
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

    public function today()
    {
      $reservationHas = false;
      $reservation = Auth::User()->clases()->where('date',today())->first();
      $todayReservation = [];

      $wodHas = false;
      $wod = Wod::where('date',today())->first();
      $todayWod = [];


      if($reservation){
        $reservationHas = true;
        $todayReservation = [
          'id' => (int)$reservation->id,
          'start' => (string)date('H:i', strtotime($reservation->start_at)),
          'end' => (string)date('H:i', strtotime($reservation->finish_at)),
          'href' => (string)route('clases.show', ['clase' => $reservation->id]),
        ];
      }


      if($wod)
      {
        $wodHas = true;
        $todayWod = [
            'warmup' => (string)$wod->stage(1)->description,
            'skill' => (string)$wod->stage(2)->description,
            'wod' => (string)$wod->stage(3)->description,
        ];
      }

      $today = [
        'date' => today()->format('Y-m-d'),
        'dateHuman' =>  (string) ucfirst(today()->formatLocalized('%A %d')).' de '. ucfirst(today()->formatLocalized('%B')) ,
        'wod' => [
          'has' => (bool)$wodHas,
          'stages' => $todayWod,
        ],
        'auth_reservation' => [
          'has' => (bool)$reservationHas,
          'reservation' => $todayReservation ,
        ]
      ];

      return response()->json(['data' => $today ], 200);

    }


}
