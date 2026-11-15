@extends('layouts.layout_account')

@section('content')
<div class="content-body" style="min-height: 887px;margin-top:10px;">
<!-- HANDLING CAMPAIGN -->
@if(isset($dta['id']) && $dta['service_type']==="collection")
                    <div class="col-lg-10" >
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Setting Up Contribution Plan</h4>
                                <p class="text-muted m-b-15 f-s-12">Cash to be collected regularly either by cash or via mobile money</p>
                                <div class="basic-form">
                                    <form action="{{route('collection_schedule_register',[$dta['scope_id'],$dta['service_type'],$dta['id'] ])}}" method="POST">
                                        @method('POST')
                                        @csrf
                                        <div class="form-group">
                                            <input name="id" type="hidden" value="{{ $dta['id'] }}" class="form-control input-default">
                                            <input name="ref_center_id" type="hidden" value="{{ $dta['scope_id'] }}" class="form-control input-default">
                                            <input name="approve_ref_id" type="hidden" value="{{ $dta['approve_ref_id'] }}" class="form-control input-default">
                                        </div>
                                        <div class="form-group">
                                            <label>Amount to be paid by every member:</label>
                                            <input required name="amount_edit" type="number" value="{{ (isset($dta['id'] ))? $campaign->amount : old('amount_edit') }}" min="0" step="100" class="form-control" placeholder="0" />
                                        </div>
                                        <div class="col-md-6">
                                            <label class="m-t-20">Start Date</label>
                                            <input name="start_date_edit" type="text" value="{{ (isset($dta['id'] ))? $campaign->start_date : old('start_date_edit') }}" class="form-control" placeholder="2017-06-04" id="mdate" data-dtp="dtp_I3CSZ">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="m-t-20">Interval in Days</label>
                                            <select name="interval_edit" class="form-control">
                                                <option value="0" >- No Scheuled -</option>
                                                @for($t=1; $t<=30;$t++ )
                                                <option value="{{ $t }}" >{{$t}} Days</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Any Remarks ?</label>
                                            <textarea name="approve_narration" class="form-control" placeholder="Write here...">{{ old('approve_narration') }}</textarea>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-8 ml-auto">
                                                <button name="update" value="update" type="submit" class="btn btn-primary"> Enter </button>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="card">
                                        <p> Set amount to zero or plan to non to clear schedule ! </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


<!-- HANDLING CAMPAIGN -->
@elseif($dta['service_type']==="campaign")

                    <div class="col-lg-10" >
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Setting Up Campaign For Open Contribution Plan</h4>
                                <p class="text-muted m-b-15 f-s-12">Cash shall be via both cash and mobile money</p>
                                <div class="basic-form">
                                    <form action="{{route('collection_schedule_register',[$dta['scope_id'],$dta['service_type'] ])}}" method="POST">
                                        @method('POST')
                                        @csrf
                                        <div class="form-group">
                                            @if(isset($dta['id']))
                                            <input name="id" type="hidden" value="{{ $dta['id'] }}" class="form-control input-default">
                                            @endif
                                            <input name="ref_center_id" type="hidden" value="{{ $dta['scope_id'] }}" class="form-control input-default">
                                            <input name="approve_ref_id" type="hidden" value="{{ $dta['approve_ref_id'] }}" class="form-control input-default">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Set status</label>
                                            <select name="active_status_edit" class="form-control"  required >
                                                <option value="">- Set Status -</option>
                                                <option value="active">Active</option>
                                                <option value="inactive">Disabled</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Subject Title</label>
                                            <input name="name_edit" type="text" value="{{ (isset($dta['id'] ))? $campaign->name : old('name_edit') }}" class="form-control input-default">
                                        </div>
                                        <div class="form-group">
                                            <label>Target amount:</label>
                                            <input required name="amount_edit" type="number" value="{{ (isset($dta['id'] ))? $campaign->amount : old('amount_edit') }}" min="0" step="100" class="form-control" placeholder="0" />
                                        </div>
                                        <div class="form-group">
                                            <label>Detail of topic:</label>
                                            <textarea required name="description_edit" class="form-control" placeholder="Write here...">{{ (isset($dta['id'] ))? $campaign->description : old('description_edit') }}</textarea>
                                        </div>
                                        <div class="form-group">Image (optional) 
                                            <input name="image1_edit" type="file" class="form-control-file">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="m-t-20">Start Date</label>
                                            <input name="start_date_edit" type="text" value="{{ (isset($dta['id'] ))? $campaign->start_date : old('start_date_edit') }}" class="form-control" placeholder="2017-06-04" id="mdate" data-dtp="dtp_I3CSZ">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="m-t-20">End Date</label>
                                            <input name="end_date_edit" type="text" value="{{ (isset($dta['id'] ))? $campaign->end_date : old('end_date_edit') }}" class="form-control" placeholder="2017-06-04" id="mdate" data-dtp="dtp_I3CSZ">
                                        </div>
                                        <div class="form-group">
                                            <label>Why do you want this campaign online ?</label>
                                            <textarea required name="approve_narration" class="form-control" placeholder="Write here...">{{ old('approve_narration') }}</textarea>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-8 ml-auto">
                                                @if(isset($dta['id'] ))
                                                <button name="update" value="update" type="submit" class="btn btn-primary"> Enter </button>
                                                @else
                                                <button name="submit" value="submit" type="submit" class="btn btn-primary"> Enter </button>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                    <div class="card">
                                        <p> Campaigns start operatinf after approval is done from central office ! </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
@endif 
                    
                    
</div>

@endsection
