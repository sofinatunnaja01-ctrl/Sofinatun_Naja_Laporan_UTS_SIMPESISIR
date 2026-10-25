<?php
require_once __DIR__ . '/../includes/init.php';
require_role('admin');
csrf_check();
$id = (int)($_POST['id'] ?? 0);
try {
    $stmt = $conn->prepare('DELETE FROM lokasi WHERE id_lokasi = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    flash('ok', $stmt->affected_rows ? 'Lokasi berhasil dihapus.' : 'Lokasi tidak ditemukan.');
} catch (mysqli_sql_exception $ex) {
    if ($ex->getCode() !== 1451) { throw $ex; }   // 1451 = dirujuk foreign key
    flash('error', 'Lokasi tidak dapat dihapus karena masih dipakai oleh data pengamatan. Hapus atau pindahkan data pengamatannya terlebih dahulu.');
}
redirect('lokasi/index.php');
