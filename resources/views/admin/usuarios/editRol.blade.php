@extends('admin.layouts.app')

@section('title', 'Editar Rol')

@section('content')

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title">Editar Rol: {{ ucfirst($role->name) }}</h5>
                <a href="{{ route('roles.index') }}" class="btn btn-danger btn-sm">Cancelar</a>
            </div>
            <hr>

            <form action="{{ route('roles.update', $role->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="role_name" class="form-label">Nombre del Rol:</label>
                    <input type="text" name="role_name" id="role_name" class="form-control" value="{{ $role->name }}"
                        required>
                </div>

                <div class="mb-3">
                    <label for="permissions" class="form-label">Permisos:</label>
                    <div class="row">
                        @foreach ($permissions as $index => $permission)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]"
                                        value="{{ $permission->name }}"
                                        {{ in_array($permission->name, $role->permissions->pluck('name')->toArray()) ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ ucfirst($permission->name) }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Actualizar Rol</button>
            </form>

        </div>
    </div>

@endsection
