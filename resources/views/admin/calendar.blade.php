<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Calendar</title>
    <link rel="stylesheet" href="{{ asset('css/calendar.css') }}" />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>


</head>

<body>
    @include('components.header')

    <div class="container">
        <h3>Calendario de Reservas Confirmadas</h3>
        <div id="calendar"></div>
    </div>

    <div id="eventModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modalTitle"></h2>
            </br>
            <p><strong>Inicio:</strong> <span id="modalStart"></span></p>
            <p><strong>Fin:</strong> <span id="modalEnd"></span></p>
            <p><strong>Descripción:</strong> <span id="modalDescription"></span></p>
            <p><strong>Usuario:</strong> <span id="modalUser"></span></p>
            <p><strong>Propiedad:</strong> <span id="modalProperty"></span></p>
            <pre id="modalExtraProps" style="white-space: pre-wrap;"></pre>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: '/admin/calendar/reservations',
                eventClick: function(info) {
                    document.getElementById('modalTitle').textContent = info.event.title;

                    document.getElementById('modalStart').textContent = info.event.start.toLocaleString();
                    document.getElementById('modalEnd').textContent = info.event.end ? info.event.end.toLocaleString() : 'No especificado';
                    document.getElementById('modalDescription').textContent = info.event.extendedProps.description || 'Sin descripción';
                    document.getElementById('modalUser').textContent = info.event.extendedProps.user ? info.event.extendedProps.user : 'No especificado';
                    document.getElementById('modalProperty').textContent = info.event.extendedProps.property ? info.event.extendedProps.property : 'No especificado';

                    const modal = document.getElementById('eventModal');
                    modal.style.display = 'block';

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
        });
    </script>


    @include('components.footer')
    <script src="{{ asset('js/session-expiry.js') }}"></script>

</body>