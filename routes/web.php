<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return view('pages.login');
})->name('login');


Route::controller(App\Http\Controllers\webrqController::class)->group( function(){
    Route::match(['get','post'],'/login','login')->name('login');
    Route::match(['get','post'],'/change_password','change_password')->name('change_password');
    Route::match(['get','post'],'/signup','signup')->name('signup');
    Route::match(['get','post'],'/signup_verify','signup_verify')->name('signup_verify');
    Route::match(['get','post'],'/account_client','account_client')->name('account_client');
    Route::match(['get','post'],'/account_admin/{currency?}','account_admin')->name('account_admin');
    Route::match(['get','post'],'/user_profile','user_profile')->name('user_profile');
    Route::match(['get','post'],'/user_centers','user_centers')->name('user_centers');
    Route::match(['get','post'],'/user_centers_admin/{provider_id?}','user_centers_admin')->name('user_centers_admin');
    Route::match(['get','post'],'/user_accounts','user_accounts')->name('user_accounts');
    Route::match(['get','post'],'/all_user_accounts/{category?}/{invest?}','all_user_accounts')->name('all_user_accounts');
    Route::match(['get','post'],'/user_centers_register/{id?}','user_centers_register')->name('user_centers_register');
    Route::match(['get','post'],'/centers_member_register/{center_id}/{id?}','centers_member_register')->name('centers_member_register');
    Route::match(['get','post'],'/center_member_edit_status/{center_id}/{member_id}','center_member_edit_status')->name('center_member_edit_status');
    Route::match(['get','post'],'/center_members/{id?}','center_members')->name('center_members');
    Route::match(['get','post'],'/deposit_cash/{scope}/{scope_id}/{request_service_id}','deposit_cash')->name('deposit_cash');
    Route::match(['get','post'],'/deposit_request_pay','deposit_request_pay')->name('deposit_request_pay');
    Route::match(['get','post'],'/deposit_request_pay_banked','deposit_request_pay_banked')->name('deposit_request_pay_banked');
    Route::match(['get','post'],'/withdraw_cash/{scope}/{scope_id}/{request_service_id}','withdraw_cash')->name('withdraw_cash');
    Route::match(['get','post'],'/account_group/{id}','account_group')->name('account_group');
    Route::match(['get','post'],'/center_activities','center_activities')->name('center_activities');
    Route::match(['get','post'],'/withdraw_cash_confirm','withdraw_cash_confirm')->name('withdraw_cash_confirm');
    Route::match(['get','post'],'/deposit_cash_confirm','deposit_cash_confirm')->name('deposit_cash_confirm');
    Route::match(['get','post'],'/finish_action/{next?}/{message?}','finish_action')->name('finish_action');
    Route::match(['get','post'],'/deposit_bank_confirm','deposit_bank_confirm')->name('deposit_bank_confirm');
    Route::match(['get','post'],'/deposit_bank/{scope}/{scope_id}/{acc_number}/{request_service_id?}','deposit_bank')->name('deposit_bank');
    Route::match(['get','post'],'/withdraw_bank_confirm','withdraw_bank_confirm')->name('withdraw_bank_confirm');
    Route::match(['get','post'],'/withdraw_bank/{scope?}/{scope_id?}/{acc_number?}','withdraw_bank')->name('withdraw_bank');
    Route::match(['get','post'],'/transfer_wallet/{scope_source}/{scope_id_source}/{acc_number_source}/{scope_id_dest}/{acc_number_dest}','transfer_wallet')->name('transfer_wallet');
    Route::match(['get','post'],'/transfer_wallet_confirm','transfer_wallet_confirm')->name('transfer_wallet_confirm');
    Route::match(['get','post'],'/investment_reload_office','investment_reload_office')->name('investment_reload_office');
    Route::match(['get','post'],'/deposit_mm_confirm','deposit_mm_confirm')->name('deposit_mm_confirm');
    Route::match(['get','post'],'/deposit_mm/{scope?}/{scope_id?}/{request_service_id?}','deposit_mm')->name('deposit_mm');
    Route::match(['get','post'],'/withdraw_mm_confirm','withdraw_mm_confirm')->name('withdraw_mm_confirm');
    Route::match(['get','post'],'/withdraw_mm/{scope?}/{scope_id?}/{request_service_id?}','withdraw_mm')->name('withdraw_mm');
    Route::match(['get','post'],'/investment_list_client','investment_list_client')->name('investment_list_client');
    Route::match(['get','post'],'/provider_list_admin/{service_id?}','provider_list_admin')->name('provider_list_admin');
    Route::match(['get','post'],'/investment_available/{user_id_inverstor?}','investment_available')->name('investment_available');
    Route::match(['get','post'],'/investment_booking/{id?}/{acc_number?}','investment_booking')->name('investment_booking');
    Route::match(['get','post'],'/investment_booking_confirm/{id}','investment_booking_confirm')->name('investment_booking_confirm');
    Route::match(['get','post'],'/collection_schedule_register/{scope_id}/{service_type}/{request_service_id?}','collection_schedule_register')->name('collection_schedule_register');
    Route::match(['get','post'],'/collection_shedule_list','collection_shedule_list')->name('collection_shedule_list');
    Route::match(['get','post'],'/invitation_register_client/{scope}/{scope_id}/{acceptance?}','invitation_register_client')->name('invitation_register_client');
    Route::match(['get','post'],'/invitation_register_admin','invitation_register_admin')->name('invitation_register_admin');
    Route::match(['get','post'],'/invitation_list_client','invitation_list_client')->name('invitation_list_client');
    Route::match(['get','post'],'/invitation_list_admin/{admin_id?}','invitation_list_admin')->name('invitation_list_admin');
    Route::match(['get','post'],'/investment_summary_client','investment_summary_client')->name('investment_summary_client');
    Route::match(['get','post'],'/transaction_log_client/{acc_number}','transaction_log_client')->name('transaction_log_client');
    Route::match(['get','post'],'/provider_register','provider_register')->name('provider_register');
    Route::match(['get','post'],'/provider_services_register','provider_services_register')->name('provider_services_register');
    Route::match(['get','post'],'/provider_services','provider_services')->name('provider_services');
    Route::match(['get','post'],'/provider_services_selection/{id}','provider_services_selection')->name('provider_services_selection');
    Route::match(['get','post'],'/profit_dependants/{acc_number?}','profit_dependants')->name('profit_dependants');
    Route::match(['get','post'],'/request_credit_client','request_credit_client')->name('request_credit_client');
    Route::match(['get','post'],'/request_credit_client_confirm','request_credit_client_confirm')->name('request_credit_client_confirm');
    Route::match(['get','post'],'/transaction_log_provider','transaction_log_provider')->name('transaction_log_provider');
    Route::match(['get','post'],'/transaction_log_group/{center_id}/{service_id?}','transaction_log_group')->name('transaction_log_group');
    Route::match(['get','post'],'/transaction_log_group_admin/{center_id}/{service_id?}','transaction_log_group_admin')->name('transaction_log_group_admin');
    Route::match(['get','post'],'/account_provider/{id}','account_provider')->name('account_provider');
    Route::match(['get','post'],'/transaction_log_agent','transaction_log_agent')->name('transaction_log_agent');
    Route::match(['get','post'],'/transaction_log_admin/{acc_number?}','transaction_log_admin')->name('transaction_log_admin');
    Route::match(['get','post'],'/approvals_admin','approvals_admin')->name('approvals_admin');
    Route::match(['get','post'],'/approvals_client/{ref_center_id}','approvals_client')->name('approvals_client');
    Route::match(['get','post'],'/approvals_register_client/{approve_ref_id}/{relation?}/{source_id?}','approvals_register_client')->name('approvals_register_client');
    Route::match(['get','post'],'/approvals_register_admin/{approve_ref_id}/{relation?}/{source_id?}','approvals_register_admin')->name('approvals_register_admin');
    Route::match(['get','post'],'/advance_pay_centers_admin','advance_pay_centers_admin')->name('advance_pay_centers_admin');
    Route::match(['get','post'],'/advance_pay_schedule_register_admin','advance_pay_schedule_register_admin')->name('advance_pay_schedule_register_admin');
    Route::match(['get','post'],'/advance_pay_centers_activation_admin','advance_pay_centers_activation_admin')->name('advance_pay_centers_activation_admin');
    Route::match(['get','post'],'/investment_summary_admin','investment_summary_admin')->name('investment_summary_admin');
    Route::match(['get','post'],'/settings_sys_admin','settings_sys_admin')->name('settings_sys_admin');
    Route::match(['get','post'],'/transaction_detail_preview','transaction_detail_preview')->name('transaction_detail_preview');
    Route::match(['get','post'],'/centers_activation_admin/{ref_ceter_id}','centers_activation_admin')->name('centers_activation_admin');
    Route::match(['get','post'],'/users_list_admin','users_list_admin')->name('users_list_admin');
    Route::match(['get','post'],'/users_agents_admin','users_agents_admin')->name('users_agents_admin');
    Route::match(['get','post'],'/center_requests','center_requests')->name('center_requests');
    Route::match(['get','post'],'/investment_list_admin','investment_list_admin')->name('investment_list_admin');
    Route::match(['get','post'],'/formload1','formload1')->name('formload1');
    Route::match(['get','post'],'/logout','logout')->name('logout');
    Route::match(['get','post'],'/tx_receiver_easypay','tx_receiver_easypay')->name('tx_receiver_easypay');
    Route::match(['get','post'],'/campaign_listing/{scope_id?}','campaign_listing')->name('campaign_listing');
    Route::match(['get','post'],'/campaign_detail/{id?}','campaign_detail')->name('campaign_detail');
    Route::match(['get','post'],'/deposit_campaign','deposit_campaign')->name('deposit_campaign');
    Route::match(['get','post'],'/push_transaction','push_transaction')->name('push_transaction');
    Route::match(['get','post'],'/api_log_admin/{agent_id?}/{rec_status?}','api_log_admin')->name('api_log_admin');
    Route::match(['get','post'],'/tx_acc_settings/{acc_number}','tx_acc_settings')->name('tx_acc_settings');
    Route::match(['get','post'],'/agent_cash_admin','agent_cash_admin')->name('agent_cash_admin');
    Route::match(['get','post'],'/invitation_register_client_remove','invitation_register_client_remove')->name('invitation_register_client_remove');
    Route::match(['get','post'],'/report_requests/{ref_center_id?}','report_requests')->name('report_requests');
    
    Route::match(['get','post'],'/provider_centers_register/{provider_id?}/{id?}','provider_centers_register')->name('provider_centers_register');
    Route::match(['get','post'],'/collection_accounts_available/{id?}','collection_accounts_available')->name('collection_accounts_available');
    Route::match(['get','post'],'/provider_centers_member_register/{center_id}/{provider_id}/{id?}','provider_centers_member_register')->name('provider_centers_member_register');
    Route::match(['get','post'],'/request_deposit_client/{provider_id?}','request_deposit_client')->name('request_deposit_client');
    Route::match(['get','post'],'/request_deposit_client_confirm','request_deposit_client_confirm')->name('request_deposit_client_confirm');
    
    
    Route::match(['get','post'],'/clients_register/{id?}','clients_register')->name('clients_register');
    Route::match(['get','post'],'/clients_list_admin/{id?}','clients_list_admin')->name('clients_list_admin');
    Route::match(['get','post'],'/deposit_mm_api','deposit_mm_api')->name('deposit_mm_api');
    Route::match(['get','post'],'/withdraw_mm_api_client/{client_id?}','withdraw_mm_api_client')->name('withdraw_mm_api_client');
    
});



    Route::fallback( function () {
        return view('pages.error_503');
    });
