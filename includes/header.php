<?php /* set $judul sebelum include */ ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($judul ?? 'SIMPESISIR') ?> - SIMPESISIR</title>
<link rel="stylesheet" href="<?= e(url('assets/style.css')) ?>">
</head>
<body>
<?php if (is_logged_in()): ?>
<nav class="nav">
  <div class="brand">SIMPESISIR</div>
  <ul>
    <li><a href="<?= e(url('dashboard.php')) ?>">Dashboard</a></li>
    <li><a href="<?= e(url('pengamatan/index.php')) ?>">Pengamatan</a></li>
    <?php if (is_admin()): ?>
    <li><a href="<?= e(url('lokasi/index.php')) ?>">Lokasi</a></li>
    <li><a href="<?= e(url('users/index.php')) ?>">Pengguna</a></li>
    <?php endif; ?>
  </ul>
  <form method="post" action="<?= e(url('logout.php')) ?>" class="logout">
    <?= csrf_field() ?>
    <span><?= e($_SESSION['nama'] ?? '') ?> (<?= e($_SESSION['role'] ?? '') ?>)</span>
    <button type="submit" class="btn btn-small">Logout</button>
  </form>
</nav>
<?php endif; ?>
<main class="container">
<?php if (!empty($_SESSION['flash'])): $f = $_SESSION['flash']; unset($_SESSION['flash']); ?>
  <div class="alert <?= $f['type'] === 'error' ? 'alert-error' : 'alert-ok' ?>"><?= e($f['msg']) ?></div>
<?php endif; ?>
