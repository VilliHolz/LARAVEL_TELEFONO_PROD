@extends('admin.layouts.app')

@section('title', 'Tu perfil')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-primary">
        <div class="card-body">
            <h2 class="card-title">Modificar Contraseña</h2>
            <hr>

            <form method="POST" action="{{ route('profile.updatePassword') }}">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="current_password">Contraseña Actual</label>
                    <input type="password" name="current_password" id="current_password" class="form-control"
                        placeholder="Contraseña Actual">
                </div>
                <div class="form-group">
                    <label for="new_password">Nueva Contraseña</label>
                    <input type="password" name="new_password" id="new_password" class="form-control"
                        placeholder="Nueva Contraseña">
                </div>
                <div class="form-group">
                    <label for="new_password_confirmation">Confirmar Nueva Contraseña</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                        class="form-control" placeholder="Confirmar Nueva Contraseña">
                </div>
                <button type="submit" class="btn btn-outline-primary">Actualizar Contraseña</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Modificar Datos</h5>
            <hr>
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="name">Nombre</label>
                        <input type="text" name="name" id="name" class="form-control"
                            value="{{ old('name', Auth::user()->name) }}" required>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" name="email" id="email" class="form-control"
                            value="{{ old('email', Auth::user()->email) }}" required>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="address">Dirección</label>
                        <input type="text" name="address" id="address" class="form-control" placeholder="Dirección"
                            value="{{ old('address', Auth::user()->address) }}">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="phone">Teléfono</label>
                        <br>
                        <input type="tel" name="phone" id="phone" class="form-control"
                            value="{{ old('phone', Auth::user()->phone) }}">
                        <input type="hidden" name="phone_country_code" id="phone_country_code">
                        <input type="hidden" name="phone_country_flag" id="phone_country_flag">
                    </div>

                    <div class="form-group col-md-4">
                        <label for="website">Página Web</label>
                        <br>
                        <input type="text" name="website" id="website" class="form-control" placeholder="Página Web"
                            value="{{ old('website', Auth::user()->website) }}">
                    </div>

                    <div class="form-group col-md-12">
                        <label for="avatar">Avatar</label>
                        <input type="file" name="avatar" id="avatar" class="form-control">
                        @if (Auth::user()->avatar)
                            <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="Avatar" width="100">
                        @endif
                    </div>
                </div>
                <button type="submit" class="btn btn-outline-primary">Actualizar Perfil</button>
            </form>
        </div>
    </div>

@endsection
