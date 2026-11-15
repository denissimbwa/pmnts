@extends('layouts.layout_account')

@section('content')

<div class="content-body">

        <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Users List </h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Groups</th>
                                                <th>Acc No</th>
                                                <th>Amount</th>
                                                <th>Profit Srcs</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($t=0;$t<count($dta['body']);$t++)
                                            <tr>
                                                <td>{{$dta['body'][$t]->id."-".$dta['body'][$t]->name}}</td>
                                                <td>{{$dta['body'][$t]->mobile}}</td>
                                                <td>{{$dta['body'][$t]->centers_count}}</td>
                                                <td>{{$dta['body'][$t]->acc_number}}</td>
                                                <td>{{ number_format($dta['body'][$t]->amount)." ".$dta['body'][$t]->currency}}</td>
                                                <td>{{$dta['body'][$t]->proffit_sources}}</td>
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
            <!-- #/ container -->
</div>
@endsection