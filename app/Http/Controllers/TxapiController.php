<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TxapiController extends Controller
{
    //

public static function tx_api_request($id){
  //retrieve the initiated transaction
  $tx_detail=DB::table('tx_api_refs')->find($id);

  //check if transaction was not processed already
  if($tx_detail->tx_status!=="new"||$tx_detail->sync_status!=="no")
   return null;

  //Fetch its related api channelt
    $channel_detail=DB::table('tx_accounts')->where('acc_category','=','admin')
                                            ->where('currency','=',$tx_detail->currency_source)
//                                            ->where('tx_value_type','=',($tx_detail->tx_channel==="transit")?"cash":$tx_detail->tx_value_type)  // make transit channel use cash tx_value_type
                                            ->where('tx_channel','=',$tx_detail->tx_channel)
                                            ->where('active_status','=','active')
                                            ->get();
                                            

   $channel_detail=$channel_detail[0]??$channel_detail;
   $api=$channel_detail->acc_name;

   //Choose which api service should handle the enitity
   if($api==="easypay UGX")
     {
      if($tx_detail->tx_type==="deposit")
        return self::flutterwave_request_deposit($tx_detail);
      else
        return self::flutterwave_request_withdraw($tx_detail);
      //self::easypayug_request($tx_detail);
     }
   if($api==="RELWORX")
     {
      if($tx_detail->tx_type==="deposit")
        return self::relworks_request_deposit($tx_detail);
      else
        return self::relworks_request_withdraw($tx_detail);
     }
   else if($api==="cash UGX")
     self::cashug_request($tx_detail);
   else if($api==="bank UGX")
     self::cashug_request($tx_detail);    //self::bankug_request($tx_detail);
   else if($api==="provider UGX")
     self::cashug_request($tx_detail);
   else if($api==="transit UGX")
     self::cashug_request($tx_detail);
 }

//API proxied through ZHURIS payments gateway
public static function easypayug_request_zhuris($tx_detail){
    //flag the transaction as pending
    $mobile_phone_number=(MethodsController::class)::mobile_phone_maker(['mobile'=>$tx_detail->ref_id,'currency'=>$tx_detail->currency_destination]);
    $affected=DB::table('tx_api_refs')->where('id','=',$tx_detail->id)->update([ 'tx_status'=>'pending' ]);
    $tx_id=$tx_detail->tx_id;
    $source=$mobile_phone_number;
    $destination=$mobile_phone_number;
    $amount=$tx_detail->ref_amount;
    $param1="";
    $param2="";
    $param5="";
    $tx_type=$tx_detail->tx_type;
    $base_url=config('api_config.base_url');
    return $rslt=self::api_payline_zhuris($tx_id,$source,$destination,$amount,$param1,$param2,$param5,$tx_type,$base_url);
}
//Send mobile money trasnction request to ZHURIS
public static function api_payline_zhuris($pay_id,$source,$destination,$amount,$param1,$param2,$param5,$tx_type,$base_url)
   {
    $rslt="";
    $data['tx_id']=$pay_id;
    $data['pay_id']=$pay_id;
    $data['tx_type']=$tx_type;
    $data['amount']=$amount;
    $data['ref_amount']=$amount;
    $data['call_back_url']=$base_url."/api/easypayzhuris";
    $data['access_code']=config('api_config.access_code_zhuris');
    $data['source']=$source;    //For Deposit
    $data['destination']=$destination;  //For Withdraw
    $data['code']="90";
    $data['param1']=$param1;
    if($param2!=null&&$param2!="")
    $data['param2']=$param2;
    if($param5!=null&&$param5!="")
    $data['param5']=$param5;
    
    $url="https://payments.zhuris.com/app_api/api_script1.php";
    $rslt=(MethodsController::class)::post_url_submitter($url,$data);
    return $rslt;
   }

//Receive PAI response and update transaction status         $data['api_fee']=($success==1)?$api_fee:"0";
public static function easypayug_receive_zhuris(Request $rq,$tx_detail=null){
    //prepair data that for update
         $tx_status=($rq->input('tx_status')==="succesful" || $rq->input('tx_status')==="successful") ? "success":$rq->input('tx_status');
         $tx_status=($rq->input('tx_status')==null && $rq->input('amount') > 0) ? "success":$tx_status;
         $params['column_values']=['tx_id'=>$rq->input('tx_id'),
                                   'api_id'=>$rq->input('zhuris_id'),
                                   'api_amount'=>$rq->input('amount'),
                                   'api_fee'=>$rq->input('charge')??"0",
                                   'tx_status'=>($tx_status==="success")?"pending":$tx_status,
                                   'api_ref_body'=>json_encode($rq->input()),
                                   'api_errormsg'=>$rq->input('errormsg')??"",
                                  ];
         $params['condition'][]=[' ','tx_id','=',$rq->input('tx_id')];
//return $params;
return         $dta=self::tx_response_finisher($params);
}



//Receive PAI response and update transaction status         $data['api_fee']=($success==1)?$api_fee:"0";
public static function tx_push_receive($tx_id){
    //prepair data that for update
    $tx_detail=DB::table('tx_api_refs')->where('id','=',$tx_id)->where('tx_status','=','pending' )->first();
/*
    $data=$response ?? null;
    if($response==null)
     {
      (MethodsController::class)::sendmail(config('api_config.email_admin'),"denissim@yahoo.com","Relworx Response ERROR ",' Cannot process this transaction ');
      return " Failed to process transaction ";
     }
*/

    
    $amount_cash=($tx_detail->tx_type==="deposit") ? $tx_detail->ref_amount : $tx_detail->ref_amount*-1;
    $amount_cash=($tx_detail->status==="success" ) ? $amount_cash : 0;
    $status=($tx_detail->status==="success") ? "success" : "failed";

    Log::channel('slackNotification')->info('Finishing TX', ['response_body'=>'Pushed transaction','ID'=>$tx_id,'tx_Detail'=>$tx_detail,'Approve status'=>$tx_detail->status,'TX status'=>$tx_detail->tx_status] );

//Complete the response action

    if($status==="success")
        DB::table('tx_api_refs')->where('tx_id','=',$tx_detail->tx_id)->update([ 'tx_status'=>$status,'active_status'=>'complete','status'=>$status,'amount_cash'=>$amount_cash,'api_ref_body'=>'Pushed transaction','api_amount'=>($tx_detail->ref_amount*1 + $tx_detail->tx_fee*1 ),'sync_status'=>'yes' ]);
    else
        DB::table('tx_api_refs')->where('tx_id','=',$tx_detail->tx_id)->update([ 'tx_status'=>$status,'active_status'=>'complete','status'=>$status,'amount_cash'=>'0','api_ref_body'=>'Pushed transaction','api_amount'=>'0','sync_status'=>'yes' ]);



$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => $tx_detail->call_back_url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array('tx_id' => $tx_detail->pay_id,'status' => $status,'api_ref_body' => $tx_detail->api_ref_body,'code' => '41'),
));
$response = curl_exec($curl);
curl_close($curl);

/*
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $tx_detail->call_back_url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array('code' => '41','api_ref_body' => $tx_detail->api_ref_body,'status' => $status,'tx_id','=',$tx_detail->pay_id),
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$tx_detail->pay_id
          ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
*/
        (MethodsController::class)::sendmail(config('api_config.email_admin'),"denissim@yahoo.com","Relworx Push Finisher ", json_encode(array('api_ref_body' => "Pushed Transaction from gateway",'status' => $status,'tx_id','=',$tx_id) ) );
    Log::channel('slackNotification')->info('Relworx Pushed Finisher '.$tx_detail->call_back_url, ['response_body'=>$response,'Detail_Type'=>$tx_detail->tx_type ] );
}




//Receive PAI response and update transaction status         $data['api_fee']=($success==1)?$api_fee:"0";
public static function relworx_receive(Request $rq){
    //prepair data that for update
    $post = $rq->input();
    Log::channel('slackNotification')->info('TX Notification ONE', ['response_1'=>json_encode($rq->input()) ] );
    $response=json_encode($rq->input());
    $response=json_decode($response,true);
    $data=$response ?? null;
    if($response==null)
     {
      (MethodsController::class)::sendmail(config('api_config.email_admin'),"denissim@yahoo.com","Relworx Response ERROR ",' Cannot process this transaction ');
      return " Failed to process transaction ";
     }
    
    $tx_id=$response['customer_reference'];
    $tx_detail=DB::table('tx_api_refs')->where('tx_id','=',$tx_id)->where('tx_status','=','pending' )->first();

 

    Log::channel('slackNotification')->info('Finishing TX', ['response_body'=>$response,'tx_Detail'=>$tx_detail] );
    
    $amount_cash=($tx_detail->tx_type==="deposit") ? $tx_detail->ref_amount*1+$tx_detail->tx_cash_back*1 : ($tx_detail->ref_amount*1 + $tx_detail->tx_cash_back*1)*-1;
    $amount_cash=($response['status']== true || $response['status']==="success") ? $amount_cash : 0;
    $status=($response['status']== "success" || $response['success']==="success") ? "success" : "failed";


//Complete the response acxtion

    if($status==="success")
        DB::table('tx_api_refs')->where('tx_id','=',$tx_id)->update([ 'tx_status'=>$status,'active_status'=>'complete','status'=>$status,'amount_cash'=>$amount_cash,'api_ref_body'=>json_encode($rq->input()),'api_amount'=>($tx_detail->ref_amount*1 + $tx_detail->tx_fee*-1 ),'sync_status'=>'yes'  ]);
    else
        DB::table('tx_api_refs')->where('tx_id','=',$tx_id)->update([ 'tx_status'=>$status,'active_status'=>'complete','status'=>$status,'amount_cash'=>0,'api_ref_body'=>json_encode($rq->input()),'api_amount'=>0,'sync_status'=>'yes'  ]);



$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => $tx_detail->call_back_url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array('tx_id' => $tx_detail->pay_id,'status' => $status,'api_ref_body' => $tx_detail->api_ref_body,'code' => '41'),
));
$response = curl_exec($curl);
curl_close($curl);

/*
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $tx_detail->call_back_url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array('api_ref_body' => $tx_detail->api_ref_body,'status' => $status,'tx_id','=',$tx_detail->pay_id),
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$tx_id
          ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
*/
        (MethodsController::class)::sendmail(config('api_config.email_admin'),"denissim@yahoo.com","Relworx Finisher ", json_encode(array('api_ref_body' => json_encode($rq->input()),'status' => $status,'tx_id','=',$tx_id) ) );
    Log::channel('slackNotification')->info('Relworx Finisher', ['response_body'=>$response,'Detail_Type'=>json_encode($rq->input())] );

/*
    (MethodsController::class)::sendmail(config('api_config.email_admin'),"denissim@yahoo.com","Relworx Response ONE",json_encode($rq->input()) );
    
         $tx_status=($data['status']==="succesful" || $data['status']==="successful" || $data['status']==="SUCCESSFUL") ? "success":$data['status'];
         $tx_status=($data['status']==null && $data['amount'] > 0) ? "success":$tx_status;
         $params['column_values']=['tx_id'=>$tx_id,
                                   'api_id'=>$data['flw_ref'] ?? $data['id'],
                                   'api_amount'=>$data['amount'] ?? "0",
                                   'api_fee'=>$data['fee'] ?? "0",  // $data['charged_amount'] for deposit via flutterwave mobile money
                                   'tx_status'=>($tx_status==="success")?"pending":$tx_status,
                                   'api_ref_body'=>$data['narration'] ?? $data['complete_message'],//json_encode($rq->input()),
                                   'api_errormsg'=>($tx_status==="success")?"":$data['is_approved'],
                                  ];
         $params['condition'][]=[' ','tx_id','=',$tx_id];
    (MethodsController::class)::sendmail(config('api_config.email_admin'),"denissim@yahoo.com","Flutter Response TWO ",json_encode($params) );
//return         $dta=self::tx_response_finisher($params);
//Send values to call back url
*/

}


