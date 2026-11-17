<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Configuración — Matriz de permisos</h2>
    </x-slot>

    @php
        $canEdit = auth()->user()->can('editar-roles');
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm rounded-2xl p-6 border border-slate-200/80 space-y-4">
                <a href="{{ route('settings.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-slate-600 hover:text-slate-800 transition">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Volver a Configuración</span>
                </a>

                {{-- Filtros --}}
                <form method="GET" action="{{ route('settings.permissions.index') }}" class="flex flex-wrap items-end gap-3 pt-2">
                    <div>
                        <x-input-label for="module" value="Módulo (categoría)" />
                        <select id="module" name="module" class="mt-1 text-xs rounded-xl border border-slate-300 px-3 py-2">
                            <option value="">Todos los módulos</option>
                            @foreach ($modules as $key => $label)
                                <option value="{{ $key }}" @selected($module === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="user" value="Ver como usuario" />
                        <select id="user" name="user" class="mt-1 text-xs rounded-xl border border-slate-300 px-3 py-2">
                            <option value="">— Ninguno —</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" @selected($inspected?->id === $u->id)>
                                    {{ $u->name }} ({{ $u->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <x-primary-button>Aplicar</x-primary-button>
                    @if(request()->hasAny(['module', 'user']))
                        <a href="{{ route('settings.permissions.index') }}" class="text-xs text-slate-500 hover:text-slate-800 underline pb-2">Limpiar</a>
                    @endif
                </form>

                @if ($inspected)
                    <div class="rounded-xl bg-blue-50 border border-blue-200 p-4 text-xs text-blue-900">
                        <div class="font-bold text-sm mb-1">
                            <i class="fa-solid fa-user-shield text-blue-600 mr-1"></i>
                            {{ $inspected->name }} — roles: {{ $inspected->roles->pluck('name')->join(', ') ?: 'ninguno' }}.
                        </div>
                        <p class="text-blue-700">La última columna resalta sus permisos efectivos y de dónde provienen.</p>
                        @if ($inspected->roles->count() > 1)
                            <p class="text-[11px] text-blue-600 mt-1 italic">
                                * Como tiene varios roles, en cada sesión solo aplica el rol que elija al iniciar sesión + sus permisos directos.
                            </p>
                        @endif
                    </div>
                @endif

                {{-- Matriz: filas = permisos por módulo, columnas = roles --}}
                <form method="POST" action="{{ route('settings.permissions.matrix') }}">
                    @csrf
                    @method('PUT')

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm border border-slate-200 rounded-xl overflow-hidden">
                            <thead class="bg-slate-50 text-slate-700 text-xs uppercase font-semibold">
                                <tr class="text-left">
                                    <th class="p-2.5 border-b border-slate-200">Permiso</th>
                                    <th class="p-2.5 border-b border-slate-200">Acción</th>
                                    @foreach ($roles as $role)
                                        <th class="p-2.5 border-b border-slate-200 text-center capitalize">{{ $role->name }}</th>
                                    @endforeach
                                    <th class="p-2.5 border-b border-slate-200 text-center">Permiso directo</th>
                                    @if ($inspected)
                                        <th class="p-2.5 border-b border-blue-200 bg-blue-100/60 text-blue-900 text-center">{{ $inspected->name }}</th>
                                    @endif
                                    @can('eliminar-permisos')
                                        <th class="p-2.5 border-b border-slate-200"></th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($groups as $key => $group)
                                    <tr class="bg-slate-100/80">
                                        <td colspan="{{ 4 + $roles->count() + ($inspected ? 1 : 0) }}" class="p-2.5 font-bold text-xs uppercase text-slate-700 tracking-wider">
                                            {{ $group['label'] }}
                                        </td>
                                    </tr>
                                    @foreach ($group['permissions'] as $action => $permission)
                                        <tr class="border-b border-slate-100 hover:bg-slate-50/70 transition">
                                            <td class="p-2.5 font-mono text-xs text-slate-800">
                                                {{ $permission->name }}
                                                <input type="hidden" name="visible[]" value="{{ $permission->name }}">
                                            </td>
                                            <td class="p-2.5 text-xs text-slate-600 font-medium">
                                                {{ \App\Support\PermissionCatalog::actionLabel($action) }}
                                            </td>
                                            @foreach ($roles as $role)
                                                <td class="p-2.5 text-center">
                                                    <input type="checkbox" name="matrix[{{ $role->id }}][]" value="{{ $permission->name }}"
                                                        class="rounded border-slate-300 text-orange-600 focus:ring-orange-500"
                                                        @checked(in_array($permission->name, $rolePermissions[$role->id]))
                                                        @disabled(! $canEdit || $role->name === $superRole)>
                                                </td>
                                            @endforeach
                                            <td class="p-2.5 text-xs text-center text-slate-600">
                                                {{ $permission->users->pluck('name')->join(', ') ?: '—' }}
                                            </td>
                                            @if ($inspected)
                                                <td class="p-2.5 text-xs text-center bg-blue-50/70">
                                                    @if (isset($origins[$permission->name]))
                                                        <span class="text-emerald-700 font-bold">✓</span>
                                                        <span class="text-[11px] text-blue-800 font-medium">{{ implode(' · ', $origins[$permission->name]) }}</span>
                                                    @else
                                                        <span class="text-slate-300">✗</span>
                                                    @endif
                                                </td>
                                            @endif
                                            @can('eliminar-permisos')
                                                <td class="p-2.5 text-right">
                                                    <button type="submit" form="delete-{{ $permission->id }}"
                                                            class="text-xs text-rose-600 hover:text-rose-800 underline cursor-pointer">
                                                        Eliminar
                                                    </button>
                                                </td>
                                            @endcan
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($canEdit)
                        <div class="mt-4 flex flex-wrap items-center gap-4">
                            <x-primary-button>Guardar matriz</x-primary-button>
                            <span class="text-xs text-slate-500">
                                <i class="fa-solid fa-circle-info mr-1"></i> La columna «{{ $superRole }}» no se edita: siempre conserva todos los permisos.
                            </span>
                        </div>
                    @endif
                </form>

                {{-- Formularios de eliminación fuera de la tabla --}}
                @can('eliminar-permisos')
                    @foreach ($groups as $group)
                        @foreach ($group['permissions'] as $permission)
                            <form id="delete-{{ $permission->id }}" method="POST" action="{{ route('settings.permissions.destroy', $permission) }}"
                                  onsubmit="return confirm('¿Eliminar el permiso {{ $permission->name }}?')">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endforeach
                    @endforeach
                @endcan
            </div>

            {{-- Crear nuevo permiso --}}
            @can('crear-permisos')
                <div class="bg-white shadow-sm rounded-2xl p-6 border border-slate-200/80 space-y-3">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-key text-orange-500"></i>
                        <h3 class="font-bold text-slate-900 text-base">Nuevo permiso</h3>
                    </div>
                    <p class="text-xs text-slate-500">
                        Se crea con la convención <code class="bg-slate-100 px-1 py-0.5 rounded text-orange-600 font-mono">accion-modulo</code> y se asigna automáticamente al rol <strong>{{ $superRole }}</strong>.
                        Recuerde que un permiso nuevo solo tiene efecto cuando el código lo verifica (middleware <code class="bg-slate-100 px-1 py-0.5 rounded font-mono">permission:</code> o <code class="bg-slate-100 px-1 py-0.5 rounded font-mono">@can</code>).
                    </p>

                    <form method="POST" action="{{ route('settings.permissions.store') }}" class="flex flex-wrap items-end gap-3 pt-2">
                        @csrf
                        <div>
                            <x-input-label for="action" value="Acción" />
                            <x-text-input id="action" name="action" list="actions-list" class="mt-1 text-xs" :value="old('action')" placeholder="aprobar" required />
                            <datalist id="actions-list">
                                @foreach (config('permisos.actions') as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </datalist>
                        </div>

                        <div>
                            <x-input-label for="new-module" value="Módulo" />
                            <x-text-input id="new-module" name="module" list="modules-list" class="mt-1 text-xs" :value="old('module')" placeholder="compras" required />
                            <datalist id="modules-list">
                                @foreach ($modules as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </datalist>
                        </div>

                        <x-primary-button>Crear permiso</x-primary-button>
                    </form>

                    <x-input-error :messages="$errors->get('action')" />
                    <x-input-error :messages="$errors->get('module')" />
                    <x-input-error :messages="$errors->get('name')" />
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
