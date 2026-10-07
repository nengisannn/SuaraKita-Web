<?php
// siswa/riwayat_pengaduan.php - Riwayat Pengaduan Siswa Secure & Modular dengan Pagination

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

require_siswa();

$siswaUsername = $_SESSION['siswa_username'];
$siswa_id      = $_SESSION['siswa_id'];
$sekolah_id    = $_SESSION['sekolah_id'];

// Parameter Search & Pagination
$keyword = sanitize_input($_GET['q'] ?? '');

$perPageOptions = [10, 20, 50, 100];
$perPage = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], $perPageOptions) ? (int)$_GET['per_page'] : 10;
$page    = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset  = ($page - 1) * $perPage;

// Prepared SQL Query Condition
$conditions = ["siswa_id = ?", "sekolah_id = ?"];
$params     = [$siswa_id, $sekolah_id];
$types      = "ii";

if ($keyword !== '') {
    $conditions[] = "(judul LIKE ? OR isi LIKE ? OR kategori LIKE ?)";
    $likeKw = '%' . $keyword . '%';
    $params[] = $likeKw;
    $params[] = $likeKw;
    $params[] = $likeKw;
    $types .= "sss";
}

$where_sql = implode(" AND ", $conditions);

// Count Total Rows for Pagination
$countRow  = db_fetch_one("SELECT COUNT(*) as total FROM pengaduan WHERE $where_sql", $params, $types);
$totalData  = $countRow['total'] ?? 0;
$totalPages = ceil($totalData / $perPage) ?: 1;

// Fetch Student Complaints
$data_params = array_merge($params, [$perPage, $offset]);
$data_types  = $types . "ii";

$pengaduan_list = db_fetch_all(
    "SELECT * FROM pengaduan WHERE $where_sql ORDER BY created_at DESC LIMIT ? OFFSET ?",
    $data_params,
    $data_types
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Pengaduan | SuaraKita</title>
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

<!-- Sidebar siswa -->
<?php include 'partials/sidebar.php'; ?>

<main class="ml-64 min-h-screen">
    <!-- Header -->
    <?php include 'partials/header.php'; ?>

    <!-- Konten -->
    <div class="p-6 mt-20">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-navy">Riwayat Pengaduan Saya</h2>
                <p class="text-slate-500 text-sm">Daftar keluhan & aspirasi yang pernah Anda sampaikan.</p>
            </div>
            <a href="buat_pengaduan.php" class="bg-navy text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-teal transition shadow-sm">
                + Buat Pengaduan Baru
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <!-- Search & Controls -->
            <form method="GET" class="p-4 border-b border-slate-200 bg-slate-50/50 flex flex-wrap gap-3 items-center justify-between">
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <input type="text" name="q" value="<?= htmlspecialchars($keyword) ?>" placeholder="Cari judul atau isi pengaduan..."
                        class="border border-slate-300 rounded-lg px-3.5 py-2 text-sm focus:ring-2 focus:ring-teal focus:border-teal w-full sm:w-72 transition" />
                    <button type="submit" class="bg-navy text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-teal transition">Cari</button>
                    <?php if (!empty($keyword)): ?>
                        <a href="riwayat_pengaduan.php" class="text-xs text-slate-500 hover:text-slate-700 underline">Reset</a>
                    <?php endif; ?>
                </div>

                <div class="flex items-center gap-2 text-xs text-slate-600 ml-auto">
                    <span>Tampilkan:</span>
                    <select name="per_page" onchange="this.form.submit()" class="border border-slate-300 rounded-lg px-2.5 py-1.5 bg-white text-sm focus:ring-2 focus:ring-teal">
                        <?php foreach ($perPageOptions as $opt): ?>
                            <option value="<?= $opt ?>" <?= $perPage == $opt ? 'selected' : '' ?>><?= $opt ?> per halaman</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-100 text-slate-700 font-semibold border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3.5">No</th>
                            <th class="px-5 py-3.5">Judul Pengaduan</th>
                            <th class="px-5 py-3.5">Kategori</th>
                            <th class="px-5 py-3.5">Urgensi</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5">Tanggal</th>
                            <th class="px-5 py-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-200">
                        <?php if (empty($pengaduan_list)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-8 text-slate-500">
                                    Belum ada pengaduan yang dibuat.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = $offset + 1; foreach ($pengaduan_list as $row): ?>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-5 py-3.5 font-mono text-slate-500"><?= $no++; ?></td>
                                    <td class="px-5 py-3.5">
                                        <div class="font-medium text-slate-800"><?= htmlspecialchars($row['judul']) ?></div>
                                        <?php if (!empty($row['lampiran'])): ?>
                                            <span class="text-xs text-teal font-medium flex items-center gap-1 mt-0.5">📎 Lampiran Terlampir</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded border border-slate-200 text-xs">
                                            <?= htmlspecialchars($row['kategori'] ?? 'Fasilitas') ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <?php
                                        $urgency_badge = match($row['urgensi'] ?? 'sedang') {
                                            'rendah' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'sedang' => 'bg-sky-50 text-sky-700 border-sky-200',
                                            'tinggi' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'darurat' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            default => 'bg-slate-50 text-slate-700 border-slate-200'
                                        };
                                        ?>
                                        <span class="px-2.5 py-1 rounded-full border text-xs font-medium <?= $urgency_badge ?>">
                                            <?= ucfirst(htmlspecialchars($row['urgensi'] ?? 'sedang')) ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <?php
                                        $status_badge = match($row['status'] ?? 'terkirim') {
                                            'terkirim', 'dikirim' => 'bg-amber-100 text-amber-800',
                                            'ditinjau' => 'bg-purple-100 text-purple-800',
                                            'diproses' => 'bg-blue-100 text-blue-800',
                                            'selesai'  => 'bg-emerald-100 text-emerald-800',
                                            'ditolak'  => 'bg-rose-100 text-rose-800',
                                            default    => 'bg-slate-100 text-slate-700'
                                        };
                                        ?>
                                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $status_badge ?>">
                                            <?= ucfirst(htmlspecialchars($row['status'])) ?>
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-xs text-slate-500 font-mono">
                                        <?= date('d/m/Y', strtotime($row['created_at'])) ?>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <a href="detail_pengaduan.php?id=<?= $row['id']; ?>"
                                           class="bg-teal/10 text-teal hover:bg-teal hover:text-white px-3 py-1.5 rounded-lg text-xs font-medium transition">
                                            Lihat Detail ➔
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
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?q=<?= urlencode($keyword) ?>&per_page=<?= $perPage ?>&page=<?= $i ?>"
                            class="px-3 py-1.5 text-xs font-medium rounded-lg border transition <?= $i == $page ? 'bg-navy text-white border-navy' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-100' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

</body>
</html>