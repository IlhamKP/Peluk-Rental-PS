<?php
session_start();
require_once 'koneksi.php';

if(!isset($_SESSION['id_user'])){
    header("Location: index.php");
    exit;
}
date_default_timezone_set('Asia/Makassar');
/* UNIT AKTIF */
$q_aktif = mysqli_query($conn,
    "SELECT COUNT(*) AS total
     FROM sesi
     WHERE status='aktif'"
);
$aktif = mysqli_fetch_assoc($q_aktif);

/* WAKTU HABIS */
$q_habis = mysqli_query($conn,
    "SELECT COUNT(*) AS total
     FROM sesi
     WHERE status='habis'"
);
$habis = mysqli_fetch_assoc($q_habis);

/* TOTAL USER */
$q_user = mysqli_query($conn,
    "SELECT COUNT(*) AS total
     FROM users
     WHERE role='user'"
);
$user = mysqli_fetch_assoc($q_user);

/* OMZET HARI INI */
$q_omzet = mysqli_query($conn,
    "SELECT SUM(total_bayar) AS total
     FROM sesi
     WHERE DATE(waktu_mulai)=CURDATE()"
);
$omzet = mysqli_fetch_assoc($q_omzet);

/* TOTAL UNIT */
$q_unit = mysqli_query($conn,
    "SELECT COUNT(*) AS total
     FROM unit_ps"
);
$total_unit = mysqli_fetch_assoc($q_unit);

