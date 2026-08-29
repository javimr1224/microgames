@extends('adminlte::page')

@section('title', 'Editar Juego')

@section('content_header')
    <h1>Editar Juego: {{ $game->name }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.games.update', $game->slug) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="Nombre del Juego" value="{{ old('name', $game->name) }}" required>
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="description">Descripción</label>
                    <textarea name="description" class="form-control" id="description" rows="3" placeholder="Descripción del Juego" required>{{ old('description', $game->description) }}</textarea>
                    @error('description')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="image">Imagen Actual</label>
                    @if($game->image)
                        <img src="{{ $game->image_url }}" alt="{{ $game->name }}" width="150" class="mb-2">
                    @else
                        <p>No hay imagen actual.</p>
                    @endif
                    <input type="file" name="image" class="form-control-file" id="image">
                    @error('image')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="video">Video Actual (GIF/MP4/WebM)</label>
                    @if($game->video)
                        @if(Str::endsWith($game->video_url, '.gif'))
                            <img src="{{ $game->video_url }}" alt="Game GIF" width="150" class="mb-2">
                        @else
                            <video width="150" controls class="mb-2">
                                <source src="{{ $game->video_url }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @endif
                    @else
                        <p>No hay video actual.</p>
                    @endif
                    <input type="file" name="video" class="form-control-file" id="video">
                    @error('video')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="file">Archivo del Juego</label>
                    <input type="text" name="file" class="form-control" id="file" placeholder="Ruta al archivo del juego" value="{{ old('file', $game->file) }}" required>
                    @error('file')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="category">Categoría</label>
                    <input type="text" name="category" class="form-control" id="category" placeholder="Categoría del Juego" value="{{ old('category', $game->category) }}" required>
                    @error('category')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="price">Precio</label>
                    <input type="number" name="price" class="form-control" id="price" step="0.01" placeholder="Precio del Juego" value="{{ old('price', $game->price) }}">
                    @error('price')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="stripe_price_id">ID de Precio Stripe</label>
                    <input type="text" name="stripe_price_id" class="form-control" id="stripe_price_id" placeholder="ID de Precio en Stripe" value="{{ old('stripe_price_id', $game->stripe_price_id) }}">
                    @error('stripe_price_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="recommended" class="form-check-input" id="recommended" value="1" {{ old('recommended', $game->recommended) ? 'checked' : '' }}>
                        <label class="form-check-label" for="recommended">Recomendado</label>
                    </div>
                    @error('recommended')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Actualizar Juego</button>
            </form>
        </div>
    </div>
@stop
