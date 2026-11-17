<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest shadow-sm transition ease-in-out duration-150 cursor-pointer']) }}>
    {{ $slot }}
</button>
