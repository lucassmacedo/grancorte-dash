<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoterizacaoCargaItem extends Model
{
    protected $connection = 'protheus';

    protected $primaryKey = 'ZF8_PEDIDO';
    protected $guarded    = [];
    protected $table      = 'ZF8010';

    public $timestamps = false;
}
