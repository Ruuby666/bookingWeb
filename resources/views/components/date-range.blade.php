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
    <input type="text" id="daterange" placeholder="Select a date range"/>

    <!-- jQuery and DateRangePicker script from CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment/min/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>


    <!-- Initialize Date Range Picker -->
    <script>
        var dbDates = [
            '2024-09-15',
            '2024-09-20'
        ];

        $('#daterange').daterangepicker({
            "autoApply": true,
            "linkedCalendars": false,
            "autoUpdateInput": true,
            "showCustomRangeLabel": false,
            "minDate": moment(),
            "opens": "center",
            "drops": "auto",
            "isInvalidDate": function (date) {
                // Check if the date is in the invalidDates array
                return dbDates.includes(date.format('YYYY-MM-DD'));
            }
        }, function (start, end) {

            console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
        });

    </script>

</body>

</html>