//Send mobile money transaction to RELWORKS
public static function relworks_request_withdraw($tx_detail)
   {
    Log::channel('slackNotification')->info('RELWORKS WITHDRAW GW1', ['detail'=>$tx_detail,'tx_id'=>$tx_detail->tx_id, 'amount'=>$tx_detail->ref_amount , 'acc_no'=>config('api_config.relworks_mm_acc_no'),'link'=>config('api_config.relworks_mm_endpoint').'/request-payment' ]);
    DB::table('tx_api_refs')->where('tx_id','=',$tx_detail->tx_id)->update([ 'status'=>'processing' ]);
    $mobile_phone_number="+".(MethodsController::class)::mobile_phone_maker(['mobile'=>$tx_detail->ref_id,'currency'=>$tx_detail->currency_source]);
    $network=(MethodsController::class)::get_telecom_name($mobile_phone_number);
    $affected=DB::table('tx_api_refs')->where('id','=',$tx_detail->id)->update([ 'tx_status'=>'pending' ]);
    $tx_amount=($tx_detail->ref_amount*1-$tx_detail->tx_fee*1);
     
    $curl = curl_init();
    $link=config('api_config.relworks_mm_endpoint').'/send-payment';
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://payments.relworx.com/api/mobile-money/send-payment',
    //  CURLOPT_URL => $link,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => json_encode(['account_no' => config('api_config.relworks_mm_acc_no'),'reference' => $tx_detail->tx_id,'msisdn' => $mobile_phone_number,'currency' => 'UGX','amount' => $tx_amount,'description' => 'Withdraw Request.']),
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . config('api_config.relworks_mm_tocken'),
        'Content-Type: application/json',
        'Accept: application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    Log::channel('slackNotification')->info('RELWORKS RESPONSE '.$mobile_phone_number, ['api_response'=>$response,'CURLOPT_POSTFIELDS'=>json_encode(CURLOPT_POSTFIELDS),'CURLOPT_URL'=>config('api_config.relworks_mm_endpoint').'/request-payment','TOCKEN'=>config('api_config.relworks_mm_tocken') ]);

    $response=json_decode($response,true);
//    return $response['status'];
    if($response==null)
      return $dta['status']="failed";
      
    $dta['status']=($response['success']==="successful") ? "success" : $response['success'];
    $dta['message']=$response['message'];
//    $dta['link']=$response['meta']['authorization']['redirect'];

    if($response['success']==="successful")
      DB::table('tx_api_refs')->where('tx_id','=',$tx_detail->tx_id )->update([ 'status'=>'pending' ]);
    else
      DB::table('tx_api_refs')->where('tx_id','=',$tx_detail->tx_id )->update([ 'status'=>'failed' ]);

    return $dta;
}


//Send mobile money transaction to RELWORKS
public static function relworks_wallet_balance($acc_number,$currency)
   {
//    Log::channel('slackNotification')->info('RELWORKS Bal fetch', ['Acc'=>$acc_number,'Currency'=>$currency ]);
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://payments.relworx.com/api/mobile-money/check-wallet-balance',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_POSTFIELDS => json_encode(array('account_no' => $acc_number,'currency' => $currency)),
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . config('api_config.relworks_mm_tocken'),
        'Content-Type: application/json',
        'Accept: application/json'
      ),
    ));
    
    $response = curl_exec($curl);
//    Log::channel('slackNotification')->info('RELWORKS Bal fetch', ['Acc'=>$acc_number,'Bal'=>$response ]);

    curl_close($curl);
  return $response;
}

//Send mobile money transaction to RELWORKS
public static function relworks_request_deposit($tx_detail)
   {
    DB::table('tx_api_refs')->where('tx_id','=',$tx_detail->tx_id)->update([ 'status'=>'processing' ]);
    $mobile_phone_number="+".(MethodsController::class)::mobile_phone_maker(['mobile'=>$tx_detail->ref_id,'currency'=>$tx_detail->currency_source]);
    $network=(MethodsController::class)::get_telecom_name($mobile_phone_number);
    $affected=DB::table('tx_api_refs')->where('id','=',$tx_detail->id)->update([ 'tx_status'=>'pending' ]);
    $tx_amount=($tx_detail->ref_amount*1+$tx_detail->tx_fee*1);
    
    Log::channel('slackNotification')->info('RELWORKS DEPOSIT GW1', ['mobile'=>$mobile_phone_number,'tx_id'=>$tx_detail->tx_id, 'amount'=>$tx_amount , 'acc_no'=>config('api_config.relworks_mm_acc_no'),'link'=>config('api_config.relworks_mm_endpoint') ,'TOKEN'=>config('api_config.relworks_mm_tocken')]);
    
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://payments.relworx.com/api/mobile-money/request-payment',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => json_encode(['account_no' => config('api_config.relworks_mm_acc_no'),'reference' => $tx_detail->tx_id,'msisdn' => $mobile_phone_number,'currency' => 'UGX','amount' => $tx_amount,'description' => 'Payment Request.']),
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer ' . config('api_config.relworks_mm_tocken'),
    'Content-Type: application/json',
    'Accept: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;


    curl_close($curl);

    Log::channel('slackNotification')->info('RELWORKS RESPONSE ONE', ['api_response'=>$response,'CURLOPT_POSTFIELDS'=>json_encode(CURLOPT_POSTFIELDS),'CURLOPT_URL'=>config('api_config.relworks_mm_endpoint').'/request-payment','TOCKEN'=>config('api_config.relworks_mm_tocken') ]);

    $response=json_decode($response,true);
    
    if($response==null)
      $dta['status']="failed";
      
    $dta['status']=($response['success']==="successful") ? "success" : $response['success'];
    $dta['message']=$response['message'];
//    $dta['link']=$response['meta']['authorization']['redirect'];

    if($dta['status']==="success")
      DB::table('tx_api_refs')->where('tx_id','=',$tx_detail->tx_id )->update([ 'status'=>'pending' ]);
    else
      DB::table('tx_api_refs')->where('tx_id','=',$tx_detail->tx_id )->update([ 'status'=>'failed' ]);

    return $dta;
}



//Send mobile money transaction to flutterwave
public static function flutterwave_request_deposit($tx_detail)
   {
    $mobile_phone_number=(MethodsController::class)::mobile_phone_maker(['mobile'=>$tx_detail->ref_id,'currency'=>$tx_detail->currency_destination]);
    $network=(MethodsController::class)::get_telecom_name($mobile_phone_number);
    $affected=DB::table('tx_api_refs')->where('id','=',$tx_detail->id)->update([ 'tx_status'=>'pending' ]);
    
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.flutterwave.com/v3/charges?type=mobile_money_uganda',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{"phone_number": "'.$tx_detail->ref_id.'",
                             "network": "'.$network.'",
                             "amount": '.$tx_detail->ref_amount.',
                             "currency": "'.$tx_detail->currency_destination.'",
                             "email": "consult.zhuris@gmail.com",
                             "redirect_url": "'.(($tx_detail->tx_service_cat==="campaign") ? config('api_config.base_url')."/campaign_detail/".$tx_detail->request_service_id : config('api_config.base_url') ).'",
                             "tx_ref": "'.$tx_detail->tx_id.'"}',
      CURLOPT_HTTPHEADER => array(
                                    'Authorization: Bearer '.config('api_config.flutterwave_mm_tocken'),
                                    'content-type: application/json'
                                  ),
                                )
                    );

    $response = curl_exec($curl);
    $response=json_decode($response,true);
//    return $response['status'];
    if($response==null)
      return $dta['status']="failed";
    $dta['status']=($response['status']==="successful") ? "success" : $response['status'];
    $dta['message']=$response['message'];
    $dta['link']=$response['meta']['authorization']['redirect'];

curl_close($curl);

return $dta;
}


//Send mobile money transaction to flutterwave
public static function flutterwave_request_withdraw($tx_detail)
   {
    $mobile_phone_number=(MethodsController::class)::mobile_phone_maker(['mobile'=>$tx_detail->ref_id,'currency'=>$tx_detail->currency_destination]);
    $network=(MethodsController::class)::get_telecom_name($mobile_phone_number);
    $affected=DB::table('tx_api_refs')->where('id','=',$tx_detail->id)->update([ 'tx_status'=>'pending' ]);
    
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.flutterwave.com/v3/transfers',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{"account_bank": "MPS",
                             "account_number": "'.$mobile_phone_number.'",
                             "amount": '.$tx_detail->ref_amount.',
                             "currency": "'.$tx_detail->currency_source.'",
                             "beneficiary_name": "'.$tx_detail->param2.'",
                             "reference": "'.$tx_detail->tx_id.'",
                             "callback_url": "'.config('api_config.base_url').'/api/flutterwave/mmoney",
                             "meta": {
                                     "sender": "ZHURISOFT COSNULT LTD",
                                     "sender_country": "UG",
                                     "mobile_number": "256701620575"
                                    }
                            }',
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer '.config('api_config.flutterwave_mm_tocken'),
        'content-type: application/json'
      ),
    ));
    
    $response = curl_exec($curl);
    (MethodsController::class)::sendmail(config('api_config.email_admin'),"denissim@yahoo.com","Flutter Response WITHDRAW",$response );
    Log::channel('slackNotification')->info('WITHDRAW FLUTTER INITIATED', ['response'=>$response]);
//return $response;
    $response=json_decode($response,true);
    if($response==null)
      $dta['status']="failed";
    $dta['status']=($response['status']==="successful") ? "success" : $response['status'];
curl_close($curl);

//Lines duplicated from flutterwave_receive to effect withdraw action to complete transaction
         $data=$response['data'] ?? null;
         $tx_id=$data['tx_ref'] ?? $data['reference'];
         $tx_status=($data['status']==="succesful" || $data['status']==="successful" || $data['status']==="SUCCESSFUL" || $data['status']==="NEW") ? "success":$data['status'];
         $tx_status=($data['status']==null && $data['amount'] > 0) ? "success":$tx_status;
         $params['column_values']=['tx_id'=>$tx_id,
                                   'api_id'=>$data['flw_ref'] ?? $data['id'],
                                   'api_amount'=>$data['amount'] ?? "0",
                                   'api_fee'=>$data['fee'] ?? "0",  // $data['charged_amount'] for deposit via flutterwave mobile money
                                   'tx_status'=>($tx_status==="success")?"pending":$tx_status,
                                   'api_ref_body'=>$data['narration'] ?? $data['complete_message'],//json_encode($rq->input()),
                                   'api_errormsg'=>($tx_status==="success")?"":$data['is_approved'],
                                  ];
         $params['condition'][]=[' ','tx_id','=',$tx_id];
    (MethodsController::class)::sendmail(config('api_config.email_admin'),"denissim@yahoo.com","Flutter Response THREE ",json_encode($params) );
    $dta=self::tx_response_finisher($params);

return $dta;
}






//Receive PAI response and update transaction status         $data['api_fee']=($success==1)?$api_fee:"0";
public static function flutterwave_receive(Request $rq){
    //prepair data that for update
    $post = $rq->input();
    //$post=json_decode($post,true);
    
    $response=json_encode($rq->input());
    $response=json_decode($response,true);
    $data=$response['data'] ?? null;
    if($response['data']==null)
     {
      (MethodsController::class)::sendmail(config('api_config.email_admin'),"denissim@yahoo.com","Flutter Response ERROR ",' Cannot process this transaction ');
      return " Cannot process this transaction ";
     }

    $tx_id=$data['tx_ref'] ?? $data['reference'];

    (MethodsController::class)::sendmail(config('api_config.email_admin'),"denissim@yahoo.com","Flutter Response ONE",json_encode($rq->input()) );
    
         $tx_status=($data['status']==="succesful" || $data['status']==="successful" || $data['status']==="SUCCESSFUL") ? "success":$data['status'];
         $tx_status=($data['status']==null && $data['amount'] > 0) ? "success":$tx_status;
         $params['column_values']=['tx_id'=>$tx_id,
                                   'api_id'=>$data['flw_ref'] ?? $data['id'],
                                   'api_amount'=>$data['amount'] ?? "0",
                                   'api_fee'=>$data['fee'] ?? "0",  // $data['charged_amount'] for deposit via flutterwave mobile money
                                   'tx_status'=>($tx_status==="success")?"pending":$tx_status,
                                   'api_ref_body'=>$data['narration'] ?? $data['complete_message'],//json_encode($rq->input()),
                                   'api_errormsg'=>($tx_status==="success")?"":$data['is_approved'],
                                  ];
         $params['condition'][]=[' ','tx_id','=',$tx_id];
    (MethodsController::class)::sendmail(config('api_config.email_admin'),"denissim@yahoo.com","Flutter Response TWO ",json_encode($params) );
return         $dta=self::tx_response_finisher($params);
}

