@extends('adminlte::page')

@section('title', 'MicroGames Admin Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalUsers ?? 0 }}</h3>
                    <p>Usuarios Registrados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('admin.users') }}" class="small-box-footer">
                    Más info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalGames ?? 0 }}</h3>
                    <p>Juegos Activos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-gamepad"></i>
                </div>
                <a href="{{ route('admin.games') }}" class="small-box-footer">
                    Más info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $todaySessions ?? 0 }}</h3>
                    <p>Partidas Hoy</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="{{ route('admin.matches') }}" class="small-box-footer">
                    Más info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>${{ $revenue ?? 0 }}</h3>
                    <p>Ingresos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <a href="{{ route('admin.incomes') }}" class="small-box-footer">
                    Más info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Recent activity table --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Actividad Reciente</h3>
        </div>
        <div class="card-body table-responsive p-0" style="max-height: 300px;">
            <table class="table table-head-fixed text-nowrap">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Acción</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentActivities ?? [] as $activity)
                        <tr>
                            <td>{{ $activity->user->name }}</td>
                            <td>{{ $activity->action }}</td>
                            <td>{{ $activity->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                    @if(empty($recentActivities))
                        <tr>
                            <td colspan="3" class="text-center">No hay actividad reciente</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Quick action buttons --}}
    <div class="row">
        <div class="col-3">
            <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-block">
                <i class="fas fa-plus"></i> Nuevo Juego
            </a>
        </div>
        <div class="col-3">
            <a href="{{ route('admin.users') }}" class="btn btn-success btn-block">
                <i class="fas fa-users"></i> Administrar Usuarios
            </a>
        </div>
        <div class="col-3">
            <a href="{{ route('admin.matches') }}" class="btn btn-warning btn-block">
                <i class="fas fa-gamepad"></i> Ver Partidas
            </a>
        </div>
        <div class="col-3">
            <a href="{{ route('admin.incomes') }}" class="btn btn-danger btn-block">
                <i class="fas fa-dollar-sign"></i> Ingresos
            </a>
        </div>
    </div>
@stop

@section('css')
    {{-- agregar CSS en un futuro para personalizacion adaptada --}}
@stop

@section('js')
    <script>
        console.log('AdminLTE dashboard cargado para Microgames');
    </script>
@stop
