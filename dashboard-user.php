<?php
require_once 'koneksi.php';
session_start();

if(!isset($_SESSION['id_user'])){
    header("location: index.php");
    exit;
}


$query = mysqli_query($conn, "SELECT * FROM unit_ps");

$id_user = $_SESSION['id_user'];

$querySesi = mysqli_query($conn,"
SELECT
    sesi.*,
    unit_ps.nama_unit,
    unit_ps.tipe_ps
FROM sesi
JOIN unit_ps
ON sesi.id_unit = unit_ps.id_unit
WHERE sesi.id_user = '$id_user'
AND sesi.status = 'aktif'
LIMIT 1;
");

$sesi = mysqli_fetch_assoc($querySesi);
$queryRiwayat = false;

if($sesi){
    $id_sesi = $sesi['id_sesi'];

    $queryRiwayat = mysqli_query($conn,"
    SELECT *
    FROM riwayat_tambah_waktu
    WHERE id_sesi='$id_sesi'
    ORDER BY waktu_tambah DESC
    ");
}


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User</title>
    <link rel="stylesheet" href="css/dashboard-user.css">
</head>
<body>
    <div class="header">
        <h3>PELUK Coffee Shop & Rental PS</h3>
        <div class="kanan">
            <label><span class="header-teks">Hai, </span><?= ($_SESSION['username']);?></label>
            <button><img src="./icon/bell.png" ></button>
            <button onclick="window.location.href='logout.php'">Keluar</button>
        </div>
    </div>
    <div class="isi">
        <?php if($sesi): ?>
        <div class="ada">
            <div class="atas">
                <label><?=$sesi['tipe_ps'];?> - <?=$sesi['nama_unit'];?></label>
            </div>
            <div class="tengah">
                <div id="timer" class="waktu"></div>
                <label>Sisa Waktu</label>
            </div>
            <div class="bawah">
                <div class="riwayat">
                    <div class="card">
                        <label>Mulai Main</label><br>
                        <span><?= date('d M, H.i', strtotime($sesi['waktu_mulai'])); ?></span>
                    </div>
                    <div class="card">
                        <label>Tarif</label><br>
                        <span>Rp<?= number_format($sesi['harga_per_jam'],0,',','.'); ?>/Jam</span>
                    </div>
                    <div class="card">
                        <label>Total Bayar</label><br>
                        <span>Rp<?= number_format($sesi['total_bayar'],0,',','.'); ?></span>
                    </div>
                    <div class="card">
                        <label>Durasi awal</label><br>
                        <span><?= $sesi['durasi_jam']; ?> menit</span>
                    </div>
                    <div class="card">
                        <label>Total Tambahan</label><br>
                        <span>+<?= $sesi['total_tambahan_menit']; ?> menit</span>
                    </div>
                </div>
                <?php if($queryRiwayat && mysqli_num_rows($queryRiwayat) > 0): ?>
                <div class="riwayat-tambah">
                    <label>Riwayat Tambahan Waktu</label>
                    <?php while($row = mysqli_fetch_assoc($queryRiwayat)): ?>
                    <div class="tambah-waktu">
                        <span>+<?= $row['tambahan_menit'];?> menit</span>
                        <span><?= date('d M, H.i', strtotime($row['waktu_tambah'])); ?></span>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="belum-ada">
            <div class="kotak">
                <img src="./icon/controller.png">
            </div>
            <div class="teks">
                <label>Belum ada sesi aktif</label>
                <span>Datang ke kasir untuk mulai main. Begitu admin memulai sesi atas namamu, waktu mainmu akan muncul otomatis di sini.</span>
            </div>
            <div class="unit">
                <?php while($row = mysqli_fetch_assoc($query)): ?>
                <div class="card">
                    <div class="atas">
                        <label><?= ($row['tipe_ps']);?></label>
                        <span class="<?= ($row['status']);?>"></span>
                    </div>
                    <div class="tengah">
                        <label><?= ($row['nama_unit']);?></label>
                    </div>
                    <div class="bawah">
                        <label class="<?= $row['status']; ?>">
                            <?= ($row['status']);?>
                        </label>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
<script>

const waktuSelesai = new Date("<?= $sesi['waktu_selesai']; ?>").getTime();

function updateTimer(){

    const sekarang = new Date().getTime();

    let selisih = Math.floor((waktuSelesai - sekarang)/1000);

    if(selisih <= 0){

        document.getElementById("timer").innerHTML = "00:00:00";
        clearInterval(timer);
        return;
    }

    let jam = Math.floor(selisih / 3600);
    let menit = Math.floor((selisih % 3600)/60);
    let detik = selisih % 60;

    document.getElementById("timer").innerHTML =
        String(jam).padStart(2,'0') + ":" +
        String(menit).padStart(2,'0') + ":" +
        String(detik).padStart(2,'0');

}

updateTimer();

const timer = setInterval(updateTimer,1000);

</script>
</html>