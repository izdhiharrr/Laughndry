<?php
/**
 * config/midtrans.php — Konfigurasi Midtrans Payment Gateway
 * 
 * Mode: SANDBOX (untuk testing)
 * Dokumentasi: https://docs.midtrans.com
 */

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
define('MIDTRANS_SERVER_KEY', $_ENV['MIDTRANS_SERVER_KEY'] ?? 'Mid-server-placeholder');
define('MIDTRANS_CLIENT_KEY', $_ENV['MIDTRANS_CLIENT_KEY'] ?? 'Mid-client-placeholder');
define('MIDTRANS_IS_PRODUCTION', filter_var($_ENV['MIDTRANS_IS_PRODUCTION'] ?? false, FILTER_VALIDATE_BOOLEAN));

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
