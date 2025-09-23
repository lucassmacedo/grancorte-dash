<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Spatie\Permission\Traits\HasRoles;

class Cliente extends Authenticatable
{
    use Sortable, HasRoles, AuthenticationLoggable, Notifiable, QueryCacheable;

    public $sortableAs = [
        'score'
    ];
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
        'acesso_gruppo_clientes' => 'array',
    ];

    /**
     * Invalidate the cache automatically
     * upon update in the database.
     *
     * @var bool
     */
    protected static $flushCacheOnUpdate = true;


    // create situacao A = ativo, I = inativo, B = bloqueado
    public static $situation = [
        'A' => 'Ativo',
        'I' => 'Inativo',
    ];


    public function getStatusAttribute()
    {
        return self::$situation[$this->cod_situacao];
    }

    public function getStatusColorAttribute()
    {
        switch ($this->cod_situacao) {
            case 'A':
                return 'success';
                break;
            case 'I':
                return 'danger';
                break;
            case 'B':
                return 'secondary';
                break;
        }
    }

    public function linhas()
    {
        return $this->hasMany(ClienteLinhas::class, 'cliente_id', 'id');
    }

    public function arquivos()
    {
        return $this->hasMany(ClienteArquivo::class, 'cliente_id', 'id');
    }

    public function score()
    {
        return $this->hasMany(ClienteScore::class, 'cliente', 'codigo');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'cnpj';
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function routeNotificationForMail($notification)
    {
        return $this->email_recovery;
    }

    public function getEmailForPasswordReset()
    {
        return $this->email_recovery;
    }
}
