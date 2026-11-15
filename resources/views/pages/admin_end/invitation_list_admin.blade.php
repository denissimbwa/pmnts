@extends('layouts.layout_account')

@section('content')




        <!--**********************************
            Content body start
        ***********************************-->
<div class="content-body">

        <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
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
                                                <th>User</th>
                                                <th>Role</th>
                                                <th>Current status</th>
                                                <th>Acceptance Status</th>
                                                <th>Invited By</th>
                                                <th>Remove Access</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($t=0;($dta!=null && $t<count($dta)); $t++)
                                            <tr title="Invited By : {{ $dta[$t]->name_admin." ".$dta[$t]->mobile_admin }}">
                                                <th> {{ ($t+1) }}</th>
                                                <td> {{ $dta[$t]->access_ref_name }} </td>
                                                <td>{{ $dta[$t]->created_at }}</td>
                                                <td>{{ $dta[$t]->name_member." : ".$dta[$t]->mobile_member }}</td>
                                                <td>{{ $dta[$t]->role }}</td>
                                                <td>{{ $dta[$t]->active_status }}</td>
                                                <td> <span class="badge {{ ($dta[$t]->acceptance==="accepted")?'badge-success' : ''}} px-2"> {{ $dta[$t]->acceptance }} </span></td>
                                                <td>{{ $dta[$t]->name_admin." : ".$dta[$t]->mobile_admin }}</td>
                                                <td><form action="{{ route('invitation_register_client_remove') }}" method="POST"> @csrf <input type="hidden" name="id" value="{{ $dta[$t]->id}}" > <button class="btn btn-danger" type="submit" name="update" value="update" > Remove Access </button> </form></td>
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
<!--**********************************
        Main wrapper end
    ***********************************-->

@endsection