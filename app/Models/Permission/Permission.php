<?php

namespace App\Models\Permission;

use Kyslik\ColumnSortable\Sortable;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Wildside\Userstamps\Userstamps;

class Permission extends \Spatie\Permission\Models\Permission
{
    use Sortable, QueryCacheable;

    public $cacheFor = 3600; // cache time, in seconds

    protected $guarded = [];
    protected static $flushCacheOnUpdate = true;

    public $cacheTags = ['permission'];

    public $cachePrefix = 'permission_';
}
