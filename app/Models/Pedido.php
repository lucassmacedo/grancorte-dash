<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Wildside\Userstamps\Userstamps;

class Pedido extends Model
{
    use Userstamps, SoftDeletes, Sortable;

    protected $guarded = [];

    public $sortableAs = [
        'cliente_nome',
        'clientes.cidade'
    ];

    const STATUS_ANALISE   = 0;
    const STATUS_BLOQUEADO = 1;
    const STATUS_BAIXADO   = 2;

    const STATUS_CORTADO   = 3;
    const STATUS_CANCELADO = 4;


    public static $status = [
        0 => 'Em Análise',
        1 => 'Bloqueado',
        2 => 'Corte Realizado',
        3 => 'Baixado',
        4 => 'Cancelado',
    ];

    public static $status_color = [
        0 => 'info',
        1 => 'warning',
        2 => 'primary',
        3 => 'success',
        4 => 'danger',
    ];

    public static $tipo_pedido  = [
        1 => 'Venda Normal',
        2 => 'Simple Remessa',
        3 => 'Bonificação',
        4 => 'Doação/Brinde'
    ];
    public static $tipo_veiculo = [
        1 => 'Veículo 1',
        2 => 'Veículo 2',
        3 => 'Veículo 3',
        4 => 'Veículo 4',
        5 => 'Veículo 5',
    ];

    public function items()
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function bloqueios()
    {
        return $this->hasMany(PedidoBloqueio::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'codigo_cliente', 'codigo');
    }

    public function roterizacao()
    {
        return $this->belongsTo(LogisticaRoterizacao::class, 'id', 'pedido_id');
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'codigo_vendedor', 'codigo');
    }

    public function endereco()
    {
        return $this->hasOne(PedidoEndereco::class);
    }

    public function getLocal()
    {
        return Produto::$cod_local[$this->cod_local];
    }

    public function getFilial()
    {
        return Filial::listFiliais()[$this->cod_filial];
    }

    public function setDataEntregaAttribute($value)
    {
        $this->attributes['data_entrega'] = date('Y-m-d', strtotime(str_replace('/', '-', $value)));
    }

    public function hasBloqueios()
    {
        return (auth()->user()->hasRole(['admin']) || (auth()->user()->libera_pedido_estourado || auth()->user()->libera_pedido_cliente_debitos || auth()->user()->libera_pedido_preco_alterado));
    }
}
