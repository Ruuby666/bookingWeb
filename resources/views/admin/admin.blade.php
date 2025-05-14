<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Panel</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}" />
</head>

<body>
    @include('components.header')

    <div class="container">
        <h1 class="page-title">🏠 Property Management</h1>

        <div class="table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Location</th>
                        <th>Price</th>
                        <th>Capacity</th>
                        <th>Features</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($properties as $property)
                        <tr>
                            <td onclick="openModal({{ $property->id }})">{{ $property->title }}</td>
                            <td>{{ $property->location }}</td>
                            <td>{{ $property->price_per_night }}€</td>
                            <td>{{ $property->capacity }}</td>
                            <td>
                                <div class="property-icons">
                                    <span class="badge {{ $property->wifi ? 'active' : '' }}" title="Wi-Fi">📶</span>
                                    <span class="badge {{ $property->tv ? 'active' : '' }}" title="TV">📺</span>
                                    <span class="badge {{ $property->pool ? 'active' : '' }}" title="Piscina">🏊</span>
                                    <span class="badge {{ $property->garden ? 'active' : '' }}" title="Jardín">🌳</span>
                                    <span class="badge {{ $property->parking ? 'active' : '' }}" title="Parking">🚗</span>
                                    <span class="badge {{ $property->terrace ? 'active' : '' }}" title="Terraza">🌅</span>
                                    <span class="badge {{ $property->safeBox ? 'active' : '' }}" title="Caja fuerte">🔒</span>
                                    <span class="badge {{ $property->entertainment ? 'active' : '' }}" title="Entretenimiento">🎮</span>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('properties.edit', $property) }}" class="btn-edit">✏️
                                        Edit</a>
                                    <form action="{{ route('properties.destroy', $property->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this property?');"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete">🗑️ Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <div id="modal-{{ $property->id }}" class="modal hidden">
                            <div class="modal-content">
                                <span class="close" onclick="closeModal({{ $property->id }})">&times;</span>
                                <h2>{{ $property->title }}</h2>
                                <ul>
                                    <li><strong>Descripción:</strong> {{ $property->description }}</li>
                                    <li><strong>Ubicación:</strong> {{ $property->location }}</li>
                                    <li><strong>Precio por noche:</strong> €{{ $property->price_per_night }}</li>
                                    <li><strong>Capacidad:</strong> {{ $property->capacity }}</li>
                                    <li><strong>Metros cuadrados:</strong> {{ $property->size }} m²</li>
                                    <li><strong>Dormitorios:</strong> {{ $property->bedrooms }}</li>
                                    <li><strong>Baños:</strong> {{ $property->bathrooms }}</li>
                                    <li><strong>Wi-Fi:</strong> {{ $property->wifi ? 'Sí' : 'No' }}</li>
                                    <li><strong>TV:</strong> {{ $property->tv ? 'Sí' : 'No' }}</li>
                                    <li><strong>Piscina:</strong> {{ $property->pool ? 'Sí' : 'No' }}</li>
                                    <li><strong>Jardín:</strong> {{ $property->garden ? 'Sí' : 'No' }}</li>
                                    <li><strong>Parking:</strong> {{ $property->parking ? 'Sí' : 'No' }}</li>
                                    <li><strong>Terraza:</strong> {{ $property->terrace ? 'Sí' : 'No' }}</li>
                                    <li><strong>Caja fuerte:</strong> {{ $property->safeBox ? 'Sí' : 'No' }}</li>
                                    <li><strong>Entretenimiento:</strong> {{ $property->entertainment }}</li>
                                    <li><strong>Latitud:</strong> {{ $property->lat }}</li>
                                    <li><strong>Longitud:</strong> {{ $property->lng }}</li>
                                </ul>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6">No properties available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="buttons">
            <a href="{{ route('properties.create') }}" class="btn-add">➕ Add New Property</a>
        </div>
    </div>

    <div id="session-data" data-session-lifetime="{{ config('session.lifetime') }}"
        data-redirect-url="{{ route('index') }}">
    </div>

    @include('components.footer')
    <script src="{{ asset('js/session-expiry.js') }}"></script>
    <script>
        function openModal(id) {
            document.getElementById(`modal-${id}`).style.display = 'block';
        }

        function closeModal(id) {
            document.getElementById(`modal-${id}`).style.display = 'none';
        }

        // Cerrar modal si se hace clic fuera de él
        window.onclick = function(event) {
            document.querySelectorAll('.modal').forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }
    </script>

</body>
