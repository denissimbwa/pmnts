<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MethodsController extends Controller
{
    //
    
    
public static function sendmail($from,$to,$subject,$message)
    {
	   $header = "From: $from";
	   $retval = mail($to,$subject,$message,$header);
	   if( $retval == true )  
	     {
		  return "Email sent successfully...";
	     }
	   else
	     {
		  return "Email could not be sent...";
	     }
	}
    
public static function charges(){
        $data=['dp_charge'=>0,'wd_charge'=>200,'tx_charge'=>500,'api_charge'=>300];
        return $data;
    }
// give it curency and it return it's corresponding code and vise verser 'UGX' return 256 and '256' return UGX
public static function get_currency_code($currency_or_country_code){
        $data=['UGX'=>'256','256'=>'UGX','KES'=>'254','254'=>'KES'];
        return $data[$currency_or_country_code];
    }
//Get televom provider for full telephone number
public static function get_telecom_name($telephone){
        if (str_starts_with($telephone, '25670') || str_starts_with($telephone, '25674') || str_starts_with($telephone, '25675') || str_starts_with($telephone, '25620'))
           return "AIRTEL";
        if (str_starts_with($telephone, '25677') || str_starts_with($telephone, '25678') || str_starts_with($telephone, '25676') || str_starts_with($telephone, '25631') || str_starts_with($telephone, '25639'))
           return "MTN";
    }
//Make fully qualied mobile phone number ['mobile','currency','code']
public static function mobile_phone_maker($params){
    $mobile=substr($params['mobile'],1, strlen($params['mobile'] - 1));
    $code=$params['code'] ?? self::get_currency_code($params['currency']);
    return $code.$mobile;
}

public static function validation_profit_alocation($rq){
    $total=100;
    $slots=$rq;
    for($t=0; $t < count($slots); $t++ )
       $total-=$slots[$t];
    return ($total==0)?true:false;
}

public static function validation_account_owner($params){
 $acc_number=$params['acc_number'];
 $user_id=$params['user_id'];
 if($acc_number==null)
 return 1;

 $found=DB::table('tx_accounts')->where('user_id','=',$user_id)->where('acc_number','=',$acc_number)->count();
 return $found;
}

public static function provider_sync($provider_id,$approve_ref_id=null){
    //get provider fulldetail
        $params['condition'][]=[' and ','pr.id','=',$provider_id];
        $dta=(qrymkrController::class)::providers_get($params);
        $dta=$dta['body'][0];
    //update provider investor account
        $affected=DB::table('providers')->where('id','=',$provider_id)
                                        ->update(['acc_number_investor'=>$dta->acc_number_investor_selected,'active_status_invest'=>'closed']);
    //disconnect all other ccounts that are not selected from investment_booking
        $affected=DB::table('investment_bookings')->where('provider_id','=',$provider_id)
//                                                  ->where('acc_number','!=',$dta->acc_number_investor_selected)
                                                  ->where('approve_ref_id','!=',$approve_ref_id)
                                                  ->update(['status'=>'closed','approve_status'=>'rejected']);
}

public static function api_ref_recovery_sync($tx_id_parent){
        //get total paid in live acounts log
        $recovered_amount=DB::table('tx_account_logs')->where('tx_id_parent','=',$tx_id_parent)
                                          ->where('tx_channel','!=','transit')
                                          ->sum('cash_amount');
        $recovered_amount=$recovered_amount??0;
        $affected=DB::table('tx_api_refs')->where('tx_id','=',$tx_id_parent)
                                        ->update(['recovered_amount'=>$recovered_amount]);
}

public static function account_sync_user($user_id){
        //get approval count
        $acc_detail=DB::table('tx_accounts')->where('user_id','=',$user_id)
                                            ->get();
        for($t=0;$t<count($acc_detail);$t++)
         self::account_sync(['acc_number'=>$acc_detail[$t]->acc_number]);
    return $acc_detail;
}

public static function account_sync($params){
        //get approval count
        $acc_detail=DB::table('tx_accounts')->where('acc_number','=',$params['acc_number'])
                                            ->first();
        //$acc_detail=$acc_detail[0];
        
        $cash_amount=DB::table('tx_account_logs')->where('acc_number','=',$params['acc_number'])
                                          ->where('tx_value_type','=',$acc_detail->tx_value_type)
//                                          ->where('currency','=',$acc_detail->currency)
                                          ->sum('cash_amount');
        $cash_amount=$cash_amount??0;
        $affected=DB::table('tx_accounts')->where('acc_number','=',$params['acc_number'])
                                        ->update(['amount'=>$cash_amount]);
}

//SMS uganda lines
public static function send_sms_uganda($content,$from,$to)   
  {
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => config('api_config.sms_url_uganda'),
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array('numbers' => $to,'msg' => $content,'username' => config('api_config.sms_username_africa'),'passwd'=>config('api_config.sms_key_uganda') ),
  CURLOPT_HTTPHEADER => array(
    'apiKey: '.config('api_config.sms_key_uganda').''
  ),
)); 
 
$response = curl_exec($curl);
curl_close($curl);
return $response;
}


