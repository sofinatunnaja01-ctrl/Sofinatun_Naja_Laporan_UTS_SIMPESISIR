<?php
// $id_abaikan = id user yang sedang diedit (agar cek username unik tidak bentrok dengan dirinya sendiri)
function validasi_user(mysqli $conn, array $in, bool $wajib_password, int $id_abaikan = 0): array {
    $d = [
        'nama'     => trim($in['nama'] ?? ''),
        'username' => trim($in['username'] ?? ''),
        'role'     => trim($in['role'] ?? ''),
    ];
    $pass  = (string)($in['password'] ?? '');
    $pass2 = (string)($in['password2'] ?? '');
    $err = [];

    if (mb_strlen($d['nama']) < 3 || mb_strlen($d['nama']) > 100) { $err[] = 'Nama 3-100 karakter.'; }
    if (!preg_match('/^[A-Za-z0-9_]{4,30}$/', $d['username'])) { $err[] = 'Username 4-30 karakter (huruf, angka, underscore).'; }
    if (!in_array($d['role'], ['admin', 'petugas'], true)) { $err[] = 'Peran tidak valid.'; }

    if ($wajib_password || $pass !== '') {
        if (strlen($pass) < 8 || !preg_match('/[A-Za-z]/', $pass) || !preg_match('/\d/', $pass)) {
            $err[] = 'Password minimal 8 karakter dan mengandung huruf serta angka.';
        }
        if ($pass !== $pass2) { $err[] = 'Konfirmasi password tidak sama.'; }
    }
    if (!$err) {
        $stmt = $conn->prepare('SELECT 1 FROM users WHERE username = ? AND id_user <> ?');
        $stmt->bind_param('si', $d['username'], $id_abaikan);
        $stmt->execute();
        if ($stmt->get_result()->fetch_row()) { $err[] = 'Username sudah dipakai.'; }
    }
    return [$err, $d, $pass];
}
