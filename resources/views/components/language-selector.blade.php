@props([
    'variant' => 'dark', // 'dark' for dark navbar / slate-950, 'light' for light backgrounds
])

@php
    $currentLocale = app()->getLocale();
    $locales = config('app.available_locales', [
        'es' => ['name' => 'Español', 'native' => 'Español', 'flag' => '🇪🇸', 'dir' => 'ltr'],
        'en' => ['name' => 'English', 'native' => 'English', 'flag' => '🇺🇸', 'dir' => 'ltr'],
        'pt' => ['name' => 'Portuguese', 'native' => 'Português', 'flag' => '🇧🇷', 'dir' => 'ltr'],
        'it' => ['name' => 'Italian', 'native' => 'Italiano', 'flag' => '🇮🇹', 'dir' => 'ltr'],
        'ar' => ['name' => 'Arabic', 'native' => 'العربية', 'flag' => '🇸🇦', 'dir' => 'rtl'],
    ]);
    $current = $locales[$currentLocale] ?? $locales['es'];
@endphp

<div x-data="{ open: false }" @click.outside="open = false" class="relative inline-block text-start">
    <button @click="open = !open" 
            type="button"
            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-semibold uppercase tracking-wider transition-all duration-200 border {{ $variant === 'dark' ? 'bg-slate-900/80 border-white/10 text-stone-200 hover:text-amber-400 hover:border-amber-400/50' : 'bg-white border-stone-200 text-slate-700 hover:text-amber-600 hover:border-amber-300 shadow-sm' }}"
            aria-expanded="false" 
            aria-haspopup="true">
        <span class="text-sm leading-none">{{ $current['flag'] }}</span>
        <span class="font-medium">{{ $current['native'] }}</span>
        <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    <div x-show="open" 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-50 mt-2 w-44 rounded-xl shadow-2xl py-1.5 border overflow-hidden {{ $currentLocale === 'ar' ? 'left-0' : 'right-0' }} {{ $variant === 'dark' ? 'bg-slate-900 border-white/10 text-white' : 'bg-white border-stone-100 text-slate-800' }}"
         style="display: none;">
        @foreach($locales as $code => $info)
            <a href="{{ route('locale.switch', $code) }}"
               class="flex items-center justify-between px-3.5 py-2 text-xs font-medium transition-colors {{ $currentLocale === $code ? ($variant === 'dark' ? 'bg-amber-500/20 text-amber-300 font-bold' : 'bg-amber-50 text-amber-700 font-bold') : ($variant === 'dark' ? 'hover:bg-white/5 hover:text-amber-300' : 'hover:bg-stone-50 hover:text-amber-600') }}">
                <div class="flex items-center gap-2.5">
                    <span class="text-base leading-none">{{ $info['flag'] }}</span>
                    <span>{{ $info['native'] }}</span>
                </div>
                @if($currentLocale === $code)
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                @endif
            </a>
        @endforeach
    </div>
</div>
