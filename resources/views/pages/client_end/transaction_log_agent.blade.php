@extends('layouts.layout_account')

@section('content')

<div class="content-body">

        <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Cash= 22,000 | Mmoney= 300,000 &nbsp;<a class="btn btn-primary" href="{{route('deposit_bank')}}"> Bank Cash </a> </h4>
                                Agent - Denis
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Activity</th>
                                                <th>Name</th>
                                                <th>Ref ID</th>
                                                <th>Mobile</th>
                                                <th>Provider</th>
                                                <th>Amount</th>
                                                <th>Currency</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>2022-10-26</td>
                                                <td>request</td>
                                                <td>Denis Simbwa</td>
                                                <td>UAR672A</td>
                                                <td>0701620575</td>
                                                <td>Petrocity Ntinda</td>
                                                <td>-10,000</td>
                                                <td>UGX</td>
                                                <td>success -<a href="{{route('transaction_detail_preview')}}"><span class="badge badge-success px-2"> View </span></a></td>
                                            </tr>
                                            <tr>
                                                <td>2022-10-26</td>
                                                <td>cash</td>
                                                <td>Denis Simbwa</td>
                                                <td>UAR672A</td>
                                                <td>0701620575</td>
                                                <td>Petrocity Ntinda</td>
                                                <td>11,000</td>
                                                <td>UGX</td>
                                                <td>success -<a href="{{route('transaction_detail_preview')}}"><span class="badge badge-success px-2"> View </span></a></td>
                                            </tr>
                                            <tr>
                                                <td>2022-10-26</td>
                                                <td>request</td>
                                                <td>Denis Simbwa</td>
                                                <td>UAR672A</td>
                                                <td>0701620575</td>
                                                <td>Petrocity Ntinda</td>
                                                <td>-10,000</td>
                                                <td>UGX</td>
                                                <td>success -<a href="{{route('transaction_detail_preview')}}"><span class="badge badge-success px-2"> View </span></a></td>
                                            </tr>
                                            <tr>
                                                <td>2022-10-26</td>
                                                <td>mobile money</td>
                                                <td>Denis Simbwa</td>
                                                <td>UAR672A</td>
                                                <td>0701620575</td>
                                                <td>Petrocity Ntinda</td>
                                                <td>10,000</td>
                                                <td>UGX</td>
                                                <td>success -<a href="{{route('transaction_detail_preview')}}"><span class="badge badge-success px-2"> View </span></a></td>
                                            </tr>
                                            <tr>
                                                <td>2022-10-26</td>
                                                <td>request</td>
                                                <td>Denis Simbwa</td>
                                                <td>UAR672A</td>
                                                <td>0701620575</td>
                                                <td>Petrocity Ntinda</td>
                                                <td>-10,000</td>
                                                <td>UGX</td>
                                                <td>success -<a href="{{route('transaction_detail_preview')}}"><span class="badge badge-success px-2"> View </span></a></td>
                                            </tr>
                                            <tr>
                                                <td>2022-10-26</td>
                                                <td>request</td>
                                                <td>Denis Simbwa</td>
                                                <td>UAR672A</td>
                                                <td>0701620575</td>
                                                <td>Petrocity Ntinda</td>
                                                <td>-10,000</td>
                                                <td>UGX</td>
                                                <td>success -<a href="{{route('transaction_detail_preview')}}"><span class="badge badge-success px-2"> View </span></a></td>
                                            </tr>
                                            <tr>
                                                <td>2022-10-26</td>
                                                <td>cash</td>
                                                <td>Denis Simbwa</td>
                                                <td>UAR672A</td>
                                                <td>0701620575</td>
                                                <td>Petrocity Ntinda</td>
                                                <td>11,000</td>
                                                <td>UGX</td>
                                                <td>success -<a href="{{route('transaction_detail_preview')}}"><span class="badge badge-success px-2"> View </span></a></td>
                                            </tr>
                                            <tr>
                                                <td>2022-10-26</td>
                                                <td>request</td>
                                                <td>Denis Simbwa</td>
                                                <td>UAR672A</td>
                                                <td>0701620575</td>
                                                <td>Petrocity Ntinda</td>
                                                <td>-10,000</td>
                                                <td>UGX</td>
                                                <td>success -<a href="{{route('transaction_detail_preview')}}"><span class="badge badge-success px-2"> View </span></a></td>
                                            </tr>
                                            <tr>
                                                <td>2022-10-26</td>
                                                <td>mobile money</td>
                                                <td>Denis Simbwa</td>
                                                <td>UAR672A</td>
                                                <td>0701620575</td>
                                                <td>Petrocity Ntinda</td>
                                                <td>10,000</td>
                                                <td>UGX</td>
                                                <td>success -<a href="{{route('transaction_detail_preview')}}"><span class="badge badge-success px-2"> View </span></a></td>
                                            </tr>
                                            <tr>
                                                <td>2022-10-26</td>
                                                <td>request</td>
                                                <td>Denis Simbwa</td>
                                                <td>UAR672A</td>
                                                <td>0701620575</td>
                                                <td>Petrocity Ntinda</td>
                                                <td>-10,000</td>
                                                <td>UGX</td>
                                                <td>success -<a href="{{route('transaction_detail_preview')}}"><span class="badge badge-success px-2"> View </span></a></td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Date</th>
                                                <th>Activity</th>
                                                <th>Name</th>
                                                <th>Ref ID</th>
                                                <th>Mobile</th>
                                                <th>Provider</th>
                                                <th>Amount</th>
                                                <th>Currency</th>
                                                <td>success -<a href="{{route('transaction_detail_preview')}}"><span class="badge badge-success px-2"> View </span></a></td>
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