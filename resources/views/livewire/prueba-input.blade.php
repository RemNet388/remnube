<div class="p-3 border rounded shadow-sm" style="max-width: 400px;">
    <label>Escribí algo:</label>
    <input type="text" wire:model="texto" class="form-control form-control-sm mb-2">

    <p class="mt-2">Valor actual: <strong>{{ $texto }}</strong></p>
</div>
