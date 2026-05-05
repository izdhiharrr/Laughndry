<?php
/**
 * admin.php — Halaman Admin
 * Terhubung ke database laughndry_db untuk data real.
 */
session_start();
require_once __DIR__ . '/../config/database.php';

// ═══════════════════ HANDLE ACTIONS (POST) ═══════════════════

// --- Login ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['is_admin'] = true;
        $_SESSION['admin_name'] = $user['nama_lengkap'];
    } else {
        $login_error = "Username atau password salah!";
    }
}

// --- Logout ---
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// --- Update Status Pesanan ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $order_id]);
    header("Location: admin.php");
    exit;
}

// --- Delete Pesanan ---
if (isset($_GET['delete_order'])) {
    $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->execute([$_GET['delete_order']]);
    header("Location: admin.php");
    exit;
}

// --- Delete Pelanggan ---
if (isset($_GET['delete_customer'])) {
    $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
    $stmt->execute([$_GET['delete_customer']]);
    header("Location: admin.php");
    exit;
}

$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

// ═══════════════════ FETCH DATA ═══════════════════
if ($is_admin) {
    // Fetch pesanan dengan JOIN ke customers dan order_items
    $orders = $pdo->query("
        SELECT 
            o.id,
            c.nama AS customer_nama,
            c.id AS customer_id,
            o.total_harga,
            o.metode_bayar,
            o.status,
            o.created_at,
            GROUP_CONCAT(oi.kategori SEPARATOR ', ') AS kategori_list,
            GROUP_CONCAT(CONCAT(oi.nama_item, ' (x', oi.qty, ')') SEPARATOR ', ') AS item_list,
            SUM(oi.qty) AS total_qty
        FROM orders o
        JOIN customers c ON o.customer_id = c.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ")->fetchAll();

    // Fetch pelanggan
    $customers = $pdo->query("SELECT * FROM customers ORDER BY id ASC")->fetchAll();

    // Stats
    $total_orders = count($orders);
    $total_customers = count($customers);
    $total_revenue = $pdo->query("SELECT COALESCE(SUM(total_harga), 0) FROM orders")->fetchColumn();
    $pending_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Laughndry — Admin Panel</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <link href="../style.css" rel="stylesheet" />
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#00433a",
                        "on-primary": "#ffffff",
                        "on-secondary-fixed-variant": "#663e00",
                        "tertiary-container": "#7d4230",
                        "inverse-surface": "#2f3131",
                        "on-error-container": "#93000a",
                        "surface-bright": "#f9f9f9",
                        "on-surface": "#1a1c1c",
                        "inverse-on-surface": "#f0f1f1",
                        "on-tertiary-fixed-variant": "#6f3726",
                        "on-primary-fixed": "#00201b",
                        "secondary-fixed": "#ffddb9",
                        "secondary-fixed-dim": "#ffb962",
                        "on-primary-container": "#8bd3c4",
                        "tertiary": "#612c1b",
                        "on-tertiary-container": "#ffb49e",
                        "outline": "#6f7976",
                        "tertiary-fixed-dim": "#ffb59f",
                        "on-primary-fixed-variant": "#005046",
                        "primary-fixed-dim": "#8cd4c5",
                        "error": "#ba1a1a",
                        "primary-container": "#035d51",
                        "on-secondary-fixed": "#2b1700",
                        "primary-fixed": "#a7f1e0",
                        "surface-container": "#eeeeee",
                        "on-error": "#ffffff",
                        "error-container": "#ffdad6",
                        "on-background": "#1a1c1c",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-high": "#e8e8e8",
                        "on-tertiary": "#ffffff",
                        "surface-container-low": "#f3f3f3",
                        "surface-variant": "#e2e2e2",
                        "secondary": "#865300",
                        "on-secondary-container": "#6e4300",
                        "inverse-primary": "#8cd4c5",
                        "surface-tint": "#1b6a5d",
                        "background": "#f9f9f9",
                        "outline-variant": "#bec9c5",
                        "tertiary-fixed": "#ffdbd1",
                        "on-secondary": "#ffffff",
                        "surface-dim": "#dadada",
                        "on-tertiary-fixed": "#380d02",
                        "surface": "#f9f9f9",
                        "on-surface-variant": "#3f4946",
                        "secondary-container": "#fbad48",
                        "surface-container-highest": "#e2e2e2"
                    },
                    borderRadius: {
                        DEFAULT: "0.25rem",
                        lg: "0.5rem",
                        xl: "0.75rem",
                        full: "9999px"
                    }
                }
            }
        };
    </script>
