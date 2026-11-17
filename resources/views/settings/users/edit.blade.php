<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar usuario: {{ $user->name }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('settings.users.update', $user) }}" class="bg-white shadow-sm rounded-2xl p-6 border border-slate-200/80 space-y-5">
                @csrf
                @method('PUT')
                @include('settings.users.partials.form')
                <div class="flex items-center gap-4 pt-4 border-t border-slate-100">
                    <x-primary-button>Actualizar Usuario</x-primary-button>
                    <a href="{{ route('settings.users.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 underline">Cancelar</a>
                    <a href="{{ route('settings.permissions.index', ['user' => $user->id]) }}" class="text-xs font-semibold text-orange-600 hover:text-orange-800 underline">Ver permisos efectivos</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
