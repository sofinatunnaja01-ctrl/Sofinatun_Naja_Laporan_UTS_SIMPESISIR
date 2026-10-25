<?php
// Dimuat di awal SETIAP halaman: koneksi, session, helper, dan penjaga akses.
require_once __DIR__ . '/../config/koneksi.php';
date_default_timezone_set('Asia/Jakarta');

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax',
    ]);
    session_start();
}

// Error DB/tak terduga: catat di log, tampilkan pesan umum saja.
set_exception_handler(function ($ex) {
    error_log('Exception: ' . $ex->getMessage());
    http_response_code(500);
    exit('Terjadi kesalahan pada sistem. Silakan coba lagi nanti.');
});

function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function url(string $p = ''): string { return BASE_URL . '/' . ltrim($p, '/'); }
function redirect(string $p) { header('Location: ' . url($p)); exit; }
function flash(string $type, string $msg) { $_SESSION['flash'] = ['type' => $type, 'msg' => $msg]; }

// ---- CSRF ----
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(32)); }
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">'; }
function csrf_check() {
    $ok = $_SERVER['REQUEST_METHOD'] === 'POST'
        && isset($_POST['csrf'], $_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], (string)$_POST['csrf']);
    if (!$ok) { http_response_code(403); exit('Permintaan tidak valid (token CSRF salah).'); }
}

// ---- Autentikasi & otorisasi (SISI SERVER) ----
function is_logged_in(): bool { return !empty($_SESSION['id_user']); }

function require_login() {
    global $conn;
    if (!is_logged_in()) { flash('error', 'Silakan login terlebih dahulu.'); redirect('login.php'); }
    // Ambil ulang peran dari database; jangan percaya data dari klien.
    $stmt = $conn->prepare('SELECT nama, role FROM users WHERE id_user = ?');
    $stmt->bind_param('i', $_SESSION['id_user']);
    $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();
    if (!$u) { // akun sudah dihapus
        $_SESSION = []; session_destroy();
        session_start(); flash('error', 'Sesi tidak valid. Silakan login kembali.');
        redirect('login.php');
    }
    $_SESSION['nama'] = $u['nama'];
    $_SESSION['role'] = $u['role'];
}

function require_role(string $role) {
    require_login();
    if ($_SESSION['role'] !== $role) {
        http_response_code(403);
        exit('403 - Anda tidak berhak mengakses halaman ini.');
    }
}

function is_admin(): bool { return ($_SESSION['role'] ?? '') === 'admin'; }
