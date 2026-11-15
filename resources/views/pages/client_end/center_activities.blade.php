@extends('layouts.layout_account')

@section('content')
<div class="content-body" style="min-height: 887px; margin-top:10px;">


                <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">
                                    <h4>Recent Cash Flow | &nbsp;<a class="btn btn-success" href="">  Withdraw Cash </a> </h4>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Member</th>
                                                <th>Flow Type</th>
                                                <th>Amount</th>
                                                <th>Channel</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>1</th>
                                                <td>Denis Simbwa</td>
                                                <td><span class="badge badge-primary px-2">Deposite</span></td>
                                                <td>UGX 1,500,000</td>
                                                <td>Cash</td>
                                                <td class="color-primary"><a href="">Pending </a> </td>
                                                <td class="color-primary">
                                                    <a class="badge badge-warning" href="">Approve Now </a>
                                                    <a class="badge badge-success" href=""> View </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>2</th>
                                                <td>Denis Simbwa</td>
                                                <td><span class="badge badge-primary px-2">Deposite</span></td>
                                                <td>UGX 500,000</td>
                                                <td>Cash</td>
                                                <td class="color-primary"><a href="">Approved </a> </td>
                                                <td class="color-primary">
                                                    <a class="badge badge-success" href=""> View </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>3</th>
                                                <td>Fredrick Samson</td>
                                                <td><span class="badge badge-primary px-2">Deposite</span></td>
                                                <td>UGX 2,5000</td>
                                                <td>Mobile Money</td>
                                                <td class="color-primary"><a href="">Approved </a> </td>
                                                <td class="color-primary">
                                                    <a class="badge badge-success" href=""> View </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>4</th>
                                                <td>Isaac Joshua</td>
                                                <td><span class="badge badge-primary px-2">Deposite</span></td>
                                                <td>UGX 10,000</td>
                                                <td>Mobile Money</td>
                                                <td class="color-primary"><a href="">Approved </a> </td>
                                                <td class="color-primary">
                                                    <a class="badge badge-success" href=""> View </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>5</th>
                                                <td>Denis Simbwa</td>
                                                <td><span class="badge badge-success px-2">Withdraw</span></td>
                                                <td>UGX 10,000</td>
                                                <td>Cash</td>
                                                <td class="color-primary"><a href="">Approved </a> </td>
                                                <td class="color-primary">
                                                    <a class="badge badge-success" href=""> View </a>
                                                </td>
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
