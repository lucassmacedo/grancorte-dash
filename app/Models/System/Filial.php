<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filial extends Model
{
    protected $connection = 'protheus';

    protected $table = 'VW_PDV_CLIENTE_FILIAL';
}
