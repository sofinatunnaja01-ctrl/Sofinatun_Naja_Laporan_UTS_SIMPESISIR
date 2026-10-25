<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/lokasi_fn.php';
require_role('admin');
$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare('SELECT * FROM lokasi WHERE id_lokasi = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if (!$row) { http_response_code(404); exit('Data tidak ditemukan.'); }

$err = [];
$d = ['nama_lokasi' => $row['nama_lokasi'], 'kabupaten_kota' => $row['kabupaten_kota'], 'keterangan' => (string)$row['keterangan']];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    [$err, $d] = validasi_lokasi($_POST);
    if (!$err) {
        try {
            $stmt = $conn->prepare('UPDATE lokasi SET nama_lokasi = ?, kabupaten_kota = ?, keterangan = NULLIF(?, "") WHERE id_lokasi = ?');
            $stmt->bind_param('sssi', $d['nama_lokasi'], $d['kabupaten_kota'], $d['keterangan'], $id);
            $stmt->execute();
            flash('ok', 'Lokasi berhasil diubah.');
            redirect('lokasi/index.php');
        } catch (mysqli_sql_exception $ex) {
            if ($ex->getCode() !== 1062) { throw $ex; }
            $err[] = 'Lokasi dengan nama dan kabupaten/kota tersebut sudah ada.';
        }
    }
}
$judul = 'Edit Lokasi'; $tombol = 'Simpan Perubahan';
require __DIR__ . '/../includes/header.php';
?>
<h1>Edit Lokasi</h1>
<div class="card"><?php require __DIR__ . '/_form.php'; ?></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
