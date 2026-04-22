# ⚡ Laravel Full-Stack Master Prompt Guide
> Company Profile · Shop · CMS · DaisyUI · Laravel Breeze · Midtrans

---

## 📋 Tech Stack
| Komponen | Pilihan |
|---|---|
| Framework | Laravel 11 |
| Database | MySQL |
| Templating | Blade + Alpine.js |
| CSS | Tailwind CSS + DaisyUI (theme: corporate) |
| Auth | Laravel Breeze (Blade stack) |
| Roles | Spatie Laravel Permission |
| Bundler | Vite |
| Payment | Midtrans Snap |

---

## 📖 Cara Penggunaan

1. Gunakan prompt secara **BERURUTAN** — Prompt 1 sampai Prompt 6
2. Setiap prompt dijalankan dalam **satu sesi chat** (ChatGPT, Claude, atau Cursor IDE)
3. Di setiap sesi baru, awali dengan menempelkan ringkasan struktur file dari sesi sebelumnya
4. Cursor IDE atau GitHub Copilot memberikan hasil terbaik

### Setup DaisyUI + Tailwind di Laravel
```bash
npm install -D tailwindcss daisyui @tailwindcss/typography
npx tailwindcss init
```

```js
// tailwind.config.js
module.exports = {
  content: ["./resources/**/*.blade.php", "./resources/**/*.js"],
  plugins: [require("daisyui"), require("@tailwindcss/typography")],
  daisyui: {
    themes: ["corporate"],
    defaultTheme: "corporate"
  }
}
```

### Pesan Pembuka untuk Sesi Baru
```
Continue our previous Laravel project.
Tech stack: Laravel 11, Blade, Alpine.js, Tailwind CSS + DaisyUI,
Laravel Breeze (auth), Spatie Permission (roles), Midtrans (payment).
Here is the current folder structure: [paste struktur folder]
```

---

## PROMPT 1 — Project Setup & Arsitektur

```
You are an expert Laravel developer. Help me build a full-stack web application with the following specifications:

## Tech Stack
- Laravel 11 (latest)
- MySQL database
- Blade templating engine + Alpine.js for interactivity
- Tailwind CSS + DaisyUI (theme: corporate) for styling
- Laravel Breeze for authentication (Blade stack)
- Vite for asset bundling
- Spatie Laravel Permission for role management

## DaisyUI Setup
Install and configure DaisyUI:
- npm install -D daisyui
- Add to tailwind.config.js plugins: require("daisyui")
- Set defaultTheme: "corporate" in daisyui config
- Use DaisyUI component classes throughout all Blade views
- Combine with Alpine.js for interactive components (modal, dropdown, drawer)

## Project Overview
Build a Company Profile Website with integrated E-Commerce Shop and a Custom Admin Panel (CMS).

## Application Modules

### 1. Landing Page (Public)
- Hero, About, Services, Portfolio/Gallery, Testimonials, Contact
- All content manageable via CMS Admin
- Use DaisyUI: hero, card, carousel, rating, badge components

### 2. Shop (Public)
- Product catalog with categories & filters
- Product detail page
- Shopping cart (session-based)
- Checkout process
- Payment gateway integration (Midtrans Snap)
- Order tracking for customers
- Use DaisyUI: card, badge, table, steps, alert components

### 3. Custom Admin Panel (CMS)
- Login: /admin/login (separate from public)
- Dashboard with summary statistics
- Content Management (Landing Page sections)
- Product & Category Management
- Order Management
- Inventory & Stock Management
- Discount & Voucher Management
- User & Role Management
- Use DaisyUI: drawer, navbar, stat, table, modal, form components

## Database Tables
Generate migrations for all tables below in correct order:
users, roles, permissions, model_has_roles,
pages, sections, settings (key-value),
services, portfolio_items, testimonials,
products, categories, product_images,
carts, cart_items,
orders, order_items,
payments, payment_logs,
inventory_logs,
discounts, vouchers, voucher_usages

## Generate
1. Full project folder structure
2. All database migrations in correct order
3. All Models with relationships and fillable fields
4. tailwind.config.js with DaisyUI configuration
5. vite.config.js
6. Main layout (layouts/app.blade.php) using DaisyUI navbar & footer
7. Admin layout (layouts/admin.blade.php) using DaisyUI drawer sidebar
```

