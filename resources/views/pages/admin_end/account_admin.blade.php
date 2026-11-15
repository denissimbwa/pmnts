@extends('layouts.layout_account')

@section('content')




        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
<br>
<span class='card' style="padding:10px 0px 0px 50px;">
    Admin Dashboard| 
    <h2> Users: {{ number_format($dta->users_count) }}
    
    @if($role==="super admin")
    <a class="btn btn-success" href="{{route('invitation_register_client',['admin',\Session::get('user')->id] )}}"> Invite Admin User </a>
    <a class="btn btn-success" href="{{route('approvals_admin')}}"> Approvals ({{ number_format($dta->approval_count) }}) </a>&nbsp;
    @endif
    </h2>
</span>


<div class="container-fluid">
                <div class="row">
                    <div class="col-lg-3 col-sm-6">
                        <div class="card card-widget">
                            <div class="card-body gradient-3">
                                <div class="media">
                                    <span class="card-widget__icon"><i class="icon-home"></i></span>
                                    <div class="media-body">
                                        <h2 class="card-widget__title">{{ number_format($dta->users_count) }}</h2>
                                        <h5 class="card-widget__subtitle">Users</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6">
                        <div class="card card-widget">
                            <div class="card-body gradient-4">
                                <div class="media">
                                    <span class="card-widget__icon"><i class="icon-tag"></i></span>
                                    <div class="media-body">
                                        <h2 class="card-widget__title">{{ number_format($dta->providers_count) }}</h2>
                                        <h5 class="card-widget__subtitle">All Client</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6">
                        <div class="card card-widget">
                            <div class="card-body gradient-4">
                                <div class="media">
                                    <span class="card-widget__icon"><i class="icon-tag"></i></span>
                                    <div class="media-body">
                                        <h2 class="card-widget__title">{{ number_format($dta->investment_requests) }}</h2>
                                        <h5 class="card-widget__subtitle">Active Clients</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6">
                        <div class="card card-widget">
                            <div class="card-body gradient-9">
                                <div class="media">
                                    <span class="card-widget__icon"><i class="icon-tag"></i></span>
                                    <div class="media-body">
                                        <h2 class="card-widget__title">{{ number_format($dta->groups_count) }}</h2>
                                        <h5 class="card-widget__subtitle">Inactive Clients</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-center">
                                    <h2 class="mt-3">{{ number_format($dta->profit_today) }}</h2>
                                    <p>Profit Today</p><a href="" class="btn gradient-3 btn-lg border-0 btn-rounded px-5">View now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-center">
                                    <h2 class="mt-3">{{ number_format($dta->tx_today) }}</h2>
                                    <p>Transactions Today</p>
                                    <a href="" class="btn gradient-4 btn-lg border-0 btn-rounded px-5">View now</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-center">
                                    <h2 class="mt-3">{{ number_format($dta->cash_today) }}</h2>
                                    <p>Cash Today</p>
                                    <a href="" class="btn gradient-4 btn-lg border-0 btn-rounded px-5">View Now</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-center">
                                    <h2 class="mt-3">{{ number_format($dta->tx_today_incomplete) }}</h2>
                                    <p>Incomplete Today</p>
                                    <a href="{{route('agent_cash_admin')}}" class="btn gradient-9 btn-lg border-0 btn-rounded px-5">View Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="row">
                    <div class="col-lg-3 col-sm-6">
                        <div class="card gradient-7">
                            <div class="card-body">
                                <h3 class="card-title text-white">Business Cash</h3>
                                <div class="d-inline-block">
                                    <h2 class="text-white">{{ number_format($dta->invested_capital) }}</h2>
                                    <a class="" href="{{route('investment_list_admin')}}">Open</a>
                                </div>
                                <span class="float-right display-5 opacity-5"><i class="fa fa-money"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card gradient-5">
                            <div class="card-body">
                                <h3 class="card-title text-white">Client Cash</h3>
                                <div class="d-inline-block">
                                    <h2 class="text-white">{{ number_format($dta->amount_cash) }}</h2>
                                    <a class="" href="">Open</a>
                                </div>
                                <span class="float-right display-5 opacity-5"><i class="fa fa-heart"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-7">
                        <div class="card gradient-8">
                            <div class="card-body">
                                <h3 class="card-title text-white">Transaction</h3>
                                <div class="d-inline-block">
                                    <h3 class="text-white">{{ number_format($dta->tx_count) }}</h3>
                                    <p class="text-white mb-0"> <a class='btn btn-primary' href="{{ route('user_centers_admin')}}"> View All</a></p>
                                </div>
                                <span class="float-right display-5 opacity-5"><i class="fa fa-money"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-7">
                        <div class="card gradient-8">
                            <div class="card-body">
                                <h3 class="card-title text-white">Cash In Wallet</h3>
                                <div class="d-inline-block">
                                    <h3 class="text-white">{{ number_format($wallet_balance) }}</h3>
                                    <p class="text-white mb-0"> <a class='btn btn-primary' href=""> View All</a></p>
                                </div>
                                <span class="float-right display-5 opacity-5"><i class="fa fa-money"></i></span>
                            </div>
                        </div>
                    </div>
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