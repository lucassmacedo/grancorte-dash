<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class LogisticaCaminhao extends Model
{
    use SoftDeletes, Sortable;

    protected $guarded = [];
    protected $table   = 'logistica_caminhoes';

    protected $casts = [
        'localizacao_retorno' => 'array',
        'tipo_conservacao'    => 'array'
    ];

    protected function cpfResponsavel(): Attribute
    {
        return Attribute::make(
            set: fn(string $value): string => preg_replace('/[^0-9]/', '', $value),
        );
    }

    public static $tipo = ['toco' => 'Toco', 'carreta' => 'Carreta', 'bitruck' => 'Bitruck', '3/4' => '3/4', 'truck' => 'Truck', 'van' => 'Van', 'outros' => 'Outros'];

    public function scopeListar($query)
    {
        return $query->selectRaw("placa || ' - ' || responsavel || ' - ' || marca || ' - ' || modelo as placa, id")->pluck('placa', 'id');
    }

    public function transportadora()
    {
        return $this->belongsTo(LogisticaTransportadora::class, 'transportadora_id');
    }


}
