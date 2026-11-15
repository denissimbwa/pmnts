@extends('layouts.layout_account')

@section('content')
<div class="content-body" style="min-height: 887px;margin-top:10px;">

                    <div class=" col-lg-12 col-xl-12" >
                        <div class="card col-lg-12 col-xl-12">
                            <div class="card-body col-lg-12 col-xl-12">
                                <h4 class="card-title">Preview</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                @for($t=0;$t<count($dta['body']);$t++)
                                   @if($t==0)
                                    <tr style="background-color:grey;color:white;"> <th>{{$dta['body'][$t][0]}}</th>  <th>{{$dta['body'][$t][1]}}</th>  <th>{{$dta['body'][$t][2]}}</th> </tr>
                                   @else
                                    <tr> <td>{{$dta['body'][$t][0]}}</td>  <td>{{$dta['body'][$t][1]}}</td>  <td>{{$dta['body'][$t][2]}}</td> </tr>
                                   @endif
                                @endfor
                                    </table>
                                </div>
                                <span class="card">{{ $dta['approve_detail']->approve_narration }}</span>
                                <h4 class="card-title">Approval Form :</h4>
                                <div class="basic-form">
                                    <form action="{{route((\Session::get('user')->user_rank==='client')?'approvals_register_client':'approvals_register_admin',[$dta['approve_detail']->id,$dta['relation'],$dta['source_id']])}}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <input name="approve_ref_id" value="{{$dta['approve_detail']->approve_ref_id }}" type="hidden" class="form-control input-default">
                                        </div>
                                        <div class="form-group">
                                            <label>Set status</label>
                                            <select name="approve_status" class="form-control"  required >
                                                <option value="approved">Approve</option>
                                                <option value="rejected">Reject</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Any Remarks ?</label>
                                            <textarea required name="approve_narration" class="form-control" placeholder="Comment here..." ></textarea>
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
                        
                        <table class="table card"> Previus Reviews
                            <tr>
                                <th>Date</th>
                                <th>Action</th>
                                <th>Remark</th>
                            </tr>
                            @if(isset($dta['approve_history'])&&!empty($dta['approve_history'])&&$dta['approve_history']!=null )
                            @for($g=0;$g<count($dta['approve_history']);$g++)
                            <tr>
                                <th>{{ $dta['approve_history'][$g]->created_at }}</th>
                                <th>{{ $dta['approve_history'][$g]->approve_action }}</th>
                                <th>{{ $dta['approve_history'][$g]->approve_narration }}</th>
                            </tr>
                            @endfor
                            @endif
                        </table>
                        
                    </div>
</div>

@endsection
