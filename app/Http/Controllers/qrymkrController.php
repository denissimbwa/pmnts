<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class qrymkrController extends Controller
{
    //
static $except_keys=['enter','_tocken'];

//account_admin
public static function account_admin($params=null){
    $currency=$params['currency'];
        $dta['approval_count']=DB::table('approvals')->where('approve_scope','!=','group')->where('approve_scope','!=','user')->where('entity_type','=','request')->where('approve_status','!=','rejected')->where('approve_status','!=','approved')->where('approve_status','!=','approve_status')->count();
        $dta['investment_requests']=DB::table('investment_bookings')->where('status','=','selected')->count();
        $dta['users_count']=DB::table('users')->where('user_rank','=','client')->count();
        $dta['providers_count']=DB::table('providers')->count();
        $dta['groups_count']=DB::table('ref_centers')->count();
//        $dta['profit_today']=(DB::table('tx_accounts')->where('acc_category','=','admin')->where('tx_channel','=','profit')->where('currency','=',$currency)->first())->amount;
        $dta['credit_today']=DB::table('ref_center_requests')->whereDate('created_at', '=', date('Y-m-d'))->where('request_type','=','credit')->sum('taken_amount');
//        $dta['cash_today']=DB::table('tx_account_logs')->where('tx_value_type','=','cash')->where('tx_channel','=','transit')->where('tx_type_transit','=','deposit')->sum('cash_amount');
        $dta['bank_approvals']=DB::table('tx_account_logs')->where('tx_value_type','=','wallet')->where('tx_channel','=','transit')->sum('cash_amount');
        $dta['invested_capital']=DB::table('investment_bookings')->where('status_edit','=','selected')->sum('amount');
        $dta['user_balance']=DB::table('tx_accounts')->where('acc_category','=','user')->where('tx_value_type','=','wallet')->sum('amount');
//        $dta['group_cash']=DB::table('tx_accounts')->where('acc_category','=','group')->where('tx_value_type','=','wallet')->sum('amount');
        $dta['users_today']=DB::table('users')->where('user_rank','=','client')->whereDate('created_at', '=', date('Y-m-d'))->count();
        $dta['groups_today']=DB::table('ref_centers')->whereDate('created_at', '=', date('Y-m-d'))->count();
        $dta['cash_unbanked']=DB::table('tx_api_refs')->where('recovery_flag','=','yes')->value(DB::raw("SUM(api_amount-recovered_amount)"));

        $dta['amount_cash']=DB::table('tx_api_refs')->where('active_status','=','complete')->sum('amount_cash');// value(DB::raw("SUM(api_amount-recovered_amount)"));
        $dta['tx_count']=DB::table('tx_api_refs')->where('active_status','=','complete')->count();// value(DB::raw("SUM(api_amount-recovered_amount)"));
        $dta['tx_today']=DB::table('tx_api_refs')->where('active_status','=','complete')->where('tx_type','=','deposit')->where('created_at', '=', date('Y-m-d'))->count();// value(DB::raw("SUM(api_amount-recovered_amount)"));
        $dta['tx_today_incomplete']=DB::table('tx_api_refs')->where('active_status','!=','complete')->where('created_at', '=', date('Y-m-d'))->count();// value(DB::raw("SUM(api_amount-recovered_amount)"));
        $dta['cash_today']=DB::table('tx_api_refs')->where('active_status','=','complete')->where('created_at', '=', date('Y-m-d'))->sum('amount_cash');
        $dta['profit_today']=DB::table('tx_api_refs')->where('active_status','=','complete')->where('created_at', '=', date('Y-m-d'))->sum('tx_fee');

  return (object)$dta;
}


public static function get_office_account($tx_channel,$currency){
    $acc_detail_office=DB::table('tx_accounts')->where('acc_category','=','admin')
                                            ->where('currency','=',$currency)
                                            ->where('tx_channel','=',$tx_channel)
                                            ->where('active_status','=','active')
                                            ->get();
   return $acc_detail_office;
}
    
public static function transit_account_get($currency){
    $channel_detail=DB::table('tx_accounts')->where('acc_category','=','admin')
                                            ->where('currency','=',$currency)
                                            //->where('tx_value_type','=',$tx_detail->tx_value_type)
                                            ->where('tx_channel','=','transit')
                                            ->where('active_status','=','active')
                                            ->get();
    $channel_detail=$channel_detail[0];
    return $channel_detail;
}



public static function clients_get($params=null){
        $query['table']="client cl
                         ";   //user side of invited memeber

        $query['column_values']=[
                                'cl.id'=>'',
                                'cl.client_id'=>'',
                                'cl.created_at'=>'',
                                'cl.name'=>'',
                                'cl.mobile'=>'',
                                'cl.address'=>'',
                                'cl.website'=>'',
                                'cl.call_back_url'=>'',
                                'cl.access_code'=>'',
                                'cl.active_status'=>'',
                                'cl.tx_fee_dp'=>'',
                                'cl.tx_fee_wd'=>'',
                                'cl.amount'=>'',
                                "(select case when sum(amount_cash) is null then 0 else sum(amount_cash) end from tx_api_refs ap where ap.client_id=cl.id and ap.tx_status='success' and ap.active_status='complete' ) as amount_total"=>'',
                                "(select count(*) from tx_api_refs ap where ap.client_id=cl.id and ap.tx_status='failed' and ap.active_status='complete' ) as failed_tx_count"=>'',
                                "(select count(*) from tx_api_refs ap where ap.client_id=cl.id and ap.active_status='complete' ) as tx_count"=>'',
                                "(select count(*) from tx_api_refs ap where ap.client_id=cl.id and ap.active_status='pending' ) as tx_pending"=>'',
                                "(select case when sum(tx_cash_back) is null then 0 else sum(tx_cash_back) end from tx_api_refs ap where ap.client_id=cl.id and ap.active_status='complete' ) as tx_cash_back"=>'',
                               ];

        $query['condition'][]=[' ','1','=','1'];

        if($params!=null&&isset($params['condition'])&&$params['condition']!=null)
         for($k=0;$k<count($params['condition']);$k++)
            $query['condition'][]=$params['condition'][$k];

        if($params!=null&&isset($params['extras'])&&$params['extras']!=null)
         for($k=0;$k<count($params['extras']);$k++)
            $query['extras'][]=$params['extras'][$k];

        $dta['body']=(qryrnrController::class)::select1($query);

        $success=($dta['body']!=null && !empty($dta['body']));
        $dta['code']=($success)?"200":500;
        $dta['message']=($success)?"finished":"failed to retrieve data";
        $dta['status']=($success)?"success":"failed";
        return $dta;
    }



// Create requests scheduled by date [request_service_id, member_id, name]
public static function request_schedule_maker($params,$paid){
                    $request_detail=DB::table('ref_center_request_types')->find($params['request_service_id']);
                    $rq_data=null;
                    $rq_data['ref_center_id']=$request_detail->ref_center_id;
                    $rq_data['request_type_id']=$request_detail->tx_service_cat_ID;
                    $rq_data['request_type']=$request_detail->tx_service_cat;
                    $rq_data['user_id']=$params['user_id'] ?? "";
                    $rq_data['admin_id']=$params['admin_id'] ?? "";
                    $rq_data['member_id']=$params['member_id'];
                    $rq_data['tx_id']=$params['tx_id'] ?? "";
                    $rq_data['currency']=$params['currency'] ?? "";
                    $rq_data['flow_type']=($request_detail->tx_service_cat==="collection")?"inflow":"outflow";
                    $rq_data['taken_amount']=$params['taken_amount'] ?? "0";
                    $rq_data['paid_amount']="0";
                    $rq_data['payback_amount']=$request_detail->amount;
                    $rq_data['balance']=$request_detail->amount;
                    $rq_data['tx_value_type']=$params['tx_value_type'] ?? "";
                    $rq_data['detail1']=$params['member_id'];
                    $rq_data['detail2']=$params['name'];
                    $rq_data['request_service_id']=$params['request_service_id'];
                    $rq_data['schedule_date']='1111-11-11';

                    $query['column_values']=$rq_data;
                    $query['table']="ref_center_requests";
                    $query['condition']=null;
                    $query['extras']=null;
        //Get the last date on member's shcedule if it  is null we get next shcedule from current date
        $max_date=DB::table('ref_center_requests')->where('request_service_id','=',$params['request_service_id'])
                                                  ->where('member_id','=',$params['member_id'])
                                                  ->max('schedule_date');
        $max_date=($max_date==null || !isset($max_date)) ? $request_detail->start_date : $max_date;
        
        $date_is_ahead=(date_diff($max_date,date('Y-m-d H:i:s'))->format("%R%a") < 0);
        $schedule_date=(MethodsController::class)::pick_next_schedule($max_date,$request_detail->interval,date('Y-m-d H:i:s'));
        //Compute the immediate date to start with
        //
        $service_amount=($request_detail->amount==0)? 1: $request_detail->amount;
        $shedule_count=$paid/$service_amount;
        for($j=0; $j <= $shedule_count; $j++)
           {
            $rq_data['schedule_date']=$schedule_date;
            $unpaid_collections[]=(qryrnrController::class)::insert1($query);
            date_add($schedule_date,date_interval_create_from_date_string(($request_detail->interval * $j)." days"));
           }
    return $unpaid_collections;
}


//Initiate deposite request
public static function request_deposit_initiation($rq){
    $proceed=true;
    $dta['code']="500";
    $dta['message']="failed to initiate transaction at stage one !";
    $dta['status']="failed";

        if( $rq->input('tx_id')!=null )
          {
            $found_tx_in_log=DB::table('ref_center_requests')->where('tx_id','=',$rq->input('tx_id') )->count();
            $proceed=($found_tx_in_log==0)?$proceed:false;
            
            $params=null;
            $params['condition'][]=[' and ','pr.id','=',$rq->input('provider_id') ];
            $provider=self::providers_get($params);
            $provider=$provider['body'][0];

            $tx_value_type=$provider->tx_value_type;//"wallet";
            
            $member=DB::table('ref_center_members')->where('owner_id','=',$rq->input('user_id') )->get();
            $member=$member[0] ?? null;
            
            $center=DB::table('tx_accounts')->where('ref_center_id','=',$member->ref_center_id ?? null)->where('acc_category','=','group')->where('tx_value_type','=','wallet')->get();
            $center=$center[0] ?? null;
            $agent=DB::table('tx_accounts')->where('user_id','=',$member->user_id_ref)->where('acc_category','=','user')->where('tx_value_type','=','wallet')->get();
            $agent=$agent[0] ?? null;
            $office=DB::table('tx_accounts')->where('active_status','=','active')->where('tx_channel','=','profit')->where('acc_category','=','admin')->where('tx_value_type','=','wallet')->where('currency','=',$center->currency)->get();
            $office=$office[0];
            
            $interest=$provider->interest_total ?? 1000;
            $tx_data['ref_center_id']=$member->ref_center_id;
            $tx_data['provider_id']=$provider->id;
            $tx_data['currency']=$center->currency;
//            $tx_data['admin_id']=$rq->input('admin_id');
            $tx_data['flow_type']='outflow';
            $tx_data['offered_amount']=0;//$rq->input('offered_amount');
            $tx_data['taken_amount']=0;//$rq->input('offered_amount');
            $tx_data['payback_amount']=0;//($rq->input('offered_amount') + $rq->input('offered_amount')*($interest/100) );
            $tx_data['paid_amount']='0';
            $tx_data['balance']=0;//$tx_data['payback_amount'];
            $tx_data['limit']=$provider->business_limit ?? "200000";
            $tx_data['interest']=$interest;
            $tx_data['duration_days']=0;//($collection)?"": $provider->duration_days ?? "";
            $tx_data['grace_period']=0;//($collection)?"": $provider->grace_period ?? "";
            $tx_data['acc_number_funding']=$provider->acc_number_investor ;
            $tx_data['acc_number_provider']=$provider->acc_number_provider ;
            $tx_data['acc_number_agent']= $agent->acc_number ?? $office->acc_number;
            $tx_data['acc_number_center']=$center->acc_number ?? "";
            $tx_data['acc_number_office']= $office->acc_number;
            $tx_data['tx_value_type']=$tx_value_type;
            $tx_data['active_status']='new';
            $tx_data['country_code']=$rq->input('country_code') ?? "256";
            $tx_data['request_type']=$rq->input('request_type');
            $tx_data['requested_amount']=$rq->input('requested_amount');
            $tx_data['tx_id']=$rq->input('tx_id');
            $tx_data['user_id']=$rq->input('user_id');
            $tx_data['request_type_id']=$provider->service_id;
            
            //$provider->request_type       service_cat

//Check if tx_id does not exist in provider's log
            $found_tx_in_log=DB::table('ref_center_requests')->where('tx_id','=',$rq->input('tx_id') )->count();
            $proceed=($found_tx_in_log==0)?$proceed:false;

//return $query;
            $query['table']="ref_center_requests";
            $query['column_values']=$tx_data;
            $query['column_values']=array_filter($query['column_values']);
            $query['condition']=null;
            $query['extras']=null;
            $dta['body']=null;
            
            if($proceed)
              {
                $dta['body']=(qryrnrController::class)::insert1($query);
                $dta['body']=(object)json_decode($dta['body'],true);
              }
            $success=($dta['body']!=null&&!empty($dta['body']));
            $dta['code']=($success)?"200":"500";
            $dta['message']=($success)?"finished":"failed to initiate transaction at stage TWO!";
            $dta['status']=($success)?"success":"failed";
            
            
            
            Log::channel('slackNotification')->info('Deposit stage 1', ['data'=>$query,'RAW'=>$tx_data,'results'=>$dta ]);
//Initiate API rerequest
            if($success && $proceed){
                    DB::table('ref_center_requests')->where('tx_id','=',$tx_data['tx_id'])->update([ 'active_status'=>'initiating' ]);

            $office=DB::table('tx_accounts')->where('active_status','=','active')->where('tx_channel','=','transit')->where('acc_category','=','admin')->where('currency','=',$center->currency)->get();
            $office=$office[0];


         $release_account=DB::table('tx_accounts')->where('acc_category','=','admin')->where('tx_value_type','=','wallet')->where('tx_channel','=','release')->where('active_status','=','active')->get();
         $release_account=$release_account[0];


        $tx_value_type='wallet';
        $tx_data=null;
        $tx_data['tx_id']=$rq->input('tx_id');
        $tx_data['tx_owner_type']='user';
        $tx_data['tx_owner_id']=$rq->input('user_id');
        $tx_data['tx_value_type']=$tx_value_type;  // cash - wallet - provider - api 
        $tx_data['tx_service_cat']=$rq->input('request_type')??"";
        $tx_data['tx_service_cat_ID']=$center->service_id ??"";
        $tx_data['request_service_id']=$center->service_id ??null;
        $tx_data['ref_id']=$rq->input('ref_id');
        $tx_data['amount_source']=$rq->input('requested_amount');
        $tx_data['ref_amount']=($rq->input('requested_amount')*1+$interest*1);
        $tx_data['slip_no']=$rq->input('slip_no');
        $tx_data['tx_type']=$rq->input('tx_type');
        $tx_data['user_id']=$rq->input('user_id');
        $tx_data['tx_channel']=$rq->input('tx_channel')??"cash";
        $tx_data['param1']=$rq->input('param1') ?? "";
        $tx_data['param2']=$rq->input('param2') ?? "";

        $tx_data['source_account']=$rq->input('ref_id')??"";
        $tx_data['dest_account']=$provider->acc_number_provider ?? "";        
        $tx_data['acc_number_office']=$office->acc_number ?? "";
        $tx_data['currency_source']=$center->currency??"UGX";
        $tx_data['currency_destination']=$center->currency ?? $tx_data['currency_source'];

         
//        if($tx_data['tx_channel']==="transit" && $tx_data['tx_type']==="transfer")      // a tranfer that involves transit is treated as a withdraw in transit but with a final deposit account
//            $tx_data['tx_type']="withdraw";
/*        
        $tx_data['bank_acc_name']=$rq->input('bank_acc_name')??"";
        $tx_data['bank_name']=$rq->input('bank_name')??"";
        $tx_data['bank_branch']=$rq->input('bank_branch')??"";
        $tx_data['bank_acc_number']=$rq->input('bank_acc_number')??"";
*/

//Log::channel('slackNotification')->info('AGENT CASH DEP REQ', $tx_data);


            //check values that allow transaction to continue [currency - tx_value_type - dest_account - source_account - currency - amount - user_id - tx_value_type - tx_channel]
                    $params=null;
                    $params['currency_destination']=$tx_data['currency_destination'];
                    $params['tx_value_type']=$tx_data['tx_value_type'];
                    $params['dest_account']=$tx_data['dest_account'];
                    $params['source_account']=$tx_data['source_account'];
                    $params['amount']=$tx_data['ref_amount'];
                    $params['user_id']=$tx_data['user_id'];
                    $params['tx_channel']=$rq->input('tx_channel');
                    $params['tx_type']=$rq->input('tx_type');
                    $params['ref_id']=$rq->input('ref_id');
/*HALT
                    $checks=(TxapiController::class)::tx_validator($params);
            // Test score check point : transaction validation point
             $checks->status;
                    if($checks->status === "failed")
                     {
                      $dta['code']="500";
                      $dta['message']=$checks->message;
                      $dta['status']=$checks->status;
                      return $dta;
                     }
*/
            //        return $tx_data;
                    //Load request into table
                    $query=null;
                    $query['table']="tx_api_refs";
                    $query['column_values']=$tx_data;
                    $query['column_values']=array_filter($query['column_values']);
                    $query['condition']=null;
                    $query['extras']=null;
                    $dta['body']=(qryrnrController::class)::insert1($query);
                    $dta['body']=json_decode($dta['body'],true);
                    $success=false;
                    
                    Log::channel('slackNotification')->info('Deposit stage 2', ['data'=>$query,'results'=>$dta ]);
                    //Inititate transaction once registration is done
                    if($dta['body']!=null && !empty($dta['body']))
                              {
                                $success=true;
                                DB::table('ref_center_requests')->where('tx_id','=',$tx_data['tx_id'])->update([ 'active_status'=>'initiated' ]);
                                $dta['flutter']=(TxapiController::class)::tx_api_request($dta['body']['id']);
                              }
                    $dta['code']=($success)?"200":"500";
                    $dta['message']=($success)?"finished":"failed to initiate transaction !";
                    $dta['status']=($success)?"success":"failed";
            }
         }
        return $dta;
}

//Recieve all transaction initiations deposit and withdraw
public static function request_initiation($rq){
    $proceed=true;
        if($rq->input('request_code')!=null && $rq->input('id')!=null)
          {
            $request=DB::table('ref_center_requests')->find($rq->input('id'));
            $proceed=($request->request_code===$rq->input('request_code'));     //compare request codes
            $proceed=($request->active_status!=='processed')? true: $proceed;                   //ensure that the transaction was not already processed
            
            $collection=($request->provider_id == null || $request->provider_id==="");
            $provider=null;
            if(!$collection)
              {   
                $params=null;
                $params['condition'][]=[' and ','pr.id','=',$request->provider_id];
                $provider=self::providers_get($params);
                $provider=$provider['body'][0];
              }
            $tx_value_type=($request->provider_id != null)?"provider":"";
            $center=DB::table('tx_accounts')->where('ref_center_id','=',$request->ref_center_id ?? null)->where('acc_category','=','group')->where('tx_value_type','=','wallet')->get();
            $center=$center[0] ?? null;
            $agent=DB::table('tx_accounts')->where('user_id','=',$request->user_id)->where('acc_category','=','user')->where('tx_value_type','=','wallet')->get();
            $agent=$agent[0] ?? null;
            $office=DB::table('tx_accounts')->where('active_status','=','active')->where('tx_channel','=','profit')->where('acc_category','=','admin')->where('tx_value_type','=','wallet')->get();
            $office=$office[0];
            $interest=($collection)?0:$provider->interest_total ?? 0;
            $tx_data['currency']=$center->currency;
            $tx_data['admin_id']=$rq->input('admin_id');
            $tx_data['flow_type']='outflow';
            $tx_data['offered_amount']=$rq->input('offered_amount');
            $tx_data['taken_amount']=$rq->input('offered_amount');
            $tx_data['payback_amount']=($rq->input('offered_amount') + $rq->input('offered_amount')*($interest/100) );
            $tx_data['paid_amount']='0';
            $tx_data['balance']=$tx_data['payback_amount'];
            $tx_data['limit']=$provider->business_limit ?? "";
            $tx_data['interest']=$interest;
            $tx_data['duration_days']=($collection)?"": $provider->duration_days ?? "";
            $tx_data['grace_period']=($collection)?"": $provider->grace_period ?? "";
            $tx_data['acc_number_funding']=($collection)?"": $provider->acc_number_investor ?? "";
            $tx_data['acc_number_provider']=($collection)?"": $provider->acc_number_provider ?? "";
            $tx_data['acc_number_agent']=($collection)?"": $agent->acc_number ?? "";
            $tx_data['acc_number_center']=$center->acc_number ?? "";
            $tx_data['acc_number_office']=($collection)?"": $office->acc_number;
            $tx_data['tx_value_type']=$tx_value_type;
            $tx_data['active_status']='processed';
            $tx_data['country_code']=$rq->input('country_code') ?? "";
            

            //Update detgail into table
            $query['table']="ref_center_requests";
            $query['column_values']=$tx_data;
            $query['column_values']=array_filter($query['column_values']);
            $query['condition'][]=[' ','id','=',$rq->input('id')];
            $query['extras']=null;
//return $query;
            $dta['body']=null;
            
//Check if tx_id does not exist in account log on provider log
            $found_tx_in_log=DB::table('tx_account_logs')->where('tx_id','=',$request->tx_id)->count();
            
            $proceed=($found_tx_in_log==0)?$proceed:false;
            
            if($proceed)
              {
               $dta['body']=(qryrnrController::class)::update1($query);
//                $dta['body']=(object)json_decode($dta['body'][0],true);
              }
            else
              {
                $dta['code']="500";
                $dta['message']="failed to initiate transaction !";
                $dta['status']="failed";
              }
// load a -ve negative transaction into provider's account log
            if($proceed)
            if( ($request->provider_id != null) && $tx_data['flow_type']==="outflow" && $request->request_type==="credit" )
                {
                    //insert a log outflow in the ref_center_requests_logs table show taken amount
                    //$dta['body']=json_decode($dta['body'],true);
                    $request_detail= json_decode($dta['body'],true);
                    $request_detail= (object)$request_detail[0];

                    $tx_data=null;
                    $tx_data['ref_center_request_id']=$request_detail->id;
                    $tx_data['amount']=($request_detail->payback_amount * -1);
                    $tx_data['flow_type']='outflow';
                    $tx_data['ref_center_id']=$request_detail->ref_center_id;
                    $tx_data['request_type_id']=$request_detail->request_type_id;
                    $tx_data['slip_no']=$request_detail->request_code;
                    $tx_data['admin_id']=$request_detail->admin_id;
                    $tx_data['member_id']=$request_detail->member_id;
                    $tx_data['request_code']=$request_detail->request_code;
                    $tx_data['currency']=$request_detail->currency;
                    $tx_data['tx_value_type']='provider';
                    $tx_data['tx_id_ref']=$request_detail->tx_id;
                    $tx_data['tx_id_ref_source_table']='tx_account_logs';
                    
                    //Load request into table
                    $query['table']="ref_center_requests_logs";
                    $query['column_values']=$tx_data;
                    $query['condition']=null;
                    $query['extras']=null;
                    $dta['log']=(qryrnrController::class)::insert1($query);

                     //Make withdraw on provider's account
                                        $acc_body=['tx_id'=>$request->tx_id,
                                                   'acc_number'=>$request_detail->acc_number_provider,
                                                   'source_account'=>$request_detail->acc_number_provider,
                                                   'dest_account'=>"",
                                                   'tx_type'=>'withdraw',
                                                   'currency'=>$request->currency,
                                                   'currency_source'=>$request->currency,
                                                   'currency_destination'=>$request->currency,
                                                   'cash_amount'=>($request_detail->taken_amount * -1),
                                                   'tx_id_parent'=>$request->tx_id,
                                                   'tx_value_type'=>'provider',  //tx_value_type_source,
                                                   'tx_flag'=>'taken',     // normal   - transit   - profit - taken/paid
                                                   'transit_status'=>'none',    // pending  -   none    -       complete
                                                   'tx_channel'=>'provider'
                                                  ];     //similar to that in api_ref
                          $dta['withdraw']=(TxapiController::class)::tx_acc_loader($acc_body,null);
                          (MethodsController::class)::account_sync([ 'acc_number'=>$request_detail->acc_number_provider ]);
                }
            $success=($dta['body']!=null&&!empty($dta['body']));
    
            $dta['code']=($success)?"200":"500";
            $dta['message']=($success)?"finished":"failed to initiate transaction !".((!$proceed)?"Invalid Code or transaction No":"" );
            $dta['status']=($success)?"success":"failed";
 
          }
        else
         {
            $tx_data['request_code']=$rq->input('request_code');
            $tx_data['country_code']=$rq->input('country_code');
            $tx_data['user_id']=$rq->input('user_id');
            $tx_data['provider_id']=$rq->input('provider_id');
            $tx_data['ref_center_id']=$rq->input('ref_center_id');
            $tx_data['member_id']=$rq->input('member_id');
            $tx_data['tx_id']=$rq->input('tx_id');
            $tx_data['request_type_id']=$rq->input('request_type_id');
            $tx_data['request_type']=$rq->input('request_type');
            $tx_data['requested_amount']=$rq->input('requested_amount');
            $tx_data['detail1']=$rq->input('detail1');
            $tx_data['detail2']=$rq->input('detail2');

            //Load request into table
            $query['table']="ref_center_requests";
            $query['column_values']=$tx_data;
            $query['column_values']=array_filter($query['column_values']);
            $query['condition']=null;
            $query['extras']=null;
            $dta['body']=null;
            if($proceed)
              {
                $dta['body']=(qryrnrController::class)::insert1($query);
                $dta['body']=(object)json_decode($dta['body'],true);
              }

            $success=($dta['body']!=null&&!empty($dta['body']));
    
            $dta['code']=($success)?"200":"500";
            $dta['message']=($success)?"finished":"failed to initiate transaction !";
            $dta['status']=($success)?"success":"failed";
         }
        return $dta;
}

//ref_center_request_types
public static function ref_center_request_types_get($params=null){
        $query['table']="ref_center_request_types cr 
                         left join ref_centers ct on cr.ref_center_id=ct.id
                         left join ref_center_requests rq on cr.id=rq.request_service_id      
                         ";   //user side of invited memeber

        $query['column_values']=[
                                'cr.id'=>'',
                                'cr.created_at'=>'',
                                'cr.name'=>'',
                                'cr.amount'=>'',
                                'cr.description'=>'',
                                'cr.interval'=>'',
                                'cr.active_status'=>'',
                                'cr.image1'=>'',
                                'cr.image2'=>'',
                                'cr.image3'=>'',
                                'cr.start_date'=>'',
                                'cr.end_date'=>'',
                                'cr.tx_service_cat_ID'=>'',
                                'cr.tx_service_cat'=>'',
                                'cr.ref_center_id'=>'',
                                'ct.currency'=>'',
                                'ct.country_code'=>'',
                                'ct.provider_acc_number'=>'',
                                'ct.user_id as owner_id'=>'',
                                'ct.address_gps'=>'',
                                'ct.name as name_center'=>'',
                                'ct.center_type'=>'',
                                'rq.detail1'=>'',
                                'rq.detail2'=>'',
                                'rq.flow_type'=>'',
                                'rq.tx_id as tx_id_request'=>'',
                                'sum(rq.requested_amount) as requested_amount'=>'',
                                'sum(rq.offered_amount) as offered_amount'=>'',
                                'sum(rq.taken_amount) as taken_amount'=>'',
                                'sum(rq.payback_amount) as payback_amount'=>'',
                                'sum(rq.paid_amount) as paid_amount'=>'',
                                'sum(rq.balance) as balance'=>'',
                                "(select acc_number from tx_accounts cac where cac.ref_center_id=rq.ref_center_id and cac.tx_value_type='cash' limit 1) as acc_number_cash"=>'',
                                "(select acc_number from tx_accounts cac where cac.ref_center_id=rq.ref_center_id and cac.tx_value_type='wallet' limit 1) as acc_number_wallet"=>'',
                               ];

        $query['condition'][]=[' ','1','=','1'];

        if($params!=null&&isset($params['condition'])&&$params['condition']!=null)
         for($k=0;$k<count($params['condition']);$k++)
            $query['condition'][]=$params['condition'][$k];

        $query['extras'][]=' group by cr.id ';
        if($params!=null&&isset($params['extras'])&&$params['extras']!=null)
         for($k=0;$k<count($params['extras']);$k++)
            $query['extras'][]=$params['extras'][$k];

        $dta['body']=(qryrnrController::class)::select1($query);

        $success=($dta['body']!=null && !empty($dta['body']));
        $dta['code']=($success)?"200":500;
        $dta['message']=($success)?"finished":"failed to retrieve data";
        $dta['status']=($success)?"success":"failed";
        return $dta;
    }

//Get all invitationss to access system departments inivtation_list
public static function user_access_get($params=null){
        $query['table']="user_access ua 
                         left join users u on ua.admin_id=u.id      
                         left join users um on ua.user_id=um.id";   //user side of invited memeber

        $query['column_values']=[
                                'ua.id'=>'',
                                'ua.created_at'=>'',
                                'ua.admin_id'=>'',
                                'ua.mobile'=>'',
                                'ua.country_code'=>'',
                                'ua.access_ref_name'=>'',
                                'ua.access_ref_id'=>'',
                                'ua.access_ref_id_type'=>'',
                                'ua.access_scope'=>'',
                                'ua.access_table'=>'',
                                'ua.role'=>'',
                                'ua.acceptance'=>'',
                                'ua.active_status'=>'',
                                'um.name as name_member'=>'',
                                'um.mobile as mobile_member'=>'',
                                'u.name as name_admin'=>'',
                                'u.mobile as mobile_admin'=>'',
                               ];

        $query['condition'][]=[' ','1','=','1'];

        if($params!=null&&isset($params['condition'])&&$params['condition']!=null)
         for($k=0;$k<count($params['condition']);$k++)
            $query['condition'][]=$params['condition'][$k];

        if($params!=null&&isset($params['extras'])&&$params['extras']!=null)
         for($k=0;$k<count($params['extras']);$k++)
            $query['extras'][]=$params['extras'][$k];

        $dta['body']=(qryrnrController::class)::select1($query);

        $success=($dta['body']!=null && !empty($dta['body']));
        $dta['code']=($success)?"200":500;
        $dta['message']=($success)?"finished":"failed to retrieve data";
        $dta['status']=($success)?"success":"failed";
        return $dta;
    }


//Get all requests types
public static function request_types_get($params=null){
        $query['table']="request_types rt";

        $query['column_values']=[
                                'rt.id'=>'',
                                'rt.name'=>'',
                                'rt.description'=>'',
                                'rt.limit as limit_request'=>'',
                                'rt.interest'=>'',
                                'rt.duration_days'=>'',
                                '(select count(*) from providers pr where pr.service_id=rt.id ) as provider_count'=>'',
                                '(select count(*) from ref_centers rc where rc.service_id=rt.id ) as center_count'=>'',
                                '(select count(*) from ref_center_requests rq where rq.request_type_id=rt.id ) as request_count'=>'',
                               ];

        $query['condition'][]=[' ','1','=','1'];

        if($params!=null&&isset($params['condition'])&&$params['condition']!=null)
         for($k=0;$k<count($params['condition']);$k++)
            $query['condition'][]=$params['condition'][$k];

        if($params!=null&&isset($params['extras'])&&$params['extras']!=null)
         for($k=0;$k<count($params['extras']);$k++)
            $query['extras'][]=$params['extras'][$k];

        $dta['body']=(qryrnrController::class)::select1($query);

        $success=($dta['body']!=null && !empty($dta['body']));
        $dta['code']=($success)?"200":500;
        $dta['message']=($success)?"finished":"failed to retrieve data";
        $dta['status']=($success)?"success":"failed";
        return $dta;
    }


//providers_get fetch all providers summary data
public static function providers_get($params=null){
        $query['table']="providers pr left join tx_accounts ac on pr.id=ac.provider_id  
                        left join users u on pr.user_id=u.id 
                        left join investment_bookings bk on bk.provider_id=pr.id and bk.acc_number=pr.acc_number_investor and bk.status='selected' 
                        left join tx_accounts ai on bk.acc_number=ai.acc_number
                        left join request_types rt on pr.service_id=rt.id";

        $query['column_values']=[
                                'pr.id'=>'',
                                'pr.name as name_provider'=>'',
                                'pr.mobile as mobile_provider'=>'',
                                'pr.address as address_provider'=>'',
                                'pr.acc_number_investor'=>'',
                                'ac.acc_number as acc_number_provider'=>'',
                                'pr.interest_business'=>'',
                                'pr.interest_agent'=>'',
                                'pr.interest_center_head'=>'',
                                'pr.active_status as active_status_provider'=>'',
                                'pr.active_status_invest'=>'',
                                'pr.open_time'=>'',
                                'pr.close_time'=>'',
                                'pr.banK_acc_number'=>'',
                                'pr.banK_acc_name'=>'',
                                'pr.bank_branch'=>'',
                                'pr.bank_name'=>'',
                                'pr.service_id'=>'',
                                'pr.active_status'=>'',
                                'pr.approve_narration'=>'',
                                'u.name as name_admin'=>'',
                                'u.mobile as mobile_admin'=>'',
                                'ac.acc_number'=>'',
                                'ac.tx_value_type'=>'',
                                'ac.tx_channel'=>'',
                                'ac.amount'=>'',
                                'ac.currency'=>'',
                                'ac.acc_category as acc_category'=>'',
                                'ai.acc_number as acc_number_inverstor'=>'',
                                'ai.tx_value_type as tx_value_type_inverstor'=>'',
                                'ai.amount as amount_inverstor'=>'',
                                'ai.currency as currency_inverstor'=>'',
                                'ai.acc_category as acc_category_inverstor'=>'',
                                'ai.ref_center_id as ref_center_id_inverstor'=>'',
                                'rt.name as business_line'=>'',
                                'rt.id as request_type_id'=>'',
                                'rt.request_type'=>'',
                                'rt.grace_period'=>'',
                                'rt.duration_days'=>'',
                                'rt.description as business_line_description'=>'',
                                'rt.limit as business_limit'=>'',
                                'rt.interest as interest_total'=>'',
                                '(rt.interest-pr.interest_business-pr.interest_agent-pr.interest_center_head) as interest_investor'=>'',
                                'bk.user_id as user_id_inverstor'=>'',
                                'bk.amount as amount_booking'=>'',
                                'bk.id as booking_id'=>'',
                                '(select count(*) from ref_centers rq where rq.provider_id=pr.id ) as centers'=>'',
                                '(select count(distinct(rq.owner_id)) from ref_center_members rq where rq.provider_id=pr.id ) as member_size'=>'',
                                '(select count(distinct(rq.member_id)) from ref_center_requests rq where rq.provider_id=pr.id ) as clientel_size'=>'',
                                '(select count(*) from ref_center_requests rq where rq.provider_id=pr.id ) as activities_provider'=>'',
                                '(select count(*) from ref_center_requests rq where rq.provider_id=pr.id  and DATE(rq.created_at)= DATE(curdate()) ) as activities_today'=>'',
                                '(select sum(rq.taken_amount) from ref_center_requests rq where rq.provider_id=pr.id ) as taken_amount'=>'',
                                '(select sum(rq.balance) from ref_center_requests rq where rq.provider_id=pr.id ) as unpaid_amount'=>'',
                                '(select sum(rq.taken_amount) from ref_center_requests rq where rq.provider_id=pr.id and DATE(rq.created_at)=DATE(curdate()) ) as taken_today'=>'',
                                "(select sum(rq.paid_amount*interest/100) from ref_center_requests rq where rq.provider_id=pr.id and rq.request_type='credit' ) as profit_total"=>'',
                                '(select sum(rq.paid_amount) from ref_center_requests rq where rq.provider_id=pr.id and DATE(rq.created_at) = DATE(curdate()) ) as paid_today'=>'',
                                "(select bk.acc_number from investment_bookings bk where bk.provider_id=pr.id and bk.status='selected' limit 1 ) as acc_number_investor_selected"=>'',
                                "(select sum(lg.cash_amount) from tx_account_logs lg where lg.acc_number_funding=ac.acc_number and lg.acc_number=(select tac.acc_number from tx_accounts tac where tac.acc_category='admin' and tac.tx_channel='credit' and tac.tx_value_type='wallet' and tac.currency=ac.currency ) ) as reinvestment_amount"=>'',
                                "(
                                  (select case when sum(lg.cash_amount) is null then 0 else sum(lg.cash_amount) end from tx_account_logs lg where lg.acc_number_funding=ac.acc_number and lg.tx_type='deposit' and lg.acc_number=(select tac.acc_number from tx_accounts tac where tac.acc_category='admin' and tac.active_status='active' and tac.tx_value_type=ac.tx_value_type and tac.tx_channel=ac.tx_channel and tac.currency=ac.currency  limit 1) )
                                  +
                                  (select case when sum(lg.cash_amount) is null then 0 else sum(lg.cash_amount) end  from tx_account_logs lg where lg.dest_account=ac.acc_number and lg.tx_type='withdraw' and lg.acc_number=(select tac.acc_number from tx_accounts tac where tac.acc_category='admin' and tac.active_status='active' and tac.tx_value_type=ac.tx_value_type and tac.tx_channel=ac.tx_channel and tac.currency=ac.currency  limit 1) )
                                 ) as reinvestment_amount_pending"=>'',
                                "(select count(*) from tx_account_logs lg where lg.acc_number_funding=ac.acc_number and lg.acc_number=(select tac.acc_number from tx_accounts tac where tac.acc_category='admin' and tac.tx_channel='credit' and tac.tx_value_type='wallet' and tac.tx_channel=ac.tx_channel and tac.active_status='active' and tac.currency=ac.currency limit 1 ) ) as reinvestment_count"=>'',
                                "(select sum(lg.cash_amount) from tx_account_logs lg where lg.acc_number=ac.acc_number and lg.source_account=pr.acc_number_investor and tx_type='deposit' ) as amount_inverstor_total"=>'',
//                                "(select sum(lg.cash_amount) from tx_account_logs lg where lg.dest_account=ac.acc_number and lg.tx_type='withdraw' and lg.acc_number=(select tac.acc_number from tx_accounts tac where tac.acc_category='admin' and tac.tx_channel='credit' and tac.tx_value_type='wallet' and tac.currency=ac.currency )) as capital_delivered"=>'',          and tac.tx_channel='credit' 
//                                "(select sum(lg.cash_amount) from tx_account_logs lg where lg.acc_number_funding=ac.acc_number and lg.tx_type='deposit' and lg.acc_number=(select tac.acc_number from tx_accounts tac where tac.acc_category='admin' and tac.tx_channel='credit' and tac.tx_value_type='wallet' and tac.currency=ac.currency )) as capital_received"=>''       and tac.tx_channel='credit'
                               ];

        $query['condition'][]=[' ','1','=','1'];

        if($params!=null&&isset($params['condition'])&&$params['condition']!=null)
         for($k=0;$k<count($params['condition']);$k++)
            $query['condition'][]=$params['condition'][$k];

        if($params!=null&&isset($params['extras'])&&$params['extras']!=null)
         for($k=0;$k<count($params['extras']);$k++)
            $query['extras'][]=$params['extras'][$k];

        $dta['body']=(qryrnrController::class)::select1($query);

        $success=($dta['body']!=null && !empty($dta['body']));
        $dta['code']=($success)?"200":500;
        $dta['message']=($success)?"finished":"failed to retrieve data";
        $dta['status']=($success)?"success":"failed";
        return $dta;
    }


