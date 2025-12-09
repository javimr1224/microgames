@extends('adminlte::page')

@section('title', 'Crear Juego')

@section('content_header')
    <h1>Crear Nuevo Juego</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.games.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="name">Nombre</label>
                    <input type="text" name="name" class="form-control" id="name" placeholder="Nombre del Juego" value="{{ old('name') }}" required>
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="description">Descripción</label>
                    <textarea name="description" class="form-control" id="description" rows="3" placeholder="Descripción del Juego" required>{{ old('description') }}</textarea>
                    @error('description')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="image">Imagen</label>
                    <input type="file" name="image" class="form-control-file" id="image">
                    @error('image')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="video">Video (GIF/MP4/WebM)</label>
                    <input type="file" name="video" class="form-control-file" id="video">
                    @error('video')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="file">Archivo del Juego</label>
                    <input type="text" name="file" class="form-control" id="file" placeholder="Ruta al archivo del juego" value="{{ old('file') }}" required>
                    @error('file')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="category">Categoría</label>
                    <input type="text" name="category" class="form-control" id="category" placeholder="Categoría del Juego" value="{{ old('category') }}" required>
                    @error('category')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="price">Precio</label>
                    <input type="number" name="price" class="form-control" id="price" step="0.01" placeholder="Precio del Juego" value="{{ old('price', 0.00) }}">
                    @error('price')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="stripe_price_id">ID de Precio Stripe</label>
                    <input type="text" name="stripe_price_id" class="form-control" id="stripe_price_id" placeholder="ID de Precio en Stripe" value="{{ old('stripe_price_id') }}">
                    @error('stripe_price_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" name="recommended" class="form-check-input" id="recommended" value="1" {{ old('recommended') ? 'checked' : '' }}>
                        <label class="form-check-label" for="recommended">Recomendado</label>
                    </div>
                    @error('recommended')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Crear Juego</button>
            </form>
        </div>
    </div>
@stop
