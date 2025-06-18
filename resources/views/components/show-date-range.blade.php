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
    <p id="total-price">Minimum {{$property->min_nights}} nights</p>

    <!-- jQuery and DateRangePicker script from CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <!-- Initialize Date Range Picker -->
    <script>
        let reservations = @json($reservations).filter(reservation => reservation.status === 'confirmed');
        let idProperty = {{$id}};
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
            "minDate": moment().add(1, 'days'),
            "startDate": moment().add(1, 'days'),
            "endDate": moment().add(1, 'days'),
            "opens": "center",
            "drops": "auto",
            "isInvalidDate": function(date) {
                return fullDates.includes(date.format('YYYY-MM-DD'));
            }
        }, function(start, end) {
            let newCheckIn = moment(start);
            let newCheckOut = moment(end);
            let minNights = {{$property->min_nights}};
            let nights = newCheckOut.diff(newCheckIn, 'days');

            if (nights < minNights) {
                document.getElementById('total-price').textContent = ' The minimum stay is ' + minNights + ' nights.';
                return;
            }

            let selectedDates = [];
            let tempDate = moment(newCheckIn);

            while (tempDate.isBefore(newCheckOut)) {
                selectedDates.push(tempDate.format('YYYY-MM-DD'));
                tempDate.add(1, 'days');
            }

            let hasOverlap = selectedDates.some(date => fullDates.includes(date));

            if (hasOverlap) {
                document.getElementById('total-price').style.color = '#e07a5f';
                document.getElementById('total-price').textContent = 'Selected dates overlap with an existing reservation.';
                return;
            }

            fetch(`/api/property-price-range?start_date=${newCheckIn}&end_date=${newCheckOut}&property_id=${idProperty}`)
                .then(res => res.json())
                .then(data => {
                    let total = data.reduce((sum, night) => sum + parseFloat(night.price), 0);
                    document.getElementById('total-price').style.color = '#2a4261';
                    document.getElementById('total-price').textContent = `Total amount: ${total.toFixed(2)} € (${data.length} nights)`;
                    document.getElementById('total_price_input').value = total.toFixed(2);
                })
                .catch(err => {
                    document.getElementById('total-price').textContent = 'Error to obtein prices.';
                });

        });
    </script>

</body>

</html>