<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laraversine demo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
    <style>
        :root { --bg:#f6f7f9; --card:#fff; --ink:#15171c; --muted:#6b7280; --accent:#e5484d; --line:#e5e7eb; }
        @media (prefers-color-scheme: dark) {
            :root { --bg:#0f1115; --card:#181b22; --ink:#eef0f4; --muted:#9aa1ad; --line:#272b35; }
        }
        * { box-sizing: border-box; }
        body { margin:0; background:var(--bg); color:var(--ink); font:16px/1.5 system-ui,-apple-system,sans-serif; }
        main { max-width:720px; margin:0 auto; padding:32px 16px 64px; }
        h1 { margin:0 0 4px; font-size:28px; }
        .sub { color:var(--muted); margin:0 0 24px; }
        code { background:var(--card); border:1px solid var(--line); border-radius:6px; padding:1px 6px; font-size:.9em; }
        form { background:var(--card); border:1px solid var(--line); border-radius:12px; padding:16px;
               display:grid; gap:16px; grid-template-columns:1fr 1fr; margin-bottom:16px; }
        label { display:grid; gap:6px; font-size:13px; color:var(--muted); }
        select, input[type=range] { width:100%; font:inherit; color:var(--ink); }
        select { padding:8px; border-radius:8px; border:1px solid var(--line); background:var(--bg); }
        .full { grid-column:1 / -1; }
        .toggle { display:flex; gap:8px; }
        .toggle label { flex:1; color:var(--ink); display:flex; gap:6px; align-items:center;
                        padding:8px; border:1px solid var(--line); border-radius:8px; }
        .count { color:var(--muted); font-size:14px; margin:0 0 12px; }
        ul { list-style:none; margin:0; padding:0; display:grid; gap:8px; }
        li { background:var(--card); border:1px solid var(--line); border-radius:12px; padding:12px 16px; }
        .row { display:flex; justify-content:space-between; gap:12px; align-items:baseline; }
        .name { font-weight:600; }
        .cat { color:var(--muted); font-size:13px; margin-left:8px; }
        .dist { font-variant-numeric:tabular-nums; font-weight:600; }
        .bar { height:4px; border-radius:2px; background:var(--line); margin-top:8px; overflow:hidden; }
        .bar span { display:block; height:100%; background:var(--accent); }
        .empty { text-align:center; color:var(--muted); padding:32px; }
        #map { height:360px; border-radius:12px; border:1px solid var(--line); margin-bottom:16px; z-index:0; }
        pre { background:var(--card); border:1px solid var(--line); border-radius:12px; padding:16px;
              overflow-x:auto; font-size:13px; margin-top:24px; }
    </style>
</head>
<body>
<main>
    <h1>📍 Laraversine</h1>
    <p class="sub">Nearest-location search with Eloquent. Haversine in SQL, no PostGIS.</p>

    <form method="get" id="filters">
        <label>I am in
            <select name="origin" onchange="this.form.submit()">
                @foreach ($origins as $key => [$label])
                    <option value="{{ $key }}" @selected($key === $origin)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label>Category
            <select name="category" onchange="this.form.submit()">
                <option value="">All</option>
                @foreach ($categories as $c)
                    <option value="{{ $c }}" @selected($c === $category)>{{ ucfirst($c) }}</option>
                @endforeach
            </select>
        </label>
        <label class="full">Within {{ rtrim(rtrim(number_format($radius, 1), '0'), '.') }} {{ $unit === 'mile' ? 'miles' : 'km' }}
            <input type="range" name="radius" min="1" max="50" step="1" value="{{ $radius }}"
                   onchange="this.form.submit()" oninput="this.parentElement.firstChild.textContent = 'Within ' + this.value + ' {{ $unit === 'mile' ? 'miles' : 'km' }}'">
        </label>
        <div class="toggle full">
            <label><input type="radio" name="unit" value="km" @checked($unit === 'km') onchange="this.form.submit()"> Kilometres</label>
            <label><input type="radio" name="unit" value="mile" @checked($unit === 'mile') onchange="this.form.submit()"> Miles</label>
        </div>
    </form>

    <div id="map"></div>

    <p class="count">{{ $places->count() }} of {{ $total }} places, nearest first</p>

    @if ($places->isEmpty())
        <div class="empty">Nothing in range. Widen the radius.</div>
    @else
        <ul>
            @foreach ($places as $place)
                <li>
                    <div class="row">
                        <div><span class="name">{{ $place->name }}</span><span class="cat">{{ $place->category }}</span></div>
                        <span class="dist">{{ $place->distance() }}</span>
                    </div>
                    <div class="bar"><span style="width: {{ min(100, $place->distance()->value / $radius * 100) }}%"></span></div>
                </li>
            @endforeach
        </ul>
    @endif

<pre>Place::nearby([{{ implode(', ', $origins[$origin][1]) }}])
    ->within({{ (int) $radius }})
    ->nearestFirst()
    ->get();</pre>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script>
    const origin = @json($origins[$origin][1]);
    const radiusMeters = {{ $radius }} * {{ $unit === 'mile' ? 1609.344 : 1000 }};
    const places = @json($markers);

    const map = L.map('map');
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    const circle = L.circle(origin, {
        radius: radiusMeters, color: '#e5484d', weight: 2, fillOpacity: 0.08,
    }).addTo(map);

    L.marker(origin).addTo(map).bindPopup('You are here');

    places.forEach((p, i) => {
        L.circleMarker(p.position, {
            radius: 7, color: '#fff', weight: 2, fillColor: '#e5484d', fillOpacity: 1,
        }).addTo(map).bindPopup(`<strong>${p.name}</strong><br>${p.category} &middot; ${p.distance}`);
    });

    map.fitBounds(circle.getBounds(), { padding: [16, 16] });
</script>
</body>
</html>