//Fetch agent_cash log of cash received by agents
public static function tx_api_refs_agent_cash($params){
        $query['table']="tx_api_refs r 
                         left join users u on r.user_id=u.id ";
        $query['column_values']=['r.id'=>'',
                                  'r.user_id'=>'',
                                  'sum(r.ref_amount) as ref_amount'=>'',
                                  'sum(r.amount_source) as amount_source'=>'',
                                  'sum(r.tx_fee) as tx_fee'=>'',
                                  'sum(r.api_fee) as api_fee'=>'',
                                  'sum(r.api_amount) as amount_received'=>'',
                                  'r.recovery_flag'=>'',
                                  'sum(r.recovered_amount) as amount_banked'=>'',
                                  'sum(r.api_amount-r.recovered_amount) as amount_unbanked'=>'',
                                  'max(r.created_at) as last_date'=>'',
                                  'r.image1'=>'',
                                  'r.param1'=>'',
                                  'r.param2'=>'',
                                  'r.param3'=>'',
                                  'r.param4'=>'',
                                  'u.name as name_agent'=>'',
                                 ];
        $query['condition'][]=[' ','recovery_flag','=','yes'];

        if($params!=null&&isset($params['condition'])&&$params['condition']!=null)
         for($k=0;$k<count($params['condition']);$k++)
            $query['condition'][]=$params['condition'][$k];

        if($params!=null&&isset($params['extras'])&&$params['extras']!=null)
         for($k=0;$k<count($params['extras']);$k++)
            $query['extras'][]=$params['extras'][$k];

        $dta['body']=(qryrnrController::class)::select1($query);

        $success=($dta['body']!=null && !empty($dta['body']));
        $dta['code']=($success)?"200":500;
        $dta['message']=($success)?"finished":"failed to retrieve data";
        $dta['status']=($success)?"success":"failed";
        return $dta;
}

