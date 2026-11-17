<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <label for="document_type" class="block text-sm font-semibold text-slate-700 mb-1">Tipo de Documento *</label>
        <select id="document_type" name="document_type" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm shadow-sm" required>
            @foreach (['NIT' => 'NIT / Empresa', 'CC' => 'Cédula de Ciudadanía (CC)', 'CE' => 'Cédula de Extranjería (CE)', 'PP' => 'Pasaporte (PP)'] as $value => $label)
                <option value="{{ $value }}" @selected(old('document_type', $provider->document_type) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('document_type')
            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="document_number" class="block text-sm font-semibold text-slate-700 mb-1">Número de Documento *</label>
        <input id="document_number" name="document_number" type="text"
               value="{{ old('document_number', $provider->document_number) }}"
               placeholder="Ej: 900111222"
               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm shadow-sm" required>
        @error('document_number')
            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-4">
    <label for="name" class="block text-sm font-semibold text-slate-700 mb-1">Razón Social o Nombre del Proveedor *</label>
    <input id="name" name="name" type="text"
           value="{{ old('name', $provider->name) }}"
           placeholder="Ej: Distribuidora de Carnes & Alimentos SAS"
           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm shadow-sm" required>
    @error('name')
        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mt-4">
    <label for="contact_name" class="block text-sm font-semibold text-slate-700 mb-1">Persona de Contacto / Asesor Comercial (Opcional)</label>
    <input id="contact_name" name="contact_name" type="text"
           value="{{ old('contact_name', $provider->contact_name) }}"
           placeholder="Ej: Laura Ríos"
           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm shadow-sm">
    @error('contact_name')
        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
    <div>
        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Correo Electrónico (Opcional)</label>
        <input id="email" name="email" type="email"
               value="{{ old('email', $provider->email) }}"
               placeholder="ventas@proveedor.com"
               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm shadow-sm">
        @error('email')
            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="phone" class="block text-sm font-semibold text-slate-700 mb-1">Teléfono / Celular (Opcional)</label>
        <input id="phone" name="phone" type="text"
               value="{{ old('phone', $provider->phone) }}"
               placeholder="Ej: 6012345678"
               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm shadow-sm">
        @error('phone')
            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mt-4">
    <label for="address" class="block text-sm font-semibold text-slate-700 mb-1">Dirección de Sede / Despacho (Opcional)</label>
    <input id="address" name="address" type="text"
           value="{{ old('address', $provider->address) }}"
           placeholder="Ej: Cartago, Calle 14 #4-20"
           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm shadow-sm">
    @error('address')
        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mt-4 pt-2">
    <label class="inline-flex items-center space-x-2 cursor-pointer">
        <input type="checkbox" name="active" value="1"
               class="w-4 h-4 rounded text-orange-600 border-slate-300 focus:ring-orange-500"
               @checked(old('active', $provider->active))>
        <span class="text-sm font-medium text-slate-700">Proveedor Activo para Órdenes de Compra</span>
    </label>
</div>
