@extends('layouts.layout_account')

@section('content')

        <!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body" style="margin-top:10px;">



        <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Settings</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Wd Max</th>
                                                <th>Dp Max</th>
                                                <th>Wd Charge</th>
                                                <th>Dp Charge</th>
                                                <th>Max Group Size</th>
                                                <th>Min Group Size</th>
                                                <th>Inter-tx delay (sec)</th>
                                                <th>Admin Approvals</th>
                                                <th>Set New</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>1</th>
                                                <td><input type="text" disabled class="input input-round" value="2022-10-31"></td>
                                                <td><input name="wd_max_edit" type="text" class="input input-round"></td>
                                                <td><input type="dp_max_edit" class="input input-round"></td>
                                                <td><input type="wd_charge_edit" class="input input-round"></td>
                                                <td><input type="dp_charge_edit" class="input input-round"></td>
                                                <td><input type="max_group_size_edit" class="input input-round"></td>
                                                <td><input type="min_group_size_edit" class="input input-round"></td>
                                                <td><input type="tx_delay_interval_edit" class="input input-round"></td>
                                                <td><input type="approval_count_edit" class="input input-round"></td>
                                                <td><input type="submit" class="btn btn-danger" value="ENTER"></td>
                                            </tr>
                                            <tr>
                                                <th>1</th>
                                                <td>2022-10-31</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>10</td>
                                                <td>3</td>
                                                <td>60</td>
                                                <td>2</td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <th>1</th>
                                                <td>2022-10-31</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>0</td>
                                                <td>10</td>
                                                <td>3</td>
                                                <td>60</td>
                                                <td>2</td>
                                                <td>&nbsp;</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                        <!-- /# card -->
                    </div>

@endsection
