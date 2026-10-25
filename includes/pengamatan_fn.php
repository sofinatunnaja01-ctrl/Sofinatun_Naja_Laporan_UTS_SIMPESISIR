<?php
// Validasi & helper untuk modul pengamatan.
function ada_baris(mysqli $conn, string $tabel, string $kolom, int $id): bool {
    $izin = ['lokasi' => 'id_lokasi', 'ekosistem' => 'id_ekosistem']; // whitelist nama tabel/kolom
    if (!isset($izin[$tabel]) || $izin[$tabel] !== $kolom) { return false; }
    $stmt = $conn->prepare("SELECT 1 FROM `$tabel` WHERE `$kolom` = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    return (bool)$stmt->get_result()->fetch_row();
}

function validasi_pengamatan(mysqli $conn, array $in): array {
    $d = [
        'id_lokasi'    => (int)($in['id_lokasi'] ?? 0),
        'id_ekosistem' => (int)($in['id_ekosistem'] ?? 0),
        'tanggal'      => trim($in['tanggal'] ?? ''),
        'kondisi'      => trim($in['kondisi'] ?? ''),
        'tutupan'      => str_replace(',', '.', trim($in['tutupan_persen'] ?? '')),
        'catatan'      => trim($in['catatan'] ?? ''),
    ];
    $err = [];
    if (!ada_baris($conn, 'lokasi', 'id_lokasi', $d['id_lokasi'])) { $err[] = 'Lokasi tidak valid.'; }
    if (!ada_baris($conn, 'ekosistem', 'id_ekosistem', $d['id_ekosistem'])) { $err[] = 'Ekosistem tidak valid.'; }

    $dt = DateTime::createFromFormat('Y-m-d', $d['tanggal']);
    if (!$dt || $dt->format('Y-m-d') !== $d['tanggal']) { $err[] = 'Format tanggal tidak valid.'; }
    elseif ($d['tanggal'] > date('Y-m-d')) { $err[] = 'Tanggal tidak boleh di masa depan.'; }

    if (!in_array($d['kondisi'], ['baik', 'sedang', 'rusak'], true)) { $err[] = 'Kondisi tidak valid.'; }

    if ($d['tutupan'] === '' || !is_numeric($d['tutupan']) || $d['tutupan'] < 0 || $d['tutupan'] > 100) {
        $err[] = 'Tutupan harus berupa angka 0 sampai 100.';
    }
    if (mb_strlen($d['catatan']) > 500) { $err[] = 'Catatan maksimal 500 karakter.'; }
    return [$err, $d];
}

function ambil_master(mysqli $conn): array {
    return [
        $conn->query('SELECT id_lokasi, nama_lokasi FROM lokasi ORDER BY nama_lokasi')->fetch_all(MYSQLI_ASSOC),
        $conn->query('SELECT id_ekosistem, nama_ekosistem FROM ekosistem ORDER BY nama_ekosistem')->fetch_all(MYSQLI_ASSOC),
    ];
}

// Ambil satu pengamatan dan pastikan pengguna berhak mengubah/menghapusnya.
function ambil_pengamatan_milik(mysqli $conn, int $id): array {
    $stmt = $conn->prepare('SELECT * FROM pengamatan WHERE id_pengamatan = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if (!$row) { http_response_code(404); exit('Data tidak ditemukan.'); }
    // Admin boleh semua; petugas hanya data miliknya sendiri.
    if (!is_admin() && (int)$row['id_user'] !== (int)$_SESSION['id_user']) {
        http_response_code(403);
        exit('403 - Anda hanya boleh mengubah data milik Anda sendiri.');
    }
    return $row;
}
