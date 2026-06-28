<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Kafe</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen">
    <div class="max-w-5xl mx-auto p-6">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-wide text-blue-600 font-bold">Panel Kafe</p>
                    <h1 class="text-2xl font-black text-slate-800 mt-1"><?= html_escape($cafe->cafe_name ?? 'Kafe') ?></h1>
                </div>
                <form action="<?= site_url('auth/logout') ?>" method="POST">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <button type="submit" class="text-xs font-bold bg-slate-900 text-white px-3 py-2 rounded-xl">Keluar</button>
                </form>
            </div>

            <div class="grid md:grid-cols-3 gap-4 mt-6">
                <div class="bg-blue-600 text-white rounded-2xl p-4">
                    <p class="text-[10px] uppercase tracking-wide">Total pendapatan</p>
                    <p class="text-2xl font-black mt-2"><?= format_rupiah($total_earned) ?></p>
                </div>
                <div class="bg-white border rounded-2xl p-4">
                    <p class="text-[10px] uppercase tracking-wide text-slate-500">Status meja</p>
                    <p class="text-xl font-black text-slate-800 mt-2"><?= html_escape($cafe->status_meja ?? '-') ?></p>
                </div>
                <div class="bg-white border rounded-2xl p-4">
                    <p class="text-[10px] uppercase tracking-wide text-slate-500">Alamat</p>
                    <p class="text-sm font-bold text-slate-700 mt-2"><?= html_escape($cafe->address ?? '-') ?></p>
                </div>
            </div>

            <div id="kelola-meja" class="mt-8 border-t border-slate-200 pt-8">
                <h2 class="text-lg font-black text-slate-800">Kelola Meja</h2>
                <?php if ($this->session->flashdata('table_success')): ?><div class="mt-3 rounded-xl bg-emerald-50 p-3 text-sm font-bold text-emerald-800"><?= html_escape($this->session->flashdata('table_success')) ?></div><?php endif; ?>
                <?php if ($this->session->flashdata('table_error')): ?><div class="mt-3 rounded-xl bg-red-50 p-3 text-sm font-bold text-red-800"><?= html_escape($this->session->flashdata('table_error')) ?></div><?php endif; ?>
                <form method="POST" id="table-form" class="mt-4 grid gap-3 rounded-2xl border bg-slate-50 p-4 md:grid-cols-5">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="action" id="table-action" value="create_table">
                    <input type="hidden" name="id_meja" id="table-id">
                    <input required type="number" min="1" max="9999" name="nomor_meja" id="table-number" placeholder="Nomor meja" class="rounded-xl border p-3 text-sm">
                    <input required type="number" min="1" max="100" name="kapasitas" id="table-capacity" value="2" placeholder="Kapasitas" class="rounded-xl border p-3 text-sm">
                    <select name="table_status" id="table-status" class="rounded-xl border p-3 text-sm"><option value="tersedia">Tersedia</option><option value="terisi">Terisi</option><option value="nonaktif">Nonaktif</option></select>
                    <button class="rounded-xl bg-blue-600 p-3 text-sm font-bold text-white">Simpan Meja</button>
                    <button type="button" id="cancel-table-edit" onclick="resetTableForm()" class="hidden rounded-xl bg-slate-200 p-3 text-sm font-bold">Batal</button>
                </form>
                <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($tables as $table): ?>
                        <div class="flex items-center justify-between rounded-xl border bg-white p-3">
                            <div><p class="font-black text-slate-800">Meja <?= html_escape($table->nomor_meja) ?></p><p class="text-xs text-slate-500"><?= (int)$table->kapasitas ?> kursi · <?= html_escape(ucfirst($table->status)) ?></p></div>
                            <div class="flex gap-1"><button type="button" onclick='editTable(<?= json_encode(array('id'=>(int)$table->id_meja,'number'=>$table->nomor_meja,'capacity'=>(int)$table->kapasitas,'status'=>$table->status), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="rounded-lg bg-amber-50 px-2 py-2 text-xs font-bold text-amber-700">Edit</button><form method="POST" onsubmit="return confirm('Hapus meja ini?')"><input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>"><input type="hidden" name="action" value="delete_table"><input type="hidden" name="id_meja" value="<?= (int)$table->id_meja ?>"><button class="rounded-lg bg-red-50 px-2 py-2 text-xs font-bold text-red-700">Hapus</button></form></div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($tables)): ?><p class="text-sm font-bold text-slate-400">Belum ada data meja.</p><?php endif; ?>
                </div>
            </div>

            <div id="kelola-produk" class="mt-8 border-t border-slate-200 pt-8">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-blue-600 font-bold">Menu Café</p>
                        <h2 class="text-lg font-black text-slate-800">Kelola Produk</h2>
                    </div>
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700"><?= count($products) ?> produk</span>
                </div>

                <?php if ($this->session->flashdata('product_success')): ?>
                    <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800"><?= html_escape($this->session->flashdata('product_success')) ?></div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('product_error')): ?>
                    <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-800"><?= html_escape($this->session->flashdata('product_error')) ?></div>
                <?php endif; ?>

                <div class="mt-5 grid gap-6 lg:grid-cols-[340px_1fr]">
                    <form method="POST" enctype="multipart/form-data" id="product-form" class="h-fit rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                        <input type="hidden" name="action" id="product-action" value="create_product">
                        <input type="hidden" name="id_menu" id="product-id" value="">
                        <h3 id="product-form-title" class="font-black text-slate-800">Tambah Produk</h3>
                        <div class="mt-4 grid gap-3 text-sm">
                            <input required type="text" maxlength="100" name="menu_name" id="product-name" placeholder="Nama produk" class="rounded-xl border p-3">
                            <select required name="id_kategori" id="product-category" class="rounded-xl border p-3">
                                <option value="">Pilih kategori</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= (int)$category->id_kategori ?>"><?= html_escape($category->nama_kategori) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input required type="number" min="1" name="price" id="product-price" placeholder="Harga" class="rounded-xl border p-3">
                            <select name="status" id="product-status" class="rounded-xl border p-3">
                                <option value="1">Aktif / tersedia</option>
                                <option value="0">Nonaktif</option>
                            </select>
                            <label class="rounded-xl border border-dashed border-slate-300 bg-white p-3">
                                <span class="block font-bold text-slate-700">Foto Produk</span>
                                <span id="product-image-help" class="mb-2 block text-xs text-slate-400">Wajib. JPG, PNG, atau WebP maksimal 5 MB.</span>
                                <input required type="file" name="product_image" id="product-image" accept="image/jpeg,image/png,image/webp" class="block w-full text-xs text-slate-500 file:mr-2 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:font-bold file:text-blue-700">
                            </label>
                            <div class="flex gap-2">
                                <button type="submit" class="flex-1 rounded-xl bg-blue-600 p-3 font-bold text-white hover:bg-blue-700">Simpan Produk</button>
                                <button type="button" id="cancel-edit" onclick="resetProductForm()" class="hidden rounded-xl bg-slate-200 px-4 font-bold text-slate-700">Batal</button>
                            </div>
                        </div>
                    </form>

                    <div class="grid content-start gap-3 sm:grid-cols-2">
                        <?php if (empty($products)): ?>
                            <div class="sm:col-span-2 rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm font-bold text-slate-400">Belum ada produk. Tambahkan produk pertama dari form.</div>
                        <?php endif; ?>
                        <?php foreach ($products as $product): ?>
                            <?php $product_image = !empty($product->image) ? (preg_match('#^https?://#i', $product->image) ? $product->image : base_url('uploads/' . ltrim($product->image, '/'))) : 'https://placehold.co/300x200/f8fafc/64748b?text=Produk'; ?>
                            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                                <img src="<?= html_escape($product_image) ?>" alt="<?= html_escape($product->menu_name) ?>" class="h-36 w-full object-cover">
                                <div class="p-4">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="truncate font-black text-slate-800"><?= html_escape($product->menu_name) ?></p>
                                            <p class="text-xs text-slate-400"><?= html_escape($product->nama_kategori ?? '-') ?></p>
                                        </div>
                                        <span class="shrink-0 rounded-full px-2 py-1 text-[10px] font-black <?= (int)$product->status === 1 ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>"><?= (int)$product->status === 1 ? 'AKTIF' : 'NONAKTIF' ?></span>
                                    </div>
                                    <p class="mt-2 font-black text-blue-600"><?= format_rupiah($product->price) ?></p>
                                    <div class="mt-3 flex gap-2">
                                        <button type="button" onclick='editProduct(<?= json_encode(array('id' => (int)$product->id_menu, 'name' => $product->menu_name, 'category' => (int)$product->id_kategori, 'price' => (int)$product->price, 'status' => (int)$product->status), JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="flex-1 rounded-lg bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700">Edit</button>
                                        <form method="POST" class="flex-1" onsubmit="return confirm('Hapus produk ini?')">
                                            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                            <input type="hidden" name="action" value="delete_product">
                                            <input type="hidden" name="id_menu" value="<?= (int)$product->id_menu ?>">
                                            <button type="submit" class="w-full rounded-lg bg-red-50 px-3 py-2 text-xs font-bold text-red-700">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <h2 class="text-lg font-black text-slate-800">Transaksi Terbaru</h2>
                <div class="overflow-hidden rounded-2xl border border-slate-200 mt-3">
                    <table class="min-w-full text-xs">
                        <thead class="bg-slate-50 text-slate-500 uppercase">
                            <tr>
                                <th class="px-3 py-2 text-left">Invoice</th>
                                <th class="px-3 py-2 text-left">Produk</th>
                                <th class="px-3 py-2 text-left">Total</th>
                                <th class="px-3 py-2 text-left">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <?php foreach ($transactions as $trx): ?>
                                <tr class="border-t">
                                    <td class="px-3 py-2 font-bold text-slate-700"><?= html_escape($trx->invoice) ?></td>
                                    <td class="px-3 py-2 text-slate-600"><?= html_escape($trx->product) ?></td>
                                    <td class="px-3 py-2 font-bold text-slate-800"><?= format_rupiah($trx->sale) ?></td>
                                    <td class="px-3 py-2 text-slate-600"><?= $trx->status == 1 ? 'Lunas' : 'Pending' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        function editTable(table) {
            document.getElementById('table-action').value = 'update_table'; document.getElementById('table-id').value = table.id;
            document.getElementById('table-number').value = table.number; document.getElementById('table-capacity').value = table.capacity;
            document.getElementById('table-status').value = table.status; document.getElementById('cancel-table-edit').classList.remove('hidden');
            document.getElementById('table-form').scrollIntoView({behavior:'smooth'});
        }
        function resetTableForm() {
            document.getElementById('table-form').reset(); document.getElementById('table-action').value = 'create_table';
            document.getElementById('table-id').value = ''; document.getElementById('cancel-table-edit').classList.add('hidden');
        }
        function editProduct(product) {
            document.getElementById('product-action').value = 'update_product';
            document.getElementById('product-id').value = product.id;
            document.getElementById('product-name').value = product.name;
            document.getElementById('product-category').value = product.category;
            document.getElementById('product-price').value = product.price;
            document.getElementById('product-status').value = product.status;
            document.getElementById('product-image').required = false;
            document.getElementById('product-image-help').textContent = 'Opsional. Kosongkan jika foto tidak diganti.';
            document.getElementById('product-form-title').textContent = 'Edit Produk';
            document.getElementById('cancel-edit').classList.remove('hidden');
            document.getElementById('product-form').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function resetProductForm() {
            const form = document.getElementById('product-form');
            form.reset();
            document.getElementById('product-action').value = 'create_product';
            document.getElementById('product-id').value = '';
            document.getElementById('product-image').required = true;
            document.getElementById('product-image-help').textContent = 'Wajib. JPG, PNG, atau WebP maksimal 5 MB.';
            document.getElementById('product-form-title').textContent = 'Tambah Produk';
            document.getElementById('cancel-edit').classList.add('hidden');
        }
    </script>
</body>
</html>
