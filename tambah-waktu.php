<?php
require 'koneksi.php';

$id_unit = $_POST['id_unit'];
$tambah  = $_POST['tbm'];

// Ambil harga per jam sesi yang sedang aktif
$q = mysqli_query($conn,"
SELECT harga_per_jam
FROM sesi
WHERE id_unit='$id_unit'
AND status='aktif'
LIMIT 1
");

$data = mysqli_fetch_assoc($q);

$harga_perjam = $data['harga_per_jam'];

// Hitung biaya tambahan
$tambahanBayar = ($harga_perjam / 60) * $tambah;

// Update sesi
mysqli_query($conn,"
UPDATE sesi
SET
    waktu_selesai = DATE_ADD(waktu_selesai, INTERVAL $tambah MINUTE),
    tambahan_menit = tambahan_menit + $tambah,
    tambahan_bayar = tambahan_bayar + $tambahanBayar,
    total_bayar = total_bayar + $tambahanBayar
WHERE id_unit='$id_unit'
AND status='aktif'
");

header("Location: dashboard-admin.php");
exit;