<?php
require 'koneksi.php';

date_default_timezone_set('Asia/Makassar');

$id_unit = $_POST['id_unit'];
$sekarang = date("Y-m-d H:i:s");

// Selesaikan sesi
mysqli_query($conn,"
UPDATE sesi
SET
    status='habis',
    waktu_selesai='$sekarang'
WHERE id_unit='$id_unit'
AND status='aktif'
");

// Kembalikan unit menjadi tersedia
mysqli_query($conn,"
UPDATE unit_ps
SET status='tersedia'
WHERE id_unit='$id_unit'
");

header("Location: dashboard-admin.php");
exit;