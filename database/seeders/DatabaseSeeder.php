<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Page;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\Section;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = collect(['super-admin', 'admin', 'customer'])->mapWithKeys(function (string $role) {
            return [$role => Role::query()->firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ])];
        });

        foreach (['access admin', 'manage content', 'manage catalog', 'manage orders', 'manage users'] as $permissionName) {
            Permission::query()->firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        $roles['super-admin']->syncPermissions(Permission::all());
        $roles['admin']->syncPermissions([
            'access admin',
            'manage content',
            'manage catalog',
            'manage orders',
        ]);

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'phone' => '+62 812 3456 7890',
                'password' => 'password',
            ],
        );
        $admin->syncRoles([$roles['super-admin']]);

        $customer = User::query()->firstOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Customer User',
                'phone' => '+62 811 1111 1111',
                'password' => 'password',
            ],
        );
        $customer->syncRoles([$roles['customer']]);

        $homePage = Page::query()->firstOrCreate(
            ['slug' => 'home'],
            [
                'name' => 'Home',
                'title' => 'Craft memorable brand experiences and commerce journeys.',
                'excerpt' => 'A Laravel-powered company profile and e-commerce storefront with a custom CMS.',
                'is_active' => true,
                'published_at' => now(),
            ],
        );

        foreach ([
            'hero' => [
                'type' => 'hero',
                'title' => 'Build a polished company profile and shop in one Laravel codebase.',
                'subtitle' => 'Content sections, products, orders, and the admin panel are all scaffolded with DaisyUI corporate styling.',
                'cta_text' => 'Browse the Shop',
                'cta_url' => route('shop.index'),
                'sort_order' => 1,
            ],
            'about' => [
                'type' => 'content',
                'title' => 'About Sense of Jewels',
                'content' => 'This seeded content demonstrates how the landing page can pull dynamic sections from the CMS-ready database schema.',
                'sort_order' => 2,
            ],
            'contact' => [
                'type' => 'contact',
                'title' => 'Let us help your brand launch faster.',
                'content' => 'Storefront contact data can live in settings or section content depending on your preference.',
                'sort_order' => 3,
            ],
        ] as $key => $payload) {
            Section::query()->updateOrCreate(
                ['page_id' => $homePage->id, 'key' => $key],
                array_merge($payload, ['is_active' => true]),
            );
        }

        foreach ([
            ['group' => 'contact', 'key' => 'contact_email', 'value' => 'hello@senseofjewels.test'],
            ['group' => 'contact', 'key' => 'contact_phone', 'value' => '+62 821 9999 9999'],
        ] as $setting) {
            Setting::query()->updateOrCreate(
                ['key' => $setting['key']],
                ['group' => $setting['group'], 'value' => $setting['value'], 'type' => 'text'],
            );
        }

        foreach ([
            [
                'title' => 'Brand Storytelling',
                'slug' => 'brand-storytelling',
                'summary' => 'Translate company positioning into rich landing page content.',
                'description' => 'Use the CMS section builder to manage hero content, about content, and supporting assets.',
                'features' => ['Hero section', 'About block', 'Structured content'],
                'sort_order' => 1,
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'title' => 'Commerce Enablement',
                'slug' => 'commerce-enablement',
                'summary' => 'Launch catalog, cart, and checkout experiences quickly.',
                'description' => 'Product, cart, order, and payment tables are scaffolded for the next build steps.',
                'features' => ['Catalog', 'Session cart', 'Checkout skeleton'],
                'sort_order' => 2,
                'is_featured' => true,
                'is_active' => true,
            ],
        ] as $service) {
            Service::query()->updateOrCreate(['slug' => $service['slug']], $service);
        }

        Testimonial::query()->updateOrCreate(
            ['name' => 'Nadia Putri'],
            [
                'position' => 'Marketing Lead',
                'company' => 'Lumina Atelier',
                'rating' => 5,
                'message' => 'The foundation is clean, modern, and ready for our team to keep building on.',
                'sort_order' => 1,
                'is_featured' => true,
                'is_active' => true,
            ],
        );

        $category = Category::query()->updateOrCreate(
            ['slug' => 'signature-collection'],
            [
                'name' => 'Signature Collection',
                'description' => 'Seed category for the storefront skeleton.',
                'sort_order' => 1,
                'is_active' => true,
            ],
        );

        Product::query()->updateOrCreate(
            ['slug' => 'luna-gold-necklace'],
            [
                'category_id' => $category->id,
                'name' => 'Luna Gold Necklace',
                'sku' => 'SOJ-LUNA-001',
                'short_description' => 'A sample seeded product for validating catalog, cart, and checkout flows.',
                'description' => 'This product exists to verify that the full-stack storefront scaffolding renders correctly.',
                'specifications' => [
                    'material' => '18K Gold Plated',
                    'length' => '45 cm',
                    'finish' => 'Polished',
                ],
                'price' => 850000,
                'compare_at_price' => 950000,
                'cost_price' => 500000,
                'stock' => 12,
                'min_stock_alert' => 3,
                'weight' => 45,
                'is_featured' => true,
                'is_active' => true,
                'published_at' => now(),
            ],
        );
    }
}