public static function send_sms_socnet($content,$from,$to)
  {
    Log::channel('slackNotification')->info($from, ['Mobile' => $to,'SMS' => $content]);
    $from=($from==null)?"ZHURIS":$from;
    $report=self::send_sms_uganda($content,$from,$to);
//    return $report=file_get_contents(config('api_config.sms_url_socnet')."?username=".config('api_config.sms_username_socnet')."&passwd=".config('api_config.sms_password_socnet')."&msg=".urlencode($content)."&type=text&numbers=".str_replace(" ",",",$to)."&from=".config('api_config.sms_sender_socnet'));
  }


public static function post_url_submitter_headered($url,$fields,$headers)
  {
      if($headers==null)
      $headers = [
                    'Accept: */*',
                    'Content-Type: application/x-www-form-urlencoded',
                    'Custom-Header: custom-value',
                    'Custom-Header-Two: custom-value-2'
                ];

     //open connection 
     $ch = curl_init(); 
     //set the url, number of POST vars, POST data 
     curl_setopt($ch,CURLOPT_URL, $url); 
     curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($fields)); 
     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,15); 
     curl_setopt($ch, CURLOPT_TIMEOUT, 400); //timeout in seconds 
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
     if($headers!=null)
     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
    
     $result = curl_exec($ch); 
     curl_close($ch);
     return $result;
  }

public static function post_url_submitter($url,$data)
  {
$headers = [
    'Accept: */*',
    'Content-Type: application/x-www-form-urlencoded',
    'Custom-Header: custom-value',
    'Custom-Header-Two: custom-value-2'
];

 $ch = curl_init(); 

 curl_setopt($ch,CURLOPT_URL, $url); 
 curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($data)); 
 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
 curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,15); 
 curl_setopt($ch, CURLOPT_TIMEOUT, 400); //timeout in seconds 
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
 curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
 //execute post 
$result = curl_exec($ch); 
return $result;
  }



//Build transaction inistaiton body internally
public static function tx_request_builder($rq){
    $acc_nmbr_type="acc_number_".$rq['tx_value_type'];
    $deposit=($rq['tx_type']==="deposit");
    $withdraw=($rq['tx_type']==="withdraw");
    $transfer=($rq['tx_type']==="transfer");
    $transit=($rq['tx_channel']==="transit");

    //Get transacting channel detail
    if($deposit || $withdraw || $transit)
      {
         $params=['currency'=>$rq['currency_source'],
                  'tx_value_type'=>($transit)?"cash":$rq['tx_value_type'],
                  'tx_type'=>($rq['tx_type']==="transfer")?"withdraw":$rq['tx_type'],
                  'tx_channel'=>$rq['tx_channel'],
                  'ref_amount'=>$rq['amount_source']
                 ];
         $fees=(TxapiController::class)::charges_get($params);
         $rq['tx_fee']= $fees->charge;
         $rq['api_fee']= $fees->api_fee;
         $rq['acc_number_office']= $fees->acc_number;
       }
    
    //group transcations settings
    if(!$transfer && $rq['tx_owner_type']==="group")
      {
       //get group detail first
       $params=null;
       $params['condition'][]=[' and ','c.id','=',$rq['tx_owner_id'] ];
       $params['extras'][]='';
       $dta=(qrymkrController::class)::user_centers_get($params);
       $dta=$dta['body'][0];
       //load missing parameters into transaction body
       $acc_number=$dta->$acc_nmbr_type;
//print_r($acc_number);
       if($rq['source_account']==null || empty($rq['source_account']) || $rq['source_account']==="")
         {
          $rq['source_account']=($deposit)?"":$acc_number; //source account is used for withdrw
          $rq['currency_source']=($deposit)?"":$dta->currency_wallet; //source account is used for withdrw
         }
       if($rq['dest_account']==null || empty($rq['dest_account']) || $rq['dest_account']==="")
         {
          $rq['dest_account']=($withdraw)?"":$acc_number;  //destination account is used for deposit
          $rq['currency_destination']=($withdraw)?"":$dta->currency_wallet;  //destination account is used for deposit
         }
      }
    //uset transcations settings where deposit is going to an individual's account
    else if(!$transfer && $rq['tx_owner_type']==="user")
      {
       //get user's detail first
       $params=null;
       $params['condition'][]=[' and ','u.id','=',$rq['tx_owner_id'] ];
       $params['extras'][]='';
       $dta=(qrymkrController::class)::get_users($params);
       $dta=$dta['body'][0];
       //load missing parameters into transaction body
       $acc_number=$dta->acc_number;
       if($rq['source_account']==null || empty($rq['source_account']) || $rq['source_account']==="")
         {
          $rq['source_account']=($deposit)?"":$acc_number; //source account is used for withdrw
          $rq['currency_source']=($deposit)?"":$dta->currency; //source account is used for withdrw
         }
       if($rq['dest_account']==null || empty($rq['dest_account']) || $rq['dest_account']==="")
         {
          $rq['dest_account']=($withdraw)?"":$acc_number;  //destination account is used for deposit
          $rq['currency_destination']=($withdraw)?"":$dta->currency;  //destination account is used for deposit
         }
      }

    $rts=self::currency_converter($rq['amount_source'],$rq['currency_source'],$rq['currency_destination']);
    $rq['tx_rate']=$rts['rate'];
    $rq['ref_amount']=$rts['amount'];


    //If credit repay
    
    //If campaign deposit
    return $rq;
}

