@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-stone-300 bg-stone-50 focus:border-amber-500 focus:ring-amber-500 focus:bg-white rounded shadow-sm text-slate-900 placeholder-stone-400 transition-all duration-300 w-full px-4 py-3 text-sm']) }}>
