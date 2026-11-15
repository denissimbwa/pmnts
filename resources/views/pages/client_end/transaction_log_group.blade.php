@extends('layouts.layout_account')

@section('content')

<div class="content-body">

        <div class="container-fluid col-lg-12 col-xl-12">
                <div class="row col-lg-12 col-xl-12">
                    <div class="col-lg-12 col-xl-12">
                        <div class="card col-lg-12 col-xl-12">
                            <div class="card-body">
                                <h4 class="card-title">Transactions History :</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Ref</th>
                                                <th>Name</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Flow Type</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($t=0; ($activities!=null && $t<count($activities)); $t++ )
                                            <tr>
                                                <th>{{ ($t+1) }}</th>
                                                <td>{{ $activities[$t]->created_at }}</td>
                                                <td>{{ $activities[$t]->detail1 }}</td>
                                                <td>{{ $activities[$t]->detail2 }}</td>
                                                <td>{{ ($activities[$t]->flow_type==="inflow") ? number_format($activities[$t]->paid_amount) : number_format($activities[$t]->taken_amount) }}</td>
                                                <td class="color-primary"><a href=""> {{ $activities[$t]->active_status }} </a> </td>
                                                <td>{{ $activities[$t]->flow_type }}</td>
                                                <td class="color-primary">
                                                    <a class="badge badge-success" href="{{route('transaction_detail_preview')}}"> View </a>
                                                </td>
                                            </tr>
                                            @endfor
                                        <tfoot>
                                            <tr>
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Ref</th>
                                                <th>Name</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Flow Type</th>
                                                <th>Action</th>
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