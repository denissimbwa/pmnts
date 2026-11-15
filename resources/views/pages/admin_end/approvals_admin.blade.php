@extends('layouts.layout_account')

@section('content')

<div class="content-body">

        <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Approvals :</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Remarks</th>
                                                <th>Subject</th>
                                                <th>Status</th>
                                                <th>Approval Count</th>
                                                <th>Reference</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($t=0;$t<count($dta['body']);$t++)
                                            <tr>
                                                <td>{{$dta['body'][$t]->created_at}}</td>
                                                <td>{{$dta['body'][$t]->approve_narration}}</td>
                                                <td>{{$dta['body'][$t]->approve_action}}</td>
                                                <td>{{$dta['body'][$t]->approve_status}}</td>
                                                <td>{{$dta['body'][$t]->approve_count}}</td>
                                                <td>{{$dta['body'][$t]->ref_center_id}}</td>
                                                <td><span><a class="badge badge-primary px-2" href="{{route('approvals_register_admin',[$dta['body'][$t]->id,$dta['body'][$t]->source_table,$dta['body'][$t]->source_id]) }}"> Handle </a></span></td>
                                            </tr>
                                            @endfor
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Date</th>
                                                <th>Remark</th>
                                                <th>Subject</th>
                                                <th>Status</th>
                                                <th>Approval Count</th>
                                                <th>Reference</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #/ container -->
</div>
@endsection