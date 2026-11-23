<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesPermissionsSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'super_admin', 'display_name' => 'Super Admin'],
            ['name' => 'editor', 'display_name' => 'Editor'],
            ['name' => 'publisher', 'display_name' => 'Publisher'],
        ];

        $schema = DB::getSchemaBuilder();

        if ($schema->hasTable('roles')) {
            foreach ($roles as $r) {
                if ($schema->hasColumn('roles', 'display_name')) {
                    DB::table('roles')->updateOrInsert(['name' => $r['name']], $r);
                } else {
                    DB::table('roles')->updateOrInsert(['name' => $r['name']], ['name' => $r['name']]);
                }
            }
        }

        $perms = [
            ['name' => 'publish', 'display_name' => 'Can publish content'],
            ['name' => 'edit', 'display_name' => 'Can edit content'],
        ];

        if ($schema->hasTable('permissions')) {
            foreach ($perms as $p) {
                if ($schema->hasColumn('permissions', 'display_name')) {
                    DB::table('permissions')->updateOrInsert(['name' => $p['name']], $p);
                } else {
                    DB::table('permissions')->updateOrInsert(['name' => $p['name']], ['name' => $p['name']]);
                }
            }
        }

        // Assign publish and edit to super_admin by default only if role_permission table and permissions table exist
        if ($schema->hasTable('role_permission') && $schema->hasTable('roles') && $schema->hasTable('permissions')) {
            $super = DB::table('roles')->where('name', 'super_admin')->first();
            $publishPerm = DB::table('permissions')->where('name', 'publish')->first();
            $editPerm = DB::table('permissions')->where('name', 'edit')->first();

            if ($super && $publishPerm) {
                DB::table('role_permission')->updateOrInsert([
                    'role_id' => $super->id,
                    'permission_id' => $publishPerm->id,
                ], ['role_id' => $super->id, 'permission_id' => $publishPerm->id]);
            }

            if ($super && $editPerm) {
                DB::table('role_permission')->updateOrInsert([
                    'role_id' => $super->id,
                    'permission_id' => $editPerm->id,
                ], ['role_id' => $super->id, 'permission_id' => $editPerm->id]);
            }
        }
    }
}
