@extends('layouts.layout_account')

@section('content')

<div class="content-body" style="min-height: 887px; margin-top:20px;">
                    <div class="col-lg-12 col-xl-12" >
                        <div class="card col-xl-12">
                            <div class="card-body">
                                <h4 class="card-title">{{ $provider->name_provider }}</h4>
                                <p class="text-muted m-b-15 f-s-12">{{ $provider->business_line }}</p>
                                <div class="basic-form">
                                    <form action="{{ route('request_deposit_client_confirm')}}" method="POST">
                                        @method('POST')
                                        @csrf
                                        
                                        <div class="form-group">Initiating Mobile Money Deposit:
                                            <input name="provider_id" type="hidden" value="{{ $provider->id }}" required type="text" class="form-control input-default" placeholder="Provider ID. ">
                                            <input name="tx_id" type="hidden" value="{{ $tx_id }}" required type="text" class="form-control input-default" placeholder="TX ID. ">
                                        </div>
                                        <div class="form-group">Mobile Number (you can change)
                                            <input autocomplete="off" name="ref_id" type="number" value="{{ \Session::get('user')->mobile }}" required class="form-control input-default" placeholder="Mobile money number">
                                        </div>
                                        <div class="form-group">Amount
                                            <input autocomplete="off" name="requested_amount" type="number" min="0" step="50" max="{{ $provider->business_limit }}" required class="form-control input-default" placeholder="Amount">
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-8 ml-auto">
                                                <button type="submit" class="btn btn-primary"> Continue </button>
                                            </div>
                                        </div>
                                    </form>
                                    {{ $provider->business_line_description }}
                                </div>
                            </div>
                        </div>
                    </div>
</div>

@endsection
