<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Date Range Picker</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="{{ asset('css/date-range.css') }}">
</head>

<body>
    @csrf
    <input type="text" id="daterange" name="daterange" placeholder="Select a date range" /><b> *</b>
    <p id="total-price">Minimum {{ $property->min_nights }} nights</p>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        $(document).ready(async function() {
            const idProperty = @json($id);
            const minNights = @json($property->min_nights);

            // Obtén todas las fechas ocupadas antes de inicializar el picker
            const reservedDates = await fetchReservedDates(idProperty);
            checkbetweenReservations(reservedDates, minNights);
            initializeDateRangePicker(reservedDates);

            async function fetchReservedDates(propertyId) {
                try {
                    const response = await fetch(`/property/${propertyId}/reservations`);
                    const reservations = await response.json();
                    console.log(reservations);
                    const dates = new Set();

                    reservations.forEach(reservation => {
                        if (reservation.property_id == propertyId) {
                            const range = generateAllDates(reservation.check_in, reservation
                                .check_out);
                            range.forEach(date => dates.add(date));
                        }
                    });

                    return dates;
                } catch (error) {
                    return new Set();
                }
            }

            function initializeDateRangePicker(reservedDates) {
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
                    drops: 'auto',
                    isInvalidDate: function(date) {
                        return reservedDates.has(date.format('YYYY-MM-DD'));
                    }
                }, function(start, end) {
                    fetchDataAndRenderProperties(start, end);
                });
            }

            async function fetchDataAndRenderProperties(startDate, endDate) {
                const checkIn = moment(startDate);
                const checkOut = moment(endDate);
                const formattedCheckIn = checkIn.format('YYYY-MM-DD');
                const formattedCheckOut = checkOut.format('YYYY-MM-DD');

                // Inserta las variables Blade de forma segura
                const idProperty = @json($id);
                const minNights = @json($property->min_nights);
                const fullDates = [];

                const totalNights = checkOut.diff(checkIn, 'days');

                if (totalNights < minNights) {
                    displayMessage('total-price', `The minimum stay is ${minNights} nights.`, '#e07a5f');
                    return;
                }

                const selectedDates = getDateRangeArray(checkIn, checkOut);
                const hasOverlap = await checkForOverlaps(idProperty, fullDates, selectedDates);

                if (hasOverlap) {
                    displayMessage('total-price', 'Selected dates overlap with an existing reservation.',
                        '#e07a5f');
                    return;
                }

                await updatePrice(formattedCheckIn, formattedCheckOut, idProperty);
            }

            // Creates an array with all dates between two moments
            function getDateRangeArray(start, end) {
                const dates = [];
                const current = moment(start);

                while (current.isBefore(end)) {
                    dates.push(current.format('YYYY-MM-DD'));
                    current.add(1, 'days');
                }

                return dates;
            }

            // Checks for overlapping dates with existing reservations
            async function checkForOverlaps(propertyId, fullDates, selectedDates) {
                try {
                    const response = await fetch('/api/reservations');
                    const reservations = await response.json();

                    reservations.forEach(reservation => {
                        if (reservation.property_id == propertyId && reservation.status ===
                            'confirmed') {
                            const range = generateAllDates(reservation.check_in, reservation
                                .check_out);
                            fullDates.push(...range);
                        }
                    });

                    return selectedDates.some(date => fullDates.includes(date));

                } catch (error) {
                    console.error('Error fetching reservations:', error);
                    return false;
                }
            }

            //Updates the total price based on the selected date range
            async function updatePrice(startDate, endDate, propertyId){
                try {
                    const response = await fetch(
                        `/api/property-price-range?start_date=${startDate}&end_date=${endDate}&property_id=${propertyId}`
                    );
                    const data = await response.json();
                    const total = data.reduce((sum, night) => sum + parseFloat(night.price), 0);
                    displayMessage('total-price',
                        `Total amount: ${total.toFixed(2)} € (${data.length} nights)`, '#2a4261');
                    document.getElementById('total_price_input').value = total.toFixed(2);
                } catch (error) {
                    displayMessage('total-price', 'Error obtaining prices.', '#e07a5f');
                }
            }

            //Displays a message in an HTML element
            function displayMessage(elementId, message, color = '#000') {
                const el = document.getElementById(elementId);
                el.textContent = message;
                el.style.color = color;
            }

            //Generates an array of dates between two dates
            function generateAllDates(checkIn, checkOut) {
                const start = moment(checkIn);
                const end = moment(checkOut);
                const dates = [];

                while (start.isBefore(end) || start.isSame(end, 'day')) {
                    dates.push(start.format('YYYY-MM-DD'));
                    start.add(1, 'days');
                }

                return dates;
            }

            // Check gaps between reservations to ensure the minimum number of nights
            function checkbetweenReservations(reservedDates, minNights) {
                const today = moment().startOf('day');

                const sorted = Array.from(reservedDates)
                    .filter(date => moment(date, "YYYY-MM-DD").isSameOrAfter(today))
                    .sort((a, b) => a.localeCompare(b));

                if (sorted.length < 2) return;
                const blockedRanges = [];

                for (let i = 0; i < sorted.length - 1; i++) {
                    const currentDate = moment(sorted[i], "YYYY-MM-DD");
                    const nextDate = moment(sorted[i + 1], "YYYY-MM-DD");
                    const gap = nextDate.diff(currentDate, 'days') - 1;

                    if (gap > 0 && gap < minNights) {
                        for (let d = 1; d <= gap; d++) {
                            blockedRanges.push(currentDate.clone().add(d, "days").format("YYYY-MM-DD"));
                        }
                    }
                }
                blockedRanges.forEach(date => reservedDates.add(date));
            }
        });
    </script>

</body>

</html>
