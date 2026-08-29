@extends('adminlte::page')

@section('title', 'Editar Usuario')

@section('content_header')
    <h1>Editar Usuario: {{ $user->name }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="Nombre de Usuario" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="Email del Usuario" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Nueva Contraseña">
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Confirmar Nueva Contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Confirmar Nueva Contraseña">
                </div>
                <div class="form-group">
                    <label for="role">Rol</label>
                    <select name="role" class="form-control" id="role" required>
                        <option value="usuario" {{ old('role', $user->role) == 'usuario' ? 'selected' : '' }}>Usuario</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrador</option>
                    </select>
                    @error('role')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="avatar">Avatar Actual</label>
                    @if($user->avatar)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" width="100" class="mb-2 img-circle">
                    @else
                        <p>No hay avatar actual.</p>
                    @endif
                    <input type="file" name="avatar" class="form-control-file" id="avatar">
                    @error('avatar')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="banner">Banner Actual</label>
                    @if($user->banner)
                        <img src="{{ $user->banner_url }}" alt="{{ $user->name }} banner" width="200" class="mb-2">
                    @else
                        <p>No hay banner actual.</p>
                    @endif
                    <input type="file" name="banner" class="form-control-file" id="banner">
                    @error('banner')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="bio">Biografía</label>
                    <textarea name="bio" class="form-control" id="bio" rows="3" placeholder="Biografía del usuario">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
            </form>
        </div>
    </div>
@stop
