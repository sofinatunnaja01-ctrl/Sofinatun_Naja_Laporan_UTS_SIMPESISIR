<?php
require_once __DIR__ . '/../includes/init.php';
require_login();

$q = trim($_GET['q'] ?? '');
$like = '%' . $q . '%';
$stmt = $conn->prepare(
    'SELECT p.id_pengamatan, p.tanggal, p.kondisi, p.tutupan_persen, p.id_user,
            l.nama_lokasi, e.nama_ekosistem, u.nama AS petugas
     FROM pengamatan p
     JOIN lokasi l ON l.id_lokasi = p.id_lokasi
     JOIN ekosistem e ON e.id_ekosistem = p.id_ekosistem
     JOIN users u ON u.id_user = p.id_user
     WHERE l.nama_lokasi LIKE ? OR e.nama_ekosistem LIKE ?
     ORDER BY p.tanggal DESC, p.id_pengamatan DESC'
);
$stmt->bind_param('ss', $like, $like);
$stmt->execute();
$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$judul = 'Data Pengamatan';
require __DIR__ . '/../includes/header.php';
?>
<h1>Data Pengamatan</h1>
<div class="toolbar">
  <form method="get">
    <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cari lokasi / ekosistem">
    <button class="btn" type="submit">Cari</button>
  </form>
  <a class="btn" href="<?= e(url('pengamatan/tambah.php')) ?>">+ Tambah Pengamatan</a>
</div>
<div class="card table-wrap"><table>
  <tr><th>No</th><th>Tanggal</th><th>Lokasi</th><th>Ekosistem</th><th>Kondisi</th><th>Tutupan (%)</th><th>Petugas</th><th>Aksi</th></tr>
  <?php foreach ($data as $i => $r):
        $boleh = is_admin() || (int)$r['id_user'] === (int)$_SESSION['id_user']; ?>
  <tr>
    <td><?= $i + 1 ?></td><td><?= e($r['tanggal']) ?></td><td><?= e($r['nama_lokasi']) ?></td>
    <td><?= e($r['nama_ekosistem']) ?></td>
    <td><span class="badge <?= e($r['kondisi']) ?>"><?= e($r['kondisi']) ?></span></td>
    <td><?= e($r['tutupan_persen']) ?></td><td><?= e($r['petugas']) ?></td>
    <td>
      <a class="btn btn-small btn-grey" href="<?= e(url('pengamatan/detail.php?id=' . (int)$r['id_pengamatan'])) ?>">Detail</a>
      <?php if ($boleh): ?>
      <a class="btn btn-small" href="<?= e(url('pengamatan/edit.php?id=' . (int)$r['id_pengamatan'])) ?>">Edit</a>
      <form class="inline" method="post" action="<?= e(url('pengamatan/hapus.php')) ?>" onsubmit="return confirm('Hapus data ini?')">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id_pengamatan'] ?>">
        <button class="btn btn-small btn-danger" type="submit">Hapus</button>
      </form>
      <?php endif; ?>
    </td>
  </tr>
  <?php endforeach; ?>
  <?php if (!$data): ?><tr><td colspan="8">Data tidak ditemukan.</td></tr><?php endif; ?>
</table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
