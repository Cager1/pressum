<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class NewPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Auth::loginUsingId(12);
        $permissions = [
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
            [
                'name' => 'detach_category',
                'description' => 'Uklanjanje kategorija sa knjige',
            ],
        ];
        foreach ($permissions as $permission) {
            \App\Models\Permission::create($permission);
        }

        $role = \App\Models\Role::where('name', 'Super Admin')->first();
        $role->permissions()->attach(\App\Models\Permission::where('name', 'create_category')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'update_category')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'delete_category')->first());
        $role->permissions()->attach(\App\Models\Permission::where('name', 'detach_category')->first());
    }
}
