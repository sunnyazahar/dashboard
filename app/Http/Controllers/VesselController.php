<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\CustomerVessel;

class VesselController extends Controller
{
    public function index()
    {
        $vessels = CustomerVessel::with('customer')->get();
        $vesselTypes = CustomerVessel::whereNotNull('vessel_type_alias')->distinct()->pluck('vessel_type_alias');
        return view('Vessels.index', compact('vessels', 'vesselTypes'));
    }
}
