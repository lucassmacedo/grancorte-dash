<?php

namespace App\Models\Permission;

use Illuminate\Support\Str;
use Kyslik\ColumnSortable\Sortable;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\Guard;
use Spatie\Permission\PermissionRegistrar;
use Wildside\Userstamps\Userstamps;

class Role extends \Spatie\Permission\Models\Role
{
    use Sortable, Userstamps, QueryCacheable;

    public $cacheFor = 3600; // cache time, in seconds

    protected static $flushCacheOnUpdate = true;

    public $cacheTags = ['roles'];

    public $cachePrefix = 'roles_';

    public static function list()
    {
        return self::where('guard_name', 'web')
            ->orderBy('id', 'asc')
            ->pluck('name', 'id')->map(function ($item, $key) {
                return Str::title($item);
            });
    }
}
