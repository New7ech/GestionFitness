<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ImproveRolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = [
            ['name' => 'super_admin', 'guard_name' => 'web'],
            ['name' => 'manager', 'guard_name' => 'web'],
            ['name' => 'employee', 'guard_name' => 'web'],
            ['name' => 'guest', 'guard_name' => 'web'],
            ['name' => 'coach', 'guard_name' => 'web'],
        ];

        foreach ($roles as $roleData) {
            Role::firstOrCreate(
                ['name' => $roleData['name'], 'guard_name' => $roleData['guard_name']],
                $roleData
            );
        }

        $permissionsByCategory = [
            'Utilisateurs' => [
                'show-users',
                'create-users',
                'edit-users',
                'delete-users',
                'manage-user-roles',
                'manage-user-permissions',
            ],
            'Articles' => [
                'show-articles',
                'create-articles',
                'edit-articles',
                'delete-articles',
                'manage-articles-stock',
                'publish-articles',
            ],
            'Fournisseurs' => [
                'show-fournisseurs',
                'create-fournisseurs',
                'edit-fournisseurs',
                'delete-fournisseurs',
                'validate-fournisseurs',
            ],
            'Categories' => [
                'show-categories',
                'create-categories',
                'edit-categories',
                'delete-categories',
                'organize-categories',
            ],
            'Emplacements' => [
                'show-emplacements',
                'create-emplacements',
                'edit-emplacements',
                'delete-emplacements',
                'manage-emplacements-stock',
            ],
            'Factures' => [
                'show-factures',
                'create-factures',
                'edit-factures',
                'delete-factures',
                'validate-factures',
                'manage-facture-payments',
            ],
            'Roles et Permissions' => [
                'show-roles',
                'create-roles',
                'edit-roles',
                'delete-roles',
                'manage-permissions',
                'assign-roles',
                'revoke-roles',
            ],
            'Rapports' => [
                'view-reports',
                'generate-reports',
                'export-reports',
                'manage-reports',
            ],
            'Systeme' => [
                'system-settings',
                'system-backup',
                'system-logs',
                'system-maintenance',
            ],
            'Fitness' => [
                'show-participantes',
                'create-participantes',
                'edit-participantes',
                'delete-participantes',
                'view-participante-health-data',
                'show-challenges',
                'create-challenges',
                'edit-challenges',
                'delete-challenges',
                'change-challenge-status',
                'show-payments',
                'create-payments',
                'edit-payments',
                'delete-payments',
                'show-recus',
                'generate-recus',
                'record-attendance',
                'edit-attendance',
                'record-measurements',
                'edit-measurements',
                'delete-measurements',
                'manage-media',
                'delete-media',
                'manage-comments',
                'view-bilan',
                'export-bilan-pdf',
                'view-dashboard',
                'view-activity-log',
            ],
        ];

        foreach ($permissionsByCategory as $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(
                    ['name' => $permission, 'guard_name' => 'web'],
                    ['name' => $permission, 'guard_name' => 'web']
                );
            }
        }

        $roleAssignments = [
            'super_admin' => array_keys($permissionsByCategory),
            'manager' => [
                'Utilisateurs',
                'Articles',
                'Fournisseurs',
                'Categories',
                'Emplacements',
                'Factures',
                'Rapports',
                'Fitness',
            ],
            'employee' => [
                'Articles',
                'Fournisseurs',
                'Categories',
                'Emplacements',
            ],
            'guest' => [
                'Articles',
                'Fournisseurs',
                'Categories',
            ],
        ];

        foreach ($roleAssignments as $roleName => $categories) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();

            if (! $role) {
                continue;
            }

            $permissions = [];

            foreach ($categories as $category) {
                $permissions = array_merge($permissions, $permissionsByCategory[$category]);
            }

            $role->syncPermissions($permissions);
        }

        Role::where('name', 'coach')
            ->where('guard_name', 'web')
            ->first()
            ?->syncPermissions([
                'show-participantes',
                'view-participante-health-data',
                'show-challenges',
                'record-attendance',
                'edit-attendance',
                'record-measurements',
                'edit-measurements',
                'manage-media',
                'manage-comments',
                'view-bilan',
            ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command->info('Roles et permissions ameliores avec succes!');
        $this->command->info('Structure des permissions creee par categorie');
        $this->command->info('Assignation hierarchique des permissions configuree');
    }
}
