<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Permissions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin      = Role::create(['name' => 'admin']);
        $vendedor   = Role::create(['name' => 'vendedor']);
        $supervisor = Role::create(['name' => 'supervisor']);
        $permission = Permission::create(['name' => 'vendedores']);

        $admin->givePermissionTo($permission);
    }
}
