<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Configuración — Seguridad</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm rounded-2xl p-5 text-sm text-slate-700 border border-slate-200/80">
                <div class="flex items-center space-x-2 mb-1 text-slate-900 font-bold">
                    <i class="fa-solid fa-shield-halved text-orange-500"></i>
                    <span>¿Cómo se calculan los permisos?</span>
                </div>
                <p class="text-xs text-slate-600">
                    Permisos efectivos = permisos del <strong>rol activo</strong> + permisos <strong>directos</strong> del usuario.
                    Un usuario con varios roles elige con cuál trabajar al iniciar sesión y puede alternarlo en cualquier momento.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                @can('ver-usuarios')
                    <a href="{{ route('settings.users.index') }}" class="bg-white shadow-sm rounded-2xl p-6 border border-slate-200/80 hover:border-orange-300 hover:shadow-md transition group">
                        <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center mb-3 group-hover:bg-orange-600 group-hover:text-white transition">
                            <i class="fa-solid fa-users text-lg"></i>
                        </div>
                        <h3 class="font-bold text-lg text-slate-800 group-hover:text-orange-600 transition">Usuarios</h3>
                        <p class="text-3xl font-black text-slate-900 mt-2">{{ $usersCount }}</p>
                        <p class="text-xs text-slate-500 mt-1">{{ $inactiveCount }} inactivos · {{ $multiRoleCount }} con varios roles</p>
                    </a>
                @endcan

                @can('ver-roles')
                    <a href="{{ route('settings.roles.index') }}" class="bg-white shadow-sm rounded-2xl p-6 border border-slate-200/80 hover:border-orange-300 hover:shadow-md transition group">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center mb-3 group-hover:bg-amber-600 group-hover:text-white transition">
                            <i class="fa-solid fa-id-badge text-lg"></i>
                        </div>
                        <h3 class="font-bold text-lg text-slate-800 group-hover:text-amber-600 transition">Roles</h3>
                        <p class="text-3xl font-black text-slate-900 mt-2">{{ $rolesCount }}</p>
                        <p class="text-xs text-slate-500 mt-1">Perfiles de trabajo y sus permisos.</p>
                    </a>
                @endcan

                @can('ver-permisos')
                    <a href="{{ route('settings.permissions.index') }}" class="bg-white shadow-sm rounded-2xl p-6 border border-slate-200/80 hover:border-orange-300 hover:shadow-md transition group">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-3 group-hover:bg-emerald-600 group-hover:text-white transition">
                            <i class="fa-solid fa-table-cells text-lg"></i>
                        </div>
                        <h3 class="font-bold text-lg text-slate-800 group-hover:text-emerald-600 transition">Matriz de permisos</h3>
                        <p class="text-3xl font-black text-slate-900 mt-2">{{ $permissionsCount }}</p>
                        <p class="text-xs text-slate-500 mt-1">Permisos por rol, módulo y usuario.</p>
                    </a>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
