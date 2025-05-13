@extends('admin.layouts.app')

@section('title', 'Actualiar Rol')

@section('content')

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Asignar Roles y Permisos a: {{ $user->name }}</h5>
            <hr>
            <form action="{{ route('roles.updateRoles', ['id' => encrypt($user->id)]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="roles" class="form-label">Roles:</label>
                    <div class="row">
                        @foreach ($roles as $role)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="roles[]"
                                        value="{{ $role->name }}"
                                        {{ in_array($role->name, $userRoles) ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ ucfirst($role->name) }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="{{route('users.index')}}" class="btn btn-danger">Cancelar</a>
            </form>

        </div>
    </div>

@endsection
