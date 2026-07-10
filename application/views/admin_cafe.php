<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Kafe - OLONA</title>
    <link rel="icon" type="image/jpeg" href="<?= base_url('assets/logo.jpg') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .admin-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .admin-table {
            min-width: 720px;
        }
        @media (max-width: 1023px) {
            .admin-menu-panel {
                display: none;
            }
            .admin-menu-panel.is-open {
                display: block;
            }
            .admin-sidebar-card {
                display: none;
            }
        }
        @media (max-width: 640px) {
            .admin-content {
                padding: 1rem;
            }
            .admin-section {
                border-radius: 1rem;
                padding: 1rem;
            }
            .admin-actions {
                flex-direction: column;
                align-items: stretch;
            }
            .admin-actions > * {
                width: 100%;
            }
            .admin-mobile-stack {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen overflow-x-hidden">
    <?php
        $active_page = $active_page ?? 'orders';
        $nav_items = array(
            'dashboard' => array('label' => 'Dashboard', 'url' => site_url('admin_cafe/dashboard'), 'badge' => ''),
            'orders' => array('label' => 'Konfirmasi Pesanan', 'url' => site_url('admin_cafe/konfirmasi_pesanan'), 'badge' => count($pending_orders ?? array())),
            'tables' => array('label' => 'Kelola Meja', 'url' => site_url('admin_cafe/kelola_meja'), 'badge' => count($tables ?? array())),
            'products' => array('label' => 'Kelola Produk', 'url' => site_url('admin_cafe/kelola_produk'), 'badge' => count($products ?? array())),
            'transactions' => array('label' => 'Transaksi', 'url' => site_url('admin_cafe/transaksi'), 'badge' => count($transactions ?? array())),
            'settings' => array('label' => 'Setting Data Cafe', 'url' => site_url('admin_cafe/setting'), 'badge' => ''),
        );
        $active_products = 0;
        foreach (($products ?? array()) as $product) {
            if ((int)$product->status === 1) {
                $active_products++;
            }
        }
    ?>
    <div class="min-h-screen lg:flex">
        <aside class="border-b border-slate-200 bg-white lg:fixed lg:inset-y-0 lg:left-0 lg:w-72 lg:border-b-0 lg:border-r">
            <div class="flex h-full flex-col p-4 sm:p-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex min-w-0 items-center gap-3">
                        <img src="<?= base_url('assets/logo.jpg') ?>" alt="OLONA" class="h-11 w-11 rounded-2xl object-cover">
                        <div class="min-w-0">
                            <p class="text-xs font-black uppercase tracking-wide text-blue-600">Admin Kafe</p>
                            <h1 class="truncate text-lg font-black text-slate-900"><?= html_escape($cafe->cafe_name ?? 'Kafe') ?></h1>
                        </div>
                    </div>
                    <button type="button" id="admin-menu-toggle" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-black text-slate-700 lg:hidden" aria-expanded="false" aria-controls="admin-menu-panel">Menu</button>
                </div>

                <div id="admin-menu-panel" class="admin-menu-panel lg:block">
                    <nav class="admin-nav mt-5 grid gap-2 lg:mt-7">
                        <?php foreach ($nav_items as $key => $item): ?>
                            <?php $active = $active_page === $key; ?>
                            <a href="<?= $item['url'] ?>" class="flex items-center justify-between rounded-2xl px-4 py-3 text-sm font-black <?= $active ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' ?>">
                                <span><?= html_escape($item['label']) ?></span>
                                <?php if ($item['badge'] !== ''): ?>
                                    <span class="rounded-full px-2 py-0.5 text-[10px] <?= $active ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' ?>"><?= (int)$item['badge'] ?></span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>

                    <div class="admin-sidebar-card mt-7 grid gap-3 rounded-2xl bg-slate-50 p-4 text-xs">
                        <div>
                            <p class="font-bold uppercase tracking-wide text-slate-400">Status meja</p>
                            <p class="mt-1 text-sm font-black text-slate-800"><?= html_escape($cafe->status_meja ?? '-') ?></p>
                        </div>
                        <div>
                            <p class="font-bold uppercase tracking-wide text-slate-400">Alamat</p>
                            <p class="mt-1 font-bold text-slate-700"><?= html_escape($cafe->address ?? '-') ?></p>
                        </div>
                    </div>

                    <form action="<?= site_url('auth/logout') ?>" method="POST" class="mt-4 lg:mt-6 lg:mt-auto">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                        <button type="submit" class="w-full rounded-2xl bg-slate-900 px-4 py-3 text-sm font-black text-white">Keluar</button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="w-full lg:pl-72">
            <div class="admin-content mx-auto max-w-6xl p-5 lg:p-8">
                <div class="mb-6 grid gap-4 md:grid-cols-3">
                    <div class="rounded-2xl bg-blue-600 p-5 text-white shadow-sm">
                        <p class="text-[10px] uppercase tracking-wide">Total pendapatan</p>
                        <p class="mt-2 text-2xl font-black"><?= format_rupiah($total_earned) ?></p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-5">
                        <p class="text-[10px] uppercase tracking-wide text-slate-500">Pesanan pending</p>
                        <p class="mt-2 text-2xl font-black text-slate-800"><?= count($pending_orders ?? array()) ?></p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-5">
                        <p class="text-[10px] uppercase tracking-wide text-slate-500">Produk aktif</p>
                        <p class="mt-2 text-2xl font-black text-slate-800"><?= (int)$active_products ?></p>
                    </div>
                </div>

            <?php if ($active_page === 'dashboard'): ?>
            <section class="grid gap-4 sm:gap-6 lg:grid-cols-[1.2fr_0.8fr]">
                <div class="admin-section rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-blue-600 font-bold">Ringkasan Hari Ini</p>
                            <h2 class="text-xl font-black text-slate-800">Dashboard Operasional</h2>
                        </div>
                        <a href="<?= site_url('admin_cafe/konfirmasi_pesanan') ?>" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-black text-white">Cek Pesanan</a>
                    </div>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Omzet lunas hari ini</p>
                            <p class="mt-2 text-2xl font-black text-slate-900"><?= format_rupiah($dashboard_stats['today_sales'] ?? 0) ?></p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Order hari ini</p>
                            <p class="mt-2 text-2xl font-black text-slate-900"><?= (int)($dashboard_stats['today_orders'] ?? 0) ?></p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Transaksi lunas</p>
                            <p class="mt-2 text-2xl font-black text-emerald-700"><?= (int)($dashboard_stats['paid_orders'] ?? 0) ?></p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Menunggu konfirmasi</p>
                            <p class="mt-2 text-2xl font-black text-amber-700"><?= (int)($dashboard_stats['pending_orders'] ?? 0) ?></p>
                        </div>
                    </div>
                </div>

                <div class="admin-section rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs uppercase tracking-wide text-blue-600 font-bold">Data Cafe</p>
                    <h2 class="text-xl font-black text-slate-800"><?= html_escape($cafe->cafe_name ?? 'Kafe') ?></h2>
                    <div class="mt-5 grid gap-3 text-sm">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Kota</p>
                            <p class="mt-1 font-black text-slate-800"><?= html_escape($cafe->kota ?? '-') ?></p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Status meja</p>
                            <p class="mt-1 font-black text-slate-800"><?= html_escape($cafe->status_meja ?? '-') ?></p>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Prefix invoice</p>
                            <p class="mt-1 font-black text-slate-800"><?= html_escape($cafe->prefix_invoice ?? '-') ?></p>
                        </div>
                        <a href="<?= site_url('admin_cafe/setting') ?>" class="rounded-2xl bg-slate-900 px-4 py-3 text-center text-sm font-black text-white">Buka Setting Data Cafe</a>
                    </div>
                </div>

                <div class="admin-section rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h2 class="text-lg font-black text-slate-800">Transaksi Terbaru</h2>
                        <a href="<?= site_url('admin_cafe/transaksi') ?>" class="text-sm font-black text-blue-600">Lihat Semua</a>
                    </div>
                    <div class="admin-table-wrap mt-3 rounded-2xl border border-slate-200">
                        <table class="admin-table min-w-full text-xs">
                            <thead class="bg-slate-50 text-slate-500 uppercase">
                                <tr>
                                    <th class="px-3 py-2 text-left">Invoice</th>
                                    <th class="px-3 py-2 text-left">Produk</th>
                                    <th class="px-3 py-2 text-left">Total</th>
                                    <th class="px-3 py-2 text-left">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <?php foreach (array_slice($transactions, 0, 6) as $trx): ?>
                                    <tr class="border-t">
                                        <td class="px-3 py-2 font-bold text-slate-700"><?= html_escape($trx->invoice) ?></td>
                                        <td class="px-3 py-2 text-slate-600"><?= html_escape($trx->product) ?></td>
                                        <td class="px-3 py-2 font-bold text-slate-800"><?= format_rupiah($trx->sale) ?></td>
                                        <td class="px-3 py-2 text-slate-600"><?= $trx->status == 1 ? 'Lunas' : 'Pending' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($transactions)): ?>
                                    <tr class="border-t"><td colspan="4" class="px-3 py-8 text-center font-bold text-slate-400">Belum ada transaksi.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            <?php endif; ?>

            <?php if ($active_page === 'orders'): ?>
            <section class="admin-section rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-amber-600 font-bold">Pesanan Manual</p>
                        <h2 class="text-lg font-black text-slate-800">Konfirmasi Pesanan</h2>
                    </div>
                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700"><?= count($pending_orders ?? array()) ?> pending</span>
                </div>

                <?php if ($this->session->flashdata('order_success')): ?>
                    <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800"><?= html_escape($this->session->flashdata('order_success')) ?></div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('order_error')): ?>
                    <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-800"><?= html_escape($this->session->flashdata('order_error')) ?></div>
                <?php endif; ?>

                <div class="admin-table-wrap mt-4 rounded-2xl border border-slate-200">
                    <table class="admin-table min-w-full text-xs">
                        <thead class="bg-slate-50 text-slate-500 uppercase">
                            <tr>
                                <th class="px-3 py-2 text-left">Invoice</th>
                                <th class="px-3 py-2 text-left">Pesanan</th>
                                <th class="px-3 py-2 text-left">Total Bayar</th>
                                <th class="px-3 py-2 text-left">Tipe</th>
                                <th class="px-3 py-2 text-left">Waktu</th>
                                <th class="px-3 py-2 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <?php foreach (($pending_orders ?? array()) as $order): ?>
                                <tr class="border-t align-top">
                                    <td class="px-3 py-3 font-bold text-slate-700"><?= html_escape($order->invoice) ?></td>
                                    <td class="px-3 py-3 text-slate-600">
                                        <p class="font-bold text-slate-700"><?= html_escape($order->product) ?></p>
                                        <p class="mt-1 text-[11px] text-slate-400"><?= html_escape($order->members) ?></p>
                                    </td>
                                    <td class="px-3 py-3 font-black text-slate-800"><?= format_rupiah($order->price) ?></td>
                                    <td class="px-3 py-3 text-slate-600"><?= html_escape(str_replace('_', ' ', strtoupper($order->order_type ?? '-'))) ?></td>
                                    <td class="px-3 py-3 text-slate-500"><?= html_escape($order->created_at) ?></td>
                                    <td class="px-3 py-3">
                                        <div class="admin-actions flex justify-end gap-2">
                                            <a href="<?= site_url('invoice/' . rawurlencode($order->invoice)) ?>" target="_blank" class="rounded-lg bg-slate-100 px-3 py-2 font-bold text-slate-700">Invoice</a>
                                            <form method="POST" onsubmit="return confirm('Konfirmasi pesanan <?= html_escape($order->invoice) ?> sebagai lunas?')">
                                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                                <input type="hidden" name="action" value="confirm_order">
                                                <input type="hidden" name="invoice" value="<?= html_escape($order->invoice) ?>">
                                                <button class="rounded-lg bg-emerald-600 px-3 py-2 font-bold text-white">Konfirmasi</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($pending_orders)): ?>
                                <tr class="border-t"><td colspan="6" class="px-3 py-8 text-center font-bold text-slate-400">Tidak ada pesanan pending.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
            <?php endif; ?>

            <?php if ($active_page === 'tables'): ?>
            <section class="admin-section rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
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
                        <div class="admin-mobile-stack flex items-center justify-between rounded-xl border bg-white p-3">
                            <div><p class="font-black text-slate-800">Meja <?= html_escape($table->nomor_meja) ?></p><p class="text-xs text-slate-500"><?= (int)$table->kapasitas ?> kursi · <?= html_escape(ucfirst($table->status)) ?></p></div>
                            <div class="admin-actions flex gap-1"><button type="button" onclick='editTable(<?= json_encode(array('id'=>(int)$table->id_meja,'number'=>$table->nomor_meja,'capacity'=>(int)$table->kapasitas,'status'=>$table->status), JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="rounded-lg bg-amber-50 px-2 py-2 text-xs font-bold text-amber-700">Edit</button><form method="POST" onsubmit="return confirm('Hapus meja ini?')"><input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>"><input type="hidden" name="action" value="delete_table"><input type="hidden" name="id_meja" value="<?= (int)$table->id_meja ?>"><button class="w-full rounded-lg bg-red-50 px-2 py-2 text-xs font-bold text-red-700">Hapus</button></form></div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($tables)): ?><p class="text-sm font-bold text-slate-400">Belum ada data meja.</p><?php endif; ?>
                </div>
            </section>
            <?php endif; ?>

            <?php if ($active_page === 'products'): ?>
            <section class="admin-section rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="admin-mobile-stack flex items-center justify-between gap-3">
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
                                    <div class="admin-actions mt-3 flex gap-2">
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
            </section>
            <?php endif; ?>

            <?php if ($active_page === 'transactions'): ?>
            <section class="admin-section rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-black text-slate-800">Transaksi Terbaru</h2>
                <div class="admin-table-wrap rounded-2xl border border-slate-200 mt-3">
                    <table class="admin-table min-w-full text-xs">
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
            </section>
            <?php endif; ?>

            <?php if ($active_page === 'settings'): ?>
            <section class="admin-section rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-blue-600 font-bold">Profil & Pembayaran</p>
                        <h2 class="text-xl font-black text-slate-800">Setting Data Cafe</h2>
                    </div>
                    <a href="<?= site_url('cafe/detail/' . (int)$cafe->id_cafe) ?>" target="_blank" class="rounded-xl bg-slate-100 px-4 py-2 text-sm font-black text-slate-700">Lihat Halaman Cafe</a>
                </div>

                <?php if ($this->session->flashdata('setting_success')): ?>
                    <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-800"><?= html_escape($this->session->flashdata('setting_success')) ?></div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('setting_error')): ?>
                    <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-800"><?= html_escape($this->session->flashdata('setting_error')) ?></div>
                <?php endif; ?>

                <form method="POST" class="mt-6 grid gap-6">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

                    <div class="grid gap-4 lg:grid-cols-2">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <h3 class="font-black text-slate-800">Data Cafe</h3>
                            <div class="mt-4 grid gap-3 text-sm">
                                <input required type="text" maxlength="100" name="cafe_name" value="<?= html_escape($cafe->cafe_name ?? '') ?>" placeholder="Nama cafe" class="rounded-xl border p-3">
                                <textarea name="address" placeholder="Alamat lengkap" class="min-h-[96px] rounded-xl border p-3"><?= html_escape($cafe->address ?? '') ?></textarea>
                                <div class="grid gap-3 md:grid-cols-3">
                                    <input type="text" name="kota" value="<?= html_escape($cafe->kota ?? '') ?>" placeholder="Kota" class="rounded-xl border p-3">
                                    <input type="text" name="latitude" value="<?= html_escape($cafe->latitude ?? '') ?>" placeholder="Latitude" class="rounded-xl border p-3">
                                    <input type="text" name="longitude" value="<?= html_escape($cafe->longitude ?? '') ?>" placeholder="Longitude" class="rounded-xl border p-3">
                                </div>
                                <div class="grid gap-3 md:grid-cols-2">
                                    <select name="status_meja" class="rounded-xl border p-3">
                                        <option value="buka" <?= ($cafe->status_meja ?? '') === 'buka' ? 'selected' : '' ?>>Meja buka</option>
                                        <option value="penuh" <?= ($cafe->status_meja ?? '') === 'penuh' ? 'selected' : '' ?>>Meja penuh</option>
                                    </select>
                                    <input type="number" min="0" name="harga_reservasi" value="<?= (int)($cafe->harga_reservasi ?? 0) ?>" placeholder="Harga reservasi" class="rounded-xl border p-3">
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <h3 class="font-black text-slate-800">Informasi Pembayaran</h3>
                            <div class="mt-4 grid gap-3 text-sm">
                                <textarea name="payment_info" placeholder="Catatan pembayaran" class="min-h-[96px] rounded-xl border p-3"><?= html_escape($cafe->payment_info ?? '') ?></textarea>
                                <div class="grid gap-3 md:grid-cols-2">
                                    <input type="text" name="qris_name" value="<?= html_escape($cafe->qris_name ?? '') ?>" placeholder="Nama QRIS" class="rounded-xl border p-3">
                                    <input type="text" name="qris_image" value="<?= html_escape($cafe->qris_image ?? '') ?>" placeholder="URL/path gambar QRIS" class="rounded-xl border p-3">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <h3 class="font-black text-slate-800">Rekening Bank</h3>
                        <div class="mt-4 grid gap-3 text-sm md:grid-cols-2 lg:grid-cols-3">
                            <input type="text" name="bank_bca_rek" value="<?= html_escape($cafe->bank_bca_rek ?? '') ?>" placeholder="BCA nomor rekening" class="rounded-xl border p-3">
                            <input type="text" name="bank_bca_an" value="<?= html_escape($cafe->bank_bca_an ?? '') ?>" placeholder="BCA atas nama" class="rounded-xl border p-3">
                            <input type="text" name="bank_bri_rek" value="<?= html_escape($cafe->bank_bri_rek ?? '') ?>" placeholder="BRI nomor rekening" class="rounded-xl border p-3">
                            <input type="text" name="bank_bri_an" value="<?= html_escape($cafe->bank_bri_an ?? '') ?>" placeholder="BRI atas nama" class="rounded-xl border p-3">
                            <input type="text" name="bank_mandiri_rek" value="<?= html_escape($cafe->bank_mandiri_rek ?? '') ?>" placeholder="Mandiri nomor rekening" class="rounded-xl border p-3">
                            <input type="text" name="bank_mandiri_an" value="<?= html_escape($cafe->bank_mandiri_an ?? '') ?>" placeholder="Mandiri atas nama" class="rounded-xl border p-3">
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <h3 class="font-black text-slate-800">Telegram Notifikasi</h3>
                        <div class="mt-4 grid gap-3 text-sm md:grid-cols-3">
                            <input type="text" name="id_telegram_owner" value="<?= html_escape($cafe->id_telegram_owner ?? '') ?>" placeholder="Telegram owner" class="rounded-xl border p-3">
                            <input type="text" name="id_telegram_kasir" value="<?= html_escape($cafe->id_telegram_kasir ?? '') ?>" placeholder="Telegram kasir" class="rounded-xl border p-3">
                            <input type="text" name="id_telegram_dapur" value="<?= html_escape($cafe->id_telegram_dapur ?? '') ?>" placeholder="Telegram dapur" class="rounded-xl border p-3">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="rounded-2xl bg-blue-600 px-6 py-3 text-sm font-black text-white hover:bg-blue-700">Simpan Setting</button>
                    </div>
                </form>
            </section>
            <?php endif; ?>
            </div>
        </main>
        </div>
    <script>
        const adminMenuToggle = document.getElementById('admin-menu-toggle');
        const adminMenuPanel = document.getElementById('admin-menu-panel');
        if (adminMenuToggle && adminMenuPanel) {
            adminMenuToggle.addEventListener('click', function () {
                const open = adminMenuPanel.classList.toggle('is-open');
                adminMenuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
        }

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
