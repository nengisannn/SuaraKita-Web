<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

require_admin();

$adminUsername = $_SESSION['admin_username'];
$sekolah_id    = $_SESSION['sekolah_id'];

// Ambil info sekolah
$sekolah = db_fetch_one("SELECT nama_sekolah FROM sekolah WHERE id = ?", [$sekolah_id], "i");
$nama_sekolah = $sekolah['nama_sekolah'] ?? 'Institusi';

// Statistik Dashboard
$total_pengaduan = db_fetch_one("SELECT COUNT(id) AS count FROM pengaduan WHERE sekolah_id = ?", [$sekolah_id], "i")['count'] ?? 0;
$total_proses    = db_fetch_one("SELECT COUNT(id) AS count FROM pengaduan WHERE sekolah_id = ? AND status IN ('terkirim','ditinjau','diproses')", [$sekolah_id], "i")['count'] ?? 0;
$total_selesai   = db_fetch_one("SELECT COUNT(id) AS count FROM pengaduan WHERE sekolah_id = ? AND status = 'selesai'", [$sekolah_id], "i")['count'] ?? 0;
$total_siswa     = db_fetch_one("SELECT COUNT(id) AS count FROM users WHERE sekolah_id = ? AND level = 'siswa'", [$sekolah_id], "i")['count'] ?? 0;

// Rata-rata Rating CSAT Kepuasan
$csat_data  = db_fetch_one("SELECT AVG(rating) as avg_rating, COUNT(rating) as rated_count FROM pengaduan WHERE sekolah_id = ? AND rating IS NOT NULL", [$sekolah_id], "i");
$avg_rating  = number_format((float)($csat_data['avg_rating'] ?? 0), 1);
$rated_count = $csat_data['rated_count'] ?? 0;

// Data Grafik Pengaduan per Bulan
$q_grafik = db_fetch_all(
    "SELECT MONTH(created_at) AS bulan, COUNT(id) AS total
     FROM pengaduan
     WHERE sekolah_id = ? AND YEAR(created_at) = YEAR(CURDATE())
     GROUP BY MONTH(created_at) ORDER BY MONTH(created_at)",
    [$sekolah_id],
    "i"
);

$nama_bulan_map = [1=>'Jan', 2=>'Feb', 3=>'Mar', 4=>'Apr', 5=>'Mei', 6=>'Jun', 7=>'Jul', 8=>'Agu', 9=>'Sep', 10=>'Okt', 11=>'Nov', 12=>'Des'];
$bulan_labels = [];
$total_bulanan = [];

foreach ($q_grafik as $g) {
    $bulan_labels[]  = $nama_bulan_map[$g['bulan']] ?? ('Bln ' . $g['bulan']);
    $total_bulanan[] = (int)$g['total'];
}

