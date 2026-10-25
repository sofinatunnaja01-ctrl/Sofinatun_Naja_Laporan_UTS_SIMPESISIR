<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/users_fn.php';
require_role('admin');
$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare('SELECT nama, username, role FROM users WHERE id_user = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if (!$row) { http_response_code(404); exit('Pengguna tidak ditemukan.'); }

$diri_sendiri = ($id === (int)$_SESSION['id_user']);
$err = [];
$d = $row;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if ($diri_sendiri) { $_POST['role'] = $row['role']; }   // cegah admin menurunkan peran sendiri
    [$err, $d, $pass] = validasi_user($conn, $_POST, false, $id);
    if (!$err) {
        try {
            if ($pass !== '') {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $stmt = $conn->prepare('UPDATE users SET nama = ?, username = ?, role = ?, password_hash = ? WHERE id_user = ?');
                $stmt->bind_param('ssssi', $d['nama'], $d['username'], $d['role'], $hash, $id);
            } else {
                $stmt = $conn->prepare('UPDATE users SET nama = ?, username = ?, role = ? WHERE id_user = ?');
                $stmt->bind_param('sssi', $d['nama'], $d['username'], $d['role'], $id);
            }
            $stmt->execute();
            flash('ok', 'Pengguna berhasil diubah.');
            redirect('users/index.php');
        } catch (mysqli_sql_exception $ex) {
            if ($ex->getCode() !== 1062) { throw $ex; }
            $err[] = 'Username sudah dipakai.';
        }
    }
}
$judul = 'Edit Pengguna'; $tombol = 'Simpan Perubahan'; $edit = true; $role_terkunci = $diri_sendiri;
require __DIR__ . '/../includes/header.php';
?>
<h1>Edit Pengguna</h1>
<div class="card"><?php require __DIR__ . '/_form.php'; ?></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
