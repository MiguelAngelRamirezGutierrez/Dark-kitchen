@extends('layouts.app')

@section('title', $showTrashed ? 'Proveedores (Papelera)' : 'Directorio de Proveedores')
@section('page_title', 'Gestión de Proveedores ' . ($showTrashed ? '(Papelera)' : ''))

@section('content')
<div class="space-y-6">

    <!-- Barra Superior de Acciones -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex items-center space-x-3">
            @can('crear-proveedores')
                <a href="{{ route('providers.create') }}" class="inline-flex items-center space-x-2 bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-sm transition">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                    <span>Nuevo Proveedor</span>
                </a>
            @endcan

            @can('eliminar-proveedores')
                @if ($showTrashed)
                    <a href="{{ route('providers.index') }}" class="inline-flex items-center space-x-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition">
                        <i class="fa-solid fa-boxes-packing"></i>
                        <span>Ver Proveedores Activos</span>
                    </a>
                @else
                    <a href="{{ route('providers.index', ['trashed' => 1]) }}" class="inline-flex items-center space-x-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold px-4 py-2.5 rounded-xl transition">
                        <i class="fa-solid fa-trash-can text-slate-500"></i>
                        <span>Papelera ({{ $trashedCount }})</span>
                    </a>
                @endif
            @endcan
        </div>

        <!-- Formulario de Búsqueda y Filtro -->
        <form method="GET" action="{{ route('providers.index') }}" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            @if ($showTrashed)
                <input type="hidden" name="trashed" value="1">
            @endif
            <div class="relative flex-1 sm:w-64">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Buscar por razón social, NIT o contacto..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 shadow-sm">
            </div>

            <select name="status" class="py-2 px-3 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-slate-700 shadow-sm">
                <option value="">Todos los estados</option>
                <option value="active" @selected(request('status') === 'active')>Activos</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactivos</option>
            </select>

            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold px-3.5 py-2.5 rounded-xl shadow-sm transition">
                Filtrar
            </button>
            @if(request()->anyFilled(['search', 'status', 'trashed']))
                <a href="{{ route('providers.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-medium px-2 py-2">
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    <!-- Tabla de Proveedores -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-100 text-xs uppercase font-bold text-slate-500 tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Documento</th>
                        <th class="py-3.5 px-4">Razón Social</th>
                        <th class="py-3.5 px-4">Contacto</th>
                        <th class="py-3.5 px-4">Teléfono</th>
                        <th class="py-3.5 px-4 text-center">Compras</th>
                        <th class="py-3.5 px-4 text-center">Estado</th>
                        <th class="py-3.5 px-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($providers as $provider)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-mono font-medium text-slate-900 text-xs">
                                <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded-md mr-1">{{ $provider->document_type }}</span>
                                {{ $provider->document_number }}
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-900">
                                {{ $provider->name }}
                                @if($provider->address)
                                    <span class="block text-xs font-normal text-slate-400"><i class="fa-solid fa-location-dot text-slate-300 mr-1"></i>{{ $provider->address }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $provider->contact_name ?? '—' }}
                                @if($provider->email)
                                    <span class="block text-xs text-slate-400 font-mono">{{ $provider->email }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 font-mono text-xs">
                                {{ $provider->phone ?? '—' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $provider->purchases_count }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if ($provider->active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-2 whitespace-nowrap">
                                @if ($provider->trashed())
                                    <form method="POST" action="{{ route('providers.restore', $provider) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-emerald-600 hover:text-emerald-800 text-xs font-semibold hover:underline">
                                            <i class="fa-solid fa-rotate-left mr-1"></i>Restaurar
                                        </button>
                                    </form>
                                @else
                                    @can('editar-proveedores')
                                        <a href="{{ route('providers.edit', $provider) }}" class="text-orange-600 hover:text-orange-800 text-xs font-semibold hover:underline">
                                            <i class="fa-solid fa-pen-to-square mr-1"></i>Editar
                                        </a>
                                    @endcan

                                    @can('eliminar-proveedores')
                                        <form method="POST" action="{{ route('providers.destroy', $provider) }}" class="inline"
                                              onsubmit="return confirm('¿Enviar el proveedor «{{ $provider->name }}» a la papelera?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-semibold hover:underline">
                                                <i class="fa-solid fa-trash-can mr-1"></i>Eliminar
                                            </button>
                                        </form>
                                    @endcan
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-box-open text-3xl mb-2 text-slate-300"></i>
                                <p>No se encontraron proveedores registrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($providers->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $providers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