---

## PROMPT 2 — Landing Page & CMS Konten

```
Continue our Laravel project. Now focus on the Landing Page and CMS for content management.
Use DaisyUI components for all UI elements in both public and admin views.

## Landing Page - Public Routes (web.php)
- GET / → HomeController@index
- POST /contact → ContactController@store

## Landing Page Sections (all dynamic from DB)

### Hero
- Fields: title, subtitle, cta_text, cta_link, background_image
- DaisyUI: hero, hero-content, btn btn-primary

### About
- Fields: title, description, image
- DaisyUI: card, card-body, card-figure

### Services
- Fields: icon, title, description (list items)
- DaisyUI: card, card-compact, grid, badge

### Portfolio/Gallery
- Fields: title, image, category, description
- DaisyUI: card, tabs, badge, modal (lightbox)

### Testimonials
- Fields: name, photo, rating, message, position
- DaisyUI: carousel, carousel-item, rating, avatar

### Contact
- Fields: address, phone, email, maps_embed, whatsapp
- DaisyUI: form-control, input, textarea, btn

## CMS Admin Requirements
- CRUD for each landing page section above
- Image upload using Laravel Storage (public disk)
- Form validation with Laravel Form Requests
- Flash messages: <div class="alert alert-success"> / <div class="alert alert-error">
- WYSIWYG editor (TinyMCE) for rich text fields
- Use settings table (key-value) for simple configs
- Use relational tables for lists (services, portfolio_items, testimonials)

## DaisyUI Admin Form Pattern
Use this consistent pattern for all admin forms:
<div class="card bg-base-100 shadow-xl">
  <div class="card-body">
    <h2 class="card-title">Edit [Section]</h2>
    <div class="form-control">
      <label class="label"><span class="label-text">Field Label</span></label>
      <input type="text" class="input input-bordered w-full" name="field">
    </div>
    <div class="card-actions justify-end mt-4">
      <a href="..." class="btn btn-ghost">Batal</a>
      <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
  </div>
</div>

## DaisyUI Admin Table Pattern
Use this for all index/list pages:
<div class="overflow-x-auto">
  <table class="table table-zebra">
    <thead><tr><th>Col 1</th><th>Aksi</th></tr></thead>
    <tbody>
      @foreach($items as $item)
      <tr>
        <td>{{ $item->title }}</td>
        <td class="flex gap-2">
          <a class="btn btn-sm btn-info">Edit</a>
          <button class="btn btn-sm btn-error">Hapus</button>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

## Generate
1. Migrations for all content tables
2. Models with relationships
3. Admin Controllers (CRUD) for each section
4. Blade views for admin using DaisyUI (index, create, edit)
5. Public HomeController loading all section data
6. Landing page Blade layout with all sections
7. Blade components for each section using DaisyUI
8. ContactController with Laravel Mail notification
```

---

## PROMPT 3 — Shop & Product Management

