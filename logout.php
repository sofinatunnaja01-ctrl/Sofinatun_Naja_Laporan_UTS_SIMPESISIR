<?php
require_once __DIR__ . '/includes/init.php';
csrf_check();                     // logout hanya lewat POST + token CSRF

$_SESSION = [];                   // kosongkan data session
session_destroy();                // akhiri sesi lama
session_start();                  // mulai sesi baru yang bersih
session_regenerate_id(true);      // beri ID baru + kirim cookie baru

flash('ok', 'Anda telah logout.');
redirect('login.php');