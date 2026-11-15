@extends('layouts.layout_account')

@section('content')
        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body" style="margin-top:10px;">


                <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>My Investments | <a class="btn btn-success" href="{{ route('investment_available') }}"> (3) New Investments for booking </a></h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Venture</th>
                                                <th>Capital invested</th>
                                                <th>Current capital</th>
                                                <th>Proffit Made</th>
                                                <th>Withdraw</th>
                                                <th>Add Capital</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>1</th>
                                                <td>Kampala School (advance-pay)</td>
                                                <td><span class="badge badge-primary px-2">UGX 2,000,000</span></td>
                                                <td>UGX 1,500,000</td>
                                                <td>UGX 300,000</td>
                                                <td class="color-primary"><a href="">Withdraw to bank </a> </td>
                                                <td class="badge badge-sucsess"><a href="">Add Capital</a> </td>
                                            </tr>
                                            <tr>
                                                <th>2</th>
                                                <td>Petrocity (Fuel credit)</td>
                                                <td><span class="badge badge-primary px-2">UGX 1,500,000</span></td>
                                                <td>UGX 1,300,000</td>
                                                <td>UGX 100,000</td>
                                                <td class="color-primary"> <a href="">Withdraw to bank </a> </td>
                                                <td class="badge badge-sucsess"><a href="">Add Capital</a> </td>
                                            </tr>
                                            <tr>
                                                <th>4</th>
                                                <td>Zhuris Hardware (Money-lending)</td>
                                                <td><span class="badge badge-primary px-2">UGX 1,500,000</span></td>
                                                <td>UGX 1,300,000</td>
                                                <td>UGX 100,000</td>
                                                <td class="color-primary"> <a href="">Withdraw to bank </a> </td>
                                                <td class="badge badge-sucsess"><a href="">Add Capital</a> </td>
                                            </tr>
                                            <tr>
                                                <th>4</th>
                                                <td>Zhuris Hardware (Money-lending)</td>
                                                <td><span class="badge badge-warning px-2">UGX 1,500,000</span></td>
                                                <td>UGX 0</td>
                                                <td>UGX 0</td>
                                                <td class="btn btn-success"> <a href="">Approved</a> </td>
                                                <td><a class="badge badge-success px-2" href="">Load Capital</a> </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /# card -->
                    </div>



                <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>My Profit sources</h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Venture</th>
                                                <th>Capital invested</th>
                                                <th>Profit Rate % </th>
                                                <th>Proffit Collected</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>1</th>
                                                <td>Kampala School (advance-pay)</td>
                                                <td><span class="badge badge-primary px-2">UGX 2,000,000</span></td>
                                                <td>2 %</td>
                                                <td>UGX 300,000</td>
                                                <td class="color-primary"><a href="{{ route('transaction_log_client') }}" class="badge badge-success" href="">View Trasactions</a> </td>
                                            </tr>
                                            <tr>
                                                <th>2</th>
                                                <td>Petrocity (Fuel credit)</td>
                                                <td><span class="badge badge-primary px-2">UGX 1,500,000</span></td>
                                                <td>1 %</td>
                                                <td>UGX 20,000</td>
                                                <td class="color-primary"><a href="{{ route('transaction_log_client') }}" class="badge badge-success" href="">View Trasactions</a> </td>
                                            </tr>
                                            <tr>
                                                <th>2</th>
                                                <td>Zhuris Hardware (Money-lending)</td>
                                                <td><span class="badge badge-primary px-2">UGX 1,500,000</span></td>
                                                <td>5 %</td>
                                                <td>UGX 100,000</td>
                                                <td class="color-primary"><a href="{{ route('transaction_log_client') }}" class="badge badge-success" href="">View Trasactions</a> </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /# card -->
                    </div>


            </div>
@endsection