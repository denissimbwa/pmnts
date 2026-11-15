@extends('layouts.layout_account')

@section('content')

<div class="content-body" style="min-height: 887px; margin-top:20px;">
                    <div class="col-lg-12" >
                        <div class="card col-xl-12 col-lg-12">
                            <div class="card-body">
                                <h4 class="card-title">Cash Deposit</h4>
                                <p class="text-muted m-b-15 f-s-12">This action will first be approved to take effect.</p>
                                <div class="basic-form">
                                    <form action="{{ route('deposit_cash_confirm')}}" method="POST">
                                        @method('POST')
                                        @csrf
                                        <div class="form-group">
                                            <input name="scope" required type="hidden" value={{$scope??""}} class="form-control input-default" placeholder="center ">
                                            <input name="scope_id" required type="hidden" value="{{$scope_id??""}}" class="form-control input-default" placeholder="center ">
                                            <input name="country_code" required type="hidden" value="256" class="form-control input-default" placeholder="center ">
                                            <input name="mobile" required type="text"  value="{{old('mobile')}}" class="form-control input-default" placeholder="Mobile ">
                                        @if(isset($request_service_id) )
                                            <input name="request_service_id" type="hidden" value="{{$request_service_id }}" class="form-control input-rounded" placeholder="request_service_id">
                                        @endif
                                        </div>
                                        <div class="form-group">
                                            <input name="amount" type="number" min="0" step="50" value="{{old('amount')}}" required class="form-control input-default" placeholder="Amount">
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-8 ml-auto">
                                                <button type="submit" class="btn btn-primary"> Continue </button>
                                            </div>
                                        </div>
                                    </form>
                                <a class="badge badge-success" href="{{route('deposit_mm',[$scope??"",$scope_id??"",$request_service_id])}}"> Use Mobile Money Instead</a>
                                </div>
                            </div>
                        </div>
                    </div>
</div>

@endsection
