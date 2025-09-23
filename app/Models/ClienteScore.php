<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClienteScore extends Model
{
    use HasFactory;

    protected $guarded = [];
//A - Excelente:
//B - Muito Bom:
//C - Bom:
//D - Regular:
//E - Alto Risco:
    public static $categorias = [
        'A' => 'Excelente',
        'B' => 'Muito Bom',
        'C' => 'Bom',
        'D' => 'Regular',
        'E' => 'Alto Risco',
    ];
    public static $cores      = [
        'A' => '#10b759',
        'B' => '#0acf97',
        'C' => '#ffbc00',
        'D' => '#fa5c7c',
        'E' => '#ff5b5b',
    ];
}