//Fetch tx_api_refs log of transaction initiations
public static function tx_api_refs_get($params){
        $query['table']="tx_api_refs r 
                         left join users u on r.user_id=u.id ";
        $query['column_values']=['r.id'=>'',
                                  'r.created_at'=>'',
                                  'r.updated_at'=>'',
                                  'r.tx_id'=>'',
                                  'r.pay_id'=>'',
                                  'r.user_id'=>'',
                                  'r.ref_id'=>'',
                                  'r.ref_amount'=>'',
                                  'r.amount_source'=>'',
                                  'r.source_account'=>'',
                                  'r.dest_account'=>'',
                                  'r.tx_status'=>'',
                                  'r.tx_fee'=>'',
                                  'r.tx_type'=>'',
                                  'r.api_id'=>'',
                                  'r.api_ref_body'=>'',
                                  'r.api_fee'=>'',
                                  'r.api_amount'=>'',
                                  'r.api_errormsg'=>'',
                                  'r.act_time'=>'',
                                  'r.processsing_time'=>'',
                                  'r.tx_channel'=>'',
                                  'r.sync_status'=>'',
                                  'r.currency_source'=>'',
                                  'r.currency_destination'=>'',
                                  'r.tx_rate'=>'',
                                  'r.tx_value_type'=>'',
                                  'r.tx_service_cat'=>'',
                                  'r.tx_service_cat_ID'=>'',
                                  'r.tx_owner_type'=>'',
                                  'r.tx_owner_id'=>'',
                                  'r.slip_no'=>'',
                                  'r.bank_acc_name'=>'',
                                  'r.bank_name'=>'',
                                  'r.bank_branch'=>'',
                                  'r.bank_acc_number'=>'',
                                  'r.approve_status'=>'',
                                  'r.approve_action'=>'',
                                  'r.approve_ref_id'=>'',
                                  'r.approve_narration'=>'',
                                  'r.approve_count'=>'',
                                  'r.recovery_flag'=>'',
                                  'r.recovered_amount'=>'',
                                  'r.investment_flag'=>'',
                                  'r.investment_id'=>'',
                                  'r.acc_number_office'=>'',
                                  'r.image1'=>'',
                                  'r.param1'=>'',
                                  'r.param2'=>'',
                                  'r.param3'=>'',
                                  'r.param4'=>'',
                                  'u.name as user_name'=>'',
                                 ];
        $query['condition'][]=[' ','1','=','1'];

        if($params!=null&&isset($params['condition'])&&$params['condition']!=null)
         for($k=0;$k<count($params['condition']);$k++)
            $query['condition'][]=$params['condition'][$k];

        if($params!=null&&isset($params['extras'])&&$params['extras']!=null)
         for($k=0;$k<count($params['extras']);$k++)
            $query['extras'][]=$params['extras'][$k];

        $dta['body']=(qryrnrController::class)::select1($query);

        $success=($dta['body']!=null && !empty($dta['body']));
        $dta['code']=($success)?"200":500;
        $dta['message']=($success)?"finished":"failed to retrieve data";
        $dta['status']=($success)?"success":"failed";
        return $dta;
}