</head>
<body class="bg-background text-on-surface antialiased">

<?php if (!$is_admin): ?>
    <!-- ======================= LOGIN ADMIN ======================= -->
    <section class="py-24 sm:py-32 bg-surface min-h-screen flex items-center justify-center px-4">
        <div class="bg-surface-container-lowest p-8 sm:p-12 rounded-[2.5rem] shadow-xl border border-outline-variant/20 max-w-md w-full relative overflow-hidden">
            <!-- Decorative element -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-primary-fixed rounded-bl-[4rem] -z-0 opacity-50"></div>
            
            <div class="relative z-10 text-center mb-8">
                <div class="w-20 h-20 bg-secondary-container rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-secondary-container/30">
                    <span class="material-symbols-outlined text-4xl text-on-secondary-fixed">admin_panel_settings</span>
                </div>
                <h1 class="text-3xl font-black text-primary mb-2">Login Admin</h1>
                <p class="text-on-surface-variant">Silakan login untuk mengelola pesanan.</p>
            </div>

            <?php if (isset($login_error)): ?>
                <div class="bg-error-container text-on-error-container p-4 rounded-xl mb-6 text-sm font-medium flex items-center gap-2">
                    <span class="material-symbols-outlined">error</span>
                    <?= $login_error ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="admin.php" class="relative z-10 flex flex-col gap-5">
                <div>
                    <label class="block text-sm font-bold text-primary mb-2">Username</label>
                    <input type="text" name="username" placeholder="Masukkan username" required
                        class="w-full bg-surface border-2 border-outline-variant/30 rounded-xl px-4 py-3 focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-bold text-primary mb-2">Password</label>
                    <input type="password" name="password" placeholder="Masukkan password" required
                        class="w-full bg-surface border-2 border-outline-variant/30 rounded-xl px-4 py-3 focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all">
                </div>
                <button type="submit" name="login"
                    class="w-full bg-primary text-on-primary py-3.5 rounded-full font-bold text-lg hover:scale-[1.02] active:scale-95 transition-all shadow-lg shadow-primary/20 mt-4">
                    Login
                </button>
                <div class="text-center text-xs text-on-surface-variant mt-2">
                    Gunakan <b>admin</b> / <b>admin123</b> untuk demo.
                </div>
            </form>
        </div>
    </section>

