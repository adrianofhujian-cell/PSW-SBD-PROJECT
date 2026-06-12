<?php
include "../Config/config.php";

session_start();

if(isset($_POST['login']))
{
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($koneksi,
        "SELECT * FROM pengguna
         WHERE username='$username'
         AND password='$password'
         AND role='Admin'"
    );

    if(mysqli_num_rows($query) > 0)
    {
        $data = mysqli_fetch_assoc($query);

        $_SESSION['admin'] = $data['username'];

        header("Location: Dashboard.php");
        exit;
    }
    else
    {
        echo "<script>alert('Username atau Password salah!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Larris</title>
    <link rel="stylesheet" href="Login.css">
    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="container">

    <div class="left-panel">
    <div class="overlay"></div>

    <div class="left-content">
        <div class="logo">🏪</div>

        <h1>Admin Panel</h1>

        <p>
            Kelola produk, stok, pesanan,
            transaksi dan pengguna
            dalam satu dashboard.
        </p>

        <div class="features">
            <div class="item">📦 Produk</div>
            <div class="item">📊 Stok</div>
            <div class="item">🛒 Pesanan</div>
        </div>
    </div>
</div>

    <div class="right-panel">

        <div class="login-card">

            <div class="top-logo">🏪</div>

            <h2>Login Admin</h2>

            <p class="subtitle">
                Masuk ke dashboard administrator
            </p>

            <form method="POST">

                <label>Username</label>

                <div class="input-box">
                    <i class="fa-regular fa-user"></i>
                    <input type="text"
                    name="username"
                    placeholder="Masukkan username">
                </div>

                <label>Password</label>

                <div class="input-box">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password"
                    name="password"
                    placeholder="Masukkan password">
                </div>

                <button type="submit" name="login">
                    Masuk Dashboard
                </button>

            </form>

        </div>

        <div class="copyright">
            © 2026 Larris
        </div>

    </div>

</div>

</body>
</html>