<?php
/**
 * api/confirm_payment.php — Konfirmasi pembayaran manual dari pelanggan (QRIS) dengan bukti bayar
 * 
 * Dipanggil ketika pelanggan mengklik tombol "Saya Sudah Bayar" pada halaman instruksi QRIS.
 * Mengunggah bukti pembayaran, lalu memperbarui status pesanan menjadi 'menunggu verifikasi'.
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cloudinary.php';

// Menerima data via $_POST karena dikirim menggunakan FormData (multipart/form-data)
if (!isset($_POST['order_id']) || empty($_POST['order_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Order ID tidak valid']);
    exit;
}

$order_id = intval($_POST['order_id']);

// Validasi berkas bukti_bayar
if (!isset($_FILES['bukti_bayar']) || $_FILES['bukti_bayar']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Silakan lampirkan foto bukti pembayaran yang valid']);
    exit;
}

$file = $_FILES['bukti_bayar'];

// Validasi ukuran berkas (maks 2MB)
if ($file['size'] > 2 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ukuran berkas terlalu besar. Maksimal 2MB.']);
    exit;
}

// Validasi format file gambar
$allowed_extensions = ['jpg', 'jpeg', 'png'];
$allowed_mime_types = ['image/jpeg', 'image/png', 'image/x-png', 'image/pjpeg'];

$file_info = pathinfo($file['name']);
$extension = strtolower($file_info['extension'] ?? '');

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($extension, $allowed_extensions) || !in_array($mime_type, $allowed_mime_types)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Format berkas tidak valid. Harap unggah gambar JPG atau PNG']);
    exit;
}

try {
    // Pastikan pesanan tersebut ada
    $stmt = $pdo->prepare("SELECT id, status FROM `order` WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();

    if (!$order) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Pesanan tidak ditemukan']);
        exit;
    }

    // Persiapkan direktori penyimpanan
    $upload_dir = __DIR__ . '/../uploads/bukti_pembayaran/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Hitung MD5 hash dari berkas bukti bayar untuk mencegah unggahan berulang (replay attack)
    $file_hash = md5_file($file['tmp_name']);

    // Cek apakah hash ini sudah pernah diunggah untuk pesanan lain
    // Pengecualian sementara untuk gambar testing Faris (Rp 4) agar bisa digunakan berulang kali
    if ($file_hash !== '4fc66abe846842e02ae2f4052372ffff') {
        $stmt = $pdo->prepare("SELECT id FROM `order` WHERE bukti_bayar_hash = ? AND id != ?");
        $stmt->execute([$file_hash, $order_id]);
        $duplicate = $stmt->fetch();

        if ($duplicate) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Bukti pembayaran ini sudah pernah digunakan untuk pesanan lain. Harap unggah resi transfer yang sah. Jika Anda bermaksud melakukan pembayaran gabungan, silakan hubungi kami via WhatsApp untuk verifikasi manual.']);
            exit;
        }
    }

    // Inisialisasi variabel bukti bayar
    $bukti_bayar_value = null;
    $uploaded = false;

    // Proses unggah ke Cloudinary jika diaktifkan
    if (CLOUDINARY_ENABLED) {
        $cloudinary_url = false;
        
        // Coba kompresi gambar secara lokal terlebih dahulu untuk menghemat bandwidth
        if (extension_loaded('gd') && function_exists('imagecreatefromjpeg')) {
            $temp_filename = 'temp_proof_' . $order_id . '_' . bin2hex(random_bytes(8)) . '.jpg';
            $temp_destination = $upload_dir . $temp_filename;
            
            if (compressAndSaveImage($file['tmp_name'], $temp_destination, $extension)) {
                $cloudinary_url = upload_to_cloudinary($temp_destination);
                @unlink($temp_destination);
            }
        }
        
        // Jika kompresi dinonaktifkan atau gagal, upload berkas asli
        if (!$cloudinary_url) {
            $cloudinary_url = upload_to_cloudinary($file['tmp_name']);
        }
        
        if ($cloudinary_url) {
            $bukti_bayar_value = $cloudinary_url;
            $uploaded = true;
        }
    }

    // Fallback: Jika Cloudinary dinonaktifkan atau upload gagal, simpan secara lokal
    if (!$uploaded) {
        $save_extension = (extension_loaded('gd') && function_exists('imagecreatefromjpeg')) ? 'jpg' : $extension;
        $new_filename = 'proof_' . $order_id . '_' . bin2hex(random_bytes(8)) . '.' . $save_extension;
        $destination = $upload_dir . $new_filename;

        if (extension_loaded('gd') && function_exists('imagecreatefromjpeg')) {
            $uploaded = compressAndSaveImage($file['tmp_name'], $destination, $extension);
        } else {
            $uploaded = move_uploaded_file($file['tmp_name'], $destination);
        }

        if (!$uploaded) {
            throw new Exception('Gagal menyimpan file di server. Pastikan format gambar valid.');
        }
        
        $bukti_bayar_value = $new_filename;
    }

    // Update status, payment_status, bukti_bayar, dan bukti_bayar_hash
    $stmt = $pdo->prepare("
        UPDATE `order` 
        SET status = 'menunggu verifikasi', 
            payment_status = 'Menunggu Verifikasi', 
            bukti_bayar = ?, 
            bukti_bayar_hash = ?,
            updated_at = NOW() 
        WHERE id = ?
    ");
    $stmt->execute([$bukti_bayar_value, $file_hash, $order_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Konfirmasi pembayaran berhasil dikirim. Bukti pembayaran telah diunggah.',
        'order_id' => $order_id,
        'bukti_bayar' => $bukti_bayar_value
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Gagal mengonfirmasi pembayaran: ' . $e->getMessage()
    ]);
}

/**
 * Kompresi gambar bukti transfer menggunakan GD library
 * Mengubah resolusi maks lebar 800px dan kompresi JPEG quality 70.
 */
function compressAndSaveImage($source_path, $dest_path, $extension) {
    list($width, $height) = @getimagesize($source_path);
    if (!$width || !$height) {
        return false;
    }

    // Buat resource gambar berdasarkan ekstensi asli
    $source_image = null;
    if ($extension === 'png') {
        $source_image = @imagecreatefrompng($source_path);
    } else {
        $source_image = @imagecreatefromjpeg($source_path);
    }

    if (!$source_image) {
        return false;
    }

    // Hitung ukuran baru (maks lebar 800px untuk menghemat kapasitas)
    $max_width = 800;
    if ($width > $max_width) {
        $new_width = $max_width;
        $new_height = floor($height * ($max_width / $width));
    } else {
        $new_width = $width;
        $new_height = $height;
    }

    // Buat canvas baru
    $virtual_image = imagecreatetruecolor($new_width, $new_height);
    
    // Resize gambar asli ke canvas baru
    imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    // Simpan sebagai JPEG terkompresi (kualitas 70)
    $result = imagejpeg($virtual_image, $dest_path, 70);

    // Hancurkan resource memori
    imagedestroy($source_image);
    imagedestroy($virtual_image);

    return $result;
}
?>
