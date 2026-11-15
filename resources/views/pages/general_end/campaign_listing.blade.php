@extends('layouts.layout')

@section('content')
                    <div class="col-12 m-b-30">
                        <h4 class="d-inline">Active campaigns</h4>
                        <p class="text-muted">All campaigns running on this site are first approved after careful validation procedure !</p>
                        <div class="row">
                        @if(!isset($dta) || $dta!=null)
                            @for($t=0; $t < count($dta); $t++ )
                            <div class="col-md-6 col-lg-3">
                                <div class="card">
                                    <div class="card-header bg-white">
                                        <h5 class="card-title">{{ $dta[$t]->name }}</h5>
                                        <h6 class="card-subtitle mb-2 text-muted">from {{ substr($dta[$t]->start_date,0,10)." -to- ".substr($dta[$t]->end_date,0,10) }}</h6>
                                    </div>
<!--                                    <img class="img-fluid" src="https://zhuris.com/images/banner_zhuris.jpeg" alt=""> -->
                                    <div class="card-body">
                                        <p class="card-text">{{ $dta[$t]->description }}</p>
                                    </div>
                                    <div class="card-footer">
                                        <p class="card-text d-inline"><small class="text-muted">Collection: <b>{{ $dta[$t]->currency." ".number_format($dta[$t]->paid_amount,0)." / ".$dta[$t]->currency." ".number_format($dta[$t]->amount,0) }} </b> </small>
                                        </p>
                                        <a href="{{ route('campaign_detail')}}/{{ $dta[$t]->id }}" class="badge badge-success float-right"><small>Contribute Here</small></a>
                                        <a href="whatsapp://send?text={{ route('campaign_detail')}}/{{ $dta[$t]->id }}" data-action="share/whatsapp/share" target="_blank"><i class="fa fa-share-alt" style="color:grey;font-size:36"></i> Share </a>
                                    </div>
                                </div>
                            </div>
                            @endfor
                        @else
                        <div class="card-body">
                                <h4 class="card-title">Sorry</h4>
                                <div class="card-content">
                                    <div class="alert alert-success">
                                        <h4 class="alert-heading">No active campaings running at the moment!</h4>
                                        <p>Please check arround lator.</p>
                                        <hr>
                                        <p class="mb-0">All campaigns running on this site are first approved after careful validation procedure !</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        </div>
                    </div>
@endsection
