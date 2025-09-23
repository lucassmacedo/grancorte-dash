<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class DashboardRecebimento extends Model
{


    protected $table = 'dashboard_recebimento';

    protected $guarded = [];
}