public static function currency_converter($amount,$from_currency,$to_currency){
 $dta['rate']=1;
 $dta['amount']=$amount;
 if($from_currency==null || $to_currency==null || $from_currency===$to_currency)
   return $dta;
 
 return $dta;
}

//convert cahrege range xtring and extract that right charge for a given amount [range=>'1000-100,500-300', 'amount'=>3000]
public static function charge_converter($params){
//    return $params['range'];
    $charge_range=$params['range']??0;
    if($charge_range==="0")
     return $charge_range;
    $charge=0;
//PAUSE    $amount=$params['range']??"0";
    $steps=explode(",",$charge_range);
    if(!empty($steps))
    if($steps!=null)
    for($j=0;$j<count($steps);$j++)
     {
      $level=explode("-",$steps[$j]);
      //return $params['amount'].":-:".$level[1];
      if($level[0]<=$params['amount'])
         $charge=$level[1] ?? 0;
      else
         break;
     }
    return $charge;
}


//Syncronise approval       [ref_center_id]
public static function approval_sync($params){
        //get approval count
        $approve_count=DB::table('approvals')//->where('ref_center_id','=',$params['ref_center_id'])
                                                    ->where('approve_ref_id','=',$params['approve_ref_id'])
                                                    ->where('approve_status','=','approved')
                                                    ->count();
        $approve_status=($approve_count >= $params['approve_count'])?'approved':'processing';
        
        $affected=DB::table('approvals')->where('approve_ref_id','=',$params['approve_ref_id'])
                                        ->where('entity_type','=','request')
                                        ->where('approve_status','!=','approved')
                                        ->update(['approve_count'=>$approve_count,'approve_status'=>$approve_status ]);
       if($affected<1)
         return $dta['message']="Cannot process further action";
       return self::approval_finisher($params['approve_ref_id']);
}
public static function approval_finisher($approve_ref_id){
        //get approval summary
        $params['condition'][]=[' ',' and a.approve_ref_id','=',$approve_ref_id];
        $params['condition'][]=[' ',' and a.entity_type','=','request'];
        $params['extras'][]=' order by a.created_at asc';
        $approval=(qrymkrController::class)::approvals_get($params);
        $approval=$approval['body'][0];
        
        //Check if it is not succesful
        if($approval->approve_status!=="approved")
          return $dta['message']="Pending enough approvals";
        
        $columns_to_update="";
        $approve_status=$approval->approve_status;

        //Get table column names
//        $query = "SHOW COLUMNS FROM ".$approval->source_table;
 //       $data['columns'] = \DB::select($query);
        $columns  = \Schema::getColumnListing($approval->source_table);

        $set_pairs="approve_status='".$approve_status."'";
        if($columns!=null)
        if(!empty($columns))
         {
          for($t=0;$t<count($columns);$t++)
            {
             $col_name=$columns[$t];
             $pstn=(strrpos($col_name,"_edit"));
             if($pstn)
             if(strlen($col_name)-$pstn==5)
                {
                 $set_pairs.=" ,`".(substr($col_name,0,$pstn))."`=".$col_name;
                }
            }
        if($approval->source_table==="tx_api_refs")
          $set_pairs.=" ,api_amount=ref_amount,tx_status='pending',api_id='".$approval->id."'";
//          return "update users set ".$set_pairs." where id = '".$id."'";

        if($approval->source_table==="tx_account_logs")
          $set_pairs.=" ,approve_status='approved', transit_status='processed',approve_narration='".$approval->approve_narration."'";


//set new approved values into original table
        if($approval->source_table==="investment_bookings")
          DB::unprepared("update ".$approval->source_table." set ".$set_pairs." where  id = '".$approval->source_id."' and approve_ref_id = '".$approval->approve_ref_id."'");
        else if($approval->source_table==="tx_account_profit_holders" )
          DB::unprepared("update ".$approval->source_table." set ".$set_pairs." where ".$approval->source_column." = '".$approval->approve_ref_id."'");
        else
          DB::unprepared("update ".$approval->source_table." set ".$set_pairs." where id = '".$approval->source_id."' and approve_ref_id = '".$approval->approve_ref_id."'");
//          $affected=DB::table($approval->source_table)->where('id','=',$approval->source_id)->where('approve_status','=','approved')->count();

        $rslt=null;
//Process after effects of approval
        if($approval->source_table==="tx_api_refs")     // finish transaction if it was an initiationn
          {
           $rslt=(TxapiController::class)::tx_push_receive($approval->source_id);
//           $rslt=(TxapiController::class)::tx_api_response($approval->source_id);
          }
        else if($approval->source_table==="tx_account_logs")     // finish transaction if it is from live account logs - this is usually transit
          {
           $rslt=(TxapiController::class)::tx_api_response_transit_approval($approval->source_id);
          }

        else if($approval->source_table==="investment_bookings")     // finish transaction if it from live account logs - this is usually transit
          {
           $investor=DB::table("investment_bookings")->find($approval->source_id);
           $rslt=self::provider_sync($investor->provider_id,$approval->approve_ref_id);
          }

        return $rslt;     
        }

}



