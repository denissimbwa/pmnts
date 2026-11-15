@extends('layouts.layout_account')

@section('content')

<div class="content-body" style="min-height: 887px; margin-top:20px;">
                    <div class="col-lg-6  col-xl-12" >
                        <div class="card  col-xl-12">
                            <div class="card-body">
                                <h4 class="card-title">Bank Deposit : EQUITY BANK - 4201296610 - ZHURISOFT CONSULT</h4>
                                <p class="text-muted m-b-15 f-s-12">This submission will first be approved in 30min.</p>
                                <div class="basic-form">
                                    <form action="{{ route('deposit_bank_confirm')}}" method="POST">
                                        @method('POST')
                                        @csrf
                                        <div class="form-group">User's mobile:
                                            <input name="scope" required type="hidden" value={{$scope??""}} class="form-control input-default" placeholder="center ">
                                            <input name="scope_id" required type="hidden" value="{{$scope_id??""}}" class="form-control input-default" placeholder="center ">
                                            <input name="country_code" required type="hidden" value="256" class="form-control input-default" placeholder="center ">
                                            <input name="mobile" required type="text"  value="{{old('mobile')}}" class="form-control input-default" placeholder="Mobile ">
                                        @if(isset($acc_number) && $acc_number!=null)
                                            <input name="acc_number" type="hidden" value="{{$acc_number}}" class="form-control input-rounded" placeholder="Destination account">
                                        @endif
                                        </div>
                                        <div class="form-group">Amount
                                            <input name="amount" type="number" min="0" step="50" value="{{old('amount')}}" required class="form-control input-default" placeholder="Amount">
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-8 ml-auto">
                                                <button type="submit" class="btn btn-primary"> Continue </button>
                                            </div>
                                        </div>
                                    </form>
                                <a class="badge badge-success" href="{{route('deposit_mm',[$scope,$scope_id,$acc_number])}}"> Use Mobile Money Instead</a>
                                </div>
                            </div>
                        </div>
                    </div>
</div>

@endsection
