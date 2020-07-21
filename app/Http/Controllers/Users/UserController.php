<?php

namespace App\Http\Controllers\Users;


use App\Models\Users\User;
use App\Models\Users\Alert;
use App\Models\Wods\Wod;
use App\Models\Clases\Reservation;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Carbon\Carbon;
use Auth;
use Storage;
//use Image;


class UserController extends ApiController
{
    /** [__construct description] */
    // public function __construct()
    // {
    //   parent::__construct();
    //   $this->middleware('can:view,user')->only('show');
    // }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //   $users = User::all();
    //   return $this->showAll($users);
    // }

    /**
     * Request for the auth user profile
     * @return [json] [return authenticated user]
     */
    public function profile()
    {
        $user = Auth::user();
        return $this->showOne($user, 200);
    }

    public function tutorial()
    {
        Auth::user()->update(['tutorial' => true]);
        return $this->successResponse('Seen', 200);
    }

    public function image(Request $request)
    {

      $user = Auth::user();

      if ($request->hasFile('image')) {

          \Cloudinary::config(array( 
            "cloud_name" => "asomic", 
            "api_key" => "848272583179274", 
            "api_secret" => "nmfb2gvEoKogFn3yza9briK4Yg4", 
            "secure" => true
          ));

          $response = \Cloudinary\Uploader::upload($request->file('image'),[
            "width"=>450, 
            "height"=>450, 
            "crop"=>"lfill"
          ]); 

          $user->avatar = $response['secure_url'];
          if($user->save()){
            return response()->json(['success' =>'foto guardada en '.$user->avatar], 200);
          } else {
            return response()->json(['error' =>'error guardar foto'], 200);
          }
          
      }
      else {
        return response()->json(['error' =>'erro en request'], 400);
      }

    }

    // public function assistance()
    // {
    // //  $reservations = Auth::user()->reservations(3)->get();
    // $reservations = Auth::user()->assistence()->whereRaw('MONTH(date) = 2')->count();
    // //dd($reservations);

    //   // foreach ($reservations as $key => $value) {
    //   //   $year =
    //   // }
    //   return response()->json([
    //     'label' => ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
    //     'data'  => [
    //       Auth::user()->assistence()->whereRaw('MONTH(date) = 1')->count(),
    //       Auth::user()->assistence()->whereRaw('MONTH(date) = 2')->count(),
    //       Auth::user()->assistence()->whereRaw('MONTH(date) = 3')->count(),
    //       Auth::user()->assistence()->whereRaw('MONTH(date) = 4')->count(),
    //       Auth::user()->assistence()->whereRaw('MONTH(date) = 5')->count(),
    //       Auth::user()->assistence()->whereRaw('MONTH(date) = 6')->count(),
    //       Auth::user()->assistence()->whereRaw('MONTH(date) = 7')->count(),
    //       Auth::user()->assistence()->whereRaw('MONTH(date) = 8')->count(),
    //       Auth::user()->assistence()->whereRaw('MONTH(date) = 9')->count(),
    //       Auth::user()->assistence()->whereRaw('MONTH(date) = 10')->count(),
    //       Auth::user()->assistence()->whereRaw('MONTH(date) = 11')->count(),
    //       Auth::user()->assistence()->whereRaw('MONTH(date) = 12')->count(),
    //      ],

    //   ], 200);
    // }

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
      $user_plans = Auth::user()->plan_users()->orderBy('finish_date', 'desc')->get();
      return $this->showAll($user_plans, 200);
    }

    public function actualPlan()
    {
      $user_plan = Auth::user()->plan_users()->where('plan_status_id','!=',3)->orderBy('finish_date', 'desc')->first();
      return $this->showOne($user_plan, 200);
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
      // $confirmation = [];
      // $has_confirmation = (bool)false;
      $inapp_notification = [];
      $has_inapp_notification = (bool)false;

      //has confirmation
      // $clase = Auth::user()->clases->where('date',today())->first();
      // if($clase)
      // {
      //   $reservation = Reservation::where('user_id',Auth::user()->id)->where('clase_id',$clase->id)->where('reservation_status_id',1)->first();
      //   if($reservation) {
      //     $confirmation = [
      //       'start' => (string)date('H:i', strtotime($clase->start_at)),
      //       'end' => (string)date('H:i', strtotime($clase->finish_at)),
      //       'clase_id' => (string)$clase->id

      //     ];
      //     $has_confirmation = (bool)true;
      //   }
      // }

      $inapp_notification = Alert::where('from','<=', today())->where('to','>=',today())->get();
      if(count($inapp_notification)>0){
        $has_inapp_notification = (bool)true;
      }

      $alerts = [
        // 'has_confirmation' => $has_confirmation,
        // 'confirmation' => $confirmation,
        'has_inapp_notification' =>  $has_inapp_notification,
        'inapp_notification' =>  $inapp_notification,
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
          'status' => (int)$reservation->pivot->reservation_status_id,
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

    public function fcmToken(Request $request)
    {

      Auth::user()->fcm_token = $request->fcmtoken;
      if(Auth::user()->save()){
        return response()->json('guardado-'.Auth::user()->fcm_token.' enviado:'.$request->fcmtoken.'request:'.$request->toJson(), 200);
      } else {
        return response()->json('no guardado', 401);
      }

    }

    public function fcmTokenGet($token)
    {

      Auth::user()->fcm_token = $token;
      if(Auth::user()->save()){
        return response()->json('guardado'.Auth::user()->fcm_token, 200);
      } else {
        return response()->json('no guardado', 401);
      }

    }

    // public function checkAuth($token)
    // {
    //   return response()->json('ok', 200);
    // }

}
