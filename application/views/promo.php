<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promo Spesial - OLONA</title>
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
            <a href="<?= site_url('cari') ?>" class="hover:text-orange-600 transition">Cari Cafe</a>
            <a href="<?= site_url('promo') ?>" class="text-orange-600 transition">Promo</a>
        </div>
        <a href="<?= site_url('profile') ?>" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center hover:bg-orange-100 text-slate-600 transition"><i class="fa-solid fa-user"></i></a>
    </nav>

    <div class="md:hidden bg-white sticky top-0 z-40 border-b border-gray-200 px-6 py-4 flex items-center gap-3 shadow-sm">
        <button onclick="history.back()" class="w-9 h-9 rounded-full bg-gray-50 flex items-center justify-center text-slate-500 hover:bg-slate-200 transition"><i class="fa-solid fa-arrow-left"></i></button>
        <h1 class="font-extrabold text-slate-800 text-lg flex-1">Promo Spesial</h1>
    </div>

    <main class="max-w-7xl mx-auto px-6 md:px-4 py-6 md:py-10">
        <div class="text-center mb-10">
            <h2 class="text-2xl md:text-4xl font-black text-slate-900 mb-2">Hemat Lebih Banyak!</h2>
            <p class="text-slate-500 text-sm">Gunakan kode voucher di bawah ini saat checkout.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-3xl p-6 shadow-xl shadow-orange-100 border border-orange-100 relative overflow-hidden group">
                <div class="absolute -right-10 -top-10 w-32 h-32 bg-orange-500 rounded-full blur-3xl opacity-20 group-hover:opacity-40 transition"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 bg-orange-100 rounded-2xl flex items-center justify-center text-orange-600 text-xl"><i class="fa-solid fa-utensils"></i></div>
                        <span class="bg-orange-600 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">Reservasi</span>
                    </div>
                    <h3 class="font-black text-2xl text-slate-900 mb-1">Diskon 50%</h3>
                    <p class="text-sm text-slate-500 font-medium mb-6">Untuk pemesanan menu makanan saat reservasi meja.</p>
                    <div class="bg-slate-50 border border-dashed border-slate-300 rounded-xl p-3 flex justify-between items-center">
                        <code class="font-mono font-black text-lg text-slate-800 tracking-widest">DISKON50</code>
                        <button type="button" onclick="copyPromo('DISKON50')" class="text-xs font-bold text-orange-600 hover:text-orange-800 uppercase">Salin</button>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 shadow-xl shadow-blue-100 border border-blue-100 relative overflow-hidden group">
                <div class="absolute -right-10 -top-10 w-32 h-32 bg-blue-500 rounded-full blur-3xl opacity-20 group-hover:opacity-40 transition"></div>
                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 text-xl"><i class="fa-solid fa-laptop-file"></i></div>
                        <span class="bg-blue-600 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">Booking Meja</span>
                    </div>
                    <h3 class="font-black text-2xl text-slate-900 mb-1">Hemat 20%</h3>
                    <p class="text-sm text-slate-500 font-medium mb-6">Potongan harga untuk booking meja kerja per jam.</p>
                    <div class="bg-slate-50 border border-dashed border-slate-300 rounded-xl p-3 flex justify-between items-center">
                        <code class="font-mono font-black text-lg text-slate-800 tracking-widest">HEMAT20</code>
                        <button type="button" onclick="copyPromo('HEMAT20')" class="text-xs font-bold text-blue-600 hover:text-blue-800 uppercase">Salin</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function copyPromo(code) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(code);
            }
            alert('Kode ' + code + ' disalin!');
        }
    </script>
    <?php $active = 'promo'; include APPPATH . 'views/partials/mobile_nav.php'; ?>
</body>
</html>
