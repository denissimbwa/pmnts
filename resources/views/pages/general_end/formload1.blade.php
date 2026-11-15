@extends('layouts.layout_account')

@section('content')


@php
$fields=$form['fields'];
@endphp
<div class="content-body" style="min-height: 887px;margin-top:10px;">

                    <div class="col-lg-10" >
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">{{$form['title1']}} :</h4>
                                <p class="text-muted m-b-15 f-s-12">{{$form['title2']}}</p>
                                <div class="basic-form">
                                    <form action="{{route($form['action'])}}" method="{{$form['method']}}" >
                                        @method('POST')
                                        @csrf
                                        
                                        @for($k=0;$k<count($fields);$k++)
                                            @if($fields[$k]['type']==="text"||$fields[$k]['type']==="file"||$fields[$k]['type']==="hidden")
                                        <div class="form-group">
                                                <input name="{{ $fields[$k]['name'] }}" type="{{ $fields[$k]['type'] }}" placeholder="{{ $fields[$k]['placeholder'] }}" value="{{ $fields[$k]['value'] }}" class="{{ $fields[$k]['class'] }}" {{ $fields[$k]['other'] }} />
                                        </div>
                                    
                                            @elseif($fields[$k]['type']==="number")
                                        <div class="form-group">
                                                <input name="{{ $fields[$k]['name'] }}" type="{{ $fields[$k]['type'] }}" placeholder="{{ $fields[$k]['placeholder'] }}" value="{{ $fields[$k]['value'] }}" class="{{ $fields[$k]['class'] }}" step="{{ $fields[$k]['step'] }}" min="{{ $fields[$k]['step'] }}" {{ $fields[$k]['other'] }} />
                                        </div>
                                    
                                            @elseif($fields[$k]['type']==="textarea")
                                        <div class="form-group">
                                                <textarea name="{{ $fields[$k]['name'] }}" class="{{ $fields[$k]['class'] }}" placeholder="{{ $fields[$k]['placeholder'] }}" {{ $fields[$k]['other'] }} >{{ $fields[$k]['value'] }}</textarea>
                                        </div>
                                    
                                            @elseif($fields[$k]['type']==="select")
                                        <div class="form-group">
                                                <select name="{{ $fields[$k]['name'] }}" class="{{ $fields[$k]['class'] }}" {{ $fields[$k]['other'] }}>
                                                    @if($fields[$k]['placeholder']!=null&&isset($fields[$k]['placeholder'])&&!empty($fields[$k]['placeholder']))
                                                        <option value="" >- {{$fields[$k]['placeholder']}} -</option>
                                                    @endif
                                                    @for( $y=0;$y<count($fields[$k]['options']);$y++)
                                                        <option value="{{$fields[$k]['options'][$y]['value']}}" {{ ($fields[$k]['options'][$y]['option']===$fields[$k]['value'])?'selected':'' }}> {{ $fields[$k]['options'][$y]['option']}} </option>
                                                    @endfor
                                                </select>
                                            </div>
                                            @elseif($fields[$k]['type']==="button")
                                        <div class="form-group">
                                                <button type="{{ $fields[$k]['type'] }}" name="{{ $fields[$k]['name'] }}"  value="{{ $fields[$k]['name'] }}" class="{{ $fields[$k]['class'] }}" >{{ $fields[$k]['value'] }}</button>
                                        </div>
                                        @endif
                                        @endfor
                                    </form>
                                </div>
                                <p class="text-muted m-b-15 f-s-12">{{$form['bottom']}}</p>
                            </div>
                        </div>
                    </div>
</div>

@endsection