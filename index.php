<?php
session_start();
require_once 'koneksi.php';

if(isset($_POST['login'])){

    
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    
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
            echo "Password salah!";
        }

    }else{
        echo "Username tidak ditemukan!";
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
                    <input type="text" placeholder="Username" name="username" required>
                <label>PASSWORD</label>
                <div class="input-l">
                    <input type="password" placeholder="Password" name="password" required>
                </div>
                <button type="submit" name="login">Masuk</button>
                <div class="letak-daftar">
                    
                    <label>Belum punya akun? <a href="register.php">Daftar disini</a></label>
                </form>
                </div>
            </div>
</body>
</html>