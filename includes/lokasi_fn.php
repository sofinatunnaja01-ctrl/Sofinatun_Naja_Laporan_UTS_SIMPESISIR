<?php
function validasi_lokasi(array $in): array {
    $d = [
        'nama_lokasi'    => trim($in['nama_lokasi'] ?? ''),
        'kabupaten_kota' => trim($in['kabupaten_kota'] ?? ''),
        'keterangan'     => trim($in['keterangan'] ?? ''),
    ];
    $err = [];
    if (mb_strlen($d['nama_lokasi']) < 3 || mb_strlen($d['nama_lokasi']) > 100) { $err[] = 'Nama lokasi 3-100 karakter.'; }
    if (mb_strlen($d['kabupaten_kota']) < 3 || mb_strlen($d['kabupaten_kota']) > 100) { $err[] = 'Kabupaten/kota 3-100 karakter.'; }
    if (mb_strlen($d['keterangan']) > 255) { $err[] = 'Keterangan maksimal 255 karakter.'; }
    return [$err, $d];
}
