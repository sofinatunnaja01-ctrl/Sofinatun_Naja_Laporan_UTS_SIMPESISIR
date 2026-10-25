<?php
require_once __DIR__ . '/includes/init.php';
require_login();

$total_pengamatan = $conn->query('SELECT COUNT(*) c FROM pengamatan')->fetch_assoc()['c'];
$total_lokasi     = $conn->query('SELECT COUNT(*) c FROM lokasi')->fetch_assoc()['c'];
$total_user       = $conn->query('SELECT COUNT(*) c FROM users')->fetch_assoc()['c'];

$stmt = $conn->prepare('SELECT COUNT(*) c FROM pengamatan WHERE id_user = ?');
$stmt->bind_param('i', $_SESSION['id_user']);
$stmt->execute();
$milik_saya = $stmt->get_result()->fetch_assoc()['c'];

$terbaru = $conn->query(
    'SELECT p.id_pengamatan, p.tanggal, p.kondisi, p.tutupan_persen, l.nama_lokasi, e.nama_ekosistem
     FROM pengamatan p
     JOIN lokasi l ON l.id_lokasi = p.id_lokasi
     JOIN ekosistem e ON e.id_ekosistem = p.id_ekosistem
     ORDER BY p.tanggal DESC, p.id_pengamatan DESC LIMIT 5'
)->fetch_all(MYSQLI_ASSOC);

$judul = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>
<h1>Dashboard</h1>
<p>Selamat datang, <b><?= e($_SESSION['nama']) ?></b> (<?= e($_SESSION['role']) ?>).</p>
<div class="grid">
  <div class="stat"><b><?= (int)$total_pengamatan ?></b>Total pengamatan</div>
  <div class="stat"><b><?= (int)$milik_saya ?></b>Pengamatan saya</div>
  <div class="stat"><b><?= (int)$total_lokasi ?></b>Lokasi</div>
  <?php if (is_admin()): ?><div class="stat"><b><?= (int)$total_user ?></b>Pengguna</div><?php endif; ?>
</div>
<div class="card">
  <h2>Pengamatan Terbaru</h2>
  <div class="table-wrap"><table>
    <tr><th>Tanggal</th><th>Lokasi</th><th>Ekosistem</th><th>Kondisi</th><th>Tutupan (%)</th></tr>
    <?php foreach ($terbaru as $r): ?>
    <tr>
      <td><?= e($r['tanggal']) ?></td><td><?= e($r['nama_lokasi']) ?></td><td><?= e($r['nama_ekosistem']) ?></td>
      <td><span class="badge <?= e($r['kondisi']) ?>"><?= e($r['kondisi']) ?></span></td>
      <td><?= e($r['tutupan_persen']) ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$terbaru): ?><tr><td colspan="5">Belum ada data.</td></tr><?php endif; ?>
  </table></div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
