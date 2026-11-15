@extends('layouts.layout_account')

@section('content')

<div class="content-body" style="min-height: 887px; margin-top:20px;">
                    <div class="col-lg-6" >
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Invite user to have access:</h4>
                                <p class="text-muted m-b-15 f-s-12">A notification will be sent for user to confirm under their account.</p>
                                <div class="basic-form">
                                    <form action="{{ route('invitation_list')}}">
                                        @method('POST')
                                        @csrf

                                        <div class="form-group">
                                            <input name="access_ref_name" required readonly class="form-control input-rounded" placeholder="Mobile" value="The Cousins SACCO">
                                        </div>
                                        <div class="form-group">Entger User's Mobile
                                            <input name="mobile" required type="disabled" class="form-control input-rounded" placeholder="Mobile" value="0701620575">
                                        </div>
                                        <div class="form-group">Select Role:
                                            <select name="role"  required class="form-control">
                                                <option value=""> - Role - </option>
                                                <option value="member"> Member </option>
                                                <option value="admin"> Admin </option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input name="access_ref_id" type="hidden" class="form-control input-default">
                                            <input name="access_ref_id_type" type="hidden" class="form-control input-default">
                                            <input name="access_scope" type="hidden" class="form-control input-default">
                                            <input type="number" readonly  min="0" step="50" required class="form-control input-default" placeholder="Amount" value="100000">
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
