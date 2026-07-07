<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk <?= html_escape($invoice) ?> - OLONA</title>
    <link rel="icon" type="image/jpeg" href="<?= base_url('assets/logo.jpg') ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/legacy-cafeid.css') ?>">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
            .receipt-card { box-shadow: none !important; border: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen p-4 text-slate-900">
    <?php
        $status = isset($status) ? (int)$status : 0;
        $qris_src = '';
        $qris_fallback_src = '';
        $qris_local_file = '';
        $qris_candidates = array();

        if (!empty($cafe->id_cafe)) {
            $qris_candidates = glob(FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'qris_cafe_' . (int)$cafe->id_cafe . '_*.{jpg,jpeg,png,webp}', GLOB_BRACE);
        }

        if (empty($qris_candidates)) {
            $qris_candidates = glob(FCPATH . 'uploads' . DIRECTORY_SEPARATOR . '*.{jpg,jpeg,png,webp}', GLOB_BRACE);
        }

        if (!empty($qris_candidates)) {
            usort($qris_candidates, function ($a, $b) {
                return filemtime($b) <=> filemtime($a);
            });
            $qris_fallback_file = $qris_candidates[0];
            $qris_fallback_src = base_url('uploads/' . basename($qris_fallback_file)) . '?v=' . filemtime($qris_fallback_file);
        }

        if (!empty($cafe->qris_image)) {
            if (preg_match('/^https?:\/\//i', $cafe->qris_image)) {
                $qris_src = $cafe->qris_image;
            } else {
                $qris_path = ltrim($cafe->qris_image, '/');
                $qris_local_file = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $qris_path);
                if (is_file($qris_local_file)) {
                    $qris_src = base_url($qris_path) . '?v=' . filemtime($qris_local_file);
                }
            }
        }

        if (!empty($qris_candidates) && !empty($qris_local_file) && is_file($qris_local_file) && filemtime($qris_candidates[0]) > filemtime($qris_local_file)) {
            $qris_src = $qris_fallback_src;
        }

        if (empty($qris_src)) {
            $qris_src = $qris_fallback_src;
        }
    ?>

    <div class="receipt-card max-w-md mx-auto bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div class="text-center flex flex-col items-center gap-1">
            <h1 class="text-2xl font-black uppercase tracking-wide"><?= html_escape($cafe->cafe_name) ?></h1>
            <p class="text-[10px] text-slate-400 font-bold"><?= html_escape($cafe->address) ?></p>
            <span class="mt-2 bg-slate-100 text-slate-800 text-[10px] font-black px-4 py-1.5 rounded-full"><?= html_escape($invoice) ?></span>
        </div>

        <div class="border-b border-dashed border-slate-200 my-5"></div>

        <div class="space-y-3 text-xs font-bold">
            <div class="flex justify-between gap-4">
                <span class="text-slate-400">Tanggal Transaksi</span>
                <span class="text-right"><?= html_escape($created_at ?? date('Y-m-d H:i:s')) ?></span>
            </div>
            <div class="flex justify-between gap-4">
                <span class="text-slate-400">ID Pelanggan (Member)</span>
                <span class="text-right"><?= html_escape($member_reff ?? '-') ?></span>
            </div>
            <div class="flex justify-between gap-4">
                <span class="text-slate-400">Metode Pemesanan</span>
                <span class="text-blue-600 uppercase font-black"><?= html_escape($order_type) ?></span>
            </div>
        </div>

        <div class="border-b border-dashed border-slate-200 my-5"></div>

        <div>
            <p class="text-[10px] text-slate-400 uppercase tracking-widest font-black mb-3">Rincian Pembelian:</p>
            <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4 text-xs font-bold leading-relaxed whitespace-pre-line"><?= html_escape($desc_nota) ?></div>
        </div>

        <div class="border-b border-dashed border-slate-200 my-5"></div>

        <div class="space-y-2.5 text-xs sm:text-sm">
            <p class="text-[10px] text-slate-400 uppercase tracking-widest font-black">Rincian Biaya & Pajak:</p>
            <div class="flex items-start justify-between gap-3 sm:gap-4">
                <span class="text-slate-600 flex-1 min-w-0 leading-relaxed">Harga Dasar Makanan/Reservasi</span>
                <span class="font-bold shrink-0 text-right whitespace-nowrap text-[12px] sm:text-sm"><?= format_rupiah($subtotal) ?></span>
            </div>
            <div class="flex items-start justify-between gap-3 sm:gap-4">
                <span class="text-slate-600 flex-1 min-w-0 leading-relaxed">PPN (11%)</span>
                <span class="font-bold shrink-0 text-right whitespace-nowrap text-[12px] sm:text-sm"><?= format_rupiah($ppn) ?></span>
            </div>
            <div class="flex items-start justify-between gap-3 sm:gap-4">
                <span class="text-slate-600 flex-1 min-w-0 leading-relaxed">Biaya Layanan Server</span>
                <span class="font-bold shrink-0 text-right whitespace-nowrap text-[12px] sm:text-sm"><?= format_rupiah($server_fee) ?></span>
            </div>
            <div class="flex items-start justify-between gap-3 sm:gap-4 text-orange-600">
                <span class="flex-1 min-w-0 leading-relaxed">Kode Unik Pembayaran</span>
                <span class="font-black shrink-0 text-right whitespace-nowrap text-[12px] sm:text-sm">+ <?= format_rupiah($kode_unik) ?></span>
            </div>
            <div class="flex justify-between items-center gap-3 sm:gap-4 border-t border-slate-100 pt-4 mt-2">
                <span class="text-lg sm:text-xl font-black uppercase flex-1 min-w-0">Total Akhir</span>
                <span class="text-lg sm:text-xl font-black text-blue-600 shrink-0 text-right whitespace-nowrap"><?= format_rupiah($total_price) ?></span>
            </div>
        </div>

        <div class="text-center mt-5">
            <?php if ($status === 1): ?>
                <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-800 text-[10px] font-black px-4 py-2 rounded-full uppercase">Status: Terbayar (Lunas)</span>
            <?php else: ?>
                <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-800 text-[10px] font-black px-4 py-2 rounded-full uppercase">Status: Menunggu Pembayaran</span>
                <p class="no-print mt-2 text-[10px] text-slate-400 font-bold">Status dicek otomatis setiap 10 detik.</p>
            <?php endif; ?>
        </div>

        <?php if ($status === 0): ?>
            <div class="border-b border-dashed border-slate-200 my-5"></div>
            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4">
                <p class="text-center text-[10px] text-blue-900 font-black uppercase tracking-wider mb-3">Metode Pembayaran</p>
                <div class="space-y-3">
                    <?php if (!empty($cafe->bank_bca_rek)): ?>
                        <div class="bg-white border border-blue-100 rounded-xl p-3 flex justify-between gap-3">
                            <div>
                                <p class="text-[9px] text-blue-600 font-black">BANK BCA</p>
                                <p class="font-mono text-sm font-black"><?= html_escape($cafe->bank_bca_rek) ?></p>
                                <p class="text-[10px] text-slate-400 font-bold">A.N <?= html_escape($cafe->bank_bca_an) ?></p>
                            </div>
                            <button type="button" onclick="copyText('<?= html_escape($cafe->bank_bca_rek) ?>')" class="no-print text-blue-600 text-[10px] font-black">Salin</button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($cafe->bank_bri_rek)): ?>
                        <div class="bg-white border border-orange-100 rounded-xl p-3 flex justify-between gap-3">
                            <div>
                                <p class="text-[9px] text-orange-600 font-black">BANK BRI</p>
                                <p class="font-mono text-sm font-black"><?= html_escape($cafe->bank_bri_rek) ?></p>
                                <p class="text-[10px] text-slate-400 font-bold">A.N <?= html_escape($cafe->bank_bri_an) ?></p>
                            </div>
                            <button type="button" onclick="copyText('<?= html_escape($cafe->bank_bri_rek) ?>')" class="no-print text-orange-600 text-[10px] font-black">Salin</button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($cafe->bank_mandiri_rek)): ?>
                        <div class="bg-white border border-yellow-100 rounded-xl p-3 flex justify-between gap-3">
                            <div>
                                <p class="text-[9px] text-yellow-600 font-black">BANK MANDIRI</p>
                                <p class="font-mono text-sm font-black"><?= html_escape($cafe->bank_mandiri_rek) ?></p>
                                <p class="text-[10px] text-slate-400 font-bold">A.N <?= html_escape($cafe->bank_mandiri_an) ?></p>
                            </div>
                            <button type="button" onclick="copyText('<?= html_escape($cafe->bank_mandiri_rek) ?>')" class="no-print text-yellow-700 text-[10px] font-black">Salin</button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($qris_src)): ?>
                        <div class="bg-white border border-purple-100 rounded-xl p-4 text-center">
                            <p class="text-[9px] text-purple-600 font-black uppercase tracking-wider">QRIS <?= html_escape($cafe->qris_name ?: $cafe->cafe_name) ?></p>
                            <button type="button" onclick="openQrisModal()" class="no-print block mx-auto mt-3 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-100" aria-label="Buka QRIS layar penuh">
                                <img src="<?= html_escape($qris_src) ?>" onerror="this.onerror=null;this.src='<?= html_escape($qris_fallback_src) ?>';" class="mx-auto w-44 h-44 object-contain rounded-xl border border-slate-100" alt="QRIS">
                            </button>
                            <img src="<?= html_escape($qris_src) ?>" onerror="this.onerror=null;this.src='<?= html_escape($qris_fallback_src) ?>';" class="hidden print:block mt-3 mx-auto w-44 h-44 object-contain rounded-xl border border-slate-100" alt="QRIS">
                            <p class="no-print mt-2 text-[10px] text-slate-400 font-bold">Klik QRIS untuk layar penuh.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <p class="text-center text-[10px] text-orange-600 font-black mt-3">Transfer tepat <?= format_rupiah($total_price) ?> agar lunas otomatis.</p>
            </div>
        <?php endif; ?>

        <p class="text-center text-[10px] text-slate-400 font-black uppercase tracking-widest mt-6">Terima Kasih Atas Kunjungan Anda</p>

        <div class="no-print mt-6 flex gap-2">
            <button onclick="window.print()" class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black rounded-xl shadow active:scale-95 transition">Simpan Ke PDF / Cetak</button>
            <a href="<?= site_url('home') ?>" class="px-5 py-3 bg-slate-100 text-slate-700 text-xs font-black rounded-xl hover:bg-slate-200 inline-flex items-center">Kembali</a>
        </div>
    </div>

    <?php if ($status === 0 && !empty($qris_src)): ?>
        <div id="qrisModal" class="no-print fixed inset-0 z-50 hidden bg-slate-950/95 px-4 py-6 overflow-y-auto">
            <div class="max-w-md mx-auto flex justify-end">
                <button type="button" onclick="closeQrisModal()" class="mb-4 inline-flex items-center gap-2 rounded-full bg-white/15 px-5 py-3 text-white text-sm font-black backdrop-blur">
                    <span class="text-2xl leading-none">&times;</span> Tutup Layar
                </button>
            </div>
            <div class="max-w-md mx-auto bg-white rounded-[2rem] p-6 text-center shadow-2xl">
                <span class="inline-flex bg-purple-50 text-purple-700 text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded-full">QRIS Merchant Resmi</span>
                <h2 class="mt-5 text-2xl font-black uppercase"><?= html_escape($cafe->qris_name ?: $cafe->cafe_name) ?></h2>
                <div class="mt-5 rounded-2xl border border-slate-200 p-3 shadow-sm">
                    <img src="<?= html_escape($qris_src) ?>" onerror="this.onerror=null;this.src='<?= html_escape($qris_fallback_src) ?>';" class="w-full max-w-[320px] mx-auto object-contain" alt="QRIS">
                </div>
                <p class="mt-5 text-xs text-slate-500 font-bold leading-relaxed">
                    Silakan screenshot layar ini atau langsung scan menggunakan kamera smartphone Anda. Pastikan nominal pembayaran tepat <span class="text-slate-900"><?= format_rupiah($total_price) ?></span>.
                </p>
            </div>
        </div>
    <?php endif; ?>

    <script>
        function copyText(text) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text);
            }
            alert('Nomor rekening disalin!');
        }

        function openQrisModal() {
            var modal = document.getElementById('qrisModal');
            if (!modal) return;
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeQrisModal() {
            var modal = document.getElementById('qrisModal');
            if (!modal) return;
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeQrisModal();
            }
        });

        <?php if ($status === 0): ?>
            var invoiceCode = <?= json_encode((string)$invoice) ?>;
            function checkInvoiceStatus() {
                fetch('<?= site_url('api/check-status') ?>?inv=' + encodeURIComponent(invoiceCode) + '&t=' + Date.now(), {
                    cache: 'no-store',
                    headers: { 'Accept': 'application/json' }
                })
                    .then(function (response) { return response.json(); })
                    .then(function (data) {
                        if (data && parseInt(data.status, 10) === 1) {
                            window.location.reload();
                        }
                    })
                    .catch(function () {});
            }

            checkInvoiceStatus();
            setInterval(checkInvoiceStatus, 10000);
        <?php endif; ?>
    </script>
</body>
</html>
