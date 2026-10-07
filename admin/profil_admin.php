<?php
session_start();
include '../config.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['login']) || $_SESSION['level'] !== 'admin') {
    header("Location: ../auth/login.php");
}

//Ambil data admin dari session
$adminUsername = $_SESSION['admin_username'];

// Ambil seluruh data admin dan sekolah dari database
$admin_id = $_SESSION['admin_id'];
$q_admin = mysqli_query(
    $config,
    "SELECT a.*, s.nama_sekolah, s.alamat
     FROM users a
     LEFT JOIN sekolah s ON a.sekolah_id = s.id
     WHERE a.id = '$admin_id'
       AND a.level = 'admin'"
);


$admin = mysqli_fetch_assoc($q_admin);
if (!$admin) {
    // Jika admin tidak ditemukan, redirect ke login
    header("Location: ../auth/login.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin</title>
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

        <div class="bg-white p-6 rounded-b-3xl shadow max-w-2xl mx-auto">
            <div class="flex flex-col items-center">
                <!-- Foto Profil Admin -->
                  <img src="../assets/img/<?= $admin['foto'] ? $admin['foto'] : 'default.png'; ?>" 
class="w-32 h-32 rounded-full object-cover mb-4">

<form action="upload_foto.php" method="POST" enctype="multipart/form-data" class="mt-2">
    <input type="file" name="foto" class="text-sm mb-2" required>

    <button type="submit" 
    class="bg-teal text-white px-4 py-1 rounded-lg hover:bg-navy">
    Upload Foto
    </button>
    </form>
    <br>
                <!-- Informasi Admin -->
                <h2 class="text-xl font-semibold text-navy mb-2"><?= htmlspecialchars($admin['nama_sekolah']); ?></h2>
                <p class="text-center"><?= htmlspecialchars($admin['alamat']); ?></p>
            </div>  
        </div>
    </section>
</main>
</body>
</html>