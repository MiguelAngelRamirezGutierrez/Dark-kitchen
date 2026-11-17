<x-guest-layout>
    <h2 class="text-lg font-semibold text-gray-800">Hola, {{ auth()->user()->name }}</h2>
    <p class="mt-1 text-sm text-gray-600">
        Su usuario tiene varios roles. Seleccione con cuál desea trabajar en esta sesión.
        Podrá cambiarlo en cualquier momento desde el menú de usuario.
    </p>

    <form method="POST" action="{{ route('active-role.update') }}" class="mt-4 space-y-3">
        @csrf
        @method('PUT')

        @foreach ($roles as $role)
            <label class="flex items-center gap-3 rounded-xl border border-slate-200 p-3 cursor-pointer hover:bg-orange-50/50 transition">
                <input type="radio" name="role" value="{{ $role->name }}" class="text-orange-600 focus:ring-orange-500"
                    @checked(old('role', $current) === $role->name)>
                <span class="flex-1">
                    <span class="block font-medium capitalize text-slate-900">{{ $role->name }}</span>
                    <span class="block text-xs text-slate-500">{{ $role->permissions_count }} permisos</span>
                </span>
            </label>
        @endforeach

        <x-input-error :messages="$errors->get('role')" />

        @if ($directCount > 0)
            <p class="text-xs text-slate-500 bg-slate-50 p-2.5 rounded-lg border border-slate-200">
                <i class="fa-solid fa-key mr-1 text-amber-500"></i> Además, tiene {{ $directCount }} permiso(s) directo(s) que aplican con cualquier rol.
            </p>
        @endif

        <div class="flex items-center justify-between pt-2">
            <x-primary-button>Continuar</x-primary-button>
        </div>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">
        @csrf
        <button type="submit" class="text-xs text-slate-500 hover:text-slate-700 underline cursor-pointer">Cerrar sesión</button>
    </form>
</x-guest-layout>
