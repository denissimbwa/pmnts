@extends('layouts.layout_account')

@section('content')

<div class="content-body">

        <div class="container-fluid">
                <div class="row col-xl-12 col-lg-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Transactions Requests History : </h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Last Update</th>
                                                <th>Tx ID</th>
                                                <th>Channel</th>
                                                <th>Status</th>
                                                <th>Source Acc</th>
                                                <th>Dest Acc</th>
                                                <th>Currency</th>
                                                <th>Amount</th>
                                                <th>ID Ref</th>
                                                <th>Param2</th>
                                                <th>Recovery Flag</th>
                                                <th>Office Acc</th>
                                                <th>Tx Fee</th>
                                                <th>API Fee</th>
                                                <th>Tx Channel</th>
                                                <th>Recovered</th>
                                                <th>User Scope</th>
                                                <th>User ID</th>
                                                <th>Sync Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($t=0; ($dta!=null && $t < count($dta)); $t++)
                                            <tr>
                                                <td>{{ $dta[$t]->created_at }}</td>
                                                <td>{{ $dta[$t]->updated_at }}</td>
                                                <td>{{ $dta[$t]->tx_id }}</td>
                                                <td>{{ $dta[$t]->tx_channel }}</td>
                                                <td>{{ $dta[$t]->tx_status }}</td>
                                                <td>{{ $dta[$t]->source_account }}</td>
                                                <td>{{ $dta[$t]->dest_account }}</td>
                                                <td>{{ $dta[$t]->currency_source." - ".$dta[$t]->currency_destination }}</td>
                                                <td>{{ number_format($dta[$t]->ref_amount) }}</td>
                                                <td>{{ $dta[$t]->pay_id }}</td>
                                                <td>{{ $dta[$t]->param2 }}</td>
                                                <td>{{ $dta[$t]->recovery_flag }}</td>
                                                <td>{{ $dta[$t]->acc_number_office }}</td>
                                                <td>{{ number_format($dta[$t]->tx_fee) }}</td>
                                                <td>{{ number_format($dta[$t]->api_fee) }}</td>
                                                <td>{{ $dta[$t]->tx_channel }}</td>
                                                <td>{{ number_format($dta[$t]->recovered_amount) }}</td>
                                                <td>{{ $dta[$t]->tx_owner_type }}</td>
                                                <td>{{ $dta[$t]->tx_owner_id }}</td>
                                                <td>{{ $dta[$t]->sync_status }}</td>
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