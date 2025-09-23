<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class ClienteObservacoes extends Model
{
    use HasFactory, SoftDeletes;
    use Userstamps;

    protected $guarded = [];

    public static $tipos = [
        1 => 'NAO ATENDEU',
        2 => 'PEDIDO REALIZADO',
        3 => 'VAI ENTRAR EM CONTATO COM O VENDEDOR ANTERIOR',
        4 => 'ESTA COMPRANDO DE OUTRO FORNCEDOR',
        5 => 'LIGAR EM OUTRA OPORTUNIDADE',
        6 => 'ENCONTROU O PRODUTO MAIS BARATO',
        7 => 'NAO TEM INTERESSE',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
