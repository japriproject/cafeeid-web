<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Cafe - OLONA</title>
    <link rel="icon" type="image/jpeg" href="<?= base_url('assets/logo.jpg') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/legacy-cafeid.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="text-slate-800 bg-slate-50 pb-24">
    <nav class="hidden md:flex items-center justify-between px-8 py-4 glass-nav sticky top-0 z-50 border-b border-slate-100">
        <a href="<?= site_url('home') ?>" class="text-xl font-black text-slate-900 tracking-tight">OLONA<span class="text-orange-600">.</span></a>
        <div class="flex gap-6 text-sm font-bold text-slate-500">
            <a href="<?= site_url('home') ?>" class="hover:text-orange-600 transition">Beranda</a>
            <a href="<?= site_url('cari') ?>" class="text-orange-600 transition">Cari Cafe</a>
            <a href="<?= site_url('promo') ?>" class="hover:text-orange-600 transition">Promo</a>
        </div>
        <a href="<?= site_url('profile') ?>" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center hover:bg-orange-100 text-slate-600 transition"><i class="fa-solid fa-user"></i></a>
    </nav>

    <div class="md:hidden bg-white sticky top-0 z-50 border-b border-gray-100 shadow-sm">
        <div class="px-4 py-3 space-y-3">
            <form action="<?= site_url('cari') ?>" method="GET" class="flex flex-col gap-3">
                <div class="relative w-full">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="q" value="<?= html_escape($q) ?>" placeholder="Cari cafe di kotamu..." class="w-full bg-slate-50 border-none rounded-2xl py-3 pl-10 pr-4 text-sm font-bold text-slate-800 outline-none focus:ring-2 focus:ring-orange-100 transition">
                </div>
                <div class="relative w-full">
                    <i class="fa-solid fa-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-orange-500"></i>
                    <select name="loc" onchange="this.form.submit()" class="w-full bg-slate-50 border-none rounded-2xl py-3 pl-10 pr-8 text-sm font-bold text-slate-800 outline-none focus:ring-2 focus:ring-orange-100 appearance-none cursor-pointer">
                        <option value="">Semua Lokasi</option>
                        <?php foreach (($cities ?? array()) as $city): ?>
                            <option value="<?= html_escape($city->kota) ?>" <?= $loc === $city->kota ? 'selected' : '' ?>><?= html_escape($city->kota) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                </div>
            </form>
            <div class="flex gap-2 overflow-x-auto hide-scroll">
                <a href="<?= site_url('cari?q=kopi' . ($loc ? '&loc=' . rawurlencode($loc) : '')) ?>" class="bg-white border border-gray-200 text-slate-600 px-4 py-1.5 rounded-full text-[10px] font-bold whitespace-nowrap">Kopi</a>
                <a href="<?= site_url('cari?q=outdoor' . ($loc ? '&loc=' . rawurlencode($loc) : '')) ?>" class="bg-white border border-gray-200 text-slate-600 px-4 py-1.5 rounded-full text-[10px] font-bold whitespace-nowrap">Outdoor</a>
                <a href="<?= site_url('cari?q=murah' . ($loc ? '&loc=' . rawurlencode($loc) : '')) ?>" class="bg-white border border-gray-200 text-slate-600 px-4 py-1.5 rounded-full text-[10px] font-bold whitespace-nowrap">Murah</a>
            </div>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 py-6 md:py-10">
        <div class="hidden md:flex flex-col items-center justify-center mb-10">
            <h1 class="text-3xl font-black text-slate-900 mb-6">Cari Cafe di Kotamu</h1>
            <form action="<?= site_url('cari') ?>" method="GET" class="w-full max-w-2xl flex gap-3">
                <input type="text" name="q" value="<?= html_escape($q) ?>" placeholder="Nama cafe..." class="flex-1 bg-white border border-gray-200 shadow-sm rounded-full py-3 px-6 text-sm font-bold outline-none focus:ring-2 focus:ring-orange-500">
                <select name="loc" class="w-1/3 bg-white border border-gray-200 shadow-sm rounded-full py-3 px-6 text-sm font-bold outline-none cursor-pointer">
                    <option value="">Semua Lokasi</option>
                    <?php foreach (($cities ?? array()) as $city): ?>
                        <option value="<?= html_escape($city->kota) ?>" <?= $loc === $city->kota ? 'selected' : '' ?>><?= html_escape($city->kota) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="bg-slate-900 text-white px-8 py-3 rounded-full font-bold text-sm hover:bg-orange-600 transition">Cari</button>
            </form>
        </div>

        <h2 class="font-extrabold text-xs md:text-lg text-slate-800 mb-4 uppercase tracking-wider">Hasil Pencarian</h2>

        <?php if (!empty($cafes)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-6">
                <?php foreach ($cafes as $row): ?>
                    <a href="<?= site_url('cafe/detail/' . $row->id_cafe) ?>" class="flex gap-3 bg-white p-2.5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition group">
                        <div class="w-20 h-20 md:w-24 md:h-24 shrink-0 rounded-xl overflow-hidden bg-gray-100 relative">
                            <img src="<?= !empty($row->image_2) ? $row->image_2 : base_url('assets/default_cafe.jpg') ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500" alt="">
                            <div class="absolute bottom-1 right-1 bg-black/50 backdrop-blur px-1.5 py-0.5 rounded flex items-center gap-1">
                                <i class="fa-solid fa-star text-[8px] text-orange-400"></i>
                                <span class="text-[8px] font-bold text-white">4.9</span>
                            </div>
                        </div>
                        <div class="flex-1 flex flex-col justify-center min-w-0">
                            <h3 class="font-bold text-sm text-slate-900 truncate mb-1 group-hover:text-orange-600 transition"><?= html_escape($row->cafe_name) ?></h3>
                            <p class="text-[10px] text-slate-500 font-medium flex items-center gap-1.5">
                                <i class="fa-solid fa-location-dot text-orange-500 w-3"></i>
                                <span class="truncate"><?= html_escape(!empty($row->kota) ? $row->kota : $row->address) ?></span>
                            </p>
                            <p class="text-[10px] text-slate-500 font-medium flex items-center gap-1.5 mt-1">
                                <i class="fa-solid fa-location-arrow text-blue-500 w-3"></i>
                                <span>Siap reservasi hari ini</span>
                            </p>
                        </div>
                        <div class="flex items-center pr-1">
                            <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-slate-300 group-hover:bg-slate-900 group-hover:text-white transition">
                                <i class="fa-solid fa-chevron-right text-xs"></i>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center text-gray-300 mb-4">
                    <i class="fa-solid fa-mug-hot text-3xl"></i>
                </div>
                <p class="text-slate-800 font-bold text-sm">Yah, tidak ketemu...</p>
                <p class="text-slate-400 text-xs mt-1">Coba ubah lokasi atau kata kunci.</p>
            </div>
        <?php endif; ?>
    </main>

    <?php $active = 'cari'; include APPPATH . 'views/partials/mobile_nav.php'; ?>
</body>
</html>
