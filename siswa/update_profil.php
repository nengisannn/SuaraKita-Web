<?php
session_start();
include '../config.php';

$siswa_id = $_SESSION['siswa_id'];

$nama = $_POST['nama'];
$username = $_POST['username'];
$kelas = $_POST['kelas'];
$password = $_POST['password'];

mysqli_query($config,"UPDATE users SET
nama='$nama',
username='$username',
kelas='$kelas',
password='$password'
WHERE id='$siswa_id'");

header("Location: profil_siswa.php");