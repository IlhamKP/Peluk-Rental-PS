<?php
require 'koneksi.php';

$id_unit = $_POST['id_unit'];
$tambah  = $_POST['tbm'];

// Ambil harga per jam sesi yang sedang aktif
$q = mysqli_query($conn,"
SELECT
    sesi.id_sesi,
    sesi.id_user,
    sesi.harga_per_jam,
    unit_ps.nama_unit
FROM sesi
JOIN unit_ps
ON sesi.id_unit = unit_ps.id_unit
WHERE sesi.id_unit='$id_unit'
AND sesi.status='aktif'
LIMIT 1
");

$data = mysqli_fetch_assoc($q);



$id_sesi = $data['id_sesi'];
$harga_perjam = $data['harga_per_jam'];
$id_user = $data['id_user'];
$nama_unit = $data['nama_unit'];
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

mysqli_query($conn,"
INSERT INTO notifikasi(id_user, pesan)
VALUES(
    '$id_user',
    'Waktu bermain di $nama_unit telah ditambah $tambah menit.'
)
");


header("Location: dashboard-admin.php");
exit;