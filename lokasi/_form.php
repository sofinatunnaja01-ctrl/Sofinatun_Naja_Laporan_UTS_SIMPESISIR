<?php /* butuh: $d, $err, $tombol */ ?>
<?php if ($err): ?><ul class="errors"><?php foreach ($err as $m): ?><li><?= e($m) ?></li><?php endforeach; ?></ul><?php endif; ?>
<form method="post">
  <?= csrf_field() ?>
  <label>Nama lokasi</label>
  <input type="text" name="nama_lokasi" value="<?= e($d['nama_lokasi']) ?>" maxlength="100" required>
  <label>Kabupaten / Kota</label>
  <input type="text" name="kabupaten_kota" value="<?= e($d['kabupaten_kota']) ?>" maxlength="100" required>
  <label>Keterangan (opsional)</label>
  <textarea name="keterangan" rows="3" maxlength="255"><?= e($d['keterangan']) ?></textarea>
  <p><button class="btn" type="submit"><?= e($tombol) ?></button>
     <a class="btn btn-grey" href="<?= e(url('lokasi/index.php')) ?>">Batal</a></p>
</form>
