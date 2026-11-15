@extends('layouts.layout_account')

@section('content')

<div class="content-body">

        <div class="container-fluid">
                <div class="row col-xl-12 col-lg-12">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Agent Cash : </h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>Last Action</th>
                                                <th>Agent Name</th>
                                                <th>Cash Received</th>
                                                <th>Cash Banked</th>
                                                <th>Cash Unbanked</th>
                                                <th>View</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($t=0; ($dta!=null && $t < count($dta)); $t++)
                                            <tr>
                                                <td>{{ $dta[$t]->last_date }}</td>
                                                <td>{{ $dta[$t]->name_agent }}</td>
                                                <td>{{ number_format($dta[$t]->amount_received) }}</td>
                                                <td>{{ number_format($dta[$t]->amount_banked) }}</td>
                                                <td>{{ number_format($dta[$t]->amount_unbanked) }}</td>
                                                <td> <a href="{{ route('api_log_admin')}}/{{$dta[$t]->user_id.'/unpaid'}}"> History </a></td>
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