@extends('layouts.layout_account')

@section('content')

        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body" style="margin-top:10px;">



        <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Providers | <a class="btn btn-success" href="{{ route('clients_register') }}"> + Add New Client </a></h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Website</th>
                                                <th>Call Back URL</th>
                                                <th>Access Code</th>
                                                <th>Status</th>
                                                <th>Completed Transactions</th>
                                                <th>Failed Tx</th>
                                                <th>Pending Tx</th>
                                                <th>Account Bal</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @for($t=0;$t<count($dta);$t++)
                                            <tr>
                                                <th>{{ ($t+1) }}</th>
                                                <td>{{ $dta[$t]->name}} </td>
                                                <td>{{ $dta[$t]->mobile}}</td>
                                                <td>{{ $dta[$t]->website}}</td>
                                                <td>{{ $dta[$t]->call_back_url}}</td>
                                                <td>{{ $dta[$t]->access_code}}</td>
                                                <td>{{ $dta[$t]->active_status}}</td>
                                                <td>{{ $dta[$t]->tx_count}}</td>
                                                <td>{{ $dta[$t]->failed_tx_count}}</td>
                                                <td>{{ $dta[$t]->tx_pending}}</td>
                                                <td>{{ number_format($dta[$t]->amount_total) }} <a href="{{route('clients_list_admin',[$dta[$t]->id])}}"><span class="badge badge-success px-2">History<span></a> </td>
                                                <td>
                                                    <a class="badge badge-success" href="{{route('clients_register',[$dta[$t]->id])}}">Edit</a>
                                                    <a class="badge badge-success" href="{{route('withdraw_mm_api_client',[$dta[$t]->id])}}">Release Cash</a>
                                                </td>
                                            </tr>
                                       @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                        <!-- /# card -->
                    </div>

@endsection
