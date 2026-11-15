@extends('layouts.layout_account')

@section('content')

<div class="content-body" style="min-height: 887px; margin-top:20px;">
                    <div class="col-lg-6" >
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Mobile Money Deposit</h4>
                                <div class="basic-form">
                                    <form action="{{ route('deposit_mm_confirm')}}" method="POST">
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
                                        @if(isset($acc_number) && $acc_number!=null)
                                            <input name="acc_number" type="hidden" value="{{$acc_number}}" class="form-control input-rounded" placeholder="Destination account">
                                        @endif
                                        <div class="form-group">
                                            <input name="amount" type="number" min="0" step="50" value="{{old('amount')}}" required class="form-control input-default" placeholder="Amount">
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-8 ml-auto">
                                                <button type="submit" class="btn btn-primary"> Continue </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
</div>

@endsection
