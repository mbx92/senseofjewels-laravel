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
                'title' => 'Timeless',
                'subtitle' => 'Elegance',
                'content' => 'Handcrafted fine jewelry designed for the modern everyday.',
                'cta_text' => 'SHOP COLLECTION',
                'cta_url' => route('shop.index'),
                'settings' => [
                    'season_badge' => 'NEW SEASON '.date('Y'),
                    'eyebrow' => 'ARTISAN JEWELRY · BALI',
                    'text_position' => 'top-left',
                    'hero_images' => [
                        'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?auto=format&fit=crop&w=1600&q=80',
                        'https://images.unsplash.com/photo-1611652022419-a9419f74343d?auto=format&fit=crop&w=1600&q=80',
                        'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?auto=format&fit=crop&w=1600&q=80',
                    ],
                    'banner1_label' => 'SELECTED COLLECTION',
                    'banner1_title' => 'Emas & Perak',
                    'banner1_subtitle' => 'Artisan Bali',
                    'banner1_cta_text' => 'SHOP NOW',
                    'banner1_cta_url' => route('shop.index'),
                    'banner1_text_position' => 'bottom-left',
                    'banner1_image' => 'https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?auto=format&fit=crop&w=900&q=80',
                    'banner2_label' => 'EST. 2019',
                    'banner2_title' => 'Cincin &',
                    'banner2_subtitle' => 'Kalung Pilihan',
                    'banner2_cta_text' => 'EXPLORE',
                    'banner2_cta_url' => route('shop.index'),
                    'banner2_text_position' => 'bottom-left',
                    'banner2_image' => 'https://images.unsplash.com/photo-1588444650733-d53db4f3baec?auto=format&fit=crop&w=900&q=80',
                ],
                'image_path' => 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?auto=format&fit=crop&w=1600&q=80',
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

        Category::query()->where('slug', 'signature-collection')->delete();

        $categories = collect([
            [
                'name' => 'Kalung',
                'slug' => 'kalung',
                'description' => 'Koleksi kalung elegan untuk daily look maupun acara spesial.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Cincin',
                'slug' => 'cincin',
                'description' => 'Cincin modern dengan detail artisan dan finishing premium.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Gelang',
                'slug' => 'gelang',
                'description' => 'Pilihan gelang minimalis hingga statement piece.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Anting',
                'slug' => 'anting',
                'description' => 'Anting ringan dan berkilau untuk tampilan effortless.',
                'sort_order' => 4,
            ],
            [
                'name' => 'Set Perhiasan',
                'slug' => 'set-perhiasan',
                'description' => 'Set serasi untuk hadiah maupun koleksi pribadi.',
                'sort_order' => 5,
            ],
        ])->mapWithKeys(function (array $category) {
            $model = Category::query()->updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'sort_order' => $category['sort_order'],
                    'is_active' => true,
                ],
            );

            return [$category['slug'] => $model];
        });

        foreach ([
            [
                'category' => 'kalung',
                'name' => 'Luna Gold Necklace',
                'slug' => 'luna-gold-necklace',
                'sku' => 'SOJ-KLG-001',
                'short_description' => 'Kalung berlapis emas dengan sentuhan kilau lembut.',
                'description' => 'Dirancang untuk dipakai harian, dengan desain ringan dan rantai nyaman.',
                'specifications' => ['material' => '18K Gold Plated', 'length' => '45 cm', 'finish' => 'Polished'],
                'price' => 850000,
                'cost_price' => 500000,
                'stock' => 15,
                'min_stock_alert' => 4,
                'weight' => 45,
                'is_featured' => true,
            ],
            [
                'category' => 'kalung',
                'name' => 'Aurora Pearl Pendant',
                'slug' => 'aurora-pearl-pendant',
                'sku' => 'SOJ-KLG-002',
                'short_description' => 'Kalung mutiara dengan pendant ramping dan clean.',
                'description' => 'Perpaduan nuansa klasik dan modern untuk acara formal maupun casual.',
                'specifications' => ['material' => 'Freshwater Pearl', 'length' => '42 cm', 'finish' => 'Gloss'],
                'price' => 780000,
                'cost_price' => 470000,
                'stock' => 10,
                'min_stock_alert' => 3,
                'weight' => 35,
                'is_featured' => false,
            ],
            [
                'category' => 'cincin',
                'name' => 'Serenity Ring',
                'slug' => 'serenity-ring',
                'sku' => 'SOJ-CIN-001',
                'short_description' => 'Cincin minimalis dengan aksen batu zircon.',
                'description' => 'Bentuk clean dengan detail tipis agar nyaman untuk dipakai lama.',
                'specifications' => ['material' => '925 Sterling Silver', 'size' => 'Adjustable', 'stone' => 'Zircon'],
                'price' => 620000,
                'cost_price' => 360000,
                'stock' => 20,
                'min_stock_alert' => 5,
                'weight' => 12,
                'is_featured' => true,
            ],
            [
                'category' => 'cincin',
                'name' => 'Eterna Twist Ring',
                'slug' => 'eterna-twist-ring',
                'sku' => 'SOJ-CIN-002',
                'short_description' => 'Cincin twist berwarna rose gold.',
                'description' => 'Detail lilitan halus memberi karakter manis dan tetap elegan.',
                'specifications' => ['material' => 'Rose Gold Plated', 'size' => '6-8', 'finish' => 'Mirror'],
                'price' => 690000,
                'cost_price' => 410000,
                'stock' => 13,
                'min_stock_alert' => 4,
                'weight' => 14,
                'is_featured' => false,
            ],
            [
                'category' => 'gelang',
                'name' => 'Celeste Charm Bracelet',
                'slug' => 'celeste-charm-bracelet',
                'sku' => 'SOJ-GLG-001',
                'short_description' => 'Gelang charm feminin dengan warna emas hangat.',
                'description' => 'Memiliki detail charm kecil untuk mempermanis penampilan harian.',
                'specifications' => ['material' => 'Alloy Gold Plated', 'length' => '18 cm', 'closure' => 'Lobster clasp'],
                'price' => 540000,
                'cost_price' => 310000,
                'stock' => 17,
                'min_stock_alert' => 4,
                'weight' => 20,
                'is_featured' => false,
            ],
            [
                'category' => 'gelang',
                'name' => 'Noir Chain Bracelet',
                'slug' => 'noir-chain-bracelet',
                'sku' => 'SOJ-GLG-002',
                'short_description' => 'Gelang chain bold untuk gaya edgy.',
                'description' => 'Desain unisex dengan rantai tegas yang tetap nyaman dipakai.',
                'specifications' => ['material' => 'Stainless Steel', 'length' => '19 cm', 'finish' => 'Matte'],
                'price' => 590000,
                'cost_price' => 340000,
                'stock' => 11,
                'min_stock_alert' => 3,
                'weight' => 28,
                'is_featured' => true,
            ],
            [
                'category' => 'anting',
                'name' => 'Mira Hoop Earrings',
                'slug' => 'mira-hoop-earrings',
                'sku' => 'SOJ-ATG-001',
                'short_description' => 'Hoop earrings ringan untuk look sehari-hari.',
                'description' => 'Ukuran medium dengan pengunci kuat dan nyaman untuk dipakai lama.',
                'specifications' => ['material' => '18K Gold Plated', 'diameter' => '2.8 cm', 'weight_pair' => '10 g'],
                'price' => 430000,
                'cost_price' => 250000,
                'stock' => 25,
                'min_stock_alert' => 6,
                'weight' => 10,
                'is_featured' => false,
            ],
            [
                'category' => 'anting',
                'name' => 'Aster Drop Earrings',
                'slug' => 'aster-drop-earrings',
                'sku' => 'SOJ-ATG-002',
                'short_description' => 'Anting drop elegan untuk acara malam.',
                'description' => 'Siluet memanjang yang menonjolkan garis wajah secara natural.',
                'specifications' => ['material' => 'Sterling Silver', 'length' => '4.5 cm', 'stone' => 'Cubic zirconia'],
                'price' => 510000,
                'cost_price' => 300000,
                'stock' => 14,
                'min_stock_alert' => 4,
                'weight' => 16,
                'is_featured' => true,
            ],
            [
                'category' => 'set-perhiasan',
                'name' => 'Opal Grace Set',
                'slug' => 'opal-grace-set',
                'sku' => 'SOJ-SET-001',
                'short_description' => 'Set kalung dan anting dengan aksen opal.',
                'description' => 'Pilihan hadiah sempurna dengan desain serasi dan berkelas.',
                'specifications' => ['material' => 'Gold Plated', 'contents' => 'Necklace + Earrings', 'stone' => 'Synthetic opal'],
                'price' => 1250000,
                'cost_price' => 780000,
                'stock' => 8,
                'min_stock_alert' => 2,
                'weight' => 70,
                'is_featured' => true,
            ],
            [
                'category' => 'set-perhiasan',
                'name' => 'Velvet Bloom Set',
                'slug' => 'velvet-bloom-set',
                'sku' => 'SOJ-SET-002',
                'short_description' => 'Set premium dengan sentuhan floral modern.',
                'description' => 'Dirancang untuk momen spesial dengan detail mengilap yang elegan.',
                'specifications' => ['material' => 'Sterling Silver', 'contents' => 'Ring + Necklace', 'finish' => 'High polish'],
                'price' => 1390000,
                'cost_price' => 860000,
                'stock' => 6,
                'min_stock_alert' => 2,
                'weight' => 76,
                'is_featured' => false,
            ],
        ] as $product) {
            Product::query()->updateOrCreate(
                ['slug' => $product['slug']],
                [
                    'category_id' => $categories[$product['category']]->id,
                    'name' => $product['name'],
                    'sku' => $product['sku'],
                    'short_description' => $product['short_description'],
                    'description' => $product['description'],
                    'specifications' => $product['specifications'],
                    'price' => $product['price'],
                    'cost_price' => $product['cost_price'],
                    'stock' => $product['stock'],
                    'min_stock_alert' => $product['min_stock_alert'],
                    'weight' => $product['weight'],
                    'is_featured' => $product['is_featured'],
                    'is_active' => true,
                    'published_at' => now(),
                ],
            );
        }
    }
}
