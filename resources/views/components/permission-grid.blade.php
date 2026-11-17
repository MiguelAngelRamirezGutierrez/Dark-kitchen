{{--
 Matriz Módulo × Acción con casillas de verificación.
 - $groups / $actions: vienen de App\Support\PermissionCatalog
 - $selected: nombres de permisos marcados
 - $inherited: ['ver-productos' => ['vendedor'], ...] permisos que el usuario ya recibe por sus roles
 - $disabled: solo lectura (ej.: rol administrador)
--}}
@props(['groups', 'actions', 'name' => 'permissions[]', 'selected' => [], 'inherited' => [], 'disabled' => false])
<div class="overflow-x-auto">
    <table class="min-w-full text-sm border border-slate-200 rounded-xl overflow-hidden">
        <thead class="bg-slate-50">
            <tr class="text-left">
                <th class="p-2.5 border-b border-slate-200 font-semibold text-slate-700">Módulo</th>
                @foreach ($actions as $action)
                    <th class="p-2.5 border-b border-slate-200 text-center font-semibold text-slate-700">
                        {{ \App\Support\PermissionCatalog::actionLabel($action) }}
                    </th>
                @endforeach
                @unless ($disabled)
                    <th class="p-2.5 border-b border-slate-200"></th>
                @endunless
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($groups as $module => $group)
                <tr class="hover:bg-slate-50/70 transition">
                    <td class="p-2.5 font-medium text-slate-800">{{ $group['label'] }}</td>
                    @foreach ($actions as $action)
                        <td class="p-2.5 text-center">
                            @if ($permission = $group['permissions'][$action] ?? null)
                                <label class="inline-flex flex-col items-center gap-1 cursor-pointer" title="{{ $permission->name }}">
                                    <input type="checkbox" name="{{ $name }}" value="{{ $permission->name }}"
                                        class="rounded border-slate-300 text-orange-600 focus:ring-orange-500"
                                        @checked(in_array($permission->name, $selected))
                                        @disabled($disabled)>
                                    @isset($inherited[$permission->name])
                                        <span class="text-[10px] leading-tight text-teal-700 font-semibold">
                                            por rol: {{ implode(', ', $inherited[$permission->name]) }}
                                        </span>
                                    @endisset
                                </label>
                            @else
                                <span class="text-slate-300">—</span>
                            @endif
                        </td>
                    @endforeach
                    @unless ($disabled)
                        <td class="p-2.5 text-right">
                            <button type="button" class="text-xs text-orange-600 hover:text-orange-800 underline font-semibold cursor-pointer"
                                onclick="const c = this.closest('tr').querySelectorAll('input[type=checkbox]'); const all = [...c].every(x => x.checked); c.forEach(x => x.checked = !all);">
                                Todo / nada
                            </button>
                        </td>
                    @endunless
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