//Update profit dependant and return list of all dependants
public static function profit_dependants_update($rq){   
    $success=false;
    // update the values int table
    $profit_share=$rq->rate_edit;
    $id=$rq->id;
    $rate_dist="";
        for($t=0; $t<count($profit_share); $t++)
          {
           DB::table('tx_account_profit_holders')->where('id','=',$id[$t])->update([ 'rate_edit'=>$profit_share[$t],'approve_ref_id'=>$rq->input('approve_ref_id'),'approve_narration'=>$rq->input('approve_narration'), 'approve_action'=>'approve' ]);
           $rate_dist.=$profit_share[$t]."% , ";
           $success=true;
          }
    //register for approval
                           $params=array('entity_type'=>'request',
                                    'approve_ref_id'=>$rq->input('approve_ref_id'),
                                    'approve_action'=>'approve',
                                    'approve_narration'=>$rq->input('approve_narration')." -(".count($profit_share)." Acc ) ".$rate_dist,
                                    'approve_count'=>$rq->input('approve_count')??"2",
                                    'approver_role'=>'admin',
                                    'user_id'=>$rq->input('user_id'),
                                    'source_table'=>'tx_account_profit_holders',
                                    'source_column'=>'approve_ref_id',
                                    'source_id'=>$rq->input('approve_ref_id'),
                                    'approve_scope'=>'user',        // user - admin
                                    'ref_center_id'=>$rq->input('ref_center_id') ?? '',
                                    );
//      return $rq->input('ref_center_id') ?? '';
                         (MethodsController::class)::approval_register($params);

        $dta['code']=($success)?"200":500;
        $dta['message']=($success)?"finished":"failed to update rates";
        $dta['status']=($success)?"success":"failed";
        return $dta;
}


//Register profit dependant and return list of all dependants 
public static function profit_dependants_create($params){   // $params=['acc_number_giver'=>,'acc_number_receiver'=>,'rate'=>,'active_status'=>]
        $params['table']="tx_account_profit_holders";
        $params['column_values']=['acc_number_giver'=>$params['acc_number_giver'],
                                  'acc_number_receiver'=>$params['acc_number_receiver'],
                                  'rate'=>$params['rate'],
                                  'active_status'=>$params['active_status']
                                 ];
        $params['condition']=null;
        $params['extras']=null;
        $dta['body']=(qryrnrController::class)::insert1($params);
        //$dta['body']=json_decode($dta['body'],true);
        //return list of all dependants to this account
        $dta['body']=(self::profit_dependants_get($params['acc_number_giver']))['body'];
        
        $success=($dta['body']!=null && !empty($dta['body']));
        $dta['code']=($success)?"200":500;
        $dta['message']=($success)?"finished":"failed to get records";
        $dta['status']=($success)?"success":"failed";
        return $dta;
}

//Fetch all profit dependant accouunts  tx_account_profit_holders_get
public static function profit_dependants_get($params){
        $query['table']="tx_account_profit_holders p 
                          left join tx_accounts rc on p.acc_number_receiver=rc.acc_number 
                          left join tx_accounts gv on p.acc_number_giver=gv.acc_number 
                          left join users ur on rc.user_id=ur.id 
                          left join users ug on gv.user_id=ug.id 
                          ";
        $query['column_values']=[ 'p.id'=>'',
                                  'p.created_at'=>'',
                                  'p.acc_number_giver'=>'',
                                  'p.acc_number_receiver'=>'',
                                  'gv.acc_name as acc_name_giver'=>'',
                                  'gv.amount as amount_giver'=>'',
                                  'gv.currency as currency_giver'=>'',
                                  'gv.ref_center_id as ref_center_id_giver'=>'',
                                  'gv.acc_category as acc_category_giver'=>'',
                                  'rc.acc_name as acc_name_receiver'=>'',
                                  'rc.amount as amount_receiver'=>'',
                                  'rc.currency as currency_receiver'=>'',
                                  'rc.ref_center_id as ref_center_id_receiver'=>'',
                                  'rc.acc_category as acc_category_receiver'=>'',
                                  'ug.name as name_giver'=>'',
                                  'ur.name as name_receiver'=>'',
                                  'p.rate'=>'',
                                  'p.rate_edit'=>'',
                                  'p.active_status'=>''
                                 ];

        $query['condition'][]=[' ','1','=','1'];

        if($params!=null&&isset($params['condition'])&&$params['condition']!=null)
         for($k=0;$k<count($params['condition']);$k++)
            $query['condition'][]=$params['condition'][$k];

        if($params!=null&&isset($params['extras'])&&$params['extras']!=null)
         for($k=0;$k<count($params['extras']);$k++)
            $query['extras'][]=$params['extras'][$k];

        $dta['body']=(qryrnrController::class)::select1($query);

        $success=($dta['body']!=null && !empty($dta['body']));
        $dta['code']=($success)?"200":500;
        $dta['message']=($success)?"finished":"failed to retrieve data";
        $dta['status']=($success)?"success":"failed";
        return $dta;
}

