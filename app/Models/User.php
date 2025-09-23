<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Kyslik\ColumnSortable\Sortable;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Sanctum\HasApiTokens;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Sortable, HasRoles, AuthenticationLoggable, Impersonate, QueryCacheable;

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
    public $cacheTags = ['users'];

    /**
     * A cache prefix string that will be prefixed
     * on each cache key generation.
     *
     * @var string
     */
    public $cachePrefix = 'users_';

    /**
     * Invalidate the cache automatically
     * upon update in the database.
     *
     * @var bool
     */
    protected static $flushCacheOnUpdate = true;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded    = [];
    public    $timestamps = true;
    protected $casts      = [
        'cod_filial'                    => 'json',
        'cod_local'                     => 'json',
        'cod_lista'                     => 'json',
        'situacao_cadastro'             => 'json',
        'tipo_conservacao'              => 'json',
        'cod_supervisor_vendedores'     => 'json',
        'libera_pedido_estourado'       => 'json',
        'libera_pedido_cliente_debitos' => 'json',
    ];

    public static $roles = [
        1 => 'Admin',
        2 => 'Vendedor',
        3 => 'Supervisor',
        4 => 'Telemarketing',
        6 => 'Supervisor de Vendedores'
    ];

    public static $status_color = [
        false => 'danger',
        true  => 'success'
    ];
    public static $status       = [
        false => 'Inativo',
        true  => 'Ativo'
    ];


    public $sortableAs = [
        'role_name'
    ];

    public function canImpersonate()
    {
        return $this->hasRole('admin') ? true : false;
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'cod_supervisor', 'codigo');
    }

    public function gerente()
    {
        return $this->belongsTo(User::class, 'cod_gerente', 'codigo');
    }


    public function scopeVendedores($query, $ids = null)
    {
        $ids_md5 = md5('vendedores_' . implode(',', (array) $ids));
        return Cache::remember('vendedores_' . $ids_md5, 3600, function () use ($query, $ids) {
            $vendedores = $query->selectRaw("codigo||' - '||apelido as nome, codigo")
                ->whereNotNull("codigo")
                ->where("is_admin", false)
                ->where("status", true)
                ->orderBy('codigo', 'asc');

            if ($ids) {
                $vendedores->whereIn('codigo', (array) $ids);
            }

            return $vendedores->get()->pluck('nome', 'codigo');
        });
    }

    public function scopeSupervisores($query)
    {
        return $query->selectRaw("codigo||' - '||apelido as nome, codigo")
            ->whereNotNull("codigo")
            ->where(DB::raw("lower(apelido)"), 'like', '%supervisor%')
            ->where("is_admin", false)
            ->where("status", true)
            ->get()
            ->pluck('nome', 'codigo');
    }

    public function scopeGerentes($query)
    {
        return $query->selectRaw("codigo||' - '||apelido as nome, codigo")
            ->whereNotNull("codigo")
            ->where(DB::raw("lower(apelido)"), 'like', '%gerente%')
            ->where("is_admin", false)
            ->where("status", true)
            ->get()
            ->pluck('nome', 'codigo');
    }

}
