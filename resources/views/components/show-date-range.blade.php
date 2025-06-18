<!-- resources/views/daterange.blade.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Date Range Picker</title>

    <!-- Import Daterangepicker and jQuery from CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="{{ asset('css/date-range.css') }}">
</head>

<body>
    @csrf
    <input type="text" id="daterange" name="daterange" placeholder="Select a date range" /><b> *</b>
    <p id="total-price"></p>

    <!-- jQuery and DateRangePicker script from CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <!-- Initialize Date Range Picker -->
    <script>
        let reservations = @json($reservations).filter(reservation => reservation.status === 'confirmed');
        let idProperty = {{ $id }};
        var fullDates = [];

        reservations.forEach(reservation => {
            if (idProperty == reservation.property_id) {
                generateAllDates(reservation.check_in, reservation.check_out);
            }
        });

        function generateAllDates(checkIn, checkOut) {
            let startDate = moment(checkIn);
            let endDate = moment(checkOut);
            let dateArray = [];

            while (startDate.isBefore(endDate) || startDate.isSame(endDate, 'day')) {
                dateArray.push(startDate.format("YYYY-MM-DD"));
                startDate.add(1, 'days');
            }
            dateArray.forEach(date => fullDates.push(date));
        }

        $('#daterange').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY'
            },
            "autoApply": true,
            "linkedCalendars": true,
            "autoUpdateInput": true,
            "showCustomRangeLabel": true,
            "showDropdowns": false,
            "minDate": moment(),
            "opens": "center",
            "drops": "auto",
            "isInvalidDate": function(date) {
                return fullDates.includes(date.format('YYYY-MM-DD'));
            }
        }, function(start, end) {
            let newCheckIn = moment(start);
            let newCheckOut = moment(end);
            let occupiedPropertyIds = [];

            function checkOverlap(newCheckIn, newCheckOut, existingCheckIn, existingCheckOut) {
                return newCheckIn.isBefore(existingCheckOut) && newCheckOut.isAfter(existingCheckIn) || newCheckIn
                    .isAfter(existingCheckOut) && newCheckOut.isBefore(existingCheckIn);
            }

            for (let i = 0; i < reservations.length; i++) {
                let reservation = reservations[i];
                let existingCheckIn = moment(reservation.check_in);
                let existingCheckOut = moment(reservation.check_out);

                if (checkOverlap(newCheckIn, newCheckOut, existingCheckIn, existingCheckOut)) {
                    occupiedPropertyIds.push(reservation.property_id);
                }
            }

            fetch(`/api/property-price-range?start_date=${newCheckIn}&end_date=${newCheckOut}&property_id=${idProperty}`)
                .then(res => res.json())
                .then(data => {
                    let total = data.reduce((sum, night) => sum + parseFloat(night.price), 0);
                    document.getElementById('total-price').textContent = `Precio total: ${total.toFixed(2)} € (${data.length} noches)`;
                    document.getElementById('total_price_input').value = total.toFixed(2);
                })
                .catch(err => {
                    console.error('Error obteniendo los precios:', err);
                    document.getElementById('total-price').textContent = 'Error al calcular el precio';
                });

        });
    </script>

</body>

</html>