/*
public static function relworks_receive(Request $rq){
    //prepair data that for update
    $post = $rq->input();
    //$post=json_decode($post,true);
    
    $response=json_encode($rq->input());
    $response=json_decode($response,true);
    $data=$response['data'] ?? null;
    if($response['data']==null)
     {
      (MethodsController::class)::sendmail(config('api_config.email_admin'),"denissim@yahoo.com","Relworks Response ERROR ",' Cannot process this transaction ');
      return " Cannot process this transaction ";
     }

    $tx_id=$data['tx_ref'] ?? $data['reference'];

    (MethodsController::class)::sendmail(config('api_config.email_admin'),"denissim@yahoo.com","Relworks Response ONE",json_encode($rq->input()) );
    
         $tx_status=($data['status']==="succesful" || $data['status']==="successful" || $data['status']==="SUCCESSFUL") ? "success":$data['status'];
         $tx_status=($data['status']==null && $data['amount'] > 0) ? "success":$tx_status;
         $params['column_values']=['tx_id'=>$tx_id,
                                   'api_id'=>$data['flw_ref'] ?? $data['id'],
                                   'api_amount'=>$data['amount'] ?? "0",
                                   'api_fee'=>$data['fee'] ?? "0",  // $data['charged_amount'] for deposit via flutterwave mobile money
                                   'tx_status'=>($tx_status==="success")?"pending":$tx_status,
                                   'api_ref_body'=>$data['narration'] ?? $data['complete_message'],//json_encode($rq->input()),
                                   'api_errormsg'=>($tx_status==="success")?"":$data['is_approved'],
                                  ];
         $params['condition'][]=[' ','tx_id','=',$tx_id];
    (MethodsController::class)::sendmail(config('api_config.email_admin'),"denissim@yahoo.com","Flutter Response TWO ",json_encode($params) );
return         $dta=self::tx_response_finisher($params);
}
*/



//Transaction request via easy pay direct gateway
public static function easypayug_request($tx_detail){
  //flag the transaction as pending
  $affected=DB::table('tx_api_refs')->where('id','=',$tx_detail->id)->update([ 'tx_status'=>'pending' ]);
  if($tx_detail->tx_type==="deposit")
  {
    $amount=$tx_detail->ref_amount;
    $phone=$tx_detail->source_account;
    $reference=$tx_detail->tx_id;
    $reason=$tx_detail->tx_id;
     $url = config('api_config.easypayug_endpoint');
     $payload = array( 'username' => config('api_config.easypayug_username'), 
     'password' => config('api_config.easypayug_password'),
     'action' => 'mmdeposit', 
     'amount' => $amount, 
     'phone'=>$phone,
     'currency'=>config('api_config.easypayug_currency'), 
     'reference'=>$reference, 
     'reason'=>$reason
     ); 
     //set the url, number of POST vars, POST data 
     curl_setopt($ch,CURLOPT_URL, $url); 
     curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($payload)); 
     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,15); 
     curl_setopt($ch, CURLOPT_TIMEOUT, 400); //timeout in seconds 
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
     $result = curl_exec($ch); 
     curl_close($ch); 
    return json_decode($result); 
//   (MethodsController::class)::post_url_submitter_headered($url,$fields,$headers=null); 
  }
 else{
    $amount=$tx_detail->ref_amount;
    $phone=$tx_detail->dest_account;
    $pay_id=$tx_detail->tx_id;
     $url = config('api_config.easypayug_endpoint');
     $payload = array( 'username' => config('api_config.easypayug_username'), 
     'password' => config('api_config.easypayug_password'), 
     'action' => 'mmpayout', 
     'amount' => $amount,
     'phone'=>$phone,
     'reference'=>$pay_id, 
     ); 
     $ch = curl_init(); 
     //set the url, number of POST vars, POST data 
     curl_setopt($ch,CURLOPT_URL, $url); 
     curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($payload)); 
     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,15); 
     curl_setopt($ch, CURLOPT_TIMEOUT, 400); //timeout in seconds 
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
     $result = curl_exec($ch); 
     curl_close($ch); 
     print_r(json_decode($result));
     $success=(json_decode($result,true))["success"];
     $reference=(json_decode($result,true))["telecomId"];
     $api_fee=(json_decode($result,true))["charge"];
     /*
     Instant response body
     stdClass Object ( [success] => 1 [data] => Mobile Money Payout. Phone: 256701620575 Amount: UGX 500 Charge: UGX 400 Date: 2020-09-07 10:55:15 TelecomID: 43436048641 TxId: 753104 [txid] => 753104 [telecomId] => 43436048641 [amount] => 500 [currency] => UGX [phone] => 256701620575 [date] => 2020-09-07 10:55:15 [charge] => 400 )  
     */
     //eaypay wothdraw is instant and so response is processed right away
        $params['column_values']=['tx_id'=>$pay_id,
                                  'amount'=>($success==1)?$amount:"0",
                                  'api_fee'=>($success==1)?$api_fee:"0",
                                  'tx_status'=>($success==1)?"success":"failed",
                                  'reference'=>($success==1)?$reference:"failed",
                                  'api_errormsg'=>($success==1)?'':(json_decode($result,true))["errormsg"]." FOR( $amount , $phone, $pay_id)"
                                  ];
        $params['condition'][]=[' ','tx_id','=',$pay_id];
        return $dta=self::tx_response_finisher($params);
 }
}
//Easypay direct response back from API
public static function easypayug_receive(Request $rq){
         $params['column_values']=['tx_id'=>$rq->input('transactionId'),
                                   'ref_id'=>$rq->input('transactionId'),
                                   'api_amount'=>$rq->input('amount'),
                                   'tx_status'=>($rq->input('amount') > 0) ? "success":"failed",
                                   'api_ref_body'=>json_encode($rq->input())
                                  ];
         $params['condition'][]=[' ','tx_id','=',$rq->input('transactionId')];
        return $dta=self::tx_response_finisher($params);
}


//Submit cash transaction for approval  - This serves also bank deposites . It simply submits an approval request
public static function cashug_request($tx_detail){
    //flag the transaction as pending
    $affected=DB::table('tx_api_refs')->where('id','=',$tx_detail->id)->update([ 'tx_status'=>'pending','approve_ref_id'=>$tx_detail->tx_id ]);
    
    //complete transaction if it is a transit or submit for approval if it not a transit
    if($tx_detail->tx_channel==="transit"){
         return $dta=self::cashug_receive($tx_detail);
        }
    //continue with cash approval if it is not a transit transaction
               $params=array('entity_type'=>'request',
                            'approve_ref_id'=>$tx_detail->tx_id,
                            'approve_action'=>'approve',
                            'approve_narration'=>$tx_detail->tx_value_type." - ".$tx_detail->tx_type." - ".$tx_detail->tx_channel." : ".$tx_detail->currency_source." ".$tx_detail->amount_source,
                            'approve_count'=>$tx_detail->approve_count ?? "2",
                            'approver_role'=>($tx_detail->tx_channel==="bank")?"office":( ($tx_detail->tx_owner_type==="group")?"admin":$tx_detail->tx_owner_type),
                            'user_id'=>$tx_detail->user_id,
                            'source_table'=>'tx_api_refs',
                            'source_column'=>'id',
                            'source_id'=>$tx_detail->id,
                            'approve_scope'=>($tx_detail->tx_channel==="bank")?"office":$tx_detail->tx_owner_type,        // user - office
                            'ref_center_id'=>($tx_detail->tx_owner_type==="group")?$tx_detail->tx_owner_id:""
                            );
                    $dta['body_approval']=(MethodsController::class)::approval_register($params);
                    return $dta;
 }
//Receive back cash approval detail [ tx_status, api_amount, ref_id, tx_id, approve_narration]
public static function cashug_receive($tx_detail){
    if($tx_detail->tx_channel==="transit"){     // make transaction fulfilled if the channel is transit
        $set_pairs=" approve_status='approved' ,api_amount=ref_amount,tx_status='pending',api_id='".$tx_detail->slip_no."'";
        DB::unprepared("update tx_api_refs set ".$set_pairs." where id = '".$tx_detail->id."'");
    }
        //Prepare values for transaction account logging
         $params['column_values']=['tx_id'=>$tx_detail->tx_id,
                                   'ref_id'=>($tx_detail->ref_id==null || $tx_detail->ref_id==="")?$tx_detail->slip_no:$tx_detail->ref_id,
                                   'api_amount'=>($tx_detail->tx_channel==="transit")?$tx_detail->ref_amount:$tx_detail->api_amount??"0",
                                   'tx_status'=>($tx_detail->tx_channel==="transit")?"pending":$tx_detail->tx_status,
                                   'api_ref_body'=>($tx_detail->tx_channel==="transit")?"transit auto processing":$tx_detail->approve_narration??""
                                  ];
         $params['condition'][]=[' ','tx_id','=',$tx_detail->tx_id];
//return $params;
        return $dta=self::tx_response_finisher($params);
}





//get charges   
public static function charges_get($params){  //$params=['currency'=>,'tx_value_type'=>,'tx_type'=>,'tx_channel','ref_amount'=>]
    $wave_charges=($params['tx_value_type']==="provider" && $params['tx_type']!=="transfer");   // wave charges for provider back office transactions
    $charge=0;
    $params['tx_value_type']=($params['tx_channel']==="transit")?"cash":$params['tx_value_type'];
    $channel_detail=DB::table('tx_accounts')->where('acc_category','=','admin')
                                            ->where('currency','=',$params['currency'])
                                            ->where('tx_value_type','=',$params['tx_value_type'])
                                            ->where('tx_channel','=',$params['tx_channel'])
                                            ->where('active_status','=','active')
                                            ->first();
//    $channel_detail=$channel_detail[0]??$channel_detail;
    $charge=($params['tx_type']==="deposit")?$channel_detail->dp_charges ?? '0':$channel_detail->wd_charges ?? '0';
    $api_fee=($params['tx_type']==="deposit")?$channel_detail->dp_api_fee ?? '0':$channel_detail->wd_api_fee ?? '0';
    $fees['charge']=($wave_charges) ? 0 : (MethodsController::class)::charge_converter(['range'=>$charge,'amount'=>$params['ref_amount'] ]);
    $fees['api_fee']=($wave_charges) ? 0 : (MethodsController::class)::charge_converter(['range'=>$api_fee,'amount'=>$params['ref_amount'] ]);
    $fees['dp_max_day']=$channel_detail->dp_max_day;
    $fees['wd_max_day']=$channel_detail->wd_max_day;
    $fees['acc_number']=$channel_detail->acc_number;
    return (object)$fees;
}




    
public static function tx_api_response($id){
  //retrieve the initiated transaction
  $tx_detail=DB::table('tx_api_refs')->find($id);
  if($tx_detail->tx_channel==="cash" || $tx_detail->tx_channel==="bank" || $tx_detail->tx_channel==="transit" || $tx_detail->tx_channel==="mmoney" )
    return self::cashug_receive($tx_detail);
    
 }


