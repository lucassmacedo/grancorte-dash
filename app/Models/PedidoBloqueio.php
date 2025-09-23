<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoBloqueio extends Model
{
    protected $guarded = [];
    const PEDIDO_PROGRAMADO = 0;

    const LIMITE_CREDITO_EXCEDIDO = 1;
    const DEBITO_ABERTO           = 2;

    const PRECO_ALTERADO = 3;

    public static $tipos = [
        self::PEDIDO_PROGRAMADO       => 'Pedido programado',
        self::LIMITE_CREDITO_EXCEDIDO => 'Limite de crédito excedido',
        self::DEBITO_ABERTO           => 'Débito aberto',
        self::PRECO_ALTERADO          => 'Preço do produto alterado',
    ];
}
