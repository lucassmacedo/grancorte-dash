<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class ClienteNotas extends Model
{
    use HasFactory, Sortable;

    public $sortable   = ['valor_liquido'];
    public $sortableAs = ['data_mvto', 'num_docto', 'cod_saida'];


    protected $casts = [
        'vendedores'  => 'array',
        'data_pedido' => 'date',
    ];

    protected $guarded = [];

    public function itens()
    {
        return $this->hasMany(ClienteNotasItem::class, 'id_nota', 'id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cod_cli_for', 'codigo');
    }

    public function itens_pedidos()
    {
        return $this->hasMany(PedidoFaturadoItem::class, 'id_nota', 'id');
    }

    public function ValorLiquidoSortable($query, $direction)
    {
        return $query->orderBy('valor_liquido', $direction);
    }

    public function ValorMedioSortable($query, $direction)
    {
        return $query->orderBy('valor_medio', $direction);
    }

    public function NotasSortable($query, $direction)
    {
        return $query->orderBy('notas', $direction);
    }

    public function ClientesSortable($query, $direction)
    {
        return $query->orderBy('clientes', $direction);
    }

    public function ProdutosSortable($query, $direction)
    {
        return $query->orderBy('produtos', $direction);
    }


}