// 5 Pengaduan Terbaru
$recent_list = db_fetch_all("
    SELECT p.*, u.nama, u.kelas
    FROM pengaduan p
    JOIN users u ON p.siswa_id = u.id
    WHERE p.sekolah_id = ?
    ORDER BY p.created_at DESC LIMIT 5
", [$sekolah_id], "i");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin | SuaraKita</title>
    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                    🏢 <?= htmlspecialchars($nama_sekolah) ?>
                </span>
                <h2 class="text-2xl font-extrabold tracking-tight text-slate-900 mb-1.5">
                    Dashboard Pengelola
                </h2>
                <p class="text-slate-500 text-xs md:text-sm max-w-xl leading-relaxed">
                    Pantau seluruh laporan, evaluasi indeks kepuasan, dan tanggapi aspirasi mahasiswa secara cepat & efisien.
                </p>
            </div>
            <div class="relative z-10 flex items-center gap-3">
                <a href="pengaduan.php" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-xs font-semibold shadow-xs transition-all active:scale-[0.98]">
                    <span>Kelola Pengaduan ➔</span>
                </a>
            </div>
        </div>

        <!-- STATISTIC WIDGETS (Clean Hairline Cards Grid) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Card Total -->
            <div class="bg-white p-4 rounded-2xl shadow-2xs border border-slate-200/80 hover:border-slate-300 transition-all flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Laporan</span>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-extrabold text-slate-900 font-mono tracking-tight"><?= $total_pengaduan ?></span>
                        <span class="text-[11px] font-medium text-slate-400">pengaduan</span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-600 text-base">
                    📁
                </div>
            </div>

            <!-- Card Diproses -->
            <div class="bg-white p-4 rounded-2xl shadow-2xs border border-slate-200/80 hover:border-amber-200 transition-all flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Perlu Penanganan</span>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-extrabold text-amber-600 font-mono tracking-tight"><?= $total_proses ?></span>
                        <span class="text-[11px] font-medium text-slate-400">aktif</span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 text-base">
                    ⏳
                </div>
            </div>

            <!-- Card Selesai -->
            <div class="bg-white p-4 rounded-2xl shadow-2xs border border-slate-200/80 hover:border-emerald-200 transition-all flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Tuntas Selesai</span>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-extrabold text-emerald-600 font-mono tracking-tight"><?= $total_selesai ?></span>
                        <span class="text-[11px] font-medium text-slate-400">tuntas</span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 text-base">
                    ✅
                </div>
            </div>

            <!-- Card CSAT Average -->
            <div class="bg-white p-4 rounded-2xl shadow-2xs border border-slate-200/80 hover:border-emerald-200 transition-all flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Kepuasan CSAT</span>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-extrabold text-emerald-600 font-mono tracking-tight"><?= $avg_rating > 0 ? $avg_rating : '-' ?></span>
                        <span class="text-[11px] font-medium text-slate-400">/ 5.0 (<?= $rated_count ?>)</span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 text-base">
                    ⭐
                </div>
            </div>

        </div>

        <!-- CHART & RECENT DATA SECTION -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Chart Container (2-Col) -->
            <div class="lg:col-span-2 bg-white/90 backdrop-blur-md rounded-2xl p-6 shadow-xs border border-slate-200/80">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 tracking-tight">Tren Pengaduan Bulanan</h3>
                        <p class="text-xs text-slate-500">Volume laporan yang masuk sepanjang tahun <?= date('Y') ?></p>
                    </div>
                </div>
                <div class="relative h-56">
                    <canvas id="chartPengaduan"></canvas>
                </div>
            </div>

            <!-- Audit Logs & SLA Performance Widget (1-Col) -->
            <div class="bg-white/90 backdrop-blur-md rounded-2xl p-5 shadow-xs border border-slate-200/80 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Audit Activity Log</span>
                        <span class="text-[10px] bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full font-bold">Real-time</span>
                    </div>
                    <?php
                    $logs = db_fetch_all("SELECT * FROM activity_logs WHERE sekolah_id = ? ORDER BY created_at DESC LIMIT 3", [$sekolah_id], "i");
                    ?>
                    <div class="space-y-2.5 text-xs">
                        <?php if (empty($logs)): ?>
                            <p class="text-slate-400 italic">Belum ada catatan aktivitas.</p>
                        <?php else: ?>
                            <?php foreach ($logs as $l): ?>
                                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                    <div class="flex items-center justify-between text-[11px] font-semibold text-slate-800 mb-0.5">
                                        <span><?= htmlspecialchars($l['action']) ?></span>
                                        <span class="text-[10px] text-slate-400 font-mono"><?= date('H:i', strtotime($l['created_at'])) ?></span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 line-clamp-1"><?= htmlspecialchars($l['description']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-100 mt-4 flex items-center justify-between text-xs">
                    <span class="text-slate-500 font-medium">Mahasiswa Terdaftar:</span>
                    <span class="font-extrabold text-slate-900 font-mono"><?= $total_siswa ?> Akun</span>
                </div>
            </div>

        </div>

        <!-- RECENT COMPLAINTS LIST -->
        <div class="bg-white/90 backdrop-blur-md rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 tracking-tight">Laporan Pengaduan Terbaru</h3>
                    <p class="text-xs text-slate-500">5 pengaduan terakhir yang masuk dari mahasiswa.</p>
                </div>
                <a href="pengaduan.php" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition">
                    Lihat Selengkapnya ➔
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-500 text-xs font-semibold uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3">Pelapor</th>
                            <th class="px-6 py-3">Kategori</th>
                            <th class="px-6 py-3">Judul Pengaduan</th>
                            <th class="px-6 py-3">Urgensi</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        <?php if (empty($recent_list)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-10 text-slate-400 text-xs italic">
                                    Belum ada pengaduan yang masuk.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_list as $row): ?>
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-6 py-3.5">
                                        <?php if (!empty($row['is_anonim'])): ?>
                                            <div class="font-bold text-slate-700 flex items-center gap-1">🔒 Anonim <span class="text-[10px] bg-slate-200 text-slate-600 px-1.5 py-0.5 rounded">Rahasia</span></div>
                                        <?php else: ?>
                                            <div class="font-bold text-slate-900"><?= htmlspecialchars($row['nama']) ?></div>
                                            <div class="text-xs text-slate-400"><?= htmlspecialchars($row['kelas']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-3.5 text-xs text-slate-600">
                                        <?= htmlspecialchars($row['kategori'] ?? 'Fasilitas') ?>
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <div class="font-bold text-slate-900 truncate max-w-xs"><?= htmlspecialchars($row['judul']) ?></div>
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
                                    <td class="px-6 py-3.5 text-center">
                                        <a href="detail_pengaduan.php?id=<?= $row['id'] ?>" class="inline-flex items-center text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-3 py-1 rounded-lg transition-all">
                                            Respon ➔
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

<script>
    const ctx = document.getElementById('chartPengaduan').getContext('2d');
    const chartPengaduan = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($bulan_labels) ?>,
            datasets: [{
                label: 'Jumlah Pengaduan',
                data: <?= json_encode($total_bulanan) ?>,
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22, 163, 74, 0.08)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#16a34a',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
</script>

</body>
</html>
