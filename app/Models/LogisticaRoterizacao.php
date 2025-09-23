<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class LogisticaRoterizacao extends Model
{
    use HasFactory, Sortable;

    protected $table   = 'logistica_roterizacaos';
    protected $guarded = [];
    const STATUS_PENDENTE  = 0;
    const STATUS_ANDAMENTO = 1;
    const STATUS_INTEGRADO = 2;

    public $sortableAs = [
        'armazem',
        'caminhao',
        'entregas',
        'max_entregas'
    ];
    protected $casts = [
        'options'          => 'array',
        'tipo_conservacao' => 'array',
        'pedagios'         => 'array',
        'rotas'            => 'array',
        'coordenadas'      => 'array',
        'data_entrega'     => 'date',
    ];

    public static $status       = [
        0 => 'Pendente',
        1 => 'Em andamento',
        2 => 'Integrado'
    ];
    public static $tipo_frete   = ['C' => 'CIF', 'F' => 'FOB', 'T' => 'Terceiros', 'S' => 'Sem Frete'];
    public static $status_color = [
        0 => 'secondary',
        1 => 'primary',
        2 => 'success'
    ];

    public function pedidos()
    {
        return $this->hasMany(LogisticaRoterizacaoPedido::class, 'roterizacao_id');
    }

    public function veiculo()
    {
        return $this->hasOne(LogisticaCaminhao::class, 'id', 'caminhao_id');
    }

    public function armazem()
    {
        return $this->hasOne(LogisticaArmazem::class, 'id', 'armazem_id');
    }

    public function rateios()
    {
        return $this->hasMany(VLogisticaRoterizacaoRateio::class, 'roterizacao_id');
    }

//    protected static function booted()
//    {
//        static::creating(function ($model) {
//            $model->km_aproximado = 0;
//        });
//    }
}