```
Continue our Laravel project. Now implement the full E-Commerce Shop module.
Use DaisyUI for all public shop and admin product management UI.

## Public Shop Routes
GET  /shop                    → Product catalog + filter
GET  /shop/category/{slug}    → Filter by category
GET  /shop/product/{slug}     → Product detail
POST /cart/add                → Add to cart
GET  /cart                    → View cart
POST /cart/update             → Update quantity
DELETE /cart/remove/{id}      → Remove item
GET  /checkout                → Checkout page (auth required)
POST /checkout                → Process checkout
GET  /orders                  → Customer order history (auth required)
GET  /orders/{code}           → Order detail & tracking

## Public UI with DaisyUI

### Product Card
<div class="card card-compact bg-base-100 shadow-md hover:shadow-xl transition-shadow">
  <figure><img src="{{ $product->thumbnail }}" /></figure>
  <div class="card-body">
    <h2 class="card-title text-sm">{{ $product->name }}</h2>
    <div class="badge badge-secondary badge-sm">{{ $product->category->name }}</div>
    @if($product->sale_price)
      <div class="flex gap-2 items-center">
        <span class="text-primary font-bold">Rp {{ number_format($product->sale_price) }}</span>
        <span class="text-sm line-through text-gray-400">Rp {{ number_format($product->price) }}</span>
      </div>
    @else
      <span class="text-primary font-bold">Rp {{ number_format($product->price) }}</span>
    @endif
    <div class="card-actions justify-end">
      <button class="btn btn-primary btn-sm">+ Keranjang</button>
    </div>
  </div>
</div>

### Cart Page
- DaisyUI: table, badge, input[type=number], btn btn-error, divider
- Show: item list, qty control, subtotal, discount badge, total

### Checkout Page
- DaisyUI: steps (langkah checkout), form-control, card, divider
- Steps: Alamat → Pembayaran → Konfirmasi
- Voucher input field dengan tombol "Terapkan"

### Order Tracking
- DaisyUI: steps steps-vertical, badge, timeline

## Service Classes
Generate these service classes with full implementation:

CartService:
- add(productId, qty): tambah ke session cart
- remove(cartItemId): hapus dari cart
- update(cartItemId, qty): update quantity
- clear(): kosongkan cart
- merge(userId): merge session cart ke DB saat login
- getItems(): ambil semua item + hitung diskon

OrderService:
- create(userId, cartItems, address, voucherId)
- generateCode(): format ORD-YYYYMMDD-XXXXX
- calculateTotals(items, voucherId)

DiscountService:
- applyProductDiscount(product): cek & terapkan diskon aktif
- validateVoucher(code, userId, subtotal): validasi voucher
- applyVoucher(code, subtotal): hitung nilai diskon voucher

## Admin - Product Management
CRUD Products:
- name, slug (auto-generate), SKU, description (WYSIWYG)
- category_id, price, sale_price, weight
- Multiple images (upload & reorder)
- stock, low_stock_threshold, status (active/draft/archived)

CRUD Categories:
- name, slug, image, parent_id (nested category support)

Inventory Management:
- Stock adjustment form: product, qty change, reason
- Inventory log table: product, before, after, change, reason, timestamp

Discount Management:
- type: product/category, discount_type: percentage/fixed
- value, start_date, end_date, status

Voucher Management:
- code, type: percentage/fixed, value
- min_purchase, max_uses, max_uses_per_user
- start_date, end_date, usage_count

## Generate
1. Migrations: products, categories, product_images, carts, cart_items,
   orders, order_items, inventory_logs, discounts, vouchers, voucher_usages
2. All Models with full relationships
3. CartService, OrderService, DiscountService classes
4. Admin Controllers for all resources
5. Public: ShopController, CartController, CheckoutController, OrderController
6. Blade views: catalog, product detail, cart, checkout, order history, order detail
7. Admin Blade views with DaisyUI table, modal, form
8. Admin inventory adjustment form + log history table
```

---

## PROMPT 4 — Payment Gateway (Midtrans)

