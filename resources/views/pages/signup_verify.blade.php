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
                                
                                    <a class="text-center" href="index.html"> <h4>Enter Code Sent to : {{ $mobile??'' }} - For Help {{ $telephone_admin ??'www.zhuris.com' }}</h4></a>
        
                                <form class="mt-5 mb-5 login-input" action="{{route('signup_verify')}}" method="POST">
                                    
                                    @method('POST')
                                    @csrf

                                    <div class="form-group">
                                        <input type="hidden" name="mobile" class="form-control"  placeholder="Mobile" value="{{ $mobile??'' }}" required>
                                        <input type="text" name="verification_code"  class="form-control"  placeholder="Enter code" required>
                                    </div>
                                    @error('verification_code')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                    
                                    <button name="update" value="update" class="btn login-form__btn submit w-100"> Continue </button>
                                </form>
                                    <p class="mt-5 login-form__footer">Have account <a href="{{ route('login')}}" class="text-primary">Login </a> instead</p>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
@endsection
