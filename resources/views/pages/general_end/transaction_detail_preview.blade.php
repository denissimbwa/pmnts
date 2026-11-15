@extends('layouts.layout_account')

@section('content')




        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body" style="margin-top:10px;">

<span class='card' style="padding:10px 0px 0px 50px;"><p> Transaction Preview | &nbsp; <a class="btn btn-primary" href=""> Print Now </a>&nbsp; </p></span>



                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>Transaction Detail </h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th>Detail</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>Date</th>
                                                <td>2022-10-25</td>
                                            </tr>
                                            <tr>
                                                <th>Type</th>
                                                <td>Deposit</td>
                                            </tr>
                                            <tr>
                                                <th>Currency</th>
                                                <td>UGX</td>
                                            </tr>
                                            <tr>
                                                <th>Mode</th>
                                                <td>Cash</td>
                                            </tr>
                                            <tr>
                                                <th>From</th>
                                                <td>Denis SImbwa</td>
                                            </tr>
                                            <tr>
                                                <th>To</th>
                                                <td>Savings Group</td>
                                            </tr>
                                            <tr>
                                                <th>Remark</th>
                                                <td>N/a</td>
                                            </tr>
                                            <tr>
                                                <th>Status</th>
                                                <td>Approved</td>
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