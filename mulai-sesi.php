<?php

require 'koneksi.php';

$id_user = $_POST['id_user'];
$id_unit = $_POST['id_unit'];
$durasi  = $_POST['durasi']; // menit
date_default_timezone_set('Asia/Makassar');
$qUser = mysqli_query($conn,
    "SELECT username FROM users WHERE id_user='$id_user'");

$user = mysqli_fetch_assoc($qUser);

$username = $user['username'];

// ambil harga unit
$q = mysqli_query($conn,"
SELECT harga_perjam, nama_unit
FROM unit_ps
WHERE id_unit='$id_unit'
");

$unit = mysqli_fetch_assoc($q);

$harga = $unit['harga_perjam'];
$nama_unit = $unit['nama_unit'];
$mulai = date("Y-m-d H:i:s");

$selesai = date(
"Y-m-d H:i:s",
strtotime("+$durasi minutes")
);

$total = ($durasi/60) * $harga;

// simpan sesi
mysqli_query($conn,"
INSERT INTO sesi
(
id_user,
id_unit,
waktu_mulai,
waktu_selesai,
durasi_jam,
harga_per_jam,
total_bayar,
status
)
VALUES
(
'$id_user',
'$id_unit',
'$mulai',
'$selesai',
'$durasi',
'$harga',
'$total',
'aktif'
)
");

// ubah status unit
mysqli_query($conn,"
UPDATE unit_ps
SET status='dipakai'
WHERE id_unit='$id_unit'
");

mysqli_query($conn,"
INSERT INTO notifikasi(id_user,pesan)
VALUES('$id_user','Sesi kamu di $nama_unit telah dimulai.')
");

header("Location: dashboard-admin.php");
exit;