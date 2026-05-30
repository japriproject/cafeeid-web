<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk CariCafe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/legacy-cafeid.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-6">
    <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-xl max-w-sm w-full flex flex-col gap-5">
        <div class="text-center">
            <div class="mx-auto mb-5 inline-flex h-16 w-16 items-center justify-center rounded-full bg-orange-100 text-orange-600">
                <i class="fa-solid fa-right-to-bracket text-2xl"></i>
            </div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Selamat Datang</h1>
            <p class="text-slate-400 text-base leading-relaxed mt-3">Silakan login untuk reservasi atau panel kafe.</p>
        </div>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="bg-red-50 text-red-600 text-sm font-black p-4 rounded-2xl text-center border border-red-100"><?= $this->session->flashdata('error') ?></div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('success')): ?>
            <div class="bg-emerald-50 text-emerald-700 text-sm font-black p-4 rounded-2xl text-center border border-emerald-100"><?= $this->session->flashdata('success') ?></div>
        <?php endif; ?>

        <form action="<?= site_url('auth/login') . ($redirect ? '?redirect=' . rawurlencode($redirect) : '') ?>" method="POST" class="flex flex-col gap-5">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <div>
                <label class="text-[11px] font-black text-slate-400 block mb-2 uppercase tracking-[0.18em]">Nomor HP / Username</label>
                <input required type="text" name="username" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-base font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-orange-500 placeholder:text-slate-400" placeholder="e.g. 6285725757934">
            </div>

            <div>
                <label class="text-[11px] font-black text-slate-400 block mb-2 uppercase tracking-[0.18em]">Kata Sandi</label>
                <div class="relative">
                    <input required id="login-password" type="password" name="password" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 pr-12 text-base font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-orange-500 placeholder:text-slate-400" placeholder="********">
                    <button type="button" onclick="togglePassword('login-password', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-orange-600">
                        <i class="fa-solid fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full py-4 bg-slate-900 hover:bg-orange-600 text-white font-black text-sm rounded-2xl transition shadow-xl shadow-slate-300 active:scale-95 mt-2 inline-flex items-center justify-center gap-3">
                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                MASUK KE AKUN
            </button>
        </form>

        <div class="text-center text-sm flex flex-col gap-3 border-t border-gray-100 pt-5">
            <a href="<?= site_url('auth/register') . ($redirect ? '?redirect=' . rawurlencode($redirect) : '') ?>" class="text-orange-600 font-black hover:underline inline-flex items-center justify-center gap-2"><i class="fa-solid fa-user-plus"></i> Daftar member baru</a>
            <a href="<?= site_url('home') ?>" class="text-slate-400 font-bold hover:text-orange-600 inline-flex items-center justify-center gap-2"><i class="fa-solid fa-house"></i> Kembali ke Beranda</a>
        </div>
    </div>
    <script>
        function togglePassword(id, button) {
            const input = document.getElementById(id);
            const icon = button.querySelector('i');
            const visible = input.type === 'text';
            input.type = visible ? 'password' : 'text';
            icon.className = visible ? 'fa-solid fa-eye' : 'fa-solid fa-eye-slash';
        }
    </script>
</body>
</html>
