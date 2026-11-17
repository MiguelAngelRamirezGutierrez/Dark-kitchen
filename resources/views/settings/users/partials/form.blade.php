@if ($errors->any())
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<h3 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2">1. Datos del usuario</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <x-input-label for="name" value="Nombre" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required />
    </div>
    <div>
        <x-input-label for="email" value="Correo" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
    </div>
    <div>
        <x-input-label for="password" :value="$user->exists ? 'Nueva contraseña (dejar vacío para no cambiarla)' : 'Contraseña'" />
        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
    </div>
    <div>
        <x-input-label for="password_confirmation" value="Confirmar contraseña" />
        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
    </div>
</div>

<label class="inline-flex items-center gap-2 cursor-pointer pt-2">
    <input type="checkbox" name="active" value="1" class="rounded border-slate-300 text-orange-600 focus:ring-orange-500" @checked(old('active', $user->active))>
    <span class="text-xs font-semibold text-slate-700">Usuario activo (puede iniciar sesión en el ERP)</span>
</label>

<h3 class="font-bold text-slate-800 text-sm pt-4 border-t border-slate-100">2. Roles Asignados</h3>
<p class="text-xs text-slate-500">
    Puede asignar varios roles. Si el usuario tiene más de uno, al iniciar sesión elegirá con cuál trabajar.
</p>
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
    @foreach ($roles as $role)
        <label class="flex items-start gap-2.5 rounded-xl border border-slate-200 p-3 hover:bg-orange-50/40 cursor-pointer transition">
            <input type="checkbox" name="roles[]" value="{{ $role->name }}" class="mt-0.5 rounded border-slate-300 text-orange-600 focus:ring-orange-500"
                @checked(in_array($role->name, $userRoles))>
            <span>
                <span class="block font-bold text-xs text-slate-900 capitalize">{{ $role->name }}</span>
                <span class="block text-[11px] text-slate-400">{{ $role->permissions->count() }} permisos</span>
            </span>
        </label>
    @endforeach
</div>

<h3 class="font-bold text-slate-800 text-sm pt-4 border-t border-slate-100">3. Permisos directos (adicionales a los roles)</h3>
<p class="text-xs text-slate-500">
    Úselos para excepciones puntuales (ej.: un vendedor que también puede ver reportes).
    Los permisos directos aplican con <strong>cualquier</strong> rol activo. La etiqueta «por rol» indica que el permiso ya lo recibe por uno de sus roles (no es necesario marcarlo).
</p>

<x-permission-grid :groups="$groups" :actions="$actions" :selected="$directPermissions" :inherited="$inherited" />
