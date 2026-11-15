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
                                <h4 class="card-title">Providers | <a class="btn btn-success" href="{{ route('provider_register') }}"> + Add New Provider </a></h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Provider</th>
                                                <th>Business Line</th>
                                                <th>Investor</th>
                                                <th>Current capital</th>
                                                <th>Amount Taken</th>
                                                <th>Clientel size</th>
                                                <th>Centers</th>
                                                <th>Manage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @for($t=0;$t<count($dta);$t++)
                                            <tr>
                                                <th>{{ ($t+1) }}</th>
                                                <td>{{ $dta[$t]->name_provider}} <span class="badge badge-success px-2"><a href="{{route('account_provider',[$dta[$t]->id])}}">Open</a><span></td>
                                                <td>{{ $dta[$t]->business_line}}</td>
                                                <td>{{ $dta[$t]->acc_number_investor}}</td>
                                                <td><span class="badge badge-primary px-2">{{ $dta[$t]->currency." ".number_format($dta[$t]->amount,0) }}</span></td>
                                                <td>{{ $dta[$t]->currency." ".number_format($dta[$t]->taken_amount,0) }}</td>
                                                <td>{{ $dta[$t]->currency." ".number_format($dta[$t]->clientel_size,0) }}</td>
                                                <td>{{ number_format($dta[$t]->centers,0) }} <span class="badge badge-success px-2"><a href="{{route('user_centers_admin',['provider_id'=>$dta[$t]->id])}}">Open</a><span></td>
                                                <td><a class="badge badge-success" href="{{route('provider_services_selection',[$dta[$t]->id])}}">Manage</a></td>
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
