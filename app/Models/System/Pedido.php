<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $connection = 'protheus';

    protected $guarded = [];
    protected $table   = 'ZE4010';

    public $timestamps = false;
}
