<div class="modal fade" id="modalEditarTurno" tabindex="-1" aria-labelledby="modalEditarTurnoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-0">
      <div class="modal-header py-2">
        <h6 class="modal-title" id="modalEditarTurnoLabel">Editar turno</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('turnos.update-fecha-hora') }}">
        @csrf
        <input type="hidden" name="turno_id" id="edit-turno-id">

        <div class="modal-body">
          <div class="mb-2">
            <label class="form-label mb-1 small">Fecha</label>
            <input type="date" name="fecha" id="edit-fecha" class="form-control form-control-sm rounded-0" required>
          </div>
          <div>
            <label class="form-label mb-1 small">Hora</label>
            <input type="time" name="hora" id="edit-hora" class="form-control form-control-sm rounded-0" required>
          </div>
        </div>

        <div class="modal-footer py-2">
          <button type="button" class="btn btn-secondary btn-sm rounded-0" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary btn-sm rounded-0">Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('modalEditarTurno');
  modal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    document.getElementById('edit-turno-id').value = button.getAttribute('data-turno-id');
    document.getElementById('edit-fecha').value = button.getAttribute('data-fecha');
    document.getElementById('edit-hora').value = button.getAttribute('data-hora').substring(0,5);
  });
});
</script>
