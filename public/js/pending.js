function openModal(id) {
    document.getElementById('modal-' + id).classList.remove('hidden');
}

function closeModal(id) {
    document.getElementById('modal-' + id).classList.add('hidden');
}

function redirectFromButton(button) {
    const url = button.getAttribute('data-url');
    if (url) {
        window.location.href = url;
    }
}

function openFacturaModal() {
    const checkboxes = document.querySelectorAll('.reservation-checkbox:checked');
    const list = document.getElementById('selected-reservations-list');
    list.innerHTML = '';

    if (checkboxes.length === 0) {
        list.innerHTML = '<li>No hay reservas seleccionadas.</li>';
    } else {
        checkboxes.forEach(cb => {
            const li = document.createElement('li');
            li.textContent = `Reserva ID: ${cb.value}`;
            list.appendChild(li);
        });
    }

    document.getElementById('modal-factura').classList.remove('hidden');
}

function redirectfacturaFromButton(button) {
    const url = button.getAttribute('data-url');
    const checkboxes = document.querySelectorAll('.reservation-checkbox:checked');
    const invoiceAmount = document.getElementById('invoice-amount').value;

    const ids = Array.from(checkboxes).map(cb => cb.value);

    if (ids.length === 0) {
        alert("Selecciona al menos una reserva.");
        return;
    }

    if (!invoiceAmount) {
        alert("Introduce el número de la primera factura.");
        return;
    }

    const params = new URLSearchParams();
    ids.forEach(id => params.append('ids[]', id));
    params.append('invoice_amount', invoiceAmount);

    const finalUrl = `${url}?${params.toString()}`;
    window.open(finalUrl, '_blank');
    closeFacturaModal();
    setTimeout(() => location.reload(), 1000);

}


function closeFacturaModal() {
    document.getElementById('modal-factura').classList.add('hidden');
}