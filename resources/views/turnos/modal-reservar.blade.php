<!-- Modal Reservar -->
<div class="modal fade" id="modalReservar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-0">
            <div class="modal-header bg-primary text-white rounded-0">
                <h5 class="modal-title">Reservar Turno</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="{{ route('turnos.reservar') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="hora" id="modalHora">
                    <input type="hidden" name="fecha" value="{{ $fecha }}">

                    <!-- Cliente existente / nuevo -->
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label mb-1">Cliente existente</label>
                            <div class="input-group input-group-sm">
                                <select name="cliente_id" class="form-select rounded-0">
                                    <option value="">-- Seleccionar --</option>
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-primary rounded-0"
                                        data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">
                                    + Nuevo
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Nota -->
                    <div class="mb-2">
                        <textarea name="nota" class="form-control rounded-0" placeholder="Nota"></textarea>
                    </div>
                </div>
                <div class="modal-footer p-0">
                    <button type="submit" class="btn btn-success rounded-0 w-100">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Nuevo Cliente -->
<div class="modal fade" id="modalNuevoCliente" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-0">
            <div class="modal-header bg-secondary text-white rounded-0">
                <h5 class="modal-title">Crear Nuevo Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formNuevoCliente" method="post" action="{{ route('clientes.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-2">
                        <input type="text" name="nombre" class="form-control rounded-0" placeholder="Nombre" required>
                    </div>
                    <div class="mb-2">
                        <input type="text" name="telefono" class="form-control rounded-0" placeholder="Teléfono">
                    </div>
                </div>
                <div class="modal-footer p-0">
                    <button type="submit" class="btn btn-success rounded-0 w-100">Crear Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Pasar la hora seleccionada al modal de reserva
    var modal = document.getElementById('modalReservar');
    modal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var hora = button.getAttribute('data-hora');
        document.getElementById('modalHora').value = hora;
        modal.querySelector('.modal-title').innerText = 'Reservar turno ' + hora.substr(0,5);
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formNuevoCliente');
    const selectCliente = document.querySelector('select[name="cliente_id"]');

    if (!form) return;

    // Helper: mostrar errores individuales
    function mostrarErrores(errors) {
        // eliminar errores previos
        form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        for (const field in errors) {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');
                const div = document.createElement('div');
                div.className = 'invalid-feedback';
                div.style.display = 'block';
                div.innerText = errors[field].join(', ');
                input.parentNode.appendChild(div);
            }
        }
    }

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // limpiar UI previa
        form.querySelectorAll('.invalid-feedback').forEach(n => n.remove());
        form.querySelectorAll('.is-invalid').forEach(n => n.classList.remove('is-invalid'));

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new FormData(form)
            });

            const text = await res.text();
            let data;
            try { data = JSON.parse(text); } catch (err) { data = null; }

            if (!res.ok) {
                console.error('Respuesta no ok:', res.status, text);

                if (res.status === 422 && data && data.errors) {
                    // validación
                    mostrarErrores(data.errors);
                    return;
                }

                // Mensaje general
                const msg = (data && (data.message || data.error)) ? (data.message || data.error) : text;
                alert('Error al crear cliente: ' + (msg ? msg : 'respuesta inesperada. Revisar consola.'));
                return;
            }

            // éxito
            const cliente = (data && data.cliente) ? data.cliente : data;

            // Agregar al select y seleccionarlo
            const option = new Option(cliente.nombre, cliente.id, true, true);
            selectCliente.add(option);

            // Cerrar modal nuevo cliente
            const modalNuevo = bootstrap.Modal.getInstance(document.getElementById('modalNuevoCliente'));
            modalNuevo.hide();

            // Reabrir modal reservar (si existe)
            const modalReservarEl = document.getElementById('modalReservar');
            if (modalReservarEl) {
                const modalReservar = new bootstrap.Modal(modalReservarEl);
                modalReservar.show();
            }

            form.reset();

        } catch (err) {
            console.error('Error fetch cliente:', err);
            alert('Error de conexión al crear el cliente. Revisa la consola.');
        }
    });
});
</script>

