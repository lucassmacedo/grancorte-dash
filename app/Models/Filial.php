<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Spatie\Permission\Traits\HasRoles;

class Filial extends Authenticatable
{
    use Sortable, HasRoles, AuthenticationLoggable, Notifiable, QueryCacheable;

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
    public $cacheTags = ['clientes'];

    /**
     * A cache prefix string that will be prefixed
     * on each cache key generation.
     *
     * @var string
     */
    public $cachePrefix = 'clientes_';

    protected $guarded = [];

    protected $casts = [
        'locais'           => 'array',
        'horarios_pedidos' => 'array',
    ];

    /**
     * Invalidate the cache automatically
     * upon update in the database.
     *
     * @var bool
     */
    protected static $flushCacheOnUpdate = true;

    protected $table = 'filiais';

    public static function listFiliais($roleFilial = false)
    {
        return self::selectRaw("codigo, nome")
            ->when($roleFilial && auth()->user()->cod_filial, function ($query, $roleFilial) {
                return $query->whereIn('codigo', auth()->user()->cod_filial);
            })
            ->orderBy('codigo')
            ->get()
            ->pluck('nome', 'codigo')
            ->toArray();
    }

    public static function listLocais()
    {
        $filiais = self::selectRaw("codigo, nome,locais")
            ->orderBy('codigo')
            ->get()
            ->toArray();

        $data = [];
        foreach ($filiais as $filial) {
            foreach (array_keys($filial['locais']) as $local) {
                $data[sprintf('%s-%s', $filial['codigo'], $local)] = sprintf('%s - %s - %s', $filial['codigo'], $filial['nome'], Produto::$cod_local[$local]);
            }
        }

        return $data;
    }

    public static function listLocaisByFilial()
    {
        $filiais = self::selectRaw("codigo, nome,locais")
            ->orderBy('codigo')
            ->get()
            ->toArray();

        $data = [];
        foreach ($filiais as $filial) {
            foreach ($filial['locais'] as $key => $local) {
                $data[] = [
                    'cod_filial'  => $filial['codigo'],
                    'filial_nome' => $filial['nome'],
                    'cod_local'   => $key,
                    'local_nome'  => Produto::$cod_local[$key],
                ];
            }
        }

        return $data;
    }

}
