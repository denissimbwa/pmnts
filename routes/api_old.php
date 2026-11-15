<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::controller(App\Http\Controllers\webrqController::class)->group( function(){
    Route::match(['get','post'],'/login','login')->name('login');
    Route::match(['get','post'],'/change_password','change_password')->name('change_password');
    Route::match(['get','post'],'/signup','signup')->name('signup');
    Route::match(['get','post'],'/signup_verify','signup_verify')->name('signup_verify');
    Route::match(['get','post'],'/account_client','account_client')->name('account_client');
    Route::match(['get','post'],'/invitation_list_client','invitation_list_client')->name('invitation_list_client');
    Route::match(['get','post'],'/account_provider/{id}','account_provider')->name('account_provider');
    Route::match(['get','post'],'/request_credit_client','request_credit_client')->name('request_credit_client');
    Route::match(['get','post'],'/request_credit_client_confirm','request_credit_client_confirm')->name('request_credit_client_confirm');
    Route::match(['get','post'],'/withdraw_mm_confirm','withdraw_mm_confirm')->name('withdraw_mm_confirm');
    Route::match(['get','post'],'/withdraw_mm/{scope?}/{scope_id?}/{request_service_id?}','withdraw_mm')->name('withdraw_mm');
    Route::match(['get','post'],'/account_group/{id}','account_group')->name('account_group');
    Route::match(['get','post'],'/account_provider/{id}','account_provider')->name('account_provider');
    Route::match(['get','post'],'/deposit_request_pay','deposit_request_pay')->name('deposit_request_pay');
    Route::match(['get','post'],'/deposit_request_pay_banked','deposit_request_pay_banked')->name('deposit_request_pay_banked');
    Route::match(['get','post'],'/deposit_cash_confirm','deposit_cash_confirm')->name('deposit_cash_confirm');
    Route::match(['get','post'],'/deposit_bank_confirm','deposit_bank_confirm')->name('deposit_bank_confirm');
    Route::match(['get','post'],'/centers_member_register/{center_id}/{id?}','centers_member_register')->name('centers_member_register');

    Route::match(['get','post'],'/user_accounts','user_accounts')->name('user_accounts');
    Route::match(['get','post'],'/investment_list_client','investment_list_client')->name('investment_list_client');
    Route::match(['get','post'],'/investment_available/{user_id_inverstor?}','investment_available')->name('investment_available');
    Route::match(['get','post'],'/transaction_log_client/{acc_number}','transaction_log_client')->name('transaction_log_client');
    Route::match(['get','post'],'/investment_booking/{id?}/{acc_number?}','investment_booking')->name('investment_booking');


    Route::match(['get','post'],'/easypayzhuris','easypayzhuris')->name('easypayzhuris');
    Route::match(['get','post'],'/flutterwave/{channel}','flutterwave')->name('flutterwave');
    
    Route::match(['get','post'],'/collection_accounts_available/{id?}','collection_accounts_available')->name('collection_accounts_available');
    Route::match(['get','post'],'/request_deposit_client/{provider_id?}','request_deposit_client')->name('request_deposit_client');
    Route::match(['get','post'],'/request_deposit_client_confirm','request_deposit_client_confirm')->name('request_deposit_client_confirm');

    Route::match(['get','post'],'/deposit_mm_api','deposit_mm_api')->name('deposit_mm_api');
    Route::match(['get','post'],'/relworxsend','relworx')->name('relworx');

});
