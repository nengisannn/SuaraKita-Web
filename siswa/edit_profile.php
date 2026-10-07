<?php
session_start();
include '../config.php';

$siswa_id = $_SESSION['siswa_id'];

$q = mysqli_query($config,"SELECT * FROM users WHERE id='$siswa_id'");
$data = mysqli_fetch_assoc($q);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Siswa</title>
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

<body class="bg-violet-50 flex justify-center items-center min-h-screen">

<div class="bg-white p-6 rounded-xl shadow w-96">

<h2 class="text-xl font-bold mb-4 text-center">Edit Profil</h2>

<form action="update_profil.php" method="POST">

<label class="text-sm">Nama</label>
<input type="text" name="nama" value="<?= $data['nama'] ?>" 
class="w-full border p-2 rounded mb-3">

<label class="text-sm">Username</label>
<input type="text" name="username" value="<?= $data['username'] ?>" 
class="w-full border p-2 rounded mb-3">

<label class="text-sm">Password</label>
<input type="text" name="password" value="<?= $data['password'] ?>" 
class="w-full border p-2 rounded mb-3">

<label class="text-sm">Kelas</label>
<input type="text" name="kelas" value="<?= $data['kelas'] ?>" 
class="w-full border p-2 rounded mb-3">

<button type="submit" 
class="bg-teal text-white w-full py-2 rounded hover:bg-navy">
Simpan Perubahan
</button>

</form>

</div>

</body>
</html>
