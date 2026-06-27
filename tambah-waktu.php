<?php
require 'koneksi.php';

$id_unit = $_POST['id_unit'];
$tambah  = $_POST['tbm'];

// Ambil harga per jam sesi yang sedang aktif
$q = mysqli_query($conn,"
SELECT id_sesi, harga_per_jam
FROM sesi
WHERE id_unit='$id_unit'
AND status='aktif'
LIMIT 1
");

$data = mysqli_fetch_assoc($q);

$id_sesi = $data['id_sesi'];
$harga_perjam = $data['harga_per_jam'];

// Hitung biaya tambahan
$tambahanBayar = ($harga_perjam / 60) * $tambah;

// Update sesi
mysqli_query($conn,"
UPDATE sesi
SET
    waktu_selesai = DATE_ADD(waktu_selesai, INTERVAL $tambah MINUTE),
    total_tambahan_menit = total_tambahan_menit + $tambah,
    tambahan_bayar = tambahan_bayar + $tambahanBayar,
    total_bayar = total_bayar + $tambahanBayar
WHERE id_unit='$id_unit'
AND status='aktif'
");

mysqli_query($conn,"
INSERT INTO riwayat_tambah_waktu
(id_sesi, tambahan_menit)
VALUES
('$id_sesi', '$tambah')
");

header("Location: dashboard-admin.php");
exit;