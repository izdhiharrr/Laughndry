<?php
/**
 * config/cloudinary.php — Konfigurasi Cloudinary Media Storage
 * 
 * File ini memuat kredensial Cloudinary dari .env dan menyediakan fungsi utility
 * untuk mengunggah berkas gambar langsung ke Cloudinary menggunakan API REST.
 */

// Load autoloader dan dotenv jika belum dimuat
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    
    if (file_exists(__DIR__ . '/../.env')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->safeLoad();
    }
}

define('CLOUDINARY_CLOUD_NAME', (getenv('CLOUDINARY_CLOUD_NAME') !== false) ? getenv('CLOUDINARY_CLOUD_NAME') : ($_ENV['CLOUDINARY_CLOUD_NAME'] ?? ''));
define('CLOUDINARY_API_KEY', (getenv('CLOUDINARY_API_KEY') !== false) ? getenv('CLOUDINARY_API_KEY') : ($_ENV['CLOUDINARY_API_KEY'] ?? ''));
define('CLOUDINARY_API_SECRET', (getenv('CLOUDINARY_API_SECRET') !== false) ? getenv('CLOUDINARY_API_SECRET') : ($_ENV['CLOUDINARY_API_SECRET'] ?? ''));

// Toggle apakah Cloudinary aktif berdasarkan kelengkapan kredensial
define('CLOUDINARY_ENABLED', !empty(CLOUDINARY_CLOUD_NAME) && !empty(CLOUDINARY_API_KEY) && !empty(CLOUDINARY_API_SECRET));

/**
 * Mengunggah berkas gambar ke Cloudinary via REST API cURL (Signed Upload).
 * 
 * @param string $file_path Lokasi berkas temporer di server (tmp_name).
 * @return string|false URL aman (secure_url) dari Cloudinary jika sukses, false jika gagal.
 */
function upload_to_cloudinary($file_path) {
    if (!CLOUDINARY_ENABLED) {
        return false;
    }

    $cloud_name = CLOUDINARY_CLOUD_NAME;
    $api_key = CLOUDINARY_API_KEY;
    $api_secret = CLOUDINARY_API_SECRET;
    $timestamp = time();
    $folder = 'laughndry';

    // Parameter yang akan ditandatangani (diurutkan alfabetis)
    $params = [
        'folder' => $folder,
        'timestamp' => $timestamp
    ];

    ksort($params);

    // Bangun signature string: "folder=laughndry&timestamp=xxxx"
    $sign_string = "";
    foreach ($params as $key => $value) {
        $sign_string .= "$key=$value&";
    }
    $sign_string = rtrim($sign_string, '&') . $api_secret;

    // Hitung SHA-1 signature
    $signature = sha1($sign_string);

    if (!function_exists('curl_init')) {
        error_log("Cloudinary Upload Gagal: Ekstensi PHP cURL tidak aktif.");
        return false;
    }

    // Siapkan body multipart/form-data
    $post_fields = [
        'file' => new CURLFile($file_path),
        'api_key' => $api_key,
        'timestamp' => $timestamp,
        'signature' => $signature,
        'folder' => $folder
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/{$cloud_name}/image/upload");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Mengantisipasi kegagalan SSL cert di local environment (XAMPP)

    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        error_log("Cloudinary Upload cURL Error: " . $err);
        return false;
    }

    $result = json_decode($response, true);
    if (isset($result['error'])) {
        error_log("Cloudinary Upload API Error: " . $result['error']['message']);
        return false;
    }

    return $result['secure_url'] ?? false;
}
