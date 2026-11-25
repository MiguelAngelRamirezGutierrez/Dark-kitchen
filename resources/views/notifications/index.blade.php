@extends('layouts.app')

@section('title', 'Notificaciones')
@section('page_title', 'Bandeja de Notificaciones')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Alertas y Notificaciones</h2>
            <p class="text-xs text-slate-500">Historial de alertas automáticas del ERP (stock bajo, recepción de compras y reportes).</p>
        </div>

        @if (auth()->user()->unreadNotifications()->exists())
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-orange-600 hover:text-orange-700 bg-orange-50 hover:bg-orange-100 px-3.5 py-2 rounded-lg border border-orange-200 shadow-sm transition">
                    <i class="fa-solid fa-check-double"></i>
                    <span>Marcar todas como leídas</span>
                </button>
            </form>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden p-6">
        <ul class="divide-y divide-slate-100">
            @forelse ($notifications as $notification)
                <li class="py-4 flex items-start justify-between gap-4 {{ $notification->read_at ? 'opacity-60 bg-slate-50/50 -mx-6 px-6' : '' }} transition">
                    <div class="space-y-1">
                        <div class="flex items-center space-x-2">
                            @unless ($notification->read_at)
                                <span class="h-2 w-2 rounded-full bg-orange-500 inline-block animate-pulse"></span>
                            @endunless
                            <span class="font-bold text-sm text-slate-800">
                                {{ $notification->data['title'] ?? 'Notificación' }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $notification->data['message'] ?? '' }}</p>
                        <p class="text-[11px] text-slate-400 flex items-center space-x-1 pt-1">
                            <i class="fa-regular fa-clock text-[10px]"></i>
                            <span>{{ $notification->created_at->diffForHumans() }}</span>
                        </p>
                    </div>

                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="flex-shrink-0">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center space-x-1 text-xs font-semibold text-orange-600 hover:text-orange-800 bg-orange-50 hover:bg-orange-100 px-3 py-1.5 rounded-lg border border-orange-200 transition">
                            <span>Abrir</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </button>
                    </form>
                </li>
            @empty
                <li class="py-12 text-center text-slate-400 space-y-2">
                    <i class="fa-regular fa-bell-slash text-3xl text-slate-300"></i>
                    <p class="text-sm">No tiene notificaciones.</p>
                </li>
            @endforelse
        </ul>

        @if ($notifications->hasPages())
            <div class="pt-4 border-t border-slate-100 mt-4">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
