<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class LogisticaTransportadora extends Model
{
    use HasFactory, Sortable;

    protected $table   = 'logistica_transportadoras';
    protected $guarded = [];

    public static $pagamentoPor = [
        1 => 'KG Líquido',
        2 => 'KG Bruto',
        3 => 'KM',
        4 => "Saída"
    ];


    public function setCepAttribute($value)
    {
        $this->attributes['cep'] = preg_replace('/[^0-9]/', '', $value);
    }

    protected function cnpj(): Attribute
    {
        return Attribute::make(
            set: fn(string $value): string => preg_replace('/[^0-9]/', '', $value),
        );
    }

    public function locais()
    {
        return $this->hasMany(LogisticaTransportadoraLocal::class, 'transportadora_id');
    }


    public function scopeList($query)
    {
        return $query->selectRaw("cnpj||' - '||razao_social as nome, id")
            ->where("status", true)
            ->get()
            ->pluck('nome', 'id');
    }
}
