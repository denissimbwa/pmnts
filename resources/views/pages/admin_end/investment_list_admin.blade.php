@extends('layouts.layout_account')

@section('content')
        <div class="content-body">

                <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>My Investments | </h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Venture</th>
                                                <th>Capital invested</th>
                                                <th>Current capital</th>
                                                <th>Proffit Made</th>
                                                <th>Investor</th>
                                                <th>Provider</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>1</th>
                                                <td>Kampala School (advance-pay)</td>
                                                <td><span class="badge badge-primary px-2">UGX 2,000,000</span></td>
                                                <td>UGX 1,500,000</td>
                                                <td>UGX 300,000</td>
                                                <td class="color-primary">Denis </td>
                                                <td class="color-primary">Community School </td>
                                            </tr>
                                            <tr>
                                                <th>2</th>
                                                <td>Petrocity (Fuel credit)</td>
                                                <td><span class="badge badge-primary px-2">UGX 1,500,000</span></td>
                                                <td>UGX 1,300,000</td>
                                                <td>UGX 100,000</td>
                                                <td class="color-primary"> Simbwa </td>
                                                <td class="color-primary"> Shell Ntinda </td>
                                            </tr>
                                            <tr>
                                                <th>2</th>
                                                <td>City Oil (Fuel credit)</td>
                                                <td><span class="badge badge-primary px-2">UGX 1,500,000</span></td>
                                                <td>UGX 1,300,000</td>
                                                <td>UGX 100,000</td>
                                                <td class="color-primary"> Denis Simbwa </td>
                                                <td class="color-primary"> Cityoil </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /# card -->
                    </div>
</div>

@endsection