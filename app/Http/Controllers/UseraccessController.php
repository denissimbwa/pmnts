<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class UseraccessController extends Controller
{
    //
    
    
  //[user_id , password, branch, ref_center_id ]
  public static function access_check($params){
    $score['valid']=true;
    $score['error']='';
    $score['message']='';
    $valid=true;
    $user_detail=null;

    //Check for user_id existance if it is still in the session
    $valid=( array_key_exists('user_id',$params) && $params['user_id']==null) ? false : $valid;

    //Get user degtail if user ID is part of the arguments
    if( array_key_exists('user_id',$params) && ( $params['user_id']!=null))
      {
       $user_detail=DB::table('users')->find($params['user_id']);
       $valid=( $user_detail==null || empty($user_detail) ) ? false : $valid;
      }
    if(!$valid)
      {
        $score['valid']=$valid;
        $score['error']='user';
        $score['message']='user not found';
        return $score;
      }

    //Check for password validity if it is part of the request
    if(array_key_exists('password',$params) && array_key_exists('user_id',$params) )
      $valid=(self::password_check($params['user_id'],$params['password'],$user_detail)) ? $valid:false;
    
    //Check access tocken
    if(array_key_exists('access_tocken',$params) )
      $valid=( $params['access_tocken']===$user_detail->access_tocken ) ? $valid : false;

    if(!$valid)
      {
        $score['valid']=$valid;
        $score['error']='user';
        $score['message']='user not found';
        return $score;
      }

    //Checking per request branch in webrq
    $branch=$params['branch'] ?? null;
    if($branch!=null)
    switch($branch){
        case "account_group":       // Validate access to center home account
            {
             $rslt=self::account_group_check($params['user_id'],$params['ref_center_id'],$params['role']);
             $score['valid']=$rslt->status;
             if(!$rslt->status)
              {
                $score['error']='group';
                $score['message']='user not found';
              }
             else
                $score['role']=$rslt->role;
             break;
            }
        case "account_admin":       // Validate access to center home account
            {
             $rslt=self::account_admin_check($params['user_id'],$params['role'] );
             $score['valid']=$rslt->status;
             if(!$rslt->status)
              {
                $score['error']='admin';
                $score['message']='user not found';
              }
             else
                $score['role']=$rslt->role;
             break;
            }
        case "account_admin":       // Validate access to center home account
            {
             $rslt=self::request_credit_client($params['user_id'],$params['provider_id'] );
             $score['valid']=$rslt->status;
             if(!$rslt->status)
              {
                $score['error']='admin';
                $score['message']='user not found';
              }
             else
                $score['role']=$rslt->role;
             break;
            }
        default:
            return $score;
    }
    
  return $score;
  }

//Check user's access to credit release for a provider
public static function request_credit_client($user_id,$rq){
 $valid['status']=false;
        //Verify Access rights
        $dta=DB::table('user_access')->where('role','!=','viewer')->where('role','!=','member')->where('access_ref_id','=',$rq->input('provider_id'))->where('access_table','=','providers')->where('user_id','=',$user_id)->first();
        $valid['status']=($dta!=null && !empty($dta) ) ;
        if($valid['status'])
           $valid['role']=$dta->role;

    return (object)$valid;
}

//Check user's password
public static function password_check($user_id,$password1,$user_detail=null){
 if($user_detail==null)
     $user_detail=DB::table('users')->find($user_id);
 $valid=false;
 if($user_detail != null && !empty($user_detail))
  {
   $password2=\Crypt::decryptString($user_detail->password);
   $valid=($password2!==$password1 ?? "")?false:$valid;
  }
 return $valid;
}
 
public static function account_group_check($user_id,$ref_center_id,$role){
 $valid['status']=false;
 if($role!=null)
     $dta=DB::table('user_access')->where('access_scope','=','group')->where('role','=',$role)->where('user_id','=',$user_id)->where('active_status','=','active')->first();
 else
    $dta=DB::table('user_access')->where('access_scope','=','group')->where('user_id','=',$user_id)->where('active_status','=','active')->first();

 $valid['status']=($dta!=null && !empty($dta) ) ;

 if($valid['status'])
   $valid['role']=$dta->role;
 if($valid['status'] && $role!=null && $dta->role!==$role)
   $valid['status']=false;
   
 return (object)$valid;
}
 
public static function account_admin_check($user_id,$role){
 $valid['status']=false;

 $dta=DB::table('users')->find($user_id);
 if($dta==null || empty($dta) )
   return (object)$valid;

 if($dta->user_rank==="super admin")// || $dta->user_rank==="officer1" || $dta->user_rank==="officer2" || $dta->user_rank==="officer3") MAKE ONE ACTIVE TO BEGIN WITH
   {
    $valid['status']=true;
    $valid['role']=$dta->user_rank;
    return (object)$valid;   
   }
   
 if($role!=null)
   $dta=DB::table('user_access')->where('access_scope','=','admin')->where('role','=',$role)->where('user_id','=',$user_id)->where('active_status','=','active')->first();
 else
   $dta=DB::table('user_access')->where('access_scope','=','admin')->where('user_id','=',$user_id)->where('active_status','=','active')->first();


 $valid['status']=($dta!=null && !empty($dta) > 0 );

 if($valid['status'])
   $valid['role']=$dta->role;
 if($role!=null && $dta->role!==$role)
   $valid['status']=false;

 return (object)$valid;
}
 
 
}
