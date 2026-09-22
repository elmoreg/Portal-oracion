<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Artículos para Fortalecer tu Fe') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if($articles->isEmpty())
                        <p class="text-center text-gray-500 py-8">Aún no hay artículos publicados.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($articles as $article)
                                <div class="border rounded-lg p-6 shadow-sm hover:shadow-md transition">
                                    <h3 class="text-xl font-bold mb-2">
                                        <a href="{{ route('articles.show', $article->slug) }}" class="text-indigo-600 hover:text-indigo-800">
                                            {{ $article->title }}
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500 mb-4">
                                        Publicado el {{ $article->published_at?->format('d/m/Y') ?? $article->created_at->format('d/m/Y') }}
                                    </p>
                                    <p class="text-gray-700">
                                        {{ Str::limit(strip_tags($article->content), 120) }}
                                    </p>
                                    <div class="mt-4">
                                        <a href="{{ route('articles.show', $article->slug) }}" class="text-sm font-semibold text-indigo-600 hover:underline">Leer más &rarr;</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-8">
                            {{ $articles->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
