<script setup>
import ProductDescriptionEditor from '@/Components/Admin/ProductDescriptionEditor.vue';
import ProductMediaPicker from '@/Components/Admin/ProductMediaPicker.vue';
import { Link, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const gallery = defineModel('gallery', { type: Array, default: () => [] });

defineProps({
    form: { type: Object, required: true },
    categories: { type: Array, required: true },
    mode: { type: String, required: true },
    existingImages: { type: Array, default: () => [] },
});

function deleteExistingImage(img) {
    if (!confirm('Hapus gambar ini?')) {
        return;
    }
    router.delete(route('admin.products.images.destroy', { product: img.product_id, image: img.id }), {
        preserveScroll: true,
    });
}
</script>

<template>
    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-4">
                    <h2 class="card-title text-base">Informasi Dasar</h2>

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Nama Produk <span class="text-error">*</span></legend>
                        <input v-model="form.name" type="text" class="input w-full" :class="{ 'input-error': form.errors.name }" required />
                        <p v-if="form.errors.name" class="fieldset-label text-error">{{ form.errors.name }}</p>
                    </fieldset>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">SKU <span class="text-error">*</span></legend>
                            <input v-model="form.sku" type="text" class="input w-full" :class="{ 'input-error': form.errors.sku }" required />
                            <p v-if="form.errors.sku" class="fieldset-label text-error">{{ form.errors.sku }}</p>
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Kategori</legend>
                            <select v-model="form.category_id" class="select w-full">
                                <option value="">— Tanpa Kategori —</option>
                                <option v-for="cat in categories" :key="cat.id" :value="String(cat.id)">{{ cat.name }}</option>
                            </select>
                        </fieldset>
                    </div>

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Deskripsi Singkat</legend>
                        <textarea v-model="form.short_description" rows="3" class="textarea w-full" />
                    </fieldset>

                    <ProductDescriptionEditor v-model="form.description" :error="form.errors.description" />

                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Spesifikasi</legend>
                        <textarea
                            v-model="form.specifications_text"
                            rows="5"
                            class="textarea w-full"
                            :class="{ 'textarea-error': form.errors.specifications_text }"
                            placeholder="Material: Sterling Silver 925&#10;Stone: Zircon&#10;Finish: Polished"
                        />
                        <p class="fieldset-label">Satu baris per spesifikasi dengan format: Label: Nilai</p>
                        <p v-if="form.errors.specifications_text" class="fieldset-label text-error">{{ form.errors.specifications_text }}</p>
                    </fieldset>
                </div>
            </div>

            <div v-if="mode === 'edit' && existingImages.length" class="card bg-base-100 shadow-sm">
                <div class="card-body gap-3">
                    <h2 class="card-title text-base">Gambar Saat Ini</h2>
                    <div class="flex flex-wrap gap-3">
                        <div v-for="img in existingImages" :key="img.id" class="group relative">
                            <img
                                :src="img.url"
                                :alt="img.alt_text"
                                class="h-20 w-20 rounded border border-base-300 object-cover"
                                :class="img.is_primary ? 'ring-2 ring-primary' : ''"
                            />
                            <span
                                v-if="img.is_primary"
                                class="absolute bottom-0 left-0 right-0 bg-primary py-0.5 text-center text-[9px] text-primary-content"
                                >Utama</span
                            >
                            <button
                                type="button"
                                class="btn btn-error btn-xs absolute right-0 top-0 h-5 min-h-0 w-5 rounded-full p-0 opacity-0 transition-opacity group-hover:opacity-100"
                                @click="deleteExistingImage(img)"
                            >
                                ×
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-3">
                    <h2 class="card-title text-base">{{ mode === 'edit' ? 'Tambah Gambar Baru' : 'Gambar Produk' }}</h2>
                    <p class="text-sm text-base-content/60">
                        {{
                            mode === 'edit'
                                ? 'Pilih gambar dari media library. Gambar pertama dari daftar ini jadi utama jika produk belum punya gambar utama.'
                                : 'Klik gambar dari media library. Pilihan pertama otomatis jadi gambar utama, berikutnya jadi galeri.'
                        }}
                    </p>
                    <ProductMediaPicker
                        v-model="gallery"
                        :primary-label="mode === 'edit' ? 'Utama Baru' : 'Utama'"
                        :error="form.errors.media_image_urls_json"
                    />
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-3">
                    <h2 class="card-title text-base">Harga & Biaya</h2>
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Harga Jual (Rp) <span class="text-error">*</span></legend>
                        <input v-model="form.price" type="number" step="1" min="0" class="input w-full" :class="{ 'input-error': form.errors.price }" required />
                        <p v-if="form.errors.price" class="fieldset-label text-error">{{ form.errors.price }}</p>
                    </fieldset>
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Harga Modal (Rp)</legend>
                        <input v-model="form.cost_price" type="number" step="1" min="0" class="input w-full" />
                    </fieldset>
                </div>
            </div>

            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-3">
                    <h2 class="card-title text-base">Stok & Berat</h2>
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">{{ mode === 'edit' ? 'Stok' : 'Stok Awal' }}</legend>
                        <input v-model="form.stock" type="number" min="0" class="input w-full" />
                    </fieldset>
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Alert Stok Minimum</legend>
                        <input v-model="form.min_stock_alert" type="number" min="0" class="input w-full" />
                    </fieldset>
                    <fieldset class="fieldset">
                        <legend class="fieldset-legend">Berat (gram)</legend>
                        <input v-model="form.weight" type="number" step="0.01" min="0" class="input w-full" />
                    </fieldset>
                </div>
            </div>

            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-3">
                    <h2 class="card-title text-base">Status</h2>
                    <div class="flex items-center gap-3">
                        <input id="is_active" v-model="form.is_active" type="checkbox" class="toggle toggle-success" :true-value="true" :false-value="false" />
                        <label for="is_active" class="text-sm">{{ mode === 'edit' ? 'Aktif' : 'Aktif (tampil di toko)' }}</label>
                    </div>
                    <div class="flex items-center gap-3">
                        <input id="is_featured" v-model="form.is_featured" type="checkbox" class="toggle toggle-accent" :true-value="true" :false-value="false" />
                        <label for="is_featured" class="text-sm">Produk Unggulan</label>
                    </div>
                </div>
            </div>

            <Link :href="route('admin.products.index')" class="btn btn-ghost btn-block btn-sm">Batal</Link>
            <button type="submit" class="btn btn-primary w-full" :disabled="form.processing">
                {{ mode === 'edit' ? 'Perbarui Produk' : 'Simpan Produk' }}
            </button>
        </div>
    </div>
</template>
