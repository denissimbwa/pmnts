@extends('layouts.layout_account')

@section('content')
        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body" style="margin-top:10px;">


        <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Investment Log :</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Venture</th>
                                                <th>Capital invested</th>
                                                <th>Current capital</th>
                                                <th>Proffit Made</th>
                                                <th>Profit Accounts</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>1</th>
                                                <td>Kampala School (advance-pay)</td>
                                                <td><span class="badge badge-primary px-2">UGX 2,000,000</span></td>
                                                <td>UGX 1,500,000</td>
                                                <td>UGX 300,000</td>
                                                <td> 3 </td>
                                                <td> Active </td>
                                                <td class="badge badge-sucsess"><a href="{{ route('approvals_register')}}">Manage</a> </td>
                                            </tr>
                                            <tr>
                                                <th>2</th>
                                                <td>Petrocity (Fuel credit)</td>
                                                <td><span class="badge badge-primary px-2">UGX 1,500,000</span></td>
                                                <td>UGX 1,300,000</td>
                                                <td>UGX 100,000</td>
                                                <td> 3 </td>
                                                <td> Active </td>
                                                <td class="badge badge-sucsess"><a href="{{ route('approvals_register')}}">Manage</a> </td>
                                            </tr>
                                            <tr>
                                                <th>4</th>
                                                <td>Zhuris Hardware (Money-lending)</td>
                                                <td><span class="badge badge-primary px-2">UGX 1,500,000</span></td>
                                                <td>UGX 1,300,000</td>
                                                <td>UGX 100,000</td>
                                                <td> 3 </td>
                                                <td> Active </td>
                                                <td class="badge badge-sucsess"><a href="{{ route('approvals_register')}}">Manage</a> </td>
                                            </tr>
                                            <tr>
                                                <th>4</th>
                                                <td>Zhuris Hardware (Money-lending)</td>
                                                <td><span class="badge badge-warning px-2">UGX 1,500,000</span></td>
                                                <td>UGX 0</td>
                                                <td>UGX 0</td>
                                                <td> 3 </td>
                                                <td> New </td>
                                                <td class="badge badge-sucsess"><a href="{{ route('approvals_register')}}">Manage</a> </td>
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
@endsection