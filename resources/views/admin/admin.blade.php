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

        <!-- Display scope toggle buttons only for super admins -->
        @if(Auth::user()->is_super_admin)
        <div class="scope-toggle">
            <a href="{{ route('admin.properties', ['scope' => 'mine']) }}"
                class="toggle-btn {{ $scope === 'mine' ? 'active' : '' }}">
                Mine
            </a>
            <a href="{{ route('admin.properties', ['scope' => 'all']) }}"
                class="toggle-btn {{ $scope === 'all' ? 'active' : '' }}">
                All
            </a>
        </div>
        @endif

        <div class="table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Location</th>
                        <th>Price</th>
                        <th>Capacity</th>
                        <th>Features</th>
                        @if($scope === 'all')
                        <th>Owner</th>
                        @endif
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($properties as $property)
                    <tr>
                        <td>{{ $property->title }}
                            <button
                                onclick="openPropertyModal(this)"
                                data-title="{{ $property->title }}"
                                data-description="{{ $property->description }}"
                                data-location="{{ $property->location }}"
                                data-price="{{ $property->price_per_night }}"
                                data-capacity="{{ $property->capacity }}"
                                data-size="{{ $property->size }}"
                                data-bedrooms="{{ $property->bedrooms }}"
                                data-bathrooms="{{ $property->bathrooms }}"
                                data-wifi="{{ $property->wifi ? 'Sí' : 'No' }}"
                                data-tv="{{ $property->tv ? 'Sí' : 'No' }}"
                                data-pool="{{ $property->pool ? 'Sí' : 'No' }}"
                                data-garden="{{ $property->garden ? 'Sí' : 'No' }}"
                                data-parking="{{ $property->parking ? 'Sí' : 'No' }}"
                                data-terrace="{{ $property->terrace ? 'Sí' : 'No' }}"
                                data-safebox="{{ $property->safeBox ? 'Sí' : 'No' }}"
                                data-entertainment="{{ $property->entertainment }}"
                                data-lat="{{ $property->lat }}"
                                data-lng="{{ $property->lng }}">
                                <b>ⓘ</b>
                            </button>
                        </td>
                        <td>{{ $property->location }}</td>
                        <td>Around {{ $property->price_per_night }}€</td>
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

                        @if($scope === 'all')
                        <td>
                            @if($property->owner)
                            {{ $property->owner->name }}
                            <button onclick="openOwnerModal('{{ $property->owner->id }}', '{{ addslashes($property->owner->name) }}', '{{ addslashes($property->owner->email) }}', '{{ addslashes($property->owner->phone_number ?? 'N/A') }}')">
                                <b>ⓘ</b>
                            </button>
                            @else
                            N/A
                            @endif
                        </td>
                        @endif

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
                    @empty
                    <tr>
                        <td colspan="{{ $scope === 'all' ? '7' : '6' }}">No properties available.</td>
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

    <div id="ownerModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeOwnerModal()">&times;</span>
            <h2>Owner Info</h2>
            <ul>
                <li><strong>ID:</strong> <span id="ownerModalId"></span></li>
                <li><strong>Name:</strong> <span id="ownerModalName"></span></li>
                <li><strong>Email:</strong> <span id="ownerModalEmail"></span></li>
                <li><strong>Phone:</strong> <span id="ownerModalPhone"></span></li>
            </ul>
        </div>
    </div>

    <div id="propertyModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closePropertyModal()">&times;</span>

            <h2 id="modalTitle"></h2>

            <ul>
                <li><strong>Descripción:</strong> <span id="modalDescription"></span></li>
                <li><strong>Ubicación:</strong> <span id="modalLocation"></span></li>
                <li><strong>Precio:</strong> €<span id="modalPrice"></span></li>
                <li><strong>Capacidad:</strong> <span id="modalCapacity"></span></li>
                <li><strong>Metros cuadrados:</strong> <span id="modalSize"></span></li>
                <li><strong>Dormitorios:</strong> <span id="modalBedrooms"></span></li>
                <li><strong>Baños:</strong> <span id="modalBathrooms"></span></li>
                <li><strong>Wi-Fi:</strong> <span id="modalWifi"></span></li>
                <li><strong>TV:</strong> <span id="modalTv"></span></li>
                <li><strong>Piscina:</strong> <span id="modalPool"></span></li>
                <li><strong>Jardín:</strong> <span id="modalGarden"></span></li>
                <li><strong>Parking:</strong> <span id="modalParking"></span></li>
                <li><strong>Terraza:</strong> <span id="modalTerrace"></span></li>
                <li><strong>Caja fuerte:</strong> <span id="modalSafebox"></span></li>
                <li><strong>Entretenimiento:</strong> <span id="modalEntertainment"></span></li>
                <li><strong>Latitud:</strong> <span id="modalLat"></span></li>
                <li><strong>Longitud:</strong> <span id="modalLng"></span></li>
            </ul>
        </div>
    </div>

    @include('components.footer')
    <script>
        function openPropertyModal(button) {
            document.getElementById('modalTitle').textContent = button.dataset.title;
            document.getElementById('modalDescription').textContent = button.dataset.description;
            document.getElementById('modalLocation').textContent = button.dataset.location;
            document.getElementById('modalPrice').textContent = button.dataset.price;
            document.getElementById('modalCapacity').textContent = button.dataset.capacity;
            document.getElementById('modalSize').textContent = button.dataset.size;
            document.getElementById('modalBedrooms').textContent = button.dataset.bedrooms;
            document.getElementById('modalBathrooms').textContent = button.dataset.bathrooms;
            document.getElementById('modalWifi').textContent = button.dataset.wifi;
            document.getElementById('modalTv').textContent = button.dataset.tv;
            document.getElementById('modalPool').textContent = button.dataset.pool;
            document.getElementById('modalGarden').textContent = button.dataset.garden;
            document.getElementById('modalParking').textContent = button.dataset.parking;
            document.getElementById('modalTerrace').textContent = button.dataset.terrace;
            document.getElementById('modalSafebox').textContent = button.dataset.safebox;
            document.getElementById('modalEntertainment').textContent = button.dataset.entertainment;
            document.getElementById('modalLat').textContent = button.dataset.lat;
            document.getElementById('modalLng').textContent = button.dataset.lng;

            document.getElementById('propertyModal').style.display = 'flex';
        }

        function closePropertyModal() {
            document.getElementById('propertyModal').style.display = 'none';
        }

        // Close the modal when clicking outside of it
        window.onclick = function(event) {
            document.querySelectorAll('.modal').forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }

        function openOwnerModal(id, name, email, phone) {
            document.getElementById('ownerModalId').textContent = id;
            document.getElementById('ownerModalName').textContent = name;
            document.getElementById('ownerModalEmail').textContent = email;
            document.getElementById('ownerModalPhone').textContent = phone;
            document.getElementById('ownerModal').style.display = 'flex';
        }

        function closeOwnerModal() {
            document.getElementById('ownerModal').style.display = 'none';
        }
    </script>

</body>

</html>