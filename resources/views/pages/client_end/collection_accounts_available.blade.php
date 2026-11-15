@extends('layouts.layout_account')

@section('content')
<div class="content-body" style="margin-top:10px;">

                <div class="col-lg-12">
                        <div class="card col-xl-12 col-lg-12">
                            <div class="card-body">
                                <h4 class="card-title">Available Service Providers</h4>
                                <p class="text-muted"><code></code>
                                </p>
                                <div id="accordion-three" class="accordion">
                                    @for($t=0;$t<count($dta);$t++)
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0 collapsed" data-toggle="collapse" data-target="#collapseThree{{$t}}" aria-expanded="false" aria-controls="collapseThree{{$t}}">
                                                <i class="fa" aria-hidden="true"></i> {{ ($t+1) }}. {{ $dta[$t]->name_provider}} 
                                            </h5>
                                        </div>
                                        <div id="collapseThree{{$t}}" class="collapse" data-parent="#accordion-three">
                                            <table class="table table-hover">
                                             <tbody>
                                              <tr> <td>Description</td> <td>{{ $dta[$t]->approve_narration }} </td> </tr>
                                              <tr> <td>Location</td> <td>{{ $dta[$t]->address_provider }}</td></tr>
                                              <tr> <td>Credibility</td> <td>Vetted and approved  </td></tr>
                                              <tr> <td>Minimum Capital</td><td>UGX 10,000 </td></tr>
{{--                                              <tr> <td><a class="btn btn-success" href="{{ route('provider_centers_member_register', [ 'center_id'=>$dta[$t]->id, 'provider_id'=>$dta[$t]->provider_id, 'id'=>\Session::get('user')->id ])}}"><span class="badge badge-warning"> Register Now  </span></a></td>  --}}
                                              <tr> <td><a class="btn btn-success" href="{{ route('provider_centers_member_register', ['center_id' => $dta[$t]->id, 'provider_id' => $dta[$t]->provider_id,'id'=>\Session::get('user')->id  ]  )}}"><span class="badge badge-warning"> Register Now  </span></a></td>
                                                   <td><a href=""> Enquire for more </a></td>
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
                            <li>1. Submit your request (Free)</li>
                            <li>2. Wait for Approval. This may involve contacting you where necessary for more information.</li>
                            <li>3. Once activated, a button for "SAVE CASH" will appear</li>
                            <li>4. Proceed to pay with mobile money or other options available </li>
                            <li>5. Your deposits amy take upto 24 hours to refflect in the service provider</li>
                            <li>6. By proceeding, you have read and agreed to all our terms and conditions. <a class="badge badge-success px-2" href="https://docs.google.com/document/d/e/2PACX-1vSkD5hG1DP2P9qY6HFjWAZvtCzA5sK1f6QhA1W1LvZGY8PDFYfmm02OIWZENN860A/pub">Read</a> </li>
                        </ol>
                </div>

</div>
@endsection