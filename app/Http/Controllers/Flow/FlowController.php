<?php

namespace App\Http\Controllers\Flow;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DarkGhostHunter\FlowSdk\Flow;
use App\Models\Plans\PlanUserFlow;
use App\Models\Bills\Bill;
use App\Models\Plans\PlanUser;
use App\Models\Users\User;

class FlowController extends Controller
{
    public $flow;


    public function __construct()
    {
        $this->flow = Flow::make('sandbox', [
            'apiKey'    => '2BF50213-7407-4BDE-9EEA-461FL347859C',
            'secret'    => '44416dd27ebc608516a7190d6b6690ab1ec44138',
        ]);
        // $this->flow   = Flow::make('production', [
        //     'apiKey'    => '25F26959-CA01-4899-9216-8918538CL80F',
        //     'secret'    => 'a8c71eabb93099922ae0aec12acd62d606c1ca3e',
        // ]);
    }


    public function payFlow(PlanUserFlow $planuserflow)
    {
        $amount = $planuserflow->plan->amount;
        $fee = $amount*0.0396;
        $total = $amount + $fee;

      try {
        $paymentResponse = $this->flow->payment()->commit([
            'commerceOrder'     => $planuserflow->id,
            'subject'           => 'Contratar plan',
            'amount'            => round($total),
            'email'             => $planuserflow->user->email,
            'urlConfirmation'   => url('/').'/flow/confirm',
            'urlReturn'         => url('/').'/flow/return',
            'optional'          => [
                'Message' => 'Tu orden esta en proceso!'
            ]
        ]);
      }
      catch ( Exception $e) {
          //return $e->getMessage();
          return response()->json([
            'error' => $e->getMessage(),
        ]);
      }

      return response()->json([
          'url' => $paymentResponse->getUrl(),
      ]);

    }

    public function returnFlow(Request $request)
    {
      $payment = $this->flow->payment()->get($request->token);
      $paymentData = $payment->paymentData;
      $planUserflow = PlanUserFlow::find($payment->commerceOrder);
      $user = User::find($planUserflow->user_id);

      if($planUserflow->paid == 1){
        return view('flow.return');
      }


      if ($paymentData['date'] == null) {
        $planUserflow->paid = 3;
        $planUserflow->observations = 'Error fecha desde flow. Posiblemente error en el pago';
        $planUserflow->save();
        //return Redirect::route('orders.index')->withErrors(array('flow' =>'no se realizo el pago'));
        return view('flow.error');
      } else {

        $planUserflow->paid = 1;
        $planUserflow->save();
        $planUser = new PlanUser;
        $planUser->start_date = $planUserflow->start_date;
        $planUser->finish_date = $planUserflow->finish_date;
        $planUser->counter = $planUserflow->counter;
        $planUser->user_id = $planUserflow->user_id;
        $planUser->plan_id = $planUserflow->plan_id;
        if(count($user->plan_users()->where('plan_status_id',1)->get()) > 0){
          $planUser->plan_status_id = 3;
        } else {
          $planUser->plan_status_id = 1;
          $user->status_user = 1;
          $user->save();
        }

        if($planUser->save()){

          $bill = new Bill;
          $bill->payment_type_id = 6;
          $bill->plan_user_id = $planUser->id;
          $bill->date = today();
          $bill->start_date = $planUser->start_date;
          $bill->finish_date = $planUser->finish_date;
          $bill->amount = $paymentData['balance'];
          $bill->total_paid = $paymentData['amount'];
          $bill->save();

          $month = $bill->date->month;
          $year = $bill->date->year;
          $plan_id = $bill->plan_user->plan->id;
          $amount = $bill->amount;

            \DB::table('errors')->insert([
              'error' => 'entre returnFlow, userId: ' .  $user->id . ' - ' .
                          $user->full_name. 'status_user: ' . $user->status_user .  ', con plan planUserflow: ' . $planUserflow->id,
              'where' => 'FlowController',
              'created_at' => now(),
          ]);

          return view('flow.return');
        } else {
          $planUserflow->paid = 0;
          $planUserflow->save();
          return view('flow.error');
        }
      }



    }

    public function confirmFlow(Request $request)
    {
      $payment = $this->flow->payment()->get($request->token);
      $paymentData = $payment->paymentData;
      $planUserflow = PlanUserFlow::find($payment->commerceOrder);
      $user = User::find($planUserflow->user_id);

      if($planUserflow->paid == 1){
        return response()->json([
          'data' => 'no',
          ]);
      }

    if ($paymentData['date'] == null) {
        $planUserflow->paid = 3;
        $planUserflow->observations = 'Error fecha desde flow. Posiblemente error en el pago';
        $planUserflow->save();
        return response()->json([
          'data' => 'no',
          ]);
      } else {

        $planUserflow->paid = 1;
        $planUserflow->save();
        $planUser = new PlanUser;
        $planUser->start_date = $planUserflow->start_date;
        $planUser->finish_date = $planUserflow->finish_date;
        $planUser->counter = $planUserflow->counter;
        $planUser->user_id = $planUserflow->user_id;
        $planUser->plan_id = $planUserflow->plan_id;
        if(count($user->plan_users()->where('plan_status_id',1)->get()) > 0){
          $planUser->plan_status_id = 3;
        } else {
          $planUser->plan_status_id = 1;
          $user->status_user = 1;
          $user->save();
        }

        if($planUser->save()){


          $bill = new Bill;
          $bill->payment_type_id = 6;
          $bill->plan_user_id = $planUser->id;
          $bill->date = today();
          $bill->start_date = $planUser->start_date;
          $bill->finish_date = $planUser->finish_date;
          $bill->amount = $paymentData['balance'];
          $bill->total_paid = $paymentData['amount'];
          $bill->save();

          $month = $bill->date->month;
          $year = $bill->date->year;
          $plan_id = $bill->plan_user->plan->id;
          $amount = $bill->amount;

            \DB::table('errors')->insert([
              'error' => 'entre confirmFlow, userId: ' .  $user->id . ' - ' .
                          $user->full_name. 'status_user: ' . $user->status_user .
                          ', con plan planUserflow: ' . $planUserflow->id,
              'where' => 'FlowController',
              'created_at' => now(),
          ]);

          return response()->json([
            'data' => 'ok',
            ]);
        } else {
          $planUserflow->paid = 0;
          $planUserflow->save();
          return response()->json([
            'data' => 'no',
            ]);
        }
      }


      return response()->json([
        'data' => 'no',
        ]);
    }

    // }

    // public function notification($userToken, $title, $body)
    // {
    //     $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    //     $token= $userToken;


    //     $notification = [
    //         'body' => $body,
    //         'title' => $title,
    //         'sound' => true,
    //     ];

    //     $extraNotificationData = ["message" => $notification,"moredata" =>'dd'];

    //     $fcmNotification = [
    //         //'registration_ids' => $tokenList, //multple token array
    //         'to'        => $token, //single token
    //         'notification' => $notification,
    //         'data' => $extraNotificationData
    //     ];

    //     $headers = [
    //         'Authorization: key=AIzaSyAluA5QTnXgVFecuYnN_MRGCILiUv_CVeQ',
    //         'Content-Type: application/json'
    //     ];


    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL,$fcmUrl);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
    //     $result = curl_exec($ch);
    //     curl_close($ch);


    //     return true;
    // }




}
