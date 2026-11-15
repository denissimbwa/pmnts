@extends('layouts.layout_account')

@section('content')
<div class="content-body" style="min-height: 887px;margin-top:10px;">

                    <div class=" col-lg-12 col-xl-12" >
                        <div class="card col-lg-12 col-xl-12">
                            <div class="card-body col-lg-12 col-xl-12">
                                
                                
                                @if(isset($tx_detail) && isset($approve_ref_id))
                                <h4 class="card-title">Preview</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                    <tr style="background-color:grey;color:white;"> <th> ITEAM </th>  <th>VALUE</th>  </tr>
                                    <tr> <td>Date</td>  <td>{{ $tx_detail->created_at }}</td> </tr>
                                    <tr> <td>Channel</td>  <td>{{ $tx_detail->tx_channel }}</td> </tr>
                                    <tr> <td>Value Type</td>  <td>{{ $tx_detail->tx_value_type }}</td> </tr>
                                    <tr> <td>Amoutn</td>  <td>{{ $tx_detail->ref_amount }}</td> </tr>
                                    <tr> <td>Currency</td>  <td>{{ $tx_detail->currency_source." - ".$tx_detail->currency_destination }}</td> </tr>
                                    <tr> <td>Tx Type</td>  <td>{{ $tx_detail->tx_type }}</td> </tr>
                                    <tr> <td>Status</td>  <td>{{ $tx_detail->tx_status }}</td> </tr>
                                    <tr> <td>Ref ID</td>  <td>{{ $tx_detail->ref_id }}</td> </tr>
                                    <tr> <td>Source Acc</td>  <td>{{ $tx_detail->source_account }}</td> </tr>
                                    <tr> <td>Dest Acc</td>  <td>{{ $tx_detail->dest_account }}</td> </tr>
                                    <tr> <td>Tx Charge</td>  <td>{{ number_format($tx_detail->tx_fee) }}</td> </tr>
                                    <tr> <td>API Fee</td>  <td>{{ number_format($tx_detail->api_fee) }}</td> </tr>
                                    <tr> <td>Recovery Flag</td>  <td>{{ $tx_detail->recovery_flag }}</td> </tr>
                                    <tr> <td>Owner Type</td>  <td>{{ $tx_detail->tx_owner_type }}</td> </tr>
                                    <tr> <td>Owner ID</td>  <td>{{ $tx_detail->tx_owner_id }}</td> </tr>
                                    <tr> <td>Office Account</td>  <td>{{ $tx_detail->acc_number_office }}</td> </tr>
                                    <tr> <td>Approve Narration</td>  <td>{{ $tx_detail->approve_narration }}</td> </tr>
                                    <tr> <td>Last Update</td>  <td>{{ $tx_detail->updated_at }}</td> </tr>
                                    <tr> <td>Param 1</td>  <td>{{ $tx_detail->param1 }}</td> </tr>
                                    <tr> <td>Param 2</td>  <td>{{ $tx_detail->param2 }}</td> </tr>
                                    </table>
                                </div>
                                <span class="card">{{ $tx_detail->approve_narration }}</span>
                                <h4 class="card-title">Approval Form :</h4>
                                <div class="basic-form">
                                    <form action="{{route('push_transaction') }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <input name="id" value="{{$tx_detail->id }}" type="hidden" class="form-control input-default">
                                            <input name="approve_ref_id" value="{{$approve_ref_id }}" type="hidden" class="form-control input-default">
                                        </div>

                                        <div class="form-group">
                                            <select name="status" required class="form-control">
                                                <option value="" >- Status -</option>
                                                <option value="pending" > Pending</option>
                                                <option value="success" > Success</option>
                                                <option value="failed" > Failed</option>
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
                                
                                @else
                                <h4 class="card-title">Enter Transaction Number :</h4>
                                <div class="basic-form">
                                    <form action="{{route('push_transaction') }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <input name="tx_id" type="text" value="{{ old('tx_id') }}" class="form-control input-default">
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-lg-8 ml-auto">
                                                <button type="submit" name="update" value="update" class="btn btn-primary"> Look Up </button>
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
