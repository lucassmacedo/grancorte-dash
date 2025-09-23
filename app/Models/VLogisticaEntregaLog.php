<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Kyslik\ColumnSortable\Sortable;

class VLogisticaEntregaLog extends Authenticatable
{
    use Sortable;
    protected $guarded = [];

    protected $table   = 'vw_logistica_timeline';

    protected $casts = [
        'data_evento' => 'datetime',
    ];
}
