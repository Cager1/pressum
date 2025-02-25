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
                'description' => 'Stvaranje autora',
            ],
            [
                'name' => 'update_author',
                'description' => 'Ažuriranje svih autora',
            ],
            [
                'name' => 'delete_author',
                'description' => 'Brisanje svih autora',
            ],
            // Books
            [
                'name' => 'create_book',
                'description' => 'Stvaranje knjige',
            ],
            [
                'name' => 'update_book',
                'description' => 'Ažuriranje svih knjiga',
            ],
            [
                'name' => 'delete_book',
                'description' => 'Brisanje svih knjiga',
            ],
            [
                'name' => 'detach_book',
                'description' => 'Uklanjanje autora iz knjige',
            ],
            // roles
            [
                'name' => 'create_role',
                'description' => 'Stvaranje uloge',
            ],
            [
                'name' => 'update_role',
                'description' => 'Ažuriranje svih uloga',
            ],
            [
                'name' => 'delete_role',
                'description' => 'Brisanje svih uloga',
            ],

            // permissions
            [
                'name' => 'view_permission',
                'description' => 'Pregled svih dozvola',
            ],
            [
                'name' => 'create_permission',
                'description' => 'Stvaranje dozvole',
            ],
            [
                'name' => 'update_permission',
                'description' => 'Ažuriranje svih dozvola',
            ],
            [
                'name' => 'delete_permission',
                'description' => 'Brisanje svih dozvola',
            ],
            // permission_role
            [
                'name' => 'view_permission_role',
                'description' => 'Pregled svih dozvola za uloge',
            ],
            [
                'name' => 'create_permission_role',
                'description' => 'Stvaranje dozvole za ulogu',
            ],
            [
                'name' => 'update_permission_role',
                'description' => 'Ažuriranje svih dozvola za uloge',
            ],
            [
                'name' => 'delete_permission_role',
                'description' => 'Brisanje svih dozvola za uloge',
            ],
            // science
            [
                'name' => 'create_science',
                'description' => 'Stvaranje znanosti',
            ],
            [
                'name' => 'update_science',
                'description' => 'Ažuriranje svih znanosti',
            ],
            [
                'name' => 'delete_science',
                'description' => 'Brisanje svih znanosti',
            ],
            // categories
            [
                'name' => 'create_category',
                'description' => 'Stvaranje kategorije',
            ],
            [
                'name' => 'update_category',
                'description' => 'Ažuriranje svih kategorija',
            ],
            [
                'name' => 'delete_category',
                'description' => 'Brisanje svih kategorija',
            ],
            // users

            [
                'name' => 'view_user',
                'description' => 'Pregled svih korisnika',
            ],

            [
                'name' => 'update_user',
                'description' => 'Ažuriranje svih korisnika',
            ],

            // permission for files
            [
                'name' => 'create_file',
                'description' => 'Stvaranje datoteke',
            ],
            [
                'name' => 'update_file',
                'description' => 'Ažuriranje svih datoteka',
            ],
            [
                'name' => 'delete_file',
                'description' => 'Brisanje svih datoteka',
            ],
            [
                'name' => 'view_file',
                'description' => 'Pregled svih datoteka',
            ],
            // permission for all
            [
                'name' => 'all',
                'description' => 'Sve dozvole',
            ],
        ];
        foreach ($permissions as $permission) {
            \App\Models\Permission::create($permission);
        }

        // assign permissions to roles
        $role = \App\Models\Role::where('name', 'Super Admin')->first();
        $role->permissions()->attach(\App\Models\Permission::all());

        $role = \App\Models\Role::where('name', 'Admin')->first();
        // book
        $role->permissions()->attach(\App\Models\Permission::where('name', 'create_book')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'update_book')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'delete_book')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'detach_book')->first());

        // author
        $role->permissions()->attach(\App\Models\Permission::where('name', 'create_author')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'update_author')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'delete_author')->first());

        // role
        $role->permissions()->attach(\App\Models\Permission::where('name', 'create_role')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'update_role')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'delete_role')->first());

        // science
        $role->permissions()->attach(\App\Models\Permission::where('name', 'create_science')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'update_science')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'delete_science')->first());

        // file
        $role->permissions()->attach(\App\Models\Permission::where('name', 'create_file')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'update_file')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'delete_file')->first());

        // user
        $role->permissions()->attach(\App\Models\Permission::where('name', 'view_file')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'view_user')->first());

        $role = \App\Models\Role::where('name', 'Autor')->first();
        $role->permissions()->attach(\App\Models\Permission::where('name', 'create_book')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'create_file')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'create_author')->first());


    }
}
