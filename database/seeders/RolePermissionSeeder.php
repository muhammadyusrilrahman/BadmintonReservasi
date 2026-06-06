<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ──────────────────────────────────────
        // Define Permissions
        // Convention: {resource}.{action}
        // ──────────────────────────────────────
        $permissions = [
            // Dashboard
            'dashboard.view',

            // Court management
            'court.view',
            'court.create',
            'court.update',
            'court.delete',

            // Reservation management
            'reservation.view',
            'reservation.create',
            'reservation.update',
            'reservation.delete',
            'reservation.approve',
            'reservation.cancel',
            'reservation.checkin',

            // User management
            'user.view',
            'user.create',
            'user.update',
            'user.delete',

            // Transaction / Payment
            'transaction.view',
            'transaction.create',
            'transaction.update',

            // Reports
            'report.view',
            'report.export',

            // Settings
            'setting.view',
            'setting.update',

            // Schedule / Maintenance
            'schedule.view',
            'schedule.update',
            'maintenance.view',
            'maintenance.create',
            'maintenance.update',

            // Activity Log
            'activity-log.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ──────────────────────────────────────
        // Create Roles & Assign Permissions
        // ──────────────────────────────────────

        // Admin — full access
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        // Customer — limited to own reservations
        $customer = Role::firstOrCreate(['name' => 'customer']);
        $customer->givePermissionTo([
            'dashboard.view',
            'reservation.view',
            'reservation.create',
            'reservation.cancel',
        ]);

        // Kasir — cashier / payment
        $kasir = Role::firstOrCreate(['name' => 'kasir']);
        $kasir->givePermissionTo([
            'dashboard.view',
            'reservation.view',
            'reservation.approve',
            'transaction.view',
            'transaction.create',
            'transaction.update',
            'report.view',
        ]);

        // Staff — field management
        $staff = Role::firstOrCreate(['name' => 'staff']);
        $staff->givePermissionTo([
            'dashboard.view',
            'court.view',
            'reservation.view',
            'reservation.checkin',
            'schedule.view',
            'schedule.update',
            'maintenance.view',
            'maintenance.create',
            'maintenance.update',
        ]);

        // ──────────────────────────────────────
        // Create Demo Users
        // ──────────────────────────────────────
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@adenialsa.com'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $adminUser->assignRole('admin');

        $customerUser = User::firstOrCreate(
            ['email' => 'customer@adenialsa.com'],
            [
                'name' => 'Budi Santoso',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $customerUser->assignRole('customer');

        $kasirUser = User::firstOrCreate(
            ['email' => 'kasir@adenialsa.com'],
            [
                'name' => 'Siti Rahma',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $kasirUser->assignRole('kasir');

        $staffUser = User::firstOrCreate(
            ['email' => 'staff@adenialsa.com'],
            [
                'name' => 'Andi Pratama',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $staffUser->assignRole('staff');
    }
}
