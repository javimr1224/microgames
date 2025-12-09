@extends('adminlte::page')

@section('title', 'Usuarios Registrados')

@section('content_header')
    <h1>Usuarios Registrados</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Crear Usuario</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 10px">Id</th>
                            <th>Nombre</th>
                            <th class="d-none d-sm-table-cell">Email</th>
                            <th class="d-none d-md-table-cell">Rol</th>
                            <th class="d-none d-lg-table-cell">Bio</th>
                            <th class="d-none d-lg-table-cell">Registrado</th>
                            <th style="width: 120px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td class="d-none d-sm-table-cell">{{ $user->email }}</td>
                                <td class="d-none d-md-table-cell">{{ $user->role }}</td>
                                <td class="d-none d-lg-table-cell">{{ Str::limit($user->bio, 50) }}</td>
                                <td class="d-none d-lg-table-cell">{{ $user->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-secondary dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <div class="dropdown-menu" role="menu">
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="dropdown-item">
                                                <i class="fas fa-pencil-alt mr-2"></i>
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
                                                    <i class="fas fa-trash mr-2"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
