<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Kafe</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen">
    <div class="max-w-6xl mx-auto p-6">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wide text-blue-600 font-bold">Super Admin</p>
                    <h1 class="text-2xl font-black text-slate-800 mt-1">Kelola Kafe</h1>
                </div>
                <form action="<?= site_url('auth/logout') ?>" method="POST">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <button type="submit" class="text-xs font-bold bg-slate-900 text-white px-3 py-2 rounded-xl">Keluar</button>
                </form>
            </div>

            <?php if ($message): ?>
                <div class="mt-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-xl px-4 py-3 text-sm font-bold"><?= html_escape($message) ?></div>
            <?php endif; ?>

            <div class="grid lg:grid-cols-2 gap-6 mt-6">
                <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200">
                    <h2 class="text-lg font-black text-slate-800">Tambah Kafe Baru</h2>
                    <form method="POST" class="mt-4 grid gap-3 text-sm">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                        <input type="hidden" name="submit" value="1">
                        <input required type="text" name="username" placeholder="Username" class="border rounded-xl p-3">
                        <input required type="password" name="password" placeholder="Password" class="border rounded-xl p-3">
                        <input required type="text" name="cafe_name" placeholder="Nama Kafe" class="border rounded-xl p-3">
                        <textarea name="address" placeholder="Alamat" class="border rounded-xl p-3 min-h-[80px]"></textarea>
                        <input type="text" name="kota" placeholder="Kota" class="border rounded-xl p-3">
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" name="latitude" placeholder="Latitude" class="border rounded-xl p-3">
                            <input type="text" name="longitude" placeholder="Longitude" class="border rounded-xl p-3">
                        </div>
                        <input type="text" name="prefix_invoice" placeholder="Prefix Invoice" class="border rounded-xl p-3">
                        <select name="status_meja" class="border rounded-xl p-3">
                            <option value="buka">Buka</option>
                            <option value="penuh">Penuh</option>
                        </select>
                        <button type="submit" class="bg-blue-600 text-white font-bold rounded-xl p-3">Simpan Kafe</button>
                    </form>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-5">
                    <h2 class="text-lg font-black text-slate-800">Daftar Kafe</h2>
                    <div class="mt-4 space-y-3">
                        <?php foreach ($cafes as $cafe): ?>
                            <div class="border border-slate-200 rounded-xl p-3 flex items-center justify-between">
                                <div>
                                    <p class="font-black text-slate-800"><?= html_escape($cafe->cafe_name) ?></p>
                                    <p class="text-xs text-slate-500"><?= html_escape($cafe->username) ?> • <?= html_escape($cafe->kota) ?></p>
                                </div>
                                <form method="POST" onsubmit="return confirm('Hapus kafe ini?')">
                                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                    <input type="hidden" name="delete_id" value="<?= (int)$cafe->id_cafe ?>">
                                    <button type="submit" class="bg-red-500 text-white text-xs font-bold rounded-lg px-3 py-2">Hapus</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
