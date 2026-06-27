<?php
session_start();
require_once 'koneksi.php';

$pesan = "";
$status = "";

if(isset($_POST['login'])){

    $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
    $password = trim($_POST['password']);

    // Validasi input kosong
    if(empty($username)){
        $pesan = "Username tidak boleh kosong!";
        $status = "error";
    }
    elseif(empty($password)){
        $pesan = "Password tidak boleh kosong!";
        $status = "error";
    }
    else{

        $query = mysqli_query($conn,
            "SELECT * FROM users WHERE username='$username' LIMIT 1"
        );

        if(mysqli_num_rows($query) > 0){

            $user = mysqli_fetch_assoc($query);

            if($password == $user['password']){

                $_SESSION['id_user'] = $user['id_user'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                if($user['role'] == 'admin'){
                    header("Location: dashboard-admin.php");
                }else{
                    header("Location: dashboard-user.php");
                }

                exit;

            }else{
                $pesan = "Password salah!";
                $status = "error";
            }

        }else{
            $pesan = "Username tidak ditemukan!";
            $status = "error";
        }

    }
}
?>

<link href="css/index.css" rel="stylesheet">
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <div class="header-login">
        <h1>PELUK</h1>
        <p>Coffee Space & Rental PS</p>
    </div>
        <div class="kotak">
            <div class="header-kotak">
                <label>Masuk ke Ruang Main</label>
            </div>
            <span>Pakai akun yang sudah didaftarkan kasir, atau buat akun baru.</span>
            <form method="POST">
                <div class="letak">
                <label>USERNAME</label>
                    <input type="text" placeholder="Username" name="username"  value="<?= isset($_POST['username']) ? ($_POST['username']) : '' ?>">
                <label>PASSWORD</label>
                <div class="input-l">
                    <input type="password" placeholder="Password" name="password" id="password">
                    <button type="button" id="togglePassword" class="mata"><img src="./icon/eye-off.png" id="iconEye"></button>
                </div>
                <?php if($status == "error"): ?>
                    <div class="pesan-error">
                        <?= $pesan ?>
                    </div>
                    <?php endif; ?>
                <button type="submit" name="login">Masuk</button>
                <div class="letak-daftar">
                    
                    <label>Belum punya akun? <a href="register.php">Daftar disini</a></label>
                </form>
                </div>
            </div>
</body>
<script>

const password = document.getElementById("password");
const tombol = document.getElementById("togglePassword");
const iconEye = document.getElementById("iconEye");

tombol.addEventListener("click", () => {

    if(password.type === "password"){

        password.type = "text";
        iconEye.src = "./icon/eye.png";

    }else{

        password.type = "password";
        iconEye.src = "./icon/eye-off.png";

    }

});
</script>
</html>