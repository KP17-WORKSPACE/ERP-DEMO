<?php

namespace App\Http\Controllers;

use App\SysCities;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    //
    public function getCities($countryId)
    {
        $cities = SysCities::where('country_id', $countryId)
            ->orderBy('name')
            ->get(['id', 'name']); // return only id and name

        return response()->json($cities);
    }
}