//Get list of activity from ref_centers
public static function ref_center_requests_logs_get($params=null){
        $query['table']="ref_center_requests_logs lg";

        $query['column_values']=[
                                'lg.id'=>'',
                                'lg.ref_center_id'=>'',
                                'lg.ref_center_request_id'=>'',
                                'lg.request_type_id'=>'',
                                'lg.slip_no'=>'',
                                'lg.admin_id'=>'',
                                'lg.member_id'=>'',
                                'lg.request_code'=>'',
                                'lg.amount'=>'',
                                'lg.flow_type'=>'',
                                'lg.currency'=>'',
                                'lg.tx_id_ref'=>'',
                                'lg.flow_type'=>'',
                                'lg.tx_id_ref_source_table'=>'',
                                'lg.tx_flag1'=>'',
                                'lg.tx_value_type'=>'',
                                'lg.sync_status'=>'',
                               ];

        $query['condition'][]=[' ','1','=','1'];

        if($params!=null&&isset($params['condition'])&&$params['condition']!=null)
         for($k=0;$k<count($params['condition']);$k++)
            $query['condition'][]=$params['condition'][$k];

        if($params!=null&&isset($params['extras'])&&$params['extras']!=null)
         for($k=0;$k<count($params['extras']);$k++)
            $query['extras'][]=$params['extras'][$k];

        $dta['body']=(qryrnrController::class)::select1($query);

        $success=($dta['body']!=null && !empty($dta['body']));
        $dta['code']=($success)?"200":500;
        $dta['message']=($success)?"finished":"failed to retrieve data";
        $dta['status']=($success)?"success":"failed";
        return $dta;
    }

public static function ref_center_requests_get($params=null){
        $query['table']="ref_center_requests rq 
                        left join ref_centers cn on rq.ref_center_id=cn.id 
                        left join request_types rt on rq.request_type_id=rt.id 
                        left join providers pr on rq.provider_id=pr.id 
                        left join users cl on rq.user_id=cl.id 
                        left join ref_center_members cm on rq.user_id=cm.owner_id";

        $query['column_values']=[
                                'rq.id'=>'',
                                'rq.ref_center_id'=>'',
                                'rq.provider_id'=>'',
                                'rq.member_id'=>'',
                                'rq.request_type_id'=>'',
                                'rq.request_type'=>'',
                                'rq.user_id'=>'',
                                'rq.admin_id'=>'',
                                'rq.request_code'=>'',
                                'rq.currency'=>'',
                                'rq.flow_type'=>'',
                                'rq.requested_amount'=>'',
                                'rq.offered_amount'=>'',
                                'rq.taken_amount'=>'',
                                'rq.payback_amount'=>'',
                                'rq.paid_amount'=>'',
                                'rq.balance'=>'',
                                'rq.limit'=>'',
                                'rq.interest'=>'',
                                'rq.request_service_id'=>'',
                                '(rq.payback_amount-rq.taken_amount) as profit'=>'',
                                '(rq.taken_amount-rq.paid_amount) as pincipal_unpaid'=>'',
                                'rq.grace_period'=>'',
                                'rq.acc_number_funding'=>'',    //investor account number
                                'rq.acc_number_provider'=>'',   
                                'rq.acc_number_agent'=>'',
                                'rq.acc_number_center'=>'',
                                'rq.acc_number_office'=>'',     //account number for office
                                'rq.tx_value_type'=>'',
                                'rq.request_levels'=>'',
                                'rq.created_at'=>'',
                                'rq.active_status'=>'',
                                'rq.detail1'=>'',
                                'rq.detail2'=>'',
                                'pr.name as name_provider'=>'',
                                'pr.mobile as mobile_provider'=>'',
                                'pr.address as address_provider'=>'',
                                'pr.acc_number_investor'=>'',
                                'pr.interest_business'=>'',
                                'pr.interest_agent'=>'',
                                'pr.interest_center_head'=>'',
                                'pr.active_status as active_status_provider'=>'',
                                'pr.open_time'=>'',
                                'pr.close_time'=>'',
                                'cm.name as name_member'=>'',
                                'cm.mobile as mobile_member'=>'',
                                'cm.acc_number as acc_number_member'=>'',
                                'cm.active_status as active_status_member'=>'',
                                'cn.country_code'=>'',
                                'cn.name as name_center'=>'',
                                'cn.address as adress_center'=>'',
                                'cn.mobile as mobile_center'=>'',
                                'cl.name as name_user'=>'',
                                'cl.mobile as mobile_user'=>''
                               ];

        $query['condition'][]=[' ','1','=','1'];

        if($params!=null&&isset($params['condition'])&&$params['condition']!=null)
         for($k=0;$k<count($params['condition']);$k++)
            $query['condition'][]=$params['condition'][$k];

        if($params!=null&&isset($params['extras'])&&$params['extras']!=null)
         for($k=0;$k<count($params['extras']);$k++)
            $query['extras'][]=$params['extras'][$k];

        $dta['body']=(qryrnrController::class)::select1($query);

        $success=($dta['body']!=null && !empty($dta['body']));
        $dta['code']=($success)?"200":500;
        $dta['message']=($success)?"finished":"failed to retrieve data";
        $dta['status']=($success)?"success":"failed";
        return $dta;
    }


//Get summary log for center's transactions
public static function ref_center_requests_summary_get($params=null){
        $query['table']="ref_center_requests rq 
                        left join providers pr on rq.provider_id=pr.id";

        $query['column_values']=[
                                'substr(rq.created_at,1,10) as created_at'=>'',
                                'rq.provider_id'=>'',
                                'count(rq.id) as tx_count'=>'',
                                'rq.flow_type'=>'',
                                'sum(rq.requested_amount) as requested_amount'=>'',
                                'sum(rq.offered_amount) as offered_amount'=>'',
                                'sum(rq.taken_amount) as taken_amount'=>'',
                                'sum(rq.payback_amount) as payback_amount'=>'',
                                'sum(rq.paid_amount) as paid_amount'=>'',
                                'sum(rq.balance) as balance'=>'',
                                'rq.limit'=>'',
                                'sum(rq.interest) as interest'=>'',
                                'pr.name as provider_name'=>''
                               ];

        $query['condition'][]=[' ','1','=','1'];

        if($params!=null&&isset($params['condition'])&&$params['condition']!=null)
         for($k=0;$k<count($params['condition']);$k++)
            $query['condition'][]=$params['condition'][$k];

        if($params!=null&&isset($params['extras'])&&$params['extras']!=null)
         for($k=0;$k<count($params['extras']);$k++)
            $query['extras'][]=$params['extras'][$k];

        $dta['body']=(qryrnrController::class)::select1($query);

        $success=($dta['body']!=null && !empty($dta['body']));
        $dta['code']=($success)?"200":500;
        $dta['message']=($success)?"finished":"failed to retrieve data";
        $dta['status']=($success)?"success":"failed";
        return $dta;
    }



public static function tx_account_logs_get($params=null){
        $query['table']="tx_account_logs al 
                         left join tx_accounts cc on al.acc_number=cc.acc_number
                         left join tx_api_refs rf on al.tx_id_parent=rf.tx_id
                         ";

        $query['column_values']=[
                                'al.id'=>'',
                                'al.tx_id'=>'',
                                'al.created_at'=>'',
                                'al.acc_number'=>'',
                                'al.user_id'=>'',
                                'al.ref_id'=>'',
                                'al.cash_amount as amount'=>'',
                                'al.tx_type'=>'',
                                'al.slip_no'=>'',
                                'al.tx_status'=>'',
                                'al.tx_channel'=>'',
                                'al.currency_source'=>'',
                                'al.currency_destination'=>'',
                                "(case when al.tx_type='deposit' then al.currency_destination else al.currency_source end ) as currency"=>'',
                                'al.tx_rate'=>'',
                                'al.tx_value_type'=>'',
                                'al.tx_service_cat'=>'',
                                'al.tx_service_cat_ID'=>'',
                                'al.tx_id_parent'=>'',
                                'al.tx_flag'=>'',
                                'al.transit_status'=>'',
                                'al.param1 as param1_acc'=>'',
                                'al.param2 as param2_acc'=>'',
                                'cc.ref_center_id'=>'',
                                'cc.provider_id'=>'',
                                'cc.user_uuid'=>'',
                                'cc.acc_name'=>'',
                                'cc.acc_description'=>'',
                                'cc.acc_number'=>'',
                                'cc.currency'=>'',
                                'cc.profit_dependants'=>'',
                                'cc.invite_code'=>'',
                                'rf.recovery_flag'=>'',
                                'rf.recovered_amount'=>'',
                                'rf.bank_acc_name'=>'',
                                'rf.bank_name'=>'',
                                'rf.bank_branch'=>'',
                                'rf.bank_acc_number'=>'',
                                'rf.source_account'=>'',
                                'rf.dest_account'=>'',
                                'rf.api_id'=>'',
                                'rf.api_fee'=>'',
                                'rf.api_amount'=>'',
                                'rf.currency_source'=>'',
                                'rf.currency_destination'=>'',
                                'rf.tx_rate'=>'',
                                'rf.investment_flag'=>'',
                                'rf.investment_id'=>'',
                                'rf.param1 as param1_fr'=>'',
                                'rf.param2 as param2_fr'=>'',
                                'rf.approve_status'=>'',
                                '(select gm.name from ref_center_members gm left join ref_centers rc on gm.ref_center_id=rc.id where (rc.currency=al.currency_destination or rc.currency=al.currency_source) and (gm.member_id=al.ref_id or gm.mobile=al.ref_id) )as name_member'=>''
                               ];

        $query['condition'][]=[' ','1','=','1'];

        if($params!=null&&isset($params['condition'])&&$params['condition']!=null)
         for($k=0;$k<count($params['condition']);$k++)
            $query['condition'][]=$params['condition'][$k];

        if($params!=null&&isset($params['extras'])&&$params['extras']!=null)
         for($k=0;$k<count($params['extras']);$k++)
            $query['extras'][]=$params['extras'][$k];

        $dta['body']=(qryrnrController::class)::select1($query);

        $success=($dta['body']!=null && !empty($dta['body']));
        $dta['code']=($success)?"200":500;
        $dta['message']=($success)?"finished":"failed to retrieve data";
        $dta['status']=($success)?"success":"failed";
        return $dta;
    }



