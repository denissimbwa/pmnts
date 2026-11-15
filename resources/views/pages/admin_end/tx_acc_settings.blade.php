@extends('layouts.layout_account')

@section('content')
<div class="content-body" style="min-height: 887px;margin-top:10px;">

                    <div class=" col-lg-12 col-xl-12" >
                        <div class="card col-lg-12 col-xl-12">
                            <div class="card-body col-lg-12 col-xl-12">
                                
                                
                                @if(isset($acc_detail) && isset($approve_ref_id))
                                <h4 class="card-title">Transacting Acccount Settings</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                    <tr style="background-color:grey;color:white;"> <th> ITEM </th>  <th>VALUE</th>  </tr>
                                    <tr> <td>Date</td>  <td>{{ $acc_detail->created_at }}</td> </tr>
                                    <tr> <td>Acc Name</td>  <td>{{ $acc_detail->acc_name }}</td> </tr>
                                    <tr> <td>Description</td>  <td>{{ $acc_detail->acc_description }}</td> </tr>
                                    <tr> <td>Value</td>  <td>{{ $acc_detail->tx_value_type }}</td> </tr>
                                    <tr> <td>Currency</td>  <td>{{ $acc_detail->currency }}</td> </tr>
                                    <tr> <td>Channel</td>  <td>{{ $acc_detail->tx_channel }}</td> </tr>
                                    <tr> <td>Status</td>  <td>{{ $acc_detail->active_status }}</td> </tr>
                                    <tr> <td>Category</td>  <td>{{ $acc_detail->acc_category }}</td> </tr>
                                    <tr> <td>invite Code</td>  <td>{{ $acc_detail->invite_code }}</td> </tr>
                                    <tr> <td>User ID</td>  <td>{{ $acc_detail->user_id }}</td> </tr>
                                    </table>
                                </div>
                                <h4 class="card-title">Approval Form :</h4>
                                <span class="card">Format is : 1000-100,500-300 :: max1-chage,max2-charge2,max3-charge3</span>
                                <div class="basic-form">
                                    <form action="{{route('tx_acc_settings',[$acc_detail->acc_number]) }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <input name="id" value="{{$acc_detail->id }}" type="hidden" class="form-control input-default">
                                            <input name="approve_ref_id" value="{{$approve_ref_id }}" type="hidden" class="form-control input-default">
                                        </div>
                                        <div class="form-group">Withdraw Charge
                                            <input required name="wd_charges_edit" value="{{ $acc_detail->wd_charges }}" type="text" placeholder='1000-100,500-300' class="form-control input-default">
                                        </div>

                                        <div class="form-group">Maximum Withdraw Per Day
                                            <input required name="wd_max_day_edit" value="{{ $acc_detail->wd_max_day }}" type="text" placeholder='1000-100,500-300' class="form-control input-default">
                                        </div>

                                        <div class="form-group">API Withdraw charge
                                            <input required name="wd_api_fee_edit" value="{{ $acc_detail->wd_api_fee }}" type="text" placeholder='1000-100,500-300' class="form-control input-default">
                                        </div>

                                        <div class="form-group">Deposit Charge
                                            <input required name="dp_charges_edit" value="{{ $acc_detail->dp_charges }}" type="text" placeholder='1000-100,500-300' class="form-control input-default">
                                        </div>

                                        <div class="form-group">Maximum Deposit Per Day
                                            <input required name="dp_max_day_edit" value="{{ $acc_detail->dp_max_day }}" type="text" placeholder='100000' class="form-control input-default">
                                        </div>

                                        <div class="form-group">API deposit charge
                                            <input required name="dp_api_fee_edit" value="{{ $acc_detail->dp_api_fee }}" type="text" placeholder='1000-100,500-300' class="form-control input-default">
                                        </div>

                                        <div class="form-group">
                                            <select required name="active_status_edit" required class="form-control">
                                                <option value="" >- Status -</option>
                                                <option value="active" > Active</option>
                                                <option value="inactive" > Inactive</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Any Remarks ?</label>
                                            <textarea required name="approve_narration" class="form-control" placeholder="Comment here..." ></textarea>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-8 ml-auto">
                                                <button type="submit" name="update" value="update" class="btn btn-primary"> Enter </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                    </div>
</div>



@endsection
