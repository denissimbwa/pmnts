<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

class webrqController extends Controller
{
    //
    public function change_password(Request $rq){
            $user_id=\Session::get('user')->id ?? $rq->header('id') ?? null;
            $access_tocken=\Session::get('user')->access_tocken ?? $rq->bearerToken() ?? null;
            $acc_number=\Session::get('user')->acc_number ?? $rq->header('acc_number') ?? "202301301051413710010";
            $mobile=\Session::get('user')->mobile ?? $rq->header('mobile') ?? "";
            $web_access=(\Session::get('user') !=null);  //check if it is an api request
        
        if($user_id==null)  //Ensure that user is loged in
          return (!$web_access) ? ['status'=>'failed','message'=>'Please log in first'] : redirect('/login');

        if($rq->input()!=null)
         {
            //validate submited data
            $validateData = $rq->validate((ValidationRules::class)::change_password());
            
            $userdetail=DB::table('users')->find($user_id);     //fetch user with corresponding ID
            
            $password_existing=\Crypt::decryptString($userdetail->password);    //get existing password
            
//PAUSED            if($password_existing !== $rq->input('password_old') );    //Confirm old password
//              return (!$web_access) ? ['status'=>'failed','message'=>'Enter the correct old password'] : back()->withInput();
//              return (!$web_access) ? ['status'=>'failed','message'=>'Enter the correct old password -'.$password_existing."=".$rq->input('password_old')."-".strcmp($password_existing,$rq->input('password_old') )] : back()->withInput();
            

            $affected=DB::table('users')->where('id','=',$user_id)->update(['password'=>\Crypt::encryptString($rq->input('password'))]);
            if(!$web_access)
                return ['status'=>'success','user'=>$userdetail];
            if($userdetail->user_rank==="client")
              return redirect('/account_client');
            else
              return redirect('/account_admin');
         }
      else
        return view('pages.change_password');
    }
    
    public function login(Request $rq){
        $web_access=($rq->filled('_token'));
        if($rq->input()!=null)
         {
        //validate submited data
            $validateData = $rq->validate((ValidationRules::class)::login());
        //fetch user with corresponding username
            $query_srvc=qrymkrController::class;    //instantiate the query data requester
            $result=$query_srvc::login_mkr($rq);

        //compare decrypted password with submitted password
            if($result['decrypted_password']!=null&&strcmp($result['decrypted_password'],$rq->input('password'))==0 )
              {
                $result['code']="200";
                $result['message']="login successful";
        //pick user's  full summary data
                $params=null;
                $params['condition'][]=[' and ','u.id','=',($result['body'][0])->id];
//                $params['condition'][]=[' and ','a.acc_category','=','user'];
                $dta=(qrymkrController::class)::get_users($params);
        $dta=$dta['body'][0];
        //create an access tocken and set it on user record
        $access_tocken=md5($dta->id.date("YmdHis"));
        DB::table('users')->where('id','=',$dta->id)->update(['access_tocken'=>$access_tocken]);

//        $dta = collect(($dta)->merge(['access_tocken'=>$access_tocken]) );  //append an access tocken to limit multiple logins of the same user
        $dta->access_tocken=$access_tocken;
//CHeck if it is API request so that we end and jsut return login user data
        if(!$rq->filled('_token'))
          return ['status'=>'success','dta'=>$dta];

        //Load user's summary data into session
                \Session::put('user',$dta);
                if(\Session::get('user')->user_rank==="client")
                  return redirect('/account_client');
                else
                  return redirect('/account_admin');
              }
            else
              {
                $result['code']="500";
                $result['message']="Incorrect username or password";
                if($web_access)
                $rq->session()->flash('error','Username or password is incorrect. Please register yourself first');
                return (!$web_access) ? ['status'=>'failed','message'=>'Incorrect mobile or password'] : back()->withInput();
              }
         }
        else
            return (!$web_access) ? ['status'=>'failed','message'=>'Incorrect mobile or password'] : view('pages.login');
    }
    

    public function signup(Request $rq,$id=null){
        if($rq->input()!=null)
         {
            $web_access=($rq->filled('_token'));
//            \Arr::except($rq, (MethodsController::class)::skip_params());    //remove off reserved parameter names
            $validateData = $rq->validate((ValidationRules::class)::signup());
            
            //Look up user
            $result = DB::table('users')
            ->where('mobile',$rq->input('mobile'))
            ->get();
            $res = json_decode($result,true);
            
            $verification_code= (MethodsController::class)::code_generator(6);
            //If user does not exists
            if(sizeof($res)==0){
                    $rq->merge(['password'=>\Crypt::encryptString($rq->input('password')) ]);   //encrypt password
                    $rq->merge(['verification_code'=>$verification_code ]);   //Generate verification code
                    $rq->merge(['user_id'=> date("Ymdhis").(MethodsController::class)::code_generator(6) ]);
                    
                    $user_signup=(qrymkrController::class)::signup_mkr($rq);     //regiser the user and return data to be used in confirm screen
                    $user_signup=json_decode($user_signup['body'],true);
                    $user_signup=(is_array($user_signup))?(object)$user_signup: $user_signup;
             
//Send SMS for verification
                             $content="Hi, ".$rq->input('name')." Your code is ".$user_signup->verification_code;
                             $from="ZHURISOFT";
                             $to=$rq->input('mobile');
                             (MethodsController::class)::send_sms_socnet($content,$from,$to);
//PAUSED                             Log::channel('slackNotification')->info('Signup Code', ['Mobile' => $to,'SMS' => $content]);
//End here if it is api request
                  if(!$web_access)
                   return ['status'=>'success','mobile'=>$user_signup->mobile,'verification_code'=>$user_signup->verification_code,'telephone_admin'=>config('api_config.telephone_admin')];
                   
                    \Session::put('user', $user_signup);
                    $rq->session()->flash('register_status','User data registered successfully, activation SMS sent to '.$rq->input('mobile')." ");
    
                    return view('pages.signup_verify',['mobile'=>$user_signup->mobile,'verification_code'=>'','telephone_admin'=>config('api_config.telephone_admin')]);
             }
            else
             {
                if(!$web_access)
                  return ['status'=>'failed','message'=>'User already exists'];

                $rq->session()->flash('register_status','User exists !' );
                  return redirect('/signup');
             }
         }
        else
         return view('pages.signup');
    }
    
    public function signup_verify(Request $rq){
        if($rq->input()!=null)
             {      
                 $web_access=($rq->filled('_token'));   //Check if it is a web access or not
                 
                    $rslt=(qrymkrController::class)::signup_verify_mkr($rq);     // call function that picks the data
                    $dta['body']=$rslt['body'];     // call function that picks the data
                    $dta['code']=$rslt['code'];
                    $dta['status']=$rslt['status'];
                    $dta['message']=$rslt['message'];
                    //return $dta;     //Forward results to a view
                    if($dta['status']==="success")
                      {
                        $rslt=$rslt['body'][0];
                        $access_tocken=md5($rslt->id.date("YmdHis"));
                        DB::table('users')->where('id','=',$rslt->id)->update(['access_tocken'=>$access_tocken]);
                        $rslt->access_tocken=$access_tocken;
                        
                        if($web_access)                 
                         \Session::put('user',$rslt);   //Load user detail into session

                        $params=null;
                        $params['condition'][]=[' and ','pr.acc_number_investor','=',$rslt->acc_number];
                        $providers=(qrymkrController::class)::providers_get($params);                 //get provider's detail
                        $providers=$providers['body'];
                        $params=null;
                        $params['condition'][]=[' and ','m.mobile','=',$rslt->mobile];
                        $centers=(qrymkrController::class)::center_members_get($params);
                        $centers=$centers['body'];
                        
                        if(!$web_access)                 // Terminate and return data if it is mobile access
                         return ['status'=>'success','user'=>$rslt,'centers'=>$centers,'providers'=>$providers];
                        
                        return view('pages.client_end.account_client',['centers'=>$centers,'providers'=>$providers]);
                      }
                    else
                      {
                        if(!$web_access)                 // Terminate and return data if it is mobile access
                         return ['status'=>'failed','message'=>'Failed to verify user'];
                        
                        return view('pages.signup_verify',['mobile'=>$rq->input('mobile'),'verification_code'=>'_____']);
                      }
             }
        else
        return view('pages.signup_verify');
    }

    public function logout(){
            Session()->flush();    
            return redirect('/');
    }


    public function users_list_admin($rq=null){
        $params=null;
//        $params['condition'][]=['','',''];
//        $params['extras'][]=' group by a.acc_number';
        $dta=(qrymkrController::class)::get_users($params);
        return view('pages.admin_end.users_list_admin',compact('dta'));
    }


    public function account_client(Request $rq){
        $id=\Session::get('user')->id ?? $rq->header('id') ?? "";
//return $rq->header();
        $access_tocken=\Session::get('user')->access_tocken ?? $rq->bearerToken() ?? null;
        $acc_number=\Session::get('user')->acc_number ?? $rq->header('accnumber') ?? "";
        $mobile=\Session::get('user')->mobile ?? $rq->header('mobile') ?? "";
        $web_access=(\Session::get('user') !=null);  //check if it is an api request
        

        $params=null;
        $params['user_id']=$id;
        $params['branch']="account_client";
        $params['access_tocken']=$access_tocken;
        $proceed=(UseraccessController::class)::access_check($params);
        if(!$proceed['valid'])
          return ($web_access) ? redirect()->route('login') : ['status'=>'expired','message'=>'User account access failure. Please try to login afresh'];

        //Create account in case user has no account
        if($acc_number==null || $acc_number==="")
         {
           $user_detail_temp=DB::table('users')->find($id);
           $currency=(MethodsController::class)::get_currency_code($user_detail_temp->country_code);
           $params=['user_id'=>$id, 'acc_name'=>$user_detail_temp->name, 'currency'=>$currency, 'tx_value_type'=>'wallet', 'acc_category'=>'user'];
           $acc_number=json_decode((qrymkrController::class)::account_create($params))->acc_number;
         }

//return $params;
        $user_id=$id;//\Session::get('user')->id ?? $rq->header('id') ?? "";
        $user_acc_number=$acc_number;
        $user_mobile=$mobile;
        (MethodsController::class)::account_sync([ 'acc_number'=>$user_acc_number]);
        $params['condition'][]=[' and ','u.id','=',$user_id];
//                $params['condition'][]=[' and ','a.acc_category','=','user'];
        $dta=(qrymkrController::class)::get_users($params);
        if($web_access)
        \Session::put('user',$dta['body'][0]);

        $params=null;
        $params['condition'][]=[' and ','pr.acc_number_investor','=',$user_acc_number];
        $providers=(qrymkrController::class)::providers_get($params);                 //get provider's detail
        $providers=$providers['body'];
        
        $params=null;
        $params['condition'][]=[' and ','m.mobile','=',$user_mobile];
        $centers=(qrymkrController::class)::center_members_get($params);
        $centers=$centers['body'];
        
        $params=null;
        $params['condition'][]=[' and ','m.owner_id','=',$id];
        $params['extras'][]=' group by m.owner_id';
        $acc_collects=(qrymkrController::class)::center_members_get($params);
        $acc_collects=$acc_collects['body'];


        $params=null;
        $params['condition'][]=[' and ','rq.user_id','=',$id];
        $params['extras'][]=" order by rq.id desc limit 50";
        $activities=(qrymkrController::class)::ref_center_requests_get($params);
        $activities=$activities['body'];

//return $acc_collects;

        $cash_in_transit=DB::table('tx_api_refs')->where('recovery_flag','=','yes')->where('tx_owner_id','=',$user_id) ->value(DB::raw("SUM(api_amount-recovered_amount)"));
        
        if($web_access)
            return view('pages.client_end.account_client',['providers'=>$providers,'centers'=>$centers,'cash_in_transit'=>$cash_in_transit, 'acc_collects'=>$acc_collects, 'activities'=>$activities ]);
        else
            return ['status'=>'success','user'=>$dta['body'][0],'providers'=>$providers,'centers'=>$centers,'cash_in_transit'=>$cash_in_transit,'acc_collects'=>$acc_collects, 'activities'=>$activities ];
    }
    public function user_profile($rq=null){
        return view('pages.general_end.user_profile');
    }
    
//Get user centeres and groups from database
    public function user_centers(Request $rq,$id=null){
             $params=null;
             $params['condition'][]=[' and ','c.user_id','=',(\Session::get('user')->id)];
             $params['condition'][]=[' and ','txa.tx_value_type','=','wallet'];
//             $params['extras'][]=' group by c.id  limit 1 ';
             $dta=(qrymkrController::class)::user_centers_get($params);
             return view('pages.client_end.user_centers',compact('dta'));
    }




    public function clients_list_admin(Request $rq,$id=null){
             $params=null;
             if($id!=null)
             $params['condition'][]=[' and ','cl.id','=',$id];
             $params['extras'][]=null;
             $dta=(qrymkrController::class)::clients_get($params);
             $dta=$dta['body'];
        return view('pages.admin_end.clients_list_admin',['dta'=>$dta]);
    }
    
    public function client_detail_get(Request $rq,$id=null){
        $web_access=(\Session::get('user') !=null);  //check if it is an api request;
        
        $access_code=$rq->header('access_code');
        
             $params=null;
//             $params['condition'][]=[' and ','cl.access_code','=', $access_code ];
             $params['condition'][]=[' and ','cl.id','=',$id];
             $params['extras'][]=null;
             $dta=(qrymkrController::class)::clients_get($params);
             $dta=$dta['body'][0];

//             Log::channel('slackNotification')->info('Client Profile', ['body'=>$dta,'request'=>json_encode($rq->input()) , 'params'=>$params] );
             
             $success=($dta==null ) ? 'failed':'success';
             if(!$web_access)
                return ['status'=>$success,'message'=>'found','data'=>$dta, 'request'=>json_encode($rq->input())  ];

        return view('pages.admin_end.clients_list_admin',['dta'=>$dta]);
    }
    
    public function client_detail_get_(Request $rq, $client_id=null ){
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $access_tocken=\Session::get('user')->access_tocken ?? $rq->bearerToken() ?? null;
        $web_access=(\Session::get('user') !=null);  //check if it is an api request;
 
        $access_code=$rq->input('access_code');
        Log::channel('slackNotification')->info('Client Profile 1', ['request'=>json_encode($rq->input())  ] );
        
             $params['condition'][]=[' and ','cl.access_code','=', $access_code ];
             $params['condition'][]=[' and ','cl.id','=',$client_id ];
             $params['extras'][]=null;
             $dta=(qrymkrController::class)::clients_get($params);
             $dta=$dta['body'];
            
    Log::channel('slackNotification')->info('Client Profile', ['body'=>$dta,'request'=>json_encode($rq->input()) , 'params'=>$params] );

            $success=(count($dta)==0 ||$dta==null ) ? 'failed':'success';
            
             if(!$web_access)
                return ['status'=>$success,['dta'=>$dta, 'request'=>json_encode($rq->input()) ] ];
        return view('pages.admin_end.clients_list_admin',['dta'=>$dta]);
    }
    
    


