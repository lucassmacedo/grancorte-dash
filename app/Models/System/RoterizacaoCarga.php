<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoterizacaoCarga extends Model
{
    protected $connection = 'protheus';

    protected $primaryKey = 'ZE7_CARGA';

    protected $guarded = [];

    protected $table = 'ZE7010';

    public $timestamps = false;
}
