@extends('layouts.app')

@section('content')
<div class="container" style="font-family: 'Roboto', sans-serif; font-size: 0.85rem;">
    <h2 class="mb-3 text-uppercase fw-bold">📦 Retiros de Caja</h2>

    <div class="row align-items-start mb-3">
        {{-- 💰 Total actual --}}
        <div class="col-md-3">
            <div class="card text-bg-light shadow-sm rounded-0 h-100">
                <div class="card-body text-center">
                    <h6 class="card-title mb-1 text-uppercase small">💰 Total Actual</h6>
                    <p class="card-text fs-5 fw-bold mb-0">
                        ${{ number_format($totalDisponible, 2) }}
                    </p>
                </div>
            </div>
        </div>

        {{-- 🧾 Formulario de nuevo retiro --}}
        <div class="col-md-6">
            <form method="POST" action="{{ route('pagos.store') }}" class="row g-2 align-items-end">
                @csrf
                <div class="col-md-4">
                    <label class="form-label small mb-1">Fecha</label>
                    <input type="date" name="fecha"
                           class="form-control form-control-sm rounded-0"
                           value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small mb-1">Monto</label>
                    <input type="number" step="0.01" name="monto"
                           class="form-control form-control-sm rounded-0"
                           placeholder="Monto" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small mb-1">Motivo</label>
                    <input type="text" name="motivo"
                           class="form-control form-control-sm rounded-0"
                           placeholder="Motivo (opcional)">
                </div>
                <div class="col-12 mt-1">
                    <button class="btn btn-primary btn-sm w-100 rounded-0 fw-semibold">
                        Registrar Retiro
                    </button>
                </div>
            </form>
        </div>

        {{-- 📋 Botón para ver pagos realizados --}}
        <div class="col-md-3 d-flex justify-content-end">
            <a href="{{ route('pagos.index') }}" 
               class="btn btn-success btn-sm rounded-0 fw-semibold align-self-start mt-4">
                <i class="bi bi-cash-stack"></i> Ver Pagos Realizados
            </a>
        </div>
    </div>

    {{-- 📄 Listado de retiros --}}
    <div class="table-responsive">
        <table class="table table-sm table-bordered table-striped align-middle rounded-0">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th>
                    <th>Retiro</th>
                    <th>Caja</th>
                </tr>
            </thead>
            <tbody>
                @foreach($retiros as $r)
                    <tr>
                        <td>{{ $r->fecha }}</td>
                        <td>${{ number_format($r->monto, 2) }}</td>
                        <td>{{ $r->dejar_para_siguiente_caja ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
@push('submenu')
<a href="{{ route('pagos.index') }}" class="btn btn-primary btn-sm rounded-0 fw-semibold small">
    <i class="bi bi-cash-stack"></i> Ver Pagos Realizados
</a>
@endpush