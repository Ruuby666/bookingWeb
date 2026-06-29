/**
 * date-range.js
 * Require: jQuery, moment.js, daterangepicker (cargados antes de este script)
 *
 * Configuración inyectada desde Blade:
 *   window.DATE_RANGE_CONFIG = { propertyId, minNights }
 */

$(document).ready(async function () {
    'use strict';

    const propertyId = window.DATE_RANGE_CONFIG?.propertyId;
    const minNights  = window.DATE_RANGE_CONFIG?.minNights;

    // Obtén todas las fechas ocupadas antes de inicializar el picker
    const reservedDates = await fetchReservedDates(propertyId);
    checkBetweenReservations(reservedDates, minNights);
    initializeDateRangePicker(reservedDates);

    // --- Fetch fechas reservadas ---
    async function fetchReservedDates(propertyId) {
        try {
            const response     = await fetch(`/property/${propertyId}/reservations`);
            const reservations = await response.json();
            const dates        = new Set();

            reservations.forEach(reservation => {
                if (reservation.property_id == propertyId) {
                    const range = generateAllDates(reservation.check_in, reservation.check_out);
                    range.forEach(date => dates.add(date));
                }
            });

            return dates;
        } catch (error) {
            console.error('Error fetching reserved dates:', error);
            return new Set();
        }
    }

    // --- Inicializar el picker ---
    function initializeDateRangePicker(reservedDates) {
        $('#daterange').daterangepicker({
            locale:               { format: 'DD/MM/YYYY' },
            autoApply:            true,
            linkedCalendars:      true,
            autoUpdateInput:      true,
            showCustomRangeLabel: true,
            showDropdowns:        false,
            minDate:              moment().add(1, 'days'),
            endDate:              moment().add(1, 'days'),
            opens:                'center',
            drops:                'auto',
            isInvalidDate: function (date) {
                return reservedDates.has(date.format('YYYY-MM-DD'));
            }
        }, function (start, end) {
            fetchDataAndRenderProperties(start, end);
        });
    }

    // --- Validar rango seleccionado y actualizar precio ---
    async function fetchDataAndRenderProperties(startDate, endDate) {
        const checkIn  = moment(startDate);
        const checkOut = moment(endDate);
        const totalNights = checkOut.diff(checkIn, 'days');

        if (totalNights < minNights) {
            displayMessage('total-price', `The minimum stay is ${minNights} nights.`, '#e07a5f');
            return;
        }

        const selectedDates = getDateRangeArray(checkIn, checkOut);
        const fullDates     = [];
        const hasOverlap    = await checkForOverlaps(propertyId, fullDates, selectedDates);

        if (hasOverlap) {
            displayMessage('total-price', 'Selected dates overlap with an existing reservation.', '#e07a5f');
            return;
        }

        await updatePrice(
            checkIn.format('YYYY-MM-DD'),
            checkOut.format('YYYY-MM-DD'),
            propertyId
        );
    }

    // --- Array de fechas entre dos momentos (sin incluir checkout) ---
    function getDateRangeArray(start, end) {
        const dates   = [];
        const current = moment(start);

        while (current.isBefore(end)) {
            dates.push(current.format('YYYY-MM-DD'));
            current.add(1, 'days');
        }

        return dates;
    }

    // --- Comprobar solapamiento con reservas confirmadas ---
    async function checkForOverlaps(propertyId, fullDates, selectedDates) {
        try {
            const response     = await fetch('/api/reservations');
            const reservations = await response.json();

            reservations.forEach(reservation => {
                if (reservation.property_id == propertyId) {
                    const range = generateAllDates(reservation.check_in, reservation.check_out);
                    fullDates.push(...range);
                }
            });

            return selectedDates.some(date => fullDates.includes(date));
        } catch (error) {
            console.error('Error fetching reservations:', error);
            return false;
        }
    }

    // --- Actualizar precio total ---
    async function updatePrice(startDate, endDate, propertyId) {
        showPriceSpinner();
        try {
            const response = await fetch(
                `/api/property-price-range?start_date=${startDate}&end_date=${endDate}&property_id=${propertyId}`
            );
            const data  = await response.json();
            const total = data.reduce((sum, night) => sum + parseFloat(night.price), 0);

            displayMessage('total-price', `Total amount: ${total.toFixed(2)} € (${data.length} nights)`, '#2a4261');
            document.getElementById('total_price_input').value = total.toFixed(2);
        } catch (error) {
            displayMessage('total-price', 'Error obtaining prices.', '#e07a5f');
        }
    }

    // --- Spinner mientras carga el precio ---
    function showPriceSpinner() {
        const el = document.getElementById('total-price');
        el.innerHTML = '<span class="price-spinner"></span>';
        el.style.color = '';
    }

    // --- Mostrar mensaje en un elemento ---
    function displayMessage(elementId, message, color = '#000') {
        const el   = document.getElementById(elementId);
        el.textContent = message;
        el.style.color = color;
    }

    // --- Generar array de fechas entre check-in y check-out (inclusivo) ---
    function generateAllDates(checkIn, checkOut) {
        const start = moment(checkIn);
        const end   = moment(checkOut);
        const dates = [];

        while (start.isBefore(end) || start.isSame(end, 'day')) {
            dates.push(start.format('YYYY-MM-DD'));
            start.add(1, 'days');
        }

        return dates;
    }

    // --- Bloquear huecos menores a minNights entre reservas ---
    function checkBetweenReservations(reservedDates, minNights) {
        const today = moment().startOf('day');

        const sorted = Array.from(reservedDates)
            .filter(date => moment(date, 'YYYY-MM-DD').isSameOrAfter(today))
            .sort((a, b) => a.localeCompare(b));

        if (sorted.length < 2) return;

        for (let i = 0; i < sorted.length - 1; i++) {
            const currentDate = moment(sorted[i],     'YYYY-MM-DD');
            const nextDate    = moment(sorted[i + 1], 'YYYY-MM-DD');
            const gap         = nextDate.diff(currentDate, 'days') - 1;

            if (gap > 0 && gap < minNights) {
                for (let d = 1; d <= gap; d++) {
                    reservedDates.add(currentDate.clone().add(d, 'days').format('YYYY-MM-DD'));
                }
            }
        }
    }
});
