@extends('layouts.layout_account')

@section('content')

<div class="content-body">

        <div class="container-fluid">
                <div class="row col-lg-12 col-xl-12">
                    <div class="col-lg-12">
                        <div class="card col-lg-12 col-xl-12">
                            <div class="card-body">
                                <h4 class="card-title">Members | &nbsp; <a class="btn btn-success" href="{{ route('centers_member_register',['center_id'=>$dta['center_id'],'id'=>null]) }}"> + Add Member</a> &nbsp;  </h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Reg Count</th>
                                                <th>Mobile</th>
                                                <th>Ref ID</th>
                                                <th>Cash</th>
                                                <th>Debt</th>
                                                <th>Status</th>
                                                <th>Center Name</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($t=0;$t<count($dta['body']);$t++)
                                            <tr style="background-color:{{($dta['body'][$t]->active_status_member==='inactive' || $dta['body'][$t]->active_status_member==='pending')?"pink":"white"}}">
                                                <td>{{$dta['body'][$t]->name_member}}</td>
                                                <td>{{$dta['body'][$t]->reg_count_member}}</td>
                                                <td>{{$dta['body'][$t]->mobile_member}}</td>
                                                <td>{{$dta['body'][$t]->member_id}}</td>
                                                <td>{{$dta['body'][$t]->cash_member}}</td>
                                                <td>{{$dta['body'][$t]->unpaid_member}}</td>
                                                <td>{{$dta['body'][$t]->active_status_member}} - {{$dta['body'][$t]->role_member}} | <a class="badge badge-success px-2" href="{{ route('center_member_edit_status',['center_id'=>$dta['body'][$t]->id_center,'member_id'=>$dta['body'][$t]->member_id]) }}">change</a></td>
                                                <td><a href="{{ route('account_group',[$dta['center_id']])}}"> {{$dta['body'][$t]->name_center}} </a> </td>
                                            </tr>
                                            @endfor
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Ref ID</th>
                                                <th>Cash</th>
                                                <th>Debt</th>
                                                <th>Status</th>
                                                <th>Center Name</th>
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