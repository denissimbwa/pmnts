@extends('layouts.layout')

@section('content')

<body class="h-100">
    
    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10" />
            </svg>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

    



    <div class="login-form-bg h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100">
                <div class="col-xl-6">
                    <div class="form-input-content">
                        <div class="card login-form mb-0">
                            <div class="card-body pt-5">
                                        <h5 class="card-title">{{ $dta->name }}</h5>
                                        <h5 class="card-title">Target : {{ $dta->currency." ".number_format($dta->amount) }} </h5>
                                        <h6 class="card-subtitle mb-2 text-muted">Contribution Form <span class="badge badge-success px-2">{{ $dta->currency." ".number_format(($dta->paid_amount+$dta->taken_amount),0) }} collected</span></h6>
<!--                                <img class="img-fluid" src="https://zhuris.com/images/banner_zhuris.jpeg" alt=""> -->
                                <p>{{ $dta->description }}</p>
                                <hr>
                                @if(Session::get('register_status'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{Session::get('register_status')}}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                                </button>
                                </div>
                                @endif
                                
        
                                <form class="mt-5 mb-5 login-input" action="{{route('deposit_campaign')}}" method="POST">
                                    
                                    @method('POST')
                                    @csrf

                                    <div class="form-group">
                                        <input name="request_service_id" type="hidden" class="form-control" value="{{ $dta->id }}"  placeholder="Name" required>
                                        <input name="tx_id" type="hidden" class="form-control" value="{{ $tx_id }}"  placeholder="Tx_ID" required>
                                        <input name="param1" type="hidden" class="form-control" value="name"  placeholder="Name" required>
                                        <input name="param2" type="text" class="form-control" value="{{ old('param2') }}"  placeholder="Name" required>
                                    </div>
                                    @error('param2')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror

                                    
                                    <div class="form-group">
                                        <input name="country_code" type="hidden" class="form-control" value="256"  placeholder="Country" required>
                                        <input name="ref_id" type="text" class="form-control" value="{{ old('mobile') }}"  placeholder="Mobile Money Number" required>
                                    </div>
                                    @error('mobile')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror

                                    <div class="form-group">
                                        <input name="amount_source" type="number" min="0" step="50" class="form-control" placeholder="Amount" required>
                                    </div>
                                    @error('amount_source')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror

                                    <button type="input" name="enter" value="enter" class="btn login-form__btn submit w-100"> PAY NOW </button>
                                </form>
                                    <p class="mt-5 login-form__footer"> </b> NOTE:</b> Mobile money Transaction charges may apply. </p>
                                    </p>
                                

                                    <span>History</span>
                                    <table class="table table-striped table-bordered zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Date</th>
                                                <th>Name</th>
                                                <th>Amount</th>
                                                <th>Flow Type</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($t=0; ($activities!=null && $t<count($activities)); $t++ )
                                            <tr>
                                                <th>{{ ($t+1) }}</th>
                                                <td>{{ $activities[$t]->created_at }}</td>
                                                <td>{{ $activities[$t]->detail2 }}</td>
                                                <td>{{ ($activities[$t]->flow_type==="inflow") ? number_format($activities[$t]->paid_amount) : number_format($activities[$t]->taken_amount) }}</td>
                                                <td>{{ $activities[$t]->flow_type }}</td>
                                            </tr>
                                            @endfor
                                    </table>                                
                                
                                    <div class="card-footer">
                                        <p class="card-text"> {{ $dta->description }}</p>
                                    </div>
                                    <a href="whatsapp://send?text={{ route('campaign_detail')}}/{{ $id }}" data-action="share/whatsapp/share" target="_blank"><i class="fa fa-share-alt" style="color:grey;font-size:36"></i> Share </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
@endsection
