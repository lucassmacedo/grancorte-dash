<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class UserStampsServceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        Blueprint::macro('userstamps', function ($created_by = 'created_by', $updated_by = 'updated_by', $deleted_by = 'deleted_by') {
            if (!is_null($created_by)) {
                $this->unsignedBigInteger($created_by)->unsigned()->nullable();
                $this->foreign($created_by)
                     ->references('id')
                     ->on('users')
                     ->onDelete('set null');
            }

            if (!is_null($updated_by)) {
                $this->unsignedBigInteger($updated_by)->unsigned()->nullable();
                $this->foreign($updated_by)
                     ->references('id')
                     ->on('users')
                     ->onDelete('set null');
            }

            if (!is_null($deleted_by)) {
                $this->unsignedBigInteger($deleted_by)->unsigned()->nullable();
                $this->foreign($deleted_by)
                     ->references('id')
                     ->on('users')
                     ->onDelete('set null');
            }
            return $this;
        });

        Blueprint::macro('dropuserstamps', function ($created_by = 'created_by', $updated_by = 'updated_by', $deleted_by = 'deleted_by') {

            $this->dropForeign($created_by);
            $this->dropForeign($updated_by);
            $this->dropForeign($created_by);

            $columnsToDrop = [];
            if (!is_null($created_by)) {
                $columnsToDrop[] = $created_by;
            }
            if (!is_null($updated_by)) {
                $columnsToDrop[] = $updated_by;
            }
            if (!is_null($deleted_by)) {
                $columnsToDrop[] = $deleted_by;
            }
            if (!empty($columnsToDrop)) {
                $this->dropColumn($columnsToDrop);
            }
        });

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
