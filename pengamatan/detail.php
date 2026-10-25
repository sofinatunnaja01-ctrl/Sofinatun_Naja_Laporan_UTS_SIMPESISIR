<?php
require_once __DIR__ . '/../includes/init.php';
require_login();
$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare(
    'SELECT p.*, l.nama_lokasi, l.kabupaten_kota, e.nama_ekosistem, u.nama AS petugas
     FROM pengamatan p
     JOIN lokasi l ON l.id_lokasi = p.id_lokasi
     JOIN ekosistem e ON e.id_ekosistem = p.id_ekosistem
     JOIN users u ON u.id_user = p.id_user
     WHERE p.id_pengamatan = ?'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$r = $stmt->get_result()->fetch_assoc();
if (!$r) { http_response_code(404); exit('Data tidak ditemukan.'); }

$judul = 'Detail Pengamatan';
require __DIR__ . '/../includes/header.php';
?>
<h1>Detail Pengamatan #<?= (int)$r['id_pengamatan'] ?></h1>
<div class="card"><table>
  <tr><th>Lokasi</th><td><?= e($r['nama_lokasi']) ?>, <?= e($r['kabupaten_kota']) ?></td></tr>
  <tr><th>Ekosistem</th><td><?= e($r['nama_ekosistem']) ?></td></tr>
  <tr><th>Tanggal</th><td><?= e($r['tanggal']) ?></td></tr>
  <tr><th>Kondisi</th><td><span class="badge <?= e($r['kondisi']) ?>"><?= e($r['kondisi']) ?></span></td></tr>
  <tr><th>Tutupan</th><td><?= e($r['tutupan_persen']) ?> %</td></tr>
  <tr><th>Catatan</th><td><?= e($r['catatan'] ?: '-') ?></td></tr>
  <tr><th>Dicatat oleh</th><td><?= e($r['petugas']) ?> (<?= e($r['created_at']) ?>)</td></tr>
</table>
<p><a class="btn btn-grey" href="<?= e(url('pengamatan/index.php')) ?>">Kembali</a></p></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
