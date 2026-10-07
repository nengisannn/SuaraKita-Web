<?php
session_start();
include '../config.php';

//Ambil data siswa dari session
$siswaUsername = $_SESSION['siswa_username'];

// Ambil seluruh data siswa beserta nama sekolahnya
$siswa_id = $_SESSION['siswa_id'];
$q_siswa = mysqli_query(
    $config,
    "SELECT s.*, se.nama_sekolah 
     FROM users s
     LEFT JOIN sekolah se ON s.sekolah_id = se.id
     WHERE s.id = '$siswa_id'
       AND s.level = 'siswa'"
);

$siswa = mysqli_fetch_assoc($q_siswa);
if (!$siswa) {
    // Jika siswa tidak ditemukan, redirect ke login
    header("Location: ../auth/login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Siswa</title>
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

<!-- Main Content -->
<main class="ml-64 min-h-screen">

    <!-- Header -->
    <?php include 'partials/header.php'; ?>

    <!-- Content -->
    <section class="p-6 mt-20">

        <!-- Judul -->
        <div class="bg-mint rounded-t-3xl shadow justify-center flex max-w-2xl mx-auto p-4">
            <h1 class="text-2xl font-bold text-navy">Profil</h1>
            
            
        </div>

        <!-- Profil siswa -->
        <div class="bg-white p-6 rounded-b-3xl shadow max-w-2xl mx-auto">
            <div class="flex flex-col items-center">
                <!-- Foto Profil siswa -->
                <img src="../assets/img/<?= $siswa['foto'] ? $siswa['foto'] : 'default.png'; ?>" 
class="w-32 h-32 rounded-full object-cover mb-4">

<form action="upload_foto.php" method="POST" enctype="multipart/form-data" class="mt-2">
    <input type="file" name="foto" class="text-sm mb-2" required>

    <button type="submit" 
    class="bg-teal text-white px-4 py-1 rounded-lg hover:bg-navy">
    Upload Foto
    </button>
</form>
<br>
                <!-- Informasi siswa -->
                <h2 class="text-2xl font-semibold text-navy mb-2"><?= htmlspecialchars($siswa['nama']); ?></h2>
                <!-- Table elegan untuk menampilkan informasi siswa -->
                <div class="w-full mt-4 max-w-xs">
                    <table class="table-auto w-full border-collapse text-left">
                        <tbody>
                            <tr class="border-b">
                                <th class="py-2 px-4 text-gray-600">NIM</th>
                                <td class="py-2 px-4 text-gray-800"><?= htmlspecialchars($siswa['nis']); ?></td>
                            </tr>
                            <tr class="border-b">
                                <th class="py-2 px-4 text-gray-600">Username</th>
                                <td class="py-2 px-4 text-gray-800"><?= htmlspecialchars($siswa['username']); ?></td>
                            </tr>
                            <tr class="border-b">
                                <th class="py-2 px-4 text-gray-600">Password</th>
                                <td class="py-2 px-4 text-gray-800">********</td>
                            </tr>
                            <tr class="border-b">
                                <th class="py-2 px-4 text-gray-600">Kelas</th>
                                <td class="py-2 px-4 text-gray-800"><?= htmlspecialchars($siswa['kelas']); ?></td>
                            </tr>
                            <tr class="border-b">
                                <th class="py-2 px-4 text-gray-600">Institusi</th>
                                <td class="py-2 px-4 text-gray-800"><?= htmlspecialchars($siswa['nama_sekolah']); ?></td>
                            </tr>
                           
                        </tbody>
                    </table>
                    <div class="mt-6">
    <a href="edit_profile.php"
    class="bg-navy text-white px-5 py-2 rounded-lg hover:bg-teal">
    Edit Profil
    </a>
</div>
                </div>  
            </div>  
        </div>
        
    </section>
</main>
</body>
</html>