@extends('layouts.app')
@section('title', 'Agenda')
@section('titulo', 'Agenda')
@section('content')
<head>
<link rel="stylesheet" href="{{ asset('css/agenda.css') }}?v={{ time() }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Agregar SweetAlert2 desde CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- CSS de Select2 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<!-- JS de Select2 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>




</head>
<body class="bg-gray-50 font-sans leading-normal tracking-normal">

    <!-- Contenedor principal con animación -->
    <div class="calendar-container">

        <div class="calendar-box">
            <!-- Calendario -->
            <div id="calendar" class="calendar"></div>
        </div>
    </div>

 <!-- Modal para agregar evento con opciones avanzadas -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" role="document">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Nuevo Evento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <!-- Título -->
                <div class="mb-3">
                    <label for="eventTitle" class="form-label">Título</label>
                    <input type="text" id="eventTitle" class="form-control" placeholder="Ej: Congreso" required />
                </div>

                <!-- Ubicación -->
                <div class="mb-3">
                    <label for="eventLocation" class="form-label">Ubicación</label>
                    <input type="text" id="eventLocation" class="form-control" placeholder="Ej: CDMX" />
                </div>

                <!-- Todo el día -->
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="allDayEvent" onchange="toggleAllDay()">
                    <label class="form-check-label" for="allDayEvent">Todo el día</label>
                </div>

                <!-- Fechas y horas -->
                <div class="mb-3">
                    <label class="form-label">Empieza</label>
                    <input type="date" id="startDate" class="form-control mb-2" disabled />
                    <input type="time" id="startTime" class="form-control" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Termina</label>
                    <input type="date" id="endDate" class="form-control mb-2" disabled />
                    <input type="time" id="endTime" class="form-control" />
                </div>

                <!-- Repetición -->
                <div class="mb-3">
                    <label for="repeatEvent" class="form-label">Repetir</label>
                    <select id="repeatEvent" class="form-control">
                        <option value="never">Nunca</option>
                        <option value="daily">Cada día</option>
                        <option value="weekly">Cada semana</option>
                        <option value="biweekly">Cada 2 semanas</option>
                        <option value="monthly">Cada mes</option>
                        <option value="yearly">Cada año</option>
                    </select>
                </div>

                <!-- Termina repetición -->
                <div class="mb-3">
                    <label for="repeatEnd" class="form-label">Termina repetición</label>
                    <select id="repeatEnd" class="form-control">
                        <option value="never">Nunca</option>
                        <option value="onDate">En la fecha</option>
                    </select>
                    <input type="date" id="repeatEndDate" class="form-control mt-2 d-none" />
                </div>

                <!-- Invitados -->
                <div class="mb-3">
                    <label for="eventGuests" class="form-label">Invitados</label>
                    <select id="eventGuests" class="form-control" multiple>
                        <!-- Usuarios cargados vía JS -->
                    </select>
                </div>

                <!-- Alerta -->
                <div class="mb-3">
                    <label for="eventAlert" class="form-label">Alerta</label>
                    <select id="eventAlert" class="form-control">
                        <option value="none">Sin alerta</option>
                        <option value="5m">5 minutos antes</option>
                        <option value="15m">15 minutos antes</option>
                        <option value="30m">30 minutos antes</option>
                        <option value="1h">1 hora antes</option>
                        <option value="1d">1 día antes</option>
                    </select>
                </div>

                <!-- URL -->
                <div class="mb-3">
                    <label for="eventUrl" class="form-label">URL</label>
                    <input type="url" id="eventUrl" class="form-control" placeholder="Ej: https://reunion.com" />
                </div>

                <!-- Notas -->
                <div class="mb-3">
                    <label for="eventNotes" class="form-label">Notas</label>
                    <textarea id="eventNotes" class="form-control" rows="3" placeholder="Añadir detalles adicionales..."></textarea>
                </div>
            </div>

            <!-- Botones -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="saveEvent" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script>
function cargarUsuariosInvitados() {
    fetch('/eventos/usuarios')
        .then(res => {
            if (!res.ok) throw new Error('Respuesta inválida');
            return res.json();
        })
        .then(data => {
            const select = document.getElementById('eventGuests');
            select.innerHTML = ''; // Limpiar por si acaso
            data.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = user.name;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error al obtener usuarios:', error);
            alert('No se pudieron cargar los usuarios invitados');
        });
}

// Llama esto al abrir el modal
document.getElementById('eventModal').addEventListener('show.bs.modal', cargarUsuariosInvitados);


</script>




<script>
document.addEventListener('DOMContentLoaded', function () {
    // Cargar la lista de usuarios cuando la página esté lista
    fetch('/usuarios')
        .then(response => response.json())
        .then(users => {
            let guestsSelect = document.getElementById('eventGuests');

            if (!guestsSelect) {
                console.error("El elemento #eventGuests no se encontró.");
                return;
            }

            // Limpiar opciones previas
            guestsSelect.innerHTML = '';

            // Agregar opciones dinámicamente
            users.forEach(user => {
                let option = document.createElement('option');
                option.value = user.id;
                option.textContent = user.name;
                guestsSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error al obtener usuarios:', error));
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let eventModal = document.getElementById('eventModal');

    function closeModal() {
        if (eventModal) {
            eventModal.classList.remove('show');
            eventModal.style.display = 'none';
        } else {
            console.error("El modal no existe en el DOM.");
        }
    }

    function resetModalFields() {
        let inputs = eventModal.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            if (input.type === 'checkbox') {
                input.checked = false;
            } else {
                input.value = '';
            }
        });
    }

    // Botón de cerrar (X)
    let closeModalBtn = document.querySelector('#eventModal .btn-close');
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }

    // Botón de cancelar
    let cancelBtn = document.querySelector('#eventModal .btn-cancel');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', function () {
            resetModalFields();
            closeModal();
        });
    } else {
        console.error("No se encontró el botón btn-cancel en el DOM.");
    }
});


