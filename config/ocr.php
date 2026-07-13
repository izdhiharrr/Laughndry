<?php
/**
 * config/ocr.php — Konfigurasi & Utility OCR.space API
 * 
 * File ini memuat API Key OCR.space dari .env dan menyediakan fungsi utility
 * untuk melakukan OCR pembacaan gambar secara server-side via REST API cURL.
 */

// Load autoloader dan dotenv jika belum dimuat
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    
    if (file_exists(__DIR__ . '/../.env')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->safeLoad();
    }
}

// Definisikan API Key OCR.space (default ke 'helloworld' untuk testing instan)
define('OCR_SPACE_API_KEY', (getenv('OCR_SPACE_API_KEY') !== false) ? getenv('OCR_SPACE_API_KEY') : ($_ENV['OCR_SPACE_API_KEY'] ?? 'helloworld'));

/**
 * Melakukan scan OCR pada gambar menggunakan API OCR.space.
 * 
 * @param string $file_path Lokasi berkas gambar temporer di server (tmp_name).
 * @return string|false Hasil pembacaan teks (string) jika sukses, false jika gagal.
 */
function perform_ocr($file_path) {
    $api_key = OCR_SPACE_API_KEY;

    if (!function_exists('curl_init')) {
        error_log("OCR.space Upload Gagal: Ekstensi PHP cURL tidak aktif.");
        return false;
    }

    // Siapkan body multipart/form-data
    $post_fields = [
        'apikey' => $api_key,
        'file' => new CURLFile($file_path),
        'language' => 'eng',
        'isOverlayRequired' => 'false'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.ocr.space/parse/image");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Mencegah kegagalan SSL cert di local environment (XAMPP)

    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        error_log("OCR.space cURL Error: " . $err);
        return false;
    }

    $result = json_decode($response, true);
    
    // Cek apakah ada error dari API
    if (isset($result['IsErroredOnProcessing']) && $result['IsErroredOnProcessing'] === true) {
        $error_msg = $result['ErrorMessage'][0] ?? 'Terjadi kesalahan pemrosesan OCR.';
        error_log("OCR.space API Error: " . $error_msg);
        return false;
    }

    // Ambil hasil teks yang terbaca
    if (isset($result['ParsedResults'][0]['ParsedText'])) {
        return $result['ParsedResults'][0]['ParsedText'];
    }

    error_log("OCR.space Response: " . $response);
    return false;
}
