@extends('layouts.layout_account')

@section('content')
<div class="content-body" style="min-height: 887px;margin-top:10px;">

                    <div class="col-lg-12" >
                        <div class="card col-xl-12 col-lg-12">
                            <div class="card-body">
                                <h4 class="card-title">Booking for investment | {{ $provider->name_provider }}</h4>
                                <p class="text-muted m-b-15 f-s-12"> Form</p>
                                <div class="basic-form">
                                    <form action="{{route('investment_booking')}}" method="POST">
                                        @method('POST')
                                        @csrf
                                        <div class="form-group">
                                            <input name="provider_id" type="hidden" value="{{ $provider_id }}" class="form-control input-default">
                                            <input name="approve_ref_id" type="hidden" class="form-control input-default" placeholder="" value="{{ $approve_ref_id }}" />
                                        </div>
                                        <div class="form-group">
                                            <label>Investor's Account:</label>
                                            <select name="acc_number" required class="form-control" id="sel1">
                                             @for($t=0;$t<count($accounts);$t++)
                                                <option value="{{  $accounts[$t]->acc_number}}">{{  $accounts[$t]->acc_name." : ".$accounts[$t]->amount." ".$accounts[$t]->currency." |".$accounts[$t]->tx_channel }}</option>
                                            @endfor
                                            </select>
                                        </div>

                                        <h4>Booking Form:</h4>
                                        <div class="form-group">
                                            <label>How much capital wolud you like to invest ?:</label>
                                            <input required name="amount" value="{{old('amount')}}" type="number" min="0" step="10000" class="form-control" placeholder="0" />
                                        </div>
                                        <div class="form-group">
                                            <label>Any more detail you would like to share ?</label>
                                            <textarea name="narration" value="{{old('narration')}}" rows="3" class="form-control" placeholder="Write here..."></textarea>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-8 ml-auto">
                                                <button type="submit" name="submit" value="submit" class="btn btn-primary"> Submit Request </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <h3>What you need to know:</h3>
                        <p>{{ $provider->approve_narration }} </p>

                    </div>
</div>

@endsection
