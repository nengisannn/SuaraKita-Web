<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

require_siswa();

$siswaUsername = $_SESSION['siswa_username'];
$siswa_id      = $_SESSION['siswa_id'];
$sekolah_id    = $_SESSION['sekolah_id'];

// Ambil statistik pengaduan siswa ini
$total_pengaduan = db_fetch_one("SELECT COUNT(id) AS count FROM pengaduan WHERE sekolah_id = ? AND siswa_id = ?", [$sekolah_id, $siswa_id], "ii")['count'] ?? 0;
$total_proses    = db_fetch_one("SELECT COUNT(id) AS count FROM pengaduan WHERE sekolah_id = ? AND siswa_id = ? AND status IN ('terkirim','ditinjau','diproses')", [$sekolah_id, $siswa_id], "ii")['count'] ?? 0;
$total_selesai   = db_fetch_one("SELECT COUNT(id) AS count FROM pengaduan WHERE sekolah_id = ? AND siswa_id = ? AND status = 'selesai'", [$sekolah_id, $siswa_id], "ii")['count'] ?? 0;

// Ambil 5 pengaduan terbaru
$recent_list = db_fetch_all("SELECT * FROM pengaduan WHERE sekolah_id = ? AND siswa_id = ? ORDER BY created_at DESC LIMIT 5", [$sekolah_id, $siswa_id], "ii");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Mahasiswa | SuaraKita</title>
    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="../assets/js/tailwindcss.js"></script>
    <script>window.tailwind || document.write('<script src="https://cdn.tailwindcss.com"><\/script>')</script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                },
            },
        }
    </script>
</head>

<body class="bg-slate-50 text-slate-800 font-sans antialiased selection:bg-emerald-500 selection:text-white">

<?php include 'partials/sidebar.php'; ?>

<main class="ml-64 min-h-screen pb-16">
    <?php include 'partials/header.php'; ?>

    <div class="p-8 mt-20 max-w-7xl mx-auto space-y-6">
        
        <!-- Welcome Hero (Clean Light Apple Emerald Style) -->
        <div class="relative overflow-hidden bg-white rounded-3xl p-7 text-slate-900 shadow-sm border border-slate-200/90 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-emerald-50 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative z-10">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60 mb-3">
                    ✨ Selamat Datang Kembali
                </span>
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 mb-1.5">
                    Halo, <?= htmlspecialchars($siswaUsername); ?>!
                </h2>
                <p class="text-slate-500 text-xs md:text-sm max-w-xl leading-relaxed">
                    Sampaikan keluhan dan aspirasi Anda secara mudah, aman, dan transparan untuk lingkungan institusi yang lebih baik.
                </p>
            </div>
            <div class="relative z-10">
                <a href="buat_pengaduan.php" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-xs font-semibold shadow-xs transition-all active:scale-[0.98]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Buat Pengaduan Baru</span>
                </a>
            </div>
        </div>

        <!-- STATISTIC WIDGETS (Clean Hairline Cards Grid) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <!-- Card Total -->
            <div class="bg-white p-4 rounded-2xl shadow-2xs border border-slate-200/80 hover:border-slate-300 transition-all flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Laporan</span>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-extrabold text-slate-900 font-mono tracking-tight"><?= $total_pengaduan ?></span>
                        <span class="text-[11px] font-medium text-slate-400">pengaduan terdaftar</span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-700 text-base">
                    📁
                </div>
            </div>

            <!-- Card Diproses -->
            <div class="bg-white p-4 rounded-2xl shadow-2xs border border-slate-200/80 hover:border-sky-200 transition-all flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Sedang Diproses</span>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-extrabold text-sky-600 font-mono tracking-tight"><?= $total_proses ?></span>
                        <span class="text-[11px] font-medium text-slate-400">dalam penanganan</span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-600 text-base">
                    ⚙️
                </div>
            </div>

            <!-- Card Selesai -->
            <div class="bg-white p-4 rounded-2xl shadow-2xs border border-slate-200/80 hover:border-emerald-200 transition-all flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Telah Selesai</span>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-extrabold text-emerald-600 font-mono tracking-tight"><?= $total_selesai ?></span>
                        <span class="text-[11px] font-medium text-slate-400">tuntas ditangani</span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 text-base">
                    ✅
                </div>
            </div>

        </div>

        <!-- RECENT COMPLAINTS TABLE (Apple Style Table) -->
        <div class="bg-white/90 backdrop-blur-md rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 tracking-tight">Riwayat Pengaduan Terbaru</h3>
                    <p class="text-xs text-slate-500">5 pengaduan terakhir yang Anda buat.</p>
                </div>
                <a href="riwayat_pengaduan.php" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition">
                    Lihat Semua ➔
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3">Judul Pengaduan</th>
                            <th class="px-6 py-3">Kategori</th>
                            <th class="px-6 py-3">Urgensi</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        <?php if (empty($recent_list)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-10 text-slate-400 text-xs italic">
                                    Belum ada pengaduan yang dibuat. Klik "+ Buat Pengaduan Baru" untuk membuat laporan.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_list as $row): ?>
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-6 py-3.5">
                                        <div class="font-bold text-slate-900 truncate max-w-xs"><?= htmlspecialchars($row['judul']) ?></div>
                                        <?php if (!empty($row['is_anonim'])): ?>
                                            <span class="text-[10px] text-slate-400">🔒 Mode Anonim</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-3.5 text-xs text-slate-600">
                                        <?= htmlspecialchars($row['kategori'] ?? 'Fasilitas') ?>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <?php
                                        $urg_badge = match($row['urgensi'] ?? 'sedang') {
                                            'rendah' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'sedang' => 'bg-sky-50 text-sky-700 border-sky-200',
                                            'tinggi' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'darurat' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            default => 'bg-slate-50 text-slate-700 border-slate-200'
                                        };
                                        ?>
                                        <span class="px-2.5 py-0.5 rounded-full border text-[11px] font-semibold <?= $urg_badge ?>">
                                            <?= ucfirst(htmlspecialchars($row['urgensi'] ?? 'sedang')) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <?php
                                        $stat_badge = match($row['status'] ?? 'terkirim') {
                                            'terkirim', 'dikirim' => 'bg-amber-100 text-amber-800',
                                            'ditinjau' => 'bg-purple-100 text-purple-800',
                                            'diproses' => 'bg-blue-100 text-blue-800',
                                            'selesai'  => 'bg-emerald-100 text-emerald-800',
                                            'ditolak'  => 'bg-rose-100 text-rose-800',
                                            default    => 'bg-slate-100 text-slate-700'
                                        };
                                        ?>
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold <?= $stat_badge ?>">
                                            <?= ucfirst(htmlspecialchars($row['status'])) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-3.5 text-xs text-slate-400 font-mono">
                                        <?= date('d/m/Y', strtotime($row['created_at'])) ?>
                                    </td>
                                    <td class="px-6 py-3.5 text-center">
                                        <a href="detail_pengaduan.php?id=<?= $row['id'] ?>" class="inline-flex items-center text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-3 py-1 rounded-lg transition-all">
                                            Detail ➔
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</main>

</body>
</html>
