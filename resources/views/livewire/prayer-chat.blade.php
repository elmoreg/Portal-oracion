<div wire:poll.5s="refreshMessages" class="bg-white border border-gray-100 shadow-xl rounded-2xl overflow-hidden flex flex-col h-[500px]">
    <div class="px-6 py-4 border-b border-gray-100 bg-white">
        <h3 class="text-base font-bold text-soul-indigo flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse shadow-sm"></span>
            {{ __('Acompañamiento en oración') }}
        </h3>
        <p class="text-xs text-gray-500 mt-1 font-medium">{{ __('Este es un espacio seguro y confidencial. Solo los intercesores asignados y tú pueden ver estos mensajes.') }}</p>
    </div>

    <div class="flex-1 overflow-y-auto px-6 py-4 space-y-6 bg-gray-50" id="chat-messages-container">
        @forelse ($messages as $message)
            @php
                $mine = $message->author_type === $viewerRole && $message->user_id === $viewerUserId;
            @endphp
            <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }} animate-fade-in-up" style="animation-duration: 0.3s;">
                <div class="flex items-end gap-3 max-w-[80%] {{ $mine ? 'flex-row-reverse' : 'flex-row' }}">
                    <!-- Avatar -->
                    <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 text-xs font-bold shadow-sm {{ $mine ? 'bg-soul-gold text-white' : 'bg-soul-indigo text-white' }}">
                        {{ substr($message->author_type->label(), 0, 1) }}
                    </div>
                    
                    <!-- Bubble -->
                    <div class="flex flex-col {{ $mine ? 'items-end' : 'items-start' }}">
                        <span class="text-[10px] font-bold text-gray-400 mb-1 px-1 uppercase tracking-wider">{{ $message->author_type->label() }}</span>
                        <div class="px-4 py-2.5 shadow-sm text-sm whitespace-pre-wrap break-words {{ $mine ? 'bg-soul-indigo text-white rounded-2xl rounded-br-sm' : 'bg-white border border-gray-200 text-soul-indigo rounded-2xl rounded-bl-sm' }}">{{ $message->translated_body }}</div>
                        <span class="text-[10px] text-gray-400 mt-1 px-1 font-medium">{{ $message->created_at->format('H:i') }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="h-full flex flex-col items-center justify-center text-center px-4">
                <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shadow-md border border-gray-100 mb-4 text-soul-gold">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                </div>
                <p class="text-sm font-bold text-soul-indigo">{{ __('El chat está vacío') }}</p>
                <p class="text-xs text-gray-500 mt-2 max-w-xs font-medium leading-relaxed">{{ __('Escribe tu primer mensaje. Un intercesor te responderá pronto para acompañarte.') }}</p>
            </div>
        @endforelse
    </div>

    <form wire:submit="sendMessage" class="bg-white border-t border-gray-100 p-4">
        <div class="relative flex items-center">
            <label for="chat-body-{{ $prayerRequest->id }}" class="sr-only">{{ __('Mensaje') }}</label>
            <textarea
                wire:model="body"
                id="chat-body-{{ $prayerRequest->id }}"
                rows="1"
                class="w-full bg-gray-50 border border-gray-200 text-soul-indigo text-sm rounded-full pl-5 pr-12 py-3 focus:border-soul-gold focus:ring-soul-gold resize-none transition-colors shadow-inner"
                placeholder="{{ __('Escribe un mensaje...') }}"
                x-data="{ resize() { $el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 120) + 'px'; } }"
                x-init="resize()"
                @input="resize()"
            ></textarea>
            
            <button type="submit" class="absolute right-2 p-2 bg-soul-indigo text-white rounded-full hover:bg-slate-800 hover:shadow-md transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-4 h-4 translate-x-px -translate-y-px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
            </button>
        </div>
        @error('body')
            <p class="text-xs text-red-500 mt-2 ml-4 font-medium">{{ $message }}</p>
        @enderror
    </form>
    
    <script>
        document.addEventListener('livewire:initialized', () => {
            const container = document.getElementById('chat-messages-container');
            if(container) {
                container.scrollTop = container.scrollHeight;
            }
            Livewire.on('message-sent', () => {
                setTimeout(() => {
                    if(container) container.scrollTop = container.scrollHeight;
                }, 100);
            });
        });
    </script>
</div>
