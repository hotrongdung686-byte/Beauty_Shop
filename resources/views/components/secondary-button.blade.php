<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-5 py-3 bg-white border border-ink/20 rounded-sm font-semibold text-xs text-ink uppercase tracking-widest hover:bg-cream-100 focus:outline-none focus:ring-2 focus:ring-ink focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
