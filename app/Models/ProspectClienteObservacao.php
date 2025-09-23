<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\Userstamps;

class ProspectClienteObservacao extends Model
{
    use Userstamps, SoftDeletes;

    protected $guarded = [];

    protected $table = 'prospect_cliente_observacoes';
}
