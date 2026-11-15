@extends('layouts.layout_account')

@section('content')

<div class="content-body">

        <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Transactions History :</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Ref</th>
                                                <th>Name</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>1</th>
                                                <td>2022-10-29</td>
                                                <td>UAR672A</td>
                                                <td>Denis</td>
                                                <td>10,000</td>
                                                <td class="color-primary"><a href=""> taken </a> </td>
                                                <td class="color-primary">
                                                    <a class="badge badge-success" href=""> View </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>2</th>
                                                <td>2022-10-29</td>
                                                <td>UAR672A</td>
                                                <td>Simbwa</td>
                                                <td>20,000</td>
                                                <td class="color-primary"><a href=""> taken </a> </td>
                                                <td class="color-primary">
                                                    <a class="badge badge-success" href=""> View </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>2</th>
                                                <td>2022-10-29</td>
                                                <td>UAR672A</td>
                                                <td>Fredrick</td>
                                                <td>25,000</td>
                                                <td class="color-primary"><a href=""> taken </a> </td>
                                                <td class="color-primary">
                                                    <a class="badge badge-success" href=""> View </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Ref</th>
                                                <th>Name</th>
                                                <th>Amount</th>
                                                <th>Status</th>
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