```
Continue our Laravel project. Now implement Midtrans Snap payment gateway.
Use DaisyUI modal, alert, steps, and loading components for payment flow UI.

## Setup
composer require midtrans/midtrans-php

## .env Variables
MIDTRANS_SERVER_KEY=your-server-key
MIDTRANS_CLIENT_KEY=your-client-key
MIDTRANS_IS_PRODUCTION=false

## config/midtrans.php
return [
    'server_key'    => env('MIDTRANS_SERVER_KEY'),
    'client_key'    => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized'  => true,
    'is_3ds'        => true,
];

## Payment Flow
1. Customer submit checkout
   → Order dibuat dengan status: pending_payment
2. System request Snap Token dari Midtrans API
3. Customer melihat Midtrans Snap popup
4. Pembayaran selesai → Midtrans kirim webhook notification
5. System verifikasi signature → Update status order
6. Stok dikurangi, inventory log dibuat
7. Customer redirect ke halaman sukses

## Order Status Flow
pending_payment → paid → processing → shipped → delivered → completed
                      → cancelled (jika payment gagal/expired)

## Routes
POST /payment/token           → Generate Snap Token (JSON response)
POST /payment/notification    → Midtrans Webhook (exclude from CSRF)
GET  /payment/success         → Redirect sukses
GET  /payment/pending         → Redirect pending
GET  /payment/failed          → Redirect gagal

## Webhook Handler
- Verify Midtrans signature: hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey)
- Handle transaction_status: settlement, capture, deny, cancel, expire, pending
- Update order.payment_status dan order.status accordingly
- Log semua payment notifications ke payment_logs table

## DaisyUI Payment UI

### Tombol Bayar di Checkout
<button id="pay-button" class="btn btn-primary btn-block btn-lg" onclick="pay()">
  <span class="loading loading-spinner loading-sm hidden" id="loading"></span>
  <span id="pay-text">Bayar Sekarang</span>
</button>

<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
function pay() {
  document.getElementById('loading').classList.remove('hidden');
  document.getElementById('pay-text').textContent = 'Memproses...';
  
  fetch('/payment/token', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
    body: JSON.stringify({ order_id: '{{ $order->id }}' })
  })
  .then(r => r.json())
  .then(data => {
    snap.pay(data.token, {
      onSuccess: () => window.location = '/payment/success',
      onPending: () => window.location = '/payment/pending',
      onError:   () => window.location = '/payment/failed',
    });
  });
}
</script>

### Halaman Sukses
<div class="hero min-h-screen bg-base-200">
  <div class="hero-content text-center">
    <div class="max-w-md">
      <div class="text-success text-8xl mb-4">✓</div>
      <h1 class="text-3xl font-bold">Pembayaran Berhasil!</h1>
      <p class="py-4">Order #{{ $order->code }} sedang diproses.</p>
      <a href="/orders/{{ $order->code }}" class="btn btn-primary">Lihat Order</a>
    </div>
  </div>
</div>

### Alert Status di Admin
<div class="alert alert-success">
  <span>Pembayaran diterima — Order #{{ $order->code }}</span>
</div>
<div class="alert alert-warning">
  <span>Menunggu pembayaran — Order #{{ $order->code }}</span>
</div>
<div class="alert alert-error">
  <span>Pembayaran gagal — Order #{{ $order->code }}</span>
</div>

## Generate
1. MidtransService class: createSnapToken(), verifyNotification(), handleWebhook()
2. PaymentController: token(), notification(), success(), pending(), failed()
3. payment_logs migration dan model
4. Update OrderService untuk integrasi payment
5. Checkout Blade view dengan Midtrans Snap.js + DaisyUI loading button
6. Halaman order success, pending, failed dengan DaisyUI hero + alert
7. Admin order list (DaisyUI table + badge status) dan order detail view
8. Admin update status form: processing → shipped (input nomor resi)
```

---

## PROMPT 5 — Admin Panel & Role Management

