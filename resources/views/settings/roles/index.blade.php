<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Configuración — Roles</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm rounded-2xl p-6 border border-slate-200/80 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <a href="{{ route('settings.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-slate-600 hover:text-slate-800 transition">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Volver a Configuración</span>
                    </a>
                    @can('crear-roles')
                        <a href="{{ route('settings.roles.create') }}" class="inline-flex items-center space-x-1.5 bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white text-xs font-semibold px-4 py-2 rounded-xl shadow-sm transition">
                            <i class="fa-solid fa-shield-plus"></i>
                            <span>Nuevo rol</span>
                        </a>
                    @endcan
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm divide-y divide-slate-100">
                        <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-semibold">
                            <tr>
                                <th class="py-3 px-4 text-left">Rol</th>
                                <th class="py-3 px-4 text-center">Usuarios</th>
                                <th class="py-3 px-4 text-center">Permisos</th>
                                <th class="py-3 px-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach ($roles as $role)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="py-3 px-4 font-bold text-slate-900 capitalize">
                                        {{ $role->name }}
                                        @if ($role->name === $superRole)
                                            <span class="ml-2 rounded-full bg-amber-100 border border-amber-200 px-2.5 py-0.5 text-xs font-semibold text-amber-800 normal-case">todos los permisos</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center font-mono font-bold">{{ $role->users_count }}</td>
                                    <td class="py-3 px-4 text-center font-mono font-bold">{{ $role->permissions_count }}</td>
                                    <td class="py-3 px-4 text-right space-x-2 whitespace-nowrap">
                                        @can('editar-roles')
                                            <a href="{{ route('settings.roles.edit', $role) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 underline">
                                                {{ $role->name === $superRole ? 'Ver' : 'Editar' }}
                                            </a>
                                        @endcan
                                        @can('eliminar-roles')
                                            @if ($role->name !== $superRole)
                                                <form method="POST" action="{{ route('settings.roles.destroy', $role) }}" class="inline"
                                                      onsubmit="return confirm('¿Eliminar el rol {{ $role->name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-800 underline cursor-pointer">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
