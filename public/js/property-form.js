(function () {
    "use strict";

    // =====================================================
    // CONFIGURACIÓN
    // =====================================================

    const _maxCapacity = window.FORM_CONFIG?.maxCapacity ?? 0;

    let confirmedSubmission = false;

    // =====================================================
    // ELEMENTOS DEL DOM
    // =====================================================

    const phoneInput = document.querySelector("#number");
    const contactForm = document.querySelector(".contact-form");
    const reservationModal =
        document.getElementById("reservationModal");

    if (!phoneInput || !contactForm) {
        return;
    }

    // =====================================================
    // INTL TEL INPUT
    // =====================================================

    const iti = window.intlTelInput(phoneInput, {
        initialCountry: "es",
        preferredCountries: ["es", "gb", "fr", "de", "it"],
        separateDialCode: true,
        utilsScript:
            "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js",
    });

    // =====================================================
    // HELPERS
    // =====================================================

    function showJSError(input, message) {
        const el = document.createElement("span");

        el.className = "error-message js-error-message";
        el.textContent = message;

        input.parentNode.appendChild(el);
    }

    function clearJSErrors() {
        document
            .querySelectorAll(".js-error-message")
            .forEach((el) => el.remove());

        document
            .querySelectorAll(".js-error")
            .forEach((el) => el.classList.remove("js-error"));
    }

    function exceedsCapacity() {
        const adults =
            parseInt(document.getElementById("adults").value) || 0;

        const children =
            parseInt(document.getElementById("children").value) || 0;

        return adults + children > _maxCapacity;
    }

    // =====================================================
    // VALIDACIONES
    // =====================================================

    function validatePhoneField() {

        phoneInput.classList.remove("js-error");

        const existingError =
            phoneInput.parentNode.querySelector(".phone-error");

        if (existingError) {
            existingError.remove();
        }

        if (!phoneInput.value.trim()) {
            return;
        }

        if (!iti.isValidNumber()) {

            phoneInput.classList.add("js-error");

            const error = document.createElement("span");

            error.className =
                "error-message js-error-message phone-error";

            error.textContent =
                "Invalid phone number for selected country";

            phoneInput.parentNode.appendChild(error);
        }
    }

    function validateCapacity() {

        const adults =
            parseInt(document.getElementById("adults").value) || 0;

        const children =
            parseInt(document.getElementById("children").value) || 0;

        const adultsInput =
            document.getElementById("adults");

        const childrenInput =
            document.getElementById("children");

        adultsInput.classList.remove("js-error");
        childrenInput.classList.remove("js-error");

        document
            .querySelectorAll(
                "#adults ~ .js-error-message, #children ~ .js-error-message",
            )
            .forEach((e) => e.remove());

        if (exceedsCapacity()) {

            adultsInput.classList.add("js-error");
            childrenInput.classList.add("js-error");

            showJSError(
                adultsInput,
                `The max guests possible are ${_maxCapacity}`,
            );
        }
    }

    function validateForm() {

        let isValid = true;

        clearJSErrors();

        [
            "adults",
            "children",
            "name",
            "email",
            "verification_email",
        ].forEach(function (field) {

            const input =
                document.getElementById(field);

            if (input && !input.value.trim()) {

                input.classList.add("js-error");

                showJSError(
                    input,
                    "This field is required"
                );

                isValid = false;
            }
        });

        if (!phoneInput.value.trim()) {
            phoneInput.classList.add("js-error");
            showJSError(
                phoneInput,
                "Phone number is required"
            );
            isValid = false;

        } else if (!iti.isValidNumber()) {
            phoneInput.classList.add("js-error");
            showJSError(
                phoneInput,
                "Invalid phone number for selected country"
            );
            isValid = false;

        } else {
            phoneInput.value = iti.getNumber();
        }

        const email =
            document.getElementById("email").value;

        const verifyEmail =
            document.getElementById(
                "verification_email"
            ).value;

        if (
            email &&
            verifyEmail &&
            email !== verifyEmail
        ) {

            const verifyInput =
                document.getElementById(
                    "verification_email"
                );

            verifyInput.classList.add("js-error");

            showJSError(
                verifyInput,
                "The emails do not match"
            );

            isValid = false;
        }

        const adults =
            parseInt(document.getElementById("adults").value) || 0;

        const children =
            parseInt(document.getElementById("children").value) || 0;

        if (exceedsCapacity()) {

            const adultsInput =
                document.getElementById("adults");

            const childrenInput =
                document.getElementById("children");

            adultsInput.classList.add("js-error");
            childrenInput.classList.add("js-error");

            showJSError(
                adultsInput,
                `The max guests possible are ${_maxCapacity}`,
            );

            isValid = false;
        }

        return isValid;
    }

    // =====================================================
    // MODAL
    // =====================================================

    function populateConfirmationModal() {

        document.getElementById("modal-name").textContent =
            document.getElementById("name").value;

        document.getElementById("modal-email").textContent =
            document.getElementById("email").value;

        document.getElementById("modal-phone").textContent =
            iti.getNumber();

        document.getElementById("modal-adults").textContent =
            document.getElementById("adults").value;

        document.getElementById("modal-children").textContent =
            document.getElementById("children").value;

        document.getElementById("modal-dates").textContent =
            document.getElementById("daterange").value;

        document.getElementById("modal-message").textContent =
            document.getElementById("message").value || "-";

        document.getElementById("modal-price").textContent =
            document.getElementById("total-price").textContent;
    }

    // =====================================================
    // EVENT LISTENERS
    // =====================================================

    phoneInput.addEventListener(
        "input",
        validatePhoneField
    );

    phoneInput.addEventListener(
        "blur",
        validatePhoneField
    );

    phoneInput.addEventListener(
        "countrychange",
        validatePhoneField
    );

    document
        .getElementById("adults")
        .addEventListener(
            "input",
            validateCapacity
        );

    document
        .getElementById("children")
        .addEventListener(
            "input",
            validateCapacity
        );

    contactForm.addEventListener(
        "submit",
        function (e) {

            if (confirmedSubmission) {
                return;
            }

            if (!validateForm()) {

                e.preventDefault();

                return false;
            }

            e.preventDefault();

            populateConfirmationModal();

            reservationModal.style.display = "flex";
        }
    );

    document
        .getElementById("editReservationBtn")
        ?.addEventListener("click", function () {

            reservationModal.style.display = "none";
        });

    document
        .getElementById("confirmReservationBtn")
        ?.addEventListener("click", function () {

            confirmedSubmission = true;

            phoneInput.value = iti.getNumber();

            reservationModal.style.display = "none";

            document.getElementById(
                "loadingOverlay"
            ).style.display = "flex";

            contactForm.submit();
        });

})();