<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Permissions
        $permissions = [
            'access admin',
            'manage content',
            'manage catalog',
            'manage orders',
            'manage users',
            'manage settings',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Roles
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $admin      = Role::firstOrCreate(['name' => 'admin',       'guard_name' => 'web']);
        $editor     = Role::firstOrCreate(['name' => 'editor',      'guard_name' => 'web']);

        $superAdmin->syncPermissions(Permission::all());

        $admin->syncPermissions([
            'access admin',
            'manage content',
            'manage catalog',
            'manage orders',
        ]);

        $editor->syncPermissions([
            'access admin',
            'manage content',
        ]);

        // Super-admin user
        $user = User::firstOrCreate(
            ['email' => 'superadmin@senseofjewels.com'],
            [
                'name'     => 'Super Admin',
                'phone'    => '+62 812 0000 0001',
                'password' => Hash::make('password'),
            ],
        );

        $user->syncRoles([$superAdmin]);

        // Default settings
        $defaults = [
            ['key' => 'site_name',           'group' => 'general',  'value' => 'Sense of Jewels'],
            ['key' => 'site_tagline',         'group' => 'general',  'value' => 'Handcrafted Balinese Jewelry'],
            ['key' => 'currency',             'group' => 'general',  'value' => 'IDR'],
            ['key' => 'weight_unit',          'group' => 'general',  'value' => 'gram'],
            ['key' => 'maintenance_mode',     'group' => 'general',  'value' => '0'],
            ['key' => 'contact_email',        'group' => 'contact',  'value' => 'hello@senseofjewels.com'],
            ['key' => 'contact_phone',        'group' => 'contact',  'value' => '+62 821 9999 9999'],
            ['key' => 'contact_whatsapp',     'group' => 'contact',  'value' => '+62 821 9999 9999'],
            ['key' => 'whatsapp_number',      'group' => 'social',   'value' => '+62 821 9999 9999'],
            ['key' => 'social_instagram',     'group' => 'social',   'value' => 'https://instagram.com/senseofjewels'],
            ['key' => 'seo_title',            'group' => 'seo',      'value' => 'Sense of Jewels — Handcrafted Balinese Jewelry'],
            ['key' => 'seo_description',      'group' => 'seo',      'value' => 'Discover timeless artisan jewelry handmade in Bali. Fine rings, necklaces, earrings, and bracelets crafted for the modern soul.'],
            ['key' => 'shop_currency_symbol', 'group' => 'commerce', 'value' => 'Rp'],
            ['key' => 'tax_rate',             'group' => 'commerce', 'value' => '0'],
            ['key' => 'free_shipping_threshold', 'group' => 'commerce', 'value' => '500000'],
        ];

        foreach ($defaults as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                ['group' => $setting['group'], 'value' => $setting['value'], 'type' => 'text'],
            );
        }

        $this->command->info('SuperAdminSeeder: super-admin user, roles, permissions, and default settings seeded.');
    }
}
