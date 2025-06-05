<style>
    /* Estilo general del modal */
.modal-content {
    border-radius: 18px;
    border: 1px solid #e0e0e0;
    background-color: #ffffff;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
}

/* Encabezado del modal */
.modal-header {
    background-color: #e3f2fd;
    border-bottom: 1px solid #bbdefb;
    border-top-left-radius: 18px;
    border-top-right-radius: 18px;
    padding: 1rem 1.5rem;
}

.modal-title {
    font-weight: 600;
    color: #1565c0;
}

/* Botón de cerrar */
.btn-close {
    filter: brightness(0.7);
}

/* Cuerpo del modal */
.modal-body {
    padding: 1.5rem;
    background-color: #fefefe;
}

/* Inputs y selects */
.modal-body .form-control {
    border-radius: 12px;
    border: 1px solid #cfd8dc;
    padding: 0.6rem 0.9rem;
    background-color: #f9f9f9;
    font-size: 0.95rem;
    transition: border-color 0.2s ease;
}

.modal-body .form-control:focus {
    border-color: #90caf9;
    box-shadow: 0 0 0 0.15rem rgba(144, 202, 249, 0.4);
}

/* Etiquetas */
.modal-body .form-label {
    font-weight: 500;
    color: #37474f;
    margin-bottom: 0.4rem;
}

/* Pie del modal */
.modal-footer {
    background-color: #f4f6f7;
    border-top: 1px solid #e0e0e0;
    border-bottom-left-radius: 18px;
    border-bottom-right-radius: 18px;
    padding: 1rem 1.5rem;
}

/* Botones */
.modal-footer .btn-success {
    background-color: #81c784;
    border: none;
    transition: background-color 0.3s ease;
}

.modal-footer .btn-success:hover {
    background-color: #66bb6a;
}

.modal-footer .btn-secondary {
    background-color: #cfd8dc;
    color: #37474f;
    border: none;
    transition: background-color 0.3s ease;
}

.modal-footer .btn-secondary:hover {
    background-color: #b0bec5;
}

</style><!-- Modal para registrar pago -->
<div class="modal fade" id="pagoModal" tabindex="-1" aria-labelledby="pagoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="formPagoModal" method="POST">
        @csrf
        {{-- Quitamos el @method('PUT') porque usaremos POST --}}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pagoModalLabel">Registrar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Monto</label>
                    <input type="number" step="0.01" name="monto" id="modalMonto" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Fecha de Pago</label>
                    <input type="date" name="fecha_pago" id="modalFecha" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Método de Pago</label>
                    <select name="metodo_pago" id="modalMetodo" class="form-control" required>
                        <option value="">Seleccione un método</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Transferencia bancaria">Transferencia bancaria</option>
                        <option value="Tarjeta">Tarjeta</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Guardar Pago</button>
            </div>
        </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const pagoModal = document.getElementById('pagoModal');
    const form = document.getElementById('formPagoModal');

    pagoModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const ventaId = button.getAttribute('data-venta-id'); // Asegúrate de que tu botón tenga este atributo
        const fecha = button.getAttribute('data-fecha') || '';
        const monto = button.getAttribute('data-monto') || '';

        document.getElementById('modalFecha').value = fecha;
        document.getElementById('modalMonto').value = monto;
        document.getElementById('modalMetodo').value = "";

        form.action = `/ventas/${ventaId}/pagos`;
        form.method = 'POST';
    });
});
</script>
