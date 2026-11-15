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
                                <h4 class="card-title">Advance Pay Group :</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Center Name</th>
                                                <th>Credit perriod</th>
                                                <th>Members</th>
                                                <th>Credit Available</th>
                                                <th>Profit Made</th>
                                                <th>Group Admin</th>
                                                <th>Profit Accounts</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>1</th>
                                                <td>Zhurisosft Consult</td>
                                                <td>2022-10-01 - 2022-10-25 <a href="{{ route('advance_pay_schedule_register_admin')}}"><span class="badge badge-success"> +Add</span></a></td>
                                                <td>15 -<a class='success' href="{{route('center_members')}}"> View</a></td>
                                                <td>UGX 300,000 -<a class='success' href="{{route('center_requests')}}"><span class='badge badge-success 2-px'> View log </span></a></td>
                                                <td>UGX 10,000 -<a class='success' href=""> View</a></td>
                                                <td>3 -<a class='success' href="{{route('center_members')}}"><span class='badge badge-success 2-px'>log</span></a></td>
                                                <td>15 -<a class='success' href=""> View</a></td>
                                                <td> Active </td>
                                                <td class="color-primary"><a href="{{ route('advance_pay_centers_activation_admin')}}">Activate/Deactivate</a> </td>
                                            </tr>
                                            <tr>
                                                <th>2</th>
                                                <td>Home School</td>
                                                <td>2022-10-01 - 2022-10-25 <a href=""><span class="badge badge-success"> +Add</span></a></td>
                                                <td>15 -<a class='success' href="{{route('center_members')}}"> View</a></td>
                                                <td>UGX 300,000 -<a class='success' href="{{route('center_requests')}}"> <span class='badge badge-success 2-px'> View log </span> </a></td>
                                                <td>UGX 10,000 -<a class='success' href=""> View</a></td>
                                                <td>3 -<a class='success' href="{{route('center_members')}}"> View</a></td>
                                                <td>15 -<a class='success' href=""> View</a></td>
                                                <td> Active </td>
                                                <td class="color-primary"><a href="{{ route('advance_pay_centers_activation_admin')}}">Activate/Deactivate</a> </td>
                                            </tr>
                                            <tr>
                                                <th>3</th>
                                                <td>Community CLinic</a></td>
                                                <td>2022-10-01 - 2022-10-25 <a href=""><span class="badge badge-success"> +Add</span></a></td>
                                                <td>15 -<a class='success' href="{{route('center_members')}}"> View</a></td>
                                                <td>UGX 300,000 -<a class='success' href="{{route('center_requests')}}"> <span class='badge badge-success 2-px'> View log </span></a></td>
                                                <td>UGX 10,000 -<a class='success' href=""> View</a></td>
                                                <td>3 -<a class='success' href="{{route('center_members')}}"> View</a></td>
                                                <td>15 -<a class='success' href=""> View</a></td>
                                                <td> Active </td>
                                                <td class="color-primary"><a href="{{ route('advance_pay_centers_activation_admin')}}">Activate/Deactivate</a> </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

@endsection