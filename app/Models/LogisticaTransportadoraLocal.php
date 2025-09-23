<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogisticaTransportadoraLocal extends Model
{
    use HasFactory;

    protected $table   = 'logistica_transportadoras_locais';
    protected $guarded = [];


    public function transportadora()
    {
        return $this->belongsTo(LogisticaTransportadora::class, 'transportadora_id');
    }

    public function setValorUnitarioAttribute($value)
    {
        $this->attributes['valor_unitario'] = (float) str_replace([','], '.', $value);
    }
}