</script>

<script>
    function toggleAllDay() {
        let isAllDay = document.getElementById('allDayEvent').checked;
        let startDate = document.getElementById('startDate');
        let endDate = document.getElementById('endDate');
        let startTime = document.getElementById('startTime');
        let endTime = document.getElementById('endTime');
        
        let today = new Date().toISOString().split('T')[0];
        startDate.value = today;
        endDate.value = today;
        startDate.disabled = isAllDay;
        endDate.disabled = isAllDay;
        
        if (isAllDay) {
            startTime.value = '00:00';
            endTime.value = '23:59';
            startTime.disabled = true;
            endTime.disabled = true;
        } else {
            startTime.disabled = false;
            endTime.disabled = false;
        }
    }
</script>

    <script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const eventModal = document.getElementById('eventModal');

    if (!calendarEl) return console.error("Elemento #calendar no encontrado");

    const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'es',
        initialView: 'dayGridMonth',
        selectable: true,
        editable: true,
        events: '/eventos',

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'Hoy',
            month: 'Mes',
            week: 'Semana',
            day: 'Día'
        },

        dateClick: function (info) {
            resetModalFields();
            eventModal.classList.add('show');
            eventModal.style.display = 'block';

            document.getElementById('startDate').value = info.dateStr;
            document.getElementById('endDate').value = info.dateStr;

            document.getElementById('saveEvent').onclick = function () {
                guardarEvento(info.dateStr);
            };
        },

        eventClick: function (info) {
            info.jsEvent.preventDefault();

            fetch(`/evento/${info.event.id}`)
                .then(res => res.json())
                .then(data => {
                    let html = `
                        <strong>Título:</strong> ${data.title || '-'}<br>
                        <strong>Ubicación:</strong> ${data.location || '-'}<br>
                        <strong>Todo el día:</strong> ${data.all_day ? 'Sí' : 'No'}<br>
                        <strong>Inicio:</strong> ${data.start}<br>
                        <strong>Fin:</strong> ${data.end || '-'}<br>
                        <strong>Repetición:</strong> ${data.repeat || '-'}<br>
                        <strong>Fin de repetición:</strong> ${data.repeat_end || '-'}<br>
                        <strong>Invitados:</strong> ${(data.guests && data.guests.length) ? data.guests.join(', ') : '-'}<br>
                        <strong>Alerta:</strong> ${data.alert || '-'}<br>
                        <strong>Notas:</strong> ${data.notes || '-'}<br>
                        ${data.url ? `<strong>URL:</strong> <a href="${data.url}" target="_blank">${data.url}</a>` : ''}
                    `;

                    Swal.fire({
                        title: 'Detalles del Evento',
                        html: html,
                        icon: 'info',
                        confirmButtonText: 'Cerrar',
                        width: '600px'
                    });
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'No se pudo cargar el evento', 'error');
                });
        },

        eventDrop: actualizarEvento,
        eventResize: actualizarEvento
    });

    calendar.render();

    function guardarEvento(startDate) {
        const guests = Array.from(document.getElementById('eventGuests').selectedOptions).map(opt => opt.value);
        const eventData = {
            title: document.getElementById('eventTitle').value,
            location: document.getElementById('eventLocation').value,
            all_day: document.getElementById('allDayEvent').checked,
            start: `${document.getElementById('startDate').value} ${document.getElementById('startTime').value}`,
            end: `${document.getElementById('endDate').value} ${document.getElementById('endTime').value}`,
            repeat: document.getElementById('repeatEvent').value,
            repeat_end: document.getElementById('repeatEndDate').value || null,
            guests: guests,
            alert: document.getElementById('eventAlert').value,
            url: document.getElementById('eventUrl').value,
            notes: document.getElementById('eventNotes').value,
        };

        fetch('/eventos', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(eventData)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Evento guardado', '', 'success');
                calendar.refetchEvents();
                closeModal(eventModal);
            } else {
                Swal.fire('Error', 'No se pudo guardar el evento', 'error');
            }
        })
        .catch(err => console.error('Error al guardar evento:', err));
    }

    function actualizarEvento(info) {
        fetch(`/eventos/${info.event.id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                start: info.event.start.toISOString(),
                end: info.event.end ? info.event.end.toISOString() : null,
                title: info.event.title,
                location: info.event.extendedProps.location
            })
        })
        .then(res => res.json())
        .then(data => {
            console.log("Evento actualizado", data);
            calendar.refetchEvents();
        })
        .catch(err => {
            console.error("Error al actualizar:", err);
            Swal.fire('Error', 'No se pudo actualizar el evento', 'error');
        });
    }

    function closeModal(modal) {
        if (modal) {
            modal.classList.remove('show');
            modal.style.display = 'none';
        }
    }

    function resetModalFields() {
        const inputs = eventModal.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            if (input.type === 'checkbox') input.checked = false;
            else input.value = '';
        });
    }

    document.getElementById('closeModal')?.addEventListener('click', () => closeModal(eventModal));
    document.querySelector('#eventModal .btn-cancel')?.addEventListener('click', () => {
        resetModalFields();
        closeModal(eventModal);
    });
});

</script>


</body>



@endsection
