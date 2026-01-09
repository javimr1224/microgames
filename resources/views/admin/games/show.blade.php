@extends('adminlte::page')

@section('title', 'Ver Juego')

@section('content_header')
    <h1>Ver Juego: {{ $game->name }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label for="name">Nombre</label>
                <p>{{ $game->name }}</p>
            </div>
            <div class="form-group">
                <label for="description">Descripción</label>
                <p>{{ $game->description }}</p>
            </div>
            <div class="form-group">
                <label for="image">Imagen Actual</label>
                @if($game->image)
                    <img src="{{ asset($game->image) }}" alt="{{ $game->name }}" width="150" class="mb-2">
                @else
                    <p>No hay imagen actual.</p>
                @endif
            </div>
            <div class="form-group">
                <label for="video">Video Actual (GIF/MP4/WebM)</label>
                @if($game->video)
                    @if(Str::endsWith($game->video, '.gif'))
                        <img src="{{ asset($game->video) }}" alt="Game GIF" width="150" class="mb-2">
                    @else
                        <video width="150" controls class="mb-2">
                            <source src="{{ asset($game->video) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @endif
                @else
                    <p>No hay video actual.</p>
                @endif
            </div>
            <div class="form-group">
                <label for="file">Archivo del Juego</label>
                <p>{{ $game->file }}</p>
            </div>
            <div class="form-group">
                <label for="category">Categoría</label>
                <p>{{ $game->category }}</p>
            </div>
            <div class="form-group">
                <label for="price">Precio</label>
                <p>{{ $game->price }}</p>
            </div>
            <div class="form-group">
                <label for="stripe_price_id">ID de Precio Stripe</label>
                <p>{{ $game->stripe_price_id }}</p>
            </div>
            <div class="form-group">
                <label for="recommended">Recomendado</label>
                <p>{{ $game->recommended ? 'Sí' : 'No' }}</p>
            </div>
            <a href="{{ route('admin.games.index') }}" class="btn btn-primary">Volver a la lista</a>
        </div>
    </div>
@stop
