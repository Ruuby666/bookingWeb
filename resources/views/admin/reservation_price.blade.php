<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Lista de Precios de Reservas por Rango</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/admin_reservation_price.css') }}" />
    <link href="{{ asset('css/toast.css') }}" rel="stylesheet">
</head>

<body>
    @include('components.header')

    @if (session('success'))
    <x-toast :message="session('success')" type="success" />
    @endif

    @if (session('error'))
    <x-toast :message="session('error')" type="error" />
    @endif

    <div class="container">
        <h2>Lista de Precios de Reservas por Rango</h2>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Propiedad</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Precio por Noche</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservationPrices as $price)
                <tr>
                    <td>{{ $price->property->title ?? 'N/A' }}</td>
                    <td>{{ $price->start_date }}</td>
                    <td>{{ $price->end_date }}</td>
                    <td>${{ number_format($price->price_per_night, 2) }}</td>
                    <td>
                        <form action="{{ route('reservation-prices.destroy', $price->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este rango?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete">X</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">No hay rangos de precios disponibles.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="floating-button-container">
        <button class="floating-button" onclick="openPriceModal()">
            Añadir Rango de Precio
        </button>
    </div>


    <!-- Modal -->
    <div class="modal" id="addPriceModal">
        <form id="priceForm" class="modal-content" method="POST" action="{{ route('reservation-prices.create') }}">
            @csrf
            <div class="modal-header">
                <h5>Añadir Rango de Precio</h5>
                <button type="button" class="btn-close" onclick="document.getElementById('addPriceModal').style.display='none'">&times;</button>
            </div>
            <div class="modal-body">
                <label for="property_id">Propiedad</label>
                <select name="property_id" id="property_id" required>
                    <option value="">Seleccione una propiedad</option>
                    @foreach($properties as $property)
                    <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                        {{ $property->title }}
                    </option>
                    @endforeach
                </select>

                <label for="start_date">Fecha de Inicio</label>
                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required>

                <label for="end_date">Fecha de Fin</label>
                <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" required>

                <label for="price_per_night">Precio por Noche</label>
                <input type="number" step="0.01" name="price_per_night" id="price_per_night" value="{{ old('price_per_night') }}" required>

            </div>
            <div id="formErrors" class="error-message"></div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>

    @include('components.footer')
    <script src="{{ asset('js/session-expiry.js') }}"></script>
    <script>
        document.getElementById('priceForm').addEventListener('submit', function(e) {
            let errors = [];

            // Inputs
            const property = document.getElementById('property_id');
            const start = document.getElementById('start_date');
            const end = document.getElementById('end_date');
            const price = document.getElementById('price_per_night');
            const errorDiv = document.getElementById('formErrors');

            // Limpiar errores previos
            errorDiv.innerHTML = '';

            // Validaciones
            if (!property.value) {
                errors.push('Debe seleccionar una propiedad.');
            }

            if (!start.value) {
                errors.push('Debe ingresar una fecha de inicio.');
            }

            if (!end.value) {
                errors.push('Debe ingresar una fecha de fin.');
            }

            const startDate = new Date(start.value);
            const endDate = new Date(end.value);

            if (start.value && end.value && startDate >= endDate) {
                errors.push('La fecha de fin debe ser posterior a la fecha de inicio.');
            }

            if (!price.value || parseFloat(price.value) <= 0) {
                errors.push('Debe ingresar un precio válido mayor que 0.');
            }

            // Si hay errores, evitar el envío y mostrarlos
            if (errors.length > 0) {
                e.preventDefault();

                errorDiv.innerHTML = errors.map(error => `<div>• ${error}</div>`).join('');
            }
        });

        function openPriceModal() {
            document.getElementById('addPriceModal').style.display = 'block';
            document.getElementById('formErrors').innerHTML = '';
            document.getElementById('priceForm').reset();
        }
    </script>

</body>

</html>


</html>