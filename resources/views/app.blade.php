<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="bali-craft">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Nunito:wght@300;400;500&display=swap" rel="stylesheet">

    @php
        $themeColors = $page['props']['themeColors'] ?? [];
    @endphp

    @vite(['resources/css/app.css', 'resources/js/inertia.js'])
    @routes
    @include('partials.dynamic-theme')
    @inertiaHead
</head>
<body class="antialiased min-h-screen flex flex-col overflow-x-hidden font-sans">
    @inertia
    <script>
        (function () {
            window.showAppToast = function (message, type = 'success') {
                if (!message) return;

                const now = Date.now();
                const signature = `${type}::${message}`;
                if (window.__lastAppToastSignature === signature && (now - (window.__lastAppToastAt || 0)) < 400) {
                    return;
                }
                window.__lastAppToastSignature = signature;
                window.__lastAppToastAt = now;

                window.dispatchEvent(new CustomEvent('app-toast', {
                    detail: { message, type }
                }));
            };

            const updateCartBadges = (count) => {
                document.querySelectorAll('[data-cart-count-badge]').forEach((badge) => {
                    badge.textContent = count > 0 ? String(count) : '';
                    badge.classList.toggle('hidden', count <= 0);
                    badge.classList.toggle('flex', count > 0);
                });
            };

            const setButtonState = (form, state, text) => {
                const button = form.querySelector('.js-add-to-cart-btn');
                const spinner = form.querySelector('.js-add-to-cart-spinner');
                const label = form.querySelector('.js-add-to-cart-label');

                if (!button) return;

                const isLoading = state === 'loading';
                button.disabled = isLoading;
                button.classList.toggle('opacity-70', isLoading);
                button.classList.toggle('opacity-80', isLoading);
                button.classList.toggle('cursor-wait', isLoading);
                spinner?.classList.toggle('hidden', !isLoading);
                if (label && text) label.textContent = text;
            };

            const setInlineError = (form, message = '') => {
                const box = form.querySelector('.js-add-to-cart-error');
                if (!box) return;
                if (!message) {
                    box.textContent = '';
                    box.classList.add('hidden');
                    return;
                }
                box.textContent = message;
                box.classList.remove('hidden');
            };

            document.addEventListener('submit', async (event) => {
                const form = event.target.closest('.js-add-to-cart-form');
                if (!form) return;

                event.preventDefault();

                const button = form.querySelector('.js-add-to-cart-btn');
                if (button?.disabled) return;

                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const quantityInput = form.querySelector('input[name="quantity"]');
                if (quantityInput && quantityInput.max) {
                    const maxQty = Number(quantityInput.max);
                    const qty = Number(quantityInput.value || 1);
                    if (maxQty > 0 && qty > maxQty) {
                        const msg = `Maksimal ${maxQty} item untuk stok saat ini.`;
                        setInlineError(form, msg);
                        window.showAppToast(msg, 'error');
                        return;
                    }
                }

                setInlineError(form, '');
                setButtonState(form, 'loading', 'Adding...');

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: new FormData(form),
                        credentials: 'same-origin',
                    });

                    const data = await response.json().catch(() => ({}));
                    if (!response.ok || !data.ok) {
                        throw new Error(data.message || 'Failed to add item.');
                    }

                    updateCartBadges(Number(data.cart_count || 0));
                    setButtonState(form, 'idle', data.added_text || 'Added ✓');
                    window.showAppToast(data.message || 'Added ✓', 'success');
                    setInlineError(form, '');

                    setTimeout(() => {
                        setButtonState(form, 'idle', 'Add to Cart');
                    }, 1200);
                } catch (error) {
                    setButtonState(form, 'idle', 'Try Again');
                    const message = error?.message || 'Gagal menambahkan item.';
                    setInlineError(form, message);
                    window.showAppToast(message, 'error');
                }
            }, true);
        })();
    </script>
</body>
</html>