```
Continue our Laravel project. Now build the complete Custom Admin Panel.
Use DaisyUI drawer, navbar, stat, table, badge, and modal components throughout.

## Admin Authentication
URL Login  : /admin/login
Middleware : EnsureIsAdmin

// app/Http/Middleware/EnsureIsAdmin.php
public function handle(Request $request, Closure $next)
{
    if (!auth()->check() || !auth()->user()->hasAnyRole(['admin', 'super-admin', 'editor'])) {
        return redirect('/admin/login')->with('error', 'Akses ditolak.');
    }
    return $next($request);
}

## Admin Layout — DaisyUI Drawer
// layouts/admin.blade.php
<html data-theme="corporate">
<body>
<div class="drawer lg:drawer-open">
  <input id="admin-drawer" type="checkbox" class="drawer-toggle" />
  
  <div class="drawer-content flex flex-col min-h-screen">
    <!-- Navbar -->
    <div class="navbar bg-base-100 shadow-sm sticky top-0 z-10">
      <label for="admin-drawer" class="btn btn-ghost drawer-button lg:hidden">
        <svg><!-- hamburger icon --></svg>
      </label>
      <span class="flex-1 text-lg font-bold">@yield('page-title', 'Dashboard')</span>
      <div class="dropdown dropdown-end">
        <div tabindex="0" class="avatar btn btn-ghost btn-circle placeholder">
          <div class="bg-primary text-primary-content rounded-full w-8">
            <span>{{ substr(auth()->user()->name, 0, 1) }}</span>
          </div>
        </div>
        <ul tabindex="0" class="menu dropdown-content bg-base-100 rounded-box shadow w-52 mt-3">
          <li class="menu-title"><span>{{ auth()->user()->name }}</span></li>
          <li><a href="/admin/profile">Profil</a></li>
          <li><form method="POST" action="/logout">@csrf<button>Logout</button></form></li>
        </ul>
      </div>
    </div>
    
    <!-- Flash Messages -->
    @if(session('success'))
      <div class="alert alert-success m-4">
        <span>{{ session('success') }}</span>
      </div>
    @endif
    
    <!-- Page Content -->
    <main class="flex-1 p-6">@yield('content')</main>
  </div>

  <!-- Sidebar -->
  <div class="drawer-side z-20">
    <label for="admin-drawer" class="drawer-overlay"></label>
    <div class="bg-base-200 min-h-full w-64 flex flex-col">
      <div class="p-4 bg-primary text-primary-content">
        <h2 class="text-xl font-bold">Admin Panel</h2>
      </div>
      <ul class="menu p-4 flex-1">
        <li><a href="/admin/dashboard">📊 Dashboard</a></li>
        
        <li class="menu-title">Landing Page</li>
        <li><a href="/admin/hero">Hero Section</a></li>
        <li><a href="/admin/services">Services</a></li>
        <li><a href="/admin/portfolio">Portfolio</a></li>
        <li><a href="/admin/testimonials">Testimonials</a></li>
        <li><a href="/admin/contact-settings">Contact</a></li>
        
        <li class="menu-title">Shop</li>
        <li><a href="/admin/products">Produk</a></li>
        <li><a href="/admin/categories">Kategori</a></li>
        <li><a href="/admin/orders">Order</a></li>
        <li><a href="/admin/inventory">Inventory</a></li>
        
        <li class="menu-title">Promosi</li>
        <li><a href="/admin/discounts">Diskon</a></li>
        <li><a href="/admin/vouchers">Voucher</a></li>
        
        <li class="menu-title">Sistem</li>
        <li><a href="/admin/users">Users</a></li>
        <li><a href="/admin/roles">Roles</a></li>
        <li><a href="/admin/settings">Settings</a></li>
      </ul>
    </div>
  </div>
</div>
</body>
</html>

## Dashboard Statistics — DaisyUI Stat
<div class="stats stats-horizontal shadow w-full">
  <div class="stat">
    <div class="stat-figure text-primary"><!-- icon --></div>
    <div class="stat-title">Pendapatan Hari Ini</div>
    <div class="stat-value text-primary">Rp {{ number_format($todayRevenue) }}</div>
    <div class="stat-desc">{{ $todayOrders }} transaksi</div>
  </div>
  <div class="stat">
    <div class="stat-title">Total Order</div>
    <div class="stat-value">{{ $totalOrders }}</div>
    <div class="stat-desc">
      <span class="badge badge-warning badge-sm">{{ $pendingOrders }} pending</span>
    </div>
  </div>
  <div class="stat">
    <div class="stat-title">Total Produk</div>
    <div class="stat-value">{{ $totalProducts }}</div>
    @if($lowStockCount > 0)
    <div class="stat-desc text-error">⚠ {{ $lowStockCount }} stok menipis</div>
    @endif
  </div>
  <div class="stat">
    <div class="stat-title">Total Customer</div>
    <div class="stat-value">{{ $totalCustomers }}</div>
  </div>
</div>

## Role & Permission — Spatie
Roles:
- super-admin → akses penuh
- admin        → kelola shop, order, produk, inventory
- editor       → hanya kelola konten landing page

## DaisyUI Delete Confirmation Modal (Alpine.js)
<div x-data="{ open: false, deleteUrl: '' }">
  <button class="btn btn-sm btn-error"
          @click="open = true; deleteUrl = '/admin/products/{{ $product->id }}'">
    Hapus
  </button>
  
  <dialog class="modal" :class="{ 'modal-open': open }">
    <div class="modal-box">
      <h3 class="font-bold text-lg">Konfirmasi Hapus</h3>
      <p class="py-4">Data yang dihapus tidak dapat dikembalikan.</p>
      <div class="modal-action">
        <form :action="deleteUrl" method="POST">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-error">Ya, Hapus</button>
        </form>
        <button class="btn" @click="open = false">Batal</button>
      </div>
    </div>
    <label class="modal-backdrop" @click="open = false"></label>
  </dialog>
</div>

## Settings Page
General settings via admin:
- site_name, site_tagline, logo, favicon
- contact_address, contact_phone, contact_email, whatsapp_number
- social_facebook, social_instagram, social_twitter, social_youtube
- seo_title, seo_description, seo_image
- currency (IDR), weight_unit (gram)
- maintenance_mode (toggle)

## Generate
1. Admin middleware (EnsureIsAdmin)
2. Admin layout Blade dengan DaisyUI Drawer + sticky navbar
3. AdminDashboardController dengan semua query statistik
4. Dashboard Blade view dengan DaisyUI stat + Chart.js (revenue 30 hari)
5. RoleController & UserController untuk role management
6. SettingsController dengan grouped settings
7. Settings Blade view
8. routes/admin.php dengan semua route definitions
9. Seeder: SuperAdminSeeder (user, roles, permissions, settings)
```

