<?php
require_once 'koneksi.php';

if(isset($_POST['register'])){

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = ($_POST['password']);

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");

    if(mysqli_num_rows($cek) > 0){
        echo "Username sudah digunakan!";
    }else{

        $query = mysqli_query($conn,
            "INSERT INTO users(username,password)
             VALUES('$username','$password')"
        );

        if($query){
            echo "Akun berhasil dibuat!";
        }else{
            echo "Gagal membuat akun!";
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
                    <input type="text" placeholder="Username" name="username" required>
                <label>PASSWORD</label>
                <div class="input-l">
                    <input type="password" placeholder="Password" name="password" required>
                </div>
                <button type="submit" name="register">Buat akun</button>
                <div class="letak-daftar">
                    
                    <label>Sudah punya akun? <a href="index.php">Masuk disini</a></label>
                </div>
                </div>
        </form>
        </div>
</body>
</html>