    public function client_tx_log(Request $rq,$client_id=null ){
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $access_tocken=\Session::get('user')->access_tocken ?? $rq->bearerToken() ?? null;
        $web_access=(\Session::get('user') !=null);  //check if it is an api request;

//Assess access control
//        $access_code=$rq->input('access_code');
        
             $params['condition'][]=[' and ','r.active_status','=','complete' ];
             $params['condition'][]=[' and ','r.client_id','=',$client_id ];
             $params['extras'][]=null;
             $dta=(qrymkrController::class)::tx_api_refs_get($params);
             $dta=$dta['body'];
            
    Log::channel('slackNotification')->info('Client Profile', ['body'=>$dta] );

            $success=(count($dta)==0 ||$dta==null ) ? 'failed':'success';
            
             if(!$web_access)
                return ['status'=>$success,['dta'=>$dta, 'request'=>json_encode($rq->input()) ] ];
        return view('pages.admin_end.clients_list_admin',['dta'=>$dta]);
    }
    
    
    

//Register or update client
    public function clients_register(Request $rq,$provider_id,$id=null){
        if($rq->input()!=null)
             {
                    $rq->merge(['table'=>"client", 'user_id'=>\Session::get('user')->id ]);
                    $dta=(qrymkrController::class)::open_insert_update($rq);     // call function that registers data
                    $client_detail=json_decode($dta['body'],true);
                    if($dta['status']==="success")
                        {
                         return redirect('clients_list_admin');
                        }
                    else
                        return $rq->back();
             }
        else{
            $client_code=\Session::get('user')->id.((MethodsController::class)::code_generator(4));
            $dta=($id!=null)?DB::table('client')->find($id):null;
            if($dta==null)
              $dta=(object)array('center_code'=>$client_code);
            else
              $dta->center_code=($dta==null)?$client_code:$dta->center_code;
            return view('pages.admin_end.clients_register',['dta'=>$dta,'client_id'=>$client_code]); //['center_code'=>$center_code,'dta'=>$dta]
        }
    }


//Register or update center or group
    public function provider_centers_register(Request $rq,$provider_id,$id=null){
        if($rq->input()!=null)
             {
                    $rq->merge(['table'=>"ref_centers", 'user_id'=>\Session::get('user')->id, 'country_code'=>\Session::get('user')->country_code]);
                    $dta=(qrymkrController::class)::open_insert_update($rq);     // call function that registers data
                    $center_detail=json_decode($dta['body'],true);
                    if($dta['status']==="success")
                        {
                        if($rq->input('submit')!=null)
                            {
                             //Initialise center for the first time 
                             //parameters for owner access creation
                             $params=['admin_id' => \Session::get('user')->id,
                                       'user_id' => \Session::get('user')->id,
                                       'mobile'=>\Session::get('user')->mobile,
                                       'role'=>'owner',
                                       'active_status'=>'active',
                                       'access_scope'=>'group',
                                       'access_ref_id'=>$center_detail['id'],
                                       'access_ref_name'=>$center_detail['name'],
                                       'access_table'=>'ref_centers'
                                     ];
                             (MethodsController::class)::center_initiation($center_detail,$params); //
                            }
                         return redirect('user_centers');
                        }
                    else
                        return $rq->back();
             }
        else{
            $center_code=\Session::get('user')->id.((MethodsController::class)::code_generator(4));
            $dta=($id!=null)?DB::table('ref_centers')->find($id):null;
            if($dta==null)
              $dta=(object)array('center_code'=>$center_code);
            else
              $dta->center_code=($dta==null)?$center_code:$dta->center_code;
            return view('pages.client_end.provider_centers_register',['dta'=>$dta,'provider_id'=>$provider_id]); //['center_code'=>$center_code,'dta'=>$dta]
        }
    }


//Register or update center or group
    public function user_centers_register(Request $rq,$id=null){
        if($rq->input()!=null)
             {
                    $rq->merge(['table'=>"ref_centers", 'user_id'=>\Session::get('user')->id, 'country_code'=>\Session::get('user')->country_code]);
                    $dta=(qrymkrController::class)::open_insert_update($rq);     // call function that registers data
                    $center_detail=json_decode($dta['body'],true);
                    if($dta['status']==="success")
                        {
                        if($rq->input('submit')!=null)
                            {
                             //Initialise center for the first time 
                             //parameters for owner access creation
                             $params=['admin_id' => \Session::get('user')->id,
                                       'user_id' => \Session::get('user')->id,
                                       'mobile'=>\Session::get('user')->mobile,
                                       'role'=>'owner',
                                       'active_status'=>'active',
                                       'access_scope'=>'group',
                                       'access_ref_id'=>$center_detail['id'],
                                       'access_ref_name'=>$center_detail['name'],
                                       'access_table'=>'ref_centers'
                                     ];
                             (MethodsController::class)::center_initiation($center_detail,$params); //
                            }
                         return redirect('user_centers');
                        }
                    else
                        return $rq->back();
             }
        else{
            $center_code=\Session::get('user')->id.((MethodsController::class)::code_generator(4));
            $dta=($id!=null)?DB::table('ref_centers')->find($id):null;
            if($dta==null)
              $dta=(object)array('center_code'=>$center_code);
            else
              $dta->center_code=($dta==null)?$center_code:$dta->center_code;
            return view('pages.client_end.user_centers_register',['dta'=>$dta]); //['center_code'=>$center_code,'dta'=>$dta]
        }
    }


    public function center_members(Request $rq,$id){
             $params=null;
             $params['condition'][]=[' and ','c.id','=',$id];
             $params['condition'][]=[' and ','m.member_id','!=',""];
             $params['extras'][]=' group by m.member_id';
             $dta=(qrymkrController::class)::center_members_get($params);
             $dta['center_id']=$id;
        return view('pages.client_end.center_members',['dta'=>$dta]);
    }
    
    public function provider_centers_member_register(Request $rq,$center_id=null,$provider_id=null,$id=null){
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $access_tocken=\Session::get('user')->access_tocken ?? $rq->bearerToken() ?? null;
        $acc_number=\Session::get('user')->acc_number ?? $rq->header('acc_number') ?? "202301301051413710010";
        $mobile=\Session::get('user')->mobile ?? $rq->header('mobile') ?? "";
        $web_access=(\Session::get('user') !=null);  //check if it is an api request
        if($rq->input()!=null)
             {
                $found_count=DB::table('ref_center_members')->where('owner_id','=',$rq->input('owner_id'))
                                                            ->where('ref_center_id','=',$rq->input('ref_center_id'))
                                                            ->where('active_status','=','active')
                                                            ->count();
                if($found_count!=null&&$found_count!=='0'&&$found_count!=0)
                   {
                    if(!$web_access)
                      return ['status'=>'failed','message'=>'User already exists for this service !'];
                    \Session::flash($rq);
                    return back()->withInput()->withErrors(['msg' => 'User already exists under this center !']);
                   }
                $rq->merge(['table'=>"ref_center_members", 'user_id'=>$user_id]);
                $dta=(qrymkrController::class)::open_insert_update($rq);     // call function that registers data
                $member_detail=json_decode($dta['body'],true);
                if($dta['status']==="success")
                  return (!$web_access) ? ['status'=>'success','dta'=>$member_detail] : redirect('center_members/'.$member_detail['ref_center_id']);
                else
                  return (!$web_access) ? ['status'=>'failed','message'=>'Memebr not registered, please cross check the data.'] :  back()->withInput();
             }
        else{
            $center_code=\Session::get('user')->id.((MethodsController::class)::code_generator(4));
            $center=($center_id!=null)?DB::table('ref_centers')->find($center_id):null;
            $bio=($id!=null)?DB::table('users')->find($id):null;
            //return ['center_id'=>$center_id,'dta'=>$dta,'frm'=>$frm];
            if(!$web_access)
             return ['status'=>'success','center_id'=>$center_id,'dta'=>$dta,'frm'=>$frm];

            return view('pages.client_end.provider_centers_member_register',['center_id'=>$center_id,'provider_id'=>$provider_id, 'bio'=>$bio,'center'=>$center]);
        }
    }
    
    public function centers_member_register(Request $rq,$center_id=null,$id=null){
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $access_tocken=\Session::get('user')->access_tocken ?? $rq->bearerToken() ?? null;
        $acc_number=\Session::get('user')->acc_number ?? $rq->header('acc_number') ?? "202301301051413710010";
        $mobile=\Session::get('user')->mobile ?? $rq->header('mobile') ?? "";
        $web_access=(\Session::get('user') !=null);  //check if it is an api request
        if($rq->input()!=null)
             {
                $found_count=DB::table('ref_center_members')->where('member_id','=',$rq->input('member_id'))
//PAUSED                                                            ->where('ref_center_id','=',$rq->input('ref_center_id'))
                                                            ->where('active_status','=','active')
                                                            ->count();
                if($found_count!=null&&$found_count!=='0'&&$found_count!=0)
                   {
                    if(!$web_access)
                      return ['status'=>'failed','message'=>'User already exists for this service !'];
                    \Session::flash($rq);
                    return back()->withInput()->withErrors(['msg' => 'User already exists under this center !']);
                   }
                $rq->merge(['table'=>"ref_center_members", 'user_id'=>$user_id]);
                $dta=(qrymkrController::class)::open_insert_update($rq);     // call function that registers data
                $member_detail=json_decode($dta['body'],true);
                if($dta['status']==="success")
                  return (!$web_access) ? ['status'=>'success','dta'=>$member_detail] : redirect('center_members/'.$member_detail['ref_center_id']);
                else
                  return (!$web_access) ? ['status'=>'failed','message'=>'Memebr not registered, please cross check the data.'] :  back()->withInput();
             }
        else{
            $center_code=\Session::get('user')->id.((MethodsController::class)::code_generator(4));
            $frm=($center_id!=null)?DB::table('ref_centers')->find($center_id):null;
            $dta=($id!=null)?DB::table('ref_center_members')->find($id):null;
            //return ['center_id'=>$center_id,'dta'=>$dta,'frm'=>$frm];
            if(!$web_access)
             return ['status'=>'success','center_id'=>$center_id,'dta'=>$dta,'frm'=>$frm];

            return view('pages.client_end.centers_member_register',['center_id'=>$center_id,'dta'=>$dta,'frm'=>$frm]);
        }
    }
    


    public function center_member_edit_status(Request $rq,$center_id,$member_id){
        if($rq->input()!=null)
             {
                 $proceed=true;
                 if(!$proceed)
                   {
                    \Session::flash($rq);
                    return back()->withInput()->withErrors(['msg' => 'Member not found !']);
                   }
                $rq->merge(['table'=>"ref_center_members", 'user_id'=>\Session::get('user')->id,'active_status'=>'pending','approve_count'=>1]);
                $dta=(qrymkrController::class)::open_insert_update($rq);     // call function that registers data
                $member_detail=json_decode($dta['body'],true);
                if($dta['status']==="success")
                  {
                   //return $rq;
                   $params=array('entity_type'=>'request',
                            'approve_ref_id'=>$rq->input('approve_ref_id'),
                            'approve_action'=>'approve',
                            'approve_narration'=>$rq->input('approve_narration'),
                            'approve_count'=>$rq->input('approve_count')??"2",
                            'approver_role'=>'admin',
                            'user_id'=>(\Session::get('user')->id),
                            'source_table'=>'ref_center_members',
                            'source_column'=>'id',
                            'source_id'=>($rq->input('id')),
                            'approve_scope'=>'user',        // user - admin
                            'ref_center_id'=>$rq->input('ref_center_id'),
                            );
                   (MethodsController::class)::approval_register($params);
                   return redirect('center_members/'.$member_detail[0]['ref_center_id']);
                  }
                else
                  return back()->withInput();
             }
        else
           {
                $dta=DB::table('ref_center_members')->where('ref_center_id','=',$center_id)
                                                    ->where('member_id','=',$member_id)
                                                    ->first();
//                return $dta." ----- ".$center_id."++++".$member_id;
                $dta->approve_ref_id=\Session::get('user')->id.((MethodsController::class)::code_generator(6));
                return view('pages.client_end.center_member_edit_status',['dta'=>$dta]);
           }
    }


//Fetch all centers for admin view
    public function user_centers_admin($provider_id=null){
             $params=null;
             $params['condition'][]=[' and ','txa.tx_value_type','=','wallet'];
             if($provider_id!=null)
             $params['condition'][]=[' and ','pr.id','=',$provider_id];
             
             $dta=(qrymkrController::class)::user_centers_get($params);
             $dta=$dta['body'];
             return view('pages.admin_end.user_centers_admin',['provider_id'=>$provider_id,'dta'=>$dta]);
    }
//Setup credit srvice line for the group or center
    public function centers_activation_admin(Request $rq,$ref_center_id){
        if($rq->input()!=null)
             {
                 $proceed=true;
                 if(!$proceed)
                   {
                    \Session::flash($rq);
                    return back()->withInput()->withErrors(['msg' => 'Error submitting data !']);
                   }
                $rq->merge(['table'=>"ref_centers", 'admin_id'=>\Session::get('user')->id]);
                $dta=(qrymkrController::class)::open_insert_update($rq);     // call function that registers data
                $member_detail=json_decode($dta['body'],true);
                if($dta['status']==="success")
                  {
                   //return $rq;
                   $params=array('entity_type'=>'request',
                            'approve_ref_id'=>$rq->input('approve_ref_id'),
                            'approve_action'=>'approve',
                            'approve_narration'=>$rq->input('approve_narration'),
                            'approve_count'=>$rq->input('approve_count')??"1",
                            'approver_role'=>'office',
                            'user_id'=>(\Session::get('user')->id),
                            'source_table'=>'ref_centers',
                            'source_column'=>'id',
                            'source_id'=>($rq->input('id')),
                            'approve_scope'=>'office',        // user - admin
                            'ref_center_id'=>$rq->input('id'),
                            );
                   (MethodsController::class)::approval_register($params);
                   return redirect('account_admin');
                  }
                else
                  return back()->withInput();
             }
        else
            {
            $params['condition'][]=[' and ','rt.active_status','=','active'];
            $dta=(qrymkrController::class)::request_types_get($params);             //get requests types
            $request_types=$dta['body']??null;

             $center=DB::table('ref_centers')->find($ref_center_id);
             $approve_ref_id=date("Ymdhis").\Session::get('user')->id.((MethodsController::class)::code_generator(6));
             return view('pages.admin_end.centers_activation_admin',['center'=>$center,'request_types'=>$request_types,'approve_ref_id'=>$approve_ref_id ]);
            }
    }


    public function approvals_admin($rq=null){
             $params=null;
             $params['condition'][]=[' and ','a.approve_status','!=','approved'];
             $params['condition'][]=[' and ','a.approve_status','!=','rejected'];
             $params['condition'][]=[' and ','a.entity_type','=','request'];
             $params['condition'][]=[' and ','a.approve_scope','!=','user'];
             $params['condition'][]=[' and ','a.approve_scope','!=','group'];
             $params['extras'][]='';
             $dta=(qrymkrController::class)::approvals_get($params);
        return view('pages.admin_end.approvals_admin',['dta'=>$dta]);
    }

    public function approvals_client($ref_center_id=null){
             $params=null;
             $params['condition'][]=[' and ','a.approve_status','!=','approved'];
             $params['condition'][]=[' and ','a.approve_status','!=','rejected'];
             $params['condition'][]=[' and ','a.ref_center_id','=',$ref_center_id];
             $params['condition'][]=[' and ','a.entity_type','=','request'];
             $params['condition'][]=[' and ','a.approve_scope','!=','admin'];
             $params['condition'][]=[' and ','a.approve_scope','!=','office'];
             $params['extras'][]='';
             $dta=(qrymkrController::class)::approvals_get($params);
//             return $dta;
        return view('pages.client_end.approvals_client',['dta'=>$dta]);
    }


