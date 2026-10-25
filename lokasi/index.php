<?php
require_once __DIR__ . '/../includes/init.php';
require_role('admin');
$data = $conn->query(
    'SELECT l.*, COUNT(p.id_pengamatan) AS jml
     FROM lokasi l LEFT JOIN pengamatan p ON p.id_lokasi = l.id_lokasi
     GROUP BY l.id_lokasi ORDER BY l.nama_lokasi'
)->fetch_all(MYSQLI_ASSOC);
$judul = 'Data Lokasi';
require __DIR__ . '/../includes/header.php';
?>
<h1>Data Lokasi</h1>
<div class="toolbar"><span></span><a class="btn" href="<?= e(url('lokasi/tambah.php')) ?>">+ Tambah Lokasi</a></div>
<div class="card table-wrap"><table>
  <tr><th>No</th><th>Nama lokasi</th><th>Kabupaten/Kota</th><th>Keterangan</th><th>Jml pengamatan</th><th>Aksi</th></tr>
  <?php foreach ($data as $i => $r): ?>
  <tr>
    <td><?= $i + 1 ?></td><td><?= e($r['nama_lokasi']) ?></td><td><?= e($r['kabupaten_kota']) ?></td>
    <td><?= e($r['keterangan'] ?: '-') ?></td><td><?= (int)$r['jml'] ?></td>
    <td>
      <a class="btn btn-small" href="<?= e(url('lokasi/edit.php?id=' . (int)$r['id_lokasi'])) ?>">Edit</a>
      <form class="inline" method="post" action="<?= e(url('lokasi/hapus.php')) ?>" onsubmit="return confirm('Hapus lokasi ini?')">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id_lokasi'] ?>">
        <button class="btn btn-small btn-danger" type="submit">Hapus</button>
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
  <?php if (!$data): ?><tr><td colspan="6">Belum ada lokasi.</td></tr><?php endif; ?>
</table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
