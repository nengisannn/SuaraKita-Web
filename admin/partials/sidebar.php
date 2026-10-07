<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-white text-slate-800 shadow-sm flex flex-col transition-all duration-300 z-50 border-r border-slate-200/80">
    
    <!-- Logo & Brand -->
    <div class="h-20 px-6 flex items-center gap-3 border-b border-slate-100">
        <img src="../assets/img/logoSuaraKita.png" alt="Logo SuaraKita" class="h-9 w-auto rounded-xl object-contain bg-emerald-50 p-1 border border-emerald-100">
        <div>
            <h2 class="font-extrabold text-base tracking-tight text-slate-900">SuaraKita</h2>
            <p class="text-[11px] text-slate-500 font-medium">Panel Admin / Pengelola</p>
        </div>
    </div>

    <!-- Menu Navigation (Clean Light Apple Pill) -->
    <nav class="flex-1 px-3 py-5 space-y-1 font-medium text-xs">
        <a href="dashboard_admin.php"
           class="flex items-center px-3.5 py-2.5 rounded-xl transition-all duration-150 active:scale-[0.98]
            <?= $current_page == 'dashboard_admin.php' 
                ? 'bg-emerald-50 text-emerald-700 font-semibold border border-emerald-200/80 shadow-2xs' 
                : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' ?>">
            <svg class="w-4 h-4 mr-3 opacity-90 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Dashboard Admin
        </a>

        <a href="pengaduan.php"
           class="flex items-center px-3.5 py-2.5 rounded-xl transition-all duration-150 active:scale-[0.98]
            <?= $current_page == 'pengaduan.php' 
                ? 'bg-emerald-50 text-emerald-700 font-semibold border border-emerald-200/80 shadow-2xs' 
                : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' ?>">
            <svg class="w-4 h-4 mr-3 opacity-90 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            Kelola Pengaduan
        </a>

        <a href="kelola_siswa.php"
           class="flex items-center px-3.5 py-2.5 rounded-xl transition-all duration-150 active:scale-[0.98]
            <?= $current_page == 'kelola_siswa.php' 
                ? 'bg-emerald-50 text-emerald-700 font-semibold border border-emerald-200/80 shadow-2xs' 
                : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' ?>">
            <svg class="w-4 h-4 mr-3 opacity-90 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Kelola Mahasiswa
        </a>
        
        <div class="pt-4 pb-1">
            <div class="border-t border-slate-100 my-1"></div>
        </div>

        <a href="../auth/logout.php"
           class="flex items-center px-3.5 py-2.5 rounded-xl text-rose-600 hover:bg-rose-50 transition-all duration-150 active:scale-[0.98]">
            <svg class="w-4 h-4 mr-3 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Keluar (Logout)
        </a>
    </nav>
</aside>
