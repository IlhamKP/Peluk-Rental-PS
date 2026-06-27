<?php
require 'koneksi.php';

date_default_timezone_set('Asia/Makassar');

$id_unit = $_POST['id_unit'];
$sekarang = date("Y-m-d H:i:s");

$query = mysqli_query($conn,"
SELECT
    sesi.id_user,
    unit_ps.nama_unit
FROM sesi
JOIN unit_ps
ON sesi.id_unit = unit_ps.id_unit
WHERE sesi.id_unit='$id_unit'
AND sesi.status='aktif'
LIMIT 1
");

$data = mysqli_fetch_assoc($query);
$id_user = $data['id_user'];
$nama_unit = $data['nama_unit'];

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

// Tambah notifikasi
mysqli_query($conn,"
INSERT INTO notifikasi(id_user,pesan)
VALUES('$id_user','Sesi kamu di $nama_unit telah selesai.')
");

header("Location: dashboard-admin.php");
exit;