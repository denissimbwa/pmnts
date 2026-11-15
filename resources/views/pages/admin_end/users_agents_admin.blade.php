@extends('layouts.layout_account')

@section('content')

<div class="content-body">

        <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Agents List </h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Station</th>
                                                <th>Cash In hand</th>
                                                <th>Credit Unpaid</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($t=0;$t<20;$t++)
                                            <tr>
                                                <td>Denis Simbwa</td>
                                                <td>256701620575</td>
                                                <td>Ntinda</td>
                                                <td>110,000</td>
                                                <td>100,000</td>
                                                <td>active</td>
                                            </tr>
                                            <tr>
                                                <td>Garrett Winters</td>
                                                <td>256701620575</td>
                                                <td>Ntinda</td>
                                                <td>110,000</td>
                                                <td>100,000</td>
                                                <td>active</td>
                                            </tr>
                                            @endfor
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