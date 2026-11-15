@extends('layouts.layout_account')

@section('content')

<div class="content-body" style="min-height: 887px; margin-top:20px;">
                    <div class="col-lg-6" >
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Withdrawing Cash from Treasury:</h4>
                                <p class="text-muted m-b-15 f-s-12">This action will first be approved by admin.</p>
                                <div class="basic-form">
                                    <form  action="{{ route('withdraw_cash_confirm')}}" method="POST">
                                        @method('POST')
                                        @csrf
                                        <div class="form-group">User's mobile:
                                            <input name="scope" required type="hidden" value={{$scope??""}} class="form-control input-default" placeholder="center ">
                                            <input name="scope_id" required type="hidden" value="{{$scope_id??""}}" class="form-control input-default" placeholder="center ">
                                            <input name="tx_value_type" required type="hidden" value="{{$tx_value_type??"cash"}}" class="form-control input-default" placeholder="cash">
                                        @if(isset($request_service_id) )
                                            <input name="request_service_id" type="hidden" value="{{$request_service_id }}" class="form-control input-rounded" placeholder="request_service_id">
                                        @endif
                                        </div>
                                        <div class="form-group">Amount
                                            <input name="amount" type="number" min="0" step="50" value="{{old('amount')}}" required class="form-control input-default" placeholder="Amount">
                                        </div>
                                        <div class="form-group">Mobile (optional)
                                            <input name="ref_id" type="text" value="{{old('ref_id')}}" required class="form-control input-default" placeholder="Ref">
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
