<?php
require 'koneksi.php';

$nama  = $_POST['nama_unit'];
$tipe  = $_POST['tipe_ps'];
$harga = $_POST['harga_perjam'];

mysqli_query($conn,"
INSERT INTO unit_ps
(
    nama_unit,
    tipe_ps,
    harga_perjam,
    status
)
VALUES
(
    '$nama',
    '$tipe',
    '$harga',
    'tersedia'
)
");

header("Location: dashboard-admin.php");
exit;