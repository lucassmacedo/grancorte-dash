<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class VLogisticaRoterizacaoRateio extends Model
{
    use Sortable;

    public $sortableAs = [
        'agrupador',
        'peso_total',
        'valor_descarga',
        'valor_pedagio',
        'valor_escolta',
        'valor_despesa_extra',
        'valor_acrescimo',
        'valor_desconto',
        'valor_total_carga'
    ];

    public static $agrupadores      = ['filial' => "Filial", "transportadora" => "Transportadora"];
    public static $agrupadores_list = ['cod_filial' => "Filial", "transportadora_id" => "Transportadora"];

    protected $guarded = [];

    protected $table = 'v_logistica_roterizacao_rateio';

    public function roterizacao()
    {
        return $this->belongsTo(LogisticaRoterizacao::class, 'roterizacao_id');
    }
}
