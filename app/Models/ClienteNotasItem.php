<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Kyslik\ColumnSortable\Sortable;

class ClienteNotasItem extends Model
{
    use HasFactory, Sortable;

    protected $guarded = [];


    public function ValorLiquidoSortable($query, $direction)
    {
        return $query->orderBy('valor_liquido', $direction);
    }

    public function ValorMedioSortable($query, $direction)
    {
        return $query->orderBy('valor_medio', $direction);
    }

    public function QtdPriMediaSortable($query, $direction)
    {
        return $query->orderBy('qtd_pri_media', $direction);
    }

    public function NotasSortable($query, $direction)
    {
        return $query->orderBy('notas', $direction);
    }

    public function ClientesSortable($query, $direction)
    {
        return $query->orderBy('clientes', $direction);
    }

    public function CodGrupoSortable($query, $direction)
    {
        return $query->orderBy('cod_grupo', $direction);
    }

}
