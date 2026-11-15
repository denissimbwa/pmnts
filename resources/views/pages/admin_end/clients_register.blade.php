@extends('layouts.layout_account')

@section('content')

<div class="content-body" style="min-height: 887px; margin-top:20px;">
                    <div class="col-lg-6" >
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Regisering API client:</h4>
                                <p class="text-muted m-b-15 f-s-12">basic information :</p>
                                <div class="basic-form">
                                    <form action="{{ route('clients_register') }}" method="POST">
                                        @method('POST')
                                        @csrf
                                        <div class="form-group">
                                            <label>Active Status:</label>
                                            <select name="active_status" required class="form-control" id="sel1">
                                                <option value=""> - select -</option>
                                                <option value="active">Active</option>
                                                <option value="innactive">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input name="name" required type="text" class="form-control input-default" placeholder="Name of Service provider" value="{{ $dta->name ?? old('name')}}">
                                        </div>
                                        <label class="m-t-40">Start Time</label>
                                        <div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true">
                                            <input name="open_time" type="text" class="form-control" value="{{ $dta->open_time ?? old('open_time')??'06:00'}}"> <span class="input-group-append"><span class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
                                        </div>
                                        <label class="m-t-40">CLose Time</label>
                                        <div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true">
                                            <input name="close_time" type="text" class="form-control" value="{{ old('close_time')??'18:00'}}"> <span class="input-group-append"><span class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
                                        </div>
                                        <hr/>
                                        <div class="form-group">
                                            <input name="mobile" required type="text" class="form-control input-default" placeholder="Mobile" value="{{ old('mobile') }}" placeholder="0701....">
                                        </div>
                                        <div class="form-group">Location:
                                            <input name="address" required type="text" class="form-control input-default" placeholder="Address" value="{{ old('address') }}">
                                        </div>
                                        <div class="form-group">Connection Detail
                                            <input name="website" type="text" class="form-control input-default" placeholder="Website" value="{{ old('website') }}">
                                        </div>
                                        <div class="form-group">
                                            <input name="call_back_url" type="text" class="form-control input-default" placeholder="Call back URL" value="{{ old('call_back_url') }}">
                                        </div>
                                        <div class="form-group">
                                            <input name="access_code" type="text" class="form-control input-default" placeholder="ACCESS CODE" value="{{ old('access_code') }}" >
                                        </div>
                                        <div class="form-group">Deposite Charges
                                            <input name="tx_fee_dp" type="number" class="form-control input-default" placeholder="Transaction Fees" value="{{ old('tx_fee_dp') }}" >
                                        </div>
                                        <div class="form-group">Withdraw Charges
                                            <input name="tx_fee_wd" type="number" class="form-control input-default" placeholder="Transaction Fees" value="{{ old('tx_fee_wd') }}" >
                                        </div>
                                        <div class="form-group">Attachment (optional) 
                                            <input name="image" type="file" class="form-control-file">
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-8 ml-auto">
                                                <button type="submit" name="submit" value="submit" class="btn btn-primary"> Enter </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
</div>

@endsection
