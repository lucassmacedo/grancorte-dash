<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Kyslik\ColumnSortable\Sortable;

class LogisticaEntrega extends Authenticatable
{
    use Sortable;

    protected $guarded = [];
    protected $table   = 'logistica_entregas';

    protected $casts = [
        'data_nota'                   => 'date',
        'data_entrega'                => 'date',
        'inicio_entrega'              => 'datetime',
        'problemas_entrega'           => 'array',
        'problemas_entrega_resolucao' => 'array'
    ];

    public static $status = [
        0 => 'Em Rota',
        1 => 'Aguardando Entrega',
        2 => 'Descarregando',
        3 => 'Entregue',
        4 => "Problemas"
    ];

    public static $status_color     = [
        0 => 'info',
        1 => 'primary',
        2 => 'dark',
        3 => 'success',
        4 => "danger"
    ];
    public static $status_color_hex = [
        0 => '#7239ea',
        1 => '#007bff',
        2 => '#888888',
        3 => '#28a745',
        4 => "#dc3545"
    ];
}
