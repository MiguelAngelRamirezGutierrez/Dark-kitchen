@props(['active'])

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
    {{ $active ? 'Activo' : 'Inactivo' }}
</span>
