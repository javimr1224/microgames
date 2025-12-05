@extends('adminlte::page')

@section('title', 'Configuración de Administrador')

@section('content_header')
    <h1>Configuración del Perfil</h1>
@stop

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
@stop

@section('css')
    <style>
        .p-4 .x-primary-button {
            background-color: #000000 !important; /* Black */
            border-color: #000000 !important;
        }
        .p-4 .x-primary-button:hover {
            background-color: #333333 !important; /* Darker gray on hover */
            border-color: #333333 !important;
        }
    </style>
@stop