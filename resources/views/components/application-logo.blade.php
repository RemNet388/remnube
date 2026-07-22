@if(app()->environment('local') || app()->environment('production'))
    <picture>
        {{-- Versión para modo oscuro --}}
        <source srcset="{{ asset('images/logo-dark.png') }}" media="(prefers-color-scheme: dark)">
        {{-- Versión para modo claro --}}
        <img src="{{ asset('images/logo-light.png') }}" alt="RemNube" style="height: 80px; width: auto;">
    </picture>
@endif