---

## PROMPT 6 — Finalisasi & Deploy Shared Hosting

```
Continue our Laravel project. Now finalize for production deployment on shared hosting (cPanel).
Ensure DaisyUI is properly purged and optimized for production.

## DaisyUI Production Config
// tailwind.config.js
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  plugins: [require("daisyui")],
  daisyui: {
    themes: ["corporate"],  // 1 theme only = smaller CSS
    logs: false,
  }
}

// Build untuk production (auto purge unused DaisyUI classes)
npm run build

## Performance & SEO
- Eager loading pada semua query (eliminasi N+1)
- Cache settings & landing page content: Cache::remember('settings', 3600, fn() => ...)
- Image optimization on upload: intervention/image
- Dynamic meta tags per halaman dari CMS
- Sitemap: composer require spatie/laravel-sitemap

## Security
- CSRF on all forms
- Rate limiting: login (5/min), contact form (3/min), checkout (10/min)
- Validate file uploads: mime type check, max 2MB
- APP_DEBUG=false di production

## Struktur Folder di cPanel

public_html/              ← Upload isi folder /public Laravel
  index.php               ← Edit path (lihat di bawah)
  .htaccess
  build/                  ← Hasil npm run build
  storage/                ← symlink ke laravel_app/storage/app/public

laravel_app/              ← Upload di LUAR public_html
  app/
  bootstrap/
  config/
  database/
  resources/
  routes/
  storage/
  vendor/
  .env
  artisan

## Edit index.php di public_html

<?php
require __DIR__.'/../laravel_app/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel_app/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());
$response->send();
$kernel->terminate($request, $response);

## .htaccess di public_html

<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [L]
</IfModule>

## .env Production

APP_NAME="Nama Website"
APP_ENV=production
APP_KEY=           # php artisan key:generate
APP_DEBUG=false
APP_URL=https://domainmu.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nama_db
DB_USERNAME=user_cpanel
DB_PASSWORD=password_cpanel

MAIL_MAILER=smtp
MAIL_HOST=mail.domainmu.com
MAIL_PORT=465
MAIL_USERNAME=noreply@domainmu.com
MAIL_PASSWORD=password_email
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@domainmu.com

MIDTRANS_SERVER_KEY=your-server-key
MIDTRANS_CLIENT_KEY=your-client-key
MIDTRANS_IS_PRODUCTION=true

QUEUE_CONNECTION=sync
CACHE_STORE=file
SESSION_DRIVER=file

## Deploy Commands (SSH / Terminal cPanel)

cd laravel_app
composer install --optimize-autoloader --no-dev
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=ProductionSeeder
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
chmod -R 755 storage bootstrap/cache

## Cron Job di cPanel
* * * * * php /home/username/laravel_app/artisan schedule:run >> /dev/null 2>&1

## Post-Deploy Checklist
- [ ] Landing page tampil dengan benar
- [ ] Semua asset DaisyUI ter-load (cek network tab)
- [ ] Form kontak berfungsi & email terkirim
- [ ] Register & login customer berjalan
- [ ] Produk tampil di halaman shop
- [ ] Cart berfungsi (add, update, remove)
- [ ] Checkout & pembayaran Midtrans berjalan
- [ ] Webhook Midtrans diterima (cek payment_logs)
- [ ] Admin panel bisa diakses di /admin/login
- [ ] CMS berfungsi (edit, upload gambar)
- [ ] HTTPS aktif (SSL via cPanel)
- [ ] APP_DEBUG=false di .env

## Generate
1. AppServiceProvider boot() untuk production settings
2. Custom error pages (404, 500) dengan DaisyUI hero component
3. .env.example dengan semua variabel yang dibutuhkan
4. deploy.sh script untuk future updates
5. Sitemap configuration
6. Rate limiting di RouteServiceProvider
```
