<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - OLONA</title>
    <link rel="icon" type="image/jpeg" href="<?= base_url('assets/logo.jpg') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/legacy-cafeid.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body x-data="{ editing: false }" class="bg-slate-50 pb-24 text-slate-800">
    <div class="hidden md:flex items-center justify-between px-8 py-4 glass-nav sticky top-0 z-50 border-b border-slate-100">
        <a href="<?= site_url('home') ?>" class="text-xl font-black text-slate-900 tracking-tight">OLONA<span class="text-orange-600">.</span></a>
        <a href="<?= site_url('home') ?>" class="text-sm font-bold text-slate-500 hover:text-slate-900">Kembali ke Beranda</a>
    </div>

    <main class="max-w-2xl mx-auto p-4 md:p-8 space-y-6">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="p-4 rounded-2xl text-center text-sm font-bold bg-green-100 text-green-700"><?= $this->session->flashdata('success') ?></div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
            <div class="p-4 rounded-2xl text-center text-sm font-bold bg-red-100 text-red-700"><?= $this->session->flashdata('error') ?></div>
        <?php endif; ?>

        <h1 class="text-2xl font-black text-slate-900 md:hidden">Profil Saya</h1>

        <?php
            $name = $member ? $member->name : 'Member OLONA';
            $phone = $member ? $member->phone : '';
        ?>
        <section class="bg-white p-8 md:p-10 rounded-[3rem] shadow-xl border border-slate-100 text-center relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-br from-orange-400 to-red-500 opacity-10 group-hover:opacity-20 transition duration-500"></div>
            <div class="relative z-10">
                <div class="w-32 h-32 bg-white p-1.5 rounded-full mx-auto mb-6 shadow-2xl ring-4 ring-slate-50">
                    <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=<?= rawurlencode($name) ?>" class="w-full h-full rounded-full bg-slate-50" alt="">
                </div>

                <div x-show="!editing">
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight"><?= html_escape($name) ?></h2>
                    <p class="text-slate-400 font-bold mb-8 mt-2"><?= html_escape($phone ?: $reff) ?></p>
                    <div class="mb-6 bg-slate-50 border border-slate-100 rounded-2xl p-4">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Kode Referral Saya</p>
                        <div class="flex items-center justify-center gap-2">
                            <code class="text-lg font-black text-slate-900 tracking-widest"><?= html_escape($reff) ?></code>
                            <button type="button" onclick="navigator.clipboard && navigator.clipboard.writeText('<?= html_escape($reff) ?>'); alert('Kode referral disalin!')" class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 inline-flex items-center justify-center hover:bg-orange-200">
                                <i class="fa-solid fa-copy text-xs"></i>
                            </button>
                        </div>
                        <button type="button" onclick="shareReferral()" class="mt-3 inline-flex items-center gap-2 rounded-full bg-orange-600 px-5 py-2.5 text-xs font-black text-white shadow-lg hover:bg-orange-700">
                            <i class="fa-solid fa-share-nodes"></i> Bagikan Link Referral
                        </button>
                    </div>
                    <button type="button" @click="editing = true" class="bg-slate-900 text-white px-8 py-3 rounded-full font-bold text-xs tracking-widest shadow-xl hover:bg-orange-600 transition-all uppercase hover:scale-105 transform">Edit Profil</button>
                </div>

                <form method="POST" x-show="editing" class="space-y-4 max-w-xs mx-auto">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <input type="text" name="name" value="<?= html_escape($name) ?>" class="w-full text-center font-bold text-lg bg-slate-50 border-b-2 border-slate-200 focus:border-orange-500 outline-none py-2 rounded-t-lg" placeholder="Nama Lengkap">
                    <input type="text" name="phone" value="<?= html_escape($phone) ?>" class="w-full text-center font-bold text-slate-500 bg-slate-50 border-b-2 border-slate-200 focus:border-orange-500 outline-none py-2 rounded-t-lg" placeholder="No HP">
                    <div class="flex gap-2 justify-center pt-2">
                        <button type="button" @click="editing = false" class="bg-slate-200 text-slate-600 px-6 py-2 rounded-full font-bold text-xs">Batal</button>
                        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-full font-bold text-xs shadow-lg hover:bg-green-700">Simpan</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden">
            <a href="<?= site_url('profile') ?>" class="flex items-center gap-5 p-6 hover:bg-slate-50 transition border-b border-slate-50 group">
                <div class="w-12 h-12 rounded-2xl bg-pink-50 text-pink-500 flex items-center justify-center text-lg shadow-inner group-hover:scale-110 transition"><i class="fa-solid fa-heart"></i></div>
                <div class="flex-1 font-bold text-slate-700">Cafe Favorit</div>
                <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            </a>
            <a href="<?= site_url('profile') ?>" class="flex items-center gap-5 p-6 hover:bg-slate-50 transition border-b border-slate-50 group">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center text-lg shadow-inner group-hover:scale-110 transition"><i class="fa-solid fa-receipt"></i></div>
                <div class="flex-1 font-bold text-slate-700">Riwayat Tiket</div>
                <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            </a>
            <form action="<?= site_url('auth/logout') ?>" method="POST" onsubmit="return confirm('Keluar akun?')" class="border-t border-slate-100">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <button type="submit" class="w-full flex items-center gap-5 p-6 hover:bg-red-50 transition group text-left">
                <div class="w-12 h-12 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center text-lg shadow-inner group-hover:scale-110 transition"><i class="fa-solid fa-arrow-right-from-bracket"></i></div>
                <div class="flex-1 font-bold text-red-600">Keluar Akun</div>
                <i class="fa-solid fa-chevron-right text-red-200 text-xs"></i>
                </button>
            </form>
        </section>

        <p class="text-center text-[10px] text-slate-400 font-bold uppercase tracking-widest">Versi Aplikasi 1.0.0</p>
    </main>

    <?php $active = 'profile'; include APPPATH . 'views/partials/mobile_nav.php'; ?>
    <script>
    async function shareReferral() {
        const referralUrl = <?= json_encode(site_url('auth/register?reff=' . rawurlencode($reff))) ?>;
        const shareData = {
            title: 'Daftar OLONA',
            text: 'Daftar OLONA lewat link referral saya:',
            url: referralUrl
        };

        try {
            if (navigator.share) {
                await navigator.share(shareData);
                return;
            }
            await navigator.clipboard.writeText(referralUrl);
            alert('Link referral berhasil disalin!');
        } catch (error) {
            if (error.name !== 'AbortError') {
                window.prompt('Salin link referral berikut:', referralUrl);
            }
        }
    }
    </script>
</body>
</html>
