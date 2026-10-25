<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/lokasi_fn.php';
require_role('admin');
$err = [];
$d = ['nama_lokasi' => '', 'kabupaten_kota' => '', 'keterangan' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    [$err, $d] = validasi_lokasi($_POST);
    if (!$err) {
        try {
            $stmt = $conn->prepare('INSERT INTO lokasi (nama_lokasi, kabupaten_kota, keterangan) VALUES (?, ?, NULLIF(?, ""))');
            $stmt->bind_param('sss', $d['nama_lokasi'], $d['kabupaten_kota'], $d['keterangan']);
            $stmt->execute();
            flash('ok', 'Lokasi berhasil ditambahkan.');
            redirect('lokasi/index.php');
        } catch (mysqli_sql_exception $ex) {
            if ($ex->getCode() !== 1062) { throw $ex; }
            $err[] = 'Lokasi dengan nama dan kabupaten/kota tersebut sudah ada.';
        }
    }
}
$judul = 'Tambah Lokasi'; $tombol = 'Simpan';
require __DIR__ . '/../includes/header.php';
?>
<h1>Tambah Lokasi</h1>
<div class="card"><?php require __DIR__ . '/_form.php'; ?></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