//Recieve all transaction initiations deposit and withdraw
public static function tx_initiation_api_client_withdraw($rq,$client_detail,$amount=0){
    $proceed=true;
    $tx_fee_wave=false;
    // verify password against user ID left join results to prove account against its cuurency and value type
    //verify user ID against user access   to account or group  
    
    //
        $tx_data['tx_id']=date("Ymdhis").(MethodsController::class)::code_generator(6).$client_detail->id;
        $tx_data['ref_amount']=$amount*1+$client_detail->tx_cash_back*1;
        $tx_data['api_channel']=$rq->input('api_channel');
        $tx_data['tx_fee']=$rq->input('tx_fee');  // 
        $tx_data['request_body']=json_encode($rq->input) ?? "N/A";
        $tx_data['ref_id']=$client_detail->mobile;                    //mobile number
        $tx_data['tx_type']='withdraw';
        $tx_data['user_id']=$rq->input('user_id') ?? "";
        $tx_data['tx_channel']=$rq->input('tx_channel')??"mmoney";
        $tx_data['call_back_url']="";
        $tx_data['status']="new";
        $tx_data['client_id']=$rq->input('client_id');
        $tx_data['pay_id']="none";
        $tx_data['tx_cash_back']=$client_detail->tx_cash_back*-1;
        



        $tx_data['bank_acc_name']=$rq->input('bank_acc_name')??"";
        $tx_data['bank_name']=$rq->input('bank_name')??"";
        $tx_data['bank_branch']=$rq->input('bank_branch')??"";
        $tx_data['bank_acc_number']=$rq->input('bank_acc_number')??"";
        $tx_data['source_account']=$rq->input('source_account')??"";
        $tx_data['dest_account']=$rq->input('dest_account')??"";


        $tx_data['currency_source']=$rq->input('currency') ?? "UGX";
        $tx_data['currency_destination']=$rq->input('currency') ?? "UGX";
        
//Log::channel('slackNotification')->info('AGENT CASH DEP REQ', $tx_data);


        //break transaction if there was any fault with submited detail
        if(!$proceed)
          return $tx_data;
        
//return $tx_data;
/*
        $checks=(TxapiController::class)::tx_validator($params);
// Test score check point : transaction validation point
        $checks->status;
        if($checks->status === "failed")
         {
          $dta['code']="500";
          $dta['message']=$checks->message;
          $dta['status']=$checks->status;
          return $dta;
          return $checks;
         }
*/

//        return $tx_data;
        //Load request into table
        $query['table']="tx_api_refs";
        $query['column_values']=$tx_data;
        $query['column_values']=array_filter($query['column_values']);
        $query['condition']=null;
        $query['extras']=null;
        $dta['body']=(qryrnrController::class)::insert1($query);
        $dta['body']=json_decode($dta['body'],true);
        $success=false;
        //Inititate transaction once registration is done
        if($dta['body']!=null && !empty($dta['body']))
                  {
                    $success=true;
                    $dta['mmoney']=(TxapiController::class)::tx_api_request($dta['body']['id']);
                    $success=($dta['mmoney']['status']==="success");
                  }
        $dta['code']=($success)?"200":"500";
        $dta['message']=($success)?"finished":"failed to initiate transaction !";
        $dta['status']=($success)?"success":"failed";
        return $dta;
}


//Recieve all transaction initiations deposit and withdraw
public static function tx_initiation_api($rq){
    $proceed=true;
    $tx_fee_wave=false;
    // verify password against user ID left join results to prove account against its cuurency and value type
    //verify user ID against user access   to account or group  
    
    //
        $tx_data['tx_id']=date("Ymdhis").(MethodsController::class)::code_generator(6).$rq->input('client_id');
        $tx_data['ref_amount']=$rq->input('ref_amount');
        $tx_data['api_channel']=$rq->input('api_channel');
        $tx_data['tx_fee']=$rq->input('tx_fee');  // 
        $tx_data['request_body']=json_encode($rq->input) ?? "N/A";
        $tx_data['ref_id']=$rq->input('ref_id')??"";                    //mobile number
        $tx_data['tx_type']=$rq->input('tx_type');
        $tx_data['user_id']=$rq->input('user_id');
        $tx_data['tx_channel']=$rq->input('tx_channel')??"mmoney";
        $tx_data['call_back_url']=$rq->input('call_back_url')??"";
        $tx_data['status']="new";
        $tx_data['client_id']=$rq->input('client_id');
        $tx_data['pay_id']=$rq->input('tx_id');
        $tx_data['client_id']=$rq->input('client_id');
        $tx_data['tx_cash_back']=$rq->input('tx_cash_back') ?? "0";
        



        $tx_data['bank_acc_name']=$rq->input('bank_acc_name')??"";
        $tx_data['bank_name']=$rq->input('bank_name')??"";
        $tx_data['bank_branch']=$rq->input('bank_branch')??"";
        $tx_data['bank_acc_number']=$rq->input('bank_acc_number')??"";
        $tx_data['source_account']=$rq->input('source_account')??"";
        $tx_data['dest_account']=$rq->input('dest_account')??"";


        $tx_data['currency_source']=$rq->input('currency') ?? "UGX";
        $tx_data['currency_destination']=$rq->input('currency') ?? "UGX";
        
//Log::channel('slackNotification')->info('AGENT CASH DEP REQ', $tx_data);


        //break transaction if there was any fault with submited detail
        if(!$proceed)
          return $tx_data;
        
//return $tx_data;
/*
        $checks=(TxapiController::class)::tx_validator($params);
// Test score check point : transaction validation point
        $checks->status;
        if($checks->status === "failed")
         {
          $dta['code']="500";
          $dta['message']=$checks->message;
          $dta['status']=$checks->status;
          return $dta;
          return $checks;
         }
*/

//        return $tx_data;
        //Load request into table
        $query['table']="tx_api_refs";
        $query['column_values']=$tx_data;
        $query['column_values']=array_filter($query['column_values']);
        $query['condition']=null;
        $query['extras']=null;
        $dta['body']=(qryrnrController::class)::insert1($query);
        $dta['body']=json_decode($dta['body'],true);
        $success=false;
        //Inititate transaction once registration is done
        if($dta['body']!=null && !empty($dta['body']))
                  {
                    $success=true;
                    $dta['mmoney']=(TxapiController::class)::tx_api_request($dta['body']['id']);
                    $success=($dta['mmoney']['status']==="success");
                  }
        $dta['code']=($success)?"200":"500";
        $dta['message']=($success)?"finished":"failed to initiate transaction !";
        $dta['status']=($success)?"success":"failed";
        return $dta;
}



//Recieve all transaction initiations deposit and withdraw
public static function tx_initiation($rq){
    $proceed=true;
    $tx_fee_wave=false;
    // verify password against user ID left join results to prove account against its cuurency and value type
    //verify user ID against user access   to account or group  
    
    //
        $tx_data['tx_id']=$rq->input('tx_id');
        $tx_data['tx_owner_type']=$rq->input('tx_owner_type');
        $tx_data['tx_owner_id']=$rq->input('tx_owner_id');
        $tx_data['tx_value_type']=$rq->input('tx_value_type');  // cash - wallet - provider - api 
        $tx_data['tx_service_cat']=$rq->input('tx_service_cat')??"";
        $tx_data['tx_service_cat_ID']=$rq->input('tx_service_cat_ID')??"";
        $tx_data['request_service_id']=$rq->input('request_service_id')??null;
        $tx_data['ref_id']=$rq->input('ref_id');
        $tx_data['amount_source']=$rq->input('amount_source');
        $tx_data['slip_no']=$rq->input('slip_no');
        $tx_data['tx_type']=$rq->input('tx_type');
        $tx_data['user_id']=$rq->input('user_id');
        $tx_data['tx_channel']=$rq->input('tx_channel')??"cash";
        $tx_data['param1']=$rq->input('param1') ?? "";
        $tx_data['param2']=$rq->input('param2') ?? "";

         
//        if($tx_data['tx_channel']==="transit" && $tx_data['tx_type']==="transfer")      // a tranfer that involves transit is treated as a withdraw in transit but with a final deposit account
//            $tx_data['tx_type']="withdraw";
        
        $tx_data['bank_acc_name']=$rq->input('bank_acc_name')??"";
        $tx_data['bank_name']=$rq->input('bank_name')??"";
        $tx_data['bank_branch']=$rq->input('bank_branch')??"";
        $tx_data['bank_acc_number']=$rq->input('bank_acc_number')??"";
        $tx_data['source_account']=$rq->input('source_account')??"";
        $tx_data['dest_account']=$rq->input('dest_account')??"";



//overwight service category detail if there is service_id
        if($tx_data['request_service_id']!==null){
            $srvc=DB::table('ref_center_request_types')->find($tx_data['request_service_id']);
            $tx_data['tx_service_cat']=$srvc->tx_service_cat;
            $tx_data['tx_service_cat_ID']=$srvc->tx_service_cat_ID;
         }

        if($rq->has('investment_flag') && $rq->filled('investment_flag'))
         {
          $tx_data['investment_flag']=$rq->input('investment_flag');
          $tx_data['investment_id']=$rq->input('investment_id')??"";
         }
        
        $tx_data['currency_source']=$rq->input('currency_source')??"UGX";
        if($tx_data['source_account']!=="")
          {
           $params['condition'][]=[ ' and ','ac.acc_number','=',$tx_data['source_account'] ];
           $acc_detail=self::tx_accounts_get($params);
           $acc_detail=($acc_detail!=null)?$acc_detail[0]:null;
           $tx_data['currency_source']=$acc_detail->currency;
           $tx_data['tx_value_type']=$acc_detail->tx_value_type;
           if($acc_detail->tx_value_type==="provider")
             {
              $tx_data['tx_channel']="transit";
              $tx_data['acc_number_funding']=$acc_detail->acc_number;    // Append provider's account as funding account if the receiptient account is provider type
             }
           $proceed=($acc_detail->user_id===$tx_data['user_id']);   // confirm withdraw accoutn owner
           $tx_fee_wave=($acc_detail->acc_category==="admin");//Check if it is office account number in order to wave off charges
          }

        $tx_data['currency_destination']=$rq->input('currency_destination')??$tx_data['currency_source'];

//Log::channel('slackNotification')->info('AGENT CASH DEP REQ', $tx_data);
        
        if($tx_data['dest_account']!=="")
          {
           $params=null; 
           $params['condition'][]=[ ' and ','ac.acc_number','=',$tx_data['dest_account'] ];               
           $acc_detail=self::tx_accounts_get($params);
           $acc_detail=($acc_detail!=null)?$acc_detail[0]:null;
           $tx_data['currency_destination']=$acc_detail->currency;
           $tx_data['tx_value_type']=($tx_data['tx_service_cat']!=="" && $tx_data['tx_service_cat_ID']!=="") ? $tx_data['tx_value_type'] : $acc_detail->tx_value_type  ; // Do not load value type for cash depost by agent
          }
        
        $tx_data['recovery_flag']=$rq->filled('recovery_flag')?$rq->input('recovery_flag'):"no";
        $tx_data['recovery_flag']=($tx_data['tx_owner_type']==="office" && $tx_data['tx_value_type']==="cash" && $tx_data['tx_service_cat_ID']!=="")?"yes":$tx_data['recovery_flag'];
        
        //break transaction if there was any fault with submited detail
        if(!$proceed)
          return $tx_data;
        

        //perge up missing fields
        $tx_data=(MethodsController::class)::tx_request_builder($tx_data);
        $tx_data['tx_fee']=($tx_fee_wave) ? '0' : $tx_data['tx_fee'];
        $tx_data['api_fee']=($tx_fee_wave) ? '0' : $tx_data['tx_fee'];


//return $tx_data;

//Validate transaction
//check values that allow transaction to continue [currency - tx_value_type - dest_account - source_account - currency - amount - user_id - tx_value_type - tx_channel]
        $params=null;
        $params['currency']=($tx_data['currency_source']=="") ? $tx_data['currency_destination'] : $tx_data['currency_source'];
        $params['tx_value_type']=$tx_data['tx_value_type'];
        $params['dest_account']=$tx_data['dest_account'];
        $params['source_account']=$tx_data['source_account'];
        $params['amount']=$tx_data['amount_source'];
        $params['user_id']=$tx_data['user_id'];
        $params['tx_channel']=$tx_data['tx_channel'];
        $params['tx_type']=$tx_data['tx_type'];
        
        $checks=(TxapiController::class)::tx_validator($params);
// Test score check point : transaction validation point
 $checks->status;
        if($checks->status === "failed")
         {
          $dta['code']="500";
          $dta['message']=$checks->message;
          $dta['status']=$checks->status;
          return $dta;
          return $checks;
         }
//        else
//          return "Processed";

//        return $tx_data;
        //Load request into table
        $query['table']="tx_api_refs";
        $query['column_values']=$tx_data;
        $query['column_values']=array_filter($query['column_values']);
        $query['condition']=null;
        $query['extras']=null;
        $dta['body']=(qryrnrController::class)::insert1($query);
        $dta['body']=json_decode($dta['body'],true);
        $success=false;
        //Inititate transaction once registration is done
        if($dta['body']!=null && !empty($dta['body']))
                  {
                    $success=true;
                    $dta['flutter']=(TxapiController::class)::tx_api_request($dta['body']['id']);
                  }
        $dta['code']=($success)?"200":"500";
        $dta['message']=($success)?"finished":"failed to initiate transaction !";
        $dta['status']=($success)?"success":"failed";
        return $dta;
}

public static function open_insert_update($rq=null,$params=null){
        $insert=($rq->input('submit')!=null&&$rq->input('update')==null);
        $update=($rq->input('submit')==null&&$rq->input('update')!=null);
        $success=true;
        $query['table']=$rq['table'];

        $query['column_values']=\Arr::except($rq->input(), (MethodsController::class)::skip_params());
         $query['column_values']=array_filter($query['column_values']);

        $query['condition'][]=[' ','id','=',($rq->input('id')??-1111111111111111111111111)];
    
        $query['extras'][]=null;
//        return ($insert)?"inserting":"upadting";

        $code='200';
        if($insert)
            $dta['body']=(qryrnrController::class)::insert1($query);
        else if($update)
            $dta['body']=(qryrnrController::class)::update1($query);
        else
            $code="500";
        $dta['code']=$code;
        $dta['message']=($success)?"finished":"failed to enter data";
        $dta['status']=($success)?"success":"failed";
        return $dta;
    }
    
public static function approvals_get($params=null){    //Fetch Approvals
        $db_srvc=qryrnrController::class;
        $query['table']="approvals a left join users u on a.user_id=u.id ";
 
        $query['column_values']=[
                                'a.id'=>'',
                                'a.source_table'=>'',
                                'a.source_column'=>'',
                                'a.source_id'=>'',
                                'a.request'=>'',
                                'a.approve_status'=>'',
                                'a.approve_scope'=>'',
                                'a.approver_role'=>'',
                                'a.ref_center_id'=>'',
                                '(select count(*) from approvals p where p.approve_ref_id=a.id ) as approve_count',
                                'a.approve_narration'=>'',
                                'a.approve_count'=>'',
                                'a.user_id'=>'',
                                'a.created_at'=>'',
                                'a.approve_action'=>'',
                                'a.approve_ref_id'=>'',
                                'u.name as name_submitter'=>'',
                                'u.mobile as mobile_submitter'=>'',
                               ];

        $query['condition'][]=[' ','1','=','1'];

        if($params!=null&&isset($params['condition'])&&$params['condition']!=null)
         for($k=0;$k<count($params['condition']);$k++)
            $query['condition'][]=$params['condition'][$k];

        if($params!=null&&isset($params['extras'])&&$params['extras']!=null)
         for($k=0;$k<count($params['extras']);$k++)
            $query['extras'][]=$params['extras'][$k];

//        $query['extras'][]=" group by u.id order by u.name asc";


        $query['extras'][]=(isset($params['limit']) )?" page ".$params['limit']:null;

        $dta['body']=$db_srvc::select1($query);

        $dta['code']="200";
        $dta['message']="finished";
        $dta['status']="success";
        return $dta;
    }


public static function center_members_get($params=null){    //Fetch members of centers
        $db_srvc=qryrnrController::class;
        $query['table']="ref_center_members m 
                         left join ref_centers c on m.ref_center_id=c.id 
                         left join providers pr on m.provider_id=pr.id 
                         left join tx_accounts txa on (c.id=txa.ref_center_id and txa.tx_value_type='wallet') 
                         left join tx_accounts ma on (m.user_id=ma.user_id and ma.tx_value_type='wallet') 
                         left join request_types rt on c.service_id=rt.id 
                         ";

        $query['column_values']=[
                                'm.id as id_member'=>'',
                                'm.ref_center_id'=>'',
                                'm.owner_id'=>'',
                                'm.provider_id'=>'',
                                'm.user_id as admin_id_member'=>'',
                                "(case when (select count(*) from ref_center_requests rq where rq.member_id=m.member_id) = 0 then m.name else concat(m.name,' : ',(select TIMESTAMPDIFF(DAY,max(rq.created_at),curdate()) from ref_center_requests rq where rq.ref_center_id=c.id and rq.ref_center_id=m.ref_center_id and rq.member_id=m.member_id),'dys : ',' (',(select count(*) from ref_center_requests rq where rq.member_id=m.member_id and rq.taken_amount > 0),'X) ') end )as name_member"=>'',
                                'm.mobile as mobile_member'=>'',
                                'm.member_id as member_id'=>'',
                                'm.address as address_member'=>'',
                                'm.param1 as param1_member'=>'',
                                'm.param2 as param2_member'=>'',
                                'm.param3 as param3_member'=>'',
                                'm.param4 as param4_member'=>'',
                                'm.param5 as param5_member'=>'',
                                'm.image1 as image1_member'=>'',
                                'm.image2 as image2_member'=>'',
                                'm.image3 as image3_member'=>'',
                                'm.image4 as image4_member'=>'',
                                'm.image5 as image5_member'=>'',
                                'm.role as role_member'=>'',
                                '0 as cash_member'=>'',
                                '0 as unpaid_member'=>'',
                                'm.active_status as active_status_member'=>'',
                                'c.id as id_center'=>'',
                                'c.user_id'=>'',
                                'c.center_code'=>'',
                                'c.center_type'=>'',
                                'c.name as name_center'=>'',
                                'c.mobile as mobile_center'=>'',
                                'c.address as address_center'=>'',
                                'c.address_gps'=>'',
                                'c.image_center1'=>'',
                                'c.image_center2'=>'',
                                'c.image_center2'=>'',
                                'c.param1 as param1_center'=>'',
                                'c.param2 as param2_center'=>'',
                                'c.param3 as param3_center'=>'',
                                'c.param4 as param4_center'=>'',
                                'c.param5 as param5_center'=>'',
                                'c.image1 as image1_center'=>'',
                                'c.image2 as image2_center'=>'',
                                'c.image3 as image3_center'=>'',
                                'c.image4 as image4_center'=>'',
                                'c.image5 as image5_center'=>'',
                                'c.active_status'=>'',
                                'c.provider_acc_number'=>'',
                                'c.other'=>'',
                                'c.provider_id'=>'',
                                'c.credit_limit'=>'',
                                'c.advance_start_date'=>'',
                                'c.advance_end_date'=>'',
                                'c.advance_limit_rate'=>'',
                                'c.interest_rate'=>'',
                                'c.service_id'=>'',
                                'c.country_code'=>'',
                                'txa.acc_number as acc_number_center'=>'',
                                'txa.currency as currency_center'=>'',
                                'ma.acc_number'=>'',
                                'ma.currency'=>'',
                                'rt.name as service_name'=>'',
                                'rt.request_type'=>'',
                                'rt.id as request_type_id'=>'',
                                'pr.name as name_provider'=>'',
                                'pr.address as address_provider'=>'',
                                'pr.mobile as mobile_provider'=>'',
                                '(select count(*) from ref_center_members cm where c.id=cm.ref_center_id) as members'=>'',
                                '(select count(*) from ref_center_members cm where m.member_id=cm.member_id) as reg_count_member'=>'',
                                "(select sum(rq.paid_amount) from ref_center_requests rq where rq.ref_center_id=c.id and rq.user_id=m.owner_id) as amount_member"=>'',
                                "(select sum(rq.requested_amount) from ref_center_requests rq where rq.ref_center_id=c.id and rq.user_id=m.owner_id and rq.paid_amount>0) as requested_amount_total"=>'',
                                "(select sum(rq.balance) from ref_center_requests rq where rq.ref_center_id=c.id and rq.ref_center_id=m.ref_center_id and rq.member_id=m.member_id) as unpaid_member"=>'',
                                "(select TIMESTAMPDIFF(DAY,max(rq.created_at),curdate()) from ref_center_requests rq where rq.ref_center_id=c.id and rq.ref_center_id=m.ref_center_id and rq.member_id=m.member_id) as days_past"=>'',
                                "(select count(*) from ref_center_requests rq where rq.member_id=m.member_id and rq.payback_amount > 0) as requests_count"=>'',
                                "(select sum(balance) from ref_center_requests rq where rq.member_id=m.member_id) as unpaid_total"=>'',
                                "(select count(*) from tx_accounts tac where c.id=tac.ref_center_id and tac.tx_value_type='cash') as cash"=>'',
                                "(select count(*) from tx_accounts tac where c.id=tac.ref_center_id and tac.tx_value_type='cash') as wallet"=>'',
                                "(select count(*) from user_access uac where c.id=uac.access_ref_id and (uac.role='admin' or uac.role='owner')) as admin_count"=>'',
                                "(select count(*) from tx_account_profit_holders prf where prf.acc_number_receiver=txa.acc_number and prf.active_status='active') as proffit_srources"=>'',
                               ];

        $query['condition'][]=[' ','1','=','1'];

        if($params!=null&&isset($params['condition'])&&$params['condition']!=null)
         for($k=0;$k<count($params['condition']);$k++)
            $query['condition'][]=$params['condition'][$k];

        if($params!=null&&isset($params['extras'])&&$params['extras']!=null)
         for($k=0;$k<count($params['extras']);$k++)
            $query['extras'][]=$params['extras'][$k];

//        $query['extras'][]=" group by u.id order by u.name asc";


        $query['extras'][]=(isset($params['limit']) )?" page ".$params['limit']:null;

        $dta['body']=$db_srvc::select1($query);

        $dta['code']="200";
        $dta['message']="finished";
        $dta['status']="success";
        return $dta;
    }
 


public static function user_centers_get($params=null){
        $db_srvc=qryrnrController::class;
        $query['table']="ref_centers c left join tx_accounts txa on c.id=txa.ref_center_id
                         left join providers pr on c.provider_id=pr.id";

        $query['column_values']=[
                                'c.id'=>'',
                                'c.user_id'=>'',
                                'c.provider_id'=>'',
                                'c.center_code'=>'',
                                'c.center_type'=>'',
                                'c.name'=>'',
                                'c.mobile'=>'',
                                'c.address'=>'',
                                'c.address_gps'=>'',
                                'c.image_center1'=>'',
                                'c.image_center2'=>'',
                                'c.image_center2'=>'',
                                'c.param1'=>'',
                                'c.param2'=>'',
                                'c.param3'=>'',
                                'c.param4'=>'',
                                'c.param5'=>'',
                                'c.image1'=>'',
                                'c.image2'=>'',
                                'c.image3'=>'',
                                'c.image4'=>'',
                                'c.image5'=>'',
                                'c.active_status'=>'',
                                'c.provider_acc_number'=>'',
                                'c.other'=>'',
                                'c.provider_id'=>'',
                                'c.credit_limit'=>'',
                                'c.advance_start_date'=>'',
                                'c.advance_end_date'=>'',
                                'c.advance_limit_rate'=>'',
                                'c.interest_rate'=>'', 
                                'txa.acc_number as acc_number_wallet'=>'',
                                'txa.currency as currency_wallet'=>'',
                                'txa.amount as amount_wallet'=>'',
                                'pr.name as name_provider'=>'',
                                'pr.address as address_provider'=>'',
                                'pr.approve_narration as approve_narration'=>'',
                                "(select acc_number from tx_accounts cac where cac.ref_center_id=c.id and cac.tx_value_type='cash' limit 1) as acc_number_cash"=>'',
                                "(select currency from tx_accounts cac where cac.ref_center_id=c.id and cac.tx_value_type='cash' limit 1) as currency_cash"=>'',
                                "(select amount from tx_accounts cac where cac.ref_center_id=c.id and cac.tx_value_type='cash' limit 1) as amount_cash"=>'',
                                '(select count(*) from ref_center_members cm where c.id=cm.ref_center_id) as members'=>'',
                                "(select count(*) from ref_center_members cm where c.id=cm.ref_center_id and cm.active_status='inactive') as members_inactive"=>'',
                                "(select count(*) from ref_center_members cm where c.id=cm.ref_center_id and cm.active_status='inactive' and TIMESTAMPDIFF(DAY,cm.created_at,curdate())=0  ) as members_inactive_today"=>'',
                                "(select count(*) from approvals ap where c.id=ap.ref_center_id and ap.entity_type='request' and ap.approve_status!='approved') as approvals"=>'',
                                "(select sum(tac.amount) from tx_accounts tac where c.id=tac.ref_center_id and tac.tx_value_type='cash') as cash_total"=>'',
                                "(select sum(tac.amount) from tx_accounts tac where c.id=tac.ref_center_id and tac.tx_value_type='wallet') as wallet_total"=>'',
                                "(select count(*) from user_access uac where c.id=uac.access_ref_id and (uac.role='admin' or uac.role='owner')) as admin_count"=>'',
                                "(select count(*) from tx_account_profit_holders prf where prf.acc_number_receiver=txa.acc_number and prf.active_status='active') as proffit_srources"=>'',
                                "(select count(*) from tx_account_profit_holders prf where prf.acc_number_giver=txa.acc_number and prf.active_status='active') as proffit_receivers"=>'',
                                "(select sum(balance) from ref_center_requests rq where rq.ref_center_id=c.id) as unpaid_total"=>'',
                               ];

        $query['condition'][]=[' ','1','=','1'];

        if($params!=null&&isset($params['condition'])&&$params['condition']!=null)
         for($k=0;$k<count($params['condition']);$k++)
            $query['condition'][]=$params['condition'][$k];

        if($params!=null&&isset($params['extras'])&&$params['extras']!=null)
         for($k=0;$k<count($params['extras']);$k++)
            $query['extras'][]=$params['extras'][$k];

//        $query['extras'][]=" group by u.id order by u.name asc";


//        $query['extras'][]=(isset($params['limit']) )?" page ".$params['limit']:null;

        $dta['body']=$db_srvc::select1($query);

        $dta['code']="200";
        $dta['message']="finished";
        $dta['status']="success";
        return $dta;
    }
 



public static function signup_verify_mkr($rq=null,$params=null){
        $success=true;

        $query['table']="users";
        $query['column_values']=[
                                'verification_status'=>'yes'
                               ];
        $query['condition'][]=[' ','verification_code','=',($rq->input('verification_code')??-1111111111111111111111111)];
        $query['condition'][]=[' and ','mobile','=',($rq->input('mobile')??-1111111111111111111111111)];
        $query['extras'][]=' ';
        $dta['body']=(qryrnrController::class)::update1($query);

        $user_data=json_decode($dta['body'],true);

        $success=($dta['body']!=null&&isset($user_data[0]['id']));
        
        
        //create account for user
        if($success) // 
          {
           $currency="UGX";     
           $params=['user_id'=>$user_data[0]['id'], 'acc_name'=>$user_data[0]['name'], 'currency'=>$currency, 'tx_value_type'=>'wallet', 'acc_category'=>'user'];
           $account=self::account_create($params);
           $params=null;
           $params['condition']=[   [' and ',' u.id','=',$user_data[0]['id']]   ];
           $params['extras'][]=' group by u.id order by u.created_at limit 1';
           return $dta=(self::get_users($params));
          }
        //Get user data and load into session
        

        $dta['code']=($success)?'200':'400';
        $dta['message']=($success)?"finished".json_encode($params['condition']):"Cannot maatch user's code";
        $dta['status']=($success)?"success":"failed";
        return $dta;
    }


public static function signup_mkr($rq=null,$params=null){
        $insert=($rq->input('submit')!=null&&$rq->input('update')==null);
        $update=($rq->input('submit')==null&&$rq->input('update')!=null);
        $success=true;
        $db_srvc=qryrnrController::class;
        $query['table']="users";

        $query['column_values']=\Arr::except($rq->input(), (MethodsController::class)::skip_params());

        $query['condition'][]=[' ','id','=',($rq->input('id')??-1111111111111111111111111)];

        $query['extras'][]=(isset($params['limit']) )?" limit ".$params['limit']:null;

//        return $query;
        $code='200';
        if($insert)
            $dta['body']=$db_srvc::insert1($query);
        else if($update)
            $dta['body']=$db_srvc::update1($query);
        else
           {
            $code="500";
            $success=false;
           }
        $dta['code']=$code;
        $dta['message']=($success)?"finished":"failed to enter data";
        $dta['status']=($success)?"success":"failed";
        return $dta;
    }

public static function login_mkr($rq=null,$params=null){
        $db_srvc=qryrnrController::class;
        $query['table']="users";
//        $query['column_values']=$rq->input();

        $query['column_values']=[
                                'id'=>'',
                                'mobile'=>'',
                                'name'=>'',
                                'password'=>'',
                               ];

        $query['condition'][]=[' ','mobile','=',$rq->input('mobile')];
//        $query['condition'][]=[' and ','password','=',$rq->input('password')];

        $query['extras'][]=" limit 1 ";

        $dta['body']=$db_srvc::select1($query);

        //$dta['decrypted_password']=null;
        //$pass_enc=$dta['body'][0]->password;
        $dta['decrypted_password']=($dta['body']!=null&&!empty($dta['body']))?\Crypt::decryptString($dta['body'][0]->password):null;
        
        $dta['code']="200";
        $dta['message']="finished";
        $dta['status']="success";
        return $dta;
    }
 

public static function get_users($params=null){
        $db_srvc=qryrnrController::class;
        $query['table']="users u left join tx_accounts a on u.id=a.user_id and a.acc_category='user'";

        $query['column_values']=[
                                'distinct(u.id)'=>'',
                                'u.name'=>'',
                                'u.mobile'=>'',
                                'u.country_code'=>'',
                                'u.verification_status'=>'',
                                'u.active_status'=>'',
                                'u.user_rank'=>'',
                                'u.login_count'=>'',
                                'u.access_tocken'=>'',
                                'a.amount'=>'',
                                'a.acc_number'=>'',
                                'a.currency'=>'',
                                '(select count(*) from tx_account_profit_holders p where p.acc_number_receiver=a.acc_number) as proffit_sources'=>'',
                                "(select count(*) from providers v where v.active_status_invest='available') as investment_available"=>'',
                                "(select count(*) from tx_accounts ts where ts.user_id=u.id) as accounts_count"=>'',
                                "(select sum(amount) from tx_accounts ts where ts.user_id=u.id) as amount_total"=>'',
                                "(select sum(amount) from tx_accounts ts where ts.user_id=u.id and ts.tx_value_type='cash') as amount_total_cash"=>'',
                                "(select sum(amount) from tx_accounts ts where ts.user_id=u.id and ts.tx_value_type='wallet') as amount_total_wallet"=>'',
                                "(select count(*) from ref_centers cnt where cnt.user_id=u.id) as centers_count"=>'',
                                "(select sum(balance) from ref_center_requests rq where rq.member_id in (select cm.member_id from ref_center_members cm where cm.mobile=u.mobile and cm.country_code=u.country_code) ) as unpaid_total"=>'',
                               ];

        $query['condition'][]=[' ','1','=','1'];

        if($params!=null&&isset($params['condition'])&&$params['condition']!=null)
         for($k=0;$k<count($params['condition']);$k++)
            $query['condition'][]=$params['condition'][$k];

        if($params!=null&&isset($params['extras'])&&$params['extras']!=null)
         for($k=0;$k<count($params['extras']);$k++)
            $query['extras'][]=$params['extras'][$k];

//        $query['extras'][]=" group by u.id order by u.name asc";


        $dta['body']=$db_srvc::select1($query);

        $dta['code']="200";
        $dta['message']="finished";
        $dta['status']="success";
        return $dta;
    }
 


    // tx_accounts_create create an account for a person    [user_id,acc_name,currency,tx_value_type,acc_category]
    public static function account_create($params,$force=null){
        //Check the last account creation time
        $last_acc_time=DB::table('tx_accounts')->where('user_id','=',$params['user_id'])->max('created_at');
        
        //check if it is a re-submission or it is permited repeat
        $diff_in_minutes=10000;
        if($force==null)
        $diff_in_minutes=(!isset($last_acc_time)&&$last_acc_time==null&&!empty($last_acc_time)) ?(MethodsController::class)::date_diff_open($last_acc_time,date('Y-m-d H:s:i'),"minutes"):10000;

        //stop if last account creation is within 30min
        if($diff_in_minutes<(720+3))
            return null;

        $currency=$params['currency']??'UGX';
        $tx_value_type=$params['tx_value_type']??'cash';
        $acc_category=$params['acc_category']??'user';

        $query['table']="tx_accounts";
        $query['column_values']=[
                                  'user_id'=>$params['user_id'],
                                  'acc_name'=>($params['acc_name']!=null&&!empty($params['acc_name']))?$params['acc_name']:(DB::table('users')->find($params['user_id']))[0]->name,
                                  'currency'=>$currency,
                                  'tx_value_type'=>$tx_value_type,
                                  'acc_category'=>$acc_category,
                                  'acc_number'=>date("Ymdhis").((MethodsController::class)::code_generator(3))."00".$params['user_id'],
                                  'invite_code'=>$params['user_id'].((MethodsController::class)::code_generator(4)),
                                  'ref_center_id'=>$params['ref_center_id']??"",
                                  'provider_id'=>$params['provider_id']??"",
                                  'tx_channel'=>$params['tx_channel']??"",
                                ];
        $query['condition']=null;//[' ','id','=','-1111111111111111111111111'];
        $query['extras']=null;//(isset($params['limit']) )?" limit ".$params['limit']:null;
        $acc_data=(qryrnrController::class)::insert1($query);
        return $acc_data;
    }
    
    

    //Fetch account summary    $params['condition'][]=[' ','1','=','1'] 
    public static function tx_accounts_get($params){
        $query['table']="tx_accounts ac 
                         left join users u on ac.user_id=u.id
                        ";
        $query['column_values']=[
                                  'ac.user_id'=>'',
                                  'ac.acc_name'=>'',
                                  'ac.currency'=>'',
                                  'ac.tx_value_type'=>'',
                                  'ac.tx_channel'=>'',
                                  'ac.acc_category'=>'',
                                  'ac.acc_number'=>'',
                                  'ac.invite_code'=>'',
                                  'ac.ref_center_id'=>'',
                                  'ac.provider_id'=>'',
                                  'ac.ref_center_id'=>'',
                                  'ac.amount'=>'',
                                  'u.name as name_owner'=>'',
                                  'u.mobile as mobile_owner'=>'',
                                  'u.country_code'=>'',
                                  "( select count(*) from providers pr where pr.acc_number_investor=ac.acc_number ) as investment_count"=>'',
                                  "( select count(*) from tx_account_profit_holders sr where sr.acc_number_receiver=ac.acc_number ) as profit_srources_count"=>'',
                                  "( select count(*) from tx_account_profit_holders sr where sr.acc_number_giver=ac.acc_number ) as profit_dependants"=>'',
                                  "( select sum(tl.cash_amount) from tx_account_logs tl where tl.tx_type='deposit' ) as deposit_day"=>'',
                                  "( select sum(tl.cash_amount) from tx_account_logs tl where tl.tx_type='withdraw' ) as withdraw_day"=>''
                                ];
        $query['condition'][]=[' ','1','=','1'];
        if($params!=null && isset($params['condition']) && $params['condition']!=null)
         for($k=0;$k<count($params['condition']);$k++)
            $query['condition'][]=$params['condition'][$k];

        if($params!=null && isset($params['extras']) && $params['extras']!=null)
         for($k=0;$k<count($params['extras']);$k++)
            $query['extras'][]=$params['extras'][$k];

//        return $query;
        $acc_data=(qryrnrController::class)::select1($query);
        return $acc_data;
    }

public static function get_users_old($rq=null,$params=null){
        $db_srvc=qryrnrController::class;
        $query['table']="mylogs1";
//        $query['column_values']=$rq->input();

        $query['column_values']=[
//                                'id'=>date('js'),
                                'name'=>$rq->input('name'),
                                'mobile'=>$rq->input('mobile'),
                                'password'=>$rq->input('password'),
                               ];
        return json_encode($query['column_values']);
        $query['column_values'] = array_except($query['column_values'],$except_keys);
        return json_encode($query['column_values']);

        $query['condition'][]=[' ','id','=',2534];

        $query['extras'][]=(isset($params['limit']) )?" limit ".$params['limit']:null;
        $query['extras'][]=(isset($params['limit']) )?" page ".$params['limit']:null;
//        $query['step1_request']=json_encode($rq);
//        $dta['body']=$db_srvc::sql_select1($query);
        $dta['body']=$db_srvc::select1($query);
//        $dta['body']=$db_srvc::insert1($query);
//        $dta['body']=$db_srvc::update1($query);
//        $dta['body']=$db_srvc::delete1($query);
        $dta['code']="200";
        $dta['message']="finished";
        $dta['status']="success";
        return $dta;
    }
 

}