//Process transactions api responses and log them into the live accounts log    comes with  [ tx_id, ref_id, api_amount, tx_status, ref_body ]
public static function tx_response_finisher($params){
  //get the transaction detail
         $params['table']='tx_api_refs';
         $params['condition'][]=[' and ','tx_status','=','pending'];    // ensure it is in pending state
         $params['extras']=null;

         $tx_detail=(qryrnrController::class)::update1($params);
         if($tx_detail==null || empty($tx_detail) )
         return $dta['message']="Was not pending approval";

         $tx_detail=json_decode($tx_detail,true);
         $tx_detail=(object)$tx_detail[0];

          if($tx_detail->tx_status!=="complete" && $tx_detail->tx_status!=="success" && $tx_detail->sync_status==="no")
            {
            $credit_cash_settlement=($tx_detail->recovery_flag==='yes');
             
//return $tx_detail;
             
             if($tx_detail->tx_type==="withdraw" || ($tx_detail->tx_type==="transfer" && $tx_detail->tx_channel==="transit") )      //Finsish withdrawal  
               {
                 $dta['withdraw']= self::withdraw_process($tx_detail);
               }
             else if($tx_detail->tx_type==="deposit")       //Finish deposit
               {
                 if($tx_detail->recovery_flag==='paid') 
                    $dta['deposit']= self::tx_api_response_transit_settlement($tx_detail);
                 if($tx_detail->tx_service_cat==='release')
                    {
                        $dta['deposit']= self::tx_api_response_release_settlement($tx_detail);
                        Log::channel('slackNotification')->info('TX-release COMPLETED', ['mobile'=>$tx_detail->ref_id,'tx_id'=>$tx_detail->tx_id, 'amount'=>$tx_detail->ref_amount, 'API-amount'=>$tx_detail->api_amount, 'tx_status'=>$tx_detail->tx_status ]);
                    }
                 else
                   $dta['deposit']= self::deposit_process($tx_detail);
               }
             else if($tx_detail->tx_type==="transfer" && $tx_detail->tx_channel!=="transit"){      //Finish transfer
                 $params=json_decode(json_encode($tx_detail), TRUE);        // add only missing parameters and make the internal transfer
                 $params['tx_value_type_source']=$tx_detail->tx_value_type;
                 $params['tx_value_type_dest']=$tx_detail->tx_value_type;
                 $dta['transfer']=self::transfer_acc_internal($params);
             }
             
                //first tget the business profit account to receive the pay
               $params=null;
               $params['condition'][]=[ ' and ','ac.currency','=',$tx_detail->currency_source ?? $tx_detail->currency_destination ];
               $params['condition'][]=[ ' and ','ac.tx_value_type','=','wallet' ];
               $params['condition'][]=[ ' and ','ac.tx_channel','=','profit' ];
               $params['condition'][]=[ ' and ','ac.acc_category','=','admin' ];
               $acc_profit=(qrymkrController::class)::tx_accounts_get($params);
               $acc_profit=$acc_profit[0];
               //make the internal wllaet transfer
            
            //Withdraw charge from source account and send them to profit account of business
//PAUSED            $back_office_transaction=($tx_detail->source_account===$acc_profit->acc_number);
            $back_office_transaction=((DB::table('tx_accounts')->where('currency','=',($tx_detail->tx_type === 'withdraw') ? $tx_detail->currency_source : $tx_detail->currency_destination)->where('acc_number','=',($tx_detail->tx_type === 'withdraw') ? $tx_detail->source_account : $tx_detail->dest_account)->where('acc_category','=','admin')->where('tx_channel','!=','credit')->count()) > 0) ;
            $provider_transaction=($tx_detail->tx_value_type ==="provider");

            if(!$provider_transaction)     // no withdraw charges are attached to provider value types
            if(!$back_office_transaction)     // no withdraw charges are attached to company business profit account
            if($tx_detail->tx_fee > 0){
              $params=['tx_id'=>$tx_detail->tx_id.$tx_detail->user_id."0011",
                       'source_account'=>$tx_detail->source_account,        // from originating account
                       'dest_account'=>$acc_profit->acc_number,         // to business profit account
                       'currency_source'=>$tx_detail->currency_source,
                       'currency_destination'=> $tx_detail->currency_source,
                       'amount_source'=>$tx_detail->tx_fee,
                       'tx_channel'=>'wallet',
                       'tx_value_type_source'=>$tx_detail->tx_value_type,
                       'tx_value_type_dest'=>$tx_detail->tx_value_type
                      ];
               $dta['charges']=self::transfer_acc_internal($params);
            }
            
            //Withdraw api charges from profit account
            if(!$provider_transaction)     // no withdraw charges are attached to provider value types
            if(!$back_office_transaction)     // no withdraw charges are attached to company business profit account
            if($tx_detail->api_fee > 0){
                        $acc_body=['tx_id'=>$tx_detail->tx_id.$tx_detail->user_id."00012",
                                   'acc_number'=>$acc_profit->acc_number,
                                   'source_account'=>$acc_profit->acc_number,
                                   'tx_type'=>'withdraw',
                                   'currency'=>$tx_detail->currency_source,
                                   'currency_source'=>$tx_detail->currency_source,
                                   'cash_amount'=>($tx_detail->api_fee *-1),
                                   'tx_id_parent'=>$tx_detail->tx_id,
                                   'tx_value_type'=>$tx_detail->tx_value_type,
                                   'tx_flag'=>'normal',     // normal   - transit   - profit
                                   'transit_status'=>'none',
                                   'tx_channel'=>'wallet'
                                  ];     //similar to that in api_ref
                $dta['api_charge']=self::tx_acc_loader($acc_body,null);
            }
            
            //Reflect the lampsum cash value onto corresponding channel account log
//PAUSED            $back_office_transaction=((DB::table('tx_accounts')->where('acc_number','=',($tx_detail->tx_type === 'withdraw') ? $tx_detail->source_account : $tx_detail->dest_account)->where('acc_category','=','admin')->count()) > 0) ;

//            if($tx_detail->acc_number_office!=null && $tx_detail->acc_number_office!=="" )
            if(!$back_office_transaction && !$credit_cash_settlement)   // Do not duplicate transaction if account number is for office or if is cash received for credit settlement
            if($tx_detail->tx_type === 'deposit' || $tx_detail->tx_type === 'withdraw' || ($tx_detail->tx_type === 'transfer' && $tx_detail->tx_channel==='transit')){
                $amount=($tx_detail->tx_type === 'deposit') ? ($tx_detail->api_amount - $tx_detail->api_fee) : ($tx_detail->api_amount + $tx_detail->api_fee)*-1 ;
                        $acc_body=['tx_id'=>$tx_detail->tx_id.$tx_detail->user_id."003330",
                                   'acc_number'=>$tx_detail->acc_number_office,
                                   'source_account'=>($tx_detail->tx_type === 'deposit') ? $tx_detail->dest_account : $tx_detail->source_account,
                                   'tx_type'=>$tx_detail->tx_type,
                                   'currency'=>($tx_detail->tx_type === 'deposit') ? $tx_detail->currency_destination : $tx_detail->currency_source,
                                   'currency_source'=>$tx_detail->currency_source,
                                   'cash_amount'=>$amount,
                                   'tx_id_parent'=>"LOG-".$tx_detail->tx_id,
                                   'tx_value_type'=>$tx_detail->tx_value_type,
                                   'tx_flag'=>'normal',     // normal   - transit   - profit
                                   'transit_status'=>'none',
                                   'tx_channel'=>$tx_detail->tx_channel
                                  ];     //similar to that in api_ref
                $dta['system_log']=self::tx_acc_loader($acc_body,null);
            }
            
                Log::channel('slackNotification')->info('TX FINISHER', $dta);

            if( isset($tx_detail->request_service_id) && $tx_detail->request_service_id !=null && $tx_detail->request_service_id!==""){
              $dta['request_log']=self::collection_finisher($tx_detail->id);
            }
            

             //mark the transaction as synced so as not to be reprocessed deu to resubmission  and remove charges if it is back office business account
             $dta['synced']=$affected=DB::table('tx_api_refs')->where('id','=',$tx_detail->id)->update(['sync_status'=>"yes",
                                                                                                        'tx_status'=>"complete",
                                                                                                        'tx_fee'=>($back_office_transaction || $provider_transaction)?'0':$tx_detail->tx_fee,
                                                                                                        'api_fee'=>($back_office_transaction || $provider_transaction)?'0':$tx_detail->api_fee
                                                                                                      ]);
            //Record overall transaction onto office giant account to indicate what is int API accounts
             if($tx_detail->tx_type === "withdraw" || $tx_detail->tx_type === "deposit")
               $dta['office_log']=self::tx_record_office($tx_detail);
            
             return $dta;
            }
          else
            return $dta['message']="Invalid request for this transaction status";
 }


//duplicate transaction into back office channel account
public static function tx_record_office($tx_detail){
    //confirm it is not transfer and it is a wallet transcation
    if($tx_detail->tx_channel !== "wallet" or ($tx_detail->tx_type!=='deposit' && $tx_detail->tx_type!=='withdraw' ) )
      return null;

    //get acting wallet account
    $trasacting_account=($tx_detail->tx_type==="withdraw") ? $tx_detail->source_account : $tx_detail->dest_account;
    //confirm it is not ac account from back office
    $found=DB::table('tx_accounts')->where('acc_number','=',$trasacting_account)->where('acc_category','=','admin')->count();
    if($found > 0)
      return null;

    //check if it withdraw and convert amoun to negative
    $amount=($tx_detail->tx_type==="withdraw") ? ($tx_detail->api_amount * -1) : $tx_detail->api_amount;

                        $acc_body=['tx_id'=>$tx_detail->tx_id."-BO",
                                   'acc_number'=>$tx_detail->acc_number_office,
                                   'source_account'=>$trasacting_account,
                                   'tx_type'=>$tx_detail->tx_type,
                                   'currency'=>$tx_detail->currency_source,
                                   'currency_source'=>($tx_detail->tx_type==="withdraw") ? $tx_detail->currency_source : $tx_detail->currency_destination,
                                   'cash_amount'=>$amount,
                                   'tx_id_parent'=>$tx_detail->tx_id."-BO",
                                   'tx_value_type'=>$tx_detail->tx_value_type,
                                   'tx_flag'=>'normal',     // normal   - transit   - profit
                                   'transit_status'=>'none',
                                   'tx_channel'=>'wallet'
                                  ];     //similar to that in api_ref
                       $dta=self::tx_acc_loader($acc_body,null);
            return $dta;
}

//transfer cash from wallet to wallet
public static function transfer_process($tx_id,$from_account,$to_account){
$params['condition'][]=[' and ','r.tx_id','=',$tx_id];
$dt_detail=tx_api_refs_get($params);
$dt_detail=$dt_detail['body'][0];
/*                        $acc_body=['tx_id'=>$tx_detail->tx_id,
                                   'acc_number'=>$tx_detail->source_account,
                                   'source_account'=>$tx_detail->source_account??"",
                                   'dest_account'=>$tx_detail->dest_account??"",
                                   'tx_type'=>'withdraw',
                                   'currency'=>$tx_detail->currency_source,
                                   'currency_source'=>$tx_detail->currency_source??"",
                                   'currency_destination'=>$tx_detail->currency_destination??"",
                                   'cash_amount'=>$tx_detail->amount_source,
                                   'tx_id_parent'=>$tx_detail->tx_id,
                                   'tx_value_type'=>$tx_detail->tx_value_type,
                                   'tx_flag'=>'normal',     // normal   - transit   - profit
                                   'transit_status'=>($cash_in_transit)?'pending':'none',
                                   'tx_channel'=>$tx_detail->tx_channel
                                  ];     //similar to that in api_ref
*/
  $params=['tx_id'=>$tx_detail->tx_id.$tx_detail->user_id,
           'source_account'=>$from_account,
           'dest_account'=>$to_account,
           'currency_source'=>$tx_detail->currency_source,
           'currency_destination'=>$dest_acc->currency_destination ?? $tx_detail->currency_source,
           'amount_source'=>$tx_detail->amount_source,
           'tx_channel'=>$tx_detail->tx_channel,
           'tx_value_type_source'=>$tx_detail->tx_value_type,
           'tx_value_type_dest'=>$dest_acc->tx_value_type
          ];
 return self::transfer_acc_internal($params);
}    


