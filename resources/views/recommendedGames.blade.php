@php use Illuminate\Support\Str; @endphp
@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-4">Recommended Games</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($games as $game)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <img src="{{ asset($game->image) }}" alt="{{ $game->name }}" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h2 class="text-xl font-bold mb-2">{{ $game->name }}</h2>
                        <p class="text-gray-700 text-sm mb-4">{{ Str::limit($game->description, 100) }}</p>
                        <a href="{{ route('games.show', $game->id) }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Play Now</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
