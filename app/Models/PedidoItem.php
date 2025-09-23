<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Wildside\Userstamps\Userstamps;

class PedidoItem extends Model
{
    use Userstamps, Sortable;

    protected $guarded = [];

    public $sortableAs = [
        'cliente_nome',
        'clientes.cidade'
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'codigo_produto', 'codigo');
    }

    public function produto_corte()
    {
        return $this->belongsTo(ProdutoPrecoCorte::class, 'codigo_produto', 'codigo');
    }
}
