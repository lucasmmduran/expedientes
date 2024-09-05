<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suscriptor extends Model
{
    use HasFactory;

    protected $table = ['suscriptores'];

    protected $fillable = [
        'expte_nro',
        'suscriptor_original',
        'dni',
        'orden',
        'paginas',
        'numero_if'
    ];
}
