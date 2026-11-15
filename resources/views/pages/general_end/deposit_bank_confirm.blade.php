@extends('layouts.layout_account')

@section('content')

<div class="content-body" style="min-height: 887px; margin-top:20px;">
                    <div class="col-lg-12 col-xl-12" >
                        <div class="card col-xl-12">
                            <div class="card-body">
                                <h4 class="card-title">Bank Deposit : EQUITY BANK - 4201296610 - ZHURISOFT CONSULT</h4>
                                <p class="text-muted m-b-15 f-s-12">This action will first be approved before payment reflects.</p>
                                <div class="basic-form">
                                    <form action="{{ route('deposit_bank_confirm') }}" >
                                        @method('POST')
                                        @csrf

                                        <div class="form-group">
                                            <input name="tx_type" value="deposit" type="hidden" class="form-control input-default">
                                            <input name="tx_id" type="hidden" value="{{$dta['tx_id']}}" required readonly class="form-control" placeholder="transaction number">
                                            <input name="tx_owner_type" value="{{$dta['tx_owner_type']}}" type="hidden" class="form-control input-default">
                                            <input name="tx_owner_id" value="{{$dta['tx_owner_id']}}" type="hidden" class="form-control input-default" >
                                            <input name="tx_value_type" value="wallet" type="hidden" class="form-control input-default">
                                            <input name="tx_channel" value="bank" type="hidden" class="form-control input-default">
                                        @if(isset($dta['tx_service_cat']) && isset($dta['tx_service_cat_ID']))
                                            <input name="tx_service_cat" value="{{$tx_service_cat}}" type="hidden" class="form-control input-default">
                                            <input name="tx_service_cat_ID" value="{{$tx_service_cat_ID}}" type="hidden" class="form-control input-default" >
                                        @endif
                                        @if(isset($dta['recovery_flag']) && $dta['recovery_flag']!=null)
                                            <input name="recovery_flag" type="hidden" value="{{$dta['recovery_flag']}}" class="form-control input-rounded" placeholder="Recovery flag">
                                        @endif
                                        @if(isset($dta['dest_account']) && $dta['dest_account']!=null)
                                            <input name="dest_account" type="hidden" value="{{$dta['dest_account']}}" class="form-control input-rounded" placeholder="destination account">
                                        @endif
                                        </div>
                                        <div class="form-group">
                                            <input name="ref_id" readonly required type="text" class="form-control input-rounded" placeholder="Ref ID" value="{{$dta['ref_id'] }}">
                                        </div>
                                        <div class="form-group">Name
                                            <input name="name" required readonly type="disabled" class="form-control input-rounded" placeholder="Name" value="{{$dta['member']->name ?? $dta['member']}}">
                                        </div>
                                        <div class="form-group">Amount
                                            <input type="number" name="amount_source" readonly value={{$dta['amount']}}  min="0" step="50" required class="form-control input-rounded" placeholder="Amount" >
                                        </div>
                                        <div class="form-group">Attachment (optional) 
                                            <input name="image1" type="file" class="form-control-file">
                                        </div>
                                        <div class="form-group">Enter Slip No.:
                                            <input name="slip_no" type="text" value="" class="form-control input-rounded" placeholder="Transaction No.">
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-8 ml-auto">
                                                <button type="submit" name="submit" value="submit" class="btn btn-primary"> Enter </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
</div>

@endsection
