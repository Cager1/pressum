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
            [
                'name' => 'detach_book',
                'description' => 'Detach Book from author',
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
                'name' => 'view_permission',
                'description' => 'View Permission',
            ],
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
            // permission_role
            [
                'name' => 'view_permission_role',
                'description' => 'View Permission Role',
            ],
            [
                'name' => 'create_permission_role',
                'description' => 'Create Permission Role',
            ],
            [
                'name' => 'update_permission_role',
                'description' => 'Update Permission Role',
            ],
            [
                'name' => 'delete_permission_role',
                'description' => 'Delete Permission Role',
            ],
            // science
            [
                'name' => 'create_science',
                'description' => 'Create Science',
            ],
            [
                'name' => 'update_science',
                'description' => 'Update Science',
            ],
            [
                'name' => 'delete_science',
                'description' => 'Delete Science',
            ],
            // users

            [
                'name' => 'view_user',
                'description' => 'View User',
            ],

            [
                'name' => 'update_user',
                'description' => 'Update User',
            ],

            // permission for files
            [
                'name' => 'create_file',
                'description' => 'Create File',
            ],
            [
                'name' => 'update_file',
                'description' => 'Update File',
            ],
            [
                'name' => 'delete_file',
                'description' => 'Delete File',
            ],
            [
                'name' => 'view_file',
                'description' => 'View File',
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
        $role->permissions()->attach(\App\Models\Permission::where('name', 'detach_book')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'create_role')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'update_role')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'delete_role')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'create_science')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'update_science')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'delete_science')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'create_file')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'update_file')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'delete_file')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'view_file')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'view_user')->first());




        $role = \App\Models\Role::where('name', 'Autor')->first();
        $role->permissions()->attach(\App\Models\Permission::where('name', 'create_book')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'update_book')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'delete_book')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'create_file')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'create_author')->first());


    }
}
