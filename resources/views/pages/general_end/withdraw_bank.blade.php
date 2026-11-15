@extends('layouts.layout_account')

@section('content')

<div class="content-body" style="min-height: 887px; margin-top:20px;">
                    <div class=" col-xl-12 col-lg-12" >
                        <div class="card  col-xl-12 col-lg-12">
                            <div class="card-body">
                                <h4 class="card-title">Transfering Cash To Bank Account:</h4>
                                <p class="text-muted m-b-15 f-s-12">Transaction charges may apply.</p>
                                <div class="basic-form">
                                    <form  action="{{ route('withdraw_bank_confirm')}}" method="POST">
                                        @method('POST')
                                        @csrf
                                        <div class="form-group">
                                            <input name="scope" required type="hidden" value={{$scope??""}} class="form-control input-default" placeholder="center ">
                                            <input name="scope_id" required type="hidden" value="{{$scope_id??""}}" class="form-control input-default" placeholder="center ">
                                            <input name="tx_value_type" required type="hidden" value="{{$tx_value_type??"cash"}}" class="form-control input-default" placeholder="cash">
                                        @if(isset($acc_number) && $acc_number!=null)
                                            <input name="acc_number" type="hidden" value="{{$acc_number}}" class="form-control input-rounded" placeholder="source account">
                                        @endif
                                        </div>
                                        <div class="form-group">Mobile:
                                            <input name="ref_id" type="text" value="{{old('ref_id')}}" required class="form-control input-default" placeholder="Ref / Mobile">
                                        </div>
                                        <div class="form-group">Amount:
                                            <input name="amount" type="number" min="0" step="50" value="{{old('amount')}}" required class="form-control input-default" placeholder="Amount">
                                        </div>

                                        <hr><span>Enter You Bank Detail:</span>
                                        <div class="form-group">Acc Number:
                                            <input name="bank_acc_number" required type="number" value="{{old('bank_acc_number')}}" class="form-control input-default" placeholder="Account Number" >
                                        </div>
                                        <div class="form-group">Account Name:
                                            <input name="bank_acc_name" required  type="text value="{{old('bank_acc_name')}}"" class="form-control input-default" placeholder="Account Name" >
                                        </div>
                                        <div class="form-group">Bank Name:
                                            <input name="bank_name" required type="text" value="{{old('bank_name')}}" class="form-control input-default" placeholder="Bank Name" >
                                            <input name="bank_branch" required type="text" value="{{old('bank_branch')}}" class="form-control input-default" placeholder="Branch">
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
