<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login · RSHP</title>
    <link rel="stylesheet" href="../aset/css/style.css">
</head>

<body>
    <div class="navbar">
        <div class="logo">
            <img src="../aset/UNAIR_SEAL_LOGO_2025_RGB-01.png" alt="Logo RSHP" />
        </div>
        <div class="menu">
            <a href="../index.html">Home</a>
            <a href="../struktur.html">Struktur Organisasi</a>
            <a href="../layanan.html">Layanan Umum</a>
            <a href="../visi_misi.html">Visi, Misi & Tujuan</a> <!-- ✅ perbaikan path -->
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
        session_start();
        if (isset($_SESSION['flash_msg'])) {
            echo "<p class='flash-msg'>" . $_SESSION['flash_msg'] . "</p>";
            unset($_SESSION['flash_msg']);
        }
        ?>
    </div>

    <hr />
    <small>
        &copy; 2025 RSHP (Website statis tugas Modul 1) | Sumber:
        <a href="https://rshp.unair.ac.id" target="_blank">RSHP Unair</a>
    </small>
</body>

</html>