    //approval register office side
    public function approvals_register_admin(Request $rq,$approve_id=null,$relation=null,$source_id=null){
        if($rq->input()!=null)
         {
          if($rq->input('approve_narration')!=null&&$rq->input('approve_status')!=null)
             {
                $rq->merge(['table'=>"approvals", 'user_id'=>\Session::get('user')->id,'approver_role'=>\Session::get('user')->user_rank ]);
                $dta=(qrymkrController::class)::open_insert_update($rq);     // call function that registers data
                $detail=json_decode($dta['body'],true);
                if($dta['status']==="success")
                  {
                    //$params=['ref_center_id'=>$detail[0]['ref_center_id'],'source_id'=>$detail[0]['source_id']];
                   $dta=(MethodsController::class)::approval_sync($detail);
//return $dta;
                   return redirect('finish_action/account_client');
                  }
                else
                  return back()->withInput();
             }
         }
        else
          { 
            //Generate new approval form preview data for user to approve
             $approval_detail=DB::table('approvals')->find($approve_id);
             $params=['relation'=>$approval_detail->source_table,'approve_ref_id'=>$approval_detail->approve_ref_id,'source_id'=>$approval_detail->source_id,'source_column'=>$approval_detail->source_column];
//             $params=['relation'=>$relation,'approve_ref_id'=>$approve_ref_id,'source_id'=>$source_id,'source_column'=>$approval_detail->source_column];
             $dta=(MethodsController::class)::approval_preview($params);
            return view('pages.general_end.approvals_register',['dta'=>$dta]);
          }
    }


    public function approvals_register_client(Request $rq,$approve_id=null,$relation=null,$source_id=null){
        //Verify that the user has rights to approve
                $proceed=true;
                $approver_role=DB::table('approvals')->select('approver_role')->where('id','=',$approve_id)->get();
                $approver_role=$approver_role[0]->approver_role;
                $params=['user_id'=>\Session::get('user')->id,'approver_role'=>$approver_role,'access_table'=>'ref_centers'];
                $access=(MethodsController::class)::users_access_check($params);
                if($access['status']==false)
                   {
                    \Session::flash($rq);
                    return back()->withInput()->withErrors(['msg' => $access['role'].' needs permission !']);
                   }
        if($rq->input()!=null)
         {
          if($rq->input('approve_narration')!=null&&$rq->input('approve_status')!=null)
             {
                $rq->merge(['table'=>"approvals", 'user_id'=>\Session::get('user')->id,'approver_role'=>$access['role']]);
                $dta=(qrymkrController::class)::open_insert_update($rq);     // call function that registers data
                $detail=json_decode($dta['body'],true);
                if($dta['status']==="success")
                  {
                    //$params=['ref_center_id'=>$detail[0]['ref_center_id'],'source_id'=>$detail[0]['source_id']];
                   $dta=(MethodsController::class)::approval_sync($detail);
//return $dta;
                   return redirect('finish_action/account_client');
                  }
                else
                  return back()->withInput();
             }
         }
        else
          { 
            //Generate new approval form preview data for user to approve
             $approval_detail=DB::table('approvals')->find($approve_id);
             $params=['relation'=>$approval_detail->source_table,'approve_ref_id'=>$approval_detail->approve_ref_id,'source_id'=>$approval_detail->source_id,'source_column'=>$approval_detail->source_column];
//             $params=['relation'=>$relation,'approve_ref_id'=>$approve_ref_id,'source_id'=>$source_id];
             $dta=(MethodsController::class)::approval_preview($params);
            return view('pages.general_end.approvals_register',['dta'=>$dta]);
          }
    }

    public function report_requests(Request $rq,$id=null){
        $params=null;
        if($id!=null)
        $params['condition'][]=[' and ','rq.provider_id','=',$id];

        $params['extras'][]=' group by substr(rq.created_at,1,10), rq.provider_id desc';
        $dta=(qrymkrController::class)::ref_center_requests_summary_get($params);
        $dta=$dta['body'];
        return view('pages.admin_end.report_requests',compact('dta'));
    }

    
    public function account_group(Request $rq,$id=null){
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $access_tocken=\Session::get('user')->access_tocken ?? $rq->bearerToken() ?? null;
        $acc_number=\Session::get('user')->acc_number ?? $rq->header('accnumber') ?? "";
        $mobile=\Session::get('user')->mobile ?? $rq->header('mobile') ?? "";
        $web_access=(\Session::get('user') !=null);  //check if it is an api request

        $params=null;
        $params['user_id']=$user_id;
        $params['branch']="account_group";
        $params['role']=null;
        $params['access_tocken']=$access_tocken;
        $params['ref_center_id']=$id;
        $proceed=(object)(UseraccessController::class)::access_check($params);
        if(!$proceed->valid)
          return (!$web_access) ? ['status'=>'failed','message'=>'User account access failure. Please try to login afresh'] : redirect()->route('login');

        $params=null;
        $params['condition'][]=[' and ','c.id','=',$id];
        $dta=(qrymkrController::class)::user_centers_get($params);
        $dta=$dta['body'][0];

        $params=null;
        $params['condition'][]=[' and ','cr.ref_center_id','=',$id];
        $services=(qrymkrController::class)::ref_center_request_types_get($params);
        $services=$services['body'];

        $params=null;
        $params['condition'][]=[' and ','rq.ref_center_id','=',$id];
        $params['extras'][]=' order by rq.created_at desc limit 5 ';
        $transactions=(qrymkrController::class)::ref_center_requests_get($params);
        $transactions=$transactions['body'];
        
        $params=null;
        $params['condition'][]=[' and ','pr.acc_number_investor','=',$dta->acc_number_wallet];
        $providers=(qrymkrController::class)::providers_get($params);                 //get provider's detail
        $providers=$providers['body'];

        $params=null;
        $params['condition'][]=[' and ','c.id','=',$id];
        $params['condition'][]=[' and ','m.member_id','!=',""];
        $params['extras'][]=' group by m.member_id';
        $members=(qrymkrController::class)::center_members_get($params);
        $members=$members['body'];

        return (!$web_access) ? ['status'=>'success','dta'=>$dta,'providers'=>$providers,'transactions'=>$transactions,'services'=>$services, 'members'=>$members,'role'=>$proceed->role] : view('pages.client_end.account_group',['dta'=>$dta,'providers'=>$providers,'transactions'=>$transactions,'services'=>$services, 'members'=>$members,'role'=>$proceed->role]);
    }


    public function finish_action($next,$message=null){
        return redirect()->route($next,['message'=>$message]);
    }

    public function account_admin($currency=null){
        $params=null;
        $params['user_id']=\Session::get('user')->id ?? null;
        $params['branch']="account_admin";
        $params['role']=null;
        $params['access_tocken']=\Session::get('user')->access_tocken ?? null;
        $proceed=(UseraccessController::class)::access_check($params);
        if(!$proceed['valid'])
          return redirect()->route('login');

        $params['currency']=$currency??"UGX";
        $dta=(qrymkrController::class)::account_admin($params);
        
        $wallet=(TxapiController::class)::relworks_wallet_balance(config('api_config.relworks_mm_acc_no'),'UGX');
        $wallet=json_decode($wallet,true);
        $wallet_balance=$wallet['balance'] ?? "N/A";

        return view('pages.admin_end.account_admin',['dta'=>$dta,'role'=>$proceed['role'],'wallet_balance'=>$wallet_balance ]);
    }
    
    public function user_accounts(Request $rq){
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $access_tocken=\Session::get('user')->access_tocken ?? $rq->bearerToken() ?? null;
        $acc_number=\Session::get('user')->acc_number ?? $rq->header('accnumber') ?? "";
        $mobile=\Session::get('user')->mobile ?? $rq->header('mobile') ?? "";
        $web_access=(\Session::get('user') !=null);  //check if it is an api request

        $params=null;
        $params['condition'][]=[' and ','ac.user_id','=',$user_id];
        $params['extras'][]=' order by ac.created_at asc ';
        $dta=(qrymkrController::class)::tx_accounts_get($params);
        return (!$web_access) ? ['status'=>'success','dta'=>$dta] : view('pages.client_end.user_accounts',compact('dta'));
    }
    public function all_user_accounts(Request $rq,$category=null,$invest=null){
        $params=null;
        if($category!=null && $category != 0)
          $params['condition'][]=[' and ','ac.acc_category','=',$category];
/*
        if($invest!=null)
          $params['condition'][]=[' and ','investment_count','>','0'];
*/        
        $params['extras'][]=' order by ac.created_at asc ';
        $dta=(qrymkrController::class)::tx_accounts_get($params);
        return view('pages.admin_end.all_user_accounts',compact('dta'));
    }
    
    // capture new payment made by agent to clear cash recoveries
    public function deposit_request_pay_banked(Request $rq){
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $access_tocken=\Session::get('user')->access_tocken ?? $rq->bearerToken() ?? null;
        $acc_number=\Session::get('user')->acc_number ?? $rq->header('accnumber') ?? "";
        $mobile=\Session::get('user')->mobile ?? $rq->header('mobile') ?? "";
        $name=\Session::get('user')->name ?? $rq->header('name') ?? "";
        $web_access=(\Session::get('user') !=null);  //check if it is an api request


         $dta['tx_id']=date("Ymdhis").(MethodsController::class)::code_generator(6)."030".$user_id;
         $dta['tx_owner_type']="office";
         $dta['tx_owner_id']=$user_id;
         $credit_account=DB::table('tx_accounts')->where('acc_category','=','admin')->where('tx_value_type','=','wallet')->where('tx_channel','=','credit')->where('active_status','=','active')->get();
         $dta['dest_account']=$credit_account[0]->acc_number;
         $dta['recovery_flag']='paid';
         $dta['ref_id']=$mobile;
         $dta['member']=$name;
         $dta['amount']=$rq->input('amount');

         if(!$web_access)
            return ['status'=>'success','dta'=>$dta];

        return view('pages.general_end.deposit_bank_confirm',compact('dta'));
    }

    // Clear borrower debt by receiving cash and bank later
    public function deposit_request_pay(Request $rq){
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $access_tocken=\Session::get('user')->access_tocken ?? $rq->bearerToken() ?? null;
        $web_access=(\Session::get('user') !=null);  //check if it is an api request

         $dta['member']=(object)['name'=>$rq->input('name')];
         $dta['tx_id']=$user_id.date("Ymdhis").(MethodsController::class)::code_generator(6)."010".$rq->input('member_id');
         $dta['tx_owner_type']="office";
         $dta['tx_owner_id']=$user_id;
         $dta['tx_channel']='transit';
         $dta['tx_service_cat']=$rq->input('request_type');
         $dta['tx_service_cat_ID']=$rq->input('service_id');
         $dta['ref_id']=$rq->input('member_id');
         $dta['amount']=$rq->input('amount');
         
         $credit_account=DB::table('tx_accounts')->where('acc_category','=','admin')->where('tx_value_type','=','wallet')->where('tx_channel','=','credit')->where('active_status','=','active')->get();
         $dta['dest_account']=$credit_account[0]->acc_number;
         if(!$web_access)
            return ['status'=>'success','dta'=>$dta];

        return view('pages.client_end.deposit_cash_confirm',compact('dta'));
    }
 
 
    // scope = center/user and the id to pick preview transaction-detail either from a center or users table 
    public function deposit_cash($scope,$scope_id,$request_service_id){
        //$scope_id=($scope_id==null)?"":$scope_id;
        //$dest_account=DB::table('tx_accounts')->find($scope_id);
        return view('pages.client_end.deposit_cash',['scope_id'=>$scope_id,'scope'=>$scope,'request_service_id'=>$request_service_id]);
    }
    public function deposit_cash_confirm(Request $rq){
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $currency=\Session::get('user')->currency ?? $rq->header('currency') ?? "";
        $access_tocken=\Session::get('user')->access_tocken ?? $rq->bearerToken() ?? null;
        $acc_number=\Session::get('user')->acc_number ?? $rq->header('accnumber') ?? "";
        $mobile=\Session::get('user')->mobile ?? $rq->header('mobile') ?? "";
        $name=\Session::get('user')->name ?? $rq->header('name') ?? "";
        $web_access=(\Session::get('user') !=null);  //check if it is an api request

       if($rq->filled('tx_id') )
         {
          $rq->merge(['user_id'=>$user_id ?? "" ,'tx_channel'=>$rq->input('tx_channel') ?? 'cash', 'currency_source'=>$currency ]);
          $dta=(qrymkrController::class)::tx_initiation($rq);
//return $dta;
          if($dta['status']==="success")
           return (!$web_access) ? ['status'=>'success','dta'=>$dta] : redirect('finish_action/account_client');
          else
           return (!$web_access) ? ['status'=>'failed','message'=>'Could not create cash deposit'] : back()->withInput();
         }
       else
        {
         //get charges involved and show user
         $dta['tx_fee']="0";
         //validate affordability and decide whether transaction proceeds or not
         $dta['proceed']="yes";
         $dta['amount']=$rq->input('amount');
         $dta['tx_owner_type']=$rq->input('scope')??"";
         $dta['member']=null;
         $dta['tx_owner_id']="";
         $dta['ref_id']="";
         
         if($rq->filled('request_service_id'))
         $dta['request_service_id']=$rq->input('request_service_id');
         
         if($rq->input('scope')==="group")
            $dta['member']=DB::table('ref_center_members')->where('mobile','=',$rq->input('mobile'))->where('country_code','=',$rq->input('country_code'))->where('ref_center_id','=',$rq->input('scope_id'))->get();
         else if($rq->input('scope')==="user")
           $dta['member']=DB::table('users')->where('mobile','=',$rq->input('mobile'))->where('country_code','=',$rq->input('country_code'))->get();
         
         if($dta['member']==null || empty($dta['member']) || count($dta['member'])==0 )
          return back()->withInput();

         $dta['ref_id']=($rq->input('scope')==="group")?$dta['member'][0]->member_id:$dta['member'][0]->id;
         $dta['tx_owner_id']=($rq->input('scope')==="group")?$dta['member'][0]->ref_center_id:$dta['member'][0]->id;

         $dta['member']=$dta['member'][0];
         $dta['tx_id']=$user_id.date("Ymdhis").(MethodsController::class)::code_generator(6)."010".$dta['member']->member_id;
         return view('pages.client_end.deposit_cash_confirm',compact('dta'));
        }
    }


    // scope = center/user and the id to pick preview transaction-detail either from a center or users table 
    public function withdraw_cash($scope,$scope_id,$request_service_id){
        return view('pages.client_end.withdraw_cash',['scope_id'=>$scope_id,'scope'=>$scope,'tx_value_type'=>'cash','request_service_id'=>$request_service_id]);
    }
    public function withdraw_cash_confirm(Request $rq){
       if($rq->input('tx_id')!=null)
         {
          $rq->merge(['user_id'=>\Session::get('user')->id ,'tx_channel'=>$rq->input('tx_channel')??'cash']);
          $dta=(qrymkrController::class)::tx_initiation($rq);
          //return $dta;
          if($dta['status']==="success")
           return redirect('finish_action/account_client');
          else
           return back()->withInput();
         }
       else
        {
         //validate affordability and decide whether transaction proceeds or not
         $dta['proceed']="yes";
         $dta['amount']=$rq->input('amount');
         $dta['tx_owner_type']=$rq->input('scope')??"";
         $dta['member']=null;
         $dta['tx_owner_id']="";
         $dta['ref_id']=$rq->input('ref_id')??"";

         if($rq->filled('request_service_id'))
         $dta['request_service_id']=$rq->input('request_service_id');


         if($rq->input('scope')==="group")
            $dta['member']=DB::table('tx_accounts')->where('acc_category','=','group')->where('tx_value_type','=',$rq->input('tx_value_type'))->where('ref_center_id','=',$rq->input('scope_id'))->get();
         else if($rq->input('scope')==="user")
            $dta['member']=DB::table('tx_accounts')->where('acc_category','=','user')->where('tx_value_type','=',$rq->input('tx_value_type'))->where('user_id','=',\Session::get('user')->id)->get();
         
         if($dta['member']==null || empty($dta['member']) || count($dta['member'])==0 )
          return back()->withInput();

         $dta['tx_owner_id']=($rq->input('scope')==="group")?$dta['member'][0]->ref_center_id:$dta['member'][0]->id;
         $dta['tx_value_type']=$rq->input('tx_value_type');
         
         $dta['source_account']=$dta['member'][0]->acc_number;
         
         //get charges involved and show user
         $dta['tx_fee']="0";
         $params=['currency'=>$dta['member'][0]->currency,'tx_value_type'=>'cash','tx_type'=>'withdraw','tx_channel'=>'cash','ref_amount'=>$dta['amount']];
         $dta['tx_fee']=((TxapiController::class)::charges_get($params) )->charge;

         $dta['member']=$dta['member'][0];
         $dta['tx_id']=\Session::get('user_id').date("Ymdhis").(MethodsController::class)::code_generator(6)."010".$dta['tx_owner_id'];
         return view('pages.client_end.withdraw_cash_confirm',compact('dta'));
        }
    }