//approval preview  [relation - approve_ref_id - id ]  //id is for the table named via relation and approve_ref_id is the approval request
public static function approval_preview($params){
            $source_id=$dta['source_id']=$params['source_id'];
            $relation=$dta['relation']=$params['relation'];
            $approve_ref_id=$params['approve_ref_id'];

   //Get detail of subject being appoved
//            $subject_data=DB::table($params['relation'])->find($params['source_id']);
            $subject_data=DB::table($params['relation'])->where($params['source_column'],'=',$params['source_id'])->first();
            //$dta['subject_data']=$subject_data??null;

    //Get previus approvals istory
             $params['condition'][]=[' and ','a.approve_ref_id','=',$approve_ref_id];
             $params['condition'][]=[' and ','a.entity_type','!=','request'];
             $params['extras'][]=null;
             $dta['approve_history']=((qrymkrController::class)::approvals_get($params))['body']??null;

   
   //Get approval request detail
            $approve_detail=DB::table('approvals')->where('approve_ref_id','=',$params['approve_ref_id'])->where('entity_type','=','request')->first();
            $dta['approve_detail']=$approve_detail??null;
   //Build array of preview data
    
       if($dta['relation']==="ref_center_members"){                             // Detail about center member
         $dta['body'][]=['PARAMETER NAME','PREVIUS STATUS','NEW STATUS','type'];
         $dta['body'][]=['Name',$subject_data->name,$subject_data->name,'text'];
         $dta['body'][]=['Mobile',$subject_data->mobile,$subject_data->mobile,'text'];
         $dta['body'][]=['address',$subject_data->address,$subject_data->address,'text'];
         $dta['body'][]=['Status',$subject_data->active_status,$subject_data->active_status_edit,'text'];
         $dta['body'][]=['Role',$subject_data->role,$subject_data->role_edit,'text'];
         $dta['body'][]=['Credit Limit',$subject_data->credit_limit,$subject_data->credit_limit_edit,'text'];
       }
       if($dta['relation']==="tx_api_refs"){                             // Detail about center member
         $dta['body'][]=['PARAMETER NAME','PREVIUS STATUS','NEW STATUS','type'];
         $dta['body'][]=['Ref',$subject_data->ref_id,$subject_data->ref_id,'text'];
         $dta['body'][]=['Amount',$subject_data->ref_amount,$subject_data->ref_amount,'text'];
         $dta['body'][]=['Status',$subject_data->tx_status,"success",'text'];
         $dta['body'][]=['Action',$subject_data->tx_type,$subject_data->tx_type,'text'];
         $dta['body'][]=['Slip No.',$subject_data->slip_no,$subject_data->slip_no,'text'];
         $dta['body'][]=['Mode',$subject_data->tx_value_type,$subject_data->tx_value_type,'text'];
         $dta['body'][]=['Channel',$subject_data->tx_channel,$subject_data->tx_channel,'text'];
         $dta['body'][]=['From.',$subject_data->source_account,$subject_data->dest_account,'text'];
         $dta['body'][]=['To.',$subject_data->source_account,$subject_data->dest_account,'text'];
    
         if($subject_data->tx_channel==="bank"){
          $dta['body'][]=['Bank Name.',$subject_data->bank_name,$subject_data->bank_name,'text'];
          $dta['body'][]=['Bank Acc No.',$subject_data->bank_acc_number,$subject_data->bank_acc_number,'text'];
          $dta['body'][]=['Bank Acc Name.',$subject_data->bank_acc_name,$subject_data->bank_acc_name,'text'];
          $dta['body'][]=['Bank Branch.',$subject_data->bank_branch,$subject_data->bank_branch,'text'];
         }
         
       }

       if($dta['relation']==="providers"){                             // Detail about center member
         $dta['body'][]=['PARAMETER NAME','PREVIUS STATUS','NEW STATUS','type'];
         $dta['body'][]=['Ref',$subject_data->currency,$subject_data->currency,'text'];
         $dta['body'][]=['Amount',$subject_data->name,$subject_data->name_edit,'text'];
         $dta['body'][]=['Mobile',$subject_data->mobile,$subject_data->mobile_edit,'text'];
         $dta['body'][]=['Address.',$subject_data->address,$subject_data->address_edit,'text'];
         $dta['body'][]=['Bank Name',$subject_data->bank_name,$subject_data->bank_name_edit,'text'];
         $dta['body'][]=['Bank Acc',$subject_data->banK_acc_number,$subject_data->banK_acc_number_edit,'text'];
         $dta['body'][]=['Acc Name.',$subject_data->banK_acc_name,$subject_data->banK_acc_name_edit,'text'];
         $dta['body'][]=['Branch.',$subject_data->bank_branch,$subject_data->bank_branch_edit,'text'];
         $dta['body'][]=['Service.',$subject_data->service_id,$subject_data->service_id_edit,'text'];
         $dta['body'][]=['Investor Acc.',$subject_data->acc_number_investor,$subject_data->acc_number_investor_edit,'text'];
         $dta['body'][]=['Interest Business.',$subject_data->interest_business,$subject_data->interest_business_edit,'text'];
         $dta['body'][]=['Interest Agent.',$subject_data->interest_agent,$subject_data->interest_agent_edit,'text'];
         $dta['body'][]=['Interest Center Head.',$subject_data->interest_center_head,$subject_data->interest_center_head_edit,'text'];
         $dta['body'][]=['Active Status.',$subject_data->active_status,$subject_data->active_status_edit,'text'];
         $dta['body'][]=['Availabel For Invest.',$subject_data->active_status_invest,$subject_data->active_status_invest_edit,'text'];
         $dta['body'][]=['Opening Time.',$subject_data->open_time,$subject_data->open_time_edit,'text'];
         $dta['body'][]=['Closing Time.',$subject_data->close_time,$subject_data->close_time_edit,'text'];
       }

       if($dta['relation']==="investment_bookings"){                             // Detail about center member
        $provider_detail=DB::table('providers')->find($subject_data->provider_id);
        $user_detail=DB::table('users')->find($subject_data->user_id);
        $acc_detail=DB::table('tx_accounts')->where('acc_number','=',$subject_data->acc_number)->first();
         $dta['body'][]=['DETAIL','STATUS',' ','type'];
         $dta['body'][]=['Provider',$provider_detail->name,$provider_detail->mobile,'text'];
         $dta['body'][]=['Provider',$provider_detail->address,$provider_detail->address,'text'];
         $dta['body'][]=['Client Name',$user_detail->name,$user_detail->mobile,'text'];
         $dta['body'][]=['Investing Acc No',$subject_data->acc_number,$subject_data->acc_number,'text'];
         $dta['body'][]=['Investing Acc Name',$acc_detail->acc_name,$acc_detail->ref_center_id,'text'];
         $dta['body'][]=['Status.',$subject_data->status,$subject_data->status,'text'];
         $dta['body'][]=['Narration',$subject_data->narration,$subject_data->narration,'text'];
         $dta['body'][]=['Amount Offered',number_format($subject_data->amount,0),number_format($subject_data->amount,0),'text'];
       }


       if($dta['relation']==="tx_account_logs"){                             // Transaction in account log
       $tx_api=DB::table('tx_api_refs')->where('tx_id','=',$subject_data->tx_id_parent)->first();
         $dta['body'][]=['DETAIL','STATUS',' ','type'];
         $dta['body'][]=['Transaction No.',$subject_data->tx_id,$subject_data->tx_id,'text'];
         $dta['body'][]=['Acc No.',$subject_data->acc_number,$subject_data->acc_number,'text'];
         $dta['body'][]=['Source Acc.',$subject_data->source_account,$subject_data->source_account,'text'];
         $dta['body'][]=['Dest Acc.',$subject_data->dest_account,$subject_data->dest_account,'text'];
         $dta['body'][]=['User ID.',$subject_data->user_id,$subject_data->user_id,'text'];
         $dta['body'][]=['Ref ID.',$subject_data->ref_id,$subject_data->ref_id,'text'];
         $dta['body'][]=['Currency.',$subject_data->currency,$subject_data->currency,'text'];
         $dta['body'][]=['Tx Type/Flow.',$subject_data->tx_type,$subject_data->tx_type,'text'];
         $dta['body'][]=['Slip No.',$subject_data->slip_no,$subject_data->slip_no,'text'];
         $dta['body'][]=['Channel.',$subject_data->tx_channel,$subject_data->tx_channel,'text'];
         $dta['body'][]=['Source Currency.',$subject_data->currency_source,$subject_data->currency_source,'text'];
         $dta['body'][]=['Dest Currency.',$subject_data->currency_destination,$subject_data->currency_destination,'text'];
         $dta['body'][]=['Tx Value Type.',$subject_data->tx_value_type,$subject_data->tx_value_type,'text'];
         $dta['body'][]=['Service Category.',$subject_data->tx_service_cat,$subject_data->tx_service_cat,'text'];
         $dta['body'][]=['Service Cat ID.',$subject_data->tx_service_cat_ID,$subject_data->tx_service_cat_ID,'text'];
         $dta['body'][]=['Tx Owner Type.',$subject_data->tx_owner_type,$subject_data->tx_owner_type,'text'];
         $dta['body'][]=['Tx Owner ID.',$subject_data->tx_owner_id,$subject_data->tx_owner_id,'text'];
         $dta['body'][]=['Tx parent.',$subject_data->tx_id_parent,$subject_data->tx_id_parent,'text'];
         $dta['body'][]=['Tx Flag.',$subject_data->tx_flag,$subject_data->tx_flag,'text'];
         $dta['body'][]=['BANK NAME',$tx_api->bank_name,$tx_api->bank_branch,'text'];
         $dta['body'][]=['BANK Acc',$tx_api->bank_acc_name,$tx_api->bank_acc_number,'text'];
         $dta['body'][]=['Amount',number_format($subject_data->cash_amount,0),number_format($subject_data->cash_amount,0),'text'];
         $dta['body'][]=['Transit Type.',$subject_data->tx_type_transit,$subject_data->tx_type_transit,'text'];
       }
         
       if($dta['relation']==="ref_centers"){                             // Detail about center
         $dta['body'][]=['DETAIL','STATUS',' ','type'];
         $dta['body'][]=['User ID.',$subject_data->user_id,$subject_data->user_id,'text'];
         $dta['body'][]=['Admin ID',$subject_data->admin_id,$subject_data->admin_id,'text'];
         $dta['body'][]=['Center Code.',$subject_data->center_code,$subject_data->center_code,'text'];
         $dta['body'][]=['Center Type.',$subject_data->center_type,$subject_data->center_type,'text'];
         $dta['body'][]=['Currency.',$subject_data->currency,$subject_data->currency,'text'];
         $dta['body'][]=['Center Name.',$subject_data->name,$subject_data->name,'text'];
         $dta['body'][]=['Mobile.',$subject_data->mobile,$subject_data->mobile,'text'];
         $dta['body'][]=['Country Code.',$subject_data->country_code,$subject_data->country_code,'text'];
         $dta['body'][]=['Address.',$subject_data->address,$subject_data->address,'text'];
         $dta['body'][]=['GPS.',$subject_data->address_gps,$subject_data->address_gps,'text'];
         $dta['body'][]=['Param 1.',$subject_data->param1,$subject_data->param1,'text'];
         $dta['body'][]=['Param 2.',$subject_data->param2,$subject_data->param2,'text'];
         $dta['body'][]=['Param 3.',$subject_data->param3,$subject_data->param3,'text'];
         $dta['body'][]=['Param 4.',$subject_data->param4,$subject_data->param4,'text'];
         $dta['body'][]=['Param 5.',$subject_data->param5,$subject_data->param5,'text'];
         $dta['body'][]=['Credit Limit.',$subject_data->credit_limit,$subject_data->credit_limit_edit,'text'];
         $dta['body'][]=['Advance Start.',$subject_data->advance_start_date,$subject_data->advance_start_date_edit,'text'];
//PAUSEED         $dta['body'][]=['Advance End.',$subject_data->advance_end_date,$subject_data->advance_end_date_edit,'text'];
         $dta['body'][]=['Advance Limit Rate.',$subject_data->advance_limit_rate,$subject_data->advance_limit_rate_edit,'text'];
         $dta['body'][]=['Interest Rate.',$subject_data->interest_rate,$subject_data->interest_rate_edit,'text'];
         
         $dta['body'][]=['Provider Acc No..',$subject_data->provider_acc_number,$subject_data->provider_acc_number_edit,'text'];
         $dta['body'][]=['Provider ID.',$subject_data->provider_id,$subject_data->provider_id_edit,'text'];
         $dta['body'][]=['Service ID.',$subject_data->service_id,$subject_data->service_id_edit,'text'];
         $dta['body'][]=['Other Detail',$subject_data->other,$subject_data->other_edit,'text'];
         $dta['body'][]=['Status.',$subject_data->active_status,$subject_data->active_status_edit,'text'];
       }
         

       if($dta['relation']==="tx_account_profit_holders"){  
         $params=null;
         $params['condition'][]=[' and ',' p.approve_ref_id','=',$approve_ref_id];
         $profit_holders=(qrymkrController::class)::profit_dependants_get($params);
         $profit_holders=$profit_holders['body'];
         $dta['body'][]=['NAME','Current',' New ',' '];
         for($g=0; $g<count($profit_holders); $g++ )
             $dta['body'][]=[$profit_holders[$g]->name_receiver, $profit_holders[$g]->rate, $profit_holders[$g]->rate_edit,'text'];
       }

       if($dta['relation']==="ref_center_request_types"){                             // Detail about center
         $dta['body'][]=['DETAIL','Current State',' New Status ',''];
         $dta['body'][]=['Name ',$subject_data->name,$subject_data->name_edit,'text'];
         $dta['body'][]=['Amount ',number_format($subject_data->amount),number_format($subject_data->amount_edit),'text'];
         $dta['body'][]=['Detail',$subject_data->description,$subject_data->description_edit,'text'];
         $dta['body'][]=['Status.',$subject_data->active_status,$subject_data->active_status_edit,'text'];
         $dta['body'][]=['Start Date.',$subject_data->start_date,$subject_data->start_date_edit,'text'];
         $dta['body'][]=['End Date.',$subject_data->end_date,$subject_data->end_date_edit,'text'];
         $dta['body'][]=['Service Type.',$subject_data->tx_service_cat,$subject_data->tx_service_cat,'text'];
         $dta['body'][]=['Interval.',$subject_data->interval,$subject_data->interval_edit,'text'];
         $dta['body'][]=['Image 1.',$subject_data->image1,$subject_data->image1_edit,'image'];
       }
         
       if($dta['relation']==="tx_accounts"){                             // Detail about center
         $dta['body'][]=['DETAIL','Current State',' New Status ',''];
         $dta['body'][]=['Name ',$subject_data->acc_name,$subject_data->acc_name,'text'];
         $dta['body'][]=['Acc Number',$subject_data->acc_number,$subject_data->acc_number,'text'];
         $dta['body'][]=['Withdraw Charge.',$subject_data->wd_charges,$subject_data->wd_charges_edit,'text'];
         $dta['body'][]=['Withdraw Max @day.',$subject_data->wd_max_day,$subject_data->wd_max_day_edit,'text'];
         $dta['body'][]=['Withdraw API Fee.',$subject_data->wd_charges,$subject_data->wd_charges_edit,'text'];
         $dta['body'][]=['Deposit Charge.',$subject_data->dp_charges,$subject_data->dp_charges_edit,'text'];
         $dta['body'][]=['Depsoit Max @day.',$subject_data->dp_max_day,$subject_data->dp_max_day_edit,'text'];
         $dta['body'][]=['Deposit API Fee.',$subject_data->dp_api_fee,$subject_data->dp_api_fee_edit,'text'];
       }
         

    return $dta;
}



