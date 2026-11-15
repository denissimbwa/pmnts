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
                                
                                <a class="text-center" href="index.html"> <h4>Change Your Password</h4></a>

                                @if(Session::get('register_status'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{Session::get('register_status')}}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                                </button>
                                </div>
                                @endif
                                
        
                                <form class="mt-5 mb-5 login-input" action="{{route('change_password')}}" method="POST">
                                    
                                    @method('POST')
                                    @csrf

                                    <div class="form-group">
                                        <input name="password_old" type="password" class="form-control" placeholder="Password" required>
                                    </div>
                                    @error('password_old')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror

                                    <div class="form-group">
                                        <input name="password" type="password" class="form-control" placeholder="Password" required>
                                        <input name="confirm_password" type="password" class="form-control" placeholder="Confirm Password" required>
                                    </div>
                                    @error('password')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                    @error('confirm_password')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror

                                    <button type="input" name="submit" value="submit" class="btn login-form__btn submit w-100"> Register </button>
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
