<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClienteGrupoScore extends Model
{
    use HasFactory;

    protected $table = 'cliente_grupo_scores';
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
}
