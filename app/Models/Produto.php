<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Rennokki\QueryCache\Traits\QueryCacheable;

class Produto extends Model
{
    use QueryCacheable;

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
    public $cacheTags = ['produtos'];

    /**
     * A cache prefix string that will be prefixed
     * on each cache key generation.
     *
     * @var string
     */
    public $cachePrefix = 'produtos_';

    /**
     * Invalidate the cache automatically
     * upon update in the database.
     *
     * @var bool
     */
    protected static $flushCacheOnUpdate = true;

    protected $guarded = [];


    public static $cod_local = [1 => "1 - Frigorífico", 15 => '15 - Penha', 80 => "80 - Taff"];

    public static $cod_filial   = [
        '10101'  => 'GRAN CORTE - INDUSTRIA',
        '20101' => 'FRISS - INDUSTRIA',
        '40101' => 'GRAN CORTE - ITAPETININGA',
    ];
    public static $conservacoes = [
        'Congelado' => 'Congelado',
        'Ambiente'  => 'Ambiente',
        'Resfriado' => 'Resfriado'
    ];

    public function files()
    {
        return $this->hasMany(ProdutoFile::class, 'codigo', 'codigo');
    }

    public function precos()
    {
        return $this->hasMany(ProdutoPreco::class, 'codigo', 'codigo');
    }

    public function preco()
    {
        return $this->hasOne(ProdutoPreco::class, 'codigo', 'codigo');
    }

    public static function listLocais($roleFilial = false)
    {
        $locais = self::$cod_local;
        if ($roleFilial && auth()->user()->cod_local) {
            $locais = array_intersect_key($locais, array_flip(auth()->user()->cod_local));
        }

        return $locais;
    }

    public static function listGrupos()
    {
        return Cache::remember('produtos_grupos', 3600, function () {
            return Produto::selectRaw("distinct cod_grupo,desc_grupo")
                ->orderBy('cod_grupo')
                ->get()
                ->pluck('desc_grupo', 'cod_grupo')
                ->toArray();
        });
    }

}
