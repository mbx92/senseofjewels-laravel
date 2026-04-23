@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">Contact Settings</h1>
    <p class="text-sm text-base-content/60">Kelola informasi kontak yang tampil di halaman depan.</p>
</div>

<form action="{{ route('admin.contact-settings.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body space-y-4">
            <h2 class="card-title">Edit Contact Info</h2>

            <div class="form-control">
                <label class="label"><span class="label-text">Alamat</span></label>
                <textarea name="contact_address" rows="3"
                    class="textarea textarea-bordered w-full @error('contact_address') textarea-error @enderror"
                    placeholder="Jl. Contoh No.1, Jakarta">{{ old('contact_address', $settings->get('contact_address')) }}</textarea>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="form-control">
                    <label class="label"><span class="label-text">Nomor Telepon</span></label>
                    <input type="text" name="contact_phone"
                        value="{{ old('contact_phone', $settings->get('contact_phone')) }}"
                        class="input input-bordered w-full @error('contact_phone') input-error @enderror"
                        placeholder="+62 812 3456 7890">
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">Email Kontak</span></label>
                    <input type="email" name="contact_email"
                        value="{{ old('contact_email', $settings->get('contact_email')) }}"
                        class="input input-bordered w-full @error('contact_email') input-error @enderror"
                        placeholder="hello@example.com">
                    @error('contact_email')
                        <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                    @enderror
                </div>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">WhatsApp Number</span></label>
                <input type="text" name="contact_whatsapp"
                    value="{{ old('contact_whatsapp', $settings->get('contact_whatsapp')) }}"
                    class="input input-bordered w-full"
                    placeholder="628123456789 (tanpa + dan spasi)">
                <label class="label"><span class="label-text-alt">Format internasional, e.g. 628123456789</span></label>
            </div>

            <div class="form-control">
                <label class="label"><span class="label-text">Google Maps Embed URL / iFrame Src</span></label>
                <textarea name="contact_maps_embed" rows="3"
                    class="textarea textarea-bordered w-full font-mono text-xs"
                    placeholder="https://www.google.com/maps/embed?pb=...">{{ old('contact_maps_embed', $settings->get('contact_maps_embed')) }}</textarea>
                <label class="label"><span class="label-text-alt">Tempel URL dari bagian &lt;iframe src="..."&gt; Google Maps</span></label>
            </div>

            <div class="card-actions justify-end mt-4">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>
@endsection
