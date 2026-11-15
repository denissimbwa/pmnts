@extends('layouts.layout_account')

@section('content')

<div class="content-body">

        <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Requests Log :  </h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Mobile</th>
                                                <th>Credit Type</th>
                                                <th>Credit</th>
                                                <th>Unpaid</th>
                                                <th>Station</th>
                                                <th>Service Point</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @for($t=1;$t<50;$t++)
                                            <tr>
                                                <td>Tiger Nixon</td>
                                                <td>07017889...</td>
                                                <td>Fuel Credit</td>
                                                <td>11,000</td>
                                                <td>5,000</td>
                                                <td>Ntinda stage</td>
                                                <td>Cityoil</td>
                                            </tr>
                                            <tr>
                                                <td>Ajey Nixon</td>
                                                <td>07010000...</td>
                                                <td>Fuel Credit</td>
                                                <td>11,000</td>
                                                <td>5,000</td>
                                                <td>Bukoto Stage</td>
                                                <td>Petrocity</td>
                                            </tr>
                                            <tr>
                                                <td>Jack</td>
                                                <td>07020000...</td>
                                                <td>Advance Pay</td>
                                                <td>315,000</td>
                                                <td>315,000</td>
                                                <td>Community School</td>
                                                <td>Community School</td>
                                            </tr>
                                            @endfor
                                        </tbody>
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