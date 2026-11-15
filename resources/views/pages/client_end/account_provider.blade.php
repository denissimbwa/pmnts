@extends('layouts.layout_account')

@section('content')




        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
<br>
<span class='card col-xl-12' style="padding:10px 0px 0px 50px;">{{ $dta->name_provider }} | <h2> Prepaid Bal: {{ $dta->currency." ".number_format($dta->amount,0) }} 
<a class="btn btn-primary" href="{{route('deposit_bank',['provider',$dta->id,$dta->acc_number])}}">+ Top Up Service Value</a>&nbsp;
&nbsp;<a class="btn btn-success" href="{{route('invitation_register_client',['provider',$dta->id] )}}"> Invite User</a>
@if($dta->amount > 0)
 <a class="btn btn-warning" href="{{route('withdraw_bank',['provider',$dta->id,$dta->acc_number]) }}"> Consume Service </a>&nbsp; 
@endif
<a class="btn btn-primary" href="{{ route('report_requests',[$dta->id]) }}">Report Requests</a>

</h2></span>

            <div class="container-fluid mt-3">
                <div class="row">
                    
@if($cash_in_transit>0)
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
                    <div class="col-lg-4 col-sm-6">
                        <div class="card gradient-1">
                            <div class="card-body">
                                <h3 class="card-title text-white">Available Cash </h3>
                                <div class="d-inline-block">
                                    <h2 class="text-white">{{ $dta->currency." ".number_format($dta->amount,0) }}</h2>
                                    <p class="text-white mb-0"> {{ $dta->activities_provider }} - Activities</p>
                                    <a class="" href="">View </a>
                                </div>
                                <span class="float-right display-5 opacity-5"><i class="fa fa-money"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="card gradient-7">
                            <div class="card-body">
                                <h3 class="card-title text-white">Cash Transfered Today</h3>
                                <div class="d-inline-block">
                                    <h2 class="text-white">{{ $dta->currency." ".number_format($dta->taken_today) }} </h2>
                                    <p class="text-white mb-0"> {{$dta->activities_today }} - Activities </p>
                                    <p class="text-white mb-0"> {{ $dta->currency." ".number_format($dta->paid_today,0) }} paid-today </p>
                                    <a class="" href="">Open</a>
                                </div>
                                <span class="float-right display-5 opacity-5"><i class="fa fa-money"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="card gradient-7">
                            <div class="card-body">
                                <h3 class="card-title text-white">Member Size</h3>
                                <div class="d-inline-block">
                                    <h2 class="text-white">{{ $dta->member_size }} </h2>
                                    <a class="" href="">Open</a>
                                </div>
                                <span class="float-right display-5 opacity-5"><i class="fa fa-money"></i></span>
                            </div>
                        </div>
                    </div>
                    
                    
                    
                </div>



                <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>
                                    @if($dta->request_type==="credit")
                                    &nbsp;
                                    <form action="{{ route('request_credit_client')}}" method="POST">
                                        @csrf
                                        <input type="hidden" name="provider_id" value="{{ $dta->id }} ">
                                        <button type="submit" class="btn btn-success" >  Credit </button>
                                    </form>
                                    @endif
                                    Recent Activity | 
                                    </h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Mobile</th>
                                                <th>Name</th>
                                                <th>Amount</th>
                                                <th>Charge</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($t=0; ($activities!=null && $t<count($activities)); $t++ )
                                            <tr style="background-color:{{ ($activities[$t]->active_status!=='complete') ? "pink":"white" }};">
                                                <th>{{ ($t+1) }}</th>
                                                <td>{{ $activities[$t]->created_at }}</td>
                                                <td>{{ $activities[$t]->mobile_member }}</td>
                                                <td>{{ $activities[$t]->name_member }}</td>
                                                <td>{{ number_format($activities[$t]->requested_amount,0) }}</td>
                                                <td>{{ number_format($activities[$t]->interest,0) }}</td>
                                                <td class="color-primary"><a href=""> {{ $activities[$t]->active_status }} </a> </td>
                                                <td class="color-primary">
                                                    <a class="badge badge-success" href=""> View </a>
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