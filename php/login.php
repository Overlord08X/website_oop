<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login · RSHP</title>
    <link rel="stylesheet" href="../aset/css/style.css">
</head>

<body>
    <?php
    require_once("auth.php"); // Memuat fungsi auth dan memulai sesi
    ?>
    <div class="navbar">
        <div class="logo">
            <img src="../aset/logo.png" alt="Logo RSHP" />
        </div>
        <div class="menu">
            <a href="../index.html">Home</a>
            <a href="../struktur.html">Struktur Organisasi</a>
            <a href="../layanan.html">Layanan Umum</a>
            <a href="../visi_misi.html">Visi, Misi & Tujuan</a>
            <a href="./login.php" class="active">Login</a>
        </div>
    </div>

    <div class="login-box">
        <h2>Login</h2>
        <form action="./proses_login.php" method="post">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="submit" value="Login">
        </form>

        <?php
        display_flash_message(); // Menampilkan flash message
        ?>
    </div>

    <hr />
    <small>
        &copy; 2025 RSHP (Website statis tugas Modul 1) | Sumber:
        <a href="https://rshp.unair.ac.id" target="_blank">RSHP Unair</a>
    </small>
</body>

</html>