@extends('layouts.layout_account')

@section('content')

<div class="content-body" style="min-height: 887px;">
                    <div class="col-lg-10" >
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Register For Membership:</h4>
                                <p class="text-muted m-b-15 f-s-12">Detail depends on what the provider's requirements</p>
                                @if($errors->any())
                                <div class="alert alert-danger">{{$errors->first()}}</div>
                                @endif
                                <div class="basic-form">
                                    <form action="{{ route('provider_centers_member_register',['center_id'=>$center_id,'provider_id'=>$provider_id, 'id'=>\Session::get('user')->id ])}}" method="POST">
                                        @csrf
                                        
                                        <input name="provider_id" type="hidden" value="{{ $center->provider_id??""}}" class="form-control input-default">
                                        
                                        <div class="form-group">
                                            <input name="ref_center_id" type="hidden" value="{{ $center->id??old('ref_center_id')??"0"}}" class="form-control input-default">
                                            <input name="owner_id" type="hidden" value="{{ $bio->id??old('owner_id')??"0"}}" class="form-control input-default">
                                        </div>
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input required name="name" type="text" value="{{ $bio->name??old('name')??""}}" class="form-control" placeholder="Name" />
                                        </div>
                                        <div class="form-group">
                                            <label>Mobile</label>
                                            <input required name="mobile" type="text" value="{{ $bio->mobile??old('mobile')??""}}" class="form-control" placeholder="Mobile" />
                                        </div>
                                        <div class="form-group">
                                            <label>Ref ID</label>
                                            <input required name="member_id" type="text" readonly value="{{ $bio->mobile??old('member_id')??""}}" class="form-control" placeholder="ID / Mobile" />
                                        </div>
                                        <div class="form-group">
                                            <label>Address</label>
                                            <input name="address" type="text" value="{{ $bio->address??old('address')}}" class="form-control" placeholder="Address" />
                                        </div>
                                        <div class="form-group">
                                            <label>Referee telephone :</label>
                                            <input name="ref_id" type="text" value="{{ $bio->ref_id??old('ref_id')}}" class="form-control" placeholder="07016...." />
                                        </div>
                                        <h4>Group Specific Items:</h4>
                                        @for($t=1;$t<=5;$t++)
                                        <?php $fld="param".$t;  if($center->$fld==null)continue; ?>
                                        <div class="form-group">
                                            <label>{{ $center->$fld??""}}:</label>
                                            <input name="{{$fld}}" type="text" placeholder="{{ $frm->$fld??""}}" value="{{ $dta->$fld??old($fld)}}"  class="form-control">
                                        </div>
                                        @endfor
                                        
                                        <hr/>Images:<hr/>
                                        @for($t=1;$t<=5;$t++)
                                        <?php $fld="image".$t; if($center->$fld==null)continue; ?>
                                        <div class="form-group">
                                            <label>{{ $center->$fld??""}}:</label>
                                            <input name="{{$fld}}" type="file" value="{{ $dta->$fld??old($fld)}}"  class="form-control">
                                        </div>
                                        @endfor
                                        

                                        @if(isset($dta->id)&&$dta->id!=null)
                                        <div class="form-group">
                                            <label>Active status:</label>
                                            <select name="active_status_edit" required class="form-control" id="sel1">
                                                <option value=""> - Select Active Status -</option>
                                                <option {{ (isset($dta->active_status)&&$dta->active_status==="active")?"selected":""  }} value="active">Active</option>
                                                <option {{ (isset($dta->active_status)&&$dta->active_status==="inactive")?"selected":""  }} value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Limit:</label>
                                            <input name="credit_limit_edit" type="number" value="{{ $dta->credit_limit??old('credit_limit')}}" class="form-control" placeholder="Credit Limit" value="0" />
                                            <input name="id" type="hidden" value="{{ $dta->id??""}}" class="form-control" placeholder="ID" value="0" />
                                        </div>
                                        <div class="form-group">
                                            <label>Change Role:</label>
                                            <select name="role_edit" required class="form-control" id="sel1">
                                                <option value=""> - Select Role -</option>
                                                <option {{ (isset($dta->role)&&$dta->role==="member")?"selected":""  }} value="member">Member</option>
                                                <option {{ (isset($dta->role)&&$dta->role==="admin")?"selected":""  }} value="admin">Admin</option>
                                                <option {{ (isset($dta->role)&&$dta->role==="owner")?"selected":""  }} value="owner">Owner</option>
                                            </select>
                                        </div>
                                        @endif

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
