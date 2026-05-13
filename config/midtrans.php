<?php
/**
 * config/midtrans.php — Konfigurasi Midtrans Payment Gateway
 * 
 * Mode: SANDBOX (untuk testing)
 * Dokumentasi: https://docs.midtrans.com
 */

// ═══════════════════════════════════════════
// Midtrans Sandbox Credentials
// ═══════════════════════════════════════════
define('MIDTRANS_SERVER_KEY', 'MASUKKAN_SERVER_KEY_DISINI');
define('MIDTRANS_CLIENT_KEY', 'MASUKKAN_CLIENT_KEY_DISINI');
define('MIDTRANS_IS_PRODUCTION', false);

// URL Endpoints
define('MIDTRANS_SNAP_URL', MIDTRANS_IS_PRODUCTION 
    ? 'https://app.midtrans.com/snap/v1/transactions' 
    : 'https://app.sandbox.midtrans.com/snap/v1/transactions'
);

define('MIDTRANS_SNAP_JS_URL', MIDTRANS_IS_PRODUCTION 
    ? 'https://app.midtrans.com/snap/snap.js' 
    : 'https://app.sandbox.midtrans.com/snap/snap.js'
);

// API URL untuk verifikasi status transaksi
define('MIDTRANS_API_URL', MIDTRANS_IS_PRODUCTION 
    ? 'https://api.midtrans.com/v2' 
    : 'https://api.sandbox.midtrans.com/v2'
);
