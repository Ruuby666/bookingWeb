document.addEventListener('DOMContentLoaded', () => {

    'use strict';

    const form = document.getElementById('priceForm');

    if (!form) {
        return;
    }

    const property = document.getElementById('property_id');
    const start = document.getElementById('start_date');
    const end = document.getElementById('end_date');
    const price = document.getElementById('price_per_night');
    const errorDiv = document.getElementById('formErrors');
    const modal = document.getElementById('addPriceModal');

    //---------------------------------------------------------
    // Initial configuration
    //---------------------------------------------------------

    const today = new Date().toISOString().split('T')[0];

    start.min = today;
    end.min = today;

    //---------------------------------------------------------
    // Automatic end date
    //---------------------------------------------------------

    start.addEventListener('change', () => {

        if (!start.value) {
            return;
        }

        const nextDay = new Date(start.value);

        nextDay.setDate(nextDay.getDate() + 1);

        const formatted = nextDay.toISOString().split('T')[0];

        end.min = formatted;

        if (!end.value || end.value < formatted) {
            end.value = formatted;
        }

    });

    //---------------------------------------------------------
    // Form validation
    //---------------------------------------------------------

    form.addEventListener('submit', function (e) {

        let errors = [];

        errorDiv.innerHTML = '';

        clearValidation();

        if (!property.value) {

            errors.push('Please select a property.');

            property.classList.add('invalid');
        }

        if (!start.value) {

            errors.push('Please select a start date.');

            start.classList.add('invalid');
        }

        if (!end.value) {

            errors.push('Please select an end date.');

            end.classList.add('invalid');
        }

        if (start.value && end.value) {

            const startDate = new Date(start.value);
            const endDate = new Date(end.value);

            if (startDate >= endDate) {

                errors.push('End date must be after start date.');

                start.classList.add('invalid');
                end.classList.add('invalid');
            }
        }

        if (!price.value || parseFloat(price.value) <= 0) {

            errors.push('Price must be greater than zero.');

            price.classList.add('invalid');
        }

        if (errors.length > 0) {

            e.preventDefault();

            errorDiv.innerHTML = errors
                .map(error => `<div>• ${error}</div>`)
                .join('');

            errorDiv.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

    });

    //---------------------------------------------------------
    // Clear error styles
    //---------------------------------------------------------

    function clearValidation() {

        property.classList.remove('invalid');
        start.classList.remove('invalid');
        end.classList.remove('invalid');
        price.classList.remove('invalid');

    }

    //---------------------------------------------------------
    // Clear error while typing
    //---------------------------------------------------------

    [
        property,
        start,
        end,
        price
    ].forEach(input => {

        input.addEventListener('input', () => {

            input.classList.remove('invalid');

        });

    });

    //---------------------------------------------------------
    // Open modal
    //---------------------------------------------------------

    window.openPriceModal = function () {

        form.reset();

        clearValidation();

        errorDiv.innerHTML = '';

        start.min = today;
        end.min = today;

        modal.style.display = 'block';

        property.focus();

    };

    //---------------------------------------------------------
    // Close modal
    //---------------------------------------------------------

    window.closePriceModal = function () {

        modal.style.display = 'none';

    };

    //---------------------------------------------------------
    // Click outside the modal
    //---------------------------------------------------------

    window.addEventListener('click', function (e) {

        if (e.target === modal) {

            closePriceModal();

        }

    });

    //---------------------------------------------------------
    // Escape
    //---------------------------------------------------------

    document.addEventListener('keydown', function (e) {

        if (e.key === 'Escape') {

            closePriceModal();

        }

    });

});