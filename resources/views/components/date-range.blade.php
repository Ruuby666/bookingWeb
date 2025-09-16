<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Date Range Picker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <!-- Estilos del DateRangePicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="{{ asset('css/date-range.css') }}">
</head>

<body>

    <div class="daterange-container">
        <div class="daterange-header">
            <h2>Select Range</h2>
            <i class="fa-solid fa-repeat" id="reset-btn"></i>
        </div>
        <input type="text" id="daterange" placeholder="Select a date range" />
        <i class="fa-solid fa-repeat" id="reset-btn-second"></i>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>


    <script>
        $(document).ready(function() {

            // Inicializar Date Range Picker
            $('#daterange').daterangepicker({
                locale: {
                    format: 'DD/MM/YYYY'
                },
                autoApply: true,
                linkedCalendars: true,
                autoUpdateInput: true,
                showCustomRangeLabel: true,
                showDropdowns: false,
                minDate: moment().add(1, 'days'),
                endDate: moment().add(1, 'days'),
                opens: 'center',
                drops: "auto",
            }, function(start, end) {
                fetchDataAndRenderProperties(start, end);
            });

            // Función principal que gestiona todo el flujo de datos
            async function fetchDataAndRenderProperties(startDate, endDate) {
                const start = moment(startDate).format('YYYY-MM-DD');
                const end = moment(endDate).format('YYYY-MM-DD');

                try {
                    const [reservationsRes, propertiesRes] = await Promise.all([
                        fetch('/api/reservations'),
                        fetch('/api/properties')
                    ]);

                    const reservations = await reservationsRes.json();
                    const properties = await propertiesRes.json();

                    const occupiedIds = getOccupiedPropertyIds(reservations, start, end);
                    const availableProps = properties.filter(p => !occupiedIds.includes(p.id));

                    renderProperties(availableProps, propertyWithImages);
                } catch (error) {
                    console.error("Error fetching data:", error);
                }
            }

            // Comprobar solapamiento entre fechas
            function getOccupiedPropertyIds(reservations, newStart, newEnd) {
                const start = moment(newStart);
                const end = moment(newEnd);
                let occupied = [];

                reservations.forEach(res => {
                    if (res.status !== 'confirmed') return;

                    const checkIn = moment(res.check_in);
                    const checkOut = moment(res.check_out);

                    const isOverlap = start.isBefore(checkOut) && end.isAfter(checkIn);
                    if (isOverlap) {
                        occupied.push(res.property_id);
                    }
                });

                return occupied;
            }

            //  Mostrar propiedades disponibles
            function renderProperties(properties, images) {
                const container = $('#available-properties');
                container.empty();

                if (properties.length === 0) {
                    container.append('<p>No properties available for the selected dates.</p>');
                    return;
                }

                properties.forEach(prop => {
                    const img = images[prop.id] || 'default.jpg';
                    const card = `
                <a href="/property/${prop.id}">
                    <div class="cardcontainer">
                        <div class="photo">
                            <img src="/images/${prop.images_div}/${img}" alt="Image not found" style="height: 200px; width: 300px;">
                        </div>
                        <div class="content">
                            <p class="txt4">${prop.title}</p>
                            <p class="txt5">${prop.location}</p>
                            <p class="txt2">${prop.description}</p>
                        </div>
                    </div>
                </a>
            `;
                    container.append(card);
                });
            }

            async function showAllProperties() {
                try {
                    const propertiesRes = await fetch('/api/properties');
                    const properties = await propertiesRes.json();
                    renderProperties(properties, propertyWithImages);
                } catch (error) {
                    console.error("Error fetching all properties:", error);
                }
            }

            $('#reset-btn').on('click', function() {
                $('#daterange').data('daterangepicker').setStartDate(moment().add(1, 'days'));
                $('#daterange').data('daterangepicker').setEndDate(moment().add(1, 'days'));
                showAllProperties();
            });
        });
    </script>

</html>