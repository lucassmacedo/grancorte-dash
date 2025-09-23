<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rennokki\QueryCache\Traits\QueryCacheable;

class ClienteTitulo extends Model
{
//    use SoftDeletes;
//    use QueryCacheable;

    /**
     * Specify the amount of time to cache queries.
     * Do not specify or set it to null to disable caching.
     *
     * @var int|\DateTime
     */
    public $cacheFor = 3600;

    protected $guarded = [];

    /**
     * The tags for the query cache. Can be useful
     * if flushing cache for specific tags only.
     *
     * @var null|array
     */
    public $cacheTags = ['nfe_title'];

    /**
     * A cache prefix string that will be prefixed
     * on each cache key generation.
     *
     * @var string
     */
    public $cachePrefix = 'cliente_title_';

//    protected $connection = 'protheus';

//    protected $table = 'VW_PDV_CLIENTE_TITULOS';

    public static $status_color = [
        'ABERTO'    => 'warning',
        'LIQUIDADO' => 'success',
        'VENCIDO'   => 'danger',
    ];

    protected $casts = [
        'data_emissao'    => 'date',
        'data_vencimento' => 'date',
        'data_baixa'      => 'date',
    ];
}

