@extends('layouts.layout_account')

@section('content')




        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">

            <div class="container-fluid">
                <div class="row col-xl-12">


                <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>My Groups | &nbsp; <a class="btn btn-success" href="{{ route('user_centers_register') }}"> + Create New</a> &nbsp;  </h4>
                                </div>
                                <div class="table-responsive col-xl-12">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Group Name</th>
                                                <th>Type</th>
                                                <th>Members</th>
                                                <th>Inactive Members</th>
                                                <th>Cash Accounts</th>
                                                <th>Wallet Accounts</th>
                                                <th>Group Admin</th>
                                                <th>Profit Accounts</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($t=0;$t<count($dta['body']);$t++)
                                            <tr style="background-color:{{($dta['body'][$t]->members_inactive_today > 0 )?"pink":"white"}}">
                                                <th>{{ $t }}</th>
                                                <td><a href="{{ route('account_group',['id'=>$dta['body'][$t]->id]) }}">{{$dta['body'][$t]->name}}</a> </td>
                                                <td><span>{{$dta['body'][$t]->center_type}}</span></td>
                                                <td>{{$dta['body'][$t]->members}} -<a class='success' href="{{route('center_members')}}/{{$dta['body'][$t]->id}}"> View</a></td>
                                                <td>{{$dta['body'][$t]->members_inactive}} -<a class='success' href="{{route('center_members')}}/{{$dta['body'][$t]->id}}"> View</a></td>
                                                <td>{{ number_format( $dta['body'][$t]->cash_total,0) }} -<a class='success' href="{{route('transaction_log_group',[$dta['body'][$t]->id])}}"> View</a></td>
                                                <td>{{ number_format( $dta['body'][$t]->wallet_total,0) }} -<a class='success' href="{{route('transaction_log_group',[$dta['body'][$t]->id])}}"> View</a></td>
                                                <td>{{$dta['body'][$t]->admin_count}} -<a class='success' href="{{route('center_members')}}"> View</a></td>
                                                <td>{{$dta['body'][$t]->proffit_srources}} -<a class='success' href=""> View</a></td>
                                                <td class="color-primary">
                                                    <a class="badge badge-primary px-2" href="{{ route('user_centers_register')}}/{{$dta['body'][$t]->id}}">Edit Group</a> 
                                                    <hr> <a class="badge badge-success px-2" href="{{ route('account_group',['id'=>$dta['body'][$t]->id])}}"> Open </a>
                                                </td>
                                            </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /# card -->
                    </div>




            </div>
            <!-- #/ container -->
        </div>
        <!--**********************************
            Content body end
        ***********************************-->
        
    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

@endsection