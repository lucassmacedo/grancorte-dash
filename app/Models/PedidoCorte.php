<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class PedidoCorte extends Model
{
    use Userstamps;

    protected $guarded = [];

    protected $casts = [
        'pedidos' => 'json',
    ];
}