// Complete cash deposit by reconciling with proffit and provider accouts
public static function tx_api_response_release_settlement($tx_detail){
    //Get all accounts receiving deposit based on the request detail
      $accounts_paylist=self::tx_response_paylist_release($tx_detail,$tx_detail->dest_account);
    //prepare default tx query body for reuse through the list of account found
                        $acc_body=['tx_id'=>$tx_detail->tx_id,
                                   'acc_number'=>$tx_detail->dest_account,
                                   'source_account'=>"",
                                   'dest_account'=>$tx_detail->dest_account,
                                   'tx_type'=>'deposit',
                                   'tx_type_transit'=>'deposit',
                                   'currency'=>$tx_detail->currency_destination,
                                   'currency_source'=>$tx_detail->currency_source??"",
                                   'currency_destination'=>$tx_detail->currency_destination,
                                   'cash_amount'=>$tx_detail->api_amount,
                                   'tx_id_parent'=>$tx_detail->tx_id,
                                   'tx_value_type'=>$tx_detail->tx_value_type,
                                   'tx_flag'=>'normal',     // normal   - transit   - profit
                                   'transit_status'=>'none',
                                   'slip_no'=>$tx_detail->slip_no,
                                   'tx_channel'=>$tx_detail->tx_channel
                                  ];     //similar to that in api_ref

    //make deposites into the live account log. Only change necessary values from $acc_body above and deposite
      $dta=null;
      $h=0;
      for($h=0;($accounts_paylist!=null && $h<count($accounts_paylist) );$h++){
          $acc_body['tx_id']=$tx_detail->tx_id."000-0".$h;
          $acc_body['acc_number']=$accounts_paylist[$h]['acc_number'];
          $acc_body['dest_account']=$tx_detail->dest_account;
          $acc_body['source_account']="";
          $acc_body['cash_amount']=$accounts_paylist[$h]['cash_amount'];
          $acc_body['tx_flag']=$accounts_paylist[$h]['tx_flag'];
          $acc_body['transit_status']='none';
          $acc_body['acc_number_funding']=$tx_detail->dest_account;

          //load figures into live transactions log
          $dta['acc_loader'][]=self::tx_acc_loader($acc_body,null);
          // send transit records for approval
        }
        
        
         $api_account=DB::table('tx_accounts')->where('acc_category','=','admin')->where('tx_value_type','=','wallet')->where('tx_channel','=',$tx_detail->tx_channel)->where('currency','=',$tx_detail->currency_destination)->where('active_status','=','active')->get();
         $api_account=$api_account[0];
         $prov_account=DB::table('tx_accounts')->where('acc_category','=','admin')->where('tx_value_type','=','provider')->where('currency','=',$tx_detail->currency_destination)->where('active_status','=','active')->get();
         $prov_account=$prov_account[0];

    //Register transit cash for provider reloading
          //get destination account detail          
          $acc_body['tx_id']=$tx_detail->tx_id."000-0".($h+1);
          $acc_body['tx_type']='deposit';
          $acc_body['acc_number']=$prov_account->acc_number;
          $acc_body['currency']=$tx_detail->currency_destination;
          $acc_body['cash_amount']=$tx_detail->amount_source;
          $acc_body['acc_number_funding']=$tx_detail->dest_account ??"";//$tx_detail->dest_account??"";
          $acc_body['tx_value_type']='provider';//'transit';//$dest_acc_detail->tx_value_type;
          $acc_body['acc_number_api']=$api_account->acc_number;//$dest_acc_detail->tx_value_type;
          $acc_body['tx_id_parent']=$tx_detail->tx_id;

          $dta['deposit'][]=self::tx_acc_loader($acc_body,null);

    //Register into API channel to show how much cash is accumulated via the API
          $acc_body['tx_id']=$tx_detail->tx_id."000-0".($h+2);
          $acc_body['tx_type']='deposit';
          $acc_body['acc_number']=$api_account->acc_number;
          $acc_body['currency']=$tx_detail->currency_destination;
          $acc_body['cash_amount']=$tx_detail->ref_amount;
          $acc_body['acc_number_funding']=$tx_detail->acc_number_funding??"";
          $acc_body['tx_value_type']='wallet';//$dest_acc_detail->tx_value_type;
          $acc_body['tx_id_parent']=$tx_detail->tx_id;

          $dta['deposit'][]=self::tx_acc_loader($acc_body,null);
    
        
    DB::table('ref_center_requests')->where('tx_id','=',$tx_detail->tx_id)->update([ 'active_status'=>'complete','paid_amount'=>$tx_detail->ref_amount ]);
    return $dta;
}


// Complete cash payment by reconciling with bank deposit
public static function tx_api_response_transit_settlement($tx_paid){
//get all recovery transactions pending from api_refs
   $dta=null;
    $cash_available=$tx_paid->api_amount;
    $tx_detail=DB::table('tx_api_refs')->where('tx_owner_type','=','office')
                                           ->where('recovery_flag','=','yes')
                                           ->where('tx_channel','=','transit')
                                           ->where('sync_status','=','yes')
                                           ->whereColumn('api_amount','>','recovered_amount')
                                           ->where('tx_owner_id','=',$tx_paid->tx_owner_id)
                                           ->get();
    for($t=0;  ($t < count($tx_detail) && $cash_available>0); $t++ )
      {
        $balance=$tx_detail[$t]->api_amount - $tx_detail[$t]->recovered_amount;
        $amount_paid=($balance > $cash_available)?$cash_available : $balance;
        $dta[] = self::transit_finisher($tx_detail[$t]->tx_id,$amount_paid);
        $cash_available-=$amount_paid;
      }
    return $dta;
}

// Handle approved transit trasaction
public static function tx_api_response_transit_approval($id){
//get the transaction detail in transit
    $tx_detail=DB::table('tx_account_logs')->find($id);
    $dta = self::transit_finisher($tx_detail->tx_id_parent,$tx_detail->cash_amount);
    return $dta;
}


//Process made to transit account log
public static function transit_finisher($tx_id_parent,$amount){
 $amount_original=$amount;
 $tx_detail=DB::table('tx_account_logs')->where('tx_id_parent','=',$tx_id_parent)
                                        ->where('tx_channel','=','transit')
                                        ->where('tx_type','!=','withdraw')
                                        ->where('param1','!=','tx_id_parent_transit')
                                        ->whereColumn('cash_amount','>','cash_amount_transit')
                                        ->get();
 
// if($tx_detail==null)
//   return;
 $size=count($tx_detail);
 
 for($g=0;($g < count($tx_detail) && $amount>0 );$g++)
    {//Get pending balance of the transit entity and determine how much is going to be paid depending on deposit available
     $unpaid=$tx_detail[$g]->cash_amount-$tx_detail[$g]->cash_amount_transit;
     $cash_amount = ( $unpaid <= $amount ) ? $unpaid : $amount;
     $amount-=$cash_amount;
     //Make withdraw
                        $acc_body=['tx_id'=>$tx_detail[$g]->tx_id.$g.$tx_detail[$g]->user_id.(MethodsController::class)::code_generator(5)."01",
                                   'acc_number'=>$tx_detail[$g]->acc_number,
                                   'source_account'=>$tx_detail[$g]->source_account??"",
                                   'dest_account'=>$tx_detail[$g]->dest_account??"",
                                   'tx_type'=>'withdraw',
                                   'currency'=>$tx_detail[$g]->currency,
                                   'currency_source'=>$tx_detail[$g]->currency_source??"",
                                   'currency_destination'=>$tx_detail[$g]->currency_destination??"",
                                   'cash_amount'=>($cash_amount * -1),
                                   'tx_id_parent'=>$tx_detail[$g]->tx_id_parent,
                                   'tx_value_type'=>$tx_detail[$g]->tx_value_type,  //tx_value_type_source,
                                   'tx_flag'=>$tx_detail[$g]->tx_flag,     // normal   - transit   - profit - taken/paid
                                   'transit_status'=>'none',    // pending  -   none    -       complete
                                   'tx_channel'=>$tx_detail[$g]->tx_channel,
                                   'param1'=>'tx_id_parent_transit',
                                   'param2'=>$tx_detail[$g]->tx_id
                                  ];     //similar to that in api_ref
          $dta['withdraw'][]=self::tx_acc_loader($acc_body,null);
          
          self::transit_status_finisher($tx_id_parent,$tx_detail[$g]->tx_id);         // evaluate and update transit flags
          
     //prepare deposit
      if($tx_detail[$g]->tx_type_transit==="deposit" || $tx_detail[$g]->tx_type_transit==="transfer"){
          //get destination account detail          
          $params=null;
          $params['condition'][]=[' and ','ac.acc_number','=',$acc_body['dest_account']];
          $dest_acc_detail=(qrymkrController::class)::tx_accounts_get($params);
          $dest_acc_detail=$dest_acc_detail[0] ?? "";

          $acc_body['tx_id']=$tx_detail[$g]->tx_id.$g.$tx_detail[$g]->user_id.(MethodsController::class)::code_generator(5)."02";
          $acc_body['tx_type']='deposit';
          $acc_body['acc_number']=$tx_detail[$g]->dest_account;
          $acc_body['currency']=$tx_detail[$g]->currency_destination;
          $acc_body['cash_amount']=$cash_amount;
          $acc_body['acc_number_funding']=$tx_detail[$g]->acc_number_funding??"";
          $acc_body['tx_value_type']=$dest_acc_detail->tx_value_type;

          $dta['deposit'][]=self::tx_acc_loader($acc_body,null);
      }
    }
    
    //Record the duplicate activity onto the corresponding business bank account
    $tx_detail_ref=DB::table('tx_api_refs')->where('tx_id','=',$tx_id_parent)->first();
    $account_detail_office=DB::table('tx_accounts')->where('tx_channel','=',$tx_detail_ref->tx_channel ?? 'bank')->where('acc_category','=','admin')->where('active_status','=','active')->where('currency','=',(($tx_detail_ref->tx_type === 'withdraw') ? $tx_detail_ref->currency_source : $tx_detail_ref->currency_destination))->first();
            if($tx_detail_ref->tx_type === 'withdraw'){
//                Log::channel('slackNotification')->info('DDD', ['tx_type' => $account_detail_office->acc_number]);

                        $acc_body=['tx_id'=>$tx_detail_ref->tx_id.(MethodsController::class)::code_generator(3)."003330",
                                   'acc_number'=>$account_detail_office->acc_number,
                                   'source_account'=>($tx_detail_ref->tx_type === 'withdraw') ? $tx_detail_ref->source_account : $tx_detail_ref->dest_account,
                                   'tx_type'=>$tx_detail_ref->tx_type,
                                   'currency'=>($tx_detail_ref->tx_type === 'withdraw') ? $tx_detail_ref->currency_source : $tx_detail_ref->currency_destination,
                                   'currency_source'=>$tx_detail_ref->currency_source,
                                   'cash_amount'=>($tx_detail_ref->tx_type === 'withdraw') ? $amount_original*-1 : $amount_original,
                                   'tx_id_parent'=>"LOGTR-".$tx_detail_ref->tx_id,
                                   'tx_value_type'=>$tx_detail_ref->tx_value_type,
                                   'tx_flag'=>'normal',     // normal   - transit   - profit
                                   'transit_status'=>'none',
                                   'tx_channel'=>$tx_detail_ref->tx_channel ?? 'bank'
                                  ];     //similar to that in api_ref
//                Log::channel('slackNotification')->info('EEE', $acc_body);
                if($acc_body['acc_number']!==$acc_body['source_account'])       // Ensure not to duplicate transactions that are sourced from admin office accounts
                $dta['system_log']=self::tx_acc_loader($acc_body,null);
            }

}

