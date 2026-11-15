@extends('layouts.layout_account')

@section('content')




        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">

            <div class="container-fluid mt-3">
                <div class="row col-xl-12 col-lg-12">


                <div class="col-lg-12 ">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>My Accounts </h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Acc Name</th>
                                                <th>Type</th>
                                                <th>Available</th>
                                                <th>Currency</th>
                                                <th>Investments</th>
                                                <th>Profit Dependants</th>
                                                <th>Open</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for( $t=0; ($dta!=null && $t<count($dta)); $t++)
                                            <tr>
                                                <th>{{ ($t+1) }}</th>
                                                <td><a href="{{ route('transaction_log_client',[$dta[$t]->acc_number] )}}">{{ $dta[$t]->acc_name }}</a></td>
                                                <td><span class="badge badge-primary px-2">{{ $dta[$t]->tx_value_type }}</span></td>
                                                <td>{{ number_format($dta[$t]->amount,0) }} -<a class='success' href=""> View</a></td>
                                                <td>{{ $dta[$t]->currency }}</a></td>
                                                <td>{{ number_format($dta[$t]->investment_count,0) }} -<a class='success' href=""> View</a></td>
                                                <td>{{ number_format($dta[$t]->profit_dependants,0) }} -<a class='success badge badge-success px-2' href="{{ route('profit_dependants',[$dta[$t]->acc_number])}}"> View</a></td>
                                                <td class="color-primary"><a href="{{ route('transaction_log_client',[$dta[$t]->acc_number] )}}" class="btn btn-success"> Open </a> </td>
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