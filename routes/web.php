<?php

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $origins = [
        'downtown' => ['Downtown', [7.0731, 125.6128]],
        'uptown' => ['Uptown', [7.1275, 125.6370]],
        'outskirts' => ['Outskirts', [7.1902, 125.4550]],
    ];

    $origin = $request->query('origin', 'uptown');
    abort_unless(isset($origins[$origin]), 404);

    $unit = $request->query('unit') === 'mile' ? 'mile' : 'km';
    $radius = max(1, min(50, (float) $request->query('radius', 5)));
    $category = $request->query('category');

    // The unit is read from config at query time, so it can be switched per request.
    config(['laraversine.search_by' => $unit]);

    $places = Place::nearby($origins[$origin][1])
        ->within($radius)
        ->when($category, fn ($q) => $q->where('category', $category))
        ->nearestFirst()
        ->get();

    return view('welcome', [
        'places' => $places,
        'origins' => $origins,
        'origin' => $origin,
        'unit' => $unit,
        'radius' => $radius,
        'category' => $category,
        'categories' => Place::distinct()->orderBy('category')->pluck('category'),
        'total' => Place::count(),
    ]);
});
