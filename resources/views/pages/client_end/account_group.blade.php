@extends('layouts.layout_account')

@section('content')




        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
<br>
<span class='card  col-xl-12' style="padding:10px 0px 0px 50px;">Online Balance | 
        <h2> {{ $dta->name." - ".$dta->mobile." - ".$dta->address }}: {{ $dta->currency_wallet??"N/A" }} {{ number_format($dta->wallet_total??0,0) }} 
            <a class="btn btn-primary" href="{{route('deposit_bank',['group',$dta->id,$dta->acc_number_wallet] ) }}">+ Top Up Via Bank</a>&nbsp;&nbsp;
            @if($role==="owner")
            <a class="btn btn-success" href="{{route('withdraw_bank',['group',$dta->id,$dta->acc_number_wallet]) }}"> Send to bank </a>&nbsp; 
            <a class="btn btn-success" href="{{route('invitation_register_client',['group',$dta->id] )}}"> Invite A person to join </a>
            @endif
        </h2>
</span>

            <div class="container-fluid mt-3">
                <div class="row">
                    <div class="col-lg-3 col-sm-6">
                        <div class="card gradient-1">
                            <div class="card-body">
                                <h3 class="card-title text-white">Approvals</h3>
                                <div class="d-inline-block">
                                    <h2 class="text-white">{{ $dta->approvals??0 }}</h2>
                                    <p class="text-white mb-0"> 2 -new</p>
                                    <a class="text-white mb-0" href="{{ route('approvals_client',['ref_center_id'=>$dta->id])}}"> View </a>
                                </div>
                                <span class="float-right display-5 opacity-5"><i class="fa fa-money"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card gradient-7">
                            <div class="card-body">
                                <h3 class="card-title text-white">Cash In Treasurey</h3>
                                <div class="d-inline-block">
                                    <h2 class="text-white">{{ $dta->currency_wallet??"N/A" }} {{ $dta->cash_total??0 }}</h2>
                                    <p class="text-white mb-0"> 130 - Activities </p>
                                    <a class="" href="">Open</a>
                                </div>
                                <span class="float-right display-5 opacity-5"><i class="fa fa-money"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="card gradient-5">
                            <div class="card-body">
                                <h3 class="card-title text-white">Total Debts</h3>
                                <div class="d-inline-block">
                                    <h2 class="text-white">{{ $dta->currency_wallet." ".number_format($dta->unpaid_total,0) }}</h2>
                                    <p class="text-white mb-0"> 1 -Debtor(s) </p>
                                    <a class="" href="">Open</a>
                                </div>
                                <span class="float-right display-5 opacity-5"><i class="fa fa-heart"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-7">
                        <div class="card gradient-8">
                            <div class="card-body">
                                <h3 class="card-title text-white">Members</h3>
                                <div class="d-inline-block">
                                    <h3 class="text-white">{{ number_format($dta->members,0) }}</h3>
                                    <p class="text-white mb-0"> <a class='btn btn-primary' href="{{ route('center_members')}}/{{$dta->id}}"> View All</a></p>
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
                                    <h4>Collection Services  </h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Service Name</th>
                                                <th>Collected</th>
                                                <th>Uncollected</th>
                                                <th>Taken</th>
                                                <th>Log</th>
                                                <th>Withdraw</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($t=0;($services!=null && $t<count($services));$t++)
                                            <tr>
                                                <th>{{ ($t+1) }}</th>
                                                <td>
                                                    {{ $services[$t]->name }} 
                                                    @if($services[$t]->tx_service_cat === "collection" && $services[$t]->interval ==0 && $services[$t]->interval !=null )
                                                      <span class="badge badge-warning">@ {{ $services[$t]->interval." dys"}} </span>
                                                      <a class="badge badge-success" href="{{ route('collection_shedule_list')}}"> View </a>
                                                    @elseif($services[$t]->tx_service_cat === "campaign")
                                                      {{ substr($services[$t]->start_date,0,10)." - ".substr($services[$t]->end_date,0,10) }}
                                                    @endif
                                                    <a class="badge badge-success px-2" href="{{ route('collection_schedule_register',[$dta->id,'campaign',$services[$t]->id])}}"> Manage </a>
                                                </td>
                                                <td>{{ $services[$t]->currency." ".number_format($services[$t]->paid_amount) }}</td>
                                                <td>{{ $services[$t]->currency." ".number_format($services[$t]->balance) }}</td>
                                                <td>{{ $services[$t]->currency." ".number_format($services[$t]->taken_amount) }}</td>
                                                <td><a href="{{route('transaction_log_group',[$dta->id,$services[$t]->id])}}"><span class="badge badge-primary px-2"> History </span></a></td>
                                                <td><a class="btn btn-success" href="{{ route('withdraw_cash',['group',$dta->id,$services[$t]->id ]) }}">  Withdraw Cash </a> </td>
                                                <td><a class="btn btn-warning" href="{{ route('deposit_cash',['group',$dta->id,$services[$t]->id ]) }}">  Collect Cash </a> </td>
                                            </tr>
                                            @endfor
                                            <tr>
                                                <td colspan="5">
                                                    <a class="btn btn-success" href="{{ route('collection_schedule_register',[$dta->id,'campaign' ])}}"> + Add New Campaign </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /# card -->
                    </div>


                <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>Recent Activity  </h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Tx Name</th>
                                                <th>Acc Name</th>
                                                <th>Flow Type</th>
                                                <th>Amount</th>
                                                <th>Channel</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($t=0;($transactions!=null && $t<count($transactions));$t++)
                                            <tr>
                                                <th>{{ ($t+1) }}</th>
                                                <td>{{ $transactions[$t]->detail2}}</td>
                                                <td>{{ $transactions[$t]->name_member}}</td>
                                                <td><span class="badge badge-primary px-2">{{ $transactions[$t]->flow_type}}</span></td>
                                                <td>{{ $transactions[$t]->currency." ".( ($transactions[$t]->flow_type === "inflow")?number_format($transactions[$t]->paid_amount) : number_format($transactions[$t]->taken_amount) ) }}</td>
                                                <td>{{ $transactions[$t]->tx_value_type}}</td>
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
                                    <h4>My Investments | &nbsp; &nbsp; <a class="btn btn-success" href="{{ route('investment_available',[\Session::get('user')->id])}}"> ( {{ \Session::get('user')->investment_available }} ) Available for booking </a></h4>
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
                                                    <a class="btn btn-warning" href="{{ route('investment_booking_confirm',[$providers[$t]->booking_id]) }}"> Load Investment  </a> 
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