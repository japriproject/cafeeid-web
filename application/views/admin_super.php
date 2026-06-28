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
                <div class="mt-4 <?= ($message_type ?? 'success') === 'error' ? 'bg-red-50 text-red-800 border-red-200' : 'bg-emerald-50 text-emerald-800 border-emerald-200' ?> border rounded-xl px-4 py-3 text-sm font-bold"><?= html_escape($message) ?></div>
            <?php endif; ?>

            <div class="grid lg:grid-cols-2 gap-6 mt-6">
                <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200">
                    <h2 id="cafe-form-title" class="text-lg font-black text-slate-800">Tambah Kafe Baru</h2>
                    <form id="cafe-form" method="POST" enctype="multipart/form-data" class="mt-4 grid gap-3 text-sm">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                        <input type="hidden" name="submit" value="1">
                        <input type="hidden" name="edit_id" id="cafe-edit-id" value="">
                        <input required type="text" name="username" id="cafe-username" placeholder="Username" class="border rounded-xl p-3">
                        <input required type="password" name="password" id="cafe-password" placeholder="Password" class="border rounded-xl p-3">
                        <input required type="text" name="cafe_name" id="cafe-name" placeholder="Nama Kafe" class="border rounded-xl p-3">
                        <label class="border border-dashed border-slate-300 bg-white rounded-xl p-3 cursor-pointer">
                            <span class="block font-bold text-slate-700">Thumbnail Kafe</span>
                            <span class="block text-xs text-slate-400 mb-2">JPG, PNG, atau WebP. Maksimal 5 MB.</span>
                            <input required id="cafe-thumbnail" type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp" class="block w-full text-xs text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:font-bold file:text-blue-700">
                        </label>
                        <textarea name="address" id="cafe-address" placeholder="Alamat" class="border rounded-xl p-3 min-h-[80px]"></textarea>
                        <input type="text" name="kota" id="cafe-city" placeholder="Kota" class="border rounded-xl p-3">
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" name="latitude" id="cafe-latitude" placeholder="Latitude" class="border rounded-xl p-3">
                            <input type="text" name="longitude" id="cafe-longitude" placeholder="Longitude" class="border rounded-xl p-3">
                        </div>
                        <input type="text" name="prefix_invoice" id="cafe-prefix" placeholder="Prefix Invoice" class="border rounded-xl p-3">
                        <select name="status_meja" id="cafe-status" class="border rounded-xl p-3">
                            <option value="buka">Buka</option>
                            <option value="penuh">Penuh</option>
                        </select>
                        <div class="flex gap-2"><button type="submit" class="flex-1 bg-blue-600 text-white font-bold rounded-xl p-3">Simpan Kafe</button><button id="cancel-cafe-edit" type="button" onclick="resetCafeForm()" class="hidden bg-slate-200 text-slate-700 font-bold rounded-xl px-4">Batal</button></div>
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
                                <div class="flex gap-2">
                                <button type="button" onclick='editCafe(<?= json_encode(array('id'=>(int)$cafe->id_cafe,'username'=>$cafe->username,'name'=>$cafe->cafe_name,'address'=>$cafe->address,'city'=>$cafe->kota,'latitude'=>$cafe->latitude,'longitude'=>$cafe->longitude,'prefix'=>$cafe->prefix_invoice,'status'=>$cafe->status_meja), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="bg-amber-500 text-white text-xs font-bold rounded-lg px-3 py-2">Edit</button>
                                <form method="POST" onsubmit="return confirm('Hapus kafe ini? Data terkait mungkin tidak dapat dipulihkan.')">
                                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                    <input type="hidden" name="delete_id" value="<?= (int)$cafe->id_cafe ?>">
                                    <button type="submit" class="bg-red-500 text-white text-xs font-bold rounded-lg px-3 py-2">Hapus</button>
                                </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
    function editCafe(cafe) {
        document.getElementById('cafe-edit-id').value = cafe.id;
        document.getElementById('cafe-username').value = cafe.username;
        document.getElementById('cafe-name').value = cafe.name;
        document.getElementById('cafe-address').value = cafe.address || '';
        document.getElementById('cafe-city').value = cafe.city || '';
        document.getElementById('cafe-latitude').value = cafe.latitude || '';
        document.getElementById('cafe-longitude').value = cafe.longitude || '';
        document.getElementById('cafe-prefix').value = cafe.prefix || '';
        document.getElementById('cafe-status').value = cafe.status;
        document.getElementById('cafe-password').required = false;
        document.getElementById('cafe-password').placeholder = 'Kosongkan jika password tidak diganti';
        document.getElementById('cafe-thumbnail').required = false;
        document.getElementById('cafe-form-title').textContent = 'Edit Kafe';
        document.getElementById('cancel-cafe-edit').classList.remove('hidden');
        document.getElementById('cafe-form').scrollIntoView({behavior:'smooth'});
    }
    function resetCafeForm() {
        document.getElementById('cafe-form').reset();
        document.getElementById('cafe-edit-id').value = '';
        document.getElementById('cafe-password').required = true;
        document.getElementById('cafe-password').placeholder = 'Password';
        document.getElementById('cafe-thumbnail').required = true;
        document.getElementById('cafe-form-title').textContent = 'Tambah Kafe Baru';
        document.getElementById('cancel-cafe-edit').classList.add('hidden');
    }
    </script>
</body>
</html>
