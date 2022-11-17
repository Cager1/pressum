<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create roles
        $roles = [
            [
                'name' => 'Super Admin',
            ],
            [
                'name' => 'Admin',
            ],
            [
                'name' => 'Autor',
            ],
            [
                'name' => 'Korisnik',
            ],
        ];
        foreach ($roles as $role) {
            \App\Models\Role::create($role);
        }

        // create permissions
        $permissions = [
            [
                'name' => 'create_author',
                'description' => 'Create Author',
            ],
            [
                'name' => 'update_author',
                'description' => 'Update Author',
            ],
            [
                'name' => 'delete_author',
                'description' => 'Delete Author',
            ],
            [
                'name' => 'create_book',
                'description' => 'Create Book',
            ],
            [
                'name' => 'update_book',
                'description' => 'Update Book',
            ],
            [
                'name' => 'delete_book',
                'description' => 'Delete Book',
            ],
            // roles
            [
                'name' => 'create_role',
                'description' => 'Create Role',
            ],
            [
                'name' => 'update_role',
                'description' => 'Update Role',
            ],
            [
                'name' => 'delete_role',
                'description' => 'Delete Role',
            ],

            // permissions
            [
                'name' => 'create_permission',
                'description' => 'Create Permission',
            ],
            [
                'name' => 'update_permission',
                'description' => 'Update Permission',
            ],
            [
                'name' => 'delete_permission',
                'description' => 'Delete Permission',
            ],
            // permission for all
            [
                'name' => 'all',
                'description' => 'All Permissions',
            ],
        ];
        foreach ($permissions as $permission) {
            \App\Models\Permission::create($permission);
        }

        // assign permissions to roles
        $role = \App\Models\Role::where('name', 'Super Admin')->first();
        $role->permissions()->attach(\App\Models\Permission::all());

        $role = \App\Models\Role::where('name', 'Admin')->first();
        $role->permissions()->attach(\App\Models\Permission::where('name', 'create_author')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'update_author')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'delete_author')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'create_book')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'update_book')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'delete_book')->first());

        $role = \App\Models\Role::where('name', 'Autor')->first();
        $role->permissions()->attach(\App\Models\Permission::where('name', 'create_book')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'update_book')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'delete_book')->first());

    }
}