    public function center_activities($rq=null){
        return view('pages.client_end.center_activities');
    }
    
    
    
    
    public function deposit_bank($scope,$scope_id,$acc_number=null){
        return view('pages.general_end.deposit_bank',['scope_id'=>$scope_id,'scope'=>$scope,'acc_number'=>$acc_number]);
    }
    public function deposit_bank_confirm(Request $rq){
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $access_tocken=\Session::get('user')->access_tocken ?? $rq->bearerToken() ?? null;
        $acc_number=\Session::get('user')->acc_number ?? $rq->header('accnumber') ?? "";
        $mobile=\Session::get('user')->mobile ?? $rq->header('mobile') ?? "";
        $name=\Session::get('user')->name ?? $rq->header('name') ?? "";
        $currency=\Session::get('user')->currency ?? $rq->header('currency') ?? "";
        $web_access=(\Session::get('user') !=null);  //check if it is an api request

       if($rq->filled('tx_id') )
         {
/*
           $params['user_id']=\Session::get('user')->id;
           $params['acc_number']=$rq->input('dest_account');
           $found=(MethodsController::class)::validation_account_owner($params);
           if($found==0)
           return back()->withInput();
           $params=null;
*/
          $rq->merge(['user_id'=>$user_id ,'tx_channel'=>$rq->input('tx_channel') ?? 'bank', 'currency_source'=>$currency ]);
//return $rq->input();
          $dta=(qrymkrController::class)::tx_initiation($rq);
//return $dta;
          if($dta['status']==="success")
           return (!$web_access) ? ['status'=>'success','dta'=>$dta] : redirect('finish_action/account_client');
          else
           return (!$web_access) ? ['status'=>'failed','message'=>'Failed to register bank deposit'] : back()->withInput();
         }
       else
        {
         //get charges involved and show user
         $dta['tx_fee']="0";
         //validate affordability and decide whether transaction proceeds or not
         $dta['proceed']="yes";
         $dta['amount']=$rq->input('amount');
         $dta['tx_owner_type']=$rq->input('scope')??"";
         $dta['member']=null;
         $dta['tx_owner_id']="";
         $dta['ref_id']="";
         
         
         if($rq->input('scope')==="group")
            $dta['member']=DB::table('ref_center_members')->where('mobile','=',$rq->input('mobile'))->where('country_code','=',$rq->input('country_code'))->where('ref_center_id','=',$rq->input('scope_id'))->get();
         else if($rq->input('scope')==="user")
           $dta['member']=DB::table('users')->where('mobile','=',$rq->input('mobile'))->where('country_code','=',$rq->input('country_code'))->get();
         else if($rq->input('scope')==="admin")
           $dta['member']=DB::table('users')->where('mobile','=',$rq->input('mobile'))->where('country_code','=',$rq->input('country_code'))->get();
         else if($rq->input('scope')==="provider")
           $dta['member']=DB::table('providers')->where('id','=',$rq->input('scope_id') )->where('country_code','=',$rq->input('country_code'))->get();
         
//         return [$dta,$rq->input('mobile'),$rq->input('country_code'),$rq->input('scope_id')];
         
         if($dta['member']==null || empty($dta['member']) || count($dta['member'])==0 )
          return (!$web_access) ? ['status'=>'failed','message'=>'No matches found'] : back()->withInput();

         $dta['ref_id']=($rq->input('scope')==="group")?$dta['member'][0]->member_id:$dta['member'][0]->id;
         $dta['tx_owner_id']=($rq->input('scope')==="group")?$dta['member'][0]->ref_center_id:$dta['member'][0]->id;

         if($rq->input('acc_number')!=null)             //Apend accoutn number if specified
           $dta['dest_account']=$rq->input('acc_number');

         $dta['member']=$dta['member'][0];
         $dta['tx_id']=\Session::get('user_id').date("Ymdhis").(MethodsController::class)::code_generator(6)."010".$dta['member']->id;

         return (!$web_access) ? ['status'=>'success','dta'=>$dta] : view('pages.general_end.deposit_bank_confirm',compact('dta'));
        }
    }
    
    
    public function withdraw_bank($scope,$scope_id,$acc_number=null){
        return view('pages.general_end.withdraw_bank',['scope_id'=>$scope_id,'scope'=>$scope,'tx_value_type'=>'wallet','acc_number'=>$acc_number]);
    }
    public function withdraw_bank_confirm(Request $rq){
       if($rq->input('tx_id')!=null)
         {
           $params['user_id']=\Session::get('user')->id;
           $params['acc_number']=$rq->input('source_account');
           Log::channel('slackNotification')->info('Transit Evaluation',$params );
           
           $found=(MethodsController::class)::validation_account_owner($params);
//           if(\Session::get('user')->user_rank !== "super admin" && \Session::get('user')->user_rank !== "office")
           if($found==0)
           return back()->withInput();
           $params=null;

          $rq->merge(['user_id'=>\Session::get('user')->id ,'tx_channel'=>$rq->input('tx_channel')??'wallet']);
          $dta=(qrymkrController::class)::tx_initiation($rq);
          if($dta['status']==="success")
           return redirect('finish_action/account_client');
          else
           return back()->withInput();
         }
       else
        {
         //validate affordability and decide whether transaction proceeds or not
         $dta['proceed']="yes";
         $dta['amount']=$rq->input('amount');
         $dta['tx_owner_type']=$rq->input('scope')??"";
         $dta['bank_acc_number']=$rq->input('bank_acc_number');
         $dta['bank_acc_name']=$rq->input('bank_acc_name');
         $dta['bank_name']=$rq->input('bank_name');
         $dta['bank_branch']=$rq->input('bank_branch');
         $dta['ref_id']=$rq->input('ref_id')??"";
         $dta['tx_owner_id']="";
         $dta['member']=null;


         if($rq->input('scope')==="group")
            $dta['member']=DB::table('tx_accounts')->where('acc_category','=','group')->where('tx_value_type','=',$rq->input('tx_value_type'))->where('ref_center_id','=',$rq->input('scope_id'))->get();
         else if($rq->input('scope')==="user")
            $dta['member']=DB::table('tx_accounts')->where('acc_category','=','user')->where('tx_value_type','=',$rq->input('tx_value_type'))->where('user_id','=',\Session::get('user')->id)->get();
         else if($rq->input('scope')==="admin")
            $dta['member']=DB::table('tx_accounts')->where('acc_category','=','admin')->where('tx_value_type','=',$rq->input('tx_value_type'))->where('user_id','=',\Session::get('user')->id)->get();
         else if($rq->input('scope')==="provider")
            $dta['member']=DB::table('tx_accounts')->where('acc_category','=','provider')->where('tx_value_type','=','provider')->where('provider_id','=',$rq->input('scope_id'))->get();

//return $dta;

         if($dta['member']==null || empty($dta['member']) || count($dta['member'])==0 )
          return back()->withInput();

         $dta['tx_owner_id']=($rq->input('scope')==="group")?$dta['member'][0]->ref_center_id:$dta['member'][0]->id;
         $dta['tx_value_type']=$rq->input('tx_value_type');
         
         $dta['source_account']=$dta['member'][0]->acc_number;
         
         //get charges involved and show user
         $dta['tx_fee']="0";
         $params=['currency'=>$dta['member'][0]->currency,'tx_value_type'=>'wallet','tx_type'=>'withdraw','tx_channel'=>'transit','ref_amount'=>$dta['amount']];

         $dta['tx_fee']=((TxapiController::class)::charges_get($params))->charge;
        
         if($rq->input('scope')!=="provider")
         if( ($dta['tx_fee']+$dta['amount']) > $dta['member'][0]->amount)
          return back()->withInput();
        
         if($rq->input('acc_number')!=null)             //Apend accoutn number if specified
           $dta['source_account']=$rq->input('acc_number');

         $dta['member']=$dta['member'][0];
         $dta['tx_id']=\Session::get('user_id').date("Ymdhis").(MethodsController::class)::code_generator(6)."010".$dta['tx_owner_id'];
         return view('pages.general_end.withdraw_bank_confirm',compact('dta'));
        }
    }


    public function transfer_wallet($scope_source,$scope_id_source,$acc_number_source=null,$scope_id_dest=null,$acc_number_dest=null){
        $dta['scope_source']="";
        $dta['scope_id_source']="";
        $dta['source_account']="";
        $dta['scope_dest']="";
        $dta['scope_id_dest']="";
        $dta['dest_account']="";
        return view('pages.general_end.transfer_wallet',compact('dta'));
    }
    public function transfer_wallet_confirm(Request $rq){
       if($rq->input('tx_id')!=null)
         {
           $params['user_id']=\Session::get('user')->id;
           $params['acc_number']=$rq->input('source_account');
           $found=(MethodsController::class)::validation_account_owner($params);
           if($found==0)
           return back()->withInput();
           $params=null;
          $rq->merge(['user_id'=>\Session::get('user')->id ,'tx_channel'=>$rq->input('tx_channel')??'wallet']);
          $dta=(qrymkrController::class)::tx_initiation($rq);
          //return $dta;
          if($dta['status']==="success")
           return redirect('finish_action/account_client');
          else
           return back()->withInput();
         }
       else
        {
         //validate affordability and decide whether transaction proceeds or not
         $dta['proceed']="yes";
         $dta['amount']=$rq->input('amount');
         $dta['tx_owner_type']=$rq->input('scope')??"";
         $dta['bank_acc_number']=$rq->input('bank_acc_number');
         $dta['bank_acc_name']=$rq->input('bank_acc_name');
         $dta['bank_name']=$rq->input('bank_name');
         $dta['bank_branch']=$rq->input('bank_branch');
         $dta['ref_id']=$rq->input('ref_id')??"";
         $dta['tx_owner_id']="";
         $dta['member']=null;
         $dta['source_account']=$rq->input('source_account');
         $dta['dest_account']=$rq->input('dest_account');


         $dta['member_source']=DB::table('tx_accounts')->where('acc_number','=',$dta['source_account'])->get();
         $dta['member_dest']=DB::table('tx_accounts')->where('acc_number','=',$dta['dest_account'])->get();
         $dta['tx_owner_id']=($dta['member_source'][0]->acc_category==="user" || $dta['member_source'][0]->acc_category==="admin")?$dta['member_source'][0]->user_id:$dta['member_source'][0]->ref_center_id;
         if($dta['member_source']==null || empty($dta['member_source']) || count($dta['member_source'])==0 ||$dta['member_dest']==null || empty($dta['member_dest']) || count($dta['member_dest'])==0 )
          return back()->withInput();

         //get charges involved and show user
         $dta['tx_fee']="0";
         $params=['currency'=>$dta['member_source'][0]->currency,'tx_value_type'=>$dta['member_source'][0]->tx_value_type,'tx_type'=>'withdraw','tx_channel'=>'transit','ref_amount'=>$dta['amount']];
         $dta['tx_fee']=((TxapiController::class)::charges_get($params))->charge;
        
         if( ($dta['tx_fee']+$dta['amount']) > $dta['member_source'][0]->amount)
          return back()->withInput();

         $dta['tx_id']=\Session::get('user_id').date("Ymdhis").(MethodsController::class)::code_generator(6)."010".$dta['tx_owner_id'];
         return view('pages.general_end.transfer_wallet_confirm',compact('dta'));
        }
    }

    public function investment_reload_office(Request $rq){
      if($rq->input('dest_account')!=null)
        {
         $params['condition'][]=[' and ','ac.acc_number','=',$rq->input('dest_account')];
         $booking=(qrymkrController::class)::providers_get($params);
         $booking=$booking['body'][0];

         $credit_account=DB::table('tx_accounts')->where('acc_category','=','admin')->where('tx_value_type','=',$booking->tx_value_type)->where('tx_channel','=',$booking->tx_channel)->where('active_status','=','active')->get();
         $credit_account=$credit_account[0];

         $dta['proceed']="yes";
         $dta['amount']=$rq->input('amount');
         $dta['tx_owner_type']=$booking->acc_category_inverstor;
         $dta['ref_id']=$booking->acc_number_provider;
         $dta['tx_owner_id']=($booking->acc_category==="group")?$booking->ref_center_id_inverstor:$booking->user_id_inverstor;
         $dta['tx_value_type']=$booking->tx_value_type_inverstor;
         $dta['source_account']=$credit_account->acc_number;
         $dta['dest_account']=$booking->acc_number_provider;

         $dta['tx_channel']="transit";
         $dta['tx_type']='transfer';

         $dta['member']=DB::table('tx_accounts')->where('acc_number','=',$booking->acc_number_provider)->get();
//         $dta['member']=$dta['member'][0];
//        return $dta['member'];
         if($dta['member'][0]==null || empty($dta['member'][0])  )
          return back()->withInput();

         //get charges involved and show user
         $dta['tx_fee']="0";
//PAUSE         $params=['currency'=>$booking->currency_inverstor,'tx_value_type'=>'wallet','tx_type'=>'withdraw','tx_channel'=>'transit','ref_amount'=>$dta['amount']];
//PAUSE         $dta['tx_fee']=((TxapiController::class)::charges_get($params))->charge;
         
         
//PAUSE         if( ($dta['tx_fee']+$dta['amount']) > $credit_account->amount)
//PAUSE          return back()->withInput();

         $dta['member']=$dta['member'][0];
         $dta['tx_id']=\Session::get('user_id').date("Ymdhis").(MethodsController::class)::code_generator(6)."010".$dta['tx_owner_id'];
         return view('pages.general_end.withdraw_bank_confirm',compact('dta'));
       }
    else{
         $params=null;
         $dta=(qrymkrController::class)::providers_get($params);
         $dta=$dta['body'];
         return view('pages.admin_end.investment_reload_office',compact('dta'));
       }
    }