//Check for access validation
public static function users_access_check($params){
   $role=DB::table('user_access')->select('role')
                                ->where('user_id','=',$params['user_id'])
                                ->where('access_table','=',$params['access_table'])
/*                                ->whereIn('role',[$params['approver_role'],'owner'])
                                ->where(function ($query,$params) use($params) {$query->where('approver_role','=',$params['approver_role'])
                                                                 ->orWhere('role', '=', 'owner');
                                                          })*/
                                ->get();
   $access['role']=$role[0]->role??null;
   $access['status']=($access['role']!=null&&($access['role']===$params['approver_role']||$access['role']==='owner') );
   return $access;
}

// entity_type approve_ref_id approve_action approve_count approver_role user_id source_table source_column source_id
public static function approval_register($params){  // Register approvals
       DB::table('approvals')->insert([ 
                                         [
                                          'created_at' => date("Y-m-d h:i:s"),
                                          'entity_type' => $params['entity_type']??"",      // either request or response
                                          'source_table' => $params['source_table']??"",
                                          'source_column'=>$params['source_column']??"",
                                          'source_id'=>$params['source_id']??"",
                                          'request'=>$params['request']??"request",     
                                          'status'=>$params['status']??"new",           // approve - reject
                                          'user_id'=>$params['user_id'],
                                          'approve_scope'=>$params['approve_scope']??"admin",   //admin user
                                          'approver_role'=>$params['approver_role']??"",
                                          'approve_count'=>$params['approve_count']??"2",
                                          'approve_action'=>$params['approve_action'],
                                          'approve_ref_id'=>$params['approve_ref_id'],
                                          'approve_narration'=>$params['approve_narration'],
                                          'ref_center_id'=>$params['ref_center_id']??""
                                          ]
                                      ]);
}


