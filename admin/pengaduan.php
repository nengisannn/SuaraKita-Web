<?php
// admin/pengaduan.php - Filter & Search Admin Modular & Secure

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

require_admin();

$adminUsername = $_SESSION['admin_username'];
$sekolah_id    = $_SESSION['sekolah_id'];

// Parameter Filter & Search
$keyword     = sanitize_input($_GET['q'] ?? '');
$cat_filter  = sanitize_input($_GET['kategori'] ?? '');
$urg_filter  = sanitize_input($_GET['urgensi'] ?? '');
$stat_filter = sanitize_input($_GET['status'] ?? '');
$tgl_mulai   = sanitize_input($_GET['tgl_mulai'] ?? '');
$tgl_selesai = sanitize_input($_GET['tgl_selesai'] ?? '');

// Pagination Settings
$perPageOptions = [10, 20, 50, 100];
$perPage = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], $perPageOptions) ? (int)$_GET['per_page'] : 10;
$page    = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset  = ($page - 1) * $perPage;

// Construct Dynamic Prepared SQL Query
$conditions = ["p.sekolah_id = ?"];
$params     = [$sekolah_id];
$types      = "i";

if ($keyword !== '') {
    $conditions[] = "(u.nama LIKE ? OR u.kelas LIKE ? OR p.judul LIKE ? OR p.isi LIKE ?)";
    $likeKw = '%' . $keyword . '%';
    $params[] = $likeKw;
    $params[] = $likeKw;
    $params[] = $likeKw;
    $params[] = $likeKw;
    $types .= "ssss";
}

if ($cat_filter !== '') {
    $conditions[] = "p.kategori = ?";
    $params[]     = $cat_filter;
    $types       .= "s";
}

if ($urg_filter !== '') {
    $conditions[] = "p.urgensi = ?";
    $params[]     = $urg_filter;
    $types       .= "s";
}

if ($stat_filter !== '') {
    $conditions[] = "p.status = ?";
    $params[]     = $stat_filter;
    $types       .= "s";
}

if ($tgl_mulai !== '') {
    $conditions[] = "DATE(p.created_at) >= ?";
    $params[]     = $tgl_mulai;
    $types       .= "s";
}

if ($tgl_selesai !== '') {
    $conditions[] = "DATE(p.created_at) <= ?";
    $params[]     = $tgl_selesai;
    $types       .= "s";
}

$where_sql = implode(" AND ", $conditions);

// Hitung Total Data untuk Pagination
$countRow = db_fetch_one("
    SELECT COUNT(*) as total
    FROM pengaduan p
    JOIN users u ON p.siswa_id = u.id
    WHERE $where_sql
", $params, $types);

$totalData  = $countRow['total'] ?? 0;
$totalPages = ceil($totalData / $perPage) ?: 1;

// Fetch Data Pengaduan
$data_params = array_merge($params, [$perPage, $offset]);
$data_types  = $types . "ii";

$pengaduan_list = db_fetch_all("
    SELECT p.*, u.nama, u.kelas
    FROM pengaduan p
    JOIN users u ON p.siswa_id = u.id
    WHERE $where_sql
    ORDER BY p.created_at DESC
    LIMIT ? OFFSET ?
", $data_params, $data_types);

$categories = ['Fasilitas', 'Akademik', 'Keuangan', 'Kebersihan', 'Keamanan', 'Lainnya'];
$urgencies  = ['rendah', 'sedang', 'tinggi', 'darurat'];
$statuses   = ['terkirim', 'ditinjau', 'diproses', 'selesai', 'ditolak'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pengaduan | Admin SuaraKita</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        html, body {
            font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
        }
    </style>
    <script src="../assets/js/tailwindcss.js"></script>
    <script>window.tailwind || document.write('<script src="https://cdn.tailwindcss.com"><\/script>')</script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        mint: '#DDF4E7',
                        green: '#67C090',
                        teal: '#26667F',
                        navy: '#124170',
                    },
                },
            },
        }
    </script>
</head>

<body class="bg-slate-50 text-slate-800">

<?php include 'partials/sidebar.php'; ?>

<main class="ml-64 min-h-screen">
<?php include 'partials/header.php'; ?>

