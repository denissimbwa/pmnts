@extends('layouts.layout_account')

@section('content')

<div class="content-body" style="min-height: 887px; margin-top:20px;">
                    <div class="col-xl-12 col-lg-12" >
                        <div class="card col-xl-12 col-lg-12">
                            <div class="card-body">
                                <h4 class="card-title">Confirm Detail For Cash Transfer:</h4>
                                <p class="text-muted m-b-15 f-s-12">Bank transfer may take to a maximum of 24hr after your approval.</p>
                                <div class="basic-form">
                                    <form  action="{{ route('withdraw_bank_confirm')}}" method="POST">
                                        @method('POST')
                                        @csrf
                                        <div class="form-group">
                                            <input name="tx_type" value="{{ $dta['tx_type']??"withdraw" }}" type="hidden" class="form-control input-default">
                                            <input name="tx_id" type="hidden" value="{{$dta['tx_id']}}" required readonly class="form-control" placeholder="transaction number">
                                            <input name="tx_owner_type" value="{{$dta['tx_owner_type']}}" type="hidden" class="form-control input-default">
                                            <input name="tx_owner_id" value="{{$dta['tx_owner_id']}}" type="hidden" class="form-control input-default" >
                                            <input name="tx_value_type" value="{{$dta['tx_value_type']??"cash"}}" type="hidden" class="form-control input-default">
                                            <input name="tx_channel" value="{{$dta['tx_channel']??"transit"}}" type="hidden" class="form-control input-default">
                                            <input name="slip_no" type="hidden" value="{{$dta['tx_id']}}" class="form-control input-rounded" placeholder="Slip No.">
                                            @if(isset($dta['source_account']) && $dta['source_account']!=null)
                                                <input name="source_account" type="hidden" value="{{$dta['source_account']}}" class="form-control input-rounded" placeholder="source account">
                                            @endif
                                            @if(isset($dta['dest_account']) && $dta['dest_account']!=null)
                                                <input name="dest_account" type="hidden" value="{{$dta['dest_account']}}" class="form-control input-rounded" placeholder="source account">
                                            @endif
                                            @if(isset($dta['investment_flag']) && $dta['investment_flag']!=null)
                                                <input name="investment_flag" type="hidden" value="{{$dta['investment_flag']}}" class="form-control input-rounded" placeholder="source account">
                                            @endif
                                            @if(isset($dta['investment_id']) && $dta['investment_id']!=null)
                                                <input name="investment_id" type="hidden" value="{{$dta['investment_id']}}" class="form-control input-rounded" placeholder="source account">
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <input name="ref_id" readonly required type="text" class="form-control input-rounded" placeholder="Ref ID" value="{{$dta['ref_id'] }}">
                                        </div>
                                        <div class="form-group">Name
                                            <input name="name" required readonly type="disabled" class="form-control input-rounded" placeholder="Name" value="{{$dta['member']->acc_name}}">
                                        </div> Service Charges :{{ $dta['member']->currency." ".number_format($dta['tx_fee']) }}
                                        <div class="form-group">Amount
                                            <input type="number" name="amount_source" readonly value="{{ $dta['amount'] }}"  min="0" step="50" required class="form-control input-rounded" placeholder="Amount" >
                                        </div>
                                        <hr><span>Bank Detail:</span>
                                        <div class="form-group">Acc Number:
                                            <input name="bank_acc_number" required readonly type="text" value="{{ $dta['bank_acc_number'] ?? "" }}" class="form-control input-default" placeholder="Account Number" Value="1001101107048">
                                        </div>
                                        <div class="form-group">Account Name:
                                            <input name="bank_acc_name" required  readonly type="text" value="{{ $dta['bank_acc_name'] ?? "" }}" class="form-control input-default" placeholder="Account Name" value="Denis SImbwa">
                                        </div>
                                        <div class="form-group">Bank Name:
                                            <input name="bank_name" required readonly type="text" value="{{ $dta['bank_name'] ?? "" }}" class="form-control input-default" placeholder="Bank Name" value="Equity Bank">
                                            <input name="bank_branch" required readonly type="text" value="{{ $dta['bank_branch'] ?? "" }}" class="form-control input-default" placeholder="Branch" value="Church House">
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
