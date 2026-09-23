<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex justify-center items-center px-6 py-3.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs uppercase tracking-widest rounded shadow-md hover:shadow-lg transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 w-full']) }}>
    {{ $slot }}
</button>
