@extends('layouts.auth-master')

@section('title', 'Login')

@section('content')
 <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="card-body pt-0">
                            <h3 class="text-center mt-4">
                                <a href="/" class="logo logo-admin"><img src="{{ URL::asset('/images/logo.webp')}}" height="300" alt="logo"></a>
                            </h3>
                            <div class="p-3">
                                <h4 class="text-muted font-size-18 mb-1 text-center">مرحباً بعودتك!</h4>
                                <p class="text-muted text-center">تسجيل الدخول الى منصة الغد التعليمية</p>
                                <form method="POST" class="form-horizontal mt-4" action="{{ route('login') }}">
                                       @csrf
                                    <div class="form-group">
                                        <label for="username">البريد الإلكتروني</label>
                                         <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="البريد الإلكتروني">
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror

                                    </div>
                                    <div class="form-group">
                                        <label for="userpassword">كلمة المرور</label>
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="كلمة المرور">
                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group row mt-4">
                                        <div class="col-6 text-right">
                                            <button class="btn btn-primary w-md waves-effect waves-light" type="submit">تسجيل الدخول</button>
                                        </div>
                                    </div>
                                    {{-- <div class="form-group mb-0 row">
                                        <div class="col-12 mt-4">
                                            <a href="{{ route('password.request') }}" class="text-muted"><i class="mdi mdi-lock"></i> Forgot your password?</a>
                                        </div>
                                    </div> --}}
                                </form>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="mt-5 text-center">
                        <p>Don't have an account ? <a href="/register" class="text-primary"> Signup Now </a></p>
                        <p>© {{  date('Y', strtotime('-2 year')) }} - {{  date('Y') }} Lexa. Crafted with <i class="mdi mdi-heart text-danger"></i> by Themesbrand</p>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
@stop
