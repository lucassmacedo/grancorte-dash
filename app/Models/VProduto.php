<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class VProduto extends Model
{
//    use QueryCacheable;

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
    public $cacheTags = ['v_produtos'];

    /**
     * A cache prefix string that will be prefixed
     * on each cache key generation.
     *
     * @var string
     */
    public $cachePrefix = 'v_produtos_';

    /**
     * Invalidate the cache automatically
     * upon update in the database.
     *
     * @var bool
     */
    protected static $flushCacheOnUpdate = true;

    protected $guarded = [];

    protected $table   = 'v_produtos';

    public static $cod_local = [1 => "1 - Frigorífico", 80 => "80 - Entreposto"];

    public static $cod_filial = [
        100 => '100 - Gran Corte',
        200 => '200 - Friss',
        202 => '202 - Distribuição',
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

}
