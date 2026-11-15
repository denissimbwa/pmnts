@extends('layouts.layout_account')

@section('content')

        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body" style="margin-top:10px;">



                <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>My Investments | <a class="btn btn-success" href="{{ route('provider_services_register') }}"> + Add New Business Line </a></h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Business Line</th>
                                                <th>Description</th>
                                                <th>Cash Limit</th>
                                                <th>Interest</th>
                                                <th>Providers</th>
                                                <th>Client Centers</th>
                                                <th>Requests</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($t=0;$t<count($dta);$t++)
                                            <tr>
                                                <th>{{ ($t+1) }}</th>
                                                <td>{{ $dta[$t]->name }}</td>
                                                <td>{{ $dta[$t]->description }}</td>
                                                <td>{{ $dta[$t]->limit_request }}</span></td>
                                                <td>{{ $dta[$t]->interest }} %</td>
                                                <td>{{ number_format($dta[$t]->provider_count,0) }} - <a href="{{route('provider_list_admin',[$dta[$t]->id])}}"> View </a></td>
                                                <td>{{ number_format($dta[$t]->center_count,0) }} </a></td>
                                                <td>{{ number_format($dta[$t]->request_count,0) }} - <a  class="badge badge-success"  href="{{ route('transaction_log_group_admin',[0,$dta[$t]->id]) }}"> recent </a></td>
                                                <td><a class="badge badge-primary" href="{{ route('provider_services_register')}}">Edit</a></td>
                                            </tr>
                                           @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /# card -->
                    </div>

@endsection