//score transit status
public static function transit_status_finisher($tx_id_parent,$tx_id_parent_transit){
    // get total transfered 
          $transit_paid=DB::table('tx_account_logs')->where('tx_id_parent','=',$tx_id_parent)->where('tx_type','=','withdraw')->sum('cash_amount');
    //update total recovered via api refs. This is to show how mush cash has been banked by agent and how much isn't yet banked or approved via deposit
          $affected=DB::table('tx_api_refs')->where('tx_id','=',$tx_id_parent)
                                            ->where('recovery_flag','=','yes')
                                            ->update(['recovered_amount'=>($transit_paid * -1) ]);

    //update total transfered in the parent transit log
          $transit_paid=DB::table('tx_account_logs')->where('param1','=','tx_id_parent_transit')->where('param2','=',$tx_id_parent_transit)->where('tx_type','=','withdraw')->sum('cash_amount');
          $affected=DB::table('tx_account_logs')->where('tx_id','=',$tx_id_parent_transit)
                                                ->update(['cash_amount_transit'=>($transit_paid * -1)]);
          $affected=DB::table('tx_account_logs')->where('tx_id','=',$tx_id_parent_transit)
                                                ->where('cash_amount','<=','cash_amount_transit')
                                                ->update(['transit_status'=>'complete']);
}


//transfer cash from wallet to wallet   $params=['tx_id','source_account','dest_account','currency_source','currency_destination','cash_amount','tx_channel','tx_value_type_source','tx_value_type_dest']
public static function transfer_acc_internal($params){
    //register withdraw
        $dta['source']['tx_id']=$params['tx_id'];
                        $acc_body=['tx_id'=>$params['tx_id'],
                                   'acc_number'=>$params['source_account'],
                                   'source_account'=>$params['source_account'],
                                   'dest_account'=>$params['dest_account']??"",
                                   'tx_type'=>'withdraw',
                                   'currency'=>$params['currency_source'],
                                   'currency_source'=>$params['currency_source']??"",
                                   'currency_destination'=>$params['currency_destination']??"",
                                   'cash_amount'=>($params['amount_source']*-1),
                                   'tx_id_parent'=>$params['tx_id'],
                                   'tx_value_type'=>$params['tx_value_type_source'],
                                   'tx_flag'=>'normal',     // normal   - transit   - profit
                                   'transit_status'=>'none',
                                   'tx_channel'=>$params['tx_channel']
                                  ];     //similar to that in api_ref
          $dta['source']['body']=self::tx_acc_loader($acc_body,null);
    //register deposit
        $dta['dest']['tx_id']=$params['tx_id']."000101";
                        $acc_body=['tx_id'=>$params['tx_id']."000101",
                                   'acc_number'=>$params['dest_account'],
                                   'source_account'=>$params['source_account'],
                                   'dest_account'=>$params['dest_account']??"",
                                   'tx_type'=>'deposit',
                                   'currency'=>$params['currency_source'],
                                   'currency_source'=>$params['currency_source']??"",
                                   'currency_destination'=>$params['currency_destination']??"",
                                   'cash_amount'=>$params['amount_source'],
                                   'tx_id_parent'=>$params['tx_id'],
                                   'tx_value_type'=>$params['tx_value_type_dest'],
                                   'tx_flag'=>'normal',     // normal   - transit   - profit
                                   'transit_status'=>'none',
                                   'tx_channel'=>$params['tx_channel']
                                  ];     //similar to that in api_ref
          $dta['dest']['body']=self::tx_acc_loader($acc_body,null);
    return $dta;
}    


// process withdraw transaction

public static function withdraw_process($tx_detail){
   $acc_number_receiving=$tx_detail->dest_account;
//Check whether it is a direct deposit to destination account or through transit and approval
    $cash_in_transit=($tx_detail->tx_channel==="transit");
    if($cash_in_transit)
    {
//Get transit account
    $channel_detail=DB::table('tx_accounts')->where('acc_category','=','admin')
                                            ->where('currency','=',$tx_detail->currency_destination)
                                            //->where('tx_value_type','=',$tx_detail->tx_value_type)
                                            ->where('tx_channel','=',$tx_detail->tx_channel)
                                            ->where('active_status','=','active')
                                            ->get();
    $channel_detail=$channel_detail[0];
    $acc_transit=$channel_detail->acc_number;
    $acc_number_receiving=$acc_transit;
    }
    //prepare default tx query body
                        $acc_body=['tx_id'=>$tx_detail->tx_id,
                                   'acc_number'=>$tx_detail->source_account,
                                   'source_account'=>$tx_detail->source_account,
                                   'dest_account'=>$tx_detail->dest_account,
                                   'tx_type'=>($tx_detail->tx_type==="transfer")?'withdraw':$tx_detail->tx_type,
                                   'tx_type_transit'=>$tx_detail->tx_type, //'withdraw',
                                   'currency'=>$tx_detail->currency_source,
                                   'currency_source'=>$tx_detail->currency_source??"",
                                   'currency_destination'=>$tx_detail->currency_destination,
                                   'cash_amount'=>($tx_detail->api_amount * -1),
                                   'tx_id_parent'=>$tx_detail->tx_id,
                                   'tx_value_type'=>$tx_detail->tx_value_type,
                                   'tx_flag'=>'normal',     // normal   - transit   - profit
                                   'transit_status'=>'none',
                                   'slip_no'=>$tx_detail->slip_no,
                                   'tx_channel'=>$tx_detail->tx_channel,
                                   'acc_number_funding'=>$tx_detail->acc_number_funding ?? ""
                                  ];     //similar to that in api_ref
    $withdrawing_account_detail=DB::table('tx_accounts')->where('acc_number','=',$tx_detail->source_account)->first();
    // -ve negative entry into withdrawing account
          $acc_body['tx_value_type']=$withdrawing_account_detail->tx_value_type;
          $dta['acc_loader'][]=self::tx_acc_loader($acc_body,null);

    // +ve positive entry into transit account if the transaction is a transit
      $dta=null;
      if($cash_in_transit){
          $acc_body['tx_value_type']=$tx_detail->tx_value_type;
          $acc_body['tx_id']=$acc_body['tx_id']."00050";
          $acc_body['acc_number']=$acc_number_receiving;
          $acc_body['cash_amount']=$tx_detail->api_amount;
          $acc_body['transit_status']='pending';
          $acc_body['tx_type']='deposit';
          $acc_body['tx_flag']='transit';
//REVIEW
/*
          if($tx_detail->investment_flag==="yes")
            $acc_body['acc_number_funding']=$tx_detail->source_account ?? "";
*/
          //load figures into live transactions log
          $dta=self::tx_acc_loader($acc_body,null);
          $dta=json_decode($dta,true);
          // send transit records for approval
           
           $params=array('entity_type'=>'request',
                                'approve_ref_id'=>$acc_body['tx_id'],
                                'approve_action'=>'approve',
                                'approve_narration'=>$acc_body['tx_value_type']." - ".$acc_body['tx_type_transit']." ".$acc_body['currency']." ".number_format($acc_body['cash_amount'],0)."  - slipNo.".$acc_body['slip_no']." ".$dta['cash_amount'],
                                'approve_count'=>$tx_detail->approve_count??"2",
                                'approver_role'=>"office",
                                'user_id'=>$tx_detail->user_id,
                                'source_table'=>'tx_account_logs',
                                'source_column'=>'id',
                                'source_id'=>$dta['id'],
                                'approve_scope'=>'office',        // user - office
                                'ref_center_id'=>''
                                );
                    $dta['body_approval'][]=(MethodsController::class)::approval_register($params);
          }
    return $dta;
}



// process deposit transction

public static function deposit_process($tx_detail){
   $acc_number_receiving=$tx_detail->dest_account;
//Check whether it is a direct deposit to destination account or through transit and approval
    $cash_in_transit=($tx_detail->tx_channel==="transit");
    if($cash_in_transit)
    {
//Get transit account
    $channel_detail=DB::table('tx_accounts')->where('acc_category','=','admin')
                                            ->where('currency','=',$tx_detail->currency_destination)
                                            ->where('tx_value_type','=',$tx_detail->tx_value_type)
                                            ->where('tx_channel','=',$tx_detail->tx_channel)
                                            ->where('active_status','=','active')
                                            ->get();
//    Log::channel('slackNotification')->info('AGENT CASH', ['Deposite_Credit_Pay'=> json_encode(json_decode($channel_detail)),'currency','=',$tx_detail->currency_destination,'tx_value_type','=',$tx_detail->tx_value_type,'tx_channel','=',$tx_detail->tx_channel ]);
    $channel_detail=$channel_detail[0];
    $acc_transit=$channel_detail->acc_number;
    $acc_number_receiving=$acc_transit;
    }

//Distribute deposit including profit recipients        returns [acc_number,cash_amount]
   $accounts_paylist=self::tx_response_paylist($tx_detail,$tx_detail->dest_account); //$acc_number_receiving
    //prepare default tx query body for reuse through the list of account found
                        $acc_body=['tx_id'=>$tx_detail->tx_id,
                                   'acc_number'=>$tx_detail->dest_account,
                                   'source_account'=>"",
                                   'dest_account'=>$tx_detail->dest_account,
                                   'tx_type'=>'deposit',
                                   'tx_type_transit'=>'deposit',
                                   'currency'=>$tx_detail->currency_destination,
                                   'currency_source'=>$tx_detail->currency_source??"",
                                   'currency_destination'=>$tx_detail->currency_destination,
                                   'cash_amount'=>$tx_detail->api_amount,
                                   'tx_id_parent'=>$tx_detail->tx_id,
                                   'tx_value_type'=>$tx_detail->tx_value_type,
                                   'tx_flag'=>'normal',     // normal   - transit   - profit
                                   'transit_status'=>'none',
                                   'slip_no'=>$tx_detail->slip_no,
                                   'tx_channel'=>$tx_detail->tx_channel
                                  ];     //similar to that in api_ref

    //make deposites into the live account log. Only change ncessary values from $acc_body above and deposite
      $dta=null;
      for($h=0;($accounts_paylist!=null && $h<count($accounts_paylist) );$h++){
          $acc_body['tx_id']=($h>0)?$acc_body['tx_id'].(MethodsController::class)::code_generator(3)."001110".$h:$acc_body['tx_id'];
          $acc_body['acc_number']=($cash_in_transit)?$acc_number_receiving:$accounts_paylist[$h]['acc_number'];
          $acc_body['dest_account']=$accounts_paylist[$h]['acc_number'];
          $acc_body['source_account']=($cash_in_transit)?$acc_number_receiving:"";
          $acc_body['cash_amount']=$accounts_paylist[$h]['cash_amount'];
          $acc_body['tx_flag']=$accounts_paylist[$h]['tx_flag'];
          $acc_body['transit_status']=($cash_in_transit)?'pending':'none';
          $acc_body['acc_number_funding']=$accounts_paylist[$h]['acc_number_funding']??"";

          //load figures into live transactions log
          $dta['acc_loader'][]=self::tx_acc_loader($acc_body,null);
          // send transit records for approval
        }
     if($cash_in_transit && $tx_detail->recovery_flag==="no"){
                   $params=array('entity_type'=>'request',
                                'approve_ref_id'=>$acc_body['tx_id'],
                                'approve_action'=>'approve',
                                'approve_narration'=>$acc_body['tx_value_type']." - ".$acc_body['tx_type']." - slipNo.".$acc_body['slip_no'],
                                'approve_count'=>$tx_detail->approve_count??"2",
                                'approver_role'=>"office",
                                'user_id'=>$tx_detail->user_id,
                                'source_table'=>'tx_account_logs',
                                'source_column'=>'id',
                                'source_id'=>$tx_detail->id,
                                'approve_scope'=>'office',        // user - admin
                                'ref_center_id'=>''
                                );
                    $dta['body_approval'][]=(MethodsController::class)::approval_register($params);
          }
    else if($tx_detail->recovery_flag==="yes")
       {
//PAUSED        $affected=DB::table('tx_api_refs')->where('tx_id','=',$tx_detail->tx_id)->update(['recovery_flag'=>'yes']);
        $dta['recovery update']=(MethodsController::class)::api_ref_recovery_sync($tx_detail->tx_id);
       }

    return $dta;
}


