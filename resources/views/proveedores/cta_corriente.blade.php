@extends('layouts.app')

@section('content')
<div class="container my-4">

    <h2 class="mb-3" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 600;">
        Cuenta Corriente - {{ $proveedor->nombre }}
    </h2>

    <div class="card shadow-sm">
        <div class="card-body p-1">
            <table class="table table-sm table-bordered align-middle mb-0" style="font-size: 12px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                <thead class="table-light">
                    <tr>
                        <th style="width:100px;">Fecha</th>
                        <th>Concepto</th>
                        <th style="width:80px;">Debe</th>
                        <th style="width:80px;">Haber</th>
                        <th style="width:80px;">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movimientos->sortByDesc('fecha') as $mov)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($mov->fecha)->format('d/m/Y') }}</td>
                            <td>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modalMov{{ $mov->id }}" class="text-decoration-none">
                                    {{ $mov->concepto ?? '-' }}
                                </a>
                            </td>
                            <td class="text-end">${{ number_format($mov->debe, 2) }}</td>
                            <td class="text-end">${{ number_format($mov->haber, 2) }}</td>
                            <td class="text-end">${{ number_format($mov->saldo, 2) }}</td>
                        </tr>

                        <!-- Modal detalle movimiento -->
                        <div class="modal fade" id="modalMov{{ $mov->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detalle de la operación</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body" style="font-size: 13px;">
                                        <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($mov->fecha)->format('d/m/Y') }}</p>
                                        <p><strong>Concepto:</strong> {{ $mov->concepto ?? '-' }}</p>
                                        <p><strong>Monto:</strong>
    @if($mov->debe && $mov->debe > 0)
        ${{ number_format($mov->debe, 2) }}
    @elseif($mov->haber && $mov->haber > 0)
        ${{ number_format($mov->haber, 2) }}
    @endif
</p>

                                        
                                        <p><strong>Saldo:</strong> ${{ number_format($mov->saldo, 2) }}</p>
                                        {{-- Si hay más detalles relacionados, agregalos aquí --}}
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No hay movimientos</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-center mt-2">
                {{ $movimientos->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
