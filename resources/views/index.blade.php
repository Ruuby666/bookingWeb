<!DOCTYPE html>
<html>

<head>
    <title>BookingOcra</title>
</head>
<link rel="stylesheet" href="{{ asset('css/app.css') }}">

<body>

    <h1>Users</h1>
    <ul>
        @foreach($users as $user)
        <li>{{ $user['name'] }}</li>
        @endforeach
    </ul>

    <h1>Properties</h1>
    <ul>
        @foreach($properties as $property)
        <li>{{ $property['title'] }}</li>
        <img src="{{$property['image_url']}}" alt="Image not found">
        @endforeach
    </ul>

    <h1>Reservations</h1>
    <ul>
        @foreach($reservations as $reservation)
        <li>{{ $reservation['user_id']}} {{$reservation['property_id'] }} From: {{$reservation['check_in'] }} To: {{$reservation['check_out'] }}</li>
        @endforeach
    </ul>

    <h1>Map</h1>
    <div id="map"></div>

    <!-- Google Maps JavaScript API -->
    <script>
        (g => {
            var h, a, k, p = "The Google Maps JavaScript API",
                c = "google",
                l = "importLibrary",
                q = "__ib__",
                m = document,
                b = window;
            b = b[c] || (b[c] = {});
            var d = b.maps || (b.maps = {}),
                r = new Set,
                e = new URLSearchParams,
                u = () => h || (h = new Promise(async (f, n) => {
                    await (a = m.createElement("script"));
                    e.set("libraries", [...r] + "");
                    for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]);
                    e.set("callback", c + ".maps." + q);
                    a.src = `https://maps.${c}apis.com/maps/api/js?` + e;
                    d[q] = f;
                    a.onerror = () => h = n(Error(p + " could not load."));
                    a.nonce = m.querySelector("script[nonce]")?.nonce || "";
                    m.head.append(a)
                }));
            d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n))
        })({
            key: "no",
            v: "weekly",
        });
    </script>
</body>

</html>