    public function investment_booking_confirm($id){
         //validate affordability and decide whether transaction proceeds or not
         $params['condition'][]=[' and ','bk.id','=',$id];
         $params['condition'][]=[' and ','bk.status','=','selected'];
         $params['extras'][]=' order by bk.created_at desc';
         $booking=(qrymkrController::class)::providers_get($params);
         $booking=$booking['body'][0];

         
         $dta['proceed']="yes";
         $dta['amount']=$booking->amount_booking;
         $dta['tx_owner_type']=$booking->acc_category_inverstor;
         $dta['bank_acc_number']=$booking->banK_acc_number;
         $dta['bank_acc_name']=$booking->banK_acc_name;
         $dta['bank_name']=$booking->bank_name;
         $dta['bank_branch']=$booking->bank_branch;
         $dta['investment_flag']="yes";
         $dta['investment_id']=$booking->booking_id;
         $dta['ref_id']=$booking->acc_number_provider;
         $dta['tx_owner_id']=($booking->acc_category==="group")?$booking->ref_center_id_inverstor:$booking->user_id_inverstor;
         $dta['tx_value_type']=$booking->tx_value_type_inverstor;
         $dta['source_account']=$booking->acc_number_inverstor;
         $dta['dest_account']=$booking->acc_number_provider;

         $dta['tx_channel']="transit";
         $dta['tx_type']='transfer';

         $dta['member']=DB::table('tx_accounts')->where('acc_number','=',$booking->acc_number_provider)->get();
//         $dta['member']=$dta['member'][0];
         if($dta['member'][0]==null || empty($dta['member'][0])  )
          return back()->withInput();

         //get charges involved and show user
         $dta['tx_fee']="0";
         $params=['currency'=>$booking->currency_inverstor,'tx_value_type'=>'wallet','tx_type'=>'withdraw','tx_channel'=>'transit','ref_amount'=>$dta['amount']];
         $dta['tx_fee']=((TxapiController::class)::charges_get($params))->charge;
         

         if( ($dta['tx_fee']+$dta['amount']) > $booking->amount_inverstor)
          return back()->withInput();

         $dta['member']=$dta['member'][0];
         $dta['tx_id']=\Session::get('user_id').date("Ymdhis").(MethodsController::class)::code_generator(6)."010".$dta['tx_owner_id'];
         return view('pages.general_end.withdraw_bank_confirm',compact('dta'));
    }



    public function investment_list_client(Request $rq){
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $access_tocken=\Session::get('user')->access_tocken ?? $rq->bearerToken() ?? null;
        $acc_number=\Session::get('user')->acc_number ?? $rq->header('accnumber') ?? "";
        $mobile=\Session::get('user')->mobile ?? $rq->header('mobile') ?? "";
        $country_code=\Session::get('user')->country_code ?? $rq->header('countrycode') ?? "";
        $currency=\Session::get('user')->currency ?? $rq->header('currency') ?? "";
		$web_access=(\Session::get('user') !=null);

        
        $params['condition'][]=[' and ','bk.user_id_inverstor','=',$user_id];
        $dta=(qrymkrController::class)::providers_get($params);
        $dta=$dta['body']??[];
        return (!$web_access) ? ['status'=>'success','dta'=>$dta] : view('pages.client_end.investment_list_client',compact('dta'));
    }
    public function collection_accounts_available(Request $rq,$user_id=null){
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $access_tocken=\Session::get('user')->access_tocken ?? $rq->bearerToken() ?? null;
        $acc_number=\Session::get('user')->acc_number ?? $rq->header('accnumber') ?? "";
        $mobile=\Session::get('user')->mobile ?? $rq->header('mobile') ?? "";
        $country_code=\Session::get('user')->country_code ?? $rq->header('countrycode') ?? "";
        $currency=\Session::get('user')->currency ?? $rq->header('currency') ?? "";
		$web_access=(\Session::get('user') !=null);

        $params=null;
        if($user_id!=null)
           {
            $params['condition'][]=[' and ','c.provider_id',' > '," 0 "];
//            $params['condition'][]=[' and ','pr.id',' not in ',"(select cm.provider_id from ref_center_members cm where cm.owner_id='".$user_id."') "];
            $params['extras'][]=" group by c.id order by pr.name asc";
           }
//return $params;
        $dta=(qrymkrController::class)::user_centers_get($params);
        $dta=$dta['body']??[];
//        return $dta;
        return (!$web_access) ? ['status'=>'success','dta'=>$dta] : view('pages.client_end.collection_accounts_available',compact('dta'));
    }
    public function investment_available(Request $rq,$user_id_inverstor=null){
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $access_tocken=\Session::get('user')->access_tocken ?? $rq->bearerToken() ?? null;
        $acc_number=\Session::get('user')->acc_number ?? $rq->header('accnumber') ?? "";
        $mobile=\Session::get('user')->mobile ?? $rq->header('mobile') ?? "";
        $country_code=\Session::get('user')->country_code ?? $rq->header('countrycode') ?? "";
        $currency=\Session::get('user')->currency ?? $rq->header('currency') ?? "";
		$web_access=(\Session::get('user') !=null);

        
        $params['condition'][]=[' and ','pr.active_status_invest','!=','!active'];
        $params['condition'][]=[' and ','pr.active_status_invest','!=','closed'];
        if($user_id_inverstor!=null)
           {
            $params['condition'][]=[' or ','bk.user_id','=',$user_id_inverstor];
            $params['extras'][]=" group by bk.user_id";
           }
//return $params;
        $dta=(qrymkrController::class)::providers_get($params);
        $dta=$dta['body']??[];
//        return $dta;
        return (!$web_access) ? ['status'=>'success','dta'=>$dta] : view('pages.general_end.investment_available',compact('dta'));
    }
    public function investment_booking(Request $rq,$id=null,$acc_number_investor=null){
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $access_tocken=\Session::get('user')->access_tocken ?? $rq->bearerToken() ?? null;
        $acc_number=\Session::get('user')->acc_number ?? $rq->header('accnumber') ?? "";
        $mobile=\Session::get('user')->mobile ?? $rq->header('mobile') ?? "";
        $country_code=\Session::get('user')->country_code ?? $rq->header('countrycode') ?? "";
        $currency=\Session::get('user')->currency ?? $rq->header('currency') ?? "";
		$web_access=(\Session::get('user') !=null);

        if($rq->input()!=null)
             {
                    $rq->merge(['table'=>"investment_bookings", 'user_id'=>\Session::get('user')->id,'approve_narration'=>$rq->input('narration') ]);
                    $dta=(qrymkrController::class)::open_insert_update($rq);     // call function that registers data
                    $detail=json_decode($dta['body'],true);
                    $detail=(is_array($detail))?(object)$detail:$detail;
                    if($dta['status']==="success")
                        {
                        if($rq->input('submit')!=null)
                            {
                               $params=array('entity_type'=>'request',
                                        'approve_ref_id'=>$rq->input('approve_ref_id'),
                                        'approve_action'=>'approve',
                                        'approve_narration'=>$rq->input('approve_narration'),
                                        'approve_count'=>$rq->input('approve_count')??"3",
                                        'approver_role'=>'office',
                                        'user_id'=>(\Session::get('user')->id),
                                        'source_table'=>'investment_bookings',
                                        'source_column'=>'id',
                                        'source_id'=>$detail->id,
                                        'approve_scope'=>'office',        // user - admin
                                        'ref_center_id'=>'',
                                        );
                               (MethodsController::class)::approval_register($params);
                            }
                         return (!$web_access)?['status'=>'success','dta'=>$dta]:redirect('investment_available');
                        }
                    else
                        return  (!$web_access)?['failed'=>'success','message'=>'Failed to place booking']:$rq->back();
             }
        else{
            $approve_ref_id = $user_id.((MethodsController::class)::code_generator(6));
            
            $params['condition'][]=[' and ','ac.user_id','=',$user_id];
            $params['condition'][]=[' and ','ac.tx_value_type','=','wallet'];
            if($acc_number_investor!=null)
            $params['condition'][]=[' and ','ac.acc_number','=',$acc_number_investor];
            $accounts=(qrymkrController::class)::tx_accounts_get($params);
            
            $params=null;
            $params['condition'][]=[' and ','pr.id','!=','$id'];
            $provider_detail=(qrymkrController::class)::providers_get($params);
            $provider_detail=$provider_detail['body'][0];
            if (!$web_access)
              return ['status'=>'success','approve_ref_id'=>$approve_ref_id,'provider_id'=>$id,'accounts'=>$accounts,'provider'=>$provider_detail];

            return view('pages.client_end.investment_booking',['approve_ref_id'=>$approve_ref_id,'provider_id'=>$id,'accounts'=>$accounts,'provider'=>$provider_detail]);
        }
    }

    public function invitation_register_client_remove(Request $rq,$scope=null,$scope_id=null){
        if($rq->input()!=null)
             {
                    $deleted = DB::table('user_access')->where('id', '=', $rq->input('id'))->delete();
                    if($deleted > 0)
                        {
                         return redirect('invitation_list_admin');
                        }
                    else
                        return $rq->back();
             }
        else{
             return redirect('invitation_list_admin');
            }
    }
    public function invitation_register_client(Request $rq,$scope,$scope_id){
        if($rq->input()!=null)
             {
                    $access_table=($scope==="provider")?"providers":(($scope==="group")?"ref_centers":"");
                    $rq->merge(['table'=>"user_access", 'admin_id'=>\Session::get('user')->id,'access_table'=>$access_table ]);
                    $dta=(qrymkrController::class)::open_insert_update($rq);     // call function that registers data
                    $detail=json_decode($dta['body'],true);
                    $detail=(is_array($detail))?(object)$detail:$detail;
                    if($dta['status']==="success")
                        {
                        if($rq->input('submit')!=null)
                            {
                             $content="Hi, You are invited to be ".$rq->input('role')." of ".$rq->input('access_ref_name'). "Please log into your account and accept";
                             $from="ZHURISOFT";
                             $to=$rq->input('mobile');
//PAUSED                             (MethodsController::class)::send_sms_socnet($content,$from,$to);
                            }
                         return redirect('account_'.$scope.'/'.$scope_id);
                        }
                    else
                        return $rq->back();
             }
        else{
             $table=($scope==="provider")?"providers":(($scope==="group")?"ref_centers":"users");   // user option goes to invite acces to back office
             $dta=DB::table($table)->find($scope_id);
             return view('pages.client_end.invitation_register_client',['scope'=>$scope,'scope_id'=>$scope_id,'dta'=>$dta ]);
            }
    }
// show all access list for a user and register response if use send accept or reject
    public function invitation_list_client(Request $rq){
        $web_access=(\Session::get('user') !=null);
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $country_code=\Session::get('user')->country_code ?? $rq->header('country_code') ?? "256";      // undo 256
        $mobile=\Session::get('user')->mobile ?? $rq->header('mobile') ?? "";

        if($rq->filled('id'))
             {//return $rq->input();

                    $rq->merge(['table'=>"user_access", 'user_id'=>$user_id ]);
                    $dta=(qrymkrController::class)::open_insert_update($rq);     // call function that registers data
                    $detail=json_decode($dta['body'],true);
                    $detail=(is_array($detail))?(object)$detail:$detail;
                    if($dta['status']==="success")
                        {
//                         return redirect('account_provider/'.$scope_id);
                            $params=null;
                            $params['condition'][]=[' and ','ua.country_code','=',$country_code];
                            $params['condition'][]=[' and ','ua.mobile','=',$mobile];
//                            $params['condition'][]=[' and ','ua.active_status','=','active'];
                            $params['condition'][]=[' and ','ua.acceptance','!=','rejected'];
                            $params['extras'][]="order by ua.created_at desc";
                            $dta=(qrymkrController::class)::user_access_get($params);
                            $dta=$dta['body'];
                            
                            if(!$web_access)
                                return ['status'=>'success','body'=>$dta];    //return API data

                            return view('pages.client_end.invitation_list_client',compact('dta'));
                        }
                    else
                        return (!$web_access)?['status'=>'failed','message'=>'Failed to get access invites !'] : $rq->back();
             }
        else{
        $params=null;
        $params['condition'][]=[' and ','ua.country_code','=',$country_code];
        $params['condition'][]=[' and ','ua.mobile','=',$mobile];
//        $params['condition'][]=[' and ','ua.active_status','=','active'];
        $params['extras'][]="order by ua.created_at desc";
        $dta=(qrymkrController::class)::user_access_get($params);
//        return $dta;
        $dta=$dta['body'];
        if(!$web_access)
           return ['status'=>'success','body'=>$dta];
           
         return view('pages.client_end.invitation_list_client',compact('dta'));
        }
    }

    public function invitation_register_admin($rq=null){
        return view('pages.admin_end.invitation_register_admin');
    }


    public function invitation_list_admin(Request $rq=null,$admin_id=null){
        $web_access=(\Session::get('user') !=null);
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $country_code=\Session::get('user')->country_code ?? $rq->header('country_code') ?? "256";      // undo 256
        $mobile=\Session::get('user')->mobile ?? $rq->header('mobile') ?? "";

        $params=null;
        $params['condition'][]=[' and ','ua.country_code','=',$country_code];
        
        if($admin_id!=null)
          $params['condition'][]=[' and ','ua.admin_id','=',$admin_id];

        $params['extras'][]="order by ua.created_at desc";
        $dta=(qrymkrController::class)::user_access_get($params);
//        return $dta;
        $dta=$dta['body'];
        if(!$web_access)
           return ['status'=>'success','body'=>$dta];
           
        return view('pages.admin_end.invitation_list_admin',compact('dta'));
    }


    public function deposit_mm($scope=null,$scope_id=null,$request_service_id=null){
        return view('pages.general_end.deposit_mm',['scope_id'=>$scope_id,'scope'=>$scope,'request_service_id'=>$request_service_id]);
    }

 
    public function relworxreceive(Request $rq,$channel=null){
        $dta=(TxapiController::class)::relworx_receive($rq);
    }



    public function withdraw_mm_api_client(Request $rq,$client_id=null){
        $web_access=(\Session::get('user') !=null);
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $country_code=\Session::get('user')->country_code ?? $rq->header('country_code') ?? "256";      // undo 256
        $mobile=\Session::get('user')->mobile ?? $rq->header('mobile') ?? "";
       //Check sender IP and MAC 
       $access_code=\Session::get('user')->access_code ?? $rq->header('access_code') ?? "";

    $client_id=($client_id==null) ? $rq->input('client_id') : $client_id;
//       return $rq->input(); 
    Log::channel('slackNotification')->info('Withdraw Proxy', ['request'=>$rq->input()] );
    if($rq->filled('client_id'))
     {
             $params=null;
             $params['condition'][]=[' and ','cl.id','=',$client_id];
             $params['condition'][]=[' and ','cl.access_code','=',$rq->input('access_code')];
             $params['extras'][]=null;
             $client_detail=(qrymkrController::class)::clients_get($params);
             $client_detail=$client_detail['body']['0'];
       //$client=DB::table('client')->where('active_status','=','active')->where('id','=',$rq->input('client_id'))->where('access_code','=',$rq->input('access_code') )->get();
       if($client_detail !== null )
         {
          $amount=($rq->input('amount')==null) ? $client_detail->amount_total : $rq->input('amount');
          $rq->merge(['tx_type'=>'withdraw','tx_fee'=>0,'tx_channel'=>'mmoney', 'currency'=>'UGX','api_channel'=>'RELWORX','tx_cash_back'=>$client_detail->tx_cash_back,'user_id'=>$user_id,'call_back_url'=>$client_detail->call_back_url ]);
          Log::channel('slackNotification')->info('Withdrawing Proxy', ['request'=>$rq->input()] );
          $dta=(qrymkrController::class)::tx_initiation_api_client_withdraw($rq,$client_detail,$amount);
//          return $dta;
              if($dta['status']==="success")
                    return (!$web_access) ? ['status'=>$dta['mmoney']['status'],'message'=>$dta['mmoney']['message'],'dta'=>$dta['mmoney']] : redirect('withdraw_mm_api_client',$client_id );
              else
               {
                 return (!$web_access) ? ['status'=>'failed','message'=>'Failed to initialise transaction','code'=>'500'] : redirect('withdraw_mm_api_client',$client_id) ;
               } 
         }
        else
         {
           return (!$web_access) ? ['status'=>'failed','message'=>'user not found','code'=>'500'] :  redirect('withdraw_mm_api_client',$client_id) ;
         }
      }
     else{
             $params=null;
             $params['condition'][]=[' and ','cl.id','=',$client_id];
             $params['extras'][]=null;
             $client_detail=(qrymkrController::class)::clients_get($params);
             $client_detail=$client_detail['body']['0'];
      $tx_id=date("Ymdhis").(MethodsController::class)::code_generator(6);
      return (!$web_access) ? ['status'=>(($client_detail->amount_total > 0 ) ? 'success':'failed'),'message'=>(($client_detail->amount_total > 0 ) ? 'Ok':'Unable to transact now !'),'client_id'=>$client_id,'tx_id'=>$tx_id,'client_detail'=>$client_detail ] : view('pages.admin_end.withdraw_mm_api_client',[ 'client_id'=>$client_id,'tx_id'=>$tx_id,'client_detail'=>$client_detail ]);
     }
    }    
 
