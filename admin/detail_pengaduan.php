<?php
session_start();
include '../config.php';

//Cek apakah admin sudah login
if (!isset($_SESSION['login']) || $_SESSION['level'] !== 'admin') {
    header("Location: ../auth/login.php");
}

// Ambil data admin dari session
$adminUsername = $_SESSION['admin_username'];

$sekolah_id = $_SESSION['sekolah_id'];
$id = $_GET['id'];

// Ambil detail pengaduan
$pengaduan = mysqli_query($config, "
    SELECT p.*, u.nama, u.kelas
    FROM pengaduan p
    JOIN users u ON p.siswa_id = u.id
    WHERE p.id = '$id'
    AND p.sekolah_id = '$sekolah_id'
");
$data = mysqli_fetch_assoc($pengaduan);

//Ambil Riwayat Tanggapan
$tanggapan = mysqli_query($config, "
    SELECT * FROM tanggapan_pengaduan
    WHERE pengaduan_id = '$id'
    ORDER BY created_at ASC
");

// Kalau data tidak ditemukan
if (!$data) {
    header("Location: pengaduan.php");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
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

            <!-- Kategori, Urgensi & SLA Deadline Badges -->
            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold mb-2">
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
                    <?php
                    $is_overdue = strtotime($data['deadline_sla']) < time() && !in_array($data['status'], ['selesai', 'ditolak']);
                    $sla_bg = $is_overdue ? 'bg-rose-100 text-rose-800 border-rose-300' : 'bg-emerald-50 text-emerald-800 border-emerald-200';
                    ?>
                    <span class="px-3 py-1 rounded-full border text-xs <?= $sla_bg ?>">
                        ⏱️ SLA Target: <?= date('d M Y H:i', strtotime($data['deadline_sla'])) ?> <?= $is_overdue ? '(Overdue/Telat)' : '(On-Track)' ?>
                    </span>
                <?php endif; ?>
            </div>

            <!-- Judul Pengaduan -->
            <h3 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-3">
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

            <!-- Rating CSAT Pelapor (Jika Ada) -->
            <?php if (!empty($data['rating'])): ?>
                <div class="border-t border-slate-100 pt-4 mt-4 bg-amber-50/60 border border-amber-200 rounded-xl p-4">
                    <p class="text-xs font-bold text-amber-800 uppercase tracking-wider mb-1">⭐ Penilaian Kepuasan Pelapor (CSAT)</p>
                    <div class="flex items-center gap-2 mb-1">
                        <div class="text-amber-400 text-lg">
                            <?= str_repeat('★', (int)$data['rating']) ?><?= str_repeat('☆', 5 - (int)$data['rating']) ?>
                        </div>
                        <span class="font-bold text-sm text-slate-800"><?= (int)$data['rating'] ?> / 5.0</span>
                    </div>
                    <?php if (!empty($data['ulasan_rating'])): ?>
                        <p class="text-xs italic text-slate-600">"<?= htmlspecialchars($data['ulasan_rating']) ?>"</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Meta -->
            <div class="text-sm text-gray-500 gap-3 border-t border-mint pt-4 space-y-2">
                <p class="flex items-center gap-2">Nama Pelapor :
                    <?php if (!empty($data['is_anonim'])): ?>
                        <span class="font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">🔒 Anonim (Identitas Dirahasiakan oleh Pelapor)</span>
                    <?php else: ?>
                        <span class="font-semibold text-slate-800"><?= htmlspecialchars($data['nama']) ?></span>
                    <?php endif; ?>
                </p>
                <?php if (empty($data['is_anonim'])): ?>
                    <p class="flex items-center gap-2">Kelas / Prodi : 
                        <span><?= htmlspecialchars($data['kelas']) ?></span>
                    </p>
                <?php endif; ?>
                <p class="flex items-center gap-2">Tanggal Pengaduan :
                    <span><?= date('d M Y H:i', strtotime($data['created_at'])) ?></span>
                </p>
                <p class="flex items-center gap-2">Status Pengaduan :
                <span class="px-3 py-1 rounded-full text-sm font-semibold
                    <?php
                        switch ($data['status']) {
                            case 'terkirim':
                            case 'dikirim':
                                echo 'bg-amber-100 text-amber-800';
                                break;
                            case 'ditinjau':
                                echo 'bg-purple-100 text-purple-800';
                                break;
                            case 'diproses':
                                echo 'bg-blue-100 text-blue-800';
                                break;
                            case 'selesai':
                                echo 'bg-emerald-100 text-emerald-800';
                                break;
                            case 'ditolak':
                                echo 'bg-rose-100 text-rose-800';
                                break;
                            default:
                                echo 'bg-slate-100 text-slate-700';
                        }
                    ?>">
                    <?= ucfirst($data['status']); ?>
                </span>
                </p>
            </div>

            <!-- Jika status ditolak, tampilkan alasan penolakan -->
            <?php if ($data['status'] === 'ditolak' && !empty($data['alasan_penolakan'])): ?>
                <div class="bg-rose-50 border border-rose-200 rounded-lg p-4 text-sm text-rose-800">
                    <p class="font-bold mb-1">❌ Alasan Penolakan Pengaduan:</p>
                    <p><?= nl2br(htmlspecialchars($data['alasan_penolakan'])) ?></p>
                </div>
            <?php endif; ?>

            <!-- Form Update Status -->
            <form action="../aksi/aksi_update_status.php" method="POST" class="mt-6 space-y-4 border-t border-slate-100 pt-4">
                <input type="hidden" name="id" value="<?= $data['id']; ?>">

                <div class="flex items-center gap-4">
                    <label class="text-sm font-medium text-slate-700">Ubah Status Pengaduan:</label>
                    <select name="status" id="statusSelect" onchange="toggleAlasanField()" class="border border-slate-300 px-4 py-2 rounded-lg text-sm bg-white focus:ring-2 focus:ring-teal">
                        <option value="terkirim" <?= ($data['status']=='terkirim'||$data['status']=='dikirim')?'selected':''; ?>>📩 Terkirim</option>
                        <option value="ditinjau" <?= $data['status']=='ditinjau'?'selected':''; ?>>👁️ Ditinjau</option>
                        <option value="diproses" <?= $data['status']=='diproses'?'selected':''; ?>>⚙️ Diproses</option>
                        <option value="selesai" <?= $data['status']=='selesai'?'selected':''; ?>>✅ Selesai</option>
                        <option value="ditolak" <?= $data['status']=='ditolak'?'selected':''; ?>>❌ Ditolak</option>
                    </select>

                    <button type="submit" class="bg-navy text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-teal transition">
                        Perbarui Status
                    </button>
                </div>

                <!-- Input Alasan Penolakan (Dynamic) -->
                <div id="alasanContainer" class="<?= $data['status'] === 'ditolak' ? '' : 'hidden' ?>">
                    <label class="block text-sm font-medium text-rose-700 mb-1">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea name="alasan_penolakan" rows="2" placeholder="Tuliskan alasan mengapa pengaduan ini ditolak..." class="w-full border border-rose-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-rose-500"><?= htmlspecialchars($data['alasan_penolakan'] ?? '') ?></textarea>
                </div>
            </form>

            <script>
                function toggleAlasanField() {
                    const select = document.getElementById('statusSelect');
                    const container = document.getElementById('alasanContainer');
                    if (select.value === 'ditolak') {
                        container.classList.remove('hidden');
                    } else {
                        container.classList.add('hidden');
                    }
                }
            </script>
        </div>

        <!-- Tanggapan Pengaduan & Form Balasan -->
        <div class="bg-white rounded-lg shadow p-6 space-y-6">
            <!-- Tanggapan Pengaduan -->
            <div>
                <h2 class="text-xl font-semibold text-navy mb-4">Tanggapan</h2>
                <?php while ($row = mysqli_fetch_assoc($tanggapan)): ?>
                    <div class="<?= $row['pengirim'] == 'admin' ? 'text-right' : 'text-left' ?> mb-3">
                        <div class="inline-block max-w-[75%] px-4 py-2 rounded-lg
                            <?= $row['pengirim'] == 'admin'? 'bg-navy text-white': 'bg-mint text-navy' ?>">
                            <p class="text-sm font-semibold">
                                <?= ucfirst($row['pengirim']) ?>
                            </p>
                            <p class="text-sm">
                                <?= nl2br(htmlspecialchars($row['pesan'])) ?>
                            </p>
                            <span class="text-xs opacity-70">
                                <?= date('d M Y H:i', strtotime($row['created_at'])) ?>
                            </span>
                        </div>
                    </div>
                <?php endwhile; ?>
                
                <!-- Jika tidak ada tanggapan -->
                <?php if (mysqli_num_rows($tanggapan) == 0): ?>
                    <p class="text-gray-500 italic text-center">
                        Belum ada tanggapan.
                    </p>
                <?php endif; ?>
            </div>

            <!-- Form Kirim Tanggapan -->
            <form action="../aksi/kirim_tanggapan_admin.php" method="post" class="space-y-4">
                <input type="hidden" name="pengaduan_id" value="<?= $data['id'] ?>">
                <textarea
                    name="isi"
                    rows="3"
                    required
                    placeholder="Tulis balasan untuk siswa..."
                    class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-navy"
                ></textarea>
                <div class="flex justify-end items-center">
                    <button class="bg-navy text-white px-6 py-2 rounded-lg">
                        Kirim Balasan
                    </button>
                </div>
            </form>
        </div>

    </div>
    </main>
</body>
</html>
