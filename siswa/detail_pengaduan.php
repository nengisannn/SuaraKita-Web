<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

require_siswa();

$siswa_id     = $_SESSION['siswa_id'];
$sekolah_id   = $_SESSION['sekolah_id'];
$pengaduan_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$pengaduan_id) {
    header("Location: dashboard_siswa.php");
    exit;
}

// Ambil detail pengaduan (Validasi IDOR: p.siswa_id = $siswa_id)
$data = db_fetch_one(
    "SELECT p.*, u.nama, u.kelas
     FROM pengaduan p
     JOIN users u ON p.siswa_id = u.id
     WHERE p.id = ? AND p.sekolah_id = ? AND p.siswa_id = ?",
    [$pengaduan_id, $sekolah_id, $siswa_id],
    "iii"
);

if (!$data) {
    http_response_code(403);
    die("Akses Ditolak: Pengaduan tidak ditemukan atau Anda tidak memiliki akses ke pengaduan ini.");
}

// Ambil tanggapan pengaduan
$tanggapan_list = db_fetch_all(
    "SELECT * FROM tanggapan_pengaduan WHERE pengaduan_id = ? ORDER BY created_at ASC",
    [$pengaduan_id],
    "i"
);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengaduan</title>
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
<body class="bg-violet-50">
    <!-- Sidebar -->
    <?php include 'partials/sidebar.php'; ?>
    <main class="ml-64 min-h-screen bg-violet-50">

    <!-- Header -->
    <?php include 'partials/header.php'; ?>

    <!-- Content -->
    <div class="p-6 mt-20 max-w-5xl mx-auto space-y-6">

        <!-- Judul Halaman -->
        <h2 class="text-2xl font-bold text-navy">
            Detail Pengaduan
        </h2>

        <!-- Ringkasan Pengaduan -->
        <div class="bg-white rounded-lg shadow p-6 space-y-4">

            <!-- Kategori, Urgensi & SLA Badges -->
            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                <span class="bg-slate-100 text-slate-700 px-3 py-1 rounded-full border border-slate-200">
                    📂 Kategori: <?= htmlspecialchars($data['kategori'] ?? 'Fasilitas') ?>
                </span>
                <?php
                $urgency_class = match($data['urgensi'] ?? 'sedang') {
                    'rendah' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'sedang' => 'bg-sky-50 text-sky-700 border-sky-200',
                    'tinggi' => 'bg-amber-50 text-amber-700 border-amber-200',
                    'darurat' => 'bg-rose-50 text-rose-700 border-rose-200',
                    default => 'bg-slate-50 text-slate-700 border-slate-200'
                };
                ?>
                <span class="px-3 py-1 rounded-full border <?= $urgency_class ?>">
                    ⚡ Urgensi: <?= ucfirst(htmlspecialchars($data['urgensi'] ?? 'sedang')) ?>
                </span>

                <?php if (!empty($data['deadline_sla'])): ?>
                    <span class="px-3 py-1 rounded-full border border-emerald-200 bg-emerald-50 text-emerald-800">
                        ⏱️ Estimasi Penanganan: <?= date('d M Y', strtotime($data['deadline_sla'])) ?>
                    </span>
                <?php endif; ?>
            </div>

            <!-- Judul Pengaduan -->
            <h3 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-2">
                <?= htmlspecialchars($data['judul']) ?>
            </h3>

            <!-- Isi Pengaduan -->
            <div class="text-gray-700 leading-relaxed min-h-[100px]">
                <?= nl2br(htmlspecialchars($data['isi'])) ?>
            </div>

            <!-- Lampiran Bukti Foto / Dokumen -->
            <?php if (!empty($data['lampiran'])): ?>
                <div class="border-t border-slate-100 pt-4 mt-4">
                    <p class="text-xs font-semibold text-slate-500 mb-2">📎 Bukti Lampiran Pengaduan:</p>
                    <?php
                    $ext = strtolower(pathinfo($data['lampiran'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])):
                    ?>
                        <a href="../assets/uploads/<?= htmlspecialchars($data['lampiran']) ?>" target="_blank">
                            <img src="../assets/uploads/<?= htmlspecialchars($data['lampiran']) ?>" alt="Bukti Lampiran" class="max-h-64 rounded-lg border border-slate-200 shadow-sm hover:opacity-90 transition">
                        </a>
                    <?php else: ?>
                        <a href="../assets/uploads/<?= htmlspecialchars($data['lampiran']) ?>" target="_blank" class="inline-flex items-center gap-2 bg-slate-100 text-slate-700 px-4 py-2 rounded-lg border border-slate-200 hover:bg-slate-200 text-sm font-medium transition">
                            📄 Download Dokumen Lampiran (.<?= $ext ?>)
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Timeline Status Pengaduan -->
            <div class="border-t border-slate-100 pt-5 mt-4">
                <p class="text-xs font-semibold text-slate-500 mb-3">📌 Progres Penanganan Pengaduan:</p>

                <?php if ($data['status'] === 'ditolak'): ?>
                    <div class="bg-rose-50 border border-rose-200 rounded-lg p-4 text-sm text-rose-800 space-y-1">
                        <p class="font-bold text-rose-900 flex items-center gap-1">❌ Status: Pengaduan Ditolak</p>
                        <p class="text-slate-700"><strong>Alasan Penolakan:</strong> <?= nl2br(htmlspecialchars($data['alasan_penolakan'] ?? 'Tidak ada alasan spesifik.')) ?></p>
                    </div>
                <?php else: ?>
                    <?php
                    $statuses = ['terkirim', 'ditinjau', 'diproses', 'selesai'];
                    $current_status = ($data['status'] === 'dikirim') ? 'terkirim' : $data['status'];
                    $current_idx = array_search($current_status, $statuses);
                    if ($current_idx === false) $current_idx = 0;
                    ?>
                    <div class="grid grid-cols-4 gap-2 text-center text-xs font-medium">
                        <?php foreach ($statuses as $idx => $st): ?>
                            <?php
                            $is_active = $idx <= $current_idx;
                            $st_labels = [
                                'terkirim' => '1. Terkirim',
                                'ditinjau' => '2. Ditinjau',
                                'diproses' => '3. Diproses',
                                'selesai'  => '4. Selesai'
                            ];
                            ?>
                            <div class="p-2.5 rounded-lg border transition <?= $is_active ? 'bg-teal/10 border-teal text-navy font-bold' : 'bg-slate-50 border-slate-200 text-slate-400' ?>">
                                <?= $st_labels[$st] ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Meta Informasi -->
            <div class="text-sm text-slate-500 flex flex-wrap justify-between items-center border-t border-slate-100 pt-4">
                <span>👤 Pelapor: <strong><?= htmlspecialchars($data['nama']) ?></strong> (<?= htmlspecialchars($data['kelas']) ?>)</span>
                <span>📅 Tanggal: <?= date('d M Y H:i', strtotime($data['created_at'])) ?></span>
            </div>

            <!-- Modul Rating Kepuasan CSAT (Jika status selesai) -->
            <?php if ($data['status'] === 'selesai'): ?>
                <div class="border-t border-slate-100 pt-5 mt-4">
                    <?php if (!empty($data['rating'])): ?>
                        <!-- Tampilan Rating Terkirim -->
                        <div class="bg-amber-50/50 border border-amber-200 rounded-xl p-4 text-slate-700">
                            <p class="text-xs font-bold text-amber-800 uppercase tracking-wider mb-1">⭐ Penilaian Kepuasan Anda</p>
                            <div class="flex items-center gap-2 mb-1">
                                <div class="text-amber-400 text-lg">
                                    <?= str_repeat('★', (int)$data['rating']) ?><?= str_repeat('☆', 5 - (int)$data['rating']) ?>
                                </div>
                                <span class="font-bold text-sm text-slate-800"><?= (int)$data['rating'] ?> / 5.0</span>
                            </div>
                            <?php if (!empty($data['ulasan_rating'])): ?>
                                <p class="text-sm italic text-slate-600">"<?= htmlspecialchars($data['ulasan_rating']) ?>"</p>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <!-- Form Rating Bintang 1-5 -->
                        <div class="bg-teal/5 border border-teal/20 rounded-xl p-5 space-y-3">
                            <div>
                                <h4 class="font-bold text-navy text-sm">⭐ Berikan Penilaian Kepuasan Penanganan Pengaduan</h4>
                                <p class="text-xs text-slate-500">Bagaimana kualitas dan kecepatan pelayanan pengelola terhadap pengaduan ini?</p>
                            </div>
                            <form action="../aksi/aksi_rating.php" method="POST" class="space-y-3">
                                <input type="hidden" name="pengaduan_id" value="<?= $data['id'] ?>">

                                <!-- Star Selection Radios -->
                                <div class="flex items-center gap-4 text-2xl cursor-pointer">
                                    <div class="flex flex-row-reverse justify-end gap-1 text-slate-300 focus-within:text-amber-400 group">
                                        <?php for ($s = 5; $s >= 1; $s--): ?>
                                            <input type="radio" id="star<?= $s ?>" name="rating" value="<?= $s ?>" required class="sr-only peer">
                                            <label for="star<?= $s ?>" class="peer-checked:text-amber-400 hover:text-amber-400 hover:peer-all:text-amber-400 cursor-pointer transition">★</label>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="text-xs text-slate-500 font-medium">(Pilih 1 - 5 Bintang)</span>
                                </div>

                                <textarea name="ulasan_rating" rows="2" placeholder="Tulis masukan atau ucapan terima kasih (opsional)..." class="w-full border border-slate-300 rounded-lg p-3 text-xs focus:ring-2 focus:ring-teal bg-white"></textarea>

                                <button type="submit" class="bg-teal text-white px-5 py-2 rounded-lg text-xs font-semibold hover:bg-navy transition">
                                    Kirim Penilaian Kepuasan
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Chat / Tanggapan Pengaduan -->
        <div class="bg-white rounded-lg shadow p-6 space-y-4">
            <h3 class="text-xl font-semibold text-navy">
                Tanggapan
            </h3>

            <?php if (empty($tanggapan_list)): ?>
                <p class="text-gray-500 italic text-center">
                    Belum ada tanggapan.
                </p>
            <?php else: ?>
                <?php foreach ($tanggapan_list as $row): ?>
                    <div class="<?= $row['pengirim'] == 'siswa' ? 'text-right' : 'text-left' ?>">
                        <div class="
                            inline-block
                            max-w-[75%]
                            px-4 py-2 rounded-lg
                            <?= $row['pengirim'] == 'siswa'
                                ? 'bg-teal text-white'
                                : 'bg-mint text-navy'
                            ?>
                        ">
                            <p class="text-sm font-semibold mb-1">
                                <?= ucfirst($row['pengirim']) ?>
                            </p>
                            <p class="text-sm">
                                <?= nl2br(htmlspecialchars($row['pesan'])) ?>
                            </p>
                            <span class="block text-xs opacity-70 mt-1">
                                <?= date('d M Y H:i', strtotime($row['created_at'])) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
                
            <!-- Form Kirim Tanggapan -->
            <form action="../aksi/kirim_tanggapan_siswa.php" method="post">
                <input type="hidden" name="pengaduan_id" value="<?= $pengaduan_id ?>">

                <textarea
                    name="isi"
                    rows="3"
                    required
                    placeholder="Tulis tanggapan atau pertanyaan..."
                    class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-teal"
                ></textarea>

                <div class="flex justify-end mt-4">
                    <button
                        type="submit"
                        class="bg-teal text-white px-6 py-2 rounded-lg hover:bg-navy transition"
                    >Kirim
                    </button>
                </div>
            </form>
        </div>

    </div>
</main>

    
    
</body>
</html>