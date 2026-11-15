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
                                <a class="text-center" href="index.html"> <h4>ZMT Save SMall Own Big</h4></a>
        
                                <form action="{{ route('login') }}" class="mt-5 mb-5 login-input" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <input autocomplete="off" name="mobile" type="text" value="{{ old('mobile') }}" required class="form-control" placeholder="Mobile">
                                    </div>
                                    @error('mobile')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror

                                    <div class="form-group">
                                        <input autocomplete="off" name="password" type="password" required class="form-control" placeholder="Password">
                                    </div>
                                    @error('password')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                    
                                    <button class="btn login-form__btn submit w-100"> Sign In </button>
                                </form>
                                <p class="mt-5 login-form__footer">Dont have account? <a href="{{ route('signup') }}" class="text-primary">Sign Up</a> now</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection


