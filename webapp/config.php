<?php
// ============================================================
// SmashBite — konfiguratsiya fayli
// Railway'da MUHIT O'ZGARUVCHILARI (environment variables) orqali,
// Ubuntu/lokal serverda esa quyidagi standart qiymatlar orqali ishlaydi.
// ============================================================

define('DB_HOST', getenv('MYSQLHOST') ?: 'localhost');
define('DB_NAME', getenv('MYSQLDATABASE') ?: 'smashbite');
define('DB_USER', getenv('MYSQLUSER') ?: 'smashbite_user');
define('DB_PASS', getenv('MYSQLPASSWORD') ?: 'BU_YERGA_KUCHLI_PAROL_QOYING');
define('DB_PORT', getenv('MYSQLPORT') ?: '3306');
define('DB_CHARSET', 'utf8mb4');

define('SESSION_NAME', 'smashbite_session');

// --- To'lov sozlamalari (hozircha placeholder) ---
define('PAYMENT_MODE', 'manual');
