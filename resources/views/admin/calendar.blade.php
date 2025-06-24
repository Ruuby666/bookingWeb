<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Calendar</title>
    <link rel="stylesheet" href="{{ asset('css/calendar.css') }}" />
    <link href="{{ asset('css/toast.css') }}" rel="stylesheet">
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>


</head>

<body>
    @include('components.header')

    @if (session('success'))
    <x-toast :message="session('success')" type="success" />
    @endif

    @if (session('error'))
    <x-toast :message="session('error')" type="error" />
    @endif

    <div class="container">
        <h3>Calendario de Reservas Confirmadas</h3>
        <div class="calendar-toolbar">
            
            <div>
                <form id="propiedadForm" onsubmit="return false;">
                    <label for="propiedad">¿Cuál?</label>
                    <select name="propiedad" id="propiedad" class="form-control">
                        <option selected value="todos">Todos</option>
                        <option value="Marlin I Puerto del Carmen">Marlin C1</option>
                        <option value="Marlin II Puerto del Carmen">Marlin C2</option>
                        <option value="Casa Delfin Playa Blanca">Villa Delfín</option>
                        <option value="El Galeon">El Galeon</option>
                    </select>
                </form>
            </div>
            <a href="{{ route('admin.calendar.export-excel') }}" class="btn-export">Descargar Excel</a>
        </div>
        <div id="calendar"></div>
    </div>

    <div id="eventModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modalTitle"></h2>
            </br>
            <p><strong>Inicio:</strong> <span id="modalStart"></span></p>
            <p><strong>Fin:</strong> <span id="modalEnd"></span></p>
            <p><strong>Usuario:</strong> <span id="modalUser"></span></p>
            <p><strong>Email:</strong> <span id="modalEmail"></span></p>
            <p><strong>Phone Number:</strong> <span id="modalPhone"></span></p>
            <p><strong>Propiedad:</strong> <span id="modalProperty"></span></p>
            </br>
            <p><strong>Nota:</strong> <span id="modalNote"></span></p>
            </br>
            <button id="editButton">Editar hora</button>

            <!-- Inputs ocultos inicialmente -->
            <form id="editForm" style="display: none; margin-top: 10px;" method="POST" action="{{ route('admin.calendar.reservations.update-time') }}">
                @csrf
                <input type="hidden" name="event_id" id="modalEventId">

                <label for="modalStartInput"><strong>Hora entrada:</strong></label>
                <input type="time" id="modalStartInput" name="start_time" required><br><br>

                <label for="modalEndInput"><strong>Hora salida:</strong></label>
                <input type="time" id="modalEndInput" name="end_time" required><br><br>

                <button type="submit">Guardar cambios</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const propiedad = "{{ request('propiedad') ?? 'todos' }}";
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: function(fetchInfo, successCallback, failureCallback) {
                    const propiedad = document.getElementById('propiedad').value;
                    fetch(`/admin/calendar/reservations?propiedad=${encodeURIComponent(propiedad)}`)
                        .then(response => response.json())
                        .then(data => successCallback(data))
                        .catch(error => failureCallback(error));
                },
                eventClick: function(info) {
                    document.getElementById('modalTitle').textContent = info.event.title;
                    document.getElementById('modalStart').textContent = info.event.start.toLocaleString();
                    document.getElementById('modalEnd').textContent = info.event.end ? info.event.end.toLocaleString() : 'No especificado';
                    document.getElementById('modalNote').textContent = info.event.extendedProps.note || 'Sin descripción';
                    document.getElementById('modalUser').textContent = info.event.extendedProps.user.name ? info.event.extendedProps.user.name : 'No especificado';
                    document.getElementById('modalEmail').textContent = info.event.extendedProps.user.email ? info.event.extendedProps.user.email : 'No especificado';
                    document.getElementById('modalPhone').textContent = info.event.extendedProps.user.phone_number ? info.event.extendedProps.user.name : 'No especificado';
                    document.getElementById('modalProperty').textContent = info.event.extendedProps.property ? info.event.extendedProps.property : 'No especificado';

                    const modal = document.getElementById('eventModal');
                    modal.style.display = 'block';

                    document.getElementById('editForm').style.display = 'none';

                    // Mostrar inputs al pulsar "Editar hora"
                    document.getElementById('editButton').onclick = function() {
                        document.getElementById('editForm').style.display = 'block';
                        document.getElementById('modalEventId').value = info.event.id;
                        const start = info.event.start;
                        const end = info.event.end;
                        document.getElementById('modalStartInput').value = start ? start.toISOString().slice(11, 16) : '';
                        document.getElementById('modalEndInput').value = end ? end.toISOString().slice(11, 16) : '';
                    };

                    modal.querySelector('.close').onclick = function() {
                        modal.style.display = 'none';
                    };

                    window.onclick = function(event) {
                        if (event.target === modal) {
                            modal.style.display = 'none';
                        }
                    };
                }
            });

            calendar.render();

            // Escucha cambios en el select y actualiza los eventos del calendario
            document.getElementById('propiedad').addEventListener('change', function() {
                const propiedad = this.value;
                calendar.removeAllEvents();
                calendar.refetchEvents();
                calendar.setOption('events', `/admin/calendar/reservations?propiedad=${encodeURIComponent(propiedad)}`);
                calendar.refetchEvents();
            });
        });
    </script>


    @include('components.footer')
    <script src="{{ asset('js/session-expiry.js') }}"></script>

</body>