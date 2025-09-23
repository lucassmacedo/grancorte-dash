<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClienteProspect extends Model
{
    protected $connection = 'protheus';
    protected $guarded    = [];
    protected $table      = 'SUS010';
    public    $timestamps = false;
}
