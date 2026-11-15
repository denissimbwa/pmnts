@extends('layouts.layout_account')

@section('content')


<div class="content-body col-xl-6" style="min-height: 887px; margin-top:20px;">
                    <div class="col-lg-12 col-sm-12 col-xl-12" >
                        <div class="card col-xl-12">
                            <div class="card-body">
                                <div class="text-center">
                                    @if($id==null)
                                    <img alt="" class="rounded-circle mt-4" src="https://zhuris.com/images/logo_1.png">
                                    <h4 class="card-widget__title text-dark mt-3">{{ $member->name_member }}</h4>
                                    <p class="text-muted">{{ $member->member_id }} from {{ $member->name_center }}</p>
                                    @endif
                                    <form action="{{ route('request_credit_client_confirm') }}" method="POST">
                                        @method('POST')
                                        @csrf

                                        @if($id==null)
                                        <div class="form-group">
                                            <input name="ref_center_id" type="hidden" value="{{ $member->ref_center_id }}" required class="form-control input-default" placeholder="Boorwer ID ">
                                            <input name="provider_id" type="hidden" value="{{ $provider_id }}" required class="form-control input-default" placeholder="Boorwer ID ">
                                            <input name="member_id" type="hidden" value="{{ $member->member_id }}" required class="form-control input-default" placeholder="Boorwer ID ">
                                            <input name="tx_id" type="hidden" value="{{ $tx_id }}" required class="form-control input-default" placeholder="Reference No. ">
                                            <input name="request_type_id" value="{{ $service_id }}" type="hidden" required class="form-control input-default" placeholder="Reference No. ">
                                            <input name="request_type" value="{{ $request_type }}" type="hidden" required class="form-control input-default" placeholder="Reference No. ">
                                            <input name="requested_amount" value="{{ $requested_amount }}" type="hidden" required class="form-control input-default" placeholder="Reference No. ">
                                            <input name="detail1" type="hidden" value="{{ $member->mobile_member }}" required class="form-control input-default" placeholder="Reference No. ">
                                            <input name="detail2" type="hidden" value="{{ $member->name_member }}" required class="form-control input-default" placeholder="Reference No. ">
                                        </div>
                                        <div class="form-group">Amount Taken :
                                            <input name="name" required readonly type="hidden" class="form-control input-rounded" placeholder="Name" value="{{ $member->name_member }}">
                                        </div>
                                        <div class="form-group">
                                            <input name="requested_amount" type="text" value="{{ number_format($requested_amount,0) }}" readonly disabled  required class="form-control input-default" placeholder="Amount" >
                                        </div>
                                        @else
                                        <div class="form-group">Enter Code from client: 
                                            <input name="id" type="hidden" value="{{ $id }}" required class="form-control input-default" placeholder="Reference No. ">
                                            <input name="member_id" type="hidden" value="{{ $member_id }}" required class="form-control input-default" placeholder="Reference No. ">
                                            <input name="offered_amount" type="hidden" value="{{ $offered_amount }}" required class="form-control input-default" placeholder="Reference No. ">
                                            <input autocomplete="off" name="request_code" type="text" class="form-control input-rounded" placeholder="Code">
                                        </div>
                                        @endif
                                        @if($unpaid <= 0 )
                                        <div class="form-group row">
                                            <div class="col-lg-12 ml-auto">
                                                @if($id!=null)
                                                <button type="submit" name="update" value="update" class="btn gradient-4 btn-lg border-0 btn-rounded px-5 form-control"> Continue </button>
                                                @else
                                                <button type="submit" name="submit" value="submit" class="btn gradient-4 btn-lg border-0 btn-rounded px-5 form-control"> Enter </button>
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                    </form>
                                    
                                        @if($unpaid > 0 )
                                            <div class="col-lg-12 ml-auto">
                                                <span class="form-contral btn btn-danger">SORRY ! Please Pay <b>{{ number_format($unpaid) }} </b></span>
                                                <hr>
                                                <form action="{{ route('deposit_request_pay') }}" method="POST" >
                                                    @csrf
                                                    <div class="form-group">
                                                        <input name="name" value="{{ $member->name_member }}" type="hidden" required class="form-control input-default" placeholder="Reference No. ">
                                                        <input name="request_type" value="{{ $request_type }}" type="hidden" required class="form-control input-default" placeholder="Reference No. ">
                                                        <input name="member_id" type="hidden" required value="{{ $member->member_id }}" class="form-control input-default" placeholder="Amount" >
                                                        <input name="service_id" type="hidden" required value="{{ $service_id }}" class="form-control input-default" placeholder="Amount" >
                                                        <input name="amount" type="number"min="1" max="{{ $unpaid }}" step="1" required placeholder="{{ $unpaid }}" class="form-control input-default" placeholder="Amount" >
                                                    </div>
                                                    <div class="form-group">
                                                        <select name="tx_value_type" required class="form-control input-default" >
                                                            <option value="">- Select Payment Means -</option>
                                                            <option value="cash"> Cash </option>
                                                            <option value="mmoney"> Mobile Money </option>
                                                        </select>
                                                    </div>
                                                    <button type="submit" name="submit" value="submit" class="btn btn-danger gradient-4 btn-lg border-0 btn-rounded px-5 form-control"> PAY NOW </button>
                                                </form>
                                            </div>
                                        @endif
                                    

                                </div>
                            </div>
                            <div class="card-footer border-0 bg-transparent">
                                <div class="row">
                                    <div class="col-4 border-right-1 pt-3">
                                        <a class="text-center d-block text-muted" href="javascript:void()">
                                            From:
                                            <p class="">STATION A</p>
                                        </a>
                                    </div>
                                    <div class="col-4 border-right-1 pt-3"><a class="text-center d-block text-muted" href="javascript:void()">
                                            <p class=""><a class="badge badge-success" href=""> Clear </a></p>
                                        </a>
                                    </div>
                                    <div class="col-4 pt-3"><a class="text-center d-block text-muted" href="javascript:void()">
                                        By:
                                            <p class=""> {{ \Session::get('user')->name }} </p>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


</div>

@endsection
