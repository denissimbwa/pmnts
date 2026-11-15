@extends('layouts.layout_account')

@section('content')
<div class="content-body" style="min-height: 887px;margin-top:10px;">

                    <div class="col-lg-10" >
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Credit Schedule Form :</h4>
                                <p class="text-muted m-b-15 f-s-12">This Action will be approved first</p>
                                <div class="basic-form">
                                    <form action="{{route('finish_action')}}" method="POST">
                                        @method('POST')
                                        @csrf
                                        <div class="form-group">
                                            <input name="center_code" type="hidden" class="form-control input-default">
                                            <input name="user_id" type="hidden" class="form-control input-default">
                                        </div>
                                        <div class="form-group">
                                            <label>Set status</label>
                                            <select class="form-control"  required >
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="m-t-20">Interest</label>
                                            <input required type="number" class="form-control" min="0" step="0.1" value="5">
                                        </div>
                                        <div class="form-group">
                                            <label class="m-t-20">Start Date</label>
                                            <input type="text" class="form-control" placeholder="2017-06-04" id="mdate" data-dtp="dtp_QRhpp">
                                        </div>
                                        <div class="form-group">
                                            <label class="m-t-20">End Date</label>
                                            <input type="text" class="form-control" placeholder="2017-06-04" id="mdate" data-dtp="dtp_QRhpp">
                                        </div>

                                        <div class="form-group">
                                            <label>Comment</label>
                                            <textarea required name="remark" class="form-control" placeholder="Comment here..." ></textarea>
                                        </div>
                                        <div class="form-group">Attachment (optional) 
                                            <input name="image_center1" type="file" class="form-control-file">
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-8 ml-auto">
                                                <button type="submit" class="btn btn-primary"> Enter </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <table class="table card"> Previus Schedule
                            <tr>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Credit Taken</th>
                                <th>Unpaid</th>
                            </tr>
                            <tr>
                                <th>2022-10-01</th>
                                <th>2022-11-25</th>
                                <th>5,000,000</th>
                                <th>0</th>
                            </tr>
                        </table>
                        
                    </div>
</div>

@endsection
