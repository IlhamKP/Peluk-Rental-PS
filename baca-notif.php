<?php
session_start();
require_once "koneksi.php";

$id_user = $_SESSION['id_user'];

mysqli_query($conn,"
UPDATE notifikasi
SET status='dibaca'
WHERE id_user='$id_user'
AND status='belum_dibaca'
");

header("Location: dashboard-user.php?notif=1");
exit;