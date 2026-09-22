@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-soul-indigo/80 mb-2 pl-1']) }}>
    {{ $value ?? $slot }}
</label>
