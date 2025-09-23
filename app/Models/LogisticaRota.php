<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Rennokki\QueryCache\Traits\QueryCacheable;

class LogisticaRota extends Model
{
    use SoftDeletes, QueryCacheable, Sortable;

    protected $guarded = [];

    /**
     * Specify the amount of time to cache queries.
     * Do not specify or set it to null to disable caching.
     *
     * @var int|\DateTime
     */
    public $cacheFor = 3600;

    /**
     * The tags for the query cache. Can be useful
     * if flushing cache for specific tags only.
     *
     * @var null|array
     */
    public $cacheTags = ['rotas'];

    /**
     * A cache prefix string that will be prefixed
     * on each cache key generation.
     *
     * @var string
     */
    public $cachePrefix = 'rotas_';

    public static function listar()
    {
        return self::selectRaw("codigo || ' - ' || nome as nome, id")
            ->where('status', true)
            ->orderBy('codigo')
            ->get()
            ->pluck('nome', 'id');
    }

    public static function listarByPermissao()
    {
        return
            [0 => '0 - Clientes sem rota'] +
            self::selectRaw("codigo || ' - ' || nome as nome, id")
                ->when(auth()->user()->hasRole('vendedor'), function ($query) {
                    return $query->whereIn('id', function ($query) {
                        $query->selectRaw('distinct rota_id')->from('clientes')->where('cod_vendedor', auth()->user()->codigo);
                    });
                })->where('status', true)->orderBy('codigo')->get()->pluck('nome', 'id')->toArray();
    }
}
