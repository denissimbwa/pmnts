@extends('layouts.layout_account')

@section('content')

<?php $approve_ref_id='1243'; ?>

<div class="content-body" style="min-height: 887px; margin-top:10px;">
                    <div class=" col-lg-12 col-xl-12" >
                        <div class="card  col-lg-12 col-xl-12">
                            <div class="card-body">
                                <h4 class="card-title">Set status of member :</h4>
                                <p class="text-muted m-b-15 f-s-12">This will first be approved.</p>
                                <div class="basic-form">
                                @if($errors->any())
                                <div class="alert alert-danger">{{$errors->first()}}</div>
                                @endif
                                    <form action="{{ route('center_member_edit_status',['center_id'=>$dta->ref_center_id,'member_id'=>$dta->member_id]) }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <input name="ref_center_id" type="hidden" value="{{ $dta->ref_center_id }}" required class="form-control input-default">
                                            <input name="user_id" type="hidden" value="{{ $dta->member_id}}" class="form-control input-default">
                                        </div>

                                        <div class="form-group">
                                            <label>Active status:</label>
                                            <select name="active_status_edit" required class="form-control" id="sel1">
                                                <option value=""> - Select Active Status -</option>
                                                <option {{ (isset($dta->active_status)&&$dta->active_status==="active")?"selected":""  }} value="active">Active</option>
                                                <option {{ (isset($dta->active_status)&&$dta->active_status==="inactive")?"selected":""  }} value="inactive">Inactive</option>
                                            </select>
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
                                        <div class="form-group">
                                            <label>Credit Limit:</label>
                                            <input name="credit_limit_edit" type="number" value="{{ $dta->credit_limit??old('credit_limit_edit')}}" class="form-control" placeholder="Credit Limit" value="0" />
                                            <input name="id" type="hidden" value="{{ $dta->id??""}}" class="form-control" placeholder="ID" />
                                        </div>

                                        <div class="form-group">
                                            <label>Give remark:</label>
                                            <textarea name="approve_narration" type="text" class="form-control" rows='2' placeholder="Write here ..." >{{ old('approve_narration')}}</textarea>
                                            <input name="approve_action" type="hidden" value="edit">
                                            <input name="approve_ref_id" type="hidden" value="{{$dta->approve_ref_id}}">
                                            <input name="approve_count" type="hidden" value="{{$dta->approve_count??"2"}}">
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-8 ml-auto">
                                                <input name="approve_action" type="hidden" value="approve" class="form-control" placeholder="Approve" value="0" />
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
