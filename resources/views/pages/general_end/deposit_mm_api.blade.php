@extends('layouts.layout_account')

@section('content')

<div class="content-body" style="min-height: 887px; margin-top:20px;">
                    <div class="col-lg-6" >
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Depositing Online:</h4>
                                <div class="basic-form">
                                    <form action="{{ route('deposit_mm_api') }}" >
                                        @method('POST')
                                        @csrf

                                        <div class="form-group">
                                        <div class="form-group">Amount
                                            <input type="number" name="ref_amount" rmin="10000" value="{{ old('ref_amount')??'0'}}"  min="0" step="50" required class="form-control input-rounded" placeholder="Amount" >
                                        </div>
                                        <div class="form-group">Mobile
                                            <input type="text" name="ref_id" value="{{ old('ref_id')??'0'}}"  required class="form-control input-rounded" placeholder="0701..." >
                                        </div>
                                        <div class="form-group">TX_ID Ref
                                            <input type="number" name="tx_id" alue="{{ old('tx_id')??'0'}}"  required class="form-control input-rounded" placeholder="Source ID reference" >
                                        </div>
                                        <div class="form-group">Access Key
                                            <input type="number" name="access_code" value="{{ old('access_code')??'0'}}"  required class="form-control input-rounded" placeholder="Access Code" >
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-8 ml-auto">
                                                <button type="submit" name="submit" value="submit" class="btn btn-primary"> Enter </button>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-8 ml-auto">
                                                <button type="submit" class="btn btn-primary"> Continue </button>
                                                &nbsp; -OR - <button type="submit" class="badge badge-success"> Pay Nowr</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
</div>

@endsection