<?php else: ?>
    <!-- ======================= DASHBOARD ADMIN ======================= -->
    <section class="py-12 sm:py-20 bg-surface min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-8">
            
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 sm:mb-16 gap-4 border-b border-outline-variant/20 pb-8">
                <div>
                    <span class="text-secondary-container font-black tracking-[0.2em] text-sm mb-4 block">DASHBOARD</span>
                    <h1 class="text-3xl sm:text-4xl font-black text-primary">Admin Panel</h1>
                    <p class="text-on-surface-variant mt-2 max-w-xl">Selamat datang, <b><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></b>. Kelola pesanan dan data pelanggan Laughndry.</p>
                </div>
                <a href="?logout=true" class="inline-flex items-center justify-center gap-2 bg-error-container text-on-error-container px-6 py-3 rounded-full font-bold hover:bg-error hover:text-on-error transition-colors">
                    <span class="material-symbols-outlined text-xl">logout</span> Logout
                </a>
            </div>

            <!-- ═══════════ STAT CARDS ═══════════ -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-12">
                <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-outline-variant/10">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-primary-fixed rounded-full flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary text-xl">receipt_long</span>
                        </div>
                        <span class="text-sm text-on-surface-variant font-medium">Total Pesanan</span>
                    </div>
                    <p class="text-2xl font-black text-primary"><?= $total_orders ?></p>
                </div>
                <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-outline-variant/10">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-secondary-fixed rounded-full flex items-center justify-center">
                            <span class="material-symbols-outlined text-secondary text-xl">pending_actions</span>
                        </div>
                        <span class="text-sm text-on-surface-variant font-medium">Pending</span>
                    </div>
                    <p class="text-2xl font-black text-secondary"><?= $pending_orders ?></p>
                </div>
                <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-outline-variant/10">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-primary-fixed rounded-full flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary text-xl">group</span>
                        </div>
                        <span class="text-sm text-on-surface-variant font-medium">Pelanggan</span>
                    </div>
                    <p class="text-2xl font-black text-primary"><?= $total_customers ?></p>
                </div>
                <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-outline-variant/10">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-secondary-fixed rounded-full flex items-center justify-center">
                            <span class="material-symbols-outlined text-secondary text-xl">payments</span>
                        </div>
                        <span class="text-sm text-on-surface-variant font-medium">Total Revenue</span>
                    </div>
                    <p class="text-2xl font-black text-secondary">Rp <?= number_format($total_revenue, 0, ',', '.') ?></p>
                </div>
            </div>

            <!-- ═══════════ PESANAN TABLE ═══════════ -->
            <div class="mb-16">
                <div class="text-center mb-8">
                    <h2 class="text-2xl sm:text-3xl font-black text-primary mb-2">Pesanan</h2>
                    <p class="text-on-surface-variant">Daftar pesanan dari pelanggan.</p>
                </div>
                
                <div class="bg-surface-container-lowest rounded-[2rem] p-6 sm:p-8 shadow-md border border-outline-variant/10">
                    <!-- Table -->
                    <div class="overflow-x-auto rounded-xl border border-outline-variant/20">
                        <table class="w-full text-left border-collapse min-w-[900px]">
                            <thead>
                                <tr class="bg-surface-container text-primary border-b-2 border-outline-variant/30 text-sm">
                                    <th class="p-4 font-bold whitespace-nowrap">ID</th>
                                    <th class="p-4 font-bold whitespace-nowrap">Pelanggan</th>
                                    <th class="p-4 font-bold whitespace-nowrap">Jenis Layanan</th>
                                    <th class="p-4 font-bold whitespace-nowrap">Detail Item</th>
                                    <th class="p-4 font-bold whitespace-nowrap">Qty</th>
                                    <th class="p-4 font-bold whitespace-nowrap">Harga Total</th>
                                    <th class="p-4 font-bold whitespace-nowrap">Metode Bayar</th>
                                    <th class="p-4 font-bold whitespace-nowrap">Status</th>
                                    <th class="p-4 font-bold whitespace-nowrap text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/20">
                                <?php if (empty($orders)): ?>
                                    <tr>
                                        <td colspan="9" class="p-8 text-center text-on-surface-variant bg-surface-container-low/30 italic">
                                            Belum ada pesanan.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($orders as $order): ?>
                                        <?php
                                            // Status badge colors
                                            $status_colors = [
                                                'pending'   => 'bg-yellow-100 text-yellow-800',
                                                'diproses'  => 'bg-blue-100 text-blue-800',
                                                'dicuci'    => 'bg-cyan-100 text-cyan-800',
                                                'selesai'   => 'bg-green-100 text-green-800',
                                                'diambil'   => 'bg-gray-100 text-gray-600',
                                            ];
                                            $badge = $status_colors[$order['status']] ?? 'bg-gray-100 text-gray-600';

                                            // Metode bayar badge
                                            $metode_icons = [
                                                'qris'     => '🔲 QRIS',
                                                'transfer' => '🏦 Transfer',
                                                'tunai'    => '💵 Tunai',
                                            ];
                                            $metode_label = $metode_icons[$order['metode_bayar']] ?? $order['metode_bayar'];
                                        ?>
                                        <tr class="hover:bg-surface-container-low transition-colors">
                                            <td class="p-4 font-bold text-primary">#<?= $order['id'] ?></td>
                                            <td class="p-4 font-medium text-primary"><?= htmlspecialchars($order['customer_nama']) ?></td>
                                            <td class="p-4 text-on-surface-variant text-sm"><?= htmlspecialchars($order['kategori_list'] ?? '-') ?></td>
                                            <td class="p-4 text-on-surface-variant text-sm max-w-[200px]"><?= htmlspecialchars($order['item_list'] ?? '-') ?></td>
                                            <td class="p-4 text-on-surface-variant font-medium"><?= $order['total_qty'] ?? 0 ?></td>
                                            <td class="p-4 font-bold text-secondary">Rp <?= number_format($order['total_harga'], 0, ',', '.') ?></td>
                                            <td class="p-4 text-sm"><?= $metode_label ?></td>
                                            <td class="p-4">
                                                <!-- Status Update Form -->
                                                <form method="POST" action="admin.php" class="inline">
                                                    <input type="hidden" name="update_status" value="1">
                                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                    <select name="new_status" onchange="this.form.submit()"
                                                        class="text-xs font-bold px-3 py-1.5 rounded-full border-0 cursor-pointer <?= $badge ?> focus:ring-2 focus:ring-primary/20">
                                                        <?php foreach (['pending', 'diproses', 'dicuci', 'selesai', 'diambil'] as $s): ?>
                                                            <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>>
                                                                <?= ucfirst($s) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </form>
                                            </td>
                                            <td class="p-4 text-center">
                                                <a href="?delete_order=<?= $order['id'] ?>"
                                                    onclick="return confirm('Yakin hapus pesanan #<?= $order['id'] ?>?')"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-bold bg-surface border-2 border-outline-variant/30 text-on-surface-variant rounded-lg hover:bg-error hover:text-on-error hover:border-error transition-colors">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Info -->
                    <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4 text-sm text-on-surface-variant">
                        <div>Menampilkan <?= count($orders) ?> pesanan</div>
                    </div>
                </div>
            </div>

            <!-- ═══════════ PROFIL PELANGGAN TABLE ═══════════ -->
            <div>
                <div class="text-center mb-8">
                    <h2 class="text-2xl sm:text-3xl font-black text-primary mb-2">Profil Pelanggan</h2>
                    <p class="text-on-surface-variant">Daftar pelanggan yang terdaftar.</p>
                </div>
                
                <div class="bg-surface-container-lowest rounded-[2rem] p-6 sm:p-8 shadow-md border border-outline-variant/10">
                    <!-- Table -->
                    <div class="overflow-x-auto rounded-xl border border-outline-variant/20">
                        <table class="w-full text-left border-collapse min-w-[800px]">
                            <thead>
                                <tr class="bg-surface-container text-primary border-b-2 border-outline-variant/30 text-sm">
                                    <th class="p-4 font-bold whitespace-nowrap w-24">ID</th>
                                    <th class="p-4 font-bold whitespace-nowrap">Nama</th>
                                    <th class="p-4 font-bold whitespace-nowrap">Alamat</th>
                                    <th class="p-4 font-bold whitespace-nowrap">Nomor Telepon</th>
                                    <th class="p-4 font-bold whitespace-nowrap">Terdaftar</th>
                                    <th class="p-4 font-bold text-center w-24">Delete</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/20">
                                <?php if (empty($customers)): ?>
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-on-surface-variant bg-surface-container-low/30 italic">
                                            Belum ada pelanggan terdaftar.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($customers as $cust): ?>
                                        <tr class="hover:bg-surface-container-low transition-colors group">
                                            <td class="p-4 text-on-surface-variant font-medium"><?= $cust['id'] ?></td>
                                            <td class="p-4 font-bold text-primary"><?= htmlspecialchars($cust['nama']) ?></td>
                                            <td class="p-4 text-on-surface-variant"><?= htmlspecialchars($cust['alamat']) ?></td>
                                            <td class="p-4 text-on-surface-variant"><?= htmlspecialchars($cust['telepon']) ?></td>
                                            <td class="p-4 text-on-surface-variant text-sm"><?= date('d M Y', strtotime($cust['created_at'])) ?></td>
                                            <td class="p-4 text-center">
                                                <a href="?delete_customer=<?= $cust['id'] ?>"
                                                    onclick="return confirm('Yakin hapus pelanggan <?= htmlspecialchars($cust['nama']) ?>? Semua pesanannya juga akan terhapus.')"
                                                    class="px-4 py-2 text-sm font-bold bg-surface border-2 border-outline-variant/30 text-on-surface-variant rounded-lg hover:bg-error hover:text-on-error hover:border-error transition-colors inline-flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-base">delete</span> Delete
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Info -->
                    <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4 text-sm text-on-surface-variant">
                        <div>Menampilkan <?= count($customers) ?> pelanggan</div>
                    </div>
                </div>
            </div>

        </div>
    </section>
<?php endif; ?>

</body>
</html>
