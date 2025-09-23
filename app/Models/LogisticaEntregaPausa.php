<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogisticaEntregaPausa extends Model
{
    use HasFactory;

    protected $fillable = [
        'carga',
        'placa',
        'tipo',
        'descricao',
        'hora_inicio',
        'hora_fim',
        'latitude_inicio',
        'longitude_inicio',
        'latitude_fim',
        'longitude_fim'
    ];

    protected $casts = [
        'hora_inicio' => 'datetime',
        'hora_fim'    => 'datetime',
    ];


    public static $tipos = [
        'refeicao' => 'Refeição',
        'banheiro' => 'Banheiro',
        'mecanico' => 'Problemas Mecânicos',
        'outro'    => 'Outro motivo'
    ];
//    public function motorista()
//    {
//        return $this->belongsTo(User::class, 'motorista_id');
//    }
}
