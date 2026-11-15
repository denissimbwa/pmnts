@extends('layouts.layout_account')

@section('content')




        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
<br>
<span class='card col-xl-12' style="padding:10px 0px 0px 50px;">Online Bal: <h2>{{ \Session::get('user')->currency??"N/A" }} {{ number_format(\Session::get('user')->amount??0,0) }}  
&nbsp; <a class="btn btn-primary" href="{{route('deposit_bank',['user',\Session::get('user')->id, \Session::get('user')->acc_number])}}"> + Top Up </a>
&nbsp; <a class="btn btn-warning" href="{{route('withdraw_mm',['user',\Session::get('user')->id] ) }}"> Withdraw to Mobile </a>
&nbsp; <a class="btn btn-success" href="{{route('withdraw_bank',['user',\Session::get('user')->id, \Session::get('user')->acc_number] )}}"> Send to bank </a>
&nbsp; <a class="btn btn-danger" href="{{route('invitation_list_client')}}"> Management </a>
</h2></span>

            <div class="container-fluid mt-3">
                <div class="row">
@if($cash_in_transit ?? 0 >0)
                    <div class="col-lg-3 col-sm-6">
                        <div class="card gradient-5">
                            <div class="card-body">
                                <h3 class="card-title text-white">Unbanked Cash <i class="fa fa-money"></i></h3>
                                <div class="d-inline-block">
                                    <h2 class="text-white">{{ number_format($cash_in_transit,0) }}</h2>
                                    <form action="{{route('deposit_request_pay_banked')}}" method="POST"> @csrf <input name="amount" type="number" value="{{ $cash_in_transit }}" min="0"> <button type="submit" class="btn btn-danger"> BANK NOW </button> </form>
                                </div>
                            </div>
                        </div>
                    </div>
@endif
                    <div class="col-lg-3 col-sm-6">
                    <a href="{{ route('user_centers')}}">
                        <div class="card gradient-1">
                            <div class="card-body">
                                <h3 class="card-title text-white">My Group Centers </h3>
                                <div class="d-inline-block">
                                    <h2 class="text-white">{{ number_format(\Session::get('user')->centers_count) }}</h2>
                                    <p class="text-white mb-0"> Groups found</p>
                                </div>
                                <span class="float-right display-5 opacity-5"><i class="fa fa-money"></i></span>
                            </div>
                        </div>
                    </a>
                    </div>
                    
                    
                    <div class="col-lg-3 col-sm-6">
                    <a href="{{ route('user_accounts') }}">
                        <div class="card gradient-1">
                            <div class="card-body">
                                <h3 class="card-title text-white">Accounts </h3>
                                <div class="d-inline-block">
                                    <h4 class="text-white">UGX {{ \Session::get('user')->currency." ".number_format(\Session::get('user')->amount_total_cash,0) }} -Cash</h4>
                                    <h4 class="text-white">UGX {{ \Session::get('user')->currency." ".number_format(\Session::get('user')->amount_total_wallet,0) }} -Online</h4>
                                    <p class="text-white mb-0">( {{ \Session::get('user')->accounts_count }} source(s) )</p>
                                </div>
                                <span class="float-right display-5 opacity-5"><i class="fa fa-money"></i></span>
                            </div>
                        </div>
                    </a>
                    </div>

                    <div class="col-lg-3 col-sm-6">
                        <div class="card gradient-5">
                            <div class="card-body">
                                <h3 class="card-title text-white">UNPAID Cash</h3>
                                <div class="d-inline-block">
                                    <h2 class="text-white">{{\Session::get('user')->currency." ".number_format(\Session::get('user')->unpaid_total) }}</h2>
                                    <p class="text-white mb-0"> pay now </p>
                                </div>
                                <span class="float-right display-5 opacity-5"><i class="fa fa-heart"></i></span>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="col-lg-12 col-sm-12 col-md-12 col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>My Saving Accounts | &nbsp; &nbsp; <a class="btn btn-success" href="{{ route('collection_accounts_available',['id'=>\Session::get('user')->id] )}}"> + Add Saving Account </a></h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Provider</th>
                                                <th>Cash Saved</th>                                           
                                                <th>Telephone</th>
                                                <th>Address</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($t=0;($acc_collects!=null && $t<count($acc_collects));$t++)
                                            <tr>
                                                <th>{{ ($t+1) }}</th>
                                                <td>{{ $acc_collects[$t]->name_provider }}</td>
                                                <td><span class="badge badge-primary px-2">{{ $acc_collects[$t]->currency_center." ".number_format($acc_collects[$t]->requested_amount_total,0) }}</span></td>
                                                <td>{{ $acc_collects[$t]->mobile_provider }}</td>
                                                <td>{{ $acc_collects[$t]->address_provider }}</td>
                                                <td class="color-primary">
                                                    <a class="btn btn-success" href="{{ route('request_deposit_client',['provider_id'=>$acc_collects[$t]->provider_id]) }}"> Save Cash </a>
                                                    <a class="btn btn-warning" href=""> History  </a>
                                                </td>
                                            </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /# card -->
                    </div>






                <div class="col-lg-12 col-sm-12 col-md-12 col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4> Recent Activities </h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Amount</th>                                           
                                                <th>Status</th>
                                                <th>Provider</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($t=0;($activities!=null && $t<count($activities));$t++)
                                            <tr style="background-color:{{ ($activities[$t]->active_status!=='complete') ? "pink":"white" }};">
                                                <td>{{ $activities[$t]->created_at }}</td>
                                                <td><span class="badge badge-primary px-2">{{ $activities[$t]->currency." ".number_format($activities[$t]->requested_amount,0) }}</span></td>
                                                <td>{{ $activities[$t]->active_status }}</td>
                                                <td>{{ $activities[$t]->name_provider }}</td>
                                            </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /# card -->
                    </div>


                <div class="col-lg-12 col-sm-12 col-md-12 col-xl-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>My Investments | &nbsp; &nbsp; <a class="btn btn-success" href="{{ route('investment_available' )}}"> ( {{ \Session::get('user')->investment_available }} ) Available for booking </a></h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Venture</th>
                                                <th>Capital invested</th>
                                                <th>Current capital</th>
                                                <th>Add Capital</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($t=0;($providers!=null && $t<count($providers));$t++)
                                            <tr>
                                                <th>{{ ($t+1) }}</th>
                                                <td>{{ $providers[$t]->name_provider." - ".$providers[$t]->business_line }}</td>
                                                <td><span class="badge badge-primary px-2">{{ $providers[$t]->currency_inverstor." ".number_format($providers[$t]->amount_inverstor_total,0) }}</span></td>
                                                <td>{{ $providers[$t]->currency_inverstor." ".number_format($providers[$t]->amount,0) }}</td>
                                                <td class="color-primary">
                                                    <a class="btn btn-success" href="{{ route('investment_booking',[$providers[$t]->id,$providers[$t]->acc_number_investor])}}"> Request to Re-investment </a>
                                                    @if($providers[$t]->booking_id !=null)
                                                    <a class="btn btn-warning" href="{{ route('investment_booking_confirm',[$providers[$t]->booking_id])}}"> Load Investment  </a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /# card -->
                    </div>


<span class='card' style="padding:10px 0px 0px 50px;"><p> My Groups | </p></span>

                <div class="row">
                    @for($y=0;($centers!=null && $y<count($centers)); $y++)
                    <div class="col-lg-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-center">
                                    <img src="./images/users/8.jpg" class="rounded-circle" alt="">
                                    <h5 class="mt-3 mb-1">{{ $centers[$y]->currency." ".number_format($centers[$y]->amount_member,0) }}</h5>
                                    <p class="m-0">{{ $centers[$y]->name_center }}</p>
                                    <p class="m-0"> {{ number_format($centers[$y]->members,0) }}</p>
                                    @if($centers[$y]->unpaid_member > 0)
                                    <a href="javascript:void()" class="btn btn-sm btn-warning">{{ $centers[$y]->currency." ".number_format($centers[$y]->unpaid_member,0) }} unpaid</a>
                                    @endif
                                    <a class="btn btn-success" href="{{route('account_group',[$centers[$y]->id_center])}}">Open</a><!-- <a href="javascript:void()" class="btn btn-sm btn-warning">Send Message</a> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    @endfor

                </div>




            </div>
            <!-- #/ container -->
        </div>
        <!--**********************************
            Content body end
        ***********************************-->
        
    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

@endsection