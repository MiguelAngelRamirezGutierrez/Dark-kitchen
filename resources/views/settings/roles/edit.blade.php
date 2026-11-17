<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Rol: {{ $role->name }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('settings.roles.update', $role) }}" class="bg-white shadow-sm rounded-2xl p-6 border border-slate-200/80 space-y-5">
                @csrf
                @method('PUT')
                @include('settings.roles.partials.form')
                <div class="flex items-center gap-4 pt-4 border-t border-slate-100">
                    @unless ($isSuperRole)
                        <x-primary-button>Actualizar Rol</x-primary-button>
                    @endunless
                    <a href="{{ route('settings.roles.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 underline">Volver</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
