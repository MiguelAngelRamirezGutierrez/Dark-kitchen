@if ($errors->any())
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="max-w-md">
    <x-input-label for="name" value="Nombre del rol" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
        :value="old('name', $role->name)" :readonly="$isSuperRole" required />
    <p class="mt-1 text-xs text-slate-500">Se guarda en minúsculas y con guiones (ej.: «Jefe de Bodega» → jefe-de-bodega).</p>
</div>

<h3 class="font-bold text-slate-800 text-sm pt-4 border-t border-slate-100">Permisos del rol</h3>
@if ($isSuperRole)
    <p class="text-xs text-amber-700 bg-amber-50 p-2.5 rounded-xl border border-amber-200 font-medium">
        <i class="fa-solid fa-crown mr-1"></i> El rol administrador siempre tiene todos los permisos; no se puede modificar.
    </p>
@endif

<x-permission-grid :groups="$groups" :actions="$actions"
    :selected="$isSuperRole ? collect($groups)->flatMap(fn ($g) => collect($g['permissions'])->pluck('name'))->all() : $selected"
    :disabled="$isSuperRole" />
