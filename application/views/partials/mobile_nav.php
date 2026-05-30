<div class="md:hidden fixed bottom-0 left-0 right-0 bg-white/90 backdrop-blur-lg border-t border-slate-200 px-6 py-3 flex justify-between items-center z-50 pb-safe mobile-bottom-nav">
    <a href="<?= site_url('home') ?>" class="<?= ($active ?? '') === 'home' ? 'text-orange-600' : 'text-slate-400 hover:text-slate-600' ?> flex flex-col items-center gap-1 group">
        <div class="text-xl group-active:scale-90 transition"><i class="fa-solid fa-house"></i></div>
        <span class="text-[9px] font-bold uppercase tracking-wider">Beranda</span>
    </a>
    <a href="<?= site_url('cari') ?>" class="<?= ($active ?? '') === 'cari' ? 'text-orange-600' : 'text-slate-400 hover:text-slate-600' ?> flex flex-col items-center gap-1 group">
        <div class="text-xl group-active:scale-90 transition"><i class="fa-solid fa-magnifying-glass"></i></div>
        <span class="text-[9px] font-bold uppercase tracking-wider">Cari</span>
    </a>
    <div class="relative -top-6">
        <a href="<?= site_url('profile') ?>" class="w-14 h-14 bg-slate-900 rounded-full text-white flex items-center justify-center text-xl shadow-lg shadow-slate-300 hover:bg-orange-600 transition group active:scale-90">
            <i class="fa-solid fa-ticket"></i>
        </a>
    </div>
    <a href="<?= site_url('promo') ?>" class="<?= ($active ?? '') === 'promo' ? 'text-orange-600' : 'text-slate-400 hover:text-slate-600' ?> flex flex-col items-center gap-1 group">
        <div class="text-xl group-active:scale-90 transition"><i class="fa-solid fa-percent"></i></div>
        <span class="text-[9px] font-bold uppercase tracking-wider">Promo</span>
    </a>
    <a href="<?= site_url('profile') ?>" class="<?= ($active ?? '') === 'profile' ? 'text-orange-600' : 'text-slate-400 hover:text-slate-600' ?> flex flex-col items-center gap-1 group">
        <div class="text-xl group-active:scale-90 transition"><i class="fa-solid fa-user"></i></div>
        <span class="text-[9px] font-bold uppercase tracking-wider">Profil</span>
    </a>
</div>
