<?php
// Konfigurasi aplikasi & database. Sesuaikan dengan lingkungan lokal Anda.
define('BASE_URL', '/simpesisir');   // nama folder di htdocs
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');               // XAMPP default kosong
define('DB_NAME', 'db_simpesisir');

ini_set('display_errors', '0');      // jangan tampilkan error ke pengguna
ini_set('log_errors', '1');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $ex) {
    error_log('Koneksi DB gagal: ' . $ex->getMessage());
    http_response_code(500);
    exit('Terjadi kesalahan pada sistem. Silakan coba lagi nanti.');
}
