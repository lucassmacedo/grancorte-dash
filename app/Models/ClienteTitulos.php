<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class ClienteTitulos extends Model
{
    use Sortable, SoftDeletes;

    protected $guarded    = [];
    public    $sortableAs = ['status_order','valor_total','valor_medio','titulos','clientes','cod_vendedor'];

    protected $table = 'cliente_titulos';

    public static $status_color = [
        'ABERTO'    => 'warning',
        'LIQUIDADO' => 'success',
        'VENCIDO'   => 'danger',
    ];
    public static $status = [
        'ABERTO'    => 'Aberto',
        'LIQUIDADO' => 'Liquidado',
        'VENCIDO'   => 'Vencido',
    ];

    protected $casts = [
        'data_emissao'    => 'date',
        'data_vencimento' => 'date',
        'data_baixa'      => 'date',
    ];
}
