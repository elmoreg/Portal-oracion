<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __(config('app.name', 'Portal de Oración')) }} | {{ __('Unidos en Fe y Oración') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased text-slate-800 bg-stone-50 selection:bg-amber-500 selection:text-white overflow-x-hidden">

    <!-- Top Navigation Bar (NewLife Church Style) -->
    <header class="w-full bg-slate-950/95 backdrop-blur-md text-white sticky top-0 z-50 border-b border-white/10 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- Brand Logo -->
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-slate-950 shadow-lg shadow-amber-500/20 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 3v18M6 9h12"></path>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="tracking-wider text-xl font-bold text-white group-hover:text-amber-400 transition-colors uppercase">
                            {{ __('Portal de Oración') }}
                        </span>
                        <span class="text-[10px] uppercase tracking-[0.25em] text-amber-400/90 font-medium">{{ __('Comunidad & Fe') }}</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center space-x-6 rtl:space-x-reverse text-xs uppercase tracking-widest font-semibold">
                    <a href="#proposito" class="text-stone-300 hover:text-amber-400 transition-colors">{{ __('Propósito') }}</a>
                    <a href="#muro" class="text-stone-300 hover:text-amber-400 transition-colors">{{ __('Muro de Oración') }}</a>
                    <a href="#como-funciona" class="text-stone-300 hover:text-amber-400 transition-colors">{{ __('Cómo Funciona') }}</a>
                    <a href="#versiculo" class="text-stone-300 hover:text-amber-400 transition-colors">{{ __('Versículo') }}</a>
                    
                    <x-language-selector variant="dark" />

                    @auth
                        <a href="{{ route('dashboard') }}" wire:navigate class="px-5 py-2.5 rounded border border-amber-500/50 bg-amber-500/10 text-amber-400 hover:bg-amber-500 hover:text-slate-950 transition-all">
                            {{ __('Mi Panel') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" wire:navigate class="text-stone-300 hover:text-amber-400 transition-colors">
                            {{ __('Iniciar Sesión') }}
                        </a>
                        <a href="{{ route('prayer.create') }}" wire:navigate class="px-6 py-2.5 rounded bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold transition-all shadow-md shadow-amber-500/20 hover:shadow-lg hover:-translate-y-0.5">
                            {{ __('Pedir Oración') }}
                        </a>
                    @endauth
                </nav>

                <!-- Mobile Menu Items -->
                <div class="md:hidden flex items-center gap-3">
                    <x-language-selector variant="dark" />

                    <a href="{{ route('prayer.create') }}" wire:navigate class="text-xs px-3.5 py-2 rounded bg-amber-500 text-slate-950 font-bold">
                        {{ __('Pedir Oración') }}
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Fullscreen Slider (NewLife Revolution Slider Style) -->
    <section class="relative bg-slate-950 min-h-[580px] lg:min-h-[700px] flex items-center justify-center overflow-hidden" 
             x-data="{ 
                current: 0,
                slides: [
                    {
                        img: 'https://images.unsplash.com/photo-1544427920-c49ccfb85579?auto=format&fit=crop&q=80&w=1920',
                        eyebrow: @js(__('BIENVENIDO AL PORTAL DE ORACIÓN')),
                        title: @js(__('No tienes por qué llevar tus cargas a solas')),
                        subtitle: @js(__('Una comunidad dispuesta a interceder por ti. Confidencialidad absoluta, acompañamiento genuino y fe en acción.'))
                    },
                    {
                        img: 'https://images.unsplash.com/photo-1519491050282-cf00c82424b4?auto=format&fit=crop&q=80&w=1920',
                        eyebrow: @js(__('UNIDOS EN INTERCESIÓN')),
                        title: @js(__('Creemos en el poder transformador de la oración')),
                        subtitle: @js(__('Peticiones atendidas con amor y dedicación por creyentes comprometidos con tu bienestar espiritual.'))
                    },
                    {
                        img: 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&q=80&w=1920',
                        eyebrow: @js(__('SÉ PARTE DEL MINISTERIO')),
                        title: @js(__('Acompaña a quienes más lo necesitan hoy')),
                        subtitle: @js(__('Inscríbete como intercesor y sé el canal de esperanza y consuelo para cientos de personas.'))
                    }
                ],
                next() { this.current = (this.current + 1) % this.slides.length },
                prev() { this.current = (this.current - 1 + this.slides.length) % this.slides.length },
                init() {
                    setInterval(() => { this.next() }, 6000);
                }
             }">

        <!-- Background Slides with Overlay -->
        <template x-for="(slide, index) in slides" :key="index">
            <div class="absolute inset-0 transition-opacity duration-1000 ease-in-out"
                 :class="current === index ? 'opacity-100' : 'opacity-0 pointer-events-none'">
                <img :src="slide.img" 
                     onerror="this.onerror=null; this.src='{{ asset('images/hero.jpg') }}';"
                     class="w-full h-full object-cover transform scale-105 transition-transform duration-[6000ms]" 
                     :class="current === index ? 'scale-100' : 'scale-105'" 
                     :alt="slide.title">
                <!-- Multi-layer Church Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/70 to-slate-950/50"></div>
                <div class="absolute inset-0 bg-slate-950/40"></div>
            </div>
        </template>

        <!-- Static Fallback Image for Instant First Load -->
        <div class="absolute inset-0 -z-10">
            <img src="https://images.unsplash.com/photo-1544427920-c49ccfb85579?auto=format&fit=crop&q=80&w=1920" class="w-full h-full object-cover" alt="{{ __('Portal de Oración') }}">
            <div class="absolute inset-0 bg-slate-950/75"></div>
        </div>

        <!-- Slider Content Area -->
        <div class="relative z-20 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center">
            
            <!-- Eyebrow Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-amber-500/20 border border-amber-400/40 text-amber-300 text-xs font-bold tracking-[0.2em] uppercase mb-6 animate-fade-in">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                <span x-text="slides[current].eyebrow"></span>
            </div>

            <!-- Big Title -->
            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold text-white tracking-tight leading-[1.1] mb-6 max-w-4xl mx-auto drop-shadow-md min-h-[120px] flex items-center justify-center"
                x-text="slides[current].title">
            </h1>

            <!-- Subtitle -->
            <p class="text-stone-300 text-base sm:text-lg md:text-xl max-w-2xl mx-auto mb-10 leading-relaxed font-light"
               x-text="slides[current].subtitle">
            </p>

            <!-- Dual CTA Action Buttons (NewLife style) -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6">
                <a href="{{ route('prayer.create') }}" wire:navigate 
                   class="w-full sm:w-auto px-8 py-4 rounded bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold uppercase tracking-wider text-sm transition-all shadow-xl shadow-amber-500/25 hover:shadow-amber-500/40 hover:-translate-y-1">
                    {{ __('Pedir Oración Ahora') }}
                </a>
                
                <a href="{{ route('register') }}" wire:navigate 
                   class="w-full sm:w-auto px-8 py-4 rounded border-2 border-white/80 hover:border-amber-400 bg-white/5 hover:bg-white/10 text-white hover:text-amber-300 font-bold uppercase tracking-wider text-sm transition-all backdrop-blur-sm hover:-translate-y-1">
                    {{ __('Unirme como Intercesor') }}
                </a>
            </div>

            <!-- Slider Dots Indicator -->
            <div class="flex justify-center items-center gap-3 mt-12">
                <template x-for="(slide, idx) in slides" :key="idx">
                    <button @click="current = idx" 
                            class="h-2 rounded-full transition-all duration-300"
                            :class="current === idx ? 'w-8 bg-amber-400' : 'w-2 bg-white/40 hover:bg-white/70'"
                            :aria-label="'Slide ' + (idx + 1)">
                    </button>
                </template>
            </div>
        </div>

        <!-- Slider Arrows -->
        <button @click="prev()" class="hidden md:flex absolute left-6 rtl:left-auto rtl:right-6 z-30 w-12 h-12 rounded-full bg-slate-900/60 border border-white/10 text-white hover:text-amber-400 hover:bg-slate-900 items-center justify-center transition-all">
            <svg class="w-6 h-6 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <button @click="next()" class="hidden md:flex absolute right-6 rtl:right-auto rtl:left-6 z-30 w-12 h-12 rounded-full bg-slate-900/60 border border-white/10 text-white hover:text-amber-400 hover:bg-slate-900 items-center justify-center transition-all">
            <svg class="w-6 h-6 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
    </section>

    <!-- Trust & Pillars Strip (NewLife Next Event / Feature Banner Style) -->
    <section class="bg-gradient-to-r from-amber-600 via-amber-500 to-amber-600 text-slate-950 py-7 shadow-lg relative z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8 items-center text-center md:text-start divide-y md:divide-y-0 md:divide-x rtl:md:divide-x-reverse divide-slate-950/15">
                
                <!-- Pillar 1 -->
                <div class="flex items-center justify-center md:justify-start gap-4 pt-4 md:pt-0">
                    <div class="w-12 h-12 rounded-full bg-slate-950/10 flex items-center justify-center text-slate-950 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-base tracking-tight leading-tight">{{ __('100% Confidencial y Seguro') }}</h4>
                        <p class="text-xs text-slate-950/80 font-medium mt-0.5">{{ __('Tus peticiones pueden ser totalmente anónimas') }}</p>
                    </div>
                </div>

                <!-- Pillar 2 -->
                <div class="flex items-center justify-center md:justify-start gap-4 pt-4 md:pt-0 md:ps-8">
                    <div class="w-12 h-12 rounded-full bg-slate-950/10 flex items-center justify-center text-slate-950 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-base tracking-tight leading-tight">{{ __('Intercesores Dedicados') }}</h4>
                        <p class="text-xs text-slate-950/80 font-medium mt-0.5">{{ __('Una red activa orando por cada necesidad') }}</p>
                    </div>
                </div>

                <!-- Pillar 3 -->
                <div class="flex items-center justify-center md:justify-start gap-4 pt-4 md:pt-0 md:ps-8">
                    <div class="w-12 h-12 rounded-full bg-slate-950/10 flex items-center justify-center text-slate-950 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-base tracking-tight leading-tight">{{ __('Chat y Seguimiento Privado') }}</h4>
                        <p class="text-xs text-slate-950/80 font-medium mt-0.5">{{ __('Enlace único para recibir respuestas y apoyo') }}</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Welcome & Purpose Section (NewLife 2-Column Story Style) -->
    <section id="proposito" class="py-20 lg:py-28 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-center">
                
                <!-- Left: Framed Church Photo -->
                <div class="relative">
                    <div class="relative z-10 rounded-2xl overflow-hidden shadow-2xl border-4 border-white">
                        <img src="https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&q=80&w=1000" 
                             alt="{{ __('Unidos en Fe y Oración') }}" 
                             class="w-full h-[420px] sm:h-[480px] object-cover hover:scale-105 transition-transform duration-700">
                    </div>
                    <!-- Decorative Gold Frame Offset -->
                    <div class="absolute -bottom-5 -right-5 rtl:-left-5 rtl:right-auto w-full h-full border-2 border-amber-500/40 rounded-2xl -z-0 hidden sm:block"></div>
                    
                    <!-- Floating Ministry Badge -->
                    <div class="absolute -bottom-6 left-6 rtl:left-auto rtl:right-6 z-20 bg-slate-900 text-white p-5 rounded-xl shadow-xl border border-white/10 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg bg-amber-500 text-slate-950 flex items-center justify-center font-bold text-xl">
                            ✝
                        </div>
                        <div>
                            <p class="text-xs text-amber-400 font-bold uppercase tracking-wider">{{ __('Compromiso de Fe') }}</p>
                            <p class="text-sm font-semibold text-white">{{ __('Oración continua y respuesta sincera') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Right: Purpose Narrative -->
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-2">
                        <span class="h-px w-8 bg-amber-500"></span>
                        <span class="text-amber-600 font-bold tracking-[0.2em] uppercase text-xs">{{ __('Nuestro Propósito') }}</span>
                    </div>

                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-slate-900 tracking-tight leading-tight">
                        {{ __('Un espacio de gracia para compartir tus necesidades') }}
                    </h2>

                    <p class="text-slate-600 text-base sm:text-lg leading-relaxed font-light">
                        {{ __('Creemos firmemente en el poder transformador de la oración cuando nos unimos con un mismo corazón. Este portal nació con la misión de ser un puente de esperanza, conectando a personas que atraviesan momentos difíciles con intercesores listos para orar y acompañar.') }}
                    </p>

                    <!-- Feature checkmarks -->
                    <div class="space-y-4 pt-2">
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">{{ __('Resguardo absoluto de tu privacidad') }}</h4>
                                <p class="text-slate-500 text-xs mt-0.5">{{ __('Comparte solo lo que desees; jamás compartiremos tus datos con terceros.') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">{{ __('Atención y oración personalizada') }}</h4>
                                <p class="text-slate-500 text-xs mt-0.5">{{ __('Cada petición es asignada cuidadosamente a un intercesor del equipo.') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">{{ __('Diálogo continuo en un entorno seguro') }}</h4>
                                <p class="text-slate-500 text-xs mt-0.5">{{ __('Puedes responder a los mensajes de oración a través de un chat encriptado.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Public Prayer Wall Dedicated Slide Section (Slide con Muro e Imagen Alusiva) -->
    <section id="muro" class="py-20 lg:py-28 bg-slate-950 text-white relative overflow-hidden"
             x-data="{
                active: 0,
                requests: @js($publicRequests->count() > 0 ? $publicRequests->map(fn($r) => [
                    'requester_name' => $r->requester_name,
                    'country_name' => $r->country_name,
                    'content' => $r->translated_content,
                    'prayed_count' => $r->prayed_count,
                    'time_ago' => $r->created_at->diffForHumans(),
                    'id' => $r->id,
                    'public_token' => $r->public_token,
                ])->values()->toArray() : [
                    [
                        'requester_name' => 'María G.',
                        'country_name' => 'Santiago',
                        'content' => __('Pido oración por la pronta recuperación de mi madre tras su operación de cadera. Confiamos en la paz y sanidad de Dios.'),
                        'prayed_count' => 14,
                        'time_ago' => __('Hace 1 hora'),
                        'id' => 1
                    ],
                    [
                        'requester_name' => 'Carlos R.',
                        'country_name' => 'Buenos Aires',
                        'content' => __('Oren por la restauración y unidad de mi familia en este tiempo de decisiones laborales y cambio de ciudad.'),
                        'prayed_count' => 28,
                        'time_ago' => __('Hace 3 horas'),
                        'id' => 2
                    ],
                    [
                        'requester_name' => null,
                        'country_name' => 'Bogotá',
                        'content' => __('Agradezco a todos los que oren por paz mental y fortaleza espiritual para superar la ansiedad que he sentido estos días.'),
                        'prayed_count' => 19,
                        'time_ago' => __('Hace 5 horas'),
                        'id' => 3
                    ],
                    [
                        'requester_name' => 'Elena T.',
                        'country_name' => 'Lima',
                        'content' => __('Pido oración por dirección y puertas abiertas en una nueva oportunidad de empleo para sostener a mis hijos.'),
                        'prayed_count' => 34,
                        'time_ago' => __('Hace 8 horas'),
                        'id' => 4
                    ]
                ]),
                next() {
                    this.active = (this.active + 1) % this.requests.length;
                },
                prev() {
                    this.active = (this.active - 1 + this.requests.length) % this.requests.length;
                }
             }">

        <!-- Background subtle amber glow -->
        <div class="absolute top-1/2 left-1/4 -translate-y-1/2 w-[600px] h-[600px] bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
                
                <!-- Left Column: Evocative Thematic Image (5 cols) -->
                <div class="lg:col-span-5 relative">
                    <div class="relative z-10 rounded-2xl overflow-hidden shadow-2xl border-4 border-slate-800 bg-slate-900">
                        <img src="https://images.unsplash.com/photo-1544427920-c49ccfb85579?auto=format&fit=crop&q=80&w=1000" 
                             alt="{{ __('Muro de Oración Comunitaria') }}" 
                             class="w-full h-[400px] sm:h-[460px] object-cover hover:scale-105 transition-transform duration-700">
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/30 to-transparent"></div>
                        
                        <!-- Text badge on image -->
                        <div class="absolute bottom-6 left-6 right-6 z-20">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500 text-slate-950 text-[11px] font-bold uppercase tracking-wider mb-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-950 animate-pulse"></span>
                                {{ __('Intercesión en Vivo') }}
                            </span>
                            <h3 class="text-lg font-bold text-white leading-snug">
                                {{ __('Nadie orando solo, todos unidos en fe') }}
                            </h3>
                        </div>
                    </div>

                    <!-- Decorative offset border -->
                    <div class="absolute -bottom-4 -left-4 w-full h-full border-2 border-amber-500/40 rounded-2xl -z-0 hidden sm:block"></div>
                </div>

                <!-- Right Column: Dedicated Prayer Wall Slide (7 cols) -->
                <div class="lg:col-span-7 flex flex-col justify-between space-y-6">
                    
                    <!-- Section Header -->
                    <div>
                        <div class="inline-flex items-center gap-2 mb-3">
                            <span class="h-px w-8 bg-amber-400"></span>
                            <span class="text-amber-400 font-bold tracking-[0.2em] uppercase text-xs">{{ __('Muro de Oración') }}</span>
                        </div>
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white tracking-tight">
                            {{ __('Peticiones de Nuestra Comunidad') }}
                        </h2>
                        <p class="text-stone-400 text-sm sm:text-base mt-2 font-light">
                            {{ __('Desliza para leer las necesidades actuales que han sido compartidas públicamente y levanta una oración por ellas.') }}
                        </p>
                    </div>

                    <!-- Slide Box -->
                    <div class="relative bg-slate-900/90 rounded-2xl p-7 sm:p-9 border border-white/10 shadow-2xl min-h-[260px] flex flex-col justify-between">
                        
                        <!-- Top Info -->
                        <div class="flex items-center justify-between gap-3 border-b border-white/10 pb-4 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-amber-500/20 border border-amber-400/40 text-amber-400 flex items-center justify-center font-bold text-sm">
                                    <span x-text="(requests[active].requester_name || 'A').charAt(0)"></span>
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-white leading-tight" 
                                        x-text="requests[active].requester_name || '{{ __('Petición Anónima') }}'">
                                    </h4>
                                    <span class="text-xs text-stone-400 font-light" 
                                          x-text="(requests[active].country_name || '{{ __('Comunidad') }}') + ' • ' + (requests[active].created_at ? 'Reciente' : requests[active].time_ago)">
                                    </span>
                                </div>
                            </div>

                            <span class="px-3 py-1 rounded-full bg-amber-400/10 border border-amber-400/20 text-amber-400 text-xs font-bold uppercase tracking-wider">
                                {{ __('Pública') }}
                            </span>
                        </div>

                        <!-- Prayer Text -->
                        <div class="my-3">
                            <p class="text-stone-200 text-base sm:text-lg font-serif italic leading-relaxed" 
                               x-text="'“' + requests[active].content + '”'">
                            </p>
                        </div>

                        <!-- Card Footer -->
                        <div class="pt-5 border-t border-white/10 flex flex-wrap items-center justify-between gap-4 mt-auto">
                            <div class="flex items-center gap-2 text-xs sm:text-sm text-amber-400/90 font-medium">
                                <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"></path>
                                </svg>
                                <span>
                                    <strong class="font-bold" x-text="requests[active].prayed_count || 0"></strong> 
                                    {{ __('personas han orado') }}
                                </span>
                            </div>

                            <a :href="'/muro/orar/' + (requests[active].public_token || requests[active].id)" 
                               wire:navigate 
                               class="px-5 py-2.5 rounded bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-wider transition-all shadow hover:shadow-lg inline-flex items-center gap-1.5">
                                <span>{{ __('Orar por esta petición') }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Slide Controls & Action Links -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
                        
                        <!-- Navigation Arrows & Dots -->
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2">
                                <button @click="prev()" 
                                        class="w-10 h-10 rounded-full border border-white/20 bg-slate-900 hover:bg-amber-500 text-white hover:text-slate-950 flex items-center justify-center transition-all shadow"
                                        aria-label="{{ __('Anterior') }}">
                                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                </button>
                                <button @click="next()" 
                                        class="w-10 h-10 rounded-full border border-white/20 bg-slate-900 hover:bg-amber-500 text-white hover:text-slate-950 flex items-center justify-center transition-all shadow"
                                        aria-label="{{ __('Siguiente') }}">
                                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            </div>

                            <!-- Dots Indicator -->
                            <div class="flex items-center gap-1.5">
                                <template x-for="(req, idx) in requests" :key="idx">
                                    <button @click="active = idx" 
                                            class="h-1.5 rounded-full transition-all duration-300"
                                            :class="active === idx ? 'w-6 bg-amber-400' : 'w-1.5 bg-white/30 hover:bg-white/60'">
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Link to full wall -->
                        <a href="{{ route('prayer.muro') }}" wire:navigate 
                           class="text-xs font-bold uppercase tracking-wider text-amber-400 hover:text-amber-300 transition-colors inline-flex items-center gap-1">
                            <span>{{ __('Ver Muro Completo') }}</span>
                            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>

                    </div>

                </div>

            </div>

        </div>
    </section>

    <!-- Main Action Cards (NewLife Services / Ministry Cards Style) -->
    <section id="como-funciona" class="py-20 lg:py-28 bg-stone-100/80 border-t border-stone-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Section Title -->
            <div class="text-center max-w-3xl mx-auto mb-16">
                <div class="inline-flex items-center justify-center gap-2 mb-3">
                    <span class="h-px w-8 bg-amber-500"></span>
                    <span class="text-amber-600 font-bold tracking-[0.2em] uppercase text-xs">{{ __('Participación') }}</span>
                    <span class="h-px w-8 bg-amber-500"></span>
                </div>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-slate-900 tracking-tight">
                    {{ __('¿Cómo deseas participar hoy?') }}
                </h2>
                <p class="text-slate-600 text-base sm:text-lg mt-4 font-light">
                    {{ __('Tanto si necesitas que oren por ti, como si sientes el llamado a sostener a otros en oración, este portal es para ti.') }}
                </p>
            </div>

            <!-- The Two Main Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-12 max-w-5xl mx-auto">
                
                <!-- Card 1: Pedir Oración -->
                <div class="group bg-white rounded-2xl p-8 sm:p-12 shadow-xl hover:shadow-2xl transition-all duration-300 border-t-4 border-amber-500 flex flex-col justify-between hover:-translate-y-1.5 text-start">
                    <div>
                        <!-- Icon Circle -->
                        <div class="w-16 h-16 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center mb-8 group-hover:bg-amber-500 group-hover:text-slate-950 transition-all duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                        </div>

                        <span class="text-xs uppercase tracking-widest text-amber-600 font-bold">{{ __('Confidencial & Libre') }}</span>
                        <h3 class="text-2xl sm:text-3xl font-bold text-slate-900 mt-1 mb-4">
                            {{ __('Pedir Oración') }}
                        </h3>

                        <p class="text-slate-600 leading-relaxed text-base font-light mb-8">
                            {{ __('Comparte tu petición de forma anónima o con tus datos. Recibirás un enlace único y privado para hacerle seguimiento y conversar con los intercesores que estarán orando por ti.') }}
                        </p>
                    </div>

                    <a href="{{ route('prayer.create') }}" wire:navigate 
                       class="inline-flex items-center justify-center w-full px-6 py-4 rounded bg-slate-900 hover:bg-amber-500 text-white hover:text-slate-950 font-bold text-xs uppercase tracking-widest transition-all shadow-md group-hover:shadow-lg">
                        <span>{{ __('Enviar Petición de Oración') }}</span>
                        <svg class="w-4 h-4 ms-2 rtl:rotate-180 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>

                <!-- Card 2: Orar por Otros -->
                <div class="group bg-slate-950 text-white rounded-2xl p-8 sm:p-12 shadow-xl hover:shadow-2xl transition-all duration-300 border-t-4 border-amber-400 flex flex-col justify-between hover:-translate-y-1.5 relative overflow-hidden text-start">
                    
                    <!-- Background ambient glow -->
                    <div class="absolute -top-20 -right-20 rtl:-left-20 rtl:right-auto w-48 h-48 bg-amber-500/10 rounded-full blur-3xl group-hover:bg-amber-500/20 transition-all"></div>

                    <div class="relative z-10">
                        <!-- Icon Circle -->
                        <div class="w-16 h-16 rounded-2xl bg-white/10 text-amber-400 flex items-center justify-center mb-8 border border-white/10 group-hover:bg-amber-400 group-hover:text-slate-950 transition-all duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                            </svg>
                        </div>

                        <span class="text-xs uppercase tracking-widest text-amber-400 font-bold">{{ __('Ministerio de Apoyo') }}</span>
                        <h3 class="text-2xl sm:text-3xl font-bold text-white mt-1 mb-4">
                            {{ __('Orar por Otros') }}
                        </h3>

                        <p class="text-stone-300 leading-relaxed text-base font-light mb-8">
                            {{ __('Inscríbete como intercesor del portal. El equipo de administración te asignará peticiones para que acompañes a personas en necesidad a través de la oración y mensajes de fe.') }}
                        </p>
                    </div>

                    <a href="{{ route('register') }}" wire:navigate 
                       class="relative z-10 inline-flex items-center justify-center w-full px-6 py-4 rounded bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-widest transition-all shadow-md group-hover:shadow-amber-500/20">
                        <span>{{ __('Inscribirme como Intercesor') }}</span>
                        <svg class="w-4 h-4 ms-2 rtl:rotate-180 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>

            </div>
        </div>
    </section>

    <!-- Verse of the Day Section (NewLife Fullscreen Quote/Scripture Banner) -->
    <section id="versiculo" class="relative py-24 lg:py-32 bg-slate-950 text-white overflow-hidden flex items-center justify-center">
        <!-- Background Image with parallax feel -->
        <img src="https://images.unsplash.com/photo-1507692049790-de58290a4334?auto=format&fit=crop&q=80&w=1920" 
             alt="{{ __('Versículo del Día') }}" 
             class="absolute inset-0 w-full h-full object-cover opacity-25 scale-105">
        
        <!-- Amber / Warm dark Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/85 to-slate-950/90"></div>

        <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            
            <!-- Section Subtitle with Decorative Lines -->
            <div class="inline-flex items-center justify-center gap-3 mb-8">
                <div class="h-px w-12 bg-amber-400/60"></div>
                <span class="text-amber-400 font-bold tracking-[0.25em] uppercase text-xs">{{ __('Versículo del Día') }}</span>
                <div class="h-px w-12 bg-amber-400/60"></div>
            </div>

            <!-- Big Scripture Quote -->
            <blockquote class="font-serif italic text-2xl sm:text-3xl md:text-4xl lg:text-5xl text-stone-100 leading-snug sm:leading-tight mb-8 drop-shadow-md">
                {{ __('«No se angustien por nada, y en cualquier circunstancia, acudan a Dios orando y suplicando, y dándole gracias.»') }}
            </blockquote>

            <!-- Citation -->
            <div class="inline-block">
                <cite class="text-lg sm:text-xl text-amber-400 font-bold tracking-widest not-italic">
                    {{ __('— FILIPENSES 4:6') }}
                </cite>
            </div>
        </div>
    </section>

    <!-- Footer (NewLife Church Multi-Column Footer) -->
    <footer class="bg-slate-950 text-stone-400 pt-16 pb-12 border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12 text-start">
                
                <!-- Col 1: Brand & Message -->
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-amber-500 flex items-center justify-center text-slate-950 font-bold">
                            ✝
                        </div>
                        <span class="tracking-wider text-lg font-bold text-white uppercase">
                            {{ __('Portal de Oración') }}
                        </span>
                    </div>
                    <p class="text-sm text-stone-400 max-w-md leading-relaxed font-light">
                        {{ __('Una plataforma dedicada a tender una mano en momentos de prueba, facilitando el encuentro entre la necesidad y la intercesión sincera.') }}
                    </p>
                    <p class="text-xs text-amber-400/80 font-medium">
                        {{ __('«Acerquémonos, pues, confiadamente al trono de la gracia para alcanzar misericordia.»') }}
                    </p>
                </div>

                <!-- Col 2: Accesos Rápidos -->
                <div>
                    <h4 class="text-white font-bold text-sm tracking-wider uppercase mb-4">{{ __('Navegación') }}</h4>
                    <ul class="space-y-2.5 text-xs uppercase tracking-wider font-medium">
                        <li><a href="/" class="hover:text-amber-400 transition-colors">{{ __('Inicio') }}</a></li>
                        <li><a href="{{ route('prayer.create') }}" wire:navigate class="hover:text-amber-400 transition-colors">{{ __('Pedir Oración') }}</a></li>
                        <li><a href="{{ route('register') }}" wire:navigate class="hover:text-amber-400 transition-colors">{{ __('Ser Intercesor') }}</a></li>
                        <li><a href="{{ route('login') }}" wire:navigate class="hover:text-amber-400 transition-colors">{{ __('Ingreso al Panel') }}</a></li>
                    </ul>
                </div>

                <!-- Col 3: Compromiso de Privacidad -->
                <div>
                    <h4 class="text-white font-bold text-sm tracking-wider uppercase mb-4">{{ __('Seguridad') }}</h4>
                    <p class="text-xs text-stone-400 leading-relaxed mb-3">
                        {{ __('Tu información no se comparte públicamente. Las peticiones son tratadas con el más alto rigor de confidencialidad y respeto.') }}
                    </p>
                    <span class="inline-flex items-center gap-1.5 text-xs text-green-400 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        {{ __('Plataforma Encriptada') }}
                    </span>
                </div>

            </div>

            <!-- Bottom Copyright -->
            <div class="pt-8 border-t border-white/10 text-center sm:flex sm:justify-between sm:items-center text-xs text-stone-500">
                <p>&copy; {{ date('Y') }} {{ __('Portal de Oración') }}. {{ __('Todos los derechos reservados.') }}</p>
                <p class="mt-2 sm:mt-0">{{ __('Diseñado con dedicación y fe.') }}</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
