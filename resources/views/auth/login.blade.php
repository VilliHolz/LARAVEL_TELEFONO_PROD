@extends('layouts.app')

@section('content')
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <!-- Columna del formulario -->
        <div class="col-md-6">
            <div class="auth-form-light text-left p-5" style="padding-left: 3rem; padding-right: 3rem;">
                <div class="brand-logo text-center mb-1">
                    <img src="{{ asset('assets/admin/images/logo.jpeg') }}" alt="Logo" width="100">
                </div>
                <br>
                <h4 class="font-weight-bold text-center">Iniciar Sesión</h4>
                <form method="POST" action="{{ route('login') }}" class="pt-3">
                    @csrf
                    <div class="form-group">
                        <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                            placeholder="Correo Electrónico">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror"
                            name="password" required autocomplete="current-password" placeholder="Contraseña">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mt-3 d-grid">
                        <button type="submit" class="btn btn-primary btn-lg font-weight-medium auth-form-btn">
                            INICIAR SESIÓN
                        </button>
                    </div>
                    <div class="my-2 d-flex justify-content-center align-items-center">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="remember" id="remember"
                                {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label text-muted" for="remember">
                                Mantenme registrado
                            </label>
                        </div>
                    </div>
                    <div class="my-2 d-flex justify-content-center align-items-center">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="auth-link">¿Has olvidado tu
                                contraseña?</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
