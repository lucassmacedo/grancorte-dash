<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Wildside\Userstamps\Userstamps;

class ClienteArquivo extends Model
{
    use HasFactory, Userstamps,Sortable;
    protected $table = 'cliente_arquivos';
    protected $guarded = [];
    public static $visibilidade = [
        0 => 'Público',
        1 => 'Privado',
    ];
}
