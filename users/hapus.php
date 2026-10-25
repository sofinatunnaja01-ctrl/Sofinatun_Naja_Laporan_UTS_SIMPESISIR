<?php
require_once __DIR__ . '/../includes/init.php';
require_role('admin');
csrf_check();
$id = (int)($_POST['id'] ?? 0);
if ($id === (int)$_SESSION['id_user']) {
    flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
    redirect('users/index.php');
}
try {
    $stmt = $conn->prepare('DELETE FROM users WHERE id_user = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    flash('ok', $stmt->affected_rows ? 'Pengguna berhasil dihapus.' : 'Pengguna tidak ditemukan.');
} catch (mysqli_sql_exception $ex) {
    if ($ex->getCode() !== 1451) { throw $ex; }
    flash('error', 'Pengguna tidak dapat dihapus karena masih tercatat sebagai pencatat data pengamatan.');
}
redirect('users/index.php');
