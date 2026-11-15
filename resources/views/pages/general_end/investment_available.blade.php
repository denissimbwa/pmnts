@extends('layouts.layout_account')

@section('content')
<div class="content-body" style="margin-top:10px;">

                <div class="col-lg-12">
                        <div class="card col-xl-12 col-lg-12">
                            <div class="card-body">
                                <h4 class="card-title">Available Ventures to invest in. First come first serve.</h4>
                                <p class="text-muted"><code></code>
                                </p>
                                <div id="accordion-three" class="accordion">
{{--
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0" data-toggle="collapse" data-target="#collapseOne4" aria-expanded="true" aria-controls="collapseOne4"><i class="fa" aria-hidden="true"></i>1 Fuel Credit (3,000,000 capital)</h5>
                                        </div>
                                        <div id="collapseOne4" class="collapse show table-responsive" data-parent="#accordion-three">
                                            <table class="table table-hover">
                                             <tbody>
                                              <tr> <td>Type</td> <td>Fuel Credit </td> </tr>
                                              <tr> <td>Location</td> <td>Kampala</td></tr>
                                              <tr> <td>Duration in business</td> <td>3 months</td></tr>
                                              <tr> <td>Proffit Accumulated</td><td>UGX 1,200,000</td></tr>
                                              <tr> <td>Minimum Capital</td><td>UGX 5,000,000</td></tr>
                                              <tr> <td>Interest rate</td><td>8% </td></tr>
                                              <tr> <td><a class="btn btn-success" href="{{ route('investment_booking')}}"> Book Now </a></td> <td><a href=""> Enquire for more </a></td></tr>
                                             </tbody>
                                            </table>
                                        </div>
                                    </div>
--}}
                                    @for($t=0;$t<count($dta);$t++)
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0 collapsed" data-toggle="collapse" data-target="#collapseThree{{$t}}" aria-expanded="false" aria-controls="collapseThree{{$t}}">
                                                <i class="fa" aria-hidden="true"></i> {{ ($t+1) }}. {{ $dta[$t]->business_line." : ".$dta[$t]->name_provider}} ( 1,000,000 ) 
                                                  @if(\Session::get('user')->id !=null && \Session::get('user')->id===$dta[$t]->user_id_inverstor)
                                                   <a class="btn btn-success badge badge-success px-2" href="{{ route('investment_booking',[$dta[$t]->id,$dta[$t]->acc_number_investor])}}"><span class="badge badge-success"> Request to Re-investment </span></a>
                                                  @endif
                                                  @if(\Session::get('user')->id !=null && \Session::get('user')->id===$dta[$t]->user_id_inverstor)
                                                   <a class="btn btn-warning" href="{{ route('investment_booking_confirm',[$dta[$t]->booking_id])}}"><span class="badge badge-warning"> Load Investment  </span></a>
                                                  @endif
                                            </h5>
                                        </div>
                                        <div id="collapseThree{{$t}}" class="collapse" data-parent="#accordion-three">
                                            <table class="table table-hover">
                                             <tbody>
                                              <tr> <td>Description</td> <td>{{ $dta[$t]->approve_narration }} </td> </tr>
                                              <tr> <td>Location</td> <td>{{ $dta[$t]->address_provider }}</td></tr>
                                              <tr> <td>Duration in business</td> <td>3 months</td></tr>
                                              <tr> <td>Minimum Capital</td><td>UGX 1,000,000</td></tr>
                                              <tr> <td>Interest rate</td><td>{{ $dta[$t]->interest_investor}}%</td></tr>
                                              <tr> <td> 
                                                       @if(\Session::get('user')->id !=null && \Session::get('user')->id!==$dta[$t]->user_id_inverstor)
                                                       <a class="btn btn-success" href="{{ route('investment_booking',[$dta[$t]->id])}}"><span class="badge badge-warning"> Book Now  </span></a>
                                                       @endif
                                                   </td>
                                                   <td>
                                                       <a href=""> Enquire for more </a>
                                                   </td>
                                              </tr>
                                             </tbody>
                                            </table>
                                        </div>
                                   </div>
                                    @endfor
                            </div>
                        </div>

                        <p>Procedure :</p>
                        <ol>
                            <li>1. Submit booking request (Free)</li>
                            <li>2. Wait for selection. This may involve contacting you where necessary.</li>
                            <li>3. Once selected, a button for "PAY NOW" will appear</li>
                            <li>4. Follow the guid for payment </li>
                            <li>5. Investment will start appearing in your account and your profits will start going to the beneficiary accounts </li>
                            <li>6. By investing you have read and agreed to the terms and conditions. <a class="badge badge-success px-2" href="https://docs.google.com/document/d/e/2PACX-1vSkD5hG1DP2P9qY6HFjWAZvtCzA5sK1f6QhA1W1LvZGY8PDFYfmm02OIWZENN860A/pub">Read</a> </li>
                        </ol>
                </div>

</div>
@endsection