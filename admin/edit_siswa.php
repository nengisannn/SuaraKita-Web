<?php
// admin/edit_siswa.php - Edit Data Mahasiswa

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

require_admin();

$adminUsername = $_SESSION['admin_username'];
$id            = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$sekolah_id    = $_SESSION['sekolah_id'];

if (!$id) {
    header("Location: kelola_siswa.php");
    exit;
}

$siswa = db_fetch_one(
    "SELECT * FROM users WHERE id = ? AND level = 'siswa' AND sekolah_id = ?",
    [$id, $sekolah_id],
    "ii"
);

if (!$siswa) {
    header("Location: kelola_siswa.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Data Mahasiswa | Admin SuaraKita</title>
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

<body class="bg-slate-50 text-slate-800 font-sans">

<?php include 'partials/sidebar.php'; ?>

<main class="ml-64 min-h-screen">
<?php include 'partials/header.php'; ?>

<section class="p-6 mt-20 max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-navy">Edit Data Mahasiswa</h1>
        <p class="text-slate-500 text-sm">Perbarui informasi profil mahasiswa terdaftar.</p>
    </div>

    <form action="../aksi/aksi_edit_siswa.php" method="POST" class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 space-y-5">
        <input type="hidden" name="id" value="<?= $siswa['id']; ?>">

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($siswa['nama']); ?>" required
                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal">
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">NIM / NIS</label>
            <input type="text" name="nis" value="<?= htmlspecialchars($siswa['nis']); ?>" required
                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal">
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">Kelas / Jurusan</label>
            <input type="text" name="kelas" value="<?= htmlspecialchars($siswa['kelas']); ?>" required
                class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal">
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="kelola_siswa.php" class="px-5 py-2.5 border border-slate-300 rounded-lg text-sm text-slate-600 hover:bg-slate-100 font-medium transition">
                Batal
            </a>
            <button type="submit" class="bg-navy text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-teal transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</section>

</main>
</body>
</html>
