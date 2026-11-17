<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'QuickFood ERP') }} - Rol Activo & Seguridad</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="h-full flex items-center justify-center p-4">

    <div class="max-w-md w-full space-y-6">
        <!-- Logo y Cabecera -->
        <div class="text-center">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-orange-600 to-amber-500 items-center justify-center text-white shadow-xl shadow-orange-500/30 mb-3">
                <i class="fa-solid fa-burger text-2xl"></i>
            </div>
            <h2 class="text-2xl font-black text-white tracking-tight">QuickFood ERP</h2>
            <p class="text-xs text-orange-400 font-semibold uppercase tracking-widest mt-0.5">Gestión de Roles & Perfil Activo</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-6">
            {{ $slot }}
        </div>
    </div>

</body>
</html>
