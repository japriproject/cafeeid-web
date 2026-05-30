<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CariCafe - Jelajahi Kafe Terbaik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/legacy-cafeid.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-slate-50 pb-20">
    <nav class="glass-nav sticky top-0 z-50 border-b border-slate-100 transition-all">
        <div class="max-w-7xl mx-auto px-4 md:px-8 py-4 flex items-center justify-between relative">
            <div class="flex items-center gap-4 md:gap-6 z-20">
                <a href="<?= site_url('home') ?>" class="flex items-center gap-2">
                    <span class="text-xl font-black text-slate-900 tracking-tight">Cari<span class="text-orange-600">Cafe</span>.</span>
                </a>
                <div class="relative group">
                    <button type="button" class="flex items-center gap-2 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 px-3 py-2 md:px-4 rounded-full transition cursor-pointer">
                        <i class="fa-solid fa-location-dot text-sm"></i>
                        <span class="text-[10px] md:text-xs font-bold uppercase tracking-wide truncate max-w-[110px]"><?= html_escape($city ?: 'Pilih Lokasi') ?></span>
                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                    </button>
                    <div class="invisible opacity-0 translate-y-2 group-hover:visible group-hover:opacity-100 group-hover:translate-y-0 focus-within:visible focus-within:opacity-100 focus-within:translate-y-0 transition absolute top-full left-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden z-50">
                        <div class="p-2 space-y-1 max-h-60 overflow-y-auto custom-scroll">
                            <a href="<?= site_url('home') ?>" class="block px-4 py-2 rounded-xl text-xs font-bold <?= empty($city) ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:bg-slate-50' ?>">Semua Kota</a>
                            <?php foreach (($cities ?? array()) as $city_row): ?>
                                <a href="<?= site_url('home?city=' . rawurlencode($city_row->kota)) ?>" class="block px-4 py-2 rounded-xl text-xs font-bold <?= $city === $city_row->kota ? 'bg-orange-50 text-orange-600' : 'text-slate-600 hover:bg-slate-50' ?>"><?= html_escape($city_row->kota) ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hidden md:flex absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 gap-8 text-sm font-bold text-slate-500">
                <a href="<?= site_url('home') ?>" class="text-orange-600 transition">Beranda</a>
                <a href="<?= site_url('cari') ?>" class="hover:text-orange-600 transition">Cari Cafe</a>
                <a href="<?= site_url('promo') ?>" class="hover:text-orange-600 transition">Promo</a>
                <a href="<?= site_url('profile') ?>" class="hover:text-orange-600 transition">Tiket Saya</a>
            </div>

            <div class="z-20">
                <a href="<?= site_url('profile') ?>" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center hover:bg-orange-100 text-slate-600 transition" aria-label="Profil">
                    <i class="fa-solid fa-user"></i>
                </a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 py-0 md:py-4 min-h-screen">
        <section class="mobile-edge-hero relative h-[380px] md:h-[500px] bg-slate-900 md:rounded-3xl overflow-hidden shadow-2xl group">
            <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?q=80&w=1920&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:scale-105 transition duration-1000 ease-in-out">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-slate-900/50"></div>

            <div class="relative z-10 h-full flex flex-col justify-center items-center text-center px-4 sm:px-6">
                <h1 class="text-2xl sm:text-3xl md:text-6xl font-black text-white mb-4 sm:mb-6 tracking-tight leading-tight">
                    Cafe Terbaik di <br>
                    <span class="text-orange-500"><?= html_escape($city ?: 'Kotamu') ?></span>
                </h1>

                <form action="<?= site_url('home') ?>" method="GET" class="w-full max-w-2xl bg-white/10 backdrop-blur-md border border-white/20 p-1.5 rounded-2xl sm:rounded-full flex flex-col sm:flex-row items-stretch gap-2 shadow-2xl transition hover:bg-white/20">
                    <?php if (!empty($city)): ?><input type="hidden" name="city" value="<?= html_escape($city) ?>"><?php endif; ?>
                    <div class="pl-3 text-white/70 self-start sm:self-center mt-2 sm:mt-0"><i class="fa-solid fa-magnifying-glass"></i></div>
                    <input type="text" name="search" placeholder="Cari nama cafe..." value="<?= html_escape($search ?? '') ?>" class="flex-1 bg-transparent outline-none text-sm md:text-base font-medium text-white px-2 placeholder:text-white/70 min-h-10">
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-4 sm:px-6 min-h-10 rounded-xl sm:rounded-full font-bold text-sm uppercase tracking-wide transition shadow-lg shadow-orange-500/30 w-full sm:w-auto">Cari</button>
                </form>
            </div>
        </section>

        <section class="px-0 md:px-2 -mt-5 sm:-mt-8 md:-mt-10 relative z-20 grid grid-cols-3 gap-2 sm:gap-3 md:gap-4 max-w-2xl mx-auto">
            <a href="<?= site_url('home') ?>" class="bg-white rounded-2xl p-3 md:p-6 shadow-xl border border-slate-100 flex flex-col items-center gap-2 hover:-translate-y-1 transition duration-300">
                <div class="w-10 h-10 md:w-14 md:h-14 rounded-full md:rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center text-lg md:text-2xl"><i class="fa-solid fa-laptop-file"></i></div>
                <span class="font-bold text-slate-800 text-[10px] md:text-sm text-center">Booking</span>
            </a>
            <a href="<?= site_url('home') ?>" class="bg-white rounded-2xl p-3 md:p-6 shadow-xl border border-slate-100 flex flex-col items-center gap-2 hover:-translate-y-1 transition duration-300">
                <div class="w-10 h-10 md:w-14 md:h-14 rounded-full md:rounded-2xl bg-green-50 text-green-500 flex items-center justify-center text-lg md:text-2xl"><i class="fa-solid fa-percent"></i></div>
                <span class="font-bold text-slate-800 text-[10px] md:text-sm text-center">Promo</span>
            </a>
            <a href="<?= site_url('home') ?>" class="bg-white rounded-2xl p-3 md:p-6 shadow-xl border border-slate-100 flex flex-col items-center gap-2 hover:-translate-y-1 transition duration-300">
                <div class="w-10 h-10 md:w-14 md:h-14 rounded-full md:rounded-2xl bg-purple-50 text-purple-500 flex items-center justify-center text-lg md:text-2xl"><i class="fa-solid fa-book-open"></i></div>
                <span class="font-bold text-slate-800 text-[10px] md:text-sm text-center">Panduan</span>
            </a>
        </section>

        <section class="mt-8 md:mt-12">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-[10px] sm:text-xs font-black uppercase tracking-[0.24em] text-slate-500">Lagi Pengen Suasana Apa?</h2>
            </div>
            <div class="flex gap-3 overflow-x-auto hide-scroll pb-2">
                <a href="<?= site_url('home?search=work' . ($city ? '&city=' . rawurlencode($city) : '')) ?>" class="shrink-0 flex items-center gap-2 bg-white border border-slate-200 px-4 py-3 rounded-full shadow-sm hover:border-blue-500 hover:text-blue-600 transition group text-slate-700 font-bold text-xs">
                    <span class="w-6 h-6 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xs group-hover:bg-blue-500 group-hover:text-white transition"><i class="fa-solid fa-wifi"></i></span>
                    WFC Friendly
                </a>
                <a href="<?= site_url('home?search=outdoor' . ($city ? '&city=' . rawurlencode($city) : '')) ?>" class="shrink-0 flex items-center gap-2 bg-white border border-slate-200 px-4 py-3 rounded-full shadow-sm hover:border-green-500 hover:text-green-600 transition group text-slate-700 font-bold text-xs">
                    <span class="w-6 h-6 rounded-full bg-green-50 text-green-500 flex items-center justify-center text-xs group-hover:bg-green-500 group-hover:text-white transition"><i class="fa-solid fa-tree"></i></span>
                    Outdoor
                </a>
                <a href="<?= site_url('home?search=aesthetic' . ($city ? '&city=' . rawurlencode($city) : '')) ?>" class="shrink-0 flex items-center gap-2 bg-white border border-slate-200 px-4 py-3 rounded-full shadow-sm hover:border-pink-500 hover:text-pink-600 transition group text-slate-700 font-bold text-xs">
                    <span class="w-6 h-6 rounded-full bg-pink-50 text-pink-500 flex items-center justify-center text-xs group-hover:bg-pink-500 group-hover:text-white transition"><i class="fa-solid fa-camera"></i></span>
                    Aesthetic
                </a>
                <a href="<?= site_url('home?search=smoking' . ($city ? '&city=' . rawurlencode($city) : '')) ?>" class="shrink-0 flex items-center gap-2 bg-white border border-slate-200 px-4 py-3 rounded-full shadow-sm hover:border-gray-500 hover:text-gray-700 transition group text-slate-700 font-bold text-xs">
                    <span class="w-6 h-6 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-xs group-hover:bg-gray-500 group-hover:text-white transition"><i class="fa-solid fa-smoking"></i></span>
                    Smoking Area
                </a>
            </div>
        </section>

        <section id="promo" class="mt-6">
            <a href="<?= site_url('home') ?>" class="block w-full bg-gradient-to-r from-slate-900 to-slate-800 rounded-2xl p-4 sm:p-6 shadow-xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-24 h-24 sm:w-32 sm:h-32 bg-orange-500 rounded-full blur-3xl opacity-20 group-hover:opacity-40 transition -mr-8 -mt-8 sm:-mr-10 sm:-mt-10"></div>
                <div class="relative z-10 flex flex-col sm:flex-row gap-3 sm:justify-between sm:items-center">
                    <div>
                        <span class="bg-orange-500 text-white text-[9px] font-bold px-2 py-1 rounded mb-2 inline-block">SPESIAL HARI INI</span>
                        <h3 class="text-white font-black text-lg sm:text-xl mb-1">Diskon 50% All Menu</h3>
                        <p class="text-slate-400 text-xs">Khusus reservasi kedatangan. Cek kodenya sekarang!</p>
                    </div>
                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-white/10 rounded-full flex items-center justify-center text-white group-hover:scale-110 transition backdrop-blur-md self-start">
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </div>
            </a>
        </section>

        <section class="mt-8 sm:mt-10">
            <div class="flex items-center justify-between mb-4 md:mb-6">
                <div>
                    <h2 class="font-black text-slate-900 text-lg md:text-2xl">Terpopuler</h2>
                    <p class="text-slate-500 text-[10px] md:text-sm mt-0.5">Pilihan cafe hits dari berbagai kota</p>
                </div>
                <a href="<?= site_url('home') ?>" class="text-orange-600 font-bold text-xs md:text-sm hover:underline">Lihat Semua</a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3 md:gap-6">
                <?php if (empty($cafes)): ?>
                    <div class="col-span-full bg-white rounded-3xl p-8 text-center text-slate-400 border border-slate-200">Belum ada kafe terdaftar.</div>
                <?php else: ?>
                    <?php foreach ($cafes as $row): ?>
                        <a href="<?= site_url('cafe/detail/' . $row->id_cafe) ?>" class="group bg-white rounded-3xl p-2.5 md:p-3 shadow-sm border border-slate-100 hover:shadow-xl hover:border-orange-100 transition duration-300">
                            <div class="aspect-square rounded-xl md:rounded-2xl overflow-hidden mb-2.5 bg-gray-100 relative">
                                <img src="<?= !empty($row->image_2) ? $row->image_2 : 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?q=80&w=600&auto=format&fit=crop' ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                                <div class="absolute top-2 left-2 bg-white/90 backdrop-blur-sm text-slate-900 text-[9px] md:text-[10px] font-bold px-1.5 py-0.5 rounded-lg flex items-center gap-1 shadow-sm">
                                    <i class="fa-solid fa-star text-orange-400"></i> 4.9
                                </div>
                            </div>
                            <div class="px-1 pb-1">
                                <h3 class="font-bold text-slate-900 text-sm md:text-sm truncate group-hover:text-orange-600 transition"><?= html_escape($row->cafe_name) ?></h3>
                                <p class="text-[10px] md:text-xs text-slate-500 flex items-center gap-1 mt-1">
                                    <i class="fa-solid fa-location-dot text-[8px] md:text-[10px]"></i>
                                    <?= html_escape($row->address) ?>
                                </p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section class="mt-8 sm:mt-10">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-black text-slate-900 text-lg">Jelajah Lokasi</h3>
                    <p class="text-slate-500 text-[10px] mt-0.5">Temukan vibe sesuai daerah favoritmu</p>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
                <?php foreach (array_slice(($cities ?? array()), 0, 6) as $city_row): ?>
                    <a href="<?= site_url('home?city=' . rawurlencode($city_row->kota)) ?>" class="bg-white border border-slate-100 rounded-xl p-3 sm:p-4 shadow-sm flex items-center gap-3 text-slate-700 font-bold text-[11px] sm:text-xs hover:border-blue-300 hover:text-blue-600 transition group">
                        <span class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-xs shrink-0 group-hover:bg-blue-600 group-hover:text-white transition"><i class="fa-solid fa-location-dot"></i></span>
                        <span><span class="block"><?= html_escape($city_row->kota) ?></span><span class="block text-[9px] text-slate-400">Lihat Cafe</span></span>
                    </a>
                <?php endforeach; ?>
                <a href="<?= site_url('home') ?>" class="bg-white border border-slate-100 rounded-2xl p-3 sm:p-4 shadow-sm flex items-center gap-2 text-slate-700 font-bold text-[11px] sm:text-xs hover:border-orange-200 hover:text-orange-600 transition">
                    <span class="w-8 h-8 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center text-xs"><i class="fa-solid fa-map"></i></span>
                    Semua Kota
                </a>
            </div>
        </section>

        <section class="mt-10 flex justify-between items-center text-xs font-bold text-slate-500">
            <span>Halaman <?= $page ?> dari <?= $total_pages ?></span>
            <div class="flex gap-2">
                <?php if ($page > 1): ?>
                    <a class="px-3 py-2 bg-white rounded-xl border inline-flex items-center justify-center" href="<?= site_url('home?page=' . ($page - 1) . ($search ? '&search=' . urlencode($search) : '') . ($city ? '&city=' . urlencode($city) : '')) ?>"><i class="fa-solid fa-chevron-left"></i></a>
                <?php endif; ?>
                <?php if ($page < $total_pages): ?>
                    <a class="px-3 py-2 bg-white rounded-xl border inline-flex items-center justify-center" href="<?= site_url('home?page=' . ($page + 1) . ($search ? '&search=' . urlencode($search) : '') . ($city ? '&city=' . urlencode($city) : '')) ?>"><i class="fa-solid fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer class="bg-slate-900 text-white pt-8 sm:pt-10 pb-24 md:pb-10 mt-8">
        <div class="max-w-6xl mx-auto px-4 md:px-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 md:gap-8">
            <div>
                <h2 class="text-xl font-black">CariCafe<span class="text-orange-500">.</span></h2>
                <p class="text-slate-400 text-sm mt-3 leading-relaxed">Platform reservasi dan booking meja cafe termudah di kotamu. Temukan tempat, pesan menu, dan nikmati suasana terbaik.</p>
            </div>
            <div>
                <h4 class="font-bold text-sm uppercase tracking-wide mb-3">Navigasi</h4>
                <ul class="space-y-2 text-sm text-slate-300">
                    <li><a href="<?= site_url('home') ?>" class="hover:text-white">Beranda</a></li>
                    <li><a href="<?= site_url('cari') ?>" class="hover:text-white">Cari Cafe</a></li>
                    <li><a href="<?= site_url('promo') ?>" class="hover:text-white">Promo</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-sm uppercase tracking-wide mb-3">Hubungi Kami</h4>
                <ul class="space-y-2 text-sm text-slate-300">
                    <li class="flex items-center gap-2"><i class="fa-solid fa-phone text-orange-400"></i> +62 812-3456-7890</li>
                    <li class="flex items-center gap-2"><i class="fa-solid fa-envelope text-orange-400"></i> help@caricafe.id</li>
                    <li class="flex items-center gap-2"><i class="fa-brands fa-instagram text-orange-400"></i> @caricafe.id</li>
                </ul>
            </div>
        </div>
        <div class="max-w-6xl mx-auto px-4 md:px-6 mt-8 pt-4 border-t border-white/10 text-xs text-slate-500">
            © 2026 CariCafe Indonesia. All rights reserved.
        </div>
    </footer>
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white/90 backdrop-blur-lg border-t border-slate-200 px-6 py-3 flex justify-between items-center z-50 pb-safe mobile-bottom-nav">
        <a href="<?= site_url('home') ?>" class="text-orange-600 flex flex-col items-center gap-1 group">
            <div class="text-xl group-active:scale-90 transition"><i class="fa-solid fa-house"></i></div>
            <span class="text-[9px] font-bold uppercase tracking-wider">Beranda</span>
        </a>
        <a href="<?= site_url('cari') ?>" class="text-slate-400 hover:text-slate-600 flex flex-col items-center gap-1 group">
            <div class="text-xl group-active:scale-90 transition"><i class="fa-solid fa-magnifying-glass"></i></div>
            <span class="text-[9px] font-bold uppercase tracking-wider">Cari</span>
        </a>
        <div class="relative -top-6">
            <a href="<?= site_url('profile') ?>" class="w-14 h-14 bg-slate-900 rounded-full text-white flex items-center justify-center text-xl shadow-lg shadow-slate-300 hover:bg-orange-600 transition group active:scale-90">
                <i class="fa-solid fa-ticket"></i>
            </a>
        </div>
        <a href="<?= site_url('promo') ?>" class="text-slate-400 hover:text-slate-600 flex flex-col items-center gap-1 group">
            <div class="text-xl group-active:scale-90 transition"><i class="fa-solid fa-percent"></i></div>
            <span class="text-[9px] font-bold uppercase tracking-wider">Promo</span>
        </a>
        <a href="<?= site_url('profile') ?>" class="text-slate-400 hover:text-slate-600 flex flex-col items-center gap-1 group">
            <div class="text-xl group-active:scale-90 transition"><i class="fa-solid fa-user"></i></div>
            <span class="text-[9px] font-bold uppercase tracking-wider">Profil</span>
        </a>
    </div>
</body>
</html>
