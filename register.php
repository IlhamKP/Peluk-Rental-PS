<?php
session_start();
require_once 'koneksi.php';

$pesan = "";
$status = "";

if(isset($_POST['register'])){

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

        $cek = mysqli_query($conn,
            "SELECT * FROM users WHERE username='$username'"
        );

        if(mysqli_num_rows($cek) > 0){

            $pesan = "Username sudah digunakan!";
            $status = "error";

        }else{

            $query = mysqli_query($conn,
                "INSERT INTO users(username,password)
                 VALUES('$username','$password')"
            );

            if($query){

                $id_user = mysqli_insert_id($conn);

                $_SESSION['id_user'] = $id_user;
                $_SESSION['username'] = $username;

                $_SESSION['pesan'] = "Akun berhasil dibuat!";
                $_SESSION['status'] = "success";

                header("Location: dashboard-user.php");
                exit;

            }else{

                $pesan = "Gagal membuat akun!";
                $status = "error";

            }

        }

    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="css/register.css" rel="stylesheet">
</head>
<body>
    <div class="header-login">
        <h1>PELUK</h1>
        <p>Coffee Space & Rental PS</p>
    </div>
        <div class="kotak">
                <form method="POST" >
                <label>Daftar akun Baru</label>
                <span><br>Sekali daftar, dipakai terus setiap main ke cafe.</span>
                <div class="letak">
                    <label>USERNAME</label>
                    <input type="text" placeholder="Username" name="username">
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
                <button type="submit" name="register">Buat akun</button>
                <div class="letak-daftar">
                    
                    <label>Sudah punya akun? <a href="index.php">Masuk disini</a></label>
                </div>
                </div>
        </form>
        </div>
</body>

<script>
const toast = document.querySelector(".toast");

if (toast) {
    setTimeout(() => {
        toast.classList.add("hide");
    }, 3000);

    toast.addEventListener("transitionend", () => {
        toast.remove();
    });
}

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