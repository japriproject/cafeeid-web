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
</body>
</html>
