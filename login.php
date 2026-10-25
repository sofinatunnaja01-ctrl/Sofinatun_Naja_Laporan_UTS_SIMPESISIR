<?php
require_once __DIR__ . '/includes/init.php';
if (is_logged_in()) { redirect('dashboard.php'); }

$error = '';
$username = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $username = trim($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    $stmt = $conn->prepare('SELECT id_user, nama, password_hash, role FROM users WHERE username = ? LIMIT 1');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();

    // Tetap jalankan password_verify walau user tidak ada (waktu proses seragam).
    $hash = $u ? $u['password_hash'] : password_hash('dummy-password', PASSWORD_DEFAULT);
    if (password_verify($password, $hash) && $u) {
        session_regenerate_id(true);                 // cegah session fixation
        $_SESSION['id_user'] = (int)$u['id_user'];
        $_SESSION['nama']    = $u['nama'];
        $_SESSION['role']    = $u['role'];
        $_SESSION['csrf']    = bin2hex(random_bytes(32));
        redirect('dashboard.php');
    }
    // Pesan sama untuk username salah maupun password salah.
    $error = 'Username atau password salah.';
}
$judul = 'Login';
require __DIR__ . '/includes/header.php';
?>
<div class="card login-box">
  <h1>Login SIMPESISIR</h1>
  <p>Sistem Informasi Pencatatan Pengamatan Ekosistem Pesisir</p>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <form method="post" autocomplete="off">
    <?= csrf_field() ?>
    <label for="username">Username</label>
    <input type="text" id="username" name="username" value="<?= e($username) ?>" required maxlength="50">
    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>
    <p><button class="btn" type="submit">Masuk</button></p>
  </form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
