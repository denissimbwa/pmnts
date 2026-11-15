@extends('layouts.layout_account')

@section('content')

<div class="content-body">

        <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                @if($account_detail!=null)
                                    <h4 class="card-title">Transactions History : {{ $account_detail->acc_name }} : {{ $account_detail->currency." ".number_format($account_detail->amount,0) }}</h4>
                                    @if($account_detail->tx_channel==="bank" || $account_detail->tx_channel==="profit" || $account_detail->tx_channel==="mmoney")
                                    <a class="btn btn-primary" href="{{route('deposit_bank',['admin',\Session::get('user')->id, $account_detail->acc_number] ) }}">+ Top Up Via Bank</a>&nbsp;&nbsp; 
                                        @if($account_detail->amount > 1)
                                        <a class="btn btn-success" href="{{route('withdraw_bank',['admin',\Session::get('user')->id, $account_detail->acc_number]) }}"> Withdraw From bank </a>&nbsp; 
                                        @endif
                                    @endif
                                    @if($account_detail->acc_category === "admin" && $account_detail->tx_channel === "credit")
                                        <a class="btn btn-success" href="{{route('investment_reload_office') }}"> Reinvest </a>&nbsp; 
                                    @endif
                                @endif
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
                                                <th>Transit</th>
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
                                                <td>{{ $dta[$t]->transit_status }}</td>
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