<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class LogisticaArmazem extends Model
{
    use HasFactory, Sortable;

    protected $table   = 'logistica_armazens';
    protected $guarded = [];


    // set cep remove mask
    protected function cep(): Attribute
    {
        return Attribute::make(
            set: fn($value) => preg_replace("/[^0-9]/", "", $value),
        );
    }

    public static function lista()
    {
        return self::where('status', true)->pluck('nome', 'id');
    }

}
