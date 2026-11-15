@extends('layouts.layout_account')

@section('content')

<div class="content-body">

        <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Transactions Report </h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Center</th>
                                                <th>Taken</th>
                                                <th>Paid</th>
                                                <th>Unpaid</th>
                                                <th>Clinets Count</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($dta!=null)
                                            @for($t=0;$t<count($dta);$t++)
                                            <tr>
                                                <td>{{$dta[$t]->created_at}}</td>
                                                <td>{{$dta[$t]->provider_name}}</td>
                                                <td>{{$dta[$t]->taken_amount}}</td>
                                                <td>{{$dta[$t]->paid_amount}}</td>
                                                <td>{{$dta[$t]->balance}}</td>
                                                <td>{{$dta[$t]->tx_count}}</td>
                                            </tr>
                                            @endfor
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #/ container -->
</div>
@endsection