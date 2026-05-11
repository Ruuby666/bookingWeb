(function () {
    "use strict";

    // Configuración inyectada desde Blade
    const _maxCapacity = window.FORM_CONFIG?.maxCapacity ?? 0;

    // --- Elementos del DOM ---
    const phoneInput = document.querySelector("#number");
    const contactForm = document.querySelector(".contact-form");

    if (!phoneInput || !contactForm) return; // Guardia: si no está el form, no ejecutar

    // --- Inicialización de intl-tel-input ---
    const iti = window.intlTelInput(phoneInput, {
        initialCountry: "auto",
        separateDialCode: true,
        utilsScript:
            "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js",
    });

    // --- Helpers de errores ---
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

    // --- Validación de capacidad (tiempo real) ---
    function validateCapacity() {
        const adults = parseInt(document.getElementById("adults").value) || 0;
        const children =
            parseInt(document.getElementById("children").value) || 0;
        const adultsInput = document.getElementById("adults");
        const childrenInput = document.getElementById("children");

        adultsInput.classList.remove("js-error");
        childrenInput.classList.remove("js-error");
        document
            .querySelectorAll(
                "#adults ~ .js-error-message, #children ~ .js-error-message",
            )
            .forEach((e) => e.remove());

        if (adults + children > _maxCapacity) {
            adultsInput.classList.add("js-error");
            childrenInput.classList.add("js-error");
            showJSError(
                adultsInput,
                `The max guests possible are ${_maxCapacity}`,
            );
        }
    }

    // --- Validación completa al enviar ---
    function validateForm() {
        let isValid = true;
        clearJSErrors();

        // Campos requeridos (excepto teléfono, tiene validación propia)
        ["adults", "children", "name", "email", "verification_email"].forEach(
            function (field) {
                const input = document.getElementById(field);
                if (input && !input.value.trim()) {
                    input.classList.add("js-error");
                    showJSError(input, "This field is required");
                    isValid = false;
                }
            },
        );

        // Teléfono
        if (!phoneInput.value.trim()) {
            phoneInput.classList.add("js-error");
            showJSError(phoneInput, "Phone number is required");
            isValid = false;

        } else if (iti.isValidNumber()) {
            phoneInput.value = iti.getNumber();

        } else {
            phoneInput.classList.add("js-error");
            showJSError(phoneInput, "Prefix or phone number invalid");
            isValid = false;
        }

        // Emails coinciden
        const email = document.getElementById("email").value;
        const verifyEmail = document.getElementById("verification_email").value;
        if (email && verifyEmail && email !== verifyEmail) {
            const verifyInput = document.getElementById("verification_email");
            verifyInput.classList.add("js-error");
            showJSError(verifyInput, "The emails do not match");
            isValid = false;
        }

        // Capacidad máxima
        const adults = parseInt(document.getElementById("adults").value) || 0;
        const children =
            parseInt(document.getElementById("children").value) || 0;
        if (adults + children > _maxCapacity) {
            const adultsInput = document.getElementById("adults");
            const childrenInput = document.getElementById("children");
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

    // --- Event Listeners ---
    contactForm.addEventListener("submit", function (e) {
        if (!validateForm()) {
            e.preventDefault();
            return false;
        }
        document.getElementById("loadingOverlay").style.display = "flex";
        phoneInput.value = iti.getNumber();
    });

    document
        .getElementById("verification_email")
        .addEventListener("blur", function () {
            const email = document.getElementById("email").value;
            const verifyEmail = this.value;
            this.classList.remove("js-error");
            const jsError = this.parentNode.querySelector(".js-error-message");
            if (jsError) jsError.remove();
            if (email && verifyEmail && email !== verifyEmail) {
                this.classList.add("js-error");
                showJSError(this, "The emails do not match");
            }
        });

    contactForm.addEventListener("keydown", function (e) {
        if (e.key === "Enter" && e.target.tagName !== "TEXTAREA") {
            e.preventDefault();
        }
    });

    document
        .getElementById("adults")
        .addEventListener("input", validateCapacity);
    document
        .getElementById("children")
        .addEventListener("input", validateCapacity);
})();
