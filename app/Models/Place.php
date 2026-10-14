<?php

namespace App\Models;

use Laraversine\Destination;

class Place extends Destination
{
    protected $table = 'places';

    protected $fillable = ['name', 'category', 'latitude', 'longitude'];
}
