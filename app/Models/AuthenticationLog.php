<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Kyslik\ColumnSortable\Sortable;

class AuthenticationLog extends Model
{
    use Sortable;
    public $timestamps = false;

    public $sortableAs = [
        'nome'
    ];

    protected $table = 'authentication_log';

    protected $fillable = [
        'ip_address',
        'user_agent',
        'login_at',
        'login_successful',
        'authenticatable_id',
        'authenticatable_type',
        'logout_at',
        'cleared_by_user',
        'location',
    ];

    protected $casts = [
        'cleared_by_user'  => 'boolean',
        'location'         => 'array',
        'login_successful' => 'boolean',
    ];

    protected $cast = [
        'login_at'  => 'date',
        'logout_at' => 'date'
    ];

    public function __construct(array $attributes = [])
    {
        if (!isset($this->connection)) {
            $this->setConnection(config('authentication-log.db_connection'));
        }

        parent::__construct($attributes);
    }

    public function getTable()
    {
        return config('authentication-log.table_name', parent::getTable());
    }

    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'authenticatable_id', 'id')
            ->where('authenticatable_type', User::class);
    }

    public function client()
    {
        return $this->belongsTo(Cliente::class, 'authenticatable_id', 'id')
            ->where('authenticatable_type', Cliente::class);
    }
}
