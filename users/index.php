<?php
require_once __DIR__ . '/../includes/init.php';
require_role('admin');
$data = $conn->query('SELECT id_user, nama, username, role, created_at FROM users ORDER BY role, nama')->fetch_all(MYSQLI_ASSOC);
$judul = 'Manajemen Pengguna';
require __DIR__ . '/../includes/header.php';
?>
<h1>Manajemen Pengguna</h1>
<div class="toolbar"><span></span><a class="btn" href="<?= e(url('users/tambah.php')) ?>">+ Tambah Pengguna</a></div>
<div class="card table-wrap"><table>
  <tr><th>No</th><th>Nama</th><th>Username</th><th>Peran</th><th>Dibuat</th><th>Aksi</th></tr>
  <?php foreach ($data as $i => $r): ?>
  <tr>
    <td><?= $i + 1 ?></td><td><?= e($r['nama']) ?></td><td><?= e($r['username']) ?></td>
    <td><?= e($r['role']) ?></td><td><?= e($r['created_at']) ?></td>
    <td>
      <a class="btn btn-small" href="<?= e(url('users/edit.php?id=' . (int)$r['id_user'])) ?>">Edit</a>
      <?php if ((int)$r['id_user'] !== (int)$_SESSION['id_user']): ?>
      <form class="inline" method="post" action="<?= e(url('users/hapus.php')) ?>" onsubmit="return confirm('Hapus pengguna ini?')">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id_user'] ?>">
        <button class="btn btn-small btn-danger" type="submit">Hapus</button>
      </form>
      <?php endif; ?>
    </td>
  </tr>
  <?php endforeach; ?>
</table></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