    public function deposit_mm_api(Request $rq){
        $web_access=(\Session::get('user') !=null);
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $country_code=\Session::get('user')->country_code ?? $rq->header('country_code') ?? "256";      // undo 256
        $mobile=\Session::get('user')->mobile ?? $rq->header('mobile') ?? "";
       //Check sender IP and MAC 
       $access_code=\Session::get('user')->access_code ?? $rq->header('access_code') ?? "";

        
//       return $rq->input();
       

       $client=DB::table('client')->where('active_status','=','active')->where('id','=',$rq->input('client_id'))->where('access_code','=',$rq->input('access_code') )->get();
       
       
//       return ['CLIENT'=>$client[0],'access_code'=>$rq->input('access_code') ];
       
       if($client !== null )
         {
          $client=$client[0];
//          $tx_id=date("Ymdhis").(MethodsController::class)::code_generator(6);
          $rq->merge(['tx_type'=>'deposit','tx_channel'=>'mmoney', 'currency'=>'UGX','api_channel'=>'RELWORX','tx_fee'=>$client->tx_fee_dp,'user_id'=>$user_id,'call_back_url'=>$client->call_back_url,'tx_cash_back'=>$client->tx_fee_wd ]);
          Log::channel('slackNotification')->info('Depositing Proxy', ['request'=>$rq->input()] );
          $dta=(qrymkrController::class)::tx_initiation_api($rq);
//          return $dta;
              if($dta['status']==="success")
                   return ['status'=>$dta['mmoney']['status'],'message'=>$dta['mmoney']['message'],'dta'=>$dta['mmoney']];
              else
               {
                 return ['status'=>'failed','message'=>'Failed to initialise transaction','code'=>'500'];
               }
         }
        else
         {
           return ['status'=>'failed','message'=>'user not found','code'=>'500'];
         }
    }    
    
    public function deposit_mm_confirm(Request $rq){
       if($rq->input('tx_id')!=null)
         {
          $rq->merge(['user_id'=>\Session::get('user')->id ?? "" ,'tx_type'=>'deposit','tx_value_type'=>'wallet','tx_channel'=>'mmoney', 'currency_source'=>\Session::get('user')->currency ]);
          $dta=(qrymkrController::class)::tx_initiation($rq);
          //return $dta;
          if($dta['status']==="success")
              if(array_key_exists('flutter',$dta) && $dta['flutter']['status']==="success")
                return redirect()->away($dta['flutter']['link']);
              else
               return redirect('account_client');
          else
           return back()->withInput();
         }
       else
        {
         //get charges involved and show user
         $dta['tx_fee']="0";
         //validate affordability and decide whether transaction proceeds or not
         $dta['proceed']="yes";
         $dta['amount']=$rq->input('amount');
         $dta['tx_owner_type']=$rq->input('scope')??"";
         $dta['member']=null;
         $dta['tx_owner_id']="";
         $dta['ref_id']="";
         
         if($rq->filled('request_service_id'))
         $dta['request_service_id']=$rq->input('request_service_id');
         
         if($rq->input('scope')==="group")
            $dta['member']=DB::table('ref_center_members')->where('mobile','=',$rq->input('mobile'))->where('country_code','=',$rq->input('country_code'))->where('ref_center_id','=',$rq->input('scope_id'))->get();
         else if($rq->input('scope')==="user")
           $dta['member']=DB::table('users')->where('mobile','=',$rq->input('mobile'))->where('country_code','=',$rq->input('country_code'))->get();
         
         if($dta['member']==null || empty($dta['member']) || count($dta['member'])==0 )
          return back()->withInput();

         $dta['ref_id']=($rq->input('scope')==="group")?$dta['member'][0]->member_id:$dta['member'][0]->id;
         $dta['tx_owner_id']=($rq->input('scope')==="group")?$dta['member'][0]->ref_center_id:$dta['member'][0]->id;

         if($rq->input('acc_number')!=null)             //Apend accoutn number if specified
           $dta['dest_account']=$rq->input('acc_number');

         $dta['member']=$dta['member'][0];
         $dta['tx_id']=\Session::get('user_id').date("Ymdhis").(MethodsController::class)::code_generator(6)."010".$dta['member']->member_id;
         return view('pages.general_end.deposit_mm_confirm',compact('dta'));
        }
    }


    public function withdraw_mm($scope=null,$scope_id=null,$request_service_id=null){
        return view('pages.general_end.withdraw_mm',['scope_id'=>$scope_id,'scope'=>$scope,'request_service_id'=>$request_service_id]);
    }
    public function withdraw_mm_confirm(Request $rq){

//return $rq->input();
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $access_tocken=\Session::get('user')->access_tocken ?? $rq->bearerToken() ?? null;
        $acc_number=\Session::get('user')->acc_number ?? $rq->header('accnumber') ?? "";
        $mobile=\Session::get('user')->mobile ?? $rq->header('mobile') ?? "";
        $currency=\Session::get('user')->currency ?? $rq->header('currency') ?? "";
        $web_access=(\Session::get('user') !=null);  //check if it is an api request

       if($rq->filled('tx_id') )
         {
          $rq->merge(['user_id'=>$user_id ?? "" ,'tx_type'=>'withdraw','tx_value_type'=>'wallet','tx_channel'=>'mmoney', 'currency_source'=>$currency ]);
          $dta=(qrymkrController::class)::tx_initiation($rq);
//return $dta;
          if($dta['status']==="success")
               return (!$web_access) ? ['status'=>'success','dta'=>$dta] : redirect('finish_action/account_client');
          else
           return (!$web_access) ? ['status'=>'failed','message'=>$dta['message'],'dta'=>$dta] : back()->withInput();
         }
       else
        {
         //get charges involved and show user
         $dta['tx_fee']="0";
         //validate affordability and decide whether transaction proceeds or not
         $dta['proceed']="yes";
         $dta['amount']=$rq->input('amount');
         $dta['tx_owner_type']=$rq->input('scope')??"user";
         $dta['member']=null;
         $dta['tx_owner_id']=$user_id;
         $dta['ref_id']="";
         
         if($rq->filled('request_service_id'))
         $dta['request_service_id']=$rq->input('request_service_id');
         
         if($rq->input('scope')==="group")
            $dta['member']=DB::table('ref_center_members')->where('mobile','=',$rq->input('mobile'))->where('country_code','=',$rq->input('country_code'))->where('ref_center_id','=',$rq->input('scope_id'))->get();
         else if($rq->input('scope')==="user")
           $dta['member']=DB::table('users')->where('mobile','=',$rq->input('mobile'))->where('country_code','=',$rq->input('country_code'))->get();
         
         if($dta['member']==null || empty($dta['member']) || count($dta['member'])==0 )
           $dta['member']=DB::table('users')->where('id','=',$dta['tx_owner_id'])->get();
         
         if($dta['member']!=null && empty($dta['member']) && count($dta['member'])!=0 )
         $dta['tx_owner_id']=($dta['tx_owner_type']==="group")?$dta['member'][0]->ref_center_id:$dta['member'][0]->id;

         $dta['ref_id']=$rq->input('mobile');

         $params=['currency'=>$currency,'tx_value_type'=>'wallet','tx_type'=>'withdraw','tx_channel'=>'mmoney','ref_amount'=>$dta['amount']];
         $dta['tx_fee']=((TxapiController::class)::charges_get($params) )->charge;


         $dta['params']=((TxapiController::class)::charges_get($params) );

         $dta['member']=$dta['member'][0];
         $dta['tx_id']=$user_id.date("Ymdhis").(MethodsController::class)::code_generator(6)."010". ($dta['member']->member_id ?? $dta['member']->id);
         return (!$web_access) ? ['status'=>'success','dta'=>$dta] : view('pages.general_end.withdraw_mm_confirm',compact('dta'));
        }
    }

    // Display campaings running
    public function campaign_listing($id=null){
        $params=null;
        $params['condition'][]=[' and ','cr.tx_service_cat','=','campaign'];
        $params['condition'][]=[' and ','cr.active_status','=','active'];
        $params['condition'][]=[' and ','cr.start_date','<=',date('Y-m-d H:i:s')];
        $params['condition'][]=[' and ','cr.end_date','>=',date('Y-m-d H:i:s')];
        if($id!=null)
          $params['condition'][]=[' and ','rq.ref_center_id','=',$id];
        $services=(qrymkrController::class)::ref_center_request_types_get($params);
        $dta=$services['body'];
        return view('pages.general_end.campaign_listing',compact('dta') );
    }
    // Display campaign detail for collection
    public function campaign_detail($id){
        $params=null;
        $params['condition'][]=[' and ','cr.id','=',$id];
        $params['condition'][]=[' and ','cr.tx_service_cat','=','campaign'];
        $params['condition'][]=[' and ','cr.active_status','!=','inactive'];
//        $params['condition'][]=[' and ','cr.start_date','<=',date('Y-m-d H:i:s')];
//        $params['condition'][]=[' and ','cr.end_date','>=',date('Y-m-d H:i:s')];
//        $params['extras'][]=' group by cr.id order by cr.id desc';
        $services=(qrymkrController::class)::ref_center_request_types_get($params);
        //$dta=(is_array($services['body']))?(object)$services['body']:$services['body'];
        $dta=$services['body'][0] ?? [];
        $tx_id=date("Ymdhis").$id.((MethodsController::class)::code_generator(6));
        
        
        $params=null;
        $params['condition'][]=[' and ','rq.ref_center_id','=',$dta->ref_center_id];
        $params['condition'][]=[' and ','rq.request_service_id','=',$dta->id];
//        $params['condition'][]=[' and ','MONTH(rq.created_at)','=',date('m')*1];
//        $params['condition'][]=[' and ','YEAR(rq.created_at)','=',date('Y')*1]; 
        $params['extras'][]=" group by rq.id order by rq.id desc ";
        $activities=(qrymkrController::class)::ref_center_requests_get($params);
        $activities=$activities['body'];

        return view('pages.general_end.campaign_detail',['dta'=>$dta,'tx_id'=>$tx_id,'id'=>$id ,'activities'=>$activities] );
    }
    
    // Collect payment for campaign online
    public function deposit_campaign(Request $rq){
        $params=null;
        $params['condition'][]=[' and ','cr.id','=',$rq->input('request_service_id')];
        $services=(qrymkrController::class)::ref_center_request_types_get($params);
        $dta=$services['body'][0];
          $rq->merge([
                     'user_id'=>\Session::get('user')->id ?? "" ,
                     'tx_channel'=>'mmoney',
                     'currency_source'=>$dta->currency,
                     'tx_owner_id'=>$dta->ref_center_id,
                     'tx_owner_type'=>'group',
                     'tx_value_type'=>'wallet',
                     'slip_no'=>$rq->input('tx_id'),
                     'tx_type'=>'deposit',
                     'user_id'=>\Session::get('user')->id ?? '',
                     'dest_account'=>$dta->acc_number_wallet
                    ]);
          $dta=(qrymkrController::class)::tx_initiation($rq);
//return $dta['flutter']['link'];
//return redirect()->away($dta['flutter']['link']);

          if($dta['status']==="success")
              if(array_key_exists('flutter',$dta) && $dta['flutter']['status']==="success")
                return redirect()->away($dta['flutter']['link']);
              else
               return redirect('campaign_detail/'.$rq->input('request_service_id'));
          else
               return back()->withInput();
    }

    public function collection_schedule_register(Request $rq,$scope_id,$service_type,$request_service_id=null){
        if($rq->input()!=null)
             {
                 $proceed=true;
                 $proceed=($service_type==="campaign" || $service_type==="collection");
                 if(!$proceed)
                   {
                    \Session::flash($rq);
                    return back()->withInput()->withErrors(['msg' => 'Error submitting data !']);
                   }
                $service_type=DB::table('request_types')->where('request_type','=',$service_type)->first();
                $rq->merge(['table'=>"ref_center_request_types",
                            'user_id'=>\Session::get('user')->id,
                            'tx_service_cat'=>$service_type->request_type,
                            'tx_service_cat_ID'=>$service_type->id
                           ]);
                if($rq->input('active_status_edit')==="inactive")
                 $rq->merge(['active_status'=>$rq->input('active_status_edit')]);
                if($service_type==="collection")
                 $rq->merge(['name'=>$service_type]);
                
                
                $dta=(qrymkrController::class)::open_insert_update($rq);     // call function that registers data
                $detail=json_decode($dta['body'],true);
                $detail=$detail[0] ?? $detail;
                $detail=(is_array($detail))?(object)$detail:$detail;
                if($dta['status']==="success")
                  {
                   //return $rq;
                   $params=array('entity_type'=>'request',
                            'approve_ref_id'=>$rq->input('approve_ref_id'),
                            'approve_action'=>'approve',
                            'approve_narration'=>$rq->input('approve_narration'),
                            'approve_count'=>$rq->input('approve_count')??"2",
                            'approver_role'=>($service_type==="campaign")?'office':"admin",
                            'user_id'=>(\Session::get('user')->id),
                            'source_table'=>'ref_center_request_types',
                            'source_column'=>'id',
                            'source_id'=>$detail->id,
                            'approve_scope'=>($service_type==="campaign")?'office':"admin",        // user - admin
                            'ref_center_id'=>$detail->ref_center_id,
                            );
                   (MethodsController::class)::approval_register($params);
                   return redirect('account_group/'.$detail->ref_center_id);
                  }
                else
                  return back()->withInput();
             }
        else
            {
             if($service_type!=="campaign" && $service_type!=="collection")
               return back()->withInput();

             $dta['scope_id']=$scope_id;
             $dta['service_type']=$service_type;
             $dta['approve_ref_id']=date("Ymdhis").\Session::get('user')->id.((MethodsController::class)::code_generator(6));
             if($request_service_id!=null){
               $dta['id']=$request_service_id;
               $campaign=DB::table('ref_center_request_types')->find($request_service_id);
               return view('pages.client_end.collection_schedule_register',['dta'=>$dta,'campaign'=>$campaign]);
              }
             return view('pages.client_end.collection_schedule_register',['dta'=>$dta]);
            }
    }

    public function collection_shedule_list($rq=null){
        return view('pages.client_end.collection_shedule_list');
    }

