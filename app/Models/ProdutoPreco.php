<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Kyslik\ColumnSortable\Sortable;

class ProdutoPreco extends Model
{
    use Sortable;
    public $sortableAs = [
        'descricao'
    ];
    protected $guarded = [];

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'codigo', 'codigo');
    }

    public function lista()
    {
        return $this->belongsTo(ProdutoPrecoLista::class, 'cod_lista', 'codigo');
    }

    public function scopeListaFiltrada($query, $cod_filial, $cod_local, $cod_lista, $perc_desconto = null)
    {
        $data_produtos = ProdutoPreco::selectRaw("
                produto_precos.id,
                produto_precos.codigo,
                produto_precos.preco,
                produto_precos.preco_minimo,
               case
                   when ((filiais.locais ->> produto_precos.cod_local::varchar)::jsonb ->> 'trava_estoque')::boolean
                       then
                       case
                           when pedido_items.quantidade is not null
                               then greatest(0, saldo_aux - pedido_items.quantidade)
                           else
                               saldo_aux::integer
                           end
                   end
                                                    as saldo_aux,
                coalesce(pedido_items.quantidade, 0) as qtd_vendas_hoje
            ")
            ->with('produto')
            ->whereHas('produto', function ($query) {
                $query->where('status', 1);
            })
            ->join('filiais', 'filiais.codigo', 'produto_precos.cod_filial')
            ->leftJoin(DB::raw("(select sum(quantidade) as quantidade, codigo_produto
                    from pedido_items
                    group by codigo_produto) as pedido_items"), function ($join) {
                $join->on('produto_precos.codigo', 'pedido_items.codigo_produto');
            })
            ->where([
                'cod_filial' => $cod_filial,
                'cod_local'  => $cod_local,
                "cod_lista"  => $cod_lista
            ])
            ->where('preco', '>', 0)
            ->get();

        $produtos['data'] = $data_produtos->mapWithKeys(function ($produto, $key) {
            return [
                $produto->codigo => sprintf(
                    "%s - %s (%s)",
                    $produto->codigo,
                    $produto->produto->descricao,
                    $produto->produto->cod_unidade_vda
                )
            ];
        })->toArray();


        $produtos['attributes'] = $data_produtos->mapWithKeys(function ($produto) use ($perc_desconto) {
            $preco        = $perc_desconto ? $produto->preco : round(($produto->preco * ($perc_desconto / 100)) + $produto->preco, 2);
            $preco_minimo = $perc_desconto ? $produto->preco_minimo : round(($produto->preco * ($perc_desconto / 100)) + $produto->preco_minimo, 2);

            return [
                $produto->codigo => [
                    'data-id'           => $produto->id,
                    'data-preco'        => $preco,
                    'data-preco_minimo' => $preco_minimo,
                    'data-tolerancia'    => $produto->produto->tolerancia_preco_pedido,
                    'data-peso-medio'   => $produto->produto->peso_medio,
                    'data-venda-par'    => $produto->produto->venda_por_par ? 1 : 0,
                    'data-saldo'        => $produto->saldo_aux,
                    'data-peso-padrao'  => $produto->produto->peso_padrao ? 1 : 0,
                    'data-conservacao'  => $produto->produto->conservacao,
                ]
            ];
        })->toArray();

        return $produtos;
    }
}
