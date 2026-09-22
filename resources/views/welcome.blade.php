<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Portal de Oración') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased text-soul-indigo bg-white overflow-x-hidden selection:bg-soul-gold/30 selection:text-soul-indigo">
    @livewireScripts

    <!-- Animated Blob Background -->
    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-soul-accent/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-soul-gold/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob" style="animation-delay: 2s;"></div>
        <div class="absolute -bottom-32 left-1/2 w-96 h-96 bg-blue-300/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob" style="animation-delay: 4s;"></div>
        <div class="absolute inset-0 bg-white/40 backdrop-blur-[2px]"></div>
    </div>

    <div class="relative z-10 min-h-screen flex flex-col">
        <!-- Header: Modern, Commercial -->
        <header class="w-full bg-white/80 backdrop-blur-md border-b border-gray-100 shadow-sm fixed top-0 z-50">
            <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-soul-indigo to-soul-accent rounded-xl flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v19M5 10h14"></path></svg>
                    </div>
                    <span class="font-bold text-xl tracking-tight text-soul-indigo">{{ config('app.name', 'Portal de Oración') }}</span>
                </div>
                <nav class="hidden md:flex items-center space-x-8 text-sm font-semibold">
                    @auth
                        <a href="{{ route('dashboard') }}" wire:navigate class="text-soul-indigo hover:text-soul-accent transition-colors">Mi Panel</a>
                    @else
                        <a href="{{ route('login') }}" wire:navigate class="text-gray-500 hover:text-soul-indigo transition-colors">Iniciar sesión</a>
                        <a href="{{ route('register') }}" wire:navigate class="px-6 py-2.5 rounded-lg bg-soul-indigo text-white hover:bg-soul-indigo/90 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300">Crear cuenta</a>
                    @endauth
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="flex-1 mt-24">
            <div class="max-w-7xl mx-auto px-6 py-12 md:py-20 lg:py-24 grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left: Copy -->
                <div class="space-y-8 animate-fade-in-up">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-soul-gold/10 text-soul-gold font-bold text-sm tracking-wide">
                        <span class="relative flex h-3 w-3">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-soul-gold opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-3 w-3 bg-soul-gold"></span>
                        </span>
                        ACOMPAÑAMIENTO SEGURO Y CONFIDENCIAL
                    </div>
                    
                    <h1 class="text-5xl md:text-6xl lg:text-7xl font-extrabold text-soul-indigo leading-[1.1] tracking-tight">
                        No tienes por qué <br/>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-soul-indigo via-soul-accent to-blue-500">
                            llevar tus cargas a solas
                        </span>
                    </h1>
                    
                    <p class="text-lg md:text-xl text-gray-600 leading-relaxed max-w-xl">
                        Un espacio respaldado por una comunidad dispuesta a interceder por ti. Confidencialidad absoluta, tecnología segura y apoyo genuino.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <a href="{{ route('prayer.create') }}" wire:navigate class="flex items-center justify-center px-8 py-4 rounded-xl bg-soul-indigo text-white font-bold text-lg hover:bg-slate-800 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                            Pedir oración ahora
                            <svg class="w-5 h-5 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                        <a href="{{ route('register') }}" wire:navigate class="flex items-center justify-center px-8 py-4 rounded-xl bg-white text-soul-indigo font-bold text-lg border-2 border-gray-100 hover:border-soul-gold/50 hover:bg-gray-50 hover:shadow-lg transition-all duration-300">
                            Unirme a orar
                        </a>
                    </div>
                </div>

                <!-- Right: Image / Cards Animation -->
                <div class="relative lg:h-[600px] flex items-center justify-center animate-fade-in-up" style="animation-delay: 0.2s;">
                    <!-- Decorative elements -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-soul-accent/20 to-soul-gold/20 rounded-3xl transform rotate-3 scale-105"></div>
                    
                    <div x-data="{ current: 0 }" x-init="setInterval(() => { current = (current + 1) % 3 }, 4000)" 
                        class="relative z-10 w-full h-[400px] lg:h-full rounded-3xl shadow-2xl border-4 border-white overflow-hidden bg-gray-100">
                        
                        <img src="https://images.unsplash.com/photo-1529070538774-1843cb1611bb?auto=format&fit=crop&q=80&w=1000" 
                             class="absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 ease-in-out"
                             :class="current === 0 ? 'opacity-100' : 'opacity-0'"
                             alt="Red de apoyo y comunidad" />
                             
                        <img src="https://images.unsplash.com/photo-1573164713988-8665fc963095?auto=format&fit=crop&q=80&w=1000" 
                             class="absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 ease-in-out opacity-0"
                             :class="current === 1 ? 'opacity-100' : 'opacity-0'"
                             alt="Comunidad orando juntos" />
                             
                        <img src="https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&q=80&w=1000" 
                             class="absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 ease-in-out opacity-0"
                             :class="current === 2 ? 'opacity-100' : 'opacity-0'"
                             alt="Personas en oración" />
                    </div>

                    <!-- Floating Trust Card -->
                    <div class="absolute -bottom-8 -left-8 bg-white p-6 rounded-2xl shadow-xl z-20 animate-float border border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Privacidad Garantizada</p>
                            <p class="font-bold text-soul-indigo">100% Confidencial</p>
                        </div>
                    </div>

                    <!-- Floating Stat Card -->
                    <div class="absolute top-12 -right-8 bg-white p-6 rounded-2xl shadow-xl z-20 animate-float border border-gray-100 flex items-center gap-4" style="animation-delay: 2s;">
                        <div class="w-12 h-12 rounded-full bg-soul-gold/20 flex items-center justify-center text-soul-gold">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-soul-indigo">+500</p>
                            <p class="text-sm text-gray-500 font-medium">Intercesores Activos</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features / Actions Section -->
            <div class="bg-gray-50 py-24 border-t border-gray-200 mt-12 relative overflow-hidden">
                <div class="max-w-7xl mx-auto px-6 relative z-10">
                    <div class="text-center max-w-3xl mx-auto mb-16 animate-fade-in-up" style="animation-delay: 0.4s;">
                        <h2 class="text-3xl md:text-4xl font-extrabold text-soul-indigo mb-4 tracking-tight">¿Cómo podemos ayudarte hoy?</h2>
                        <p class="text-lg text-gray-600">Nuestra plataforma está diseñada para conectar corazones y crear una red de apoyo real, efectiva y compasiva.</p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-8 lg:gap-12 max-w-5xl mx-auto">
                        
                        <!-- Card 1 -->
                        <div class="group relative bg-white rounded-[2rem] p-10 shadow-xl hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 border border-gray-100 overflow-hidden animate-fade-in-up" style="animation-delay: 0.6s;">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            
                            <div class="relative z-10">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-soul-indigo to-blue-600 flex items-center justify-center mb-8 shadow-lg text-white transform group-hover:scale-110 transition-transform duration-500">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </div>
                                
                                <h3 class="text-3xl font-bold text-soul-indigo mb-4">Pedir oración</h3>
                                <p class="text-gray-600 leading-relaxed mb-8 text-lg">
                                    Comparte tu petición de forma anónima. Recibirás un enlace único y privado para hacerle seguimiento y conversar con quien ore por ti en un entorno seguro.
                                </p>
                                
                                <a href="{{ route('prayer.create') }}" wire:navigate class="inline-flex items-center justify-center w-full px-6 py-4 rounded-xl bg-soul-indigo/5 text-soul-indigo font-bold hover:bg-soul-indigo hover:text-white transition-colors duration-300">
                                    Enviar una petición
                                    <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                </a>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div class="group relative bg-soul-indigo rounded-[2rem] p-10 shadow-xl hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 overflow-hidden animate-fade-in-up" style="animation-delay: 0.8s;">
                            <!-- Glow effect inside card -->
                            <div class="absolute -top-24 -right-24 w-48 h-48 bg-soul-accent opacity-50 rounded-full mix-blend-screen filter blur-3xl group-hover:opacity-70 transition-opacity duration-500"></div>
                            
                            <div class="relative z-10">
                                <div class="w-16 h-16 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center mb-8 shadow-lg text-soul-gold transform group-hover:scale-110 transition-transform duration-500 border border-white/20">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                </div>
                                
                                <h3 class="text-3xl font-bold text-white mb-4">Orar por otros</h3>
                                <p class="text-blue-100 leading-relaxed mb-8 text-lg">
                                    Inscríbete como intercesor. El equipo del portal te asignará peticiones de oración para que acompañes a quienes más lo necesitan, creando un impacto real.
                                </p>
                                
                                <a href="{{ route('register') }}" wire:navigate class="inline-flex items-center justify-center w-full px-6 py-4 rounded-xl bg-soul-gold text-soul-indigo font-bold hover:bg-white hover:shadow-lg transition-colors duration-300">
                                    Inscribirme como intercesor
                                    <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </main>
        
        <!-- Verse of the Day Section -->
        <div class="relative py-24 overflow-hidden">
            <!-- Background image -->
            <img src="https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0?auto=format&fit=crop&q=80&w=1920" alt="" class="absolute inset-0 w-full h-full object-cover" />
            <!-- Warm overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-amber-600/85 via-orange-500/80 to-rose-500/85"></div>
            
            <div class="relative max-w-4xl mx-auto px-6 text-center z-10 animate-fade-in-up" style="animation-delay: 0.2s;">
                <div class="inline-flex items-center justify-center gap-3 mb-8">
                    <div class="h-[1px] w-12 bg-white/50"></div>
                    <span class="text-white font-bold tracking-widest uppercase text-sm drop-shadow-sm">Versículo del día</span>
                    <div class="h-[1px] w-12 bg-white/50"></div>
                </div>
                
                <blockquote class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-white leading-tight mb-8 tracking-tight drop-shadow-lg">
                    «No se angustien por nada, y en cualquier circunstancia, acudan a Dios orando y suplicando, y dándole gracias.»
                </blockquote>
                
                <cite class="text-xl text-white/90 font-semibold not-italic drop-shadow-sm">— Filipenses 4:6</cite>
            </div>
        </div>

        <footer class="bg-white py-12 border-t border-gray-100 mt-auto">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <div class="inline-flex items-center justify-center gap-2 mb-4">
                    <svg class="w-6 h-6 text-soul-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v19M5 10h14"></path></svg>
                    <span class="font-bold text-lg text-soul-indigo">{{ config('app.name', 'Portal de Oración') }}</span>
                </div>
                <p class="text-gray-500 font-medium">«Acerquémonos, pues, confiadamente al trono de la gracia»</p>
                <p class="text-sm text-gray-400 mt-4">&copy; {{ date('Y') }} Todos los derechos reservados.</p>
            </div>
        </footer>
    </div>
</body>
</html>
