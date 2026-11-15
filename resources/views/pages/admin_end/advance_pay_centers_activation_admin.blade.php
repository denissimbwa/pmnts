@extends('layouts.layout_account')

@section('content')
<div class="content-body" style="min-height: 887px;margin-top:10px;">

                    <div class="col-lg-10" >
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Activation of Advance Pay :</h4>
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
                                            <label class="m-t-20">Provider Acc</label>
                                            <input type="text" class="form-control" placeholder="Provider ID" >
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

                    </div>
</div>

@endsection
