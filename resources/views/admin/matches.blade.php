@extends('adminlte::page')

@section('title', 'Partidas')

@section('content_header')
    <h1>Partidas</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
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
                    @foreach($scores as $score)
                        <tr>
                            <td>{{ $score->id }}</td>
                            <td>{{ $score->user->name ?? 'N/A' }}</td>
                            <td>{{ $score->game->name ?? 'N/A' }}</td>
                            <td>{{ $score->score }}</td>
                            <td>{{ $score->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
