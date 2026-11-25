<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Bandeja de notificaciones del usuario (campana del menú).
 * Cada usuario solo ve SUS notificaciones: $request->user()->notifications.
 */
class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return view('notifications.index', [
            'notifications' => $request->user()->notifications()->paginate(15),
        ]);
    }

    // Marca una como leída y lleva a la pantalla relacionada
    public function read(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect($notification->data['url'] ?? route('notifications.index'));
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Todas las notificaciones fueron marcadas como leídas.');
    }
}
