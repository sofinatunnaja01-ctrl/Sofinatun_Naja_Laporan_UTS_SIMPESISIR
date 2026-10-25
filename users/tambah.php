<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/users_fn.php';
require_role('admin');
$err = [];
$d = ['nama' => '', 'username' => '', 'role' => 'petugas'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    [$err, $d, $pass] = validasi_user($conn, $_POST, true);
    if (!$err) {
        try {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO users (nama, username, password_hash, role) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('ssss', $d['nama'], $d['username'], $hash, $d['role']);
            $stmt->execute();
            flash('ok', 'Pengguna berhasil ditambahkan.');
            redirect('users/index.php');
        } catch (mysqli_sql_exception $ex) {
            if ($ex->getCode() !== 1062) { throw $ex; }
            $err[] = 'Username sudah dipakai.';
        }
    }
}
$judul = 'Tambah Pengguna'; $tombol = 'Simpan'; $edit = false; $role_terkunci = false;
require __DIR__ . '/../includes/header.php';
?>
<h1>Tambah Pengguna</h1>
<div class="card"><?php require __DIR__ . '/_form.php'; ?></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