//ref_center_requests logs created basing on tx_api_refs
public static function collection_finisher($id){
$dta=null;
$proceed=true;    
$paid_log=null;
//Get the detail of the trasaction
  $tx_detail=DB::table('tx_api_refs')->where('id','=',$id)
                                     ->where('tx_status','=','pending')
                                     ->where('tx_owner_type','=','group')
                                     ->first();
  if($tx_detail==null || empty($tx_detail))
    {
     $proceed=false;
     $dta['message']="Transaction not found";
    }

//Verify that the transaction has no logs created
  $found_logs=DB::table('ref_center_requests_logs')->where('tx_id_ref_source_table','=','tx_api_refs')->where('tx_id_ref','=',$tx_detail->tx_id)->count();
  if($found_logs > 0)
    {
     $proceed=false;
     $dta['message']="Transaction has a log";
    }

//Confirm that it's not a credit action
  if(($tx_detail->tx_service_cat!=null && $tx_detail->tx_service_cat !== "collection" && $tx_detail->tx_service_cat !== "campaign") || $tx_detail->request_service_id == null || $tx_detail->request_service_id ==="" )
    {
     $proceed=false;
     $dta['message']="Invalid service category CAT-".$tx_detail->tx_service_cat."- ID-".$tx_detail->request_service_id."-";
    }
if(!$proceed)
  return $dta;

//Get its detail from center requests types
$request_detail=DB::table('ref_center_request_types')->find($tx_detail->request_service_id);
                                                    

$flow_type=($tx_detail->tx_type==="withdraw")?"outflow":"inflow";
$currency=($tx_detail->tx_type==="withdraw")?$tx_detail->currency_source:$tx_detail->currency_destination;
$amount=($tx_detail->tx_type==="withdraw")?($tx_detail->api_amount * -1) : $tx_detail->api_amount;

$unpaid_collections=DB::table('ref_center_requests')->where('request_service_id','=',$tx_detail->request_service_id)
                                                ->where('member_id','=',$tx_detail->ref_id)
                                                ->where('balance','!=','0')
                                                ->get();
//Prepair request detail for loading into requests and their logs
                    $rq_data=null;
                    $rq_data['ref_center_id']=$tx_detail->tx_owner_id;
                    $rq_data['request_type_id']=$tx_detail->tx_service_cat_ID;
                    $rq_data['request_type']=$tx_detail->tx_service_cat;
                    $rq_data['user_id']=$tx_detail->user_id;
                    $rq_data['admin_id']=$tx_detail->user_id;
                    $rq_data['member_id']=$tx_detail->ref_id;
                    $rq_data['tx_id']=$tx_detail->tx_id;
                    $rq_data['currency']=$currency;
                    $rq_data['flow_type']=$flow_type;
                    $rq_data['taken_amount']=($tx_detail->tx_type==="withdraw") ? $amount:"0";
                    $rq_data['paid_amount']=($tx_detail->tx_type==="deposit")?$amount:"0";
                    $rq_data['tx_value_type']=$tx_detail->tx_value_type;
                    $rq_data['detail1']=$tx_detail->ref_id;
                    $rq_data['detail2']=$tx_detail->param2;
                    $rq_data['request_service_id']=$tx_detail->request_service_id;


                    $log_data=null;
                    $log_data['flow_type']=$flow_type;
                    $log_data['ref_center_id']=$tx_detail->tx_owner_id;
                    $log_data['request_type_id']=$tx_detail->tx_service_cat_ID;
                    $log_data['slip_no']=$tx_detail->slip_no;
                    $log_data['admin_id']=$tx_detail->user_id;
                    $log_data['member_id']=$tx_detail->ref_id;
                    $log_data['currency']=$currency;
                    $log_data['tx_value_type']=$tx_detail->tx_value_type;
                    $log_data['tx_id_ref']=$tx_detail->tx_id;
                    $log_data['tx_id_ref_source_table']='tx_api_refs';
                    $log_data['request_service_id']=$tx_detail->request_service_id;

// Create new request if this member has no requests there and the request type is not a scheduled one
                if($unpaid_collections==null || empty($unpaid_collections) || count($unpaid_collections) < 1 )
                    {
                     //Create unscheduled request
                     if($request_detail->interval==0 || $request_detail->amount==0 )
                         {//Load request into requests summary table
                          $rq_data['payback_amount']=$rq_data['paid_amount'];
                          $rq_data['balance']=$rq_data['paid_amount'];
                          $query['table']="ref_center_requests";
                          $query['column_values']=$rq_data;
                          $query['condition']=null;
                          $query['extras']=null;
                          $unpaid_collections=null;
                          $unpaid_collections[]=(qryrnrController::class)::insert1($query);
                         }
                     else
                         {
                          //Create scheduled requests enough to consume the paid amount
                          $params=null;
                          $params['request_service_id']=$tx_detail->request_service_id;
                          $params['member_id']=$tx_detail->ref_id;
                          $params['name']=$tx_detail->param2;
                          $unpaid_collections=(qrymkrController::class)::request_schedule_maker($params,$amount);
                         }
                    }
//Send SMS to acknowledge deposit
//Get contact from member table
$member_detail_1=DB::table('ref_center_members')->where('member_id','=',$tx_detail->ref_id)
                                                ->get();
                             $content="Amount ".number_format($amount)." ) ".$tx_detail->tx_type."is successfull";
                             $to=(MethodsController::class)::mobile_phone_maker(['mobile'=>$member_detail_1[0]->mobile,'currency'=>$currency]);
                             //Share SMS to back office pannel
                             Log::channel('slackNotification')->info('Deposit Received '.$amount, ['Mobile' => $to,'SMS' => $content]);
                             //send receipt notice to client via SMS 
                             (MethodsController::class)::send_sms_socnet($content,null,$to);

$dta['unpaid']=$unpaid_collections;
//return $dta;
//return (json_decode($unpaid_collections[$t],true))->balance;
// Load deposites for each uncollected request
                for($t=0;($unpaid_collections!=null && $t<count($unpaid_collections) && $amount > 0 ); $t++ )
                  {
//                    $unpaid_collections[$t]=(object)json_decode($unpaid_collections[$t],true);
                    if(!is_object($unpaid_collections[$t]))
                      $unpaid_collections[$t]=(object)json_decode($unpaid_collections[$t],true);
                    
                    $paid=($unpaid_collections[$t]->balance <= $amount)?$unpaid_collections[$t]->balance:$amount;
                    $amount-=$paid;
                    //Load request into logs table
                    $log_data['ref_center_request_id']=$unpaid_collections[$t]->id;
//                    $log_data['amount']=($log_data['flow_type'] === "inflow") ? $paid : ($paid * -1);
                    $log_data['amount']=$paid;
                    $log_data['request_code']=$unpaid_collections[$t]->request_code;

                    $query['table']="ref_center_requests_logs";
                    $query['column_values']=$log_data;
                    $query['condition']=null;
                    $query['extras']=null;
                    $paid_log[]=(qryrnrController::class)::insert1($query);
                    
                    //Update request detail figures
                    self::tx_center_request_sync($unpaid_collections[$t]->id);
                  }
    $dta['paid_log']=$paid_log;
    return $dta;
}

//Transfer figures to live account log
// params[amount - profit flag - account number - tx_id_parent ] or $column_values[]=[amount - profit flag - account number - tx_id_parent]
public static function tx_acc_loader($acc_body=null,$column_values=null){
       $dta=null;
       $params['table']="tx_account_logs";
       if($column_values==null){
        $params['column_values']=$acc_body;
        $params['condition']=null;
        $params['extras']=null;
        $dta=(qryrnrController::class)::insert1($params);
        //$dta=json_decode($dta,true);
        //update account figures
         (MethodsController::class)::account_sync([ 'acc_number'=>$acc_body['acc_number'] ]);
        }
       else if($column_values!=null)
        $dta=(qrymkrController::class)::insert1_bulk($params);
    return $dta;
}

public static function ref_center_requests_logs_create($params=null){
       $dta=null;
       $params['table']="ref_center_requests_logs";
        $params['condition']=null;
        $params['extras']=null;
        $dta['body']=(qryrnrController::class)::insert1($params);
        $dta['body']=json_decode($dta['body'],true);
    return $dta;
}


//Compute and settle requests attached to transaction and return accounts with their amount for logging inlcudgin profits status and amount
//[acc_number,source_account,dest_account,amount,tx_type,tx_flag]
public static function tx_response_paylist_release($tx_detail,$acc_number_receiving=null){
    $deposit_references=null;   // carries [amount_paid,profit_made,porfit_rate_office,profit_rate_agent,profit_rate_center
    
    $cash=$tx_detail->api_amount;
    
      //get attributed request with its profit share rates
        $params['condition'][]=[' and ','rq.tx_id','=',$tx_detail->tx_id];
        $request_detail=(qrymkrController::class)::ref_center_requests_get($params);    //get the list of all transactions pedning for this member under this request type
        $request_detail=$request_detail['body']??null;
        $request_detail=$request_detail[0];

        $principal_paid=$request_detail->requested_amount; //($tx_detail->ref_amount > $tx_detail->api_amount) ? $tx_detail->api_amount : $tx_detail->ref_amount;          //Determine how much goes to pincipal
        $profit_paid=$request_detail->interest;                                  //Get the progit to be paid

    //Make withdraw on provider's account
                                        $acc_body=['tx_id'=>$tx_detail->tx_id,
                                                   'acc_number'=>$request_detail->acc_number_provider,      
                                                   'source_account'=>$request_detail->acc_number_provider,
                                                   'dest_account'=>"",
                                                   'tx_type'=>'withdraw',
                                                   'currency'=>$request_detail->currency,
                                                   'currency_source'=>$request_detail->currency,
                                                   'currency_destination'=>$request_detail->currency,
                                                   'cash_amount'=>($request_detail->requested_amount * -1),
                                                   'tx_id_parent'=>$tx_detail->tx_id,
                                                   'tx_value_type'=>'provider',  //tx_value_type_source,
                                                   'tx_flag'=>'taken',     // normal   - transit   - profit - taken/paid
                                                   'transit_status'=>'none',    // pending  -   none    -       complete
                                                   'tx_channel'=>'provider'
                                                  ];     //similar to that in api_ref
                          $dta['withdraw']=(TxapiController::class)::tx_acc_loader($acc_body,null);
                          (MethodsController::class)::account_sync([ 'acc_number'=>$request_detail->acc_number_provider ]);

        //load capital deposit to destination account of provider
//        $deposit_references[]=['acc_number'=>$request_detail->acc_number_provider, 'cash_amount'=>$request_detail->requested_amount,'tx_flag'=>'provider' ];

        if($profit_paid>0){
            //load office based profit accounts to request log
               $profit_office=($profit_paid*$request_detail->interest_business??0)/10;
               $deposit_references[]=['acc_number'=>$request_detail->acc_number_office, 'cash_amount'=>$profit_office,'tx_flag'=>'profit'];
               
               $profit_agent=($profit_paid*$request_detail->interest_agent??0)/10;
               $deposit_references[]=['acc_number'=>$request_detail->acc_number_agent, 'cash_amount'=>$profit_agent,'tx_flag'=>'profit'];

               //Get center's profit accounts and their rates and add them to the profit share
               $params=null;
               $params['condition'][]=[' and ',' p.acc_number_giver','=',$request_detail->acc_number_center];
               $center_acc=(qrymkrController::class)::profit_dependants_get($params);
               $center_acc=$center_acc['body'];
               $profit_center=($profit_paid*$request_detail->interest_center_head??0)/10;
               $share_available=100;
               for($k=0;($k<count($center_acc)  && $share_available>0 );$k++)
                {
                 $rate=($share_available < $center_acc[$k]->rate)?$share_available : $center_acc[$k]->rate;
                 $share_available-=$rate;
                 $deposit_references[]=['acc_number'=>$center_acc[$k]->acc_number_receiver, 'cash_amount'=>($profit_center*($rate??0)/100),'tx_flag'=>'profit'];
                }

           //reduce cash deposited by profit amount
               //Get investor's profit accounts and their rates and add them to the profit share
               $profit_investor=$profit_paid - $profit_office - $profit_agent - $profit_center;
               $params=null;
               $params['condition'][]=[' and ',' p.acc_number_giver','=',$request_detail->acc_number_investor];
               $investors=(qrymkrController::class)::profit_dependants_get($params);
               $investors=$investors['body'];
               if($investors!=null && count($investors)>0 && !empty($investors) && $investors[0]!=null )
                 {
                 $share_available=100;
                 for($k=0;($k<count($investors) && $share_available>0 );$k++)
                   {
                    $rate=($share_available < $investors[$k]->rate)?$share_available : $investors[$k]->rate;
                    $share_available-=$rate;
                    $deposit_references[]=['acc_number'=>$investors[$k]->acc_number_receiver, 'cash_amount'=>($profit_investor*($rate??0)/100),'tx_flag'=>'profit'];
                   }
                 }
               else
                  $deposit_references[]=['acc_number'=>$request_detail->acc_number_investor, 'cash_amount'=>$profit_investor,'tx_flag'=>'profit'];
           }
      return $deposit_references;
}