    public function investment_summary_client($rq=null){
        return view('pages.client_end.investment_summary_client');
    }
    public function transaction_log_client(Request $rq,$acc_number=null){
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $access_tocken=\Session::get('user')->access_tocken ?? $rq->bearerToken() ?? null;
        $acc_number_client=\Session::get('user')->acc_number ?? $rq->header('accnumber') ?? "";
        $mobile=\Session::get('user')->mobile ?? $rq->header('mobile') ?? "";
        $country_code=\Session::get('user')->country_code ?? $rq->header('countrycode') ?? "";
        $currency=\Session::get('user')->currency ?? $rq->header('currency') ?? "";
		$web_access=(\Session::get('user') !=null);

        $params=null;
        $account_detail=null;
        if($acc_number!=null)
          {
           $params['condition'][]=[' and ','al.acc_number','=',$acc_number];
           $params['extras'][]=' order by al.created_at desc';
           $account_detail=DB::table('tx_accounts')->where('acc_number','=',$acc_number)->get();
           $account_detail=$account_detail[0];
          }
        $dta=(qrymkrController::class)::tx_account_logs_get($params);
        $dta=$dta['body'];
        
        if(!$web_access)
           return ['status'=>'success','dta'=>$dta,'account_detail'=>$account_detail];
        return view('pages.client_end.transaction_log_client',['dta'=>$dta,'account_detail'=>$account_detail]);
    }
    
    
    public function provider_list_admin($service_id=null){
        if($service_id!=null)
        $params['condition'][]=[' and ','service_id','=',$service_id];
        $dta=(qrymkrController::class)::providers_get($params??null);
        $dta=$dta['body']??[];
        return view('pages.admin_end.provider_list_admin',compact('dta'));
    }
    public function provider_register(Request $rq){
        if($rq->input()!=null)
             {
                    $rq->merge(['table'=>"providers", 'user_id'=>\Session::get('user')->id,'country_code'=>\Session::get('user')->country_code ]);
                    $dta=(qrymkrController::class)::open_insert_update($rq);     // call function that registers data
                    $detail=json_decode($dta['body'],true);
                    if($dta['status']==="success")
                        {
                        if($rq->input('submit')!=null)
                            {
                             //Initialise center for the first time 
                             //parameters for owner access creation
                             $params=['admin_id' => \Session::get('user')->id,
                                       'user_id' => \Session::get('user')->id,
                                       'mobile'=>\Session::get('user')->mobile,
                                       'role'=>'owner',
                                       'active_status'=>'active',
                                       'access_scope'=>'provider',
                                       'access_ref_name'=>$detail['name'],
                                       'access_table'=>'providers'
                                     ];
                             (MethodsController::class)::provider_initiation($detail,$params); //
                            }
                         return redirect('provider_list_admin');
                        }
                    else
                        return $rq->back();
             }
        else{
            return view('pages.admin_end.provider_register');
        }
    }

    public function account_provider(Request $rq, $id){
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $access_tocken=\Session::get('user')->access_tocken ?? $rq->bearerToken() ?? null;
        $acc_number=\Session::get('user')->acc_number ?? $rq->header('accnumber') ?? "";
        $mobile=\Session::get('user')->mobile ?? $rq->header('mobile') ?? "";
        $web_access=(\Session::get('user') !=null);  //check if it is an api request

        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        
        $params['condition'][]=[' and ','pr.id','=',$id];
        $dta=(qrymkrController::class)::providers_get($params);
        $dta=$dta['body'][0]??null;

        (MethodsController::class)::account_sync([ 'acc_number'=>$dta->acc_number_provider ]);
        
        $params=null;
        $params['condition'][]=[' and ','rq.provider_id','=',$id];

        if(!$web_access)
            $params['condition'][]=[' and ','rq.balance','>','0'];

        $params['extras'][]=" order by rq.id desc limit 2000";
        $activities=(qrymkrController::class)::ref_center_requests_get($params);
        $activities=$activities['body'];

        $cash_in_transit=DB::table('tx_api_refs')->where('recovery_flag','=','yes')->where('tx_owner_id','=',$user_id)->value(DB::raw("SUM(api_amount-recovered_amount)"));

        if(!$web_access)
            return ['status'=>'success','dta'=>$dta,'activities'=>$activities, 'cash_in_transit'=>$cash_in_transit];

        return view('pages.client_end.account_provider',['dta'=>$dta,'activities'=>$activities, 'cash_in_transit'=>$cash_in_transit]);
    }
    
    public function provider_services_selection(Request $rq,$id){
        if($rq->input()!=null)
             {
            \Arr::except($rq,['acc_number_investor_confirm']);    //remove off reserved parameter names                    $rq->merge(['table'=>"providers", 'user_id'=>\Session::get('user')->id]);
                    $rq->merge(['table'=>"providers", 'user_id'=>\Session::get('user')->id]);
                    $dta=(qrymkrController::class)::open_insert_update($rq);     // call function that registers data
                    $detail=json_decode($dta['body'],true);
                    if($dta['status']==="success")
                        {
                           $params=array('entity_type'=>'request',
                                    'approve_ref_id'=>$rq->input('approve_ref_id'),
                                    'approve_action'=>'approve',
                                    'approve_narration'=>$rq->input('approve_narration'),
                                    'approve_count'=>$rq->input('approve_count')??"2",
                                    'approver_role'=>'office',
                                    'user_id'=>(\Session::get('user')->id),
                                    'source_table'=>'providers',
                                    'source_column'=>'id',
                                    'source_id'=>($rq->input('id')),
                                    'approve_scope'=>'office',        // user - admin
                                    'ref_center_id'=>'',
                                    );
                           (MethodsController::class)::approval_register($params);
                         return redirect('provider_list_admin');
                        }
                    else
                        return $rq->back();
             }
        else
           {
            $params['condition'][]=[' and ','rt.active_status','=','active'];
            $dta=(qrymkrController::class)::request_types_get($params);             //get requests types
            $request_types=$dta['body']??null;
    
            $params=null;
            $params['condition'][]=[' and ','pr.id','=',$id];
            $dta=(qrymkrController::class)::providers_get($params);                 //get provider's detail
            $dta=$dta['body'][0]??null;
            $approve_ref_id=date("Ymdhis").\Session::get('user')->id.((MethodsController::class)::code_generator(6));
            return view('pages.admin_end.provider_services_selection',['dta'=>$dta,'request_types'=>$request_types,'approve_ref_id'=>$approve_ref_id]);
           }
    }
    public function provider_services($rq=null){
        $dta=(qrymkrController::class)::request_types_get();
        $dta=$dta['body'];
        return view('pages.admin_end.provider_services',compact('dta'));
    }



    public function provider_services_register($rq=null){
        return view('pages.admin_end.provider_services_register');
    }
    public function profit_dependants(Request $rq,$acc_number=null){
        $user_id=\Session::get('user')->id;
        $user_rank=\Session::get('user')->user_rank;
        if($rq->input()!=null)
         {
          if($rq->filled('search') )
             {
                $accoounts=null;
                $params=null;
                $params['condition'][]=[' and ','ac.tx_value_type','=','wallet'];
                $params['condition'][]=[' and ','u.country_code','=',$rq->input('country_code')];
                $params['condition'][]=[' and ','u.mobile','=',$rq->input('search')];
                $params['condition'][]=[' or ','ac.acc_number','=',$rq->input('search')];
                $account_suggestions=(qrymkrController::class)::tx_accounts_get($params);

                $params=null;
                $approve_ref_id=date("Ymdhis").$user_id.((MethodsController::class)::code_generator(6));
                $params['condition'][]=[' and ',' p.acc_number_giver','=',$acc_number];
                $dta=(qrymkrController::class)::profit_dependants_get($params);
                $dta=$dta['body'];
                $acc_detail=DB::table('tx_accounts')->where('acc_number','=',$acc_number)->first();
                return view('pages.client_end.profit_dependants',['acc_number_giver'=>$acc_number,'dta'=>$dta, 'approve_ref_id'=>$approve_ref_id,'account_suggestions'=>$account_suggestions,'acc_detail'=>$acc_detail ]);
             }

          else if($rq->filled('rate_edit') && $rq->filled('update'))
             {
                 // Vlaideate profit distribution
                $valid_profit=(MethodsController::class)::validation_profit_alocation($rq->rate_edit);                 
                if(!$valid_profit)
                  return back()->withInput();
                  
                //Update each value of profit dependant and submit for approval
                $rq->merge(['user_id'=>$user_id ]);
                $dta=(qrymkrController::class)::profit_dependants_update($rq);
                if($dta['status']==="success" )
                   return redirect('finish_action/account_client');
                else
                  return back()->withInput();
             }
            else
             {
                $user_id=\Session::get('user')->id;
                $rq->merge(['table'=>"tx_account_profit_holders", 'user_id'=>$user_id,'active_status'=>'active','rate'=>'0' ]);
                $dta=(qrymkrController::class)::open_insert_update($rq);     // call function that registers data
                return redirect('finish_action/account_client');
             }
         }
        else
          {
            if($acc_number==null && $user_rank==="client")    // Client side must specify account number to list
              return back()->withInput();
            //Confirm ownership of account number
            if($acc_number != null && $user_rank==="client")
             {
              $found=DB::table('tx_accounts')->where('user_id','=',$user_id )->count();
              if($found < 1)
                return back()->withInput();
             }
            
            $approve_ref_id=date("Ymdhis").$user_id.((MethodsController::class)::code_generator(6));
            $params['condition'][]=[' and ',' p.acc_number_giver','=',$acc_number];
            $dta=(qrymkrController::class)::profit_dependants_get($params);
            $dta=$dta['body'];
            $acc_detail=DB::table('tx_accounts')->where('acc_number','=',$acc_number)->first();
            return view('pages.client_end.profit_dependants',['acc_number_giver'=>$acc_number,'dta'=>$dta, 'approve_ref_id'=>$approve_ref_id,'acc_detail'=>$acc_detail ]);
          }
    }
    


    public function request_deposit_client_confirm(Request $rq){
        $web_access=(\Session::get('user') !=null);
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $access_tocken=\Session::get('user')->access_tocken ?? $rq->bearerToken() ?? null;
        $acc_number=\Session::get('user')->acc_number ?? $rq->header('accnumber') ?? "";
        $mobile=\Session::get('user')->mobile ?? $rq->header('mobile') ?? "";
        $country_code=\Session::get('user')->country_code ?? $rq->header('countrycode') ?? "";
        $currency=\Session::get('user')->currency ?? $rq->header('currency') ?? "";

        if( $rq->filled('tx_id') )
             {
                    $rq->merge(['user_id'=>$user_id ,'country_code'=>$country_code,'request_type'=>'release','acc_number'=>$acc_number,'tx_channel'=>'mmoney','tx_type'=>'deposit','slip_no'=>$rq->input('tx_id') ]);

                    Log::channel('slackNotification')->info('Confirming Deposit', ['request'=>$rq->input()] );

                    $dta=(qrymkrController::class)::request_deposit_initiation($rq);     // call function that handles requests submission
                    $detail=$dta['body'];
                    
                    if($dta['status']==="success")
                         {
                          return (!$web_access) ? ['status'=>'success', 'body'=>'Transaction Initiated succesfully !'] : redirect('account_client');
                         }
                    else
                        return (!$web_access) ? ['status'=>'failed', 'message'=>'Could not initiate release'] : back()->withInput();
             }
        else
            return (!$web_access) ? ['status'=>'failed', 'message'=>'Could not initiate release'] : back()->withInput();
       }
    
    public function request_deposit_client(Request $rq,$provider_id=null){
        $web_access=(\Session::get('user') !=null);
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $access_tocken=\Session::get('user')->access_tocken ?? $rq->bearerToken() ?? null;
        $acc_number=\Session::get('user')->acc_number ?? $rq->header('accnumber') ?? "";
        $mobile=\Session::get('user')->mobile ?? $rq->header('mobile') ?? "";
        $country_code=\Session::get('user')->country_code ?? $rq->header('countrycode') ?? "";
        $currency=\Session::get('user')->currency ?? $rq->header('currency') ?? "";
/*HALT
        $params=null;
        $params['user_id']=$user_id;
        $params['branch']="request_deposit_client";
        $params['access_tocken']=$access_tocken;
        $params['provider_id']=$rq->input('provider_id');
        $proceed=(UseraccessController::class)::access_check($params);
        if(!$proceed['valid'])
          return ($web_access) ? redirect()->route('login') : ['status'=>'expired','message'=>'User account access failure. Please try to login afresh'];
*/
        
        
        $params['condition'][]=[' and ','pr.id','=',$provider_id ];
        $providers=(qrymkrController::class)::providers_get($params);                 //get provider's detail
        $provider=$providers['body'][0];

//Check for the time of operation
//        return date("Y-m-d H:i:s")." >> ".date("Y-m-d")." ".$provider->open_time.":00";
        $time_from_open=(MethodsController::class)::datetime_diff_open(date("Y-m-d")." ".$provider->open_time.":00",date("Y-m-d H:i:s"),"minutes");
        $time_from_end=(MethodsController::class)::datetime_diff_open(date("Y-m-d")." ".$provider->close_time.":00",date("Y-m-d H:i:s"),"minutes");
        $range=(MethodsController::class)::datetime_diff_open(date("Y-m-d")." ".$provider->open_time.":00",date("Y-m-d")." ".$provider->close_time.":00","minutes");
//HALT        if( ($time_from_open->hours + $time_from_end->hours) >= $range->hours )
//            return ['status'=>'failed','message'=>"Out of working hours "];

//check credit available
        if($provider->amount < $provider->business_limit ?? 0 )
            return ['status'=>'failed','message'=>"Sorry ! Please wait to deposit, and try again later ! "];
        
        $tx_id=$user_id.date("Ymdhis").(MethodsController::class)::code_generator(6)."1001".$user_id."1001".$provider->id;
        
        Log::channel('slackNotification')->info('Initiating Deposit', ['provider'=>$provider,'tx_id'=>$tx_id ]);
        
        if(!$web_access)
            return ['status'=>'success','provider'=>$provider,'tx_id'=>$tx_id];

        return view('pages.client_end.request_deposit_client',['provider'=>$provider,'tx_id'=>$tx_id ]);
    }
    

    
    public function request_credit_client(Request $rq){
        $web_access=(\Session::get('user') !=null);
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $access_tocken=\Session::get('user')->access_tocken ?? $rq->bearerToken() ?? null;
        $acc_number=\Session::get('user')->acc_number ?? $rq->header('accnumber') ?? "";
        $mobile=\Session::get('user')->mobile ?? $rq->header('mobile') ?? "";
        $country_code=\Session::get('user')->country_code ?? $rq->header('countrycode') ?? "";
        $currency=\Session::get('user')->currency ?? $rq->header('currency') ?? "";

        $params=null;
        $params['user_id']=$user_id;
        $params['branch']="request_credit_client";
        $params['access_tocken']=$access_tocken;
        $params['provider_id']=$rq->input('provider_id');
        $proceed=(UseraccessController::class)::access_check($params);
        if(!$proceed['valid'])
          return ($web_access) ? redirect()->route('login') : ['status'=>'expired','message'=>'User account access failure. Please try to login afresh'];

        
        
        $params['condition'][]=[' and ','pr.id','=',$rq->input('provider_id')];
        $providers=(qrymkrController::class)::providers_get($params);                 //get provider's detail
        $provider=$providers['body'][0];

//Check for the time of operation
//        return date("Y-m-d H:i:s")." >> ".date("Y-m-d")." ".$provider->open_time.":00";
        $time_from_open=(MethodsController::class)::datetime_diff_open(date("Y-m-d")." ".$provider->open_time.":00",date("Y-m-d H:i:s"),"minutes");
        $time_from_end=(MethodsController::class)::datetime_diff_open(date("Y-m-d")." ".$provider->close_time.":00",date("Y-m-d H:i:s"),"minutes");
        $range=(MethodsController::class)::datetime_diff_open(date("Y-m-d")." ".$provider->open_time.":00",date("Y-m-d")." ".$provider->close_time.":00","minutes");
        if( ($time_from_open->hours + $time_from_end->hours) >= $range->hours )
            return ['status'=>'failed','message'=>"Out of working hours "];

//check credit available
        if($provider->amount < $provider->business_limit ?? 0 )
            return ['status'=>'failed','message'=>"Please wait we first load more float (".$provider->amount." bal)"];
        
        if(!$web_access)
            return ['status'=>'success','provider'=>$provider];

        return view('pages.client_end.request_credit_client',['provider'=>$provider]);
    }
    
