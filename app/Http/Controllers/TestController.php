<?php

namespace App\Http\Controllers;

use App\Models\Suscriptor;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        Suscriptor::create([
            'expte_nro' => "1",
            'suscriptor_original' => "1",
            'dni' => "1",
            'orden' => 1,
            'paginas' => 1,
            'numero_if' => "1",
        ]);			

        return 1;
    }
}
