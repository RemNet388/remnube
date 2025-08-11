@extends('layouts.app')

@section('content')
<div class="container">

    {{-- Mensajes flash --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Caja de hoy --}}
    @if($cajaDiaria)
        <h1 class="mb-4">Caja del Día - {{ \Carbon\Carbon::parse($cajaDiaria->fecha)->format('d/m/Y') }}</h1>
        
        <div class="card mb-4">
            <div class="card-body">
                <p><strong>Saldo inicial:</strong> ${{ number_format($cajaDiaria->saldo_inicial, 2) }}</p>
                <p><strong>Saldo final:</strong> ${{ number_format($cajaDiaria->saldo_final, 2) }}</p>
                <p><strong>Observaciones:</strong> {{ $cajaDiaria->observaciones ?? 'Ninguna' }}</p>
                <a href="{{ route('caja.show', $cajaDiaria->id) }}" class="btn btn-primary mt-2">
                    Ver detalles de hoy
                </a>
            </div>
        </div>
    @else
        <h1 class="mb-4">No hay caja abierta para hoy</h1>
        <form action="{{ route('caja.abrir') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success">Abrir Caja</button>
        </form>
    @endif

    {{-- Histórico de cajas --}}
    <h2 class="mt-5">Histórico de Cajas</h2>
    @if($cajas->isEmpty())
        <p>No hay registros de cajas anteriores.</p>
    @else
        <table class="table table-sm table-bordered table-striped align-middle">
    <thead class="table-light">
                <tr>
                    <th>Fecha</th>
                    <th>Saldo Inicial</th>
                    <th>Saldo Final</th>
                    <th>Observaciones</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cajas as $caja)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($caja->fecha)->format('d/m/Y') }}</td>
                        <td>${{ number_format($caja->saldo_inicial, 2) }}</td>
                        <td>${{ number_format($caja->saldo_final, 2) }}</td>
                        <td>{{ $caja->observaciones ?? '-' }}</td>
                        <td>
                            <a href="{{ route('caja.show', $caja->id) }}" class="btn btn-sm btn-primary">
                                Ver detalles
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</div>
@endsection
