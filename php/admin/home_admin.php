<?php
require_once("../auth.php");
require_admin_login();

$nama = $_SESSION['user']['nama'];
$role = $_SESSION['user']['role_name'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Administrator</title>
  <link rel="stylesheet" href="../../aset/css/style.css" />
</head>

<body>
  <?php
  include("./menu.php");
  ?>

  <div class="container">
    <h1>Halo, <?= htmlspecialchars($nama) ?>!</h1>
    <p>Anda login sebagai <b><?= htmlspecialchars($role) ?></b>.</p>
    <?php display_flash_message(); ?>
  </div>

  <hr />
  <small>&copy; 2025 RSHP | Halaman Administrator</small>
</body>

</html>