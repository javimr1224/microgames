@extends('adminlte::page')

@section('title', 'Ingresos')

@section('content_header')
    <h1>Ingresos</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Listado de Ingresos</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($charges->data as $charge)
                        <tr>
                            <td>{{ $charge->id }}</td>
                            <td>{{ number_format($charge->amount / 100, 2) }} €</td>
                            <td>{{ \Carbon\Carbon::createFromTimestamp($charge->created)->toDateTimeString() }}</td>
                            <td>{{ $charge->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
