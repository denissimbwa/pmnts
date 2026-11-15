@extends('layouts.layout_account')

@section('content')

<div class="content-body" style="min-height: 887px; margin-top:20px;">
                    <div class="col-lg-6" >
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Withdrawing Cash To Client :</h4>
                                <p class="text-muted m-b-15 f-s-12">Name : {{ $client_detail->name }}</p>
                                <p class="text-muted m-b-15 f-s-12">Mobile : {{ $client_detail->mobile }}</p>
                                <p class="text-muted m-b-15 f-s-12">Maximum Amount : UGX {{ number_format($client_detail->amount_total) }}</p>
                                <div class="basic-form">
                                    <form  action="{{ route('withdraw_mm_api_client') }}">
                                        @method('POST')
                                        @csrf

                                        <div class="form-group">
                                            <input name="tx_id" type="hidden" value="{{ $tx_id }}" required readonly class="form-control" placeholder="transaction number">
                                            <input name="client_id" value="{{ $client_id }}" type="hidden" class="form-control input-default">
                                            <input name="access_code" value="{{ $client_detail->access_code }}" type="hidden" class="form-control input-default">
                                        </div>
                                        <div class="form-group">Amount
                                            <input type="number" name="amount" max="{{ $client_detail->amount_total }}" value="{{ $client_detail->amount_total }}"  min="0" step="50" required class="form-control input-rounded" placeholder="Amount To Transfer" >
                                        </div>
                                        <div class="form-group">Confirm with your password
                                            <input type="password" required class="form-control input-default" placeholder="Password">
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-8 ml-auto">
                                                <button type="submit" class="btn btn-primary"> Continue </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
</div>

@endsection
