<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Compartir Testimonio') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-gray-900">
                    
                    <h3 class="text-2xl font-bold mb-4">¡Cuéntanos tu historia!</h3>
                    <p class="text-gray-600 mb-8">
                        Nos encantaría escuchar cómo este portal ha impactado tu vida. Tu testimonio puede ser de gran bendición e inspiración para otros.
                    </p>

                    <form action="{{ route('testimonies.store') }}" method="POST">
                        @csrf

                        <div class="mb-6">
                            <label for="name" class="block font-medium text-sm text-gray-700">Tu Nombre (o seudónimo si prefieres anonimato)</label>
                            <input type="text" name="name" id="name" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" value="{{ old('name', auth()->user()->name ?? '') }}" required autofocus>
                            @error('name')
                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="content" class="block font-medium text-sm text-gray-700">Tu Testimonio</label>
                            <textarea name="content" id="content" rows="6" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>{{ old('content') }}</textarea>
                            <p class="text-sm text-gray-500 mt-2">Máximo 2000 caracteres.</p>
                            @error('content')
                                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('testimonies.index') }}" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
                                Cancelar
                            </a>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Enviar Testimonio
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
