@extends('layouts.layout_account')

@section('content')
<div class="content-body" style="min-height: 887px;margin-top:10px;">

                    <div class="col-lg-10" >
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Activation/Deactivation of center/group :</h4>
                                <p class="text-muted m-b-15 f-s-12">This Action will be approved first</p>
                                <div class="basic-form">
                                    <form action="{{route('centers_activation_admin',[$center->id])}}" method="POST">
                                        @method('POST')
                                        @csrf
                                        <div class="form-group">
                                            <input name="id" type="hidden" value="{{ $center->id}}" class="form-control input-default">
                                            <input name="approve_ref_id" type="hidden" value="{{ $approve_ref_id }}" class="form-control input-default">
                                        </div>
                                        <div class="form-group">
                                            <label>Set status</label>
                                            <select name="active_status_edit" class="form-control"  required >
                                                <option value=""> - Select -</option>
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Type:</label>
                                            <select name="service_id_edit" required class="form-control" id="sel1">
                                                <option value=""> - select B usiness Line-</option>
                                                @for($t=0;$t<count($request_types);$t++)
                                                <option value="{{$request_types[$t]->id}}">{{ $request_types[$t]->name." ".$request_types[$t]->description." ".$request_types[$t]->limit_request }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="m-t-20">Overall Limit ( this overtakes default business credit limit if set above zero )</label>
                                            <input required name="credit_limit_edit" type="number" class="form-control" min="0" step="0.1" value="0">
                                        </div>

                                        <div class="form-group">
                                            <label class="m-t-20">Advance Start Date</label>
                                            <input name="advance_start_date_edit" type="text" class="form-control" placeholder="2017-06-04" id="mdate" data-dtp="dtp_QRhpp">
                                        </div>
                                        <div class="form-group">
                                            <label class="m-t-20">Advance End Date</label>
                                            <input name="advance_end_date_edit" type="text" class="form-control" placeholder="2017-06-04" id="mdate" data-dtp="dtp_QRhpp">
                                        </div>

                                        <div class="form-group">
                                            <label class="m-t-20">Advanced Limit</label>
                                            <input required name="advance_limit_rate_edit" type="number" class="form-control" min="0" step="0.1" value="0">
                                        </div>
                                        <div class="form-group">
                                            <label class="m-t-20">Provider Acc Number</label>
                                            <input required name="provider_acc_number_edit" type="number" class="form-control" min="0" step="0.1" value="0">
                                        </div>
                                        <div class="form-group">
                                            <label class="m-t-20">Other</label>
                                            <input required name="other_edit" type="number" class="form-control" min="0" step="0.1" value="0">
                                        </div>
                                        <div class="form-group">
                                            <label class="m-t-20">Provider ID</label>
                                            <input required name="provider_id_edit" type="number" class="form-control" min="0" step="0.1" value="{{ $center->provider_id }}">
                                        </div>
                                        <div class="form-group">
                                            <label class="m-t-20">Your Comments for approval:</label>
                                            <textarea required name="approve_narration" placeholder="Write here..." class="form-control" min="0" step="0.1" value="0"></textarea>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-lg-8 ml-auto">
                                                <button type="submit" name="update" value="update" class="btn btn-primary"> Enter </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
</div>

@endsection
