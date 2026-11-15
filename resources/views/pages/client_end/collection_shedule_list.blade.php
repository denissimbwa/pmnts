@extends('layouts.layout_account')

@section('content')




        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body" style="margin-top:10px;">

<span class='card' style="padding:10px 0px 0px 50px;"><p> Available Schedules | &nbsp; <a class="btn btn-primary" href="{{route('collection_schedule_register')}}"> + Add New</a>&nbsp; </p></span>

                <div class="row">

                    <div class="col-lg-3 col-sm-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-center">
                                    <img src="./images/users/8.jpg" class="rounded-circle" alt="">
                                    <h5 class="mt-3 mb-1">Every Wednesday <a href="{{route('collection_schedule_register')}}"><span class="badge badge-primary px-2">Edit</span></a></h5>
                                    <p class="m-0">Amount Set: 10,000</p>
                                    <p class="m-0">Uncollected: 2,000,000 <a href=""><span class="badge badge-danger px-2">View List</span></a> </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>



                <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>Collection History </h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Memebr</th>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Method</th>
                                                <th>Status</th>
                                                <th>Balance</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>1</th>
                                                <td>Denis Simbwa</td>
                                                <td>2022-10-25</td>
                                                <td>UGX 1,500,000</td>
                                                <td>Cash</td>
                                                <td>pending</td>
                                                <td class="color-primary"><a href="">0 </a> </td>
                                                <td class="color-primary"><a href="{{route('approvals_register')}}">Approve Now</a> </td>
                                            </tr>
                                            <tr>
                                                <th>1</th>
                                                <td>Denis Simbwa</td>
                                                <td>2022-10-25</td>
                                                <td>UGX 1,500,000</td>
                                                <td>Cash</td>
                                                <td>pending</td>
                                                <td class="color-primary"><a href="">0 </a> </td>
                                                <td class="color-primary"><a href="{{route('approvals_register')}}">Approve Now</a> </td>
                                            </tr>
                                            <tr>
                                                <th>1</th>
                                                <td>Denis Simbwa</td>
                                                <td>2022-10-25</td>
                                                <td>UGX 1,500,000</td>
                                                <td>Mobile Money</td>
                                                <td>Approved</td>
                                                <td class="color-primary"><a href="">0 </a> </td>
                                                <td class="color-success"><a href="{{route('transaction_detail_preview')}}">View</a> </td>
                                            </tr>
                                            <tr>
                                                <th>1</th>
                                                <td>Denis Simbwa</td>
                                                <td>2022-10-25</td>
                                                <td>UGX 1,500,000</td>
                                                <td>Mobile Money</td>
                                                <td>Approved</td>
                                                <td class="color-primary"><a href="">0 </a> </td>
                                                <td class="color-success"><a href="{{route('transaction_detail_preview')}}">View</a> </td>
                                            </tr>
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