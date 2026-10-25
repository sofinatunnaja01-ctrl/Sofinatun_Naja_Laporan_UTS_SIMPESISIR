<?php /* butuh: $d, $err, $tombol, $edit (bool), $role_terkunci (bool) */ ?>
<?php if ($err): ?><ul class="errors"><?php foreach ($err as $m): ?><li><?= e($m) ?></li><?php endforeach; ?></ul><?php endif; ?>
<form method="post" autocomplete="off">
  <?= csrf_field() ?>
  <label>Nama</label>
  <input type="text" name="nama" value="<?= e($d['nama']) ?>" maxlength="100" required>
  <label>Username</label>
  <input type="text" name="username" value="<?= e($d['username']) ?>" maxlength="30" required>
  <label>Peran</label>
  <select name="role" <?= !empty($role_terkunci) ? 'disabled' : '' ?>>
    <?php foreach (['petugas', 'admin'] as $r): ?>
      <option value="<?= $r ?>" <?= $d['role'] === $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
    <?php endforeach; ?>
  </select>
  <?php if (!empty($role_terkunci)): ?><small>Peran akun Anda sendiri tidak dapat diubah.</small><?php endif; ?>
  <label>Password <?= $edit ? '(kosongkan jika tidak diganti)' : '' ?></label>
  <input type="password" name="password" <?= $edit ? '' : 'required' ?>>
  <label>Konfirmasi password</label>
  <input type="password" name="password2" <?= $edit ? '' : 'required' ?>>
  <p><button class="btn" type="submit"><?= e($tombol) ?></button>
     <a class="btn btn-grey" href="<?= e(url('users/index.php')) ?>">Batal</a></p>
</form>
