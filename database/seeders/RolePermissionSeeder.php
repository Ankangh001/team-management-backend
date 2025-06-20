<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $roles = ['super_admin', 'admin', 'user', 'team_viewer', 'team_editor'];
        $permissions = [
            'create_post',
            'edit_post',
            'delete_post',
            'comment',
            'like',
            'view_messages',
            'assign_roles',
            'reply_comments',
            'block_users',
            'view_users'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Attach permissions
        $superAdmin = Role::where('name', 'super_admin')->first();
        $superAdmin->permissions()->sync(Permission::pluck('id'));

        $admin = Role::where('name', 'admin')->first();
        $admin->permissions()->sync([
            Permission::where('name', 'create_post')->first()->id,
            Permission::where('name', 'reply_comments')->first()->id,
        ]);

        $teamViewer = Role::where('name', 'team_viewer')->first();
        $teamViewer->permissions()->sync([
            Permission::where('name', 'view_messages')->first()->id,
        ]);

        $teamEditor = Role::where('name', 'team_editor')->first();
        $teamEditor->permissions()->sync([
            Permission::where('name', 'create_post')->first()->id,
            Permission::where('name', 'edit_post')->first()->id,
            Permission::where('name', 'view_messages')->first()->id,
        ]);

        $user = Role::where('name', 'user')->first();
        $user->permissions()->sync([
            Permission::where('name', 'comment')->first()->id,
            Permission::where('name', 'like')->first()->id,
        ]);
    }
}

