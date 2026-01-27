<?php

namespace App\Http\Controllers;

use App\Models\Municipio;
use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Muestra la vista de test con datos de municipios y MNP
     */
    public function index()
    {
        $municipios = Municipio::with(['provincia', 'datosMnp' => fn($q) => $q->limit(5)])->limit(10)->get();

        return view('test', ['municipios' => $municipios]);
    }
}
