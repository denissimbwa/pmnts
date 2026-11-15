@extends('layouts.layout_account')

@section('content')

<div class="content-body">

        <div class="container-fluid col-xl-12 col-lg-12">
                <div class="row  col-xl-12 col-lg-12">
                    <div class="col-12 col-xl-12  col-lg-12">
                        <div class="card col-xl-12 col-lg-12">
                            <div class="card-body col-xl-12 col-lg-12">
                                <h4 class="card-title">User Accounts</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>Acc Name</th>
                                                <th>Acc No.</th>
                                                <th>Type</th>
                                                <th>Currency</th>
                                                <th>Balance</th>
                                                <th>Investments</th>
                                                <th>Profit Sources</th>
                                                <th>Open</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($t=1; ($dta!=null && $t<count($dta) );$t++)
                                            <tr>
                                                <td>{{ $dta[$t]->acc_name }}</td>
                                                <td>{{ $dta[$t]->acc_number }}</td>
                                                <td>
                                                    {{ $dta[$t]->tx_channel." - ".$dta[$t]->acc_category }}
                                                    @if($dta[$t]->acc_category==="admin")
                                                    <a href="{{ route('tx_acc_settings',[$dta[$t]->acc_number] )}}"><span class="badge badge-warning px-2">Manage</span></a>
                                                    @endif
                                                </td>
                                                <td>{{ $dta[$t]->currency }}</td>
                                                <td>{{ number_format($dta[$t]->amount) }}</td>
                                                <td>{{ number_format($dta[$t]->investment_count) }}</td>
                                                <td>{{ number_format($dta[$t]->profit_srources_count) }}</td>
                                                <td>
                                                    <a href="{{ route('transaction_log_admin',[$dta[$t]->acc_number] )}}"><span class="badge badge-primary px-2">Open</span></a>
                                                </td>
                                            </tr>
                                            @endfor
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Acc Name</th>
                                                <th>Type</th>
                                                <th>Currency</th>
                                                <th>Balance</th>
                                                <th>Investments</th>
                                                <th>Profit Sources</th>
                                                <th>Open</th>
                                            </tr>
                                        </tfoot>
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