<section class="p-6 mt-20">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-navy">Kelola Data Pengaduan</h1>
            <p class="text-slate-500 text-sm">Gunakan pencarian dan filter untuk menyaring laporan siswa/mahasiswa.</p>
        </div>
        <?php
        $export_query = http_build_query($_GET);
        ?>
        <div class="flex items-center gap-2">
            <a href="cetak_pdf.php?<?= $export_query ?>" target="_blank" class="bg-rose-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-rose-700 transition shadow-sm flex items-center gap-1.5">
                <span>📄 Cetak Laporan (PDF)</span>
            </a>
            <a href="ekspor_excel.php?<?= $export_query ?>" class="bg-emerald-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-emerald-700 transition shadow-sm flex items-center gap-1.5">
                <span>📊 Ekspor Excel</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200 mb-6">
        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Search Input -->
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">🔍 Pencarian Kata Kunci</label>
                    <input type="text" name="q" value="<?= htmlspecialchars($keyword) ?>" placeholder="Cari Judul, Isi, Nama, atau Kelas..."
                        class="w-full border border-slate-300 rounded-lg px-3.5 py-2 text-sm focus:ring-2 focus:ring-teal focus:border-teal transition">
                </div>

                <!-- Kategori Filter -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">📂 Kategori</label>
                    <select name="kategori" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-teal">
                        <option value="">-- Semua Kategori --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat ?>" <?= $cat_filter === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Urgensi Filter -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">⚡ Tingkat Urgensi</label>
                    <select name="urgensi" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-teal">
                        <option value="">-- Semua Urgensi --</option>
                        <?php foreach ($urgencies as $urg): ?>
                            <option value="<?= $urg ?>" <?= $urg_filter === $urg ? 'selected' : '' ?>><?= ucfirst($urg) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">📊 Status Pengaduan</label>
                    <select name="status" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-teal">
                        <option value="">-- Semua Status --</option>
                        <?php foreach ($statuses as $st): ?>
                            <option value="<?= $st ?>" <?= $stat_filter === $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tanggal Mulai -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">📅 Dari Tanggal</label>
                    <input type="date" name="tgl_mulai" value="<?= htmlspecialchars($tgl_mulai) ?>" class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-sm bg-white focus:ring-2 focus:ring-teal">
                </div>

                <!-- Tanggal Selesai -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">📅 Sampai Tanggal</label>
                    <input type="date" name="tgl_selesai" value="<?= htmlspecialchars($tgl_selesai) ?>" class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-sm bg-white focus:ring-2 focus:ring-teal">
                </div>

                <!-- Tombol Action -->
                <div class="flex items-end gap-2">
                    <button type="submit" class="w-full bg-navy text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-teal transition">
                        Terapkan Filter
                    </button>
                    <a href="pengaduan.php" class="bg-slate-100 text-slate-600 border border-slate-300 px-4 py-2 rounded-lg text-sm font-medium hover:bg-slate-200 transition">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-100 text-slate-700 font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3.5">No</th>
                        <th class="px-4 py-3.5">Pelapor</th>
                        <th class="px-4 py-3.5">Kategori</th>
                        <th class="px-4 py-3.5">Judul Pengaduan</th>
                        <th class="px-4 py-3.5">Urgensi</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5">Tanggal</th>
                        <th class="px-4 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php if (empty($pengaduan_list)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-8 text-slate-500">
                                🔍 Tidak ditemukan pengaduan yang sesuai dengan filter.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = $offset + 1; foreach ($pengaduan_list as $p): ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 py-3.5 font-mono text-slate-500"><?= $no++ ?></td>
                                <td class="px-4 py-3.5">
                                    <?php if (!empty($p['is_anonim'])): ?>
                                        <div class="font-medium text-slate-700 flex items-center gap-1">🔒 Anonim <span class="text-[10px] bg-slate-200 text-slate-600 px-1.5 py-0.5 rounded">Rahasia</span></div>
                                        <div class="text-xs text-slate-400">Identitas Disembunyikan</div>
                                    <?php else: ?>
                                        <div class="font-medium text-slate-800"><?= htmlspecialchars($p['nama']) ?></div>
                                        <div class="text-xs text-slate-400"><?= htmlspecialchars($p['kelas']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded border border-slate-200 text-xs">
                                        <?= htmlspecialchars($p['kategori'] ?? 'Fasilitas') ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-medium text-slate-800 max-w-xs truncate"><?= htmlspecialchars($p['judul']) ?></div>
                                    <?php if (!empty($p['lampiran'])): ?>
                                        <span class="text-xs text-teal font-medium flex items-center gap-1 mt-0.5">📎 Ada Lampiran</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5">
                                    <?php
                                    $urgency_badge = match($p['urgensi'] ?? 'sedang') {
                                        'rendah' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'sedang' => 'bg-sky-50 text-sky-700 border-sky-200',
                                        'tinggi' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'darurat' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        default => 'bg-slate-50 text-slate-700 border-slate-200'
                                    };
                                    ?>
                                    <span class="px-2.5 py-1 rounded-full border text-xs font-medium <?= $urgency_badge ?>">
                                        <?= ucfirst(htmlspecialchars($p['urgensi'] ?? 'sedang')) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <?php
                                    $status_badge = match($p['status'] ?? 'terkirim') {
                                        'terkirim', 'dikirim' => 'bg-amber-100 text-amber-800',
                                        'ditinjau' => 'bg-purple-100 text-purple-800',
                                        'diproses' => 'bg-blue-100 text-blue-800',
                                        'selesai'  => 'bg-emerald-100 text-emerald-800',
                                        'ditolak'  => 'bg-rose-100 text-rose-800',
                                        default    => 'bg-slate-100 text-slate-700'
                                    };
                                    ?>
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $status_badge ?>">
                                        <?= ucfirst(htmlspecialchars($p['status'])) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-xs text-slate-500 font-mono">
                                    <?= date('d/m/Y', strtotime($p['created_at'])) ?>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <a href="detail_pengaduan.php?id=<?= $p['id']; ?>"
                                       class="bg-teal/10 text-teal hover:bg-teal hover:text-white px-3 py-1.5 rounded-lg text-xs font-medium transition">
                                       Respon & Detail ➔
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls -->
        <?php if ($totalPages > 1): ?>
        <div class="flex flex-col sm:flex-row justify-between items-center p-4 border-t border-slate-200 bg-slate-50 gap-3">
            <div class="text-xs text-slate-500">
                Menampilkan Halaman <strong><?= $page ?></strong> dari <strong><?= $totalPages ?></strong> (Total: <strong><?= $totalData ?></strong> pengaduan)
            </div>
            <div class="flex items-center gap-1">
                <?php
                $queryParams = $_GET;
                for ($i = 1; $i <= $totalPages; $i++):
                    $queryParams['page'] = $i;
                    $link = '?' . http_build_query($queryParams);
                ?>
                    <a href="<?= $link ?>"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg border transition <?= $i == $page ? 'bg-navy text-white border-navy' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-100' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

</main>
</body>
</html>
