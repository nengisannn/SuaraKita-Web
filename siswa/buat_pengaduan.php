<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

require_siswa();

$siswaUsername = $_SESSION['siswa_username'];
$categories = ['Fasilitas', 'Akademik', 'Keuangan', 'Kebersihan', 'Keamanan', 'Lainnya'];
$urgencies = [
    'rendah'  => '🟢 Rendah (Masalah biasa / tidak mendesak)',
    'sedang'  => '🔵 Sedang (Membutuhkan perhatian)',
    'tinggi'  => '🟠 Tinggi (Penting / berdampak pada aktivitas)',
    'darurat' => '🔴 Darurat (Sangat mendesak / kondisi berbahaya)'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Pengaduan | SuaraKita</title>
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
    <!-- Header siswa -->
    <?php include 'partials/header.php'; ?>

    <!-- Konten Buat Pengaduan -->
    <div class="max-w-3xl mx-auto mt-28 mb-12 bg-white p-8 rounded-xl shadow-sm border border-slate-200">

        <h2 class="text-2xl font-bold text-navy mb-1">
            Form Pengaduan Mahasiswa / Siswa
        </h2>
        <p class="text-slate-500 text-sm mb-6">Sampaikan aspirasi dan kendala Anda dengan jelas untuk ditindaklanjuti.</p>

        <form action="../aksi/aksi_pengaduan.php" method="POST" enctype="multipart/form-data" class="space-y-5">

            <!-- Judul -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Judul Pengaduan <span class="text-red-500">*</span>
                </label>
                <input type="text" name="judul" required placeholder="Contoh: AC Rusak di Ruang 304"
                    class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal focus:border-teal transition">
            </div>

            <!-- Grid Kategori & Urgensi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Kategori -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Kategori Pengaduan <span class="text-red-500">*</span>
                    </label>
                    <select name="kategori" required class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal focus:border-teal transition">
                        <option value="" disabled selected>-- Pilih Kategori --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat ?>"><?= $cat ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tingkat Urgensi -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Tingkat Urgensi <span class="text-red-500">*</span>
                    </label>
                    <select name="urgensi" required class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-teal focus:border-teal transition">
                        <option value="" disabled>-- Pilih Prioritas --</option>
                        <?php foreach ($urgencies as $key => $label): ?>
                            <option value="<?= $key ?>" <?= $key === 'sedang' ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Isi -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Isi Pengaduan <span class="text-red-500">*</span>
                </label>
                <textarea name="isi" rows="5" required placeholder="Jelaskan kronologi atau rincian keluhan Anda..."
                    class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal focus:border-teal transition"></textarea>
            </div>

            <!-- Upload Bukti Lampiran (Standard File Input dengan Visual Feedback) -->
            <div>
                <label for="lampiranInput" class="block text-sm font-medium text-slate-700 mb-1">
                    Upload Bukti Foto / Dokumen Pendukung <span class="text-xs text-slate-400 font-normal">(Opsional, JPG/PNG/PDF, Maks 5MB)</span>
                </label>
                <div class="mt-1">
                    <input id="lampiranInput" name="lampiran" type="file" accept="image/*,.pdf" onchange="previewFileName(this)"
                        class="block w-full text-sm text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal file:text-white hover:file:bg-navy border border-slate-300 rounded-lg cursor-pointer bg-slate-50 focus:outline-none transition">
                </div>
                <p id="fileNameDisplay" class="mt-2 text-xs font-semibold text-teal hidden flex items-center gap-1">
                    <span>📎 File terpilih:</span> <span id="fileNameText" class="underline"></span>
                </p>
            </div>

            <!-- Opsi Pengaduan Anonim (Whistleblower Protection) -->
            <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="is_anonim" value="1" class="mt-1 rounded border-slate-300 text-teal focus:ring-teal h-4 w-4">
                    <div>
                        <span class="text-sm font-semibold text-slate-800 block">🔒 Kirim sebagai Anonim (Rahasiakan Identitas Saya)</span>
                        <span class="text-xs text-slate-500 block">Nama dan identitas Anda akan disamarkan menjadi <strong>"Anonim"</strong> di laporan publik dan panel manajemen.</span>
                    </div>
                </label>
            </div>

            <!-- Submit -->
            <div class="pt-2">
                <button type="submit"
                    class="bg-navy text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-teal focus:outline-none focus:ring-2 focus:ring-teal active:scale-[0.98] transition">
                    Kirim Pengaduan
                </button>
            </div>

        </form>
    </div>
</main>

<script>
function previewFileName(input) {
    const display = document.getElementById('fileNameDisplay');
    const text = document.getElementById('fileNameText');
    if (input.files && input.files[0]) {
        text.textContent = input.files[0].name + " (" + (input.files[0].size / 1024 / 1024).toFixed(2) + " MB)";
        display.classList.remove('hidden');
    } else {
        display.classList.add('hidden');
    }
}
</script>

</body>
</html>
