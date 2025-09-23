<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoEndereco extends Model
{
    use HasFactory;

    protected $table = 'pedidos_endereco';

    protected $fillable = [
        'pedido_id',
        'cep',
        'endereco',
        'numero',
        'bairro',
        'cidade',
        'uf',
        'city_id',
        'complemento',
        'latitude',
        'longitude'
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}
