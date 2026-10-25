<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/pengamatan_fn.php';
require_login();

[$lokasi, $ekosistem] = ambil_master($conn);
$err = [];
$d = ['id_lokasi' => 0, 'id_ekosistem' => 0, 'tanggal' => date('Y-m-d'), 'kondisi' => 'baik', 'tutupan' => '', 'catatan' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    [$err, $d] = validasi_pengamatan($conn, $_POST);
    if (!$err) {
        $stmt = $conn->prepare(
            'INSERT INTO pengamatan (id_lokasi, id_ekosistem, id_user, tanggal, kondisi, tutupan_persen, catatan)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $tutupan = (float)$d['tutupan'];
        $stmt->bind_param('iiissds', $d['id_lokasi'], $d['id_ekosistem'], $_SESSION['id_user'],
                          $d['tanggal'], $d['kondisi'], $tutupan, $d['catatan']);
        $stmt->execute();
        flash('ok', 'Data pengamatan berhasil ditambahkan.');
        redirect('pengamatan/index.php');
    }
}
$judul = 'Tambah Pengamatan';
$tombol = 'Simpan';
require __DIR__ . '/../includes/header.php';
?>
<h1>Tambah Pengamatan</h1>
<div class="card"><?php require __DIR__ . '/_form.php'; ?></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
