@extends('layouts.layout_account')

@section('content')

<div class="content-body" style="min-height: 887px; margin-top:20px;">
                    <div class="col-lg-6" >
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Business Line Selection:</h4>
                                <p class="text-muted m-b-15 f-s-12">Sett parameteres for business line.</p>
                                <div class="basic-form">
                                    <form action="{{ route('provider_services_selection',[$dta->id]) }}">
                                        @method('POST')
                                        @csrf

                                        <input name="id" type="hidden" class="form-control input-default" placeholder="" value="{{ $dta->id}}" />
                                        <input name="approve_action" type="hidden" class="form-control input-default" placeholder="" value="approve" />
                                        <input name="approve_ref_id" type="hidden" class="form-control input-default" placeholder="" value="{{$approve_ref_id}}" />

                                        <input name="bank_name_edit" type="hidden" class="form-control input-default" placeholder="" value="{{ $dta->bank_name}}" />
                                        <input name="banK_acc_number_edit" type="hidden" class="form-control input-default" placeholder="" value="{{$dta->banK_acc_number}}" />
                                        <input name="banK_acc_name_edit" type="hidden" class="form-control input-default" placeholder="" value="{{$dta->banK_acc_name}}" />
                                        <input name="bank_branch_edit" type="hidden" class="form-control input-default" placeholder="" value="{{$dta->bank_branch}}" />

                                        <div class="form-group">
                                            <label>Availability for Investment:</label>
                                            <select name="active_status_invest_edit" required class="form-control" id="sel1">
                                                <option value=""> - select -</option>
                                                <option value="available">Available</option>
                                                <option value="closed">Closed</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Active Status:</label>
                                            <select name="active_status_edit" required class="form-control" id="sel1">
                                                <option value=""> - select -</option>
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="form-group">Investor's account
                                            <input name="name_edit" type="text" class="form-control input-default" placeholder="Provider Name" value="{{ $dta->name_provider}}" />
                                        </div>
                                        <label class="m-t-40">Start Time</label>
                                        <div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true">
                                            <input name="open_time_edit" type="text" class="form-control" value="{{ $dta->open_time }}"> <span class="input-group-append"><span class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
                                        </div>
                                        <label class="m-t-40">CLose Time</label>
                                        <div class="input-group clockpicker" data-placement="bottom" data-align="top" data-autoclose="true">
                                            <input name="close_time_edit" type="text" class="form-control" value="{{ $dta->close_time }}"> <span class="input-group-append"><span class="input-group-text"><i class="fa fa-clock-o"></i></span></span>
                                        </div>
                                        <hr/>
                                        <div class="form-group">
                                            <input name="mobile_edit" required type="text" class="form-control input-default" placeholder="Mobile" value="{{ $dta->mobile_provider }}" placeholder="0701....">
                                        </div>
                                        <div class="form-group">Location:
                                            <input name="address_edit" required type="text" class="form-control input-default" placeholder="Address" value="{{ $dta->address_provider }}">
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
                                        <div class="form-group">Investor's account ( selected: {{ $dta->acc_number_investor_selected }})
                                            <input name="acc_number_investor_edit" type="text" class="form-control input-default" placeholder="Investor Acc Number" value="{{ $dta->acc_number_investor}}" />
                                        </div>
                                        <div class="form-group">Confirm Investor's Account
                                            <input name="acc_number_investor_confirm" type="text" class="form-control input-default" placeholder="Investor Acc Number" value="{{ $dta->acc_number_investor}}" />
                                        </div>
                                        <div class="form-group">Interest Rendered to office
                                            <input name="interest_business_edit" type="number" min="0" step="0.1" class="form-control input-default" placeholder="Office Charge" value="{{ $dta->interest_business}}" />
                                        </div>
                                        <div class="form-group">Interest Rendered to agent
                                            <input name="interest_agent_edit" type="number" min="0" step="0.1" class="form-control input-default" placeholder="Agent Charge" value="{{ $dta->interest_agent}}" />
                                        </div>
                                        <div class="form-group">Interest Rendered to chairmen or center head
                                            <input name="interest_center_head_edit" type="number" min="0" step="0.1" class="form-control input-default" placeholder="Chairman Charge" value="{{ $dta->interest_center_head}}" />
                                        </div>
                                        <div class="form-group">Remark
                                            <textarea name="approve_narration" class="form-control input-default" placeholder="Remark..."></textarea>
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
