<?php
require 'koneksi.php';

$id_unit = $_POST['id_unit'];

$cek = mysqli_query($conn,"
SELECT status
FROM unit_ps
WHERE id_unit='$id_unit'
");

mysqli_query($conn,"
DELETE FROM sesi
WHERE id_unit='$id_unit'
");

mysqli_query($conn,"
DELETE FROM unit_ps
WHERE id_unit='$id_unit'
");

$data = mysqli_fetch_assoc($cek);

if($data['status'] == 'dipakai'){
    die("Unit sedang dipakai, tidak bisa dihapus.");
}

mysqli_query($conn,"
DELETE FROM unit_ps
WHERE id_unit='$id_unit'
");

header("Location: dashboard-admin.php");
exit;