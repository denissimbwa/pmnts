<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class business_setup_1 extends Seeder
{
    /**
     * Run the database seeds.
     *                              php artisan db:seed --class=business_setup_1
     * @return void
     */
    public function run()
    {
        //
      \DB::table('users')->insert(
                                [
                                  [
                                   'id' => 1,
                                   'country_code' => '256',
                                   'name' => 'officer1',
                                   'password' => \Crypt::encryptString('admin'),
                                   'mobile' => '1234567890',
                                   'user_rank' => 'super admin',
                                   'user_uuid' => 'AD0001',
                                  ],
                                  [
                                   'id' => 2,
                                   'country_code' => '256',
                                   'name' => 'officer2',
                                   'password' => \Crypt::encryptString('admin'),
                                   'mobile' => '0987654321',
                                   'user_rank' => 'office',
                                   'user_uuid' => 'AD0001',
                                  ],
                                  [
                                   'id' => 3,
                                   'country_code' => '256',
                                   'name' => 'officer3',
                                   'password' => \Crypt::encryptString('admin'),
                                   'mobile' => '1234512345',
                                   'user_rank' => 'office',
                                   'user_uuid' => 'AD0001',
                                  ]
                                ]);

      \DB::table('tx_accounts')->insert(
                                [
                                  ['user_id' => 1,'acc_name' => 'cash UGX','tx_value_type' =>'cash',  'tx_channel' =>'cash', 'acc_category' => 'admin','acc_description'=>'cash','acc_number' => '123000123000111','currency'=>'UGX','invite_code'=>'AD000111','active_status'=>'active'],
                                  ['user_id' => 1,'acc_name' => 'bank UGX','tx_value_type' =>'wallet',   'tx_channel' =>'bank', 'acc_category' => 'admin','acc_description'=>'Equity bank','acc_number' => '123000123000222','currency'=>'UGX','invite_code'=>'AD000122','active_status'=>'active'],
                                  ['user_id' => 1,'acc_name' => 'wallet UGX profit','tx_value_type' =>'wallet',   'tx_channel' =>'profit', 'acc_category' => 'admin','acc_description'=>'profit','acc_number' => '123000123000333','currency'=>'UGX','invite_code'=>'AD000133','active_status'=>'active'],
                                  ['user_id' => 1,'acc_name' => 'wallet UGX credit','tx_value_type' =>'wallet',   'tx_channel' =>'credit', 'acc_category' => 'admin','acc_description'=>'loan','acc_number' => '123000123000555','currency'=>'UGX','invite_code'=>'AD000155','active_status'=>'active'],
                                  ['user_id' => 1,'acc_name' => 'wallet UGX release','tx_value_type' =>'wallet',   'tx_channel' =>'release', 'acc_category' => 'admin','acc_description'=>'Easypay mobile money','acc_number' => '123000123000444','currency'=>'UGX','invite_code'=>'AD000144','active_status'=>'active'],
                                  ['user_id' => 1,'acc_name' => 'RELWORX','tx_value_type' =>'wallet',   'tx_channel' =>'mmoney', 'acc_category' => 'admin','acc_description'=>'Relworks mobile money','acc_number' => '123000123000606','currency'=>'UGX','invite_code'=>'AD000166','active_status'=>'active'],
                                  ['user_id' => 1,'acc_name' => 'transit UGX','tx_value_type' =>'cash',   'tx_channel' =>'transit', 'acc_category' => 'admin','acc_description'=>'transit','acc_number' => '123000123000888','currency'=>'UGX','invite_code'=>'AD000188','active_status'=>'active'],
                                  ['user_id' => 1,'acc_name' => 'Exchange UGX','tx_value_type' =>'wallet',   'tx_channel' =>'exchange', 'acc_category' => 'admin','acc_description'=>'Cross currency','acc_number' => '123000123000999','currency'=>'UGX','invite_code'=>'AD000199','active_status'=>'active'],
                                  ['user_id' => 1,'acc_name' => 'provider UGX','tx_value_type' =>'provider',   'tx_channel' =>'wallet', 'acc_category' => 'admin','acc_description'=>'Providers Tx Channel','acc_number' => '123000123000777','currency'=>'UGX','invite_code'=>'AD000177','active_status'=>'active'],
                                ]);



      \DB::table('request_types')->insert(
                                [
                                  ['user_id' => 1,'request_type' => 'credit','name' =>'daily credit',  'description' =>'daily credit', 'limit' => '20000','interest'=>'10','currency'=>'UGX'],
                                  ['user_id' => 1,'request_type' => 'release','name' =>'discount',  'description' =>'targert saving', 'limit' => '200000','interest'=>'1000','currency'=>'UGX'],
                                  ['user_id' => 1,'request_type' => 'collection','name' =>'collection',  'description' =>'regular collection', 'limit' => '0','interest'=>'0','currency'=>'UGX'],
                                  ['user_id' => 1,'request_type' => 'advance','name' =>'advance',  'description' =>'monthly pay advance', 'limit' => '0','interest'=>'0','currency'=>'UGX'],
                                  ['user_id' => 1,'request_type' => 'campaign','name' =>'campaign',  'description' =>'scheduled campaign', 'limit' => '2000000','interest'=>'0','currency'=>'UGX'],
                                ]);


    }
}
