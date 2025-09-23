<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogisticaRoterizacaoRateio extends Model
{
    protected $guarded = [];

    public function roterizacao()
    {
        return $this->belongsTo(LogisticaRoterizacao::class, 'roterizacao_id');
    }
}
