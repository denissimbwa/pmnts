@extends('layouts.layout_account')

@section('content')

<div class="content-body" style="min-height: 887px; margin-top:20px;">
                    <div class="col-lg-6" >
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Withdrawing Cash from Online:</h4>
                                <p class="text-muted m-b-15 f-s-12">Transaction charges may apply:</p>
                                <div class="basic-form">
                                    <form  action="{{ route('withdraw_mm_confirm') }}">
                                        @method('POST')
                                        @csrf

                                        <div class="form-group">
                                            <input name="tx_id" type="hidden" value="{{$dta['tx_id']}}" required readonly class="form-control" placeholder="transaction number">
                                            <input name="tx_owner_type" value="{{$dta['tx_owner_type']}}" type="hidden" class="form-control input-default">
                                            <input name="tx_owner_id" value="{{$dta['tx_owner_id']}}" type="hidden" class="form-control input-default" >
                                        @if(isset($dta['tx_service_cat']) && isset($dta['tx_service_cat_ID']))
                                            <input name="tx_service_cat" value="{{ $dta['tx_service_cat'] }}" type="hidden" class="form-control input-default">
                                            <input name="tx_service_cat_ID" value="{{ $dta['tx_service_cat_ID'] }}" type="hidden" class="form-control input-default" >
                                        @endif
                                        @if(isset($dta['source_account']) && $dta['source_account']!=null)
                                            <input name="source_account" type="hidden" value="{{$dta['source_account']}}" class="form-control input-rounded" placeholder="source account">
                                        @endif
                                        @if(isset($dta['dest_account']) && $dta['dest_account']!=null)
                                            <input name="dest_account" type="hidden" value="{{$dta['dest_account']}}" class="form-control input-rounded" placeholder="source account">
                                        @endif
                                        @if(isset($dta['param1']) && $dta['param1']!=null)
                                            <input name="param1" type="hidden" value="{{$dta['param1']}}" class="form-control input-rounded" placeholder="Detail1">
                                        @endif
                                        @if(isset($dta['param2']) && $dta['param2']!=null)
                                            <input name="param2" type="hidden" value="{{$dta['param2']}}" class="form-control input-rounded" placeholder="Detail2">
                                        @endif
                                        @if(isset($dta['request_service_id']) && $dta['request_service_id']!=null)
                                            <input name="request_service_id" type="hidden" value="{{$dta['request_service_id']}}" class="form-control input-rounded" placeholder="request_service_id">
                                            <input name="param1" type="hidden" value="name" class="form-control input-rounded" placeholder="request_service_id">
                                        @endif
                                        </div>
                                        <div class="form-group">Mobile Money No:
                                            <input name="ref_id" readonly required type="text" class="form-control input-rounded" placeholder="Ref ID" value="{{$dta['ref_id'] }}">
                                        </div>
                                        @if(!isset($dta['param2']) || $dta['param2']==null)
                                        <div class="form-group">Member's Name
                                            <input name="param2" required class="form-control input-rounded" placeholder="Name" value="{{$dta['member']->name ?? ""}}">
                                        </div>
                                        @endif
                                        <div class="form-group">Amount
                                            <input type="number" name="amount_source" readonly value={{$dta['amount']}}  min="0" step="50" required class="form-control input-rounded" placeholder="Amount" >
                                        </div>
                                        <div class="form-group">Slip No
                                          <input name="slip_no" type="text" value="{{$dta['tx_id']}}" required class="form-control" placeholder="transaction number">
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
