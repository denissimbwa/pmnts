@extends('layouts.layout_account')

@section('content')

<div class="content-body" style="min-height: 887px; margin-top:20px;">
                    <div class="col-lg-6" >
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Business Line registry:</h4>
                                <p class="text-muted m-b-15 f-s-12">Sett parameteres for business line.</p>
                                <div class="basic-form">
                                    <form action="{{ route('finish_action') }}">
                                        @method('POST')
                                        @csrf

                                        <div class="form-group">
                                            <input type="text" required readonly class="form-control" placeholder="Date" value="{{ date('Y-m-j') }}" id="mdate" data-dtp="dtp_wrS5f">
                                        </div>
                                        <div class="form-group">
                                            <label>Currency:</label>
                                            <select name="currency" required class="form-control" id="sel1">
                                                <option value=""> - select -</option>
                                                <option value="UGX">UGX</option>
                                                <option value="KSH">KSH</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Request Type:</label>
                                            <select name="request_type" required class="form-control" id="sel1">
                                                <option value=""> - select -</option>
                                                <option value="advance">Advance</option>
                                                <option value="collection">Collection</option>
                                                <option value="credit">Credit</option>
                                                <option value="provider">Provider</option>
                                                <option value="campaign">Campaign</option>
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
                                            <input name="name" required type="text" class="form-control input-default" placeholder="Name of business Line" value="">
                                        </div>
                                        <div class="form-group">Default Limit:
                                            <input name="limit" required type="number" step="10" class="form-control input-default" placeholder="Cash Limit" value="10000" placeholder="0">
                                        </div>
                                        <div class="form-group">Default Interest:
                                            <input name="interest" required type="number" step="0.1" class="form-control input-default" placeholder="Cash Limit" value="10" placeholder="0">
                                        </div>
                                        <div class="form-group">Days of operation:
                                            <input name="duration_days" required type="number" step="1" class="form-control input-default" placeholder="Days" value="1">
                                        </div>
                                        <div class="form-group">Grace Perriod:
                                            <input name="grace_period" required type="number" step="1" class="form-control input-default" placeholder="Days" value="0">
                                        </div>
                                        <div class="form-group">Charge per extra day - Flat fee:
                                            <input name="penalty" required type="number" step="1" class="form-control input-default" placeholder="Penalty fee" value="0">
                                        </div>

                                        <div class="form-group">Description
                                            <textarea name="description" class="form-control input-default" placeholder="Description..."></textarea>
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
