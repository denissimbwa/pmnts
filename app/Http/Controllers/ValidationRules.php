<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ValidationRules extends Controller
{
    //
    
    public static function signup_verify(){
    return [
            'verification_code' => 'numeric|required|digits:10'
           ];
    }

    public static function signup(){
    return [
            'name' => 'required|regex:/^[a-z A-Z]+$/u',
    //      'email' => 'required|email',
            'password' => 'required|min:4|max:12',
            'confirm_password' => 'required|same:password',
            'mobile' => 'numeric|required|digits:10'
           ];
    }

    public static function login(){
    return [
            'password' => 'required|min:4|max:12',
            'mobile' => 'numeric|required|digits:10'
           ];
    }

    public static function change_password(){
    return [
            'password_old' => 'required|min:4|max:12',
            'password' => 'required|min:4|max:12',
            'confirm_password' => 'required|same:password',
           ];
    }

    

}
