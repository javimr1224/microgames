@extends('adminlte::page')

@section('title', 'Partidas')

@section('content_header')
    <h1>Partidas</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 10px">Id</th>
                            <th>Usuario</th>
                            <th>Juego</th>
                            <th>Puntuación</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($scores as $score)
                            <tr>
                                <td>{{ $score->id }}</td>
                                <td>{{ $score->user->name ?? 'N/A' }}</td>
                                <td>{{ $score->game->name ?? 'N/A' }}</td>
                                <td>{{ $score->score }}</td>
                                <td>{{ $score->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay partidas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $scores->links() }}
        </div>
    </div>
@stop
