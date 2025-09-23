<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoFaturadoItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cod_cliente', 'codigo');
    }
}
