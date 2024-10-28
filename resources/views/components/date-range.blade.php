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
    <div class="daterange-container">
        <h5>Select a Date Range</h5>
        @csrf
        <input type="text" id="daterange" placeholder="Select a date range" />
    </div>


    <!-- jQuery and DateRangePicker script from CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>


    <!-- Initialize Date Range Picker -->
    <script>
        let reservations = @json($reservations).filter(reservation => reservation.status === 'confirmed');
        let availableProperties = @json($properties);

        var dbDates = [
            // if you want to unavailable any date '2024-09-15'(example)
        ];

        function displayAvailableProperties(properties) {
            let container = $('#available-properties');
            container.empty();

            if (properties.length === 0) {
                container.append('<p>No properties available for the selected dates.</p>');
                return;
            }

            properties.forEach(property => {
                let propertyHtml = `
                <div class="cardcontainer">
                    <div class="photo">
                        <img src="/images/${property.image_url}" alt="Image not found"style="height: 200px; width: 300px;">
                    </div>
                    <div class="content">
                        <p class="txt4">${property.title}</p>
                        <p class="txt5">${property.location}</p>
                        <p class="txt2">${property.description}</p>
                    </div>
                    <div class="footer-card">
                        <p><a class="waves-effect waves-light btn" href="/property/${property.id}">Read More</a><a
                                id="heart"><span class="like"><i class="fab fa-gratipay"></i>Like</span></a></p>
                    </div>
                </div>
                `;
                container.append(propertyHtml);
            });
        }

        displayAvailableProperties(availableProperties);

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
                return dbDates.includes(date.format('YYYY-MM-DD'));
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
            
            let availableProperties = @json($properties).filter(property => !occupiedPropertyIds.includes(
                property.id));

            console.log("Occupied Property IDs:", occupiedPropertyIds);
            console.log("Available Property IDs:", availableProperties);

            displayAvailableProperties(availableProperties);


        });
    </script>

</body>

</html>
