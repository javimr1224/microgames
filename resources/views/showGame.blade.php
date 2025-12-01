@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-4">{{ $game->name }}</h1>
    <p class="text-gray-700 mb-4">{{ $game->description }}</p>
    <img src="{{ asset($game->image) }}" alt="{{ $game->name }}" class="mb-4">
    <a href="{{ url('/games/' . $game->file) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Play Game</a>
</div>
@endsection
