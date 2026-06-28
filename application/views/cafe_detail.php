<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= html_escape($cafe->cafe_name) ?> - CariCafe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/legacy-cafeid.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-slate-50 pb-24 text-slate-800">
    <?php
        $has_coords = isset($cafe->latitude, $cafe->longitude) && (float)$cafe->latitude != 0.0 && (float)$cafe->longitude != 0.0;
        $has_location = $has_coords || !empty($cafe->address);
        $maps_query = $has_coords
            ? number_format((float)$cafe->latitude, 8, '.', '') . ',' . number_format((float)$cafe->longitude, 8, '.', '')
            : (string)$cafe->address;
        $maps_url = $has_location ? 'https://www.google.com/maps?q=' . rawurlencode($maps_query) : '';
    ?>
    <div class="glass-nav border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button onclick="history.back()" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-500 hover:bg-slate-900 hover:text-white transition"><i class="fa-solid fa-arrow-left"></i></button>
                <h1 class="text-lg font-bold text-slate-900 hidden md:block"><?= html_escape($cafe->cafe_name) ?></h1>
            </div>
            <a href="<?= site_url('home') ?>" class="text-orange-600 font-bold text-xs inline-flex items-center gap-2"><i class="fa-solid fa-house"></i> Beranda</a>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-6 md:py-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <section class="relative bg-white rounded-3xl p-5 md:p-8 shadow-xl border border-slate-100 overflow-hidden">
                    <div class="flex flex-col md:flex-row gap-6 items-center md:items-start">
                        <div class="relative w-full md:w-44 h-56 md:h-44 shrink-0 rounded-2xl bg-gray-100 overflow-hidden shadow-md">
                            <img src="<?= !empty($cafe->image_2) ? $cafe->image_2 : 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=800&q=80' ?>" class="w-full h-full object-cover" alt="Foto kafe">
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <div class="flex w-full items-center justify-between gap-3 mb-2">
                                <div class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-bold uppercase"><i class="fa-solid fa-laptop-file"></i> Reservasi & Order</div>
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="shareCafe()" aria-label="Bagikan kafe<?= $logged_in ? ' dengan link referral' : '' ?>" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-orange-50 text-orange-600 hover:bg-orange-100 hover:scale-105 transition">
                                        <i class="fa-solid fa-share-nodes text-xs"></i>
                                    </button>
                                    <?php if ($has_location): ?>
                                        <a href="<?= $maps_url ?>" target="_blank" rel="noopener noreferrer" aria-label="Buka lokasi di Google Maps" class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100 hover:scale-105 transition">
                                            <i class="fa-solid fa-map text-xs"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <h2 class="text-2xl md:text-4xl font-black text-slate-900 mb-2 leading-tight"><?= html_escape($cafe->cafe_name) ?></h2>
                            <p class="text-slate-500 font-medium text-sm leading-relaxed max-w-lg mb-3"><i class="fa-solid fa-location-dot text-orange-500"></i> <?= html_escape($cafe->address) ?></p>
                            <?php if ($has_location): ?>
                                <a href="<?= $maps_url ?>" target="_blank" rel="noopener noreferrer" class="mb-3 inline-flex items-center gap-2 bg-orange-50 text-orange-600 px-3 py-2 rounded-xl text-xs font-black hover:bg-orange-100 transition">
                                    <i class="fa-solid fa-map-pin"></i>
                                    Buka Maps
                                </a>
                            <?php endif; ?>
                            <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 text-xs font-bold text-slate-600">
                                <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-star text-orange-400"></i> 4.9 Rating</span>
                                <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-wifi text-blue-500"></i> WFC Friendly</span>
                                <span class="inline-flex items-center gap-1.5"><i class="fa-solid fa-utensils text-emerald-500"></i> Menu tersedia</span>
                            </div>
                        </div>
                    </div>
                </section>

                <?php if ($cafe->status_meja == 'penuh'): ?>
                    <div class="p-3 bg-red-100 border border-red-200 text-red-800 text-xs rounded-xl font-bold inline-flex items-center gap-2"><i class="fa-solid fa-triangle-exclamation"></i> Kafe sedang penuh. Pesanan take away tetap tersedia.</div>
                <?php else: ?>
                    <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl font-bold inline-flex items-center gap-2"><i class="fa-solid fa-circle-check"></i> Kursi tersedia untuk dine in atau reservasi.</div>
                <?php endif; ?>

                <section>
                    <div class="flex items-center justify-between mb-4 px-1">
                        <h2 class="text-lg font-black text-slate-800 inline-flex items-center gap-2"><i class="fa-solid fa-mug-hot text-blue-500"></i> Menu Tersedia</h2>
                    </div>

                    <?php foreach ($menu_groups as $group): ?>
                        <?php if (!empty($group['items'])): ?>
                            <div class="mb-6">
                                <h3 class="font-black text-sm text-slate-700 mb-3 uppercase tracking-wide"><?= html_escape($group['category']->nama_kategori) ?></h3>
                                <div class="flex overflow-x-auto hide-scroll pb-3 gap-3 md:grid md:grid-cols-3 lg:grid-cols-4 md:overflow-visible">
                                    <?php foreach ($group['items'] as $menu): ?>
                                        <?php
                                            $menu_image = !empty($menu->image)
                                                ? (preg_match('#^https?://#i', $menu->image)
                                                    ? $menu->image
                                                    : base_url('uploads/' . ltrim($menu->image, '/')))
                                                : 'https://placehold.co/300x300/f8fafc/64748b?text=Menu';
                                        ?>
                                        <div class="w-40 md:w-auto shrink-0 bg-white rounded-2xl p-2.5 shadow-sm border border-slate-100 hover:border-blue-200 transition relative group">
                                            <div class="aspect-square bg-gray-100 rounded-xl mb-2 overflow-hidden">
                                                <img src="<?= $menu_image ?>" class="w-full h-full object-cover" alt="<?= html_escape($menu->menu_name) ?>">
                                            </div>
                                            <h4 class="font-bold text-slate-800 text-xs truncate"><?= html_escape($menu->menu_name) ?></h4>
                                            <p class="text-blue-600 font-black text-xs mt-0.5"><?= format_rupiah($menu->price) ?></p>
                                            <?php if ($logged_in): ?>
                                                <button type="button" onclick="addToCart(<?= $menu->id_menu ?>, '<?= addslashes($menu->menu_name) ?>', <?= $menu->price ?>)" class="mt-2 w-full bg-slate-900 text-white py-1.5 rounded-lg text-[10px] font-bold hover:bg-blue-600 transition active:scale-95">Tambah</button>
                                            <?php else: ?>
                                                <a href="<?= site_url('auth?redirect=' . rawurlencode(site_url('cafe/detail/' . $cafe->id_cafe))) ?>" class="mt-2 w-full bg-slate-900 text-white py-1.5 rounded-lg text-[10px] font-bold hover:bg-blue-600 transition inline-flex justify-center">Login</a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </section>
            </div>

            <aside class="lg:col-span-1">
                <div class="sticky top-24 bg-white rounded-3xl p-6 border border-slate-100 shadow-xl">
                    <?php if (!$logged_in): ?>
                        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-900">
                            <div class="flex items-center gap-2 font-black text-sm"><i class="fa-solid fa-lock"></i><span>Login diperlukan untuk checkout</span></div>
                            <p class="text-xs mt-2 leading-relaxed">Silakan masuk terlebih dahulu agar pesanan dan reservasi dapat diproses.</p>
                            <a href="<?= site_url('auth?redirect=' . rawurlencode(site_url('cafe/detail/' . $cafe->id_cafe))) ?>" class="mt-3 inline-flex items-center gap-2 bg-slate-900 text-white px-3 py-2 rounded-xl text-xs font-black hover:bg-orange-600">
                                <i class="fa-solid fa-right-to-bracket"></i> MASUK SEKARANG
                            </a>
                        </div>
                    <?php else: ?>
                        <form action="<?= site_url('checkout') ?>" method="POST" id="checkout-form">
                            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                            <input type="hidden" name="id_cafe" value="<?= $cafe->id_cafe ?>">
                            <input type="hidden" name="reff_member" value="<?= html_escape($member_reff ?? '') ?>">
                            <input type="hidden" name="cart_items" id="cart_items" value="[]">

                            <h3 class="font-black text-sm text-slate-800 border-b border-gray-100 pb-3 uppercase tracking-wide inline-flex items-center gap-2"><i class="fa-solid fa-chair"></i> Form Booking</h3>
                            <div class="mt-5 flex flex-col gap-4">
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 block mb-1 uppercase">Nama Member</label>
                                    <input value="<?= html_escape($member_name ?: $member_reff) ?>" readonly class="w-full bg-slate-100 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-500">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 block mb-1 uppercase">Pilih Tipe Order</label>
                                    <select name="order_type" id="order_type" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="dine_in">Makan di Tempat</option>
                                        <option value="reservation">Reservasi</option>
                                        <option value="take_away">Bawa Pulang</option>
                                    </select>
                                </div>
                                <div id="booking-details" class="space-y-4">
                                    <div>
                                        <label class="text-[10px] font-black text-slate-400 block mb-1 uppercase">Pilih Nomor Meja</label>
                                        <select name="nomor_meja" id="nomor_meja" class="w-full bg-slate-100 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-700">
                                            <option value="0">Pilih nomor meja</option>
                                            <?php for ($m = 1; $m <= 30; $m++): ?>
                                                <option value="<?= $m ?>"><?= $m ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <!-- Jumlah kursi dihapus - di-handle nanti jika diperlukan -->
                                    <div>
                                        <label class="text-[10px] font-black text-slate-400 block mb-1 uppercase">Durasi Booking (Jam)</label>
                                        <input type="number" name="durasi" id="durasi" min="1" max="24" value="1" class="w-full bg-slate-100 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-700" />
                                    </div>
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 block mb-1 uppercase">Item di Keranjang</label>
                                    <div id="cart-items" class="text-xs font-bold text-gray-500 flex flex-col gap-2 py-2 min-h-[40px]">
                                        <span class="text-gray-400 italic">Keranjang masih kosong...</span>
                                    </div>
                                </div>
                                <div class="bg-slate-900 rounded-xl p-4 text-white">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-bold uppercase">Total Bayar</span>
                                        <span id="cart-total" class="text-lg font-black text-blue-400">Rp 0</span>
                                    </div>
                                </div>
                                <button type="submit" class="w-full py-3.5 bg-blue-600 text-white rounded-xl font-bold text-sm uppercase tracking-wide shadow-lg shadow-blue-200 hover:bg-blue-700 transition"><i class="fa-solid fa-cart-shopping"></i> Lanjut ke Checkout</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
    </div>

    <script>
        async function shareCafe() {
            const shareUrl = <?= json_encode(site_url('cafe/detail/' . (int)$cafe->id_cafe) . ($logged_in ? '?reff=' . rawurlencode($member_reff) : '')) ?>;
            const shareData = {
                title: <?= json_encode($cafe->cafe_name . ' - CariCafe') ?>,
                text: <?= json_encode('Lihat menu dan reservasi di ' . $cafe->cafe_name . ' melalui CariCafe:') ?>,
                url: shareUrl
            };

            try {
                if (navigator.share) {
                    await navigator.share(shareData);
                    return;
                }
                await navigator.clipboard.writeText(shareUrl);
                alert('Link kafe berhasil disalin!');
            } catch (error) {
                if (error.name !== 'AbortError') {
                    window.prompt('Salin link berikut:', shareUrl);
                }
            }
        }

        <?php if (!empty($shared_referral)): ?>
        localStorage.setItem('cafeeid_referral', JSON.stringify({
            code: <?= json_encode($shared_referral) ?>,
            expiresAt: Date.now() + (30 * 24 * 60 * 60 * 1000)
        }));
        <?php endif; ?>

        let cart = [];

        function addToCart(id, name, price) {
            const existing = cart.find(item => item.id === id);
            if (existing) {
                existing.qty += 1;
            } else {
                cart.push({ id, name, price, qty: 1 });
            }
            renderCart();
        }

        function removeFromCart(id) {
            const existing = cart.find(item => item.id === id);
            if (!existing) return;
            existing.qty -= 1;
            if (existing.qty <= 0) {
                cart = cart.filter(item => item.id !== id);
            }
            renderCart();
        }

        function rupiah(value) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
        }

        function toggleBookingDetails() {
            const orderType = document.getElementById('order_type');
            const bookingDetails = document.getElementById('booking-details');
            if (!orderType || !bookingDetails) return;

            bookingDetails.style.display = orderType.value === 'take_away' ? 'none' : 'block';
        }

        document.addEventListener('DOMContentLoaded', function () {
            const orderType = document.getElementById('order_type');
            if (orderType) {
                orderType.addEventListener('change', toggleBookingDetails);
                toggleBookingDetails();
            }
        });

        function renderCart() {
            const cartItems = document.getElementById('cart-items');
            const hidden = document.getElementById('cart_items');
            const totalEl = document.getElementById('cart-total');
            if (!cartItems || !hidden || !totalEl) return;

            if (cart.length === 0) {
                cartItems.innerHTML = '<span class="text-gray-400 italic">Keranjang masih kosong...</span>';
                totalEl.textContent = 'Rp 0';
                hidden.value = JSON.stringify([]);
                return;
            }

            cartItems.innerHTML = cart.map(item => `
                <div class="flex items-center justify-between bg-slate-50 rounded-xl p-2 gap-2">
                    <div class="min-w-0">
                        <div class="font-bold text-slate-800 truncate">${item.name}</div>
                        <div class="text-[10px] text-gray-500">${item.qty} x ${rupiah(item.price)}</div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" onclick="removeFromCart(${item.id})" class="w-6 h-6 rounded bg-white text-slate-600 font-black">-</button>
                        <span class="text-blue-600 font-black">${rupiah(item.price * item.qty)}</span>
                    </div>
                </div>
            `).join('');

            const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            totalEl.textContent = rupiah(total);
            hidden.value = JSON.stringify(cart);
        }
    </script>
</body>
</html>
