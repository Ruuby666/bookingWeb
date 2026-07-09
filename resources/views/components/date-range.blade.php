<meta charset="UTF-8">
<title>Date Range Picker</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

@include('components.daterangepicker-assets')

<div class="daterange-container">
    <div class="daterange-header">
        <h2>Select Range</h2>
        <i class="fa-solid fa-repeat" id="reset-btn"></i>
    </div>
    <input type="text" id="daterange" placeholder="Select a date range" readonly />
    <i class="fa-solid fa-repeat" id="reset-btn-second"></i>
</div>

<script>
    $(document).ready(function() {

        // Initialize Date Range Picker
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

        // Fetch reservations and render available properties
        async function fetchDataAndRenderProperties(startDate, endDate) {

            showLoader();

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

                renderProperties(availableProps, window.INDEX_CONFIG.propertyWithImages);
            } catch (error) {
                console.error("Error fetching data:", error);
            } finally {

                hideLoader();

            }
        }

        // Check overlapping reservations
        function getOccupiedPropertyIds(reservations, newStart, newEnd) {
            const start = moment(newStart);
            const end = moment(newEnd);
            let occupied = [];

            reservations.forEach(res => {
                const checkIn = moment(res.check_in);
                const checkOut = moment(res.check_out);

                const isOverlap = start.isBefore(checkOut) && end.isAfter(checkIn);
                if (isOverlap) {
                    occupied.push(res.property_id);
                }
            });

            return occupied;
        }

        // Render property cards
        function renderProperties(properties, images) {
            const container = $('#available-properties');
            container.empty();

            if (properties.length === 0) {
                const message = document.createElement('p');
                message.textContent = 'No properties available for the selected dates.';
                container.append(message);
                return;
            }

            properties.forEach(prop => {
                const img = images[prop.id] || 'default.jpg';

                const link = document.createElement('a');
                link.href = `/property/${prop.id}`;

                const card = document.createElement('div');
                card.className = 'cardcontainer';

                const photo = document.createElement('div');
                photo.className = 'photo';

                const image = document.createElement('img');
                image.src = `/storage/images/${prop.images_div}/${img}`;
                image.alt = 'Image not found';
                image.style.height = '200px';
                image.style.width = '300px';
                photo.appendChild(image);

                const content = document.createElement('div');
                content.className = 'content';

                const title = document.createElement('p');
                title.className = 'txt4';
                title.textContent = prop.title;

                const location = document.createElement('p');
                location.className = 'txt5';
                location.textContent = prop.location;

                const description = document.createElement('p');
                description.className = 'txt2';
                description.textContent = prop.description;

                content.append(title, location, description);
                card.append(photo, content);
                link.appendChild(card);

                container.append(link);
            });
        }

        // Fetch and render all properties
        async function showAllProperties() {
            showLoader();

            try {
                const propertiesRes = await fetch('/api/properties');
                const properties = await propertiesRes.json();
                renderProperties(properties, window.INDEX_CONFIG.propertyWithImages);
            } catch (error) {
                console.error("Error fetching all properties:", error);
            } finally {
                hideLoader();
            }
        }

        // Reset dates and reload properties
        function resetDateRangeAndProperties() {
            const tomorrow = moment().add(1, 'days');
            const picker = $('#daterange').data('daterangepicker');
            picker.setStartDate(tomorrow);
            picker.setEndDate(tomorrow);

            $('#daterange').val(
                tomorrow.format('DD/MM/YYYY') +
                ' - ' +
                tomorrow.format('DD/MM/YYYY')
            );

            showAllProperties();
        }

        $('#reset-btn, #reset-btn-second').on('click', function() {
            resetDateRangeAndProperties();
        });

        // Show loader component
        function showLoader() {
            $('#loader').css('display', 'flex');
            $('#carousel-container').css('opacity', '0');
            $('#carousel-container').css('display', 'none');
        }

        // Hide loader component
        function hideLoader() {
            $('#loader').hide();
            $('#carousel-container').css('opacity', '1');
            $('#carousel-container').css('display', 'flex');

        }


    });
</script>