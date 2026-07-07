<?php
/**
 * config/midtrans.php — Konfigurasi Midtrans Payment Gateway
 * 
 * Mode: SANDBOX (untuk testing)
 * Dokumentasi: https://docs.midtrans.com
 */

// Toggle untuk mengaktifkan/menonaktifkan Midtrans (ubah ke true jika ingin mengaktifkan kembali)
define('MIDTRANS_ENABLED', false);


// Load autoloader dan dotenv jika tersedia
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    
    if (file_exists(__DIR__ . '/../.env')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->safeLoad();
    }
}

// ═══════════════════════════════════════════
// Midtrans Sandbox Credentials
// ═══════════════════════════════════════════
define('MIDTRANS_SERVER_KEY', (getenv('MIDTRANS_SERVER_KEY') !== false) ? getenv('MIDTRANS_SERVER_KEY') : ($_ENV['MIDTRANS_SERVER_KEY'] ?? 'Mid-server-placeholder'));
define('MIDTRANS_CLIENT_KEY', (getenv('MIDTRANS_CLIENT_KEY') !== false) ? getenv('MIDTRANS_CLIENT_KEY') : ($_ENV['MIDTRANS_CLIENT_KEY'] ?? 'Mid-client-placeholder'));
define('MIDTRANS_IS_PRODUCTION', filter_var((getenv('MIDTRANS_IS_PRODUCTION') !== false) ? getenv('MIDTRANS_IS_PRODUCTION') : ($_ENV['MIDTRANS_IS_PRODUCTION'] ?? false), FILTER_VALIDATE_BOOLEAN));

// URL Endpoints
define(
    'MIDTRANS_SNAP_URL',
    MIDTRANS_IS_PRODUCTION
    ? 'https://app.midtrans.com/snap/v1/transactions'
    : 'https://app.sandbox.midtrans.com/snap/v1/transactions'
);

define(
    'MIDTRANS_SNAP_JS_URL',
    MIDTRANS_IS_PRODUCTION
    ? 'https://app.midtrans.com/snap/snap.js'
    : 'https://app.sandbox.midtrans.com/snap/snap.js'
);

// API URL untuk verifikasi status transaksi
define(
    'MIDTRANS_API_URL',
    MIDTRANS_IS_PRODUCTION
    ? 'https://api.midtrans.com/v2'
    : 'https://api.sandbox.midtrans.com/v2'
);
