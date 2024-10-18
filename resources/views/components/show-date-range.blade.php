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
    <input type="text" id="daterange" placeholder="Select a date range" />


    <!-- jQuery and DateRangePicker script from CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>


    <!-- Initialize Date Range Picker -->
    <script>
        let reservations = @json($reservations);
        let idProperty = {{ $id }};
        var fullDates = [];

        reservations.forEach(reservation => {
            if (idProperty == reservation.property_id ) {
                generateAllDates(reservation.check_in, reservation.check_out);
            }
        });

        // Function to create full dates
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
            "autoApply": true,
            "linkedCalendars": true,
            "autoUpdateInput": true,
            "showCustomRangeLabel": true,
            "showDropdowns": false,
            "minDate": moment(),
            "opens": "center",
            "drops": "auto",
            "isInvalidDate": function(date) {
                // Check if the date is in the invalidDates array
                return fullDates.includes(date.format('YYYY-MM-DD'));
                //Cuando haga una reserva por apartamento quiero que se bloqueen todas las fechas ocupadas

            }
        }, function(start, end) {
            let newCheckIn = moment(start);
            let newCheckOut = moment(end);
            let occupiedPropertyIds = [];

            // Function to check if two date ranges overlap
            function checkOverlap(newCheckIn, newCheckOut, existingCheckIn, existingCheckOut) {
                return newCheckIn.isBefore(existingCheckOut) && newCheckOut.isAfter(existingCheckIn) || newCheckIn
                    .isAfter(existingCheckOut) && newCheckOut.isBefore(existingCheckIn);
            }

            // Iterate over existing reservations to find overlaps
            for (let i = 0; i < reservations.length; i++) {
                let reservation = reservations[i];
                let existingCheckIn = moment(reservation.check_in);
                let existingCheckOut = moment(reservation.check_out);

                // If there's an overlap, add the property ID to the occupied list
                if (checkOverlap(newCheckIn, newCheckOut, existingCheckIn, existingCheckOut)) {
                    occupiedPropertyIds.push(reservation.property_id);
                }
            }
            // Filter out occupied properties
            let availableProperties = @json($properties).filter(property => !occupiedPropertyIds.includes(
                property.id));

            console.log("Occupied Property IDs:", occupiedPropertyIds);
            console.log("Available Property IDs:", availableProperties);

            // Call the function to display available properties
            displayAvailableProperties(availableProperties);


        });
    </script>

</body>

</html>
