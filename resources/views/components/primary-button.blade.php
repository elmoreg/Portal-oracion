<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex justify-center items-center px-6 py-3 bg-soul-indigo border border-transparent rounded-full font-medium text-sm text-white tracking-wide hover:bg-soul-indigo/90 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-soul-gold focus:ring-offset-2 focus:ring-offset-white transition-all duration-300 w-full']) }}>
    {{ $slot }}
</button>
