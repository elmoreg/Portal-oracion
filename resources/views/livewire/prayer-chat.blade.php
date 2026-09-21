<div wire:poll.5s="refreshMessages" class="border border-gray-200 rounded-lg bg-white">
    <div class="px-4 py-3 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-700">Conversación privada</h3>
        <p class="text-xs text-gray-500">Solo la persona que hizo la petición y quienes oran por ella pueden ver estos mensajes.</p>
    </div>

    <div class="max-h-96 overflow-y-auto px-4 py-3 space-y-3">
        @forelse ($messages as $message)
            @php
                $mine = $message->author_type === $viewerRole && $message->user_id === $viewerUserId;
            @endphp
            <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[80%] rounded-lg px-3 py-2 {{ $mine ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-800' }}">
                    <p class="text-xs font-medium {{ $mine ? 'text-indigo-100' : 'text-gray-500' }} mb-0.5">
                        {{ $message->author_type->label() }}
                    </p>
                    <p class="text-sm whitespace-pre-wrap break-words">{{ $message->body }}</p>
                    <p class="text-[10px] mt-1 {{ $mine ? 'text-indigo-200' : 'text-gray-400' }}">
                        {{ $message->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400 italic">Todavía no hay mensajes. Este espacio es para acompañamiento y consejería si la persona lo necesita.</p>
        @endforelse
    </div>

    <form wire:submit="sendMessage" class="border-t border-gray-100 p-3 flex gap-2">
        <label for="chat-body-{{ $prayerRequest->id }}" class="sr-only">Mensaje</label>
        <textarea
            wire:model="body"
            id="chat-body-{{ $prayerRequest->id }}"
            rows="1"
            class="flex-1 rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
            placeholder="Escribí un mensaje..."
        ></textarea>
        <x-primary-button type="submit">Enviar</x-primary-button>
    </form>
    @error('body')
        <p class="text-xs text-red-600 px-3 pb-2">{{ $message }}</p>
    @enderror
</div>
