<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoPrecoCorte extends Model
{
    protected $guarded = [];

    protected $casts = [
        'data_entrega' => 'date'
    ];
}