    public function request_credit_client_confirm(Request $rq){
        $web_access=(\Session::get('user') !=null);
        $user_id=\Session::get('user')->id ?? $rq->header('id') ?? "";
        $access_tocken=\Session::get('user')->access_tocken ?? $rq->bearerToken() ?? null;
        $acc_number=\Session::get('user')->acc_number ?? $rq->header('accnumber') ?? "";
        $mobile=\Session::get('user')->mobile ?? $rq->header('mobile') ?? "";
        $country_code=\Session::get('user')->country_code ?? $rq->header('countrycode') ?? "";
        $currency=\Session::get('user')->currency ?? $rq->header('currency') ?? "";
        

        if($rq->filled('tx_id') )
             {
                 //validate tx_id. Check if tx_ID exists and resend that old code else send the newly generated request code
                 $detail=DB::table('ref_center_requests')->where('tx_id','=',$rq->input('tx_id'))->get();
                 $request_code=($rq->input('request_code')==null)?(MethodsController::class)::code_generator(4):$rq->input('request_code');
                 $tx_exists=($detail != null && !empty($detail) && count($detail)>0 );
                 
                 $request_code=($tx_exists)?$detail[0]->request_code:$request_code;
                        if($rq->input('request_code')==null)
                            {
                             $content="Code for ".$rq->input('detail2')."( ".$rq->input('member_id')." - ".number_format($rq->input('requested_amount'))." ) is ".$request_code;
                             $to=(MethodsController::class)::mobile_phone_maker(['mobile'=>$rq->input('detail1'),'currency'=>$currency]);
                             //Share SMS to back office pannel
                             Log::channel('slackNotification')->info('Credit Given '.$request_code, ['Mobile' => $to,'SMS' => $content]);
                             //send code to client via SMS 
//HALT SMS                             (MethodsController::class)::send_sms_socnet($content,null,$to);
                            }
                    // if the transaction already exists end at resending code and not create another entry
                    if($tx_exists)
                      {
                          $detail=$detail[0];
                          if(!$web_access)
                            return ['id'=>$detail->id,'request_code'=>$request_code,'unpaid'=>0,'member_id'=>$rq->input('member_id'),'offered_amount'=>$rq->input('requested_amount')];

                          return view('pages.client_end.request_credit_client_confirm',['id'=>$detail->id,'request_code'=>$request_code,'unpaid'=>0,'member_id'=>$rq->input('member_id'),'offered_amount'=>$rq->input('requested_amount')]);
                      }

                    $rq->merge(['country_code'=>$country_code, 'user_id'=>$user_id,'request_code'=>$request_code,'currency'=>$currency ]);
                    $dta=(qrymkrController::class)::request_initiation($rq);     // call function that handles requests submission
                    $detail=$dta['body'];

                    if($dta['status']==="success")
                        {
                         if(!$web_access)
                         return ['status'=>'success','id'=>$detail->id,'request_code'=>$request_code,'unpaid'=>0,'member_id'=>$rq->input('member_id'),'offered_amount'=>$rq->input('requested_amount')];

                         return view('pages.client_end.request_credit_client_confirm',['id'=>$detail->id,'request_code'=>$request_code,'unpaid'=>0,'member_id'=>$rq->input('member_id'),'offered_amount'=>$rq->input('requested_amount')]);
                        }
                    else
                        return (!$web_access) ? ['status'=>'failed', 'message'=>'Could not initiate credit'] : back()->withInput();
             }
        else if( $rq->filled('request_code') )
             {
                    //$detail=DB::table('ref_center_requests')->where('request_code','=',$rq->input('request_code'))->where('id','=',$rq->input('id'))->get();
                    //Append admin ID
                    $rq->merge(['request_code'=>$rq->input('request_code'),'admin_id'=>$user_id ]);
                    $dta=(qrymkrController::class)::request_initiation($rq);     // call function that handles requests submission
                    $detail=$dta['body'];
                    
                    if($dta['status']==="success")
                         return (!$web_access) ? ['status'=>'success', 'body'=>'Transaction Initiated succesfully '] : redirect('account_client');
                    else
                        return (!$web_access) ? ['status'=>'failed', 'message'=>'Could not initiate release'] : back()->withInput();
             }
        else
           {
            $action="give";
            $unpaid=DB::table('ref_center_requests')->where('country_code','=',$country_code)->where('request_type_id','=',$rq->input('service_id'))->where('member_id','=',$rq->input('member_id'))->sum('balance');
            $action=($unpaid>0)?"pay":$action;
            $params['condition'][]=[' and ','c.country_code','=',$country_code];
            $params['condition'][]=[' and ','m.member_id','=',$rq->input('member_id')];
            $params['condition'][]=[' and ','m.active_status','=','active'];
//            $params['condition'][]=[' and ','c.active_status','=','active'];
//            $params['condition'][]=[' and ','c.service_id','=',$rq->input('service_id')];
            //c.service_id  
            //c.
            
//            Log::channel('slackNotification')->info('Credit Request', $params);

            $member=(qrymkrController::class)::center_members_get($params);
            if($member['status']!=="success" || empty($member['body']) || count($member['body']) < 1 )
               return (!$web_access) ? ['status'=>'failed', 'message'=>'Client not found or not activated.'] : back()->withInput();
            $member=$member['body'][0];
//            return $rq->input('requested_amount');//$rq->input();
            $tx_id=$user_id.date("Ymdhis").(MethodsController::class)::code_generator(6)."010".$member->member_id;
            
            if(!$web_access)
               return ['status'=>'success','action'=>$action,'unpaid'=>$unpaid,'member'=>$member,'tx_id'=>$tx_id,'requested_amount'=>$rq->input('requested_amount'), 'provider_id'=>$rq->input('provider_id'),'id'=>null,'service_id'=>$rq->input('service_id'),'request_type'=>$rq->input('request_type') ?? 'credit' ];

            return view('pages.client_end.request_credit_client_confirm',['action'=>$action,'unpaid'=>$unpaid,'member'=>$member,'tx_id'=>$tx_id,'requested_amount'=>$rq->input('requested_amount'), 'provider_id'=>$rq->input('provider_id'),'id'=>null,'service_id'=>$rq->input('service_id'),'request_type'=>$rq->input('request_type') ?? 'credit' ]);
           }
    }
    
    
    public function transaction_log_provider($rq=null){
        return view('pages.client_end.transaction_log_provider');
    }
    public function transaction_log_group($ref_center_id=null,$request_service_id=null){
        $params=null;
        $params['condition'][]=[' and ','rq.ref_center_id','=',$ref_center_id];
        if($request_service_id!=null)
        $params['condition'][]=[' and ','rq.request_service_id','=',$request_service_id];

        $params['extras'][]=" order by rq.created_at desc ";
        $activities=(qrymkrController::class)::ref_center_requests_get($params);
        $activities=$activities['body'];
        return view('pages.client_end.transaction_log_group',compact('activities'));
    }
    public function transaction_log_agent($rq=null){
        return view('pages.client_end.transaction_log_agent');
    }
    public function transaction_log_group_admin($ref_center_id=null,$request_service_id=null){
        $params=null;
        if($ref_center_id!=0)
        $params['condition'][]=[' and ','rq.ref_center_id','=',$ref_center_id];
        if($request_service_id!=null)
         $params['condition'][]=[' and ','rq.request_type_id','=',$request_service_id];

         $params['condition'][]=[' and ','rq.taken_amount','!=','0'];

        $params['extras'][]=" order by rq.created_at desc ";
        $activities=(qrymkrController::class)::ref_center_requests_get($params);
        $activities=$activities['body'];
        return view('pages.admin_end.transaction_log_group_admin',compact('activities'));
    }

    public function transaction_log_admin($acc_number=null){
        $params=null;
        $account_detail=null;
        if($acc_number!=null)
          {
           (MethodsController::class)::account_sync([ 'acc_number'=>$acc_number]);
           $params['condition'][]=[' and ','al.acc_number','=',$acc_number];
           $account_detail=DB::table('tx_accounts')->where('acc_number','=',$acc_number)->get();
           $account_detail=$account_detail[0];
          }
        $params['extras'][]=" order by al.created_at desc limit 1000 ";
        $dta=(qrymkrController::class)::tx_account_logs_get($params);
        $dta=$dta['body'];
        return view('pages.admin_end.transaction_log_admin',['dta'=>$dta,'account_detail'=>$account_detail] );
    }
    public function api_log_admin(Request $rq=null,$agent_id=null,$rec_status=null){
        $params=null;
        if($agent_id!=null && $agent_id!=0)
        $params['condition'][]=[' and ','r.user_id','=',$agent_id];

        if($rec_status!=null && $rec_status!=='all')
        {
         $params['condition'][]=[' and ','r.recovery_flag','=','yes'];
         $params['condition'][]=[' and ','r.tx_status','=','complete'];
         $params['condition'][]=[' and ','r.api_amount-r.recovered_amount',($rec_status==='paid')?'<=':'>','0'];
        }

        $params['extras'][]=' order by r.created_at,r.updated_at desc';
        $dta=(qrymkrController::class)::tx_api_refs_get($params);
        $dta=$dta['body'];
        return view('pages.admin_end.api_log_admin',['dta'=>$dta] );
    }
    
    public function agent_cash_admin(Request $rq){
        $params=null;
        $params['extras'][]=' group by r.user_id order by r.created_at,r.updated_at desc';
        $dta=(qrymkrController::class)::tx_api_refs_agent_cash($params);
        $dta=$dta['body'];
        return view('pages.admin_end.agent_cash',['dta'=>$dta] );
    }
    
    public function easypayzhuris(Request $rq){
        $dta=(TxapiController::class)::easypayug_receive_zhuris($rq);
        return $dta;
    }
    
    public function flutterwave(Request $rq,$channel){
        if($channel==="mmoney")
        $dta=(TxapiController::class)::flutterwave_receive($rq);
//PAUSED        return null;
    }
/*
    public function relworksreceive(Request $rq,$channel){
        if($channel==="mmoney")
        $dta=(TxapiController::class)::relworks_receive($rq);
//PAUSED        return null;
    }
*/
    
    public function push_transaction(Request $rq){
        if($rq->filled('id') )
             {
                 Log::channel('slackNotification')->info('Pushing Transaction FOr Approval', ['request_body'=>$rq->input() ] );
                    $rq->merge(['table'=>"tx_api_refs" ]);
                    $dta=(qrymkrController::class)::open_insert_update($rq);     // call function that registers data
                    $detail=json_decode($dta['body'],true);
                    if($dta['status']==="success" && ($rq->input('status')==="success" || $rq->input('status')==="pending") )
                        {
                           $params=array('entity_type'=>'request',
                                    'approve_ref_id'=>$rq->input('approve_ref_id'),
                                    'approve_action'=>'approve',
                                    'approve_narration'=>$rq->input('approve_narration'),
                                    'approve_count'=>$rq->input('approve_count')??"2",
                                    'approver_role'=>'office',
                                    'user_id'=>(\Session::get('user')->id),
                                    'source_table'=>'tx_api_refs',
                                    'source_column'=>'id',
                                    'source_id'=>($rq->input('id')),
                                    'approve_scope'=>'office',        // user - admin
                                    'ref_center_id'=>'',
                                    );
                           (MethodsController::class)::approval_register($params);
                         return redirect('provider_list_admin');
                        }
                    else
                        return $rq->back();
             }
        else if($rq->filled('tx_id'))
           {
            $tx_detail=DB::table('tx_api_refs')->where('tx_id','=',$rq->input('tx_id'))->where('sync_status','=','no')->where('tx_channel','!=','cash')->first();
            if($tx_detail == null || empty($tx_detail))
              return back()->withInput();
            
            $approve_ref_id=date("Ymdhis").\Session::get('user')->id.((MethodsController::class)::code_generator(6));
            return view('pages.admin_end.push_transaction',['tx_detail'=>$tx_detail,'approve_ref_id'=>$approve_ref_id]);
           }
        else
            return view('pages.admin_end.push_transaction');
    }

    
    public function tx_acc_settings(Request $rq,$acc_number){
        if($rq->filled('id') )
             {
                    $rq->merge(['table'=>"tx_accounts",'approve_count'=>'3' ]);
                    $dta=(qrymkrController::class)::open_insert_update($rq);     // call function that registers data
                    $detail=json_decode($dta['body'],true);
                    if($dta['status']==="success" )
                        {
                           $params=array('entity_type'=>'request',
                                    'approve_ref_id'=>$rq->input('approve_ref_id'),
                                    'approve_action'=>'approve',
                                    'approve_narration'=>$rq->input('approve_narration'),
                                    'approve_count'=>$rq->input('approve_count')??"3",
                                    'approver_role'=>'office',
                                    'user_id'=>(\Session::get('user')->id),
                                    'source_table'=>'tx_accounts',
                                    'source_column'=>'id',
                                    'source_id'=>($rq->input('id')),
                                    'approve_scope'=>'office',        // user - admin
                                    'ref_center_id'=>'',
                                    );
                           (MethodsController::class)::approval_register($params);
                         return redirect('account_admin');
                        }
                    else
                        return $rq->back();
             }
        else
           {
            $acc_detail=DB::table('tx_accounts')->where('acc_number','=',$acc_number)->first();
            if($acc_detail == null || empty($acc_detail))
              return back()->withInput();
            
            $approve_ref_id=date("Ymdhis").\Session::get('user')->id.((MethodsController::class)::code_generator(6));
            return view('pages.admin_end.tx_acc_settings',['acc_detail'=>$acc_detail,'approve_ref_id'=>$approve_ref_id]);
           }
    }

    
    public function advance_pay_schedule_register_admin($rq=null){
        return view('pages.admin_end.advance_pay_schedule_register_admin');
    }
    public function advance_pay_centers_admin($rq=null){
        return view('pages.admin_end.advance_pay_centers_admin');
    }
    public function advance_pay_centers_activation_admin($rq=null){
        return view('pages.admin_end.advance_pay_centers_activation_admin');
    }
    public function investment_summary_admin($rq=null){
        return view('pages.admin_end.investment_summary_admin');
    }
    public function settings_sys_admin($rq=null){
        return view('pages.admin_end.settings_sys_admin');
    }
    public function transaction_detail_preview($rq=null){
        return view('pages.general_end.transaction_detail_preview');
    }
    public function users_agents_admin($rq=null){
        return view('pages.admin_end.users_agents_admin');
    }
    public function center_requests($rq=null){
        return view('pages.client_end.center_requests');
    }
    public function investment_list_admin($rq=null){
        return view('pages.admin_end.investment_list_admin');
    }
    public function formload1($rq=null){
        $form=(MethodsController::class)::register_item();
        return view('pages.general_end.formload1',compact('form'));
    }

}