public static function provider_initiation($dta,$params){
//add account creator to access group as owner
                             self::user_access_setup($params);
//Create one non-transacting account for the provider to log taken credit and repayments
                             $currency=$dta['currency'];   // currecy of loged in user
                             $params=['created_at'=>date("Y-m-d h:i:s"),'user_id'=>$dta['user_id'], 'acc_name'=>$dta['name'], 'currency'=>$currency, 'tx_channel'=>'transit', 'tx_value_type'=>'provider', 'acc_category'=>'provider','provider_id'=>$dta['id']];
                             $account_wallet=(qrymkrController::class)::account_create($params);
                             $account_wallet=json_decode($account_wallet,true);
}

public static function center_initiation($dta,$params){
//add account creator to access group as owner
                             self::user_access_setup($params);
//Create two accounts for the group
                             $currency=$dta['currency'];   // currecy of loged in user
                             $params=['created_at'=>date("Y-m-d h:i:s"),'user_id'=>$dta['user_id'], 'acc_name'=>$dta['name'], 'currency'=>$currency, 'tx_channel'=>'wallet', 'tx_value_type'=>'wallet', 'acc_category'=>'group','ref_center_id'=>$dta['id']];
                             $account_wallet=(qrymkrController::class)::account_create($params);
                             $account_wallet=json_decode($account_wallet,true);
                             $params=['created_at'=>date("Y-m-d h:i:s"),'user_id'=>$dta['user_id'], 'acc_name'=>$dta['name'], 'currency'=>$currency, 'tx_channel'=>'cash', 'tx_value_type'=>'cash', 'acc_category'=>'group','ref_center_id'=>$dta['id']];
                             $account_cash=(qrymkrController::class)::account_create($params,true);
                             $account_cash=json_decode($account_cash,true);
//add center wallet account to be default profit dependant
                             $params2=['acc_number_giver'=>$account_wallet['acc_number'],'acc_number_receiver'=>$account_wallet['acc_number'],'rate'=>'100','active_status'=>'active'];
                             $porfit_dependants=(qrymkrController::class)::profit_dependants_create($params2);
                             
//Create default collection request into ref_center_request_types
    $request_types=DB::table('request_types')->where('request_type','=','collection')
                                             ->where('currency','=',$currency)
                                             ->first();
                   DB::table('ref_center_request_types')->insert([ 
                                         [
                                          'created_at' => date("Y-m-d h:i:s"),
                                          'user_id' => $params['user_id'],
                                          'ref_center_id'=>$dta['id'] ?? "",
                                          'tx_service_cat_ID'=>$request_types->id,
                                          'tx_service_cat'=>$request_types->request_type,
                                          'tx_type'=>$request_types->request_type,
                                          'name'=>$request_types->request_type,
                                          'tx_type'=>$request_types->request_type,
                                          'interval'=>"0"
                                         ]
                                      ]);
  return $porfit_dependants;
}

