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
    <h5>Select a Date Range</h5>
    @csrf
    <input type="text" id="daterange" placeholder="Select a date range" />


    <!-- jQuery and DateRangePicker script from CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>


    <!-- Initialize Date Range Picker -->
    <script>
        let ddd = @json($reservations);
        let ppp = @json($properties);
        console.log(ppp);


        // para cuando lo haga por apartamentos usa algo como este array con todos los dias
        var dbDates = [
            '2024-09-15',
            '2024-09-20'
        ];

        // Function to display available properties
        function displayAvailableProperties(properties) {
            let container = $('#available-properties');
            container.empty();

            if (properties.length === 0) {
                container.append('<p>No properties available for the selected dates.</p>');
                return;
            }

            properties.forEach(property => {
                let propertyHtml = `
                <div class="property-card">
                    <img src="/images/${property.image_url}" alt="${property.title}" class="property-image" style="height: 200px; width: 300px;">
                    <h3>${property.title}</h3>
                    <p>${property.description}</p>
                    <p>Price/Night: $${property.price_per_night}</p>
                </div>
            `;
                container.append(propertyHtml);
            });
        }

        displayAvailableProperties(ppp);

        $('#daterange').daterangepicker({
            "autoApply": true,
            "linkedCalendars": true,
            "autoUpdateInput": true,
            "showCustomRangeLabel": true,
            "showDropdowns": false,
            "minDate": moment(),
            "opens": "center",
            "drops": "auto",
            "isInvalidDate": function (date) {
                // Check if the date is in the invalidDates array
                return dbDates.includes(date.format('YYYY-MM-DD'));
                //Cuando haga una reserva por apartamento quiero que se bloqueen todas las fechas ocupadas

            }
        }, function (start, end) {
            let newCheckIn = moment(start);
            let newCheckOut = moment(end);
            let occupiedPropertyIds = [];

            // Function to check if two date ranges overlap
            function checkOverlap(newCheckIn, newCheckOut, existingCheckIn, existingCheckOut) {
                return newCheckIn.isBefore(existingCheckOut) && newCheckOut.isAfter(existingCheckIn) || newCheckIn.isAfter(existingCheckOut) && newCheckOut.isBefore(existingCheckIn);
            }

            // Iterate over existing reservations to find overlaps
            for (let i = 0; i < ddd.length; i++) {
                let reservation = ddd[i];
                let existingCheckIn = moment(reservation.check_in);
                let existingCheckOut = moment(reservation.check_out);

                // If there's an overlap, add the property ID to the occupied list
                if (checkOverlap(newCheckIn, newCheckOut, existingCheckIn, existingCheckOut)) {
                    occupiedPropertyIds.push(reservation.property_id);
                }
            }
            // Filter out occupied properties
            let availableProperties = @json($properties).filter(property => !occupiedPropertyIds.includes(property.id));

            console.log("Occupied Property IDs:", occupiedPropertyIds);
            console.log("Available Property IDs:", availableProperties);

            // Call the function to display available properties
            displayAvailableProperties(availableProperties);


        });

    </script>

</body>

</html>
