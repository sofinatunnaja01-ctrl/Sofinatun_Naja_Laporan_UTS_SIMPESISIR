<?php /* butuh: $d, $err, $lokasi, $ekosistem, $tombol */ ?>
<?php if ($err): ?><ul class="errors"><?php foreach ($err as $m): ?><li><?= e($m) ?></li><?php endforeach; ?></ul><?php endif; ?>
<form method="post">
  <?= csrf_field() ?>
  <label>Lokasi</label>
  <select name="id_lokasi" required>
    <option value="">-- pilih lokasi --</option>
    <?php foreach ($lokasi as $l): ?>
      <option value="<?= (int)$l['id_lokasi'] ?>" <?= (int)$d['id_lokasi'] === (int)$l['id_lokasi'] ? 'selected' : '' ?>><?= e($l['nama_lokasi']) ?></option>
    <?php endforeach; ?>
  </select>
  <label>Ekosistem</label>
  <select name="id_ekosistem" required>
    <option value="">-- pilih ekosistem --</option>
    <?php foreach ($ekosistem as $x): ?>
      <option value="<?= (int)$x['id_ekosistem'] ?>" <?= (int)$d['id_ekosistem'] === (int)$x['id_ekosistem'] ? 'selected' : '' ?>><?= e($x['nama_ekosistem']) ?></option>
    <?php endforeach; ?>
  </select>
  <label>Tanggal pengamatan</label>
  <input type="date" name="tanggal" value="<?= e($d['tanggal']) ?>" max="<?= date('Y-m-d') ?>" required>
  <label>Kondisi</label>
  <select name="kondisi" required>
    <?php foreach (['baik', 'sedang', 'rusak'] as $k): ?>
      <option value="<?= $k ?>" <?= $d['kondisi'] === $k ? 'selected' : '' ?>><?= ucfirst($k) ?></option>
    <?php endforeach; ?>
  </select>
  <label>Tutupan (%)</label>
  <input type="text" name="tutupan_persen" value="<?= e($d['tutupan']) ?>" placeholder="contoh: 72.5" required>
  <label>Catatan (opsional)</label>
  <textarea name="catatan" rows="3" maxlength="500"><?= e($d['catatan']) ?></textarea>
  <p><button class="btn" type="submit"><?= e($tombol) ?></button>
     <a class="btn btn-grey" href="<?= e(url('pengamatan/index.php')) ?>">Batal</a></p>
</form>
