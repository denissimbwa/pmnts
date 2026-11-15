@extends('layouts.layout_account')

@section('content')




        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">

            <div class="container-fluid mt-3">
                <div class="row">


                <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>My Groups {{$provider_id}} | &nbsp; <a class="btn btn-success" href="{{ ($provider_id==null) ? route('user_centers_register') : route('provider_centers_register',['provider_id'=>$provider_id])}}"> + Create New</a></h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Center Name</th>
                                                <th>Type</th>
                                                <th>Location</th>
                                                <th>Members</th>
                                                <th>Currency</th>
                                                <th>Total in Wallet</th>
                                                <th>Total in Cash</th>
                                                <th>Total Debts</th>
                                                <th>Group Admin</th>
                                                <th>Profit Accounts</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @for($t=0; ($dta!=null && $t<count($dta)); $t++)
                                            <tr>
                                                <th>{{ ($t+1) }}</th>
                                                <td><a href="{{ route('account_group',[$dta[$t]->id])}}"> {{ $dta[$t]->name." - ".$dta[$t]->mobile }}</a></td>
                                                <td>{{ $dta[$t]->center_type }}</td>
                                                <td>{{ $dta[$t]->address }}</td>
                                                <td>{{ $dta[$t]->members }} -<a class='success' href="{{ route('center_members',[$dta[$t]->id ])}}"> View</a></td>
                                                <td>{{ $dta[$t]->currency_wallet }}</td>
                                                <td><span class="badge badge-primary px-2">{{ $dta[$t]->amount_wallet }}</span></td>
                                                <td>{{ $dta[$t]->amount_cash }} -<a class='success' href=""> View</a></td>
                                                <td>{{ $dta[$t]->unpaid_total }} -<a class='success' href=""> View</a></td>
                                                <td>{{ $dta[$t]->admin_count }} -<a class='success' href=""> View</a></td>
                                                <td>{{ $dta[$t]->proffit_receivers }} -<a class='success' href=""> View</a></td>
                                                <td> {{ $dta[$t]->active_status }} </td>
                                                <td class="color-primary"><a href="{{route('centers_activation_admin',[$dta[$t]->id])}}">Activate/Deactivate</a> </td>
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