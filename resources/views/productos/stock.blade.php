<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body>
    <header>
        <h1>Cabecera</h1>
    </header>

    <main>
        {{ $slot }}  {{-- 👈 Aquí se inyecta stock.blade.php --}}
    </main>

    @livewireScriptConfig
</body>
</html>
