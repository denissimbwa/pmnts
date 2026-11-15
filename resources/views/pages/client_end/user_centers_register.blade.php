@extends('layouts.layout_account')

@section('content')

<div class="content-body" style="min-height: 887px;">
                    <div class="col-lg-10" >
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Register New Group</h4>
                                <p class="text-muted m-b-15 f-s-12">Give detail about this group.</p>
                                <div class="basic-form">
                                    <form action="{{ route('user_centers_register')}}" method="POST" >
                                        @csrf
                                        @if(isset($dta->id)&&$dta->id!=null)
                                        <div class="form-group">
                                            <input name="id" type="hidden" value="{{ $dta->id }}" class="form-control input-default">
                                        </div>
                                        @endif

                                        @if(!isset($dta->id)||$dta->id==null)
                                        <div class="form-group">
                                            <input name="center_code" type="text" value="{{ $dta->center_code }}" class="form-control input-default">
                                        </div>
                                        <div class="form-group">
                                            <label>Select Currency:</label>
                                            <select name="currency" required class="form-control" id="sel1">
                                                <option value=""> - Currency -</option>
                                                <option {{ (isset($dta->center_type)&&$dta->center_type==="UGX")?"selected":""  }} value="UGX">UGX</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Select group type:</label>stage - pool - sacco - organization - advancepay - group
                                            <select name="center_type" required class="form-control" id="sel1">
                                                <option value=""> - select -</option>
                                                <option {{ (isset($dta->center_type)&&$dta->center_type==="group")?"selected":""  }} value="group">GROUP</option>
                                                <option {{ (isset($dta->center_type)&&$dta->center_type==="advancepay")?"selected":""  }} value="advancepay">ADDVANCE PAY</option>
                                                <option {{ (isset($dta->center_type)&&$dta->center_type==="stage")?"selected":""  }} value="stage">STAGE</option>
                                                <option {{ (isset($dta->center_type)&&$dta->center_type==="market")?"selected":""  }} value="market">MARKET</option>
                                            </select>
                                        </div>
                                        @endif
                                        <div class="form-group">
                                            <input name="name" type="text" value="{{ $dta->name??""}}" class="form-control input-default" placeholder="Group Name">
                                        </div>
                                        <div class="form-group">
                                            <input name="mobile" type="text" value="{{ $dta->mobile??""}}" class="form-control" placeholder="Mobile ">
                                        </div>
                                        <div class="form-group">
                                            <input name="address" type="text" value="{{ $dta->address??""}}" class="form-control" placeholder="Address">
                                        </div>

                                        <div class="form-group">logo (optional) 
                                            <input name="image_center1" type="file" class="form-control-file">
                                        </div>
                                        <div class="form-group">Image 2 (optional)
                                            <input name="image_center2" type="file" class="form-control-file">
                                        </div>
                                        <div class="form-group">Image 3 (optional)
                                            <input name="image_center3" type="file" class="form-control-file">
                                        </div>
                                        
                                        <h4>Data captured from members e.g NIN, next of KIN, etc. Leave blank if not necessary</h4>
                                        @for($t=1;$t<=5;$t++)
                                        <?php $fld="param".$t; ?>
                                        <div class="form-group">
                                            <label>Question {{$t}}:</label>
                                            <input name="{{$fld}}" type="text" value="{{ $dta->$fld??""}}"  class="form-control">
                                        </div>
                                        @endfor
                                        <hr/>Will you require images ? Enter their description e.g small photo, membership ID etc<hr/>
                                        @for($t=1;$t<=5;$t++)
                                        <?php $fld="image".$t; ?>
                                        <div class="form-group">
                                            <label>Image Name {{$t}}:</label>
                                            <input name="{{$fld}}" type="text" value="{{ $dta->$fld??""}}"  class="form-control">
                                        </div>
                                        @endfor
                                        <div class="form-group row">
                                            <div class="col-lg-8 ml-auto">
                                                <button type="submit" name="{{ (isset($dta->id)&&$dta->id!=null)?"update":"submit" }}" value="{{ (isset($dta->id)&&$dta->id!=null)?"update":"submit" }}" class="btn btn-primary"> Enter </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
</div>

@endsection
