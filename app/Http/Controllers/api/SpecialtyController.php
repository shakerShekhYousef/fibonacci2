<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Specialty;

class SpecialtyController extends Controller
{
    /**
     * get list of specialties
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        $specialties = Specialty::all();

        return success_response($specialties);
    }
}
