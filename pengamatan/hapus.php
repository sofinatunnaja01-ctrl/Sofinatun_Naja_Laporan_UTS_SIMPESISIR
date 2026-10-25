<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/pengamatan_fn.php';
require_login();
csrf_check();                                   // hanya POST + token
$id = (int)($_POST['id'] ?? 0);
ambil_pengamatan_milik($conn, $id);             // cek kepemilikan/hak akses
$stmt = $conn->prepare('DELETE FROM pengamatan WHERE id_pengamatan = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
flash('ok', 'Data pengamatan berhasil dihapus.');
redirect('pengamatan/index.php');
