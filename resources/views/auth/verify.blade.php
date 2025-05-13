@extends('layouts.app')

@section('content')
    <div class="login-page bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="bg-white">
                        <div class="row">
                            <div class="col-md-7 mx-auto">
                                <div class="auth-form-light text-center p-5">
                                    <div class="brand-logo mb-3">
                                        <img src="{{ asset('assets/admin/images/logo.png') }}" width="100">
                                    </div>
                                    @if (session('resent'))
                                        <div class="alert alert-success" role="alert">
                                            {{ __('A fresh verification link has been sent to your email address.') }}
                                        </div>
                                    @endif

                                    <h3 class="text-center">{{ __('Verify Your Email Address') }}</h3>

                                    <hr>

                                    {{ __('Before proceeding, please check your email for a verification link.') }}
                                    {{ __('If you did not receive the email') }},
                                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>.
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
