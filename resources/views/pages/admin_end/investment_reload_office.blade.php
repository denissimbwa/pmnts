@extends('layouts.layout_account')

@section('content')

<div class="content-body">

        <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title"> Recovered Capital Summary </h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Provider Name</th>
                                                <th>Current Capital</th>
                                                <th>Recovered Capital</th>
                                                <th>Activity Count</th>
                                                <th>Amount To Restore</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($t=0; ($dta!=null && $t < count($dta)); $t++)
                                            <tr>
                                                <td>{{ ($t+1) }}</td>
                                                <td>{{ $dta[$t]->name_provider }}</td>
                                                <td>{{ number_format($dta[$t]->amount,0) }}</td>
                                                <td>{{ number_format($dta[$t]->reinvestment_amount_pending,0) }}</td>
                                                <td>{{ number_format($dta[$t]->reinvestment_count,0) }} </td>
                                                <td>
                                                    <form action="{{ route('investment_reload_office') }}" method="POST">
                                                    @csrf
                                                    <input name="dest_account" type="hidden" value="{{ $dta[$t]->acc_number_provider }}" />
                                                    <input name="amount" type="number" class="form-controll" max="{{ $dta[$t]->reinvestment_amount_pending ?? 0 }}" />
                                                    <button type="submit"  class="btn btn-success form-controll"> Transfer To Provider </button>
                                                    </form>
                                                </td>
                                            </tr>
                                           @endfor
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