@extends('layouts.layout_account')

@section('content')

<div class="content-body">

        <div class="container-fluid">
                <div class="row col-xl-12 col-lg-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Transactions History : {{ $account_detail->acc_name }} : {{ $account_detail->currency." ".number_format($account_detail->amount,0) }}</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Acc Name</th>
                                                <th>Tx Type</th>
                                                <th>Amount</th>
                                                <th>Currency</th>
                                                <th>Mode</th>
                                                <th>Tx Flag</th>
                                                <th>Slip No</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($t=0; ($dta!=null && $t < count($dta)); $t++)
                                            <tr>
                                                <td>{{ $dta[$t]->created_at }}</td>
                                                <td>{{ $dta[$t]->acc_name }}</td>
                                                <td>{{ $dta[$t]->tx_type }}</td>
                                                <td>{{ number_format($dta[$t]->amount,0) }}</td>
                                                <td>{{ ($dta[$t]->tx_type==="deposit") ? $dta[$t]->currency_destination:$dta[$t]->currency_source }}</td>
                                                <td>{{ $dta[$t]->tx_value_type }}</td>
                                                <td>{{ $dta[$t]->tx_flag }}</td>
                                                <td>{{ $dta[$t]->slip_no }}</td>
                                            </tr>
                                           @endfor
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