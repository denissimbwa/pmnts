@extends('layouts.layout_account')

@section('content')

<div class="content-body" style="min-height: 887px; margin-top:20px;">
                    <div class="col-lg-6" >
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Regisering Service Provider:</h4>
                                <p class="text-muted m-b-15 f-s-12">This one who fullfils the business action.</p>
                                <div class="basic-form">
                                    <form action="{{ route('provider_register') }}" method="POST">
                                        @method('POST')
                                        @csrf

                                        <div class="form-group">
                                            <select name="currency" required class="form-control" id="sel1">
                                                <option value="UGX">UGX</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Active Status:</label>
                                            <select name="active_status" required class="form-control" id="sel1">
                                                <option value=""> - select -</option>
                                                <option value="active">Active</option>
                                                <option value="innactive">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input name="name" required type="text" class="form-control input-default" placeholder="Name of Service provider" value="{{ old('name')}}">
                                        </div>
                                        <label class="m-t-40">Start Time</label>
                                        <div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true">
                                            <input name="open_time" type="text" class="form-control" value="{{ old('open_time')??'06:00'}}"> <span class="input-group-append"><span class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
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
                                        <div class="form-group">Banking Detail
                                            <input name="bank_name" type="text" class="form-control input-default" placeholder="Bank" value="{{ old('bank_name') }}">
                                        </div>
                                        <div class="form-group">
                                            <input name="banK_acc_number" type="text" class="form-control input-default" placeholder="Acc Number" value="{{ old('banK_acc_number') }}">
                                        </div>
                                        <div class="form-group">
                                            <input name="banK_acc_name" type="text" class="form-control input-default" placeholder="Name of Account" value="{{ old('banK_acc_name') }}" >
                                        </div>
                                        <div class="form-group">
                                            <input name="bank_branch" type="text" class="form-control input-default" placeholder="Branch" value="{{ old('bank_branch') }}" >
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
