<?php
// admin/kelola_siswa.php - Kelola Data Siswa/Mahasiswa Secure & Modular dengan Pagination

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

require_admin();

$adminUsername = $_SESSION['admin_username'];
$sekolah_id    = $_SESSION['sekolah_id'];

// Parameter Search & Pagination
$keyword = sanitize_input($_GET['q'] ?? '');

$perPageOptions = [10, 20, 50, 100];
$perPage = isset($_GET['per_page']) && in_array((int)$_GET['per_page'], $perPageOptions) ? (int)$_GET['per_page'] : 10;
$page    = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset  = ($page - 1) * $perPage;

// Prepared SQL Query Condition
$conditions = ["level = 'siswa'", "sekolah_id = ?"];
$params     = [$sekolah_id];
$types      = "i";

if ($keyword !== '') {
    $conditions[] = "(nama LIKE ? OR username LIKE ? OR nis LIKE ? OR kelas LIKE ?)";
    $likeKw = '%' . $keyword . '%';
    $params[] = $likeKw;
    $params[] = $likeKw;
    $params[] = $likeKw;
    $params[] = $likeKw;
    $types .= "ssss";
}

$where_sql = implode(" AND ", $conditions);

// Count Total Rows for Pagination
$countRow = db_fetch_one("SELECT COUNT(*) as total FROM users WHERE $where_sql", $params, $types);
$totalData  = $countRow['total'] ?? 0;
$totalPages = ceil($totalData / $perPage) ?: 1;

// Fetch Students Data
$data_params = array_merge($params, [$perPage, $offset]);
$data_types  = $types . "ii";

$siswa_list = db_fetch_all(
    "SELECT * FROM users WHERE $where_sql ORDER BY id DESC LIMIT ? OFFSET ?",
    $data_params,
    $data_types
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Mahasiswa / Siswa | Admin SuaraKita</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

<!-- Sidebar -->
<?php include 'partials/sidebar.php'; ?>

<!-- Main Content -->
<main class="ml-64 min-h-screen">

    <!-- Header -->
    <?php include 'partials/header.php'; ?>

    <!-- Content -->
    <section class="p-6 mt-20">

        <!-- Header Action -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-navy">Data Mahasiswa / Siswa</h1>
                <p class="text-slate-500 text-sm">Kelola pengguna terdaftar untuk institusi Anda.</p>
            </div>
            <a href="tambah_siswa.php"
               class="bg-navy text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-teal transition shadow-sm flex items-center gap-2">
                <span>+ Tambah Akun</span>
            </a>
        </div>

        <!-- Tabel Card & Filter -->
        <div class="bg-white shadow-sm rounded-xl border border-slate-200 overflow-hidden">
            
            <!-- Search & Limit Controls -->
            <form method="GET" class="p-4 border-b border-slate-200 bg-slate-50/50 flex flex-wrap gap-3 items-center justify-between">
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <input type="text" name="q" value="<?= htmlspecialchars($keyword) ?>" placeholder="Cari Nama, NIM/NIS, Username..."
                        class="border border-slate-300 rounded-lg px-3.5 py-2 text-sm focus:ring-2 focus:ring-teal focus:border-teal w-full sm:w-72 transition" />
                    <button type="submit" class="bg-navy text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-teal transition">Cari</button>
                    <?php if (!empty($keyword)): ?>
                        <a href="kelola_siswa.php" class="text-xs text-slate-500 hover:text-slate-700 underline">Reset</a>
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
                            <th class="px-5 py-3.5">Nama Lengkap</th>
                            <th class="px-5 py-3.5">Username</th>
                            <th class="px-5 py-3.5">NIM / NIS</th>
                            <th class="px-5 py-3.5">Kelas / Jurusan</th>
                            <th class="px-5 py-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php if (empty($siswa_list)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-8 text-slate-500">
                                    Data siswa belum tersedia atau tidak cocok dengan kata kunci.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = $offset + 1; foreach ($siswa_list as $row): ?>
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-5 py-3.5 font-mono text-slate-500"><?= $no++; ?></td>
                                    <td class="px-5 py-3.5 font-semibold text-slate-800"><?= htmlspecialchars($row['nama']); ?></td>
                                    <td class="px-5 py-3.5 font-mono text-slate-600"><?= htmlspecialchars($row['username']); ?></td>
                                    <td class="px-5 py-3.5 font-mono text-slate-600"><?= htmlspecialchars($row['nis'] ?? '-'); ?></td>
                                    <td class="px-5 py-3.5 text-slate-700"><?= htmlspecialchars($row['kelas'] ?? '-'); ?></td>
                                    <td class="px-5 py-3.5 text-center space-x-2">
                                        <a href="edit_siswa.php?id=<?= $row['id']; ?>"
                                           class="text-teal font-medium hover:underline text-xs bg-teal/10 px-2.5 py-1 rounded-md">
                                            Edit
                                        </a>
                                        <a href="hapus_siswa.php?id=<?= $row['id']; ?>"
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus akun ini?')"
                                           class="text-rose-600 font-medium hover:underline text-xs bg-rose-50 px-2.5 py-1 rounded-md border border-rose-200">
                                            Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <?php if ($totalPages > 1): ?>
            <div class="flex flex-col sm:flex-row justify-between items-center p-4 border-t border-slate-200 bg-slate-50 gap-3">
                <div class="text-xs text-slate-500">
                    Menampilkan Halaman <strong><?= $page ?></strong> dari <strong><?= $totalPages ?></strong> (Total: <strong><?= $totalData ?></strong> akun)
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
    </section>

</main>
</body>
</html>
