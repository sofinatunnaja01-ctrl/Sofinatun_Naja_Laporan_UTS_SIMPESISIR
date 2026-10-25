<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/pengamatan_fn.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$row = ambil_pengamatan_milik($conn, $id);       // cek kepemilikan di server
[$lokasi, $ekosistem] = ambil_master($conn);
$err = [];
$d = [
    'id_lokasi' => $row['id_lokasi'], 'id_ekosistem' => $row['id_ekosistem'], 'tanggal' => $row['tanggal'],
    'kondisi' => $row['kondisi'], 'tutupan' => $row['tutupan_persen'], 'catatan' => (string)$row['catatan'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    [$err, $d] = validasi_pengamatan($conn, $_POST);
    if (!$err) {
        $stmt = $conn->prepare(
            'UPDATE pengamatan SET id_lokasi = ?, id_ekosistem = ?, tanggal = ?, kondisi = ?,
                    tutupan_persen = ?, catatan = ? WHERE id_pengamatan = ?'
        );
        $tutupan = (float)$d['tutupan'];
        $stmt->bind_param('iissdsi', $d['id_lokasi'], $d['id_ekosistem'], $d['tanggal'],
                          $d['kondisi'], $tutupan, $d['catatan'], $id);
        $stmt->execute();
        flash('ok', 'Data pengamatan berhasil diubah.');
        redirect('pengamatan/index.php');
    }
}
$judul = 'Edit Pengamatan';
$tombol = 'Simpan Perubahan';
require __DIR__ . '/../includes/header.php';
?>
<h1>Edit Pengamatan #<?= (int)$id ?></h1>
<div class="card"><?php require __DIR__ . '/_form.php'; ?></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
