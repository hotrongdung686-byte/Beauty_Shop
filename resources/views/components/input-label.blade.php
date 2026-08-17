@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs uppercase tracking-widest text-ink/50']) }}>
    {{ $value ?? $slot }}
</label>
