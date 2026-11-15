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
        <div class="container h-100" >
            <div class="row justify-content-center h-100">
                <div class="col-xl-6">
                    <div class="form-input-content">
                        <div class="card login-form mb-0">
                            <div class="card-body pt-5" style="background-color:white;">
                                
                                <div class="card border-primary">
                                    <div class="card-header">Finished</div>
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $message ?? "Finished !" }}</h5>
                                        <p class="card-text">Completed Submission </p>
                                        <small>Completed</small>
                                    </div>
                                    <div class="card-footer"><a href="{{ route(($next==null)?'account_client':$next)}}" class="btn btn-success"> OK </a> &nbsp; <a href="{{ route($next)}}" class="btn btn-primary"> PRINT </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection



