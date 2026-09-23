@props([
    'url',
    'text' => '',
    'imageSquare' => null,
    'imageStory' => null,
])

@php
    $encodedUrl = urlencode($url);
    $encodedText = urlencode($text);
    $whatsapp = "https://wa.me/?text={$encodedText}%20{$encodedUrl}";
    $facebook = "https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}";
    $twitter = "https://twitter.com/intent/tweet?text={$encodedText}&url={$encodedUrl}";
    $telegram = "https://t.me/share/url?url={$encodedUrl}&text={$encodedText}";
@endphp

<div
    x-data="{
        url: @js($url),
        text: @js($text),
        imageSquare: @js($imageSquare),
        imageStory: @js($imageStory),
        copied: false,
        sharingImage: false,
        async nativeShare() {
            if (navigator.share) {
                try {
                    await navigator.share({ title: this.text, text: this.text, url: this.url });
                } catch (e) { /* cancelado por el usuario */ }
            } else {
                this.copyLink();
            }
        },
        copyLink() {
            navigator.clipboard.writeText(this.url).then(() => {
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            });
        },
        async shareImage(imageUrl) {
            // Comparte el archivo de imagen directamente: en móvil, Instagram
            // (y otras apps) aparecen en el menú nativo de compartir.
            try {
                this.sharingImage = true;
                const response = await fetch(imageUrl);
                const blob = await response.blob();
                const file = new File([blob], 'peticion-oracion.png', { type: 'image/png' });

                if (navigator.canShare && navigator.canShare({ files: [file] })) {
                    await navigator.share({ files: [file], text: this.text });
                } else {
                    // Escritorio o navegador sin soporte: descarga la imagen.
                    const link = document.createElement('a');
                    link.href = imageUrl;
                    link.download = 'peticion-oracion.png';
                    link.click();
                }
            } catch (e) { /* cancelado por el usuario */ }
            finally { this.sharingImage = false; }
        },
    }}
    {{ $attributes->merge(['class' => 'rounded-xl border border-stone-200 bg-white p-5']) }}
>
    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">{{ __('Comparte esta petición') }}</p>

    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ $whatsapp }}" target="_blank" rel="noopener" aria-label="WhatsApp"
           class="inline-flex items-center gap-2 rounded-lg bg-[#25D366] px-3 py-2 text-xs font-bold text-white transition hover:opacity-90">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163a11.867 11.867 0 01-1.587-5.946C.16 5.335 5.495 0 12.05 0a11.82 11.82 0 018.413 3.488 11.82 11.82 0 013.48 8.414c-.003 6.557-5.338 11.892-11.893 11.892a11.9 11.9 0 01-5.688-1.448L.057 24zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884a9.86 9.86 0 001.599 5.31l-.999 3.648 3.9-.757zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.767.967-.94 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.71.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
            WhatsApp
        </a>

        <a href="{{ $facebook }}" target="_blank" rel="noopener" aria-label="Facebook"
           class="inline-flex items-center gap-2 rounded-lg bg-[#1877F2] px-3 py-2 text-xs font-bold text-white transition hover:opacity-90">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            Facebook
        </a>

        <a href="{{ $twitter }}" target="_blank" rel="noopener" aria-label="X"
           class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-3 py-2 text-xs font-bold text-white transition hover:opacity-90">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            X
        </a>

        <a href="{{ $telegram }}" target="_blank" rel="noopener" aria-label="Telegram"
           class="inline-flex items-center gap-2 rounded-lg bg-[#0088cc] px-3 py-2 text-xs font-bold text-white transition hover:opacity-90">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 000 12a12 12 0 0012 12 12 12 0 0012-12A12 12 0 0012 0a12 12 0 00-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 01.171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.910.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
            Telegram
        </a>

        <button type="button" @click="nativeShare()"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-3 py-2 text-xs font-bold text-slate-950 transition hover:bg-amber-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path></svg>
            {{ __('Compartir') }}
        </button>

        <button type="button" @click="copyLink()"
                class="inline-flex items-center gap-2 rounded-lg border border-stone-300 px-3 py-2 text-xs font-bold text-slate-700 transition hover:bg-stone-100">
            <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
            <svg x-show="copied" x-cloak class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            <span x-text="copied ? @js(__('¡Enlace copiado!')) : @js(__('Copiar enlace'))"></span>
        </button>
    </div>

    @if ($imageSquare || $imageStory)
        <div class="mt-5 pt-5 border-t border-stone-200">
            <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">{{ __('Para Instagram') }}</p>
            <p class="text-[11px] text-stone-500 mb-3">{{ __('Instagram no admite enlaces: comparte o descarga la imagen y publícala.') }}</p>

            <div class="flex flex-wrap items-center gap-2">
                @if ($imageSquare)
                    <button type="button" @click="shareImage(imageSquare)" :disabled="sharingImage"
                            class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-tr from-[#feda75] via-[#d62976] to-[#4f5bd5] px-3 py-2 text-xs font-bold text-white transition hover:opacity-90 disabled:opacity-60">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.2c3.2 0 3.6 0 4.9.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.27.07 1.65.07 4.87s0 3.6-.07 4.87c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.27.06-1.65.07-4.87.07s-3.6 0-4.87-.07c-1.17-.05-1.8-.25-2.23-.41a3.7 3.7 0 01-1.38-.9 3.7 3.7 0 01-.9-1.38c-.16-.42-.36-1.06-.41-2.23C2.2 15.6 2.2 15.2 2.2 12s0-3.6.07-4.87c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41C8.4 2.2 8.8 2.2 12 2.2m0 1.98c-3.15 0-3.52.01-4.76.07-.9.04-1.39.19-1.71.32-.43.17-.74.37-1.06.69-.32.32-.52.63-.69 1.06-.13.32-.28.81-.32 1.71-.06 1.24-.07 1.61-.07 4.76s.01 3.52.07 4.76c.04.9.19 1.39.32 1.71.17.43.37.74.69 1.06.32.32.63.52 1.06.69.32.13.81.28 1.71.32 1.24.06 1.61.07 4.76.07s3.52-.01 4.76-.07c.9-.04 1.39-.19 1.71-.32.43-.17.74-.37 1.06-.69.32-.32.52-.63.69-1.06.13-.32.28-.81.32-1.71.06-1.24.07-1.61.07-4.76s-.01-3.52-.07-4.76c-.04-.9-.19-1.39-.32-1.71a2.85 2.85 0 00-.69-1.06 2.85 2.85 0 00-1.06-.69c-.32-.13-.81-.28-1.71-.32-1.24-.06-1.61-.07-4.76-.07m0 3.37a4.45 4.45 0 110 8.9 4.45 4.45 0 010-8.9m0 7.34a2.89 2.89 0 100-5.78 2.89 2.89 0 000 5.78m5.67-7.56a1.04 1.04 0 11-2.08 0 1.04 1.04 0 012.08 0"/></svg>
                        <span x-text="sharingImage ? @js(__('Preparando…')) : @js(__('Compartir imagen'))"></span>
                    </button>
                @endif

                @if ($imageSquare)
                    <a href="{{ $imageSquare }}&descargar=1" download
                       class="inline-flex items-center gap-2 rounded-lg border border-stone-300 px-3 py-2 text-xs font-bold text-slate-700 transition hover:bg-stone-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        {{ __('Descargar post') }}
                    </a>
                @endif

                @if ($imageStory)
                    <a href="{{ $imageStory }}&descargar=1" download
                       class="inline-flex items-center gap-2 rounded-lg border border-stone-300 px-3 py-2 text-xs font-bold text-slate-700 transition hover:bg-stone-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        {{ __('Descargar story') }}
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>
