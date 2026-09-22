@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-200 focus:border-soul-gold focus:ring-soul-gold/30 rounded-xl shadow-sm text-soul-indigo placeholder-soul-accent/50 bg-white/50 transition-all duration-300 w-full px-4 py-3']) }}>
