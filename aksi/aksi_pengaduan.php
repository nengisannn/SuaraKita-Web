<?php
// aksi/aksi_pengaduan.php - Submit Pengaduan Secure & Modular dengan Kategori, Urgensi, Lampiran & Anonim

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/upload.php';

require_siswa();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../siswa/buat_pengaduan.php");
    exit;
}

$siswa_id   = $_SESSION['siswa_id'];
$sekolah_id = $_SESSION['sekolah_id'];

$judul     = sanitize_input($_POST['judul'] ?? '');
$kategori  = sanitize_input($_POST['kategori'] ?? 'Fasilitas');
$urgensi   = sanitize_input($_POST['urgensi'] ?? 'sedang');
$isi       = sanitize_input($_POST['isi'] ?? '');
$is_anonim = isset($_POST['is_anonim']) && $_POST['is_anonim'] == '1' ? 1 : 0;

$allowed_categories = ['Fasilitas', 'Akademik', 'Keuangan', 'Kebersihan', 'Keamanan', 'Lainnya'];
if (!in_array($kategori, $allowed_categories, true)) {
    $kategori = 'Fasilitas';
}

$allowed_urgencies = ['rendah', 'sedang', 'tinggi', 'darurat'];
if (!in_array($urgensi, $allowed_urgencies, true)) {
    $urgensi = 'sedang';
}

if (empty($judul) || empty($isi)) {
    header("Location: ../siswa/buat_pengaduan.php?pesan=gagal");
    exit;
}

// Handling File Upload Lampiran
$lampiran_filename = null;
if (isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] === UPLOAD_ERR_OK) {
    $upload = upload_secure_file(
        $_FILES['lampiran'],
        __DIR__ . '/../assets/uploads',
        'bukti_' . $siswa_id,
        ['jpg', 'jpeg', 'png', 'webp', 'pdf'],
        ['image/jpeg', 'image/png', 'image/webp', 'application/pdf']
    );

    if ($upload['success']) {
        $lampiran_filename = $upload['filename'];
    }
}

// Hitung deadline SLA penanganan berdasarkan tingkat urgensi
$days_to_add = match($urgensi) {
    'darurat' => 1,
    'tinggi'  => 2,
    'sedang'  => 5,
    'rendah'  => 7,
    default   => 5
};
$deadline_sla = date('Y-m-d H:i:s', strtotime("+$days_to_add days"));

$success = db_execute(
    "INSERT INTO pengaduan (siswa_id, sekolah_id, kategori, urgensi, deadline_sla, judul, isi, lampiran, is_anonim, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'terkirim')",
    [$siswa_id, $sekolah_id, $kategori, $urgensi, $deadline_sla, $judul, $isi, $lampiran_filename, $is_anonim],
    "iissssssi"
);

if ($success) {
    $inserted_id = db_fetch_one("SELECT LAST_INSERT_ID() AS id")['id'] ?? 0;
    log_activity($sekolah_id, $siswa_id, 'siswa', 'Buat Pengaduan', "Membuat pengaduan #$inserted_id ($judul) dengan urgensi $urgensi");
    header("Location: ../siswa/dashboard_siswa.php?pesan=berhasil");
    exit;
} else {
    header("Location: ../siswa/buat_pengaduan.php?pesan=gagal");
    exit;
}