//Compute and settle requests attached to transaction and return accounts with their amount for logging inlcudgin profits status and amount
//[acc_number,source_account,dest_account,amount,tx_type,tx_flag]
public static function tx_response_paylist($tx_detail,$acc_number_receiving=null){
    $deposit_references=null;   // carries [amount_paid,profit_made,porfit_rate_office,profit_rate_agent,profit_rate_center
    $cash=$tx_detail->api_amount;
    
    //load whole deposit to destination account if is normal deposite without center request
    $deposit_references[]=['acc_number'=>$acc_number_receiving, 'cash_amount'=>$tx_detail->api_amount,'tx_flag'=>$tx_detail->tx_value_type ];
    

    //check if the deposit involves request
    if($tx_detail->tx_service_cat!=null && $tx_detail->tx_service_cat_ID!=null && $tx_detail->tx_service_cat!=='collection' && $tx_detail->tx_service_cat!=='campaign'){
      //get attributed request with its profit share rates
        $deposit_references=null;
        $params['condition'][]=[' and ','rq.currency','=',$tx_detail->currency_destination];
        $params['condition'][]=[' and ','rq.active_status','=','processed'];
        $params['condition'][]=[' and ','rq.balance','>',0];
        $params['condition'][]=[' and ','rq.member_id','=',$tx_detail->ref_id];
        $params['condition'][]=[' and ','rq.request_type_id','=',$tx_detail->tx_service_cat_ID];
        $request_detail=(qrymkrController::class)::ref_center_requests_get($params);    //get the list of all transactions pedning for this member under this request type
    
        $request_detail=$request_detail['body']??null;
        //process all found unpaid requests
        for($g=0;$g<count($request_detail)&&$cash>0;$g++)
          {
//           $principal_paid=($paid > $request_detail[$g]->profit)?($paid - $request_detail[$g]->profit):0;
           $pincipal_bal=($request_detail[$g]->taken_amount - $request_detail[$g]->paid_amount);                //get the outstanding principal unpaid
           $pincipal_bal=($pincipal_bal < 0) ? 0 : $pincipal_bal;


           $balance=$request_detail[$g]->balance;
           $paid=($balance>=$cash)?$cash:$balance;
           $cash-=$paid;
//            Load payment deposit into the request log
                    $tx_data['ref_center_request_id']=$request_detail[$g]->id;
                    $tx_data['amount']=$paid;
                    $tx_data['flow_type']='inflow';
                    $tx_data['ref_center_id']=$request_detail[$g]->ref_center_id;
                    $tx_data['request_type_id']=$request_detail[$g]->request_type_id;
                    $tx_data['slip_no']=$request_detail[$g]->request_code;
                    $tx_data['admin_id']=$tx_detail->user_id;
                    $tx_data['member_id']=$tx_detail->ref_id;
                    $tx_data['request_code']=$request_detail[$g]->request_code;
                    $tx_data['currency']=$request_detail[$g]->currency;
                    $tx_data['tx_value_type']=$tx_detail->tx_value_type;
                    $tx_data['tx_id_ref']=$tx_detail->tx_id;
                    $tx_data['tx_id_ref_source_table']='tx_api_refs';
                    //Load request into table
                    $query['table']="ref_center_requests_logs";
                    $query['column_values']=$tx_data;
                    $query['condition']=null;
                    $query['extras']=null;
                    $dta['log']=(qryrnrController::class)::insert1($query);

           //update figures of the request
           self::tx_center_request_sync($request_detail[$g]->id);   //update figures with what is in the log

           $principal_paid=($paid > $pincipal_bal) ? $pincipal_bal: $paid;          //Determine how much goes to pincipal
           $profit_paid=$paid-$principal_paid;                                  //Get the progit to be paid

           //Settle principle paid
               //Load loan account paiment and attach the funding account to be referenced when re-investing money to the provider
           if($principal_paid>0)
           $deposit_references[]=['acc_number'=>$acc_number_receiving, 'cash_amount'=>$principal_paid,'tx_flag'=>'repaid','acc_number_funding'=>$request_detail[$g]->acc_number_provider ];
           
           
           if($profit_paid>0){
            //load office based profit accounts to request log
               $profit_office=($profit_paid*$request_detail[$g]->interest_business??0)/10;
               $deposit_references[]=['acc_number'=>$request_detail[$g]->acc_number_office, 'cash_amount'=>$profit_office,'tx_flag'=>'profit'];
               
               $profit_agent=($profit_paid*$request_detail[$g]->interest_agent??0)/10;
               $deposit_references[]=['acc_number'=>$request_detail[$g]->acc_number_agent, 'cash_amount'=>$profit_agent,'tx_flag'=>'profit'];

               //Get center's profit accounts and their rates and add them to the profit share
               $params=null;
               $params['condition'][]=[' and ',' p.acc_number_giver','=',$request_detail[$g]->acc_number_center];
               $center_acc=(qrymkrController::class)::profit_dependants_get($params);
               $center_acc=$center_acc['body'];
               $profit_center=($profit_paid*$request_detail[$g]->interest_center_head??0)/10;
               $share_available=100;
               for($k=0;($k<count($center_acc)  && $share_available>0 );$k++)
                {
                 $rate=($share_available < $center_acc[$k]->rate)?$share_available : $center_acc[$k]->rate;
                 $share_available-=$rate;
                 $deposit_references[]=['acc_number'=>$center_acc[$k]->acc_number_receiver, 'cash_amount'=>($profit_center*($rate??0)/100),'tx_flag'=>'profit'];
                }

           //reduce cash deposited by profit amount
               //Get investor's profit accounts and their rates and add them to the profit share
               $profit_investor=$profit_paid - $profit_office - $profit_agent - $profit_center;
               $params=null;
               $params['condition'][]=[' and ',' p.acc_number_giver','=',$request_detail[$g]->acc_number_investor];
               $investors=(qrymkrController::class)::profit_dependants_get($params);
               $investors=$investors['body'];
               if($investors!=null && count($investors)>0 && !empty($investors) && $investors[0]!=null )
                 {
                 $share_available=100;
                 for($k=0;($k<count($investors) && $share_available>0 );$k++)
                   {
                    $rate=($share_available < $investors[$k]->rate)?$share_available : $investors[$k]->rate;
                    $share_available-=$rate;
                    $deposit_references[]=['acc_number'=>$investors[$k]->acc_number_receiver, 'cash_amount'=>($profit_investor*($rate??0)/100),'tx_flag'=>'profit'];
                   }
                 }
               else
                  $deposit_references[]=['acc_number'=>$request_detail[$g]->acc_number_investor, 'cash_amount'=>$profit_investor,'tx_flag'=>'profit'];
           }
          }
    }
      return $deposit_references;
}

//Update values for the request at the center side
public static function tx_center_request_sync($ref_center_request_id){
//  $taken_amount = DB::table('ref_center_requests_logs')->where('ref_center_request_id','=',$ref_center_request_id)->where('flow_type','=','outflow')->sum('amount');
  $paid_amount = DB::table('ref_center_requests_logs')->where('ref_center_request_id','=',$ref_center_request_id)->where('flow_type','=','inflow')->sum('amount');
  $affected=DB::table('ref_center_requests')->where('id','=',$ref_center_request_id)->update(['paid_amount'=>$paid_amount??0 ]);
  DB::unprepared("update ref_center_requests set balance=payback_amount-paid_amount where id = '".$ref_center_request_id."'");
  DB::unprepared("update ref_center_requests set active_status='complete' where id = '".$ref_center_request_id."' and balance <= 0");
}

//Distribute profits attibuted to a transaction
public static function tx_profit_handler($tx_detail){
//check 
    
}

public static function tx_api_responder(Request $rq){
  
}



//check values that allow transaction to continue [currency - tx_value_type - dest_account - source_account - currency - amount - user_id - tx_value_type - tx_channel]
public static function tx_validator($params){
/*
//if it is a request being processed verify that user has access to that role must be of office and access a table of provider
    if($params['tx_value_type']==="cash" && $params['tx_service_cat_ID']??null!=null){
        $params['condition'][]=[' and ','a.user_id','=',$params['user_id']];
        $params['condition'][]=[' and ','a.access_scope','=','office'];
        $params['condition'][]=[' and ','a.access_table','=','providers'];
        $params['condition'][]=[' and ','a.active_status','=','active'];
        $roles=(qryrnrController::class)::user_access_get($params);
        if($roles==null || empty($roles))
            $status=false;
    }
*/    
    $deposit=($params['tx_type']==="deposit");
    $proceed['status']="success";
    $proceed['message']="";
//Get transaction charges and limits from system account settings
    $channel_detail=DB::table('tx_accounts')//->where('invite_code',' like ','%AD00') // user_uuid
                                            ->where('acc_category','=','admin')
                                            ->where('currency','=',$params['currency'])
                                            ->where('tx_value_type','=',$params['tx_value_type'])
                                            ->where('active_status','=','active')
                                            ->get();
    $params1=['currency'=>$params['currency'],'tx_value_type'=>$params['tx_value_type'],'tx_type'=>$params['tx_type'],'tx_channel'=>$params['tx_channel'],'ref_amount'=>$params['amount']];
    $charge_detail=self::charges_get($params1);
    $tx_fee=$charge_detail->charge; //  //$params=['currency'=>,'tx_value_type'=>,'tx_type'=>,'tx_channel','ref_amount'=>])
//Get user's account detail
    $ref_col=($deposit)?"dp_max_day":"wd_max_day";
    $acc_user=($deposit)?$params['dest_account']:$params['source_account'];   // use destination account if depositing and source account if withdrawing or transfering
    $pr1['condition'][]=[' and ','ac.currency','=',$params['currency']];
    $pr1['condition'][]=[' and ','ac.acc_number','=',$acc_user];
    $user_acc=(qrymkrController::class)::tx_accounts_get($pr1);
    $user_acc=$user_acc[0];
//Verify that amount does not exceed maximum limits
/*
    if( $charge_detail->$ref_col < $params['amount'])
     {
        $proceed['status']="failed";
        $proceed['message'].=" : Maximum limit exceeded !";
     }
*/
//Verify there is enough money if it is a withdraw
    if(!$deposit && (($params['amount']+$tx_fee) > $user_acc->amount ) )
     {
        $proceed['status']="failed";
        $proceed['message'].=" : Not enough money ! (Maximum ".($user_acc->amount-$tx_fee).")";
     }
/*
//Verify it is within working hours
    $start_time="";
    $end_time="";
    if($start_time!=null&&$end_time!=null)
    if((MethodsController::class)::date_diff_open($start_time,date('H:s:i'),"minutes") < 0 || (MethodsController::class)::date_diff_open($end_time,date('H:s:i'),"minutes") > 0)
    if(!$deposit && (($user_acc->amount+$tx_fee)<$params['amount']) )
     {
        $proceed['status']="failed";
        $proceed['message'].=" : Not operational hours !";
     }
*/
//Check whether there is similar pending transactions
/*
    $pending_tx=DB::table('tx_api_refs')->where('user_id','=',$user_acc->user_id)->where('tx_status','=','pending')->count();
    if($pending_tx!=null && $pending_tx > 0 )
      {
        $proceed['status']="failed";
        $proceed['message'].=" : There is a pending transaction ! Wait for atleast 2 minutes";
      }
*/
    return (object)$proceed;
}



    
}