/* DATA UNIT */
$data_unit = mysqli_query($conn,"
SELECT
    unit_ps.*,
    users.username,
    sesi.waktu_selesai
FROM unit_ps
LEFT JOIN sesi
ON unit_ps.id_unit = sesi.id_unit
AND sesi.status='aktif'
LEFT JOIN users
ON sesi.id_user = users.id_user
ORDER BY unit_ps.id_unit
");

$qRiwayat = mysqli_query($conn,"
SELECT
    sesi.*,
    users.username,
    unit_ps.nama_unit
FROM sesi
JOIN users
ON sesi.id_user = users.id_user
JOIN unit_ps
ON sesi.id_unit = unit_ps.id_unit
WHERE sesi.status='habis'
ORDER BY sesi.id_sesi DESC
");

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="css/dashboard-admin.css">
</head>
<body>
    <div class="header">
        <h3>PELUK Coffee Shop & Rental PS</h3>
        <label><span class="header-teks">Hai,</span> Admin Cafe <span class="role">ADMIN</span>
        <a href="index.php">Keluar</a></label>
    </div>
    <div class="isi">
    <div class="statistik">
        <div class="status">
            <span>UNIT AKTIF</span><br>
            <div class="total1">
                <label>
                    <?= $aktif['total']; ?>/<?= $total_unit['total']; ?>
                </label>
            </div>
        </div>
        <div class="status">
            <span>WAKTU HABIS</span><br>
            <div class="total2">
                <label>
                    <?= $habis['total']; ?>
                </label>
            </div>
        </div>
        <div class="status">
            <span>TOTAL USER</span><br>
            <div class="total3">
                <label>
                    <?= $user['total']; ?>
                </label>
            </div>
        </div>
        <div class="status">
            <span>OMZET HARI INI</span><br>
            <div class="total4">
                <label>
                    Rp<?= number_format($omzet['total'] ?? 0,0,',','.'); ?>
                </label>
            </div>
        </div>
    </div>

    <div class="cek">
        
            <button onclick="showTab('sesi')" class="tab-btn active">Unit & Sesi</button>
            <button onclick="showTab('riwayat')" class="tab-btn">Riwayat</button>
            <button onclick="showTab('kelola')" class="tab-btn">Kelola Unit</button>
    
    </div>

    <div id="sesi" class="tab-content active">
        <div class="unit-sesi" >
            
            <?php while($unit = mysqli_fetch_assoc($data_unit)): ?>
                
                <div class="unit" id="unit<?= $unit['id_unit']; ?>">
                    <div class="atas">
                        <div class="kiri">
                    <span>
                        <?= $unit['tipe_ps']; ?>
                    </span>
                    <label>
                        <?= $unit['nama_unit']; ?>
                    </label>
                </div>
                <div class="kanan">
                    <label class="status <?= $unit['status']; ?>">
                        <?= ucfirst($unit['status']); ?>
                    </label>
                </div>
            </div>
            <div class="tengah">
                <label>
                    Rp<?= number_format($unit['harga_perjam'],0,',','.'); ?>/jam
                </label>
                
                <div class="info-sesi">
                <?php if($unit['status']=='dipakai'): ?>
                
                <label><?=($unit['username']) ?></label>
        
                <span class="timer" data-end="<?= $unit['waktu_selesai']; ?>"> </span>
                <?php endif; ?>
                </div>
            </div>
            <div class="bawah">
                <?php if($unit['status'] == 'tersedia'): ?>
                <button class="mulai" type="button" onclick="buka(<?= $unit['id_unit']; ?>, '<?= $unit['nama_unit']; ?>')">Mulai Sesi</button>
                <?php else: ?>  
                    <div class="tombolT">
                        <button class="tambahW" onclick="bukaW(<?= $unit['id_unit']; ?>, '<?= $unit['nama_unit']; ?>','<?= $unit['username'];?>')">+ Waktu</button>
                        <form action="akhiri-sesi.php" method="POST">
                            <input type="hidden" name="id_unit" value="<?= $unit['id_unit']; ?>">
                            <button type="submit" class="berhenti" >Akhiri</button>
                        </form> 
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <div id="popup" class="isi-waktu">
            <div class="isi-waktu-isi">
                <div class="header-isi-waktu">
                    <label>Mulai sesi - <span id="namaUnit"></span></label>
                    <button type="button" onclick="tutup()"><img src="./icon/cross1.png"></button>
                </div>

            <?php $query = mysqli_query($conn, "SELECT id_user, username FROM users WHERE role = 'user' ORDER BY username ASC");?>
            <form id="formSesi" action="mulai-sesi.php" method="POST">
                <input type="hidden" name="id_unit" id="idUnit">
                <div class="pilih">
                    <label>Pilih User</label><br>
                    <select name="id_user" required>
                        <option value="">- pilih user -</option>
                        <?php while($user = mysqli_fetch_assoc($query)): ?>
                            <option value="<?= $user['id_user']; ?>">
                                <?= ($user['username']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
            <div class="durasi">
                <label>Durasi</label><br>
                <div class="Tdurasi">
                    <button type="button" onclick="setDurasi(this,30)">30m</button>
                    <button type="button" onclick="setDurasi(this,60)">60m</button>
                    <button type="button" onclick="setDurasi(this,90)">90m</button>
                    <button type="button" onclick="setDurasi(this,120)">120m</button>
                </div>
                <input type="number" id="durasi" name="durasi" min="30" required>
            </div>
            <div class="masukkan">
                <button type="submit">Mulai sesi</button>
            </div>
            </div>
        </form>
        </div>

        <div id="popupW" class="tambah-waktu">
            <div class="isi-tambah-waktu">
                <form action="tambah-waktu.php" method="POST">
                <div class="header-tambah-waktu">
                    <input type="hidden" id="idUnitW" name="id_unit">
                    <label>Tambah Waktu - <span id="namaUnitW"></span></label>
                    <button type="button" onclick="tutupW()"><img src="./icon/cross1.png"></button>
                </div>
                <div class="teks">
                    <label>Sedang dipakai oleh <span id="username"></span></label>
                </div>

                    <div class="tbm">
                        <label>Tambah berapa menit?</label>
                        <div class="tbmT">
                            <button type="button" onclick="TambahW(this,30)" active>+30m</button>
                            <button type="button" onclick="TambahW(this,60)">+60m</button>
                            <button type="button" onclick="TambahW(this,90)">+90m</button>
                            <button type="button" onclick="TambahW(this,120)">+120m</button>
                        </div>
                        <input type="number" id="tbm" name="tbm" min="30" required>
                    </div>
                    <div class="bawah">
                        <button>Tambahkan Waktu</button>
                    </div>
                </form>
                    
                
            </div>
        </div>
    </div>
    <div id="riwayat" class="tab-content">
        <div class="tabel-riwayat">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Unit</th>
                        <th>Mulai</th>
                        <th>Selesai</th>
                        <th>Durasi</th>
                        <th>Tambahan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($qRiwayat)): ?>
                        <tr>
                            <td class="user">
                                <?= $row['username']; ?>
                            </td>
                    
                            <td>
                                <?= $row['nama_unit']; ?>
                            </td>
                            
                            <td>
                                <?= date("d M, H.i", strtotime($row['waktu_mulai'])); ?>
                            </td>
                            
                            <td>
                                <?= date("d M, H.i", strtotime($row['waktu_selesai'])); ?>
                            </td>
                            
                            <td class="riwayat-durasi">
                                <?= $row['durasi_jam']; ?>m
                            </td>
                            
                            <td class="tambahan">
                                <?= $row['tambahan_menit'] > 0 ? '+' . $row['tambahan_menit'] . 'm' : '-'; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
            </table>
        </div>
    </div>

    <?php $unit = mysqli_query
    ($conn,"SELECT * FROM unit_ps ORDER BY id_unit ASC");
    ?>

    <div id="kelola" class="tab-content">
        <div class="isi-kelola">
            <div class="tabel-kelola">
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Tipe</th>
                            <th>Tarif/Jam</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($unit)): ?>
                        <tr>
                            <td class="user"><?= $row['nama_unit']; ?></td>
                            <td><?= $row['tipe_ps']; ?></td>
                            <td><?= $row['harga_perjam']; ?></td>
                            <td>
                                <form action="hapus-unit.php" method="POST">
                                    <input type="hidden" name="id_unit" value="<?= $row['id_unit']; ?>">
                                    <div class="Kbutton"><button>Hapus</button></div>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <div class="tambah-unit">
                <form action="tambah-unit.php" method="POST">
                <div class="header-tambah-unit">
                    <label>Tambah unit baru</label>
                </div>
                <div class="tengah">

                        <label>Nama Unit</label>
                        <input type="text" name="nama_unit" required>
                        <label>Tipe Konsol</label>
                        <select name="tipe_ps">
                            <option>PS5</option>
                            <option>PS4 Pro</option>
                            <option>PS4</option>
                        </select>
                        <label>Tarif Per Jam (Rp)</label>
                        <input type="number" step="1000" min="5000" name="harga_perjam" value="5000">
                    </div>
                    <div class="bawah">
                        <button type="submit">Tambah Unit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
<script>
function showTab(tabId){

    // Sembunyikan semua tab
    document.querySelectorAll('.tab-content').forEach(tab=>{
        tab.classList.remove('active');
    });

    // Hapus active semua tombol
    document.querySelectorAll('.tab-btn').forEach(btn=>{
        btn.classList.remove('active');
    });

    // Tampilkan tab yang dipilih
    document.getElementById(tabId).classList.add('active');

    // Aktifkan tombol yang diklik
    event.target.classList.add('active');
}

function buka(id,namaUnit){
    document.getElementById("popup").classList.add("active");
    document.getElementById("namaUnit").textContent = namaUnit;
    document.getElementById("idUnit").value=id;
}

function tutup(){
    document.getElementById("popup").classList.remove("active");
}

function bukaW(id, namaUnit, username){

    document.getElementById("popupW").classList.add("active");

    document.getElementById("namaUnitW").textContent = namaUnit;

    document.getElementById("username").textContent = username;

    document.getElementById("idUnitW").value = id;

}

function tutupW(){

    document.getElementById("popupW").classList.remove("active");

}

function tutupW(){
    document.getElementById("popupW").classList.remove("active");
}

function setDurasi(btn, menit){
    document.getElementById("durasi").value = menit;

    document.querySelectorAll(".Tdurasi button").forEach(function(b){
        b.classList.remove("active");
    });

    btn.classList.add("active");
}

function TambahW(btn, menit){
    document.getElementById("tbm").value = menit;

    document.querySelectorAll(".tbmT button").forEach(function(b){
        b.classList.remove("active");
    });

    btn.classList.add("active");
}

document.querySelectorAll(".timer").forEach(function(timer){

    mulaiTimer(
        timer,
        timer.dataset.end
    );

});

function mulaiTimer(element, waktuSelesai){

    const selesai = new Date(waktuSelesai.replace(" ","T"));

    let interval;

    function update(){

        const sekarang = new Date();

        let selisih = selesai - sekarang;

        if(selisih <= 0){
            element.innerHTML = "00:00:00";
            clearInterval(interval);
            return;
        }

        const jam = Math.floor(selisih / 1000 / 60 / 60);
        const menit = Math.floor((selisih / 1000 / 60) % 60);
        const detik = Math.floor((selisih / 1000) % 60);

        element.innerHTML =
            `${String(jam).padStart(2,"0")}:`+
            `${String(menit).padStart(2,"0")}:`+
            `${String(detik).padStart(2,"0")}`;
    }

    update();

    interval = setInterval(update,1000);
}


</script>
</html>