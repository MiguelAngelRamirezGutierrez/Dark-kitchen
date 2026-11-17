<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Configuración — Usuarios</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm rounded-2xl p-6 border border-slate-200/80 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <a href="{{ route('settings.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-slate-600 hover:text-slate-800 transition">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Volver a Configuración</span>
                    </a>
                    @can('crear-usuarios')
                        <a href="{{ route('settings.users.create') }}" class="inline-flex items-center space-x-1.5 bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white text-xs font-semibold px-4 py-2 rounded-xl shadow-sm transition">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>Nuevo usuario</span>
                        </a>
                    @endcan
                </div>

                <form method="GET" action="{{ route('settings.users.index') }}" class="flex flex-wrap items-center gap-3">
                    <div class="relative flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o correo..."
                               class="w-full text-xs rounded-xl border border-slate-300 px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    <select name="role" class="text-xs rounded-xl border border-slate-300 px-3 py-2">
                        <option value="">Todos los roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" @selected(request('role') === $role)>{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="text-xs rounded-xl border border-slate-300 px-3 py-2">
                        <option value="">Todos los estados</option>
                        <option value="active" @selected(request('status') === 'active')>Activos</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactivos</option>
                    </select>
                    <x-primary-button>Filtrar</x-primary-button>
                    @if(request()->hasAny(['search', 'role', 'status']))
                        <a href="{{ route('settings.users.index') }}" class="text-xs text-slate-500 hover:text-slate-800 underline">Limpiar</a>
                    @endif
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm divide-y divide-slate-100">
                        <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-semibold">
                            <tr>
                                <th class="py-3 px-4 text-left">Nombre</th>
                                <th class="py-3 px-4 text-left">Correo</th>
                                <th class="py-3 px-4 text-left">Roles</th>
                                <th class="py-3 px-4 text-center">Permisos directos</th>
                                <th class="py-3 px-4 text-center">Estado</th>
                                <th class="py-3 px-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse ($users as $user)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="py-3 px-4 font-bold text-slate-900">{{ $user->name }}</td>
                                    <td class="py-3 px-4 font-mono text-xs text-slate-600">{{ $user->email }}</td>
                                    <td class="py-3 px-4">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse ($user->roles as $role)
                                                <span class="inline-block rounded-full bg-orange-50 border border-orange-200 px-2 py-0.5 text-xs font-semibold text-orange-800 capitalize">{{ $role->name }}</span>
                                            @empty
                                                <span class="text-xs text-slate-400">sin rol</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-center font-mono font-bold">{{ $user->permissions_count }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <x-status-badge :active="$user->active" />
                                    </td>
                                    <td class="py-3 px-4 text-right space-x-2 whitespace-nowrap">
                                        @can('editar-usuarios')
                                            <a href="{{ route('settings.users.edit', $user) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 underline">Editar</a>
                                            @unless ($user->is(auth()->user()))
                                                <form method="POST" action="{{ route('settings.users.toggle', $user) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-xs font-semibold {{ $user->active ? 'text-rose-600 hover:text-rose-800' : 'text-emerald-600 hover:text-emerald-800' }} underline cursor-pointer">
                                                        {{ $user->active ? 'Desactivar' : 'Activar' }}
                                                    </button>
                                                </form>
                                            @endunless
                                        @endcan
                                        <a href="{{ route('settings.permissions.index', ['user' => $user->id]) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-800 underline">Ver permisos</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-slate-400">No hay usuarios para mostrar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pt-2">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
