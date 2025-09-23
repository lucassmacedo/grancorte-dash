<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\Userstamps;

class ProdutoFile extends Model
{
    use HasFactory;
    use Userstamps;

    protected $guarded = [];

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'codigo', 'codigo');
    }
}
