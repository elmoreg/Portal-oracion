<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <a href="{{ route('articles.index') }}" class="text-indigo-600 hover:underline">&larr; Volver a Artículos</a>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    <h1 class="text-3xl font-bold mb-4">{{ $article->title }}</h1>
                    <div class="text-sm text-gray-500 mb-8 border-b pb-4">
                        Por {{ $article->user->name ?? 'Autor' }} &bull; 
                        Publicado el {{ $article->published_at?->format('d/m/Y') ?? $article->created_at->format('d/m/Y') }}
                    </div>
                    
                    <div class="prose max-w-none">
                        {!! nl2br(e($article->content)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
