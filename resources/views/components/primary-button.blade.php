<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-5 py-3 bg-ink border border-transparent rounded-sm font-semibold text-xs text-white uppercase tracking-widest hover:bg-ink/85 focus:bg-ink/85 active:bg-ink focus:outline-none focus:ring-2 focus:ring-ink focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
