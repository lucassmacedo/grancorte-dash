<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class ProdutoPrecoLista extends Model
{
    use QueryCacheable;

    protected $table = 'produto_preco_listas';

    protected $guarded = [];

    public $cacheFor = 3600;

    /**
     * The tags for the query cache. Can be useful
     * if flushing cache for specific tags only.
     *
     * @var null|array
     */
    public $cacheTags = ['produtos_precos_lista'];

    /**
     * A cache prefix string that will be prefixed
     * on each cache key generation.
     *
     * @var string
     */
    public $cachePrefix = 'produtos_precos_lista_';

    /**
     * Invalidate the cache automatically
     * upon update in the database.
     *
     * @var bool
     */
    protected static $flushCacheOnUpdate = true;

    public static function getListas()
    {
        return ProdutoPrecoLista::selectRaw("codigo, codigo || '-' || descricao as descricao")
            ->orderBy('codigo', 'asc')
            ->pluck('descricao', 'codigo')->toArray();
    }
}
