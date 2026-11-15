@extends('layouts.layout_account')

@section('content')

<div class="content-body" style="min-height: 887px; margin-top:20px;">
                    <div class="col-lg-12 col-xl-12" >
                        <div class="card col-xl-12 col-lg-12">
                            <div class="card-body">
                                <h4 class="card-title">Invite user to have access: {{ $dta->name."  |".$dta->id }}</h4>
                                <p class="text-muted m-b-15 f-s-12">A notification will be sent for user to confirm under their account.</p>
                                <div class="basic-form">
                                    <form action="{{ route('invitation_register_client',[$scope,$scope_id])}}">
                                        @method('POST')
                                        @csrf

                                        <div class="form-group">
                                            <input name="access_ref_name" required readonly class="form-control input-rounded" placeholder="Name" value="{{ $dta->name }}">
                                        </div>
                                        <div class="form-group">Country:
                                            <select name="country_code"  required class="form-control">
                                                <option value="256"> Uganda</option>
                                            </select>
                                        </div>
                                        <div class="form-group">Enter User's Mobile
                                            <input name="mobile" required type="disabled" class="form-control input-rounded" placeholder="Mobile" value="">
                                        </div>
                                        <div class="form-group">Select Role:
                                            <select name="role"  required class="form-control">
                                                <option value=""> - Role - </option>
                                                <option value="member"> Member </option>
                                                <option value="admin"> Admin </option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input name="access_ref_id" value="{{ $dta->id }}" type="hidden" class="form-control input-default">
                                            <input name="access_ref_id_type" value="id" type="hidden" class="form-control input-default">
                                            <input name="access_scope" value="{{ $scope }}" type="hidden" class="form-control input-default">
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-8 ml-auto">
                                                <button type="submit" name="submit" value="submit" class="btn btn-primary"> Enter </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
</div>

@endsection
