@extends('adminlte::page')

@section('title', 'Pages')

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
                        <th>Concepto</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Skybound</td>
                        <td>3.99 €</td>
                        <td>2025-12-05</td>
                        <td>Completado</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@stop
