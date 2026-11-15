@extends('layouts.layout_account')

@section('content')




        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body col-lg-12 col-xl-12">

            <div class="container-fluid mt-3  col-lg-12 col-xl-12">
                <div class="row  col-lg-12 col-xl-12">
                        <div class="card-body col-lg-12 col-xl-12">
                                <h4 class="card-title">Add Dependant Account
                                    @if($acc_detail->acc_category==="group")
                                    <a class="badge badge-success px-2" href="{{ route('account_group',['id'=>$acc_detail->ref_center_id])}}"> Open Center </a>
                                    @endif
                                </h4>
                                <div class="basic-form">
                                    <form action="{{ route('profit_dependants',[$acc_number_giver])}}" method="POST">
                                        @csrf
                                        <div class="input-group mb-3">
                                            <input name="country_code" type="hidden" value="256" />
                                            <input type="text" name="search" class="form-control" style="padding:25px;" placeholder="Search By Mobile Number">
                                            <div class="input-group-append"><button class=" btn btn-success " type="submit"><span class="input-group-text"> Search </span></button>
                                            </div>
                                        </div>
                                    </form>
                                    @if(isset($account_suggestions))
                                    
                                    <table class="table">
                                        <tr><th>No.</th> <th>Name</th> <th>Mobile</th> <th>Action</th></tr>
                                        @for($g=0; ($account_suggestions!=null && $g < count($account_suggestions)); $g++ )
                                        <tr>
                                            <th> {{ ($g+1) }}</th>
                                            <th>{{ $account_suggestions[$g]->acc_name }}</th>
                                            <th>{{ $account_suggestions[$g]->mobile_owner }}</th>
                                            <th>
                                                <form action="{{ route('profit_dependants',[$acc_number_giver])}}" method="POST">
                                                    @csrf
                                                    <input name="acc_number_giver" type="hidden" value="{{ $acc_number_giver }}" />
                                                    <input name="acc_number_receiver" type="hidden" value="{{ $account_suggestions[$g]->acc_number }}" />
                                                    <button type="submit" name="submit" value="submit" class="btn btn-primary" > Add </button>
                                                </form>
                                            </th>
                                        </tr>
                                        @endfor
                                    </table>
                                  @endif
                                </div>
                            </div>
                            

                <div class="col-lg-12">
                        
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4> {{ $acc_detail->acc_name }}  ( {{$acc_detail->acc_category }} ) Balance : {{$acc_detail->currency." ".number_format($acc_detail->amount) }}</h4>
                                    <h5>All Profit Dependants :</h5>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Acc Name</th>
                                                <th>Profit Share %</th>
                                                <th>Date</th>
                                                <th>Set New</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <form action="{{ route('profit_dependants',[$acc_number_giver])}}" method="POST" >
                                            @csrf
                                        <tbody>
                                            @for($t=0; ($dta!=null && $t<count($dta) ); $t++ )
                                            <tr>
                                                <th> {{ ($t+1)}} </th>
                                                <td> {{ $dta[$t]->acc_name_receiver }} </td>
                                                <td> {{ $dta[$t]->rate }} %</td>
                                                <td> {{ $dta[$t]->created_at }} </td>
                                                <td><input type="number" class="form-control" name="rate_edit[]" required value="{{ $dta[$t]->rate }}" min="0" step="0.001"></td>
                                                <td class="color-danger">
                                                   <input name="id[]" value="{{ $dta[$t]->id }}" type="hidden" />
                                                </td>
                                            </tr>
                                            @endfor
                                            <tr>
                                                <td colspan="6">
                                                    <textarea leng="150" class="form-control" name="approve_narration" placeholder="Comment here..."></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th colspan="5"> Changes made shall first be approved. Total shares MUST be equal to 100</th>
                                                <td class="color-danger">
                                                   <input name="ref_center_id" value="{{ $acc_detail->ref_center_id }}" type="hidden" />
                                                   <input name="approve_ref_id" value="{{ $approve_ref_id }}" type="hidden" />
                                                   <button type="submit" class="btn btn-warning" name="update" value="update" > Update Now </button>
                                                </td>
                                            </tr>
                                            </form>
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