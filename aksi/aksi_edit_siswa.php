<?php
// aksi/aksi_edit_siswa.php - Edit Siswa Secure & Modular

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin/kelola_siswa.php");
    exit;
}

$id         = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$nama       = sanitize_input($_POST['nama'] ?? '');
$nis        = sanitize_input($_POST['nis'] ?? '');
$kelas      = sanitize_input($_POST['kelas'] ?? '');
$sekolah_id = $_SESSION['sekolah_id'];

if ($id && !empty($nama)) {
    db_execute(
        "UPDATE users SET nama = ?, nis = ?, kelas = ? WHERE id = ? AND level = 'siswa' AND sekolah_id = ?",
        [$nama, $nis, $kelas, $id, $sekolah_id],
        "sssii"
    );
}

header("Location: ../admin/kelola_siswa.php");
exit;
