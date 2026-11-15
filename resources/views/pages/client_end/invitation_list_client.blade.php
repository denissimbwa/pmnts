@extends('layouts.layout_account')

@section('content')




        <!--**********************************
            Content body start
        ***********************************-->
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
                                                <th>#</th>
                                                <th>Access To</th>
                                                <th>Date</th>
                                                <th>Role</th>
                                                <th>Current status</th>
                                                <th>Acceptance Status</th>
                                                <th>Accept</th>
                                                <th>Reject</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($t=0;($dta!=null && $t<count($dta)); $t++)
                                            <tr>
                                                <th> {{ ($t+1) }}</th>
                                                <td>
                                                   @if($dta[$t]->access_scope==="provider" && $dta[$t]->acceptance==="accepted")
                                                     <a href="{{ route('account_provider',[$dta[$t]->access_ref_id] ) }}">{{ $dta[$t]->access_ref_name }}</a>
                                                   @elseif($dta[$t]->access_scope==="group" && $dta[$t]->acceptance==="accepted")
                                                     <a href="{{ route('account_group',[$dta[$t]->access_ref_id] ) }}">{{ $dta[$t]->access_ref_name }}</a>
                                                   @elseif($dta[$t]->access_scope==="admin" && $dta[$t]->acceptance==="accepted")
                                                     <a href="{{ route('account_admin') }}">{{ $dta[$t]->access_ref_name }}</a>
                                                   @else
                                                     {{ $dta[$t]->access_ref_name }}
                                                   @endif
                                                </td>
                                                <td>{{ $dta[$t]->created_at }}</td>
                                                <td>{{ $dta[$t]->role }}</td>
                                                <td>{{ $dta[$t]->active_status }}</td>
                                                <td>{{ $dta[$t]->acceptance }}</td>
                                                @if($dta[$t]->acceptance==="pending" || $dta[$t]->active_status==="inactive")
                                                <td><form action="{{ route('invitation_list_client') }}" method="POST"> @csrf <input type="hidden" name="id" value="{{ $dta[$t]->id}}" > <input type="hidden" name="acceptance" value="accepted" > <input type="hidden" name="active_status" value="active" > <button class="btn btn-success" type="submit" name="update" value="update" > Accept </button> </form></td>
                                                <td><form action="{{ route('invitation_list_client') }}" method="POST"> @csrf <input type="hidden" name="id" value="{{ $dta[$t]->id}}" > <input type="hidden" name="acceptance" value="rejected" > <input type="hidden" name="active_status" value="inactive" > <button class="btn btn-danger" type="submit" name="update" value="update" > Reject </button> </form></td>
                                                @else
                                                <td></td>
                                                <td></td>
                                                @endif
                                                <td>
                                                @if($dta[$t]->admin_id===\Session::get('user')->id)
                                                    <form action="{{ route('invitation_register_client_remove') }}" method="POST"> @csrf <input type="hidden" name="id" value="{{ $dta[$t]->id}}" > <button class="btn btn-danger" type="submit" name="update" value="update" > Remove Access </button> </form>
                                                @endif
                                                </td>
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
</div>    <!--**********************************
        Main wrapper end
    ***********************************-->

@endsection