public static function user_access_setup($params,$action=null){  // add / remove user to/from the user access list
       if($action==null||$action!=="delete")
         return DB::table('user_access')->insert([ 
                                         [
                                          'created_at' => date("Y-m-d h:i:s"),
                                          'admin_id' => $params['admin_id']??"",
                                          'user_id' => $params['user_id'],
                                          'mobile'=>$params['mobile']??"",
                                          'access_ref_id_type'=>'user_id',
                                          'access_scope'=>$params['access_scope']??"",
                                          'access_ref_id'=>$params['access_ref_id']??"",
                                          'access_ref_name'=>$params['access_ref_name']??"",
                                          'access_table'=>$params['access_table']??"",
                                          'role'=>$params['role']??"member",
                                          'active_status'=>'active']
                                      ]);
       if($action!=null&&$action==="delete")
         return DB::table('user_access')->where('id','=',$dta->id)->delete();
}

public static function pick_next_schedule($from,$interval,$end_date){
    //Get the accurate starting date
        $next_date=$end_date;
        do{
            $next_date=date_create($next_date);
            date_add($next_date,date_interval_create_from_date_string($interval." days"));
            echo date_format($date,"Y-m-d H:i:s");
            $gap=date_diff($next_date,$end_date)->format("%R%a");
        }while($gap>=0 && $gap <= $interval);
    //Generate the dates after that date
    return $next_date;
}

public static function datetime_diff_open($from,$to,$units){
        $to=($to==null)?date("Y-m-d H:i:s"):$to;
        $from=($from==null)?date("Y-m-d H:i:s"):$from;
        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $to);
        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $from);
        $since_start = $from->diff($to);
        $diff['days_total']=$since_start->days;
        $diff['years']=$since_start->y;
        $diff['months']=$since_start->m;
        $diff['days']=$since_start->d;
        $diff['hours']=$since_start->h;
        $diff['minutes']=$since_start->i;
        $diff['seconds']=$since_start->s;
        $diff=(object)$diff;
        return $diff;
}

public static function date_diff_open($from,$to,$units){
        $to=($to==null)?date("Y-m-d H:s:i"):$to;
        $from=($from==null)?date("Y-m-d h:i:s"):$from;
        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $to);
        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $from);
        $diff_in_minutes = $to->diffInMinutes($from);
        if($units==="minutes")
            return $diff_in_minutes;
        else
            return $diff_in_minutes;
}

function uuid_generator($data = null)
{
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);
    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    // Output the 36 character UUID.
    return vsprintf('%s%s%s%s%s%s%s%s', str_split(bin2hex($data), 4));
}


    //reserved parameter names
    public static function skip_params(){
        return ['submit','enter','_token','update','zxy','relation','_method','confirm_password','table'];
    }
    
    //Random numeric codes generator
    public static function code_generator($count)
      {
        $str="1234567890";
        $indx0="0";
        $indxn=$count;
		return self::random_string($str,"",$indx0,$indxn);
	  }
    
    //Random string maker
    public static function random_string($str,$begin,$indx0,$indxn)
      {
		$new_st=$begin.substr(str_shuffle($str),$indx0,$indxn);
		return $new_st;
	  }



    //Automated form maker reference function
    public static function formload1($columns,$dta=null,$detail){
        $form['title1']=$detail["title1"]??"";
        $form['title2']=$detail["title2"]??"";
        $form['bottom']=$detail["bottom"]??"";
        $form['action']=$detail["action"]??"";
        $form['method']=$detail["method"]??"POST";
        
        $submit=true;
        for($j=0;$j<count($columns);$j++)
        {
         //text input
         $value=($dta!=null&&$dta->$columns[$j]['name']!=null)?$dta->$columns[$j]['name']:"";
         $form['fields'][]=['type'=>$columns[$j]['type']??"text",
                            'name'=>$columns[$j]['name']??"",
                            'placeholder'=>$columns[$j]['placeholder']??"",
                            'value'=>$value,
                            'class'=>$columns[$j]['class']??'form-control',
                            'id'=>$columns[$j]['id']??'',
                            'other'=>$columns[$j]['other']??'',
                            'options'=>$columns[$j]['options']??""
                          ];
         if($columns[$j]['name']==="id"&&$dta!=null)
         $submit=false;
        }
        $form['fields'][]=['type'=>'button', 'button_action'=>'submit', 'name'=>($submit)?'submit':'update','value'=>($submit)?'submit':'update','class'=>'btn btn-primary','id'=>'','other'=>''];
        return $form;
    }

    //Automated form maker reference function
    public static function register_item($data=null){
        $form['title1']="title1";
        $form['title2']="title2";
        $form['bottom']="bottom";
        $form['action']="account_client";
        $form['method']="POST";
        
        //text input
        $form['fields'][]=['type'=>'text','name'=>'name','placeholder'=>'Enter your name','value'=>'Denis Simbwa','class'=>'form-control','id'=>'','other'=>'required'];
        
        //textarea input
        $form['fields'][]=['type'=>'textarea','name'=>'name','placeholder'=>'Enter your name','value'=>'Denis Simbwa','class'=>'form-control','id'=>'','other'=>'required'];
        
        //number inpout
        $form['fields'][]=['type'=>'number','name'=>'name','placeholder'=>'Age','value'=>'Denis Simbwa','class'=>'form-control','id'=>'','other'=>'required','step'=>'1','min'=>'0'];
        
        //file attachment
        $form['fields'][]=['type'=>'file','name'=>'name','placeholder'=>'Enter your name','value'=>'Denis Simbwa','class'=>'form-control','id'=>'','other'=>'required'];

        //select
        $form['fields'][]=['type'=>'select','name'=>'name','placeholder'=>'Enter your name','value'=>'Denis Simbwa','class'=>'form-control','id'=>'','other'=>'required',
                           'options'=>[['value'=>1,'option'=>'ONE'],
                                       ['value'=>2,'option'=>'TWO'],
                                       ['value'=>3,'option'=>'THREE']
                                      ]
                          ];

        $form['fields'][]=['type'=>'button', 'button_action'=>'submit', 'name'=>'submit','value'=>'Enter','class'=>'btn btn-primary','id'=>'','other'=>''];
        return $form;
    }
}
