<?php
if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'laughndry-production-6a17.up.railway.app') {
    header("Location: https://laughndry.my.id" . $_SERVER['REQUEST_URI'], true, 301);
    exit;
}
/**
 * admin.php — Halaman Admin
 * Terhubung ke database laughndry_db untuk data real.
 */
session_start();
require_once __DIR__ . '/../config/database.php';

// Helper function to format phone number for WhatsApp
function formatWaNumber($phone) {
    // Remove all non-numeric characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    // If it starts with 0, replace with 62
    if (strpos($phone, '0') === 0) {
        $phone = '62' . substr($phone, 1);
    }
    return $phone;
}

// ═══════════════════ HANDLE ACTIONS (POST) ═══════════════════

// --- Login ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['is_admin'] = true;
        $_SESSION['admin_name'] = $user['nama_lengkap'];
        $_SESSION['admin_id'] = $user['id'];
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
    $admin_id = $_SESSION['admin_id'] ?? null;
    
    // Update status pesanan
    $stmt = $pdo->prepare("UPDATE `order` SET status = ?, user_id = ? WHERE id = ?");
    $stmt->execute([$new_status, $admin_id, $order_id]);
    
    // Jika pembayaran tunai dan status pesanan diubah ke proses pengerjaan, set status bayar otomatis ke 'Paid'
    if (!in_array($new_status, ['pending', 'menunggu verifikasi', 'ditolak'])) {
        $stmt = $pdo->prepare("
            UPDATE `order` 
            SET payment_status = 'Paid' 
            WHERE id = ? AND metode_bayar = 'tunai' AND (payment_status IS NULL OR payment_status = 'Pending' OR payment_status = '')
        ");
        $stmt->execute([$order_id]);
    }
    
    header("Location: admin.php#pesanan");
    exit;
}

// --- Konfirmasi Pembayaran QRIS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    $order_id = intval($_POST['order_id']);
    $admin_id = $_SESSION['admin_id'] ?? null;
    $stmt = $pdo->prepare("UPDATE `order` SET payment_status = 'Paid', status = 'diproses', user_id = ? WHERE id = ?");
    $stmt->execute([$admin_id, $order_id]);
    header("Location: admin.php#pesanan");
    exit;
}

// --- Tolak Pembayaran QRIS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_payment'])) {
    $order_id = intval($_POST['order_id']);
    $admin_id = $_SESSION['admin_id'] ?? null;
    // Set status pembayaran ke 'Ditolak' dan status pesanan ke 'ditolak'
    $stmt = $pdo->prepare("UPDATE `order` SET payment_status = 'Ditolak', status = 'ditolak', user_id = ? WHERE id = ?");
    $stmt->execute([$admin_id, $order_id]);
    header("Location: admin.php#pesanan");
    exit;
}

// --- Delete Pesanan ---
if (isset($_GET['delete_order'])) {
    $stmt = $pdo->prepare("DELETE FROM `order` WHERE id = ?");
    $stmt->execute([$_GET['delete_order']]);
    if (isset($_GET['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
    header("Location: admin.php");
    exit;
}

// --- Delete Pelanggan ---
if (isset($_GET['delete_customer'])) {
    $stmt = $pdo->prepare("DELETE FROM customer WHERE id = ?");
    $stmt->execute([$_GET['delete_customer']]);
    if (isset($_GET['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
    header("Location: admin.php");
    exit;
}

$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

// ═══════════════════ FETCH DATA ═══════════════════
if ($is_admin) {
    // Fetch pesanan dengan JOIN ke customer dan order_item
    $orders = $pdo->query("
        SELECT 
            o.id,
            c.nama AS customer_nama,
            c.id AS customer_id,
            c.telepon AS customer_telepon,
            c.alamat AS customer_alamat,
            o.total_harga,
            o.metode_bayar,
            o.status,
            o.payment_status,
            o.bukti_bayar,
            o.created_at,
            GROUP_CONCAT(DISTINCT oi.kategori SEPARATOR ', ') AS kategori_list,
            GROUP_CONCAT(CONCAT(oi.nama_item, ' (x', oi.qty, ')') SEPARATOR ', ') AS item_list,
            SUM(oi.qty) AS total_qty
        FROM `order` o
        JOIN customer c ON o.customer_id = c.id
        LEFT JOIN order_item oi ON o.id = oi.order_id
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ")->fetchAll();

    // Fetch pelanggan dengan status pesanan terbaru
    $customers = $pdo->query("
        SELECT 
            c.*,
            (SELECT o.status FROM `order` o WHERE o.customer_id = c.id ORDER BY o.created_at DESC LIMIT 1) AS latest_order_status
        FROM customer c
        ORDER BY c.id ASC
    ")->fetchAll();

    // Stats
    $total_orders = count($orders);
    $total_customers = count($customers);
    $total_revenue = $pdo->query("SELECT COALESCE(SUM(total_harga), 0) FROM `order`")->fetchColumn();
    $pending_orders = $pdo->query("SELECT COUNT(*) FROM `order` WHERE status = 'pending'")->fetchColumn();
    $max_order_id = (int) $pdo->query("SELECT COALESCE(MAX(id), 0) FROM `order`")->fetchColumn();

    // Chart data: 4 minggu terakhir
    $chart_data = $pdo->query("
        SELECT 
            YEARWEEK(created_at, 1) AS minggu,
            MIN(DATE(created_at)) AS start_date,
            COUNT(*) AS total_pesanan,
            COALESCE(SUM(total_harga), 0) AS total_revenue
        FROM `order`
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 4 WEEK)
        GROUP BY YEARWEEK(created_at, 1)
        ORDER BY minggu ASC
    ")->fetchAll();

    $chart_labels = [];
    $chart_orders = [];
    $chart_revenue = [];
    foreach ($chart_data as $row) {
        $chart_labels[] = date('d M', strtotime($row['start_date']));
        $chart_orders[] = (int) $row['total_pesanan'];
        $chart_revenue[] = (int) $row['total_revenue'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Laughndry — Admin Panel</title>
    <link rel="icon" type="image/png" href="../assets/gambar/LOGO.png?v=2" />
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link href="../style.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
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
    <!-- DataTables Tailwind CSS Styling -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.tailwindcss.css" />
    <style>
        /* --- CUSTOM DATATABLES PRETTIER OVERRIDES --- */
        /* Search Input Styling */
        .dt-search {
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            font-weight: 600 !important;
            color: #00433a !important; /* Primary Color */
        }
        .dt-search input {
            background-color: #ffffff !important;
            border: 2px solid rgba(111, 121, 118, 0.2) !important;
            border-radius: 0.75rem !important;
            padding: 0.375rem 1rem !important;
            font-size: 0.875rem !important;
            color: #1a1c1c !important;
            outline: none !important;
            transition: all 0.2s ease-in-out !important;
            width: 200px !important;
        }
        .dt-search input:focus {
            border-color: #00433a !important;
            box-shadow: 0 0 0 4px rgba(0, 67, 58, 0.1) !important;
        }

        /* Length Select Dropdown Styling */
        .dt-length {
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
            font-weight: 600 !important;
            color: #00433a !important; /* Primary Color */
        }
        .dt-length select {
            background-color: #ffffff !important;
            border: 2px solid rgba(111, 121, 118, 0.2) !important;
            border-radius: 0.75rem !important;
            padding: 0.375rem 2rem 0.375rem 0.75rem !important;
            font-size: 0.875rem !important;
            color: #1a1c1c !important;
            font-weight: 600 !important;
            outline: none !important;
            transition: all 0.2s ease-in-out !important;
            cursor: pointer !important;
            width: auto !important;
            min-width: 80px !important;
        }
        .dt-length select:focus {
            border-color: #00433a !important;
            box-shadow: 0 0 0 4px rgba(0, 67, 58, 0.1) !important;
        }

        /* Pagination Buttons Styling */
        .dt-paging {
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.25rem !important;
        }
        .dt-paging-button {
            border-radius: 0.5rem !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
            transition: all 0.2s ease !important;
            border: 2px solid transparent !important;
            padding: 0.375rem 0.75rem !important;
        }
        .dt-paging-button.current {
            background-color: #00433a !important;
            color: #ffffff !important;
            border-color: #00433a !important;
        }
        .dt-paging-button:hover:not(.current):not(.disabled) {
            background-color: rgba(0, 67, 58, 0.05) !important;
            color: #00433a !important;
            border-color: rgba(0, 67, 58, 0.2) !important;
        }
        .dt-paging-button.disabled {
            opacity: 0.5 !important;
            cursor: not-allowed !important;
        }

        /* Table Header Styles */
        .dt-scroll-head thead th,
        #orders-table thead th,
        #customers-table thead th {
            background-color: #00433a !important; /* primary green */
            color: #ffffff !important; /* white text */
            border-bottom: 2px solid #00322b !important;
            font-weight: 700 !important;
        }

        /* Style the DataTables native scroll container */
        .dt-scroll {
            border-radius: 0.75rem !important;
            border: 2px solid rgba(111, 121, 118, 0.15) !important;
            background-color: #ffffff !important;
            margin: 1.5rem 0 !important;
            overflow: hidden !important;
        }
        .dt-scroll-body {
            overflow-x: auto !important;
            width: 100% !important;
        }
        
        /* Compact but premium padding for both tables */
        #orders-table th,
        #orders-table td,
        #customers-table th,
        #customers-table td,
        .bg-surface-container-lowest table th,
        .bg-surface-container-lowest table td {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
            padding-top: 1rem !important;
            padding-bottom: 1rem !important;
        }
        
        /* Ensure table doesn't have border inside it that clashes */
        #orders-table,
        #customers-table {
            border-bottom: none !important;
        }
        
        /* Hide duplicate header and sorting arrows in scroll body */
        .dt-scroll-body thead,
        .dt-scroll-body thead tr,
        .dt-scroll-body thead th {
            height: 0px !important;
            line-height: 0 !important;
            padding-top: 0 !important;
            padding-bottom: 0 !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
            border: none !important;
            visibility: hidden !important;
        }
        .dt-scroll-body thead th * {
            display: none !important;
        }
        .dt-scroll-body thead th::before,
        .dt-scroll-body thead th::after {
            display: none !important;
            content: "" !important;
        }

        /* --- RESPONSIVE MOBILE OVERRIDES (max-width: 640px) --- */
        @media (max-width: 640px) {
            /* Force DataTables layout rows to stack vertically */
            div.dt-container div.dt-layout-row {
                display: flex !important;
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 0.75rem !important;
                grid-template-columns: 1fr !important;
            }
            div.dt-container div.dt-layout-row > div {
                width: 100% !important;
                display: flex !important;
                justify-content: center !important;
                padding: 0 !important;
                float: none !important;
            }
            /* Hide length selector on mobile, only show search */
            .dt-length {
                display: none !important;
            }
            .dt-search {
                width: 100% !important;
                justify-content: center !important;
                flex-wrap: wrap !important;
            }
            .dt-search input {
                flex: 1 !important;
                width: auto !important;
                min-width: 120px !important;
            }

            /* Bottom row: force info on top, paging below */
            div.dt-container div.dt-layout-row:last-child {
                display: flex !important;
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 0.75rem !important;
                grid-template-columns: 1fr !important;
            }
            div.dt-container div.dt-layout-row:last-child > div {
                width: 100% !important;
                display: block !important;
                float: none !important;
                text-align: center !important;
            }
            .dt-info {
                text-align: center !important;
                width: 100% !important;
            }
            .dt-paging {
                display: flex !important;
                flex-wrap: wrap !important;
                justify-content: center !important;
                gap: 0.25rem !important;
            }
        }
        
        /* Hide scrollbars for charts slider on mobile */
        .chart-slider::-webkit-scrollbar {
            display: none;
        }
        .chart-slider {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-background text-on-surface antialiased">

    <?php if (!$is_admin): ?>
        <!-- ======================= LOGIN ADMIN ======================= -->
        <section class="py-24 sm:py-32 bg-surface min-h-screen flex items-center justify-center px-4">
            <div
                class="bg-surface-container-lowest p-8 sm:p-12 rounded-[2.5rem] shadow-xl border border-outline-variant/20 max-w-md w-full relative overflow-hidden">
                <!-- Decorative element -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary-fixed rounded-bl-[4rem] -z-0 opacity-50"></div>

                <div class="relative z-10 text-center mb-8">
                    <img src="../assets/gambar/LOGO.png" alt="Logo Laughndry" class="w-20 h-20 mx-auto mb-6 object-contain">
                    <h1 class="text-3xl font-black text-primary mb-2">Login Admin</h1>
                    <p class="text-on-surface-variant">Silakan login untuk mengelola pesanan.</p>
                </div>

                <?php if (isset($login_error)): ?>
                    <div
                        class="bg-error-container text-on-error-container p-4 rounded-xl mb-6 text-sm font-medium flex items-center gap-2">
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
                </form>
            </div>
        </section>

    <?php else: ?>
        <div class="min-h-screen flex bg-background">
            <!-- Sidebar -->
            <aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-primary text-white flex flex-col justify-between p-6 transform -translate-x-full transition-transform duration-300 md:translate-x-0 md:sticky md:top-0 md:h-screen shrink-0 border-r border-outline-variant/10">
                <div class="flex flex-col gap-8 flex-1">
                    <!-- Logo & Brand -->
                    <div class="flex items-center gap-3">
                        <img src="../assets/gambar/LOGO.png" alt="Logo" class="w-10 h-10 object-contain rounded-full bg-white p-1">
                        <div>
                            <h2 class="font-black text-lg leading-tight text-white">Laughndry</h2>
                            <span class="text-xs text-primary-fixed-dim/80">Admin Panel</span>
                        </div>
                    </div>

                    <!-- Menu Navigation -->
                    <nav class="flex flex-col gap-2">
                        <button id="menu-dashboard" onclick="switchTab('dashboard')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all text-left bg-primary-fixed text-primary shadow-sm">
                            <span class="material-symbols-outlined">dashboard</span>
                            Dashboard
                        </button>
                        <button id="menu-pesanan" onclick="switchTab('pesanan')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all text-left text-white/80 hover:text-white hover:bg-white/10">
                            <span class="material-symbols-outlined">receipt_long</span>
                            Pesanan
                        </button>
                        <button id="menu-pelanggan" onclick="switchTab('pelanggan')" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all text-left text-white/80 hover:text-white hover:bg-white/10">
                            <span class="material-symbols-outlined">group</span>
                            Pelanggan
                        </button>
                    </nav>
                </div>

                <!-- Logout Button -->
                <div class="pt-4 border-t border-white/10">
                    <a href="?logout=true" class="w-full flex items-center justify-center gap-2 bg-error-container text-on-error-container px-4 py-3 rounded-xl font-bold hover:bg-error hover:text-on-error transition-all text-sm">
                        <span class="material-symbols-outlined text-lg">logout</span>
                        Logout
                    </a>
                </div>
            </aside>

            <!-- Overlay for Mobile Sidebar -->
            <div id="sidebar-overlay" onclick="toggleSidebar(false)" class="fixed inset-0 z-30 bg-black/50 hidden md:hidden"></div>

            <!-- Main Content Container -->
            <div class="flex-1 flex flex-col min-w-0">
                <!-- Mobile Header -->
                <header class="bg-primary text-white px-4 py-3 flex items-center justify-between md:hidden shadow-md">
                    <div class="flex items-center gap-2">
                        <img src="../assets/gambar/LOGO.png" alt="Logo" class="w-8 h-8 object-contain rounded-full bg-white p-0.5">
                        <span class="font-black text-white text-base">Laughndry Admin</span>
                    </div>
                    <button onclick="toggleSidebar(true)" class="p-2 text-white hover:bg-primary-container rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">menu</span>
                    </button>
                </header>

                <!-- Main Content -->
                <main class="flex-1 overflow-y-auto bg-background">
                    <!-- SECTION 1: DASHBOARD -->
                    <div id="section-dashboard" class="admin-section px-4 sm:px-8 py-8 md:py-12 max-w-7xl mx-auto w-full">
                        <div class="mb-10 border-b border-outline-variant/20 pb-6">
                            <span class="text-secondary font-black tracking-[0.2em] text-xs mb-2 block">RINGKASAN UTAMA</span>
                            <h1 class="text-3xl font-black text-primary">Dashboard Admin</h1>
                            <p class="text-on-surface-variant mt-1 text-sm">Berikut adalah ringkasan performa dan transaksi Laughndry saat ini.</p>
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

                        <!-- ═══════════ CHART CARDS ═══════════ -->
                        <div class="flex flex-col gap-4 sm:gap-6 mb-12 md:grid md:grid-cols-2">
                            <div class="w-full bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-outline-variant/10">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 bg-primary-fixed rounded-full flex items-center justify-center">
                                        <span class="material-symbols-outlined text-primary text-xl">trending_up</span>
                                    </div>
                                    <div>
                                        <h3 class="text-sm text-on-surface-variant font-medium">Tren Pesanan</h3>
                                        <p class="text-xs text-on-surface-variant/60">4 minggu terakhir</p>
                                    </div>
                                </div>
                                <div style="position: relative; height: 200px;">
                                    <canvas id="ordersChart"></canvas>
                                </div>
                            </div>
                            <div class="w-full bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-outline-variant/10">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 bg-secondary-fixed rounded-full flex items-center justify-center">
                                        <span class="material-symbols-outlined text-secondary text-xl">payments</span>
                                    </div>
                                    <div>
                                        <h3 class="text-sm text-on-surface-variant font-medium">Tren Revenue</h3>
                                        <p class="text-xs text-on-surface-variant/60">4 minggu terakhir</p>
                                    </div>
                                </div>
                                <div style="position: relative; height: 200px;">
                                    <canvas id="revenueChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: PESANAN -->
                    <div id="section-pesanan" class="admin-section hidden px-4 sm:px-8 py-8 md:py-12 max-w-7xl mx-auto w-full">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 pb-6 border-b border-outline-variant/20 gap-4">
                            <div>
                                <h1 class="text-3xl font-black text-primary">Pesanan</h1>
                                <p class="text-on-surface-variant mt-1 text-sm">Daftar pesanan dari pelanggan.</p>
                            </div>
                            <!-- Download Laporan Button -->
                            <div class="relative inline-block text-left dropdown-container">
                                <button onclick="toggleDropdown(this, event)" class="inline-flex items-center gap-2 bg-primary-container text-on-primary px-6 py-2.5 rounded-full font-bold text-sm hover:scale-105 active:scale-95 transition-all shadow-md focus:outline-none focus:ring-4 focus:ring-primary/20">
                                    <span class="material-symbols-outlined text-lg">download</span> Download Laporan
                                    <span class="material-symbols-outlined text-lg transition-transform arrow-icon">expand_more</span>
                                </button>
                                <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-surface-container-lowest border border-outline-variant/20 rounded-2xl shadow-xl opacity-0 invisible transition-all duration-200 z-50">
                                    <div class="py-2 flex flex-col text-center">
                                        <a href="export_pdf.php?period=monthly" target="_blank" class="px-4 py-2.5 text-sm text-on-surface-variant hover:bg-surface-container-low hover:text-primary font-bold transition-colors border-b border-outline-variant/10">Bulanan (PDF)</a>
                                        <a href="export_excel.php?period=monthly" target="_blank" class="px-4 py-2.5 text-sm text-on-surface-variant hover:bg-surface-container-low hover:text-secondary font-bold transition-colors border-b border-outline-variant/10">Bulanan (XLSX)</a>
                                        <a href="export_pdf.php?period=yearly" target="_blank" class="px-4 py-2.5 text-sm text-on-surface-variant hover:bg-surface-container-low hover:text-primary font-bold transition-colors border-b border-outline-variant/10">Tahunan (PDF)</a>
                                        <a href="export_excel.php?period=yearly" target="_blank" class="px-4 py-2.5 text-sm text-on-surface-variant hover:bg-surface-container-low hover:text-secondary font-bold transition-colors">Tahunan (XLSX)</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-surface-container-lowest rounded-[2rem] p-6 sm:p-8 shadow-md border border-outline-variant/10">
                            <!-- Table -->
                            <table id="orders-table" class="w-full text-left border-collapse">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-surface-container text-primary border-b-2 border-outline-variant/30 text-sm shadow-sm">
                                        <th class="p-4 font-bold whitespace-nowrap">ID</th>
                                        <th class="p-4 font-bold whitespace-nowrap">Tanggal</th>
                                        <th class="p-4 font-bold whitespace-nowrap">Pelanggan</th>
                                        <th class="p-4 font-bold whitespace-normal max-w-[110px]" style="width: 110px; min-width: 110px; max-width: 110px;">Jenis Layanan</th>
                                        <th class="p-4 font-bold whitespace-normal max-w-[160px]" style="width: 160px; min-width: 160px; max-width: 160px;">Detail Item</th>
                                        <th class="p-4 font-bold whitespace-nowrap">Qty</th>
                                        <th class="p-4 font-bold whitespace-nowrap">Harga Total</th>
                                        <th class="p-4 font-bold whitespace-nowrap">Metode Bayar</th>
                                        <th class="p-4 font-bold whitespace-nowrap">Status Pesanan</th>
                                        <th class="p-4 font-bold whitespace-nowrap text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-outline-variant/20">
                                    <?php if (empty($orders)): ?>
                                        <tr>
                                            <td colspan="10" class="p-8 text-center text-on-surface-variant bg-surface-container-low/30 italic">Belum ada pesanan.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($orders as $order): ?>
                                            <?php
                                            // Status badge colors
                                            $status_colors = [
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'menunggu verifikasi' => 'bg-orange-100 text-orange-800',
                                                'ditolak' => 'bg-red-100 text-red-800',
                                                'diproses' => 'bg-blue-100 text-blue-800',
                                                'cuci' => 'bg-cyan-100 text-cyan-800',
                                                'setrika' => 'bg-amber-100 text-amber-800',
                                                'selesai' => 'bg-green-100 text-green-800',
                                                'siap diambil' => 'bg-purple-100 text-purple-800',
                                                'sudah diambil' => 'bg-gray-100 text-gray-600',
                                            ];
                                            $badge = $status_colors[$order['status']] ?? 'bg-gray-100 text-gray-600';

                                            // Metode bayar badge
                                            $metode_icons = [
                                                'tunai' => '💵 Tunai',
                                                'qris' => '🔲 QRIS',
                                                'transfer' => '🏦 Transfer',
                                                'bank_transfer' => '🏦 Transfer',
                                                'credit_card' => '💳 Kartu Kredit',
                                                'gopay' => '📱 GoPay',
                                                'shopeepay' => '📱 ShopeePay',
                                                'cstore' => '🏪 Minimarket',
                                                'echannel' => '🏦 Mandiri Bill',
                                                'midtrans' => '💳 Online',
                                            ];
                                            $raw_metode = $order['metode_bayar'] ?? '';
                                            $metode_label = $metode_icons[$raw_metode] ?? ($raw_metode ?: '💳 Online');

                                            // Status Bayar badge
                                            $pay_status = $order['payment_status'] ?? 'pending';
                                            $pay_badge = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                                            $pay_text = 'Pending';
                                            
                                            if ($pay_status === 'settlement' || strtolower($pay_status) === 'paid') {
                                                $pay_badge = 'bg-green-100 text-green-800 border-green-200';
                                                $pay_text = 'Paid';
                                            } elseif (strtolower($pay_status) === 'menunggu verifikasi') {
                                                $pay_badge = 'bg-orange-100 text-orange-800 border-orange-200 animate-pulse';
                                                $pay_text = 'Menunggu Verifikasi';
                                            } elseif (in_array(strtolower($pay_status), ['deny', 'cancel', 'expire', 'ditolak'])) {
                                                $pay_badge = 'bg-red-100 text-red-800 border-red-200';
                                                $pay_text = 'Ditolak';
                                            }
                                            ?>
                                            <tr class="hover:bg-surface-container-low transition-colors">
                                                <td class="p-4 font-bold text-primary">#<?= $order['id'] ?></td>
                                                <td class="p-4 text-on-surface-variant text-sm whitespace-nowrap" data-order="<?= strtotime($order['created_at']) ?>"><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></td>
                                                <td class="p-4 font-medium text-primary"><?= htmlspecialchars($order['customer_nama']) ?></td>
                                                <td class="p-4 text-on-surface-variant text-sm max-w-[110px] whitespace-normal break-words" style="width: 110px; min-width: 110px; max-width: 110px;"><?= htmlspecialchars($order['kategori_list'] ?? '-') ?></td>
                                                <td class="p-4 text-on-surface-variant text-sm max-w-[160px] whitespace-normal break-words" style="width: 160px; min-width: 160px; max-width: 160px;"><?= htmlspecialchars($order['item_list'] ?? '-') ?></td>
                                                <td class="p-4 text-on-surface-variant font-medium"><?= $order['total_qty'] ?? 0 ?></td>
                                                <td class="p-4 font-bold text-secondary">Rp <?= number_format($order['total_harga'], 0, ',', '.') ?></td>
                                                <td class="p-4 text-sm"><?= $metode_label ?></td>
                                                <td class="p-4">
                                                    <!-- Status Update Form -->
                                                    <form method="POST" action="admin.php" class="inline">
                                                        <input type="hidden" name="update_status" value="1">
                                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                        <select name="new_status" onchange="this.form.submit()" class="text-xs font-bold pl-4 pr-8 py-2 rounded-full border-0 cursor-pointer min-w-[120px] <?= $badge ?> focus:ring-2 focus:ring-primary/20">
                                                            <?php foreach (['pending', 'menunggu verifikasi', 'ditolak', 'diproses', 'cuci', 'setrika', 'selesai', 'siap diambil', 'sudah diambil'] as $s): ?>
                                                                <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= ucwords($s) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </form>
                                                </td>
                                                <td class="p-4 text-center">
                                                    <button type="button" 
                                                            onclick="showOrderDetail(<?= htmlspecialchars(json_encode([
                                                                'id' => $order['id'],
                                                                'tanggal' => date('d M Y, H:i', strtotime($order['created_at'])),
                                                                'nama' => $order['customer_nama'],
                                                                'telepon' => $order['customer_telepon'],
                                                                'alamat' => $order['customer_alamat'] ?? '-',
                                                                'kategori' => $order['kategori_list'] ?? '-',
                                                                'items' => $order['item_list'] ?? '-',
                                                                'qty' => $order['total_qty'] ?? 0,
                                                                'total' => 'Rp ' . number_format($order['total_harga'], 0, ',', '.'),
                                                                'metode' => $metode_label,
                                                                'raw_metode' => $raw_metode,
                                                                'status_bayar' => $pay_text,
                                                                'status_pesanan' => $order['status'],
                                                                'bukti_bayar' => $order['bukti_bayar']
                                                            ])) ?>)" 
                                                            class="px-3 py-1.5 bg-[#035D51] text-white font-bold rounded-lg hover:scale-105 active:scale-95 transition-all text-xs flex items-center justify-center gap-1 mx-auto">
                                                        <span class="material-symbols-outlined text-sm">visibility</span> Detail
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- SECTION 3: PELANGGAN -->
                    <div id="section-pelanggan" class="admin-section hidden px-4 sm:px-8 py-8 md:py-12 max-w-7xl mx-auto w-full">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 pb-6 border-b border-outline-variant/20 gap-4">
                            <div>
                                <h1 class="text-3xl font-black text-primary">Profil Pelanggan</h1>
                                <p class="text-on-surface-variant mt-1 text-sm">Daftar pelanggan yang terdaftar.</p>
                            </div>
                            <!-- Download Laporan Button -->
                            <div class="relative inline-block text-left dropdown-container">
                                <button onclick="toggleDropdown(this, event)" class="inline-flex items-center gap-2 bg-primary-container text-on-primary px-6 py-2.5 rounded-full font-bold text-sm hover:scale-105 active:scale-95 transition-all shadow-md focus:outline-none focus:ring-4 focus:ring-primary/20">
                                    <span class="material-symbols-outlined text-lg">download</span> Download Laporan
                                    <span class="material-symbols-outlined text-lg transition-transform arrow-icon">expand_more</span>
                                </button>
                                <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-surface-container-lowest border border-outline-variant/20 rounded-2xl shadow-xl opacity-0 invisible transition-all duration-200 z-50">
                                    <div class="py-2 flex flex-col text-center">
                                        <a href="export_customers_pdf.php?period=monthly" target="_blank" class="px-4 py-2.5 text-sm text-on-surface-variant hover:bg-surface-container-low hover:text-primary font-bold transition-colors border-b border-outline-variant/10">Bulanan (PDF)</a>
                                        <a href="export_customers_excel.php?period=monthly" target="_blank" class="px-4 py-2.5 text-sm text-on-surface-variant hover:bg-surface-container-low hover:text-secondary font-bold transition-colors border-b border-outline-variant/10">Bulanan (XLSX)</a>
                                        <a href="export_customers_pdf.php?period=yearly" target="_blank" class="px-4 py-2.5 text-sm text-on-surface-variant hover:bg-surface-container-low hover:text-primary font-bold transition-colors border-b border-outline-variant/10">Tahunan (PDF)</a>
                                        <a href="export_customers_excel.php?period=yearly" target="_blank" class="px-4 py-2.5 text-sm text-on-surface-variant hover:bg-surface-container-low hover:text-secondary font-bold transition-colors">Tahunan (XLSX)</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-surface-container-lowest rounded-[2rem] p-6 sm:p-8 shadow-md border border-outline-variant/10">
                            <!-- Table -->
                            <table id="customers-table" class="w-full text-left border-collapse">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-surface-container text-primary border-b-2 border-outline-variant/30 text-sm shadow-sm">
                                        <th class="p-4 font-bold whitespace-normal" style="width: 50px; min-width: 50px; max-width: 50px;">ID</th>
                                        <th class="p-4 font-bold whitespace-normal" style="width: 150px; min-width: 150px; max-width: 150px;">Nama</th>
                                        <th class="p-4 font-bold whitespace-normal" style="width: 250px; min-width: 250px; max-width: 250px;">Alamat</th>
                                        <th class="p-4 font-bold whitespace-normal" style="width: 130px; min-width: 130px; max-width: 130px;">Nomor Telepon</th>
                                        <th class="p-4 font-bold whitespace-normal" style="width: 120px; min-width: 120px; max-width: 120px;">Terdaftar</th>
                                        <th class="p-4 font-bold text-center" style="width: 100px; min-width: 100px; max-width: 100px;">Delete</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-outline-variant/20">
                                    <?php if (empty($customers)): ?>
                                        <tr>
                                            <td colspan="6" class="p-8 text-center text-on-surface-variant bg-surface-container-low/30 italic">Belum ada pelanggan terdaftar.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($customers as $cust): ?>
                                            <tr class="hover:bg-surface-container-low transition-colors group">
                                                <td class="p-4 text-on-surface-variant font-medium" style="width: 50px; min-width: 50px; max-width: 50px;"><?= $cust['id'] ?></td>
                                                <td class="p-4 font-bold text-primary whitespace-normal break-words" style="width: 150px; min-width: 150px; max-width: 150px;"><?= htmlspecialchars($cust['nama']) ?></td>
                                                <td class="p-4 text-on-surface-variant whitespace-normal break-words" style="width: 250px; min-width: 250px; max-width: 250px;"><?= htmlspecialchars($cust['alamat']) ?></td>
                                                <td class="p-4 text-on-surface-variant whitespace-normal break-words" style="width: 130px; min-width: 130px; max-width: 130px;">
                                                    <?php if (!empty($cust['telepon'])): ?>
                                                        <?php
                                                        $wa_phone = formatWaNumber($cust['telepon']);
                                                        if ($cust['latest_order_status'] === 'pending') {
                                                            $wa_msg = "Pesanan laundry anda telah dibuat, mohon konfirmasi pembayaran ke admin";
                                                        } elseif ($cust['latest_order_status'] === 'diproses') {
                                                            $wa_msg = "Pesanan laundry anda telah dibuat dan saat ini sedang diproses";
                                                        } else {
                                                            $wa_msg = "Pesanan laundry anda telah dibuat, status saat ini: " . ucwords($cust['latest_order_status']);
                                                        }
                                                        $wa_link = "https://wa.me/" . $wa_phone . "?text=" . urlencode($wa_msg);
                                                        ?>
                                                        <a href="<?= $wa_link ?>" target="_blank" class="inline-flex items-center gap-1 text-[#035D51] hover:underline font-semibold">
                                                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                                            </svg>
                                                            <?= htmlspecialchars($cust['telepon']) ?>
                                                        </a>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                                <td class="p-4 text-on-surface-variant text-sm whitespace-normal" style="width: 120px; min-width: 120px; max-width: 120px;" data-order="<?= strtotime($cust['created_at']) ?>"><?= date('d M Y', strtotime($cust['created_at'])) ?></td>
                                                <td class="p-4 text-center" style="width: 100px; min-width: 100px; max-width: 100px;">
                                                    <button type="button" onclick="deleteCustomer(<?= $cust['id'] ?>, '<?= htmlspecialchars($cust['nama'], ENT_QUOTES) ?>', this)" class="px-4 py-2 text-sm font-bold bg-surface border-2 border-outline-variant/30 text-on-surface-variant rounded-lg hover:bg-error hover:text-on-error hover:border-error transition-colors inline-flex items-center gap-1">
                                                        <span class="material-symbols-outlined text-base">delete</span> Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <!-- Modal Notifikasi Pesanan Baru (Butuh Verifikasi) -->
        <div id="new-order-modal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm hidden transition-opacity duration-300 opacity-0">
            <div class="bg-surface-container-lowest max-w-md w-full rounded-[2.5rem] shadow-2xl border border-primary/20 overflow-hidden transform scale-95 transition-transform duration-300">
                <!-- Header -->
                <div class="bg-primary p-6 text-center text-on-primary relative">
                    <div class="w-16 h-16 bg-primary-container rounded-full flex items-center justify-center mx-auto mb-3 animate-bounce">
                        <span class="material-symbols-outlined text-3xl text-on-primary-container">notifications_active</span>
                    </div>
                    <h3 class="text-xl font-black text-white" id="modal-title-text">🔔 Pembayaran Baru Masuk!</h3>
                    <p class="text-xs text-white/80 mt-1" id="modal-subtitle-text">Sistem mendeteksi transaksi QRIS yang perlu diverifikasi</p>
                </div>
                
                <!-- Content -->
                <div class="p-6 flex flex-col gap-4">
                    <div class="bg-surface-container-low p-4 rounded-2xl border border-outline-variant/20">
                        <div class="flex justify-between items-center mb-2 pb-2 border-b border-outline-variant/10">
                            <span class="text-xs font-bold text-on-surface-variant/70">ORDER ID</span>
                            <span id="modal-order-id" class="text-sm font-black text-primary">#123</span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-bold text-on-surface-variant/70">PELANGGAN</span>
                            <span id="modal-customer-name" class="text-sm font-bold text-on-surface">Budi Santoso</span>
                        </div>
                        <div class="mb-2">
                            <span class="text-xs font-bold text-on-surface-variant/70 block mb-1">DETAIL ITEM</span>
                            <div id="modal-order-items" class="text-xs text-on-surface-variant bg-surface-container-lowest p-2.5 rounded-lg border border-outline-variant/10 max-h-20 overflow-y-auto font-medium">
                                Reguler (3 Hari) (x5), Express (x1)
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-outline-variant/10">
                            <span class="text-xs font-bold text-on-surface-variant/70">TOTAL BAYAR</span>
                            <span id="modal-total-harga" class="text-base font-black text-secondary">Rp 54.000</span>
                        </div>
                    </div>
                </div>
                
                <!-- Footer Buttons -->
                <div class="px-6 pb-6 flex gap-2">
                    <button onclick="closeNewOrderModal(true)" class="flex-1 py-3 bg-surface-container-high hover:bg-surface-variant text-on-surface-variant font-bold rounded-full text-sm transition-all text-center">
                        Tutup
                    </button>
                    <button id="modal-view-detail-btn" class="flex-1 py-3 bg-primary text-on-primary hover:scale-[1.02] active:scale-95 font-bold rounded-full text-sm transition-all shadow-lg shadow-primary/20 text-center">
                        Lihat Detail
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Detail Pesanan -->
        <div id="order-detail-modal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm hidden transition-opacity duration-300 opacity-0">
            <div class="bg-surface-container-lowest max-w-lg w-full rounded-[2.5rem] shadow-2xl border border-primary/20 overflow-hidden transform scale-95 transition-transform duration-300">
                <!-- Header -->
                <div class="bg-primary p-6 text-center text-on-primary">
                    <h3 class="text-xl font-black text-white" id="detail-modal-title">Detail Pesanan</h3>
                    <p class="text-xs text-white/80 mt-1" id="detail-modal-date">Tanggal Pesanan</p>
                </div>
                
                <!-- Content -->
                <div class="p-6 flex flex-col gap-4 max-h-[50vh] overflow-y-auto">
                    <!-- Customer Data -->
                    <div class="bg-surface-container-low p-4 rounded-2xl border border-outline-variant/20">
                        <h4 class="text-xs font-black text-primary uppercase tracking-wider mb-2">Informasi Pelanggan</h4>
                        <div class="flex flex-col gap-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant/70 font-semibold">Nama:</span>
                                <span id="detail-cust-name" class="font-bold text-on-surface"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-on-surface-variant/70 font-semibold">Telepon:</span>
                                <div class="flex items-center gap-2">
                                    <span id="detail-cust-phone" class="font-bold text-on-surface"></span>
                                    <a id="detail-cust-wa-btn" href="#" target="_blank" class="px-2.5 py-1 bg-green-600 text-white font-bold rounded-lg text-xs hover:bg-green-700 transition-colors inline-flex items-center gap-1">
                                        💬 Chat WA
                                    </a>
                                </div>
                            </div>
                            <div class="flex flex-col gap-1 mt-1">
                                <span class="text-on-surface-variant/70 font-semibold">Alamat:</span>
                                <span id="detail-cust-address" class="text-xs text-on-surface-variant font-medium bg-surface-container-lowest p-2.5 rounded-lg border border-outline-variant/10 leading-relaxed"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Items Detail -->
                    <div class="bg-surface-container-low p-4 rounded-2xl border border-outline-variant/20">
                        <h4 class="text-xs font-black text-primary uppercase tracking-wider mb-2">Layanan & Item</h4>
                        <div class="flex flex-col gap-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant/70 font-semibold">Jenis Layanan:</span>
                                <span id="detail-order-kategori" class="font-bold text-primary"></span>
                            </div>
                            <div class="flex flex-col gap-1 mt-1">
                                <span class="text-on-surface-variant/70 font-semibold">Detail Item:</span>
                                <p id="detail-order-items" class="text-xs text-on-surface-variant bg-surface-container-lowest p-2.5 rounded-lg border border-outline-variant/10 font-medium leading-relaxed"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Status -->
                    <div class="bg-surface-container-low p-4 rounded-2xl border border-outline-variant/20">
                        <h4 class="text-xs font-black text-primary uppercase tracking-wider mb-2">Status & Pembayaran</h4>
                        <div class="flex flex-col gap-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant/70 font-semibold">Metode Bayar:</span>
                                <span id="detail-order-metode" class="font-bold text-on-surface"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-on-surface-variant/70 font-semibold">Status Bayar:</span>
                                <span id="detail-order-status-bayar" class="text-xs font-bold px-2.5 py-1 rounded-full border"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-on-surface-variant/70 font-semibold">Status Pesanan:</span>
                                <span id="detail-order-status-pesanan" class="text-xs font-black px-2.5 py-1 rounded-full border uppercase"></span>
                            </div>
                            <div class="flex justify-between pt-3 border-t border-outline-variant/10 mt-1">
                                <span class="font-bold text-on-surface-variant">Total Tagihan:</span>
                                <span id="detail-order-total" class="font-black text-secondary text-base"></span>
                            </div>
                    </div>

                    <!-- Bukti Pembayaran -->
                    <div class="bg-surface-container-low p-4 rounded-2xl border border-outline-variant/20" id="detail-proof-section" style="display: none;">
                        <h4 class="text-xs font-black text-primary uppercase tracking-wider mb-2">Bukti Pembayaran</h4>
                        <div class="text-center">
                            <a id="detail-proof-link" href="#" target="_blank">
                                <img id="detail-proof-img" src="#" alt="Bukti Transfer" class="max-h-48 rounded-lg border border-outline-variant/10 mx-auto hover:opacity-90 transition-all shadow-sm">
                            </a>
                            <p class="text-xs text-on-surface-variant/70 mt-2">Klik gambar untuk melihat resolusi penuh / download</p>
                        </div>
                    </div>
                </div>
                
                <!-- Footer / Actions -->
                <div class="p-6 bg-surface-container-low border-t border-outline-variant/10 flex flex-col gap-2" id="detail-modal-actions">
                    <!-- Buttons inserted dynamically based on status -->
                </div>
            </div>
        </div>

        <?php
        $initial_notified_ids = [];
        if ($is_admin) {
            foreach ($orders as $o) {
                if ($o['payment_status'] === 'Menunggu Verifikasi' || 
                    ($o['metode_bayar'] === 'tunai' && $o['status'] === 'pending')) {
                    $initial_notified_ids[] = (int) $o['id'];
                }
            }
        }
        ?>
        <!-- jQuery & DataTables JS -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
        <script src="https://cdn.datatables.net/2.0.8/js/dataTables.tailwindcss.js"></script>

        <script>
            $(document).ready(function() {
                $('#orders-table').DataTable({
                    scrollX: true,
                    autoWidth: false,
                    columnDefs: [
                        { width: "110px", targets: 3 },
                        { width: "160px", targets: 4 },
                        // Hanya kolom Tanggal (index 1) yang bisa di-sort
                        { orderable: false, targets: [0, 2, 3, 4, 5, 6, 7, 8, 9] }
                    ],
                    // Pengurutan awal berdasarkan kolom kedua (indeks 1: Tanggal) secara DESC
                    order: [[1, 'desc']],
                    // Kustomisasi bahasa ke Bahasa Indonesia agar user-friendly
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ pesanan",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ pesanan",
                        infoEmpty: "Menampilkan 0 sampai 0 dari 0 pesanan",
                        infoFiltered: "(disaring dari _MAX_ total pesanan)",
                        zeroRecords: "Tidak ditemukan pesanan yang cocok",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Berikutnya",
                            previous: "Sebelumnya"
                        }
                    }
                });

                $('#customers-table').DataTable({
                    scrollX: true,
                    autoWidth: false,
                    columnDefs: [
                        { width: "50px", targets: 0 },
                        { width: "150px", targets: 1 },
                        { width: "250px", targets: 2 },
                        { width: "130px", targets: 3 },
                        { width: "120px", targets: 4 },
                        { width: "100px", targets: 5 },
                        // Hanya kolom Nama (index 1) dan Terdaftar (index 4) yang bisa di-sort
                        { orderable: false, targets: [0, 2, 3, 5] }
                    ],
                    // Pengurutan awal berdasarkan kolom kelima (indeks 4: Terdaftar) secara DESC (terbaru)
                    order: [[4, 'desc']],
                    // Kustomisasi bahasa ke Bahasa Indonesia agar user-friendly
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ pelanggan",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ pelanggan",
                        infoEmpty: "Menampilkan 0 sampai 0 dari 0 pelanggan",
                        infoFiltered: "(disaring dari _MAX_ total pelanggan)",
                        zeroRecords: "Tidak ditemukan pelanggan yang cocok",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Berikutnya",
                            previous: "Sebelumnya"
                        }
                    }
                });
            });

            function toggleSidebar(show) {
                const sidebar = $('#admin-sidebar');
                const overlay = $('#sidebar-overlay');
                if (show) {
                    sidebar.removeClass('-translate-x-full');
                    overlay.removeClass('hidden');
                } else {
                    sidebar.addClass('-translate-x-full');
                    overlay.addClass('hidden');
                }
            }

            function toggleDropdown(btn, e) {
                e.stopPropagation();
                const container = $(btn).closest('.dropdown-container');
                const menu = container.find('.dropdown-menu');
                const arrow = container.find('.arrow-icon');
                
                // Close other dropdowns
                $('.dropdown-menu').not(menu).addClass('opacity-0 invisible');
                $('.arrow-icon').not(arrow).removeClass('rotate-180');
                
                // Toggle current dropdown
                if (menu.hasClass('opacity-0')) {
                    menu.removeClass('opacity-0 invisible');
                    arrow.addClass('rotate-180');
                } else {
                    menu.addClass('opacity-0 invisible');
                    arrow.removeClass('rotate-180');
                }
            }

            // Close dropdowns when clicking outside
            $(document).on('click', function() {
                $('.dropdown-menu').addClass('opacity-0 invisible');
                $('.arrow-icon').removeClass('rotate-180');
            });

            function switchTab(tabName) {
                // Sembunyikan semua section
                $('.admin-section').addClass('hidden');
                
                // Tampilkan section yang aktif
                $('#section-' + tabName).removeClass('hidden');
                
                // Reset styling tombol navigasi sidebar
                $('#menu-dashboard').removeClass('bg-primary-fixed text-primary shadow-sm')
                                     .addClass('text-white/80 hover:text-white hover:bg-white/10');
                $('#menu-pesanan').removeClass('bg-primary-fixed text-primary shadow-sm')
                                   .addClass('text-white/80 hover:text-white hover:bg-white/10');
                $('#menu-pelanggan').removeClass('bg-primary-fixed text-primary shadow-sm')
                                     .addClass('text-white/80 hover:text-white hover:bg-white/10');
                
                // Aktifkan styling tombol yang terpilih
                $('#menu-' + tabName).removeClass('text-white/80 hover:text-white hover:bg-white/10')
                                       .addClass('bg-primary-fixed text-primary shadow-sm');
                
                // Set URL Hash
                window.location.hash = tabName;

                // Tutup sidebar di mobile jika sedang terbuka
                toggleSidebar(false);

                // Atur ulang kolom DataTables agar presisi
                if (tabName === 'pesanan') {
                    $('#orders-table').DataTable().columns.adjust().draw();
                } else if (tabName === 'pelanggan') {
                    $('#customers-table').DataTable().columns.adjust().draw();
                }
            }

            // ═══════════ CHART.JS INITIALIZATION ═══════════
            const chartLabels = <?= json_encode($chart_labels) ?>;
            const chartOrdersData = <?= json_encode($chart_orders) ?>;
            const chartRevenueData = <?= json_encode($chart_revenue) ?>;

            // Tren Pesanan - Line Chart
            new Chart(document.getElementById('ordersChart'), {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Pesanan',
                        data: chartOrdersData,
                        borderColor: '#00433a',
                        backgroundColor: 'rgba(0, 67, 58, 0.08)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#00433a',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1a1c1c',
                            titleFont: { family: 'Inter', weight: '700' },
                            bodyFont: { family: 'Inter' },
                            padding: 12,
                            cornerRadius: 12,
                            callbacks: {
                                label: function(ctx) {
                                    return ' ' + ctx.parsed.y + ' pesanan';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Inter', size: 12, weight: '600' }, color: '#6f7976' }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(111, 121, 118, 0.1)' },
                            ticks: {
                                font: { family: 'Inter', size: 12, weight: '600' },
                                color: '#6f7976',
                                stepSize: 1,
                                precision: 0
                            }
                        }
                    }
                }
            });

            // Tren Revenue - Line Chart with Area Fill
            new Chart(document.getElementById('revenueChart'), {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Revenue',
                        data: chartRevenueData,
                        borderColor: '#865300',
                        backgroundColor: 'rgba(134, 83, 0, 0.08)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#865300',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1a1c1c',
                            titleFont: { family: 'Inter', weight: '700' },
                            bodyFont: { family: 'Inter' },
                            padding: 12,
                            cornerRadius: 12,
                            callbacks: {
                                label: function(ctx) {
                                    return ' Rp ' + ctx.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Inter', size: 12, weight: '600' }, color: '#6f7976' }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(111, 121, 118, 0.1)' },
                            ticks: {
                                font: { family: 'Inter', size: 12, weight: '600' },
                                color: '#6f7976',
                                callback: function(val) {
                                    if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + 'jt';
                                    if (val >= 1000) return 'Rp ' + (val / 1000).toFixed(0) + 'rb';
                                    return 'Rp ' + val;
                                }
                            }
                        }
                    }
                }
            });

            // ═══════════ AJAX DELETE FUNCTIONS (NO PAGE RELOAD) ═══════════
            function deleteOrder(orderId, btnElement) {
                if (!confirm('Yakin hapus pesanan #' + orderId + '?')) {
                    return;
                }
                
                btnElement.disabled = true;
                
                fetch('admin.php?delete_order=' + orderId + '&ajax=1')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const table = $('#orders-table').DataTable();
                            const row = $(btnElement).closest('tr');
                            table.row(row).remove().draw(false);
                        } else {
                            alert('Gagal menghapus: ' + (data.message || 'Unknown error'));
                            btnElement.disabled = false;
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Terjadi kesalahan jaringan.');
                        btnElement.disabled = false;
                    });
            }

            function deleteCustomer(customerId, customerName, btnElement) {
                if (!confirm('Yakin hapus pelanggan ' + customerName + '? Semua pesanannya juga akan terhapus.')) {
                    return;
                }
                
                btnElement.disabled = true;
                
                fetch('admin.php?delete_customer=' + customerId + '&ajax=1')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const table = $('#customers-table').DataTable();
                            const row = $(btnElement).closest('tr');
                            table.row(row).remove().draw(false);
                        } else {
                            alert('Gagal menghapus: ' + (data.message || 'Unknown error'));
                            btnElement.disabled = false;
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Terjadi kesalahan jaringan.');
                        btnElement.disabled = false;
                    });
            }

            // ═══════════ REAL-TIME NEW ORDER POLLING & VERIFICATION ═══════════
            // Simpan ID pesanan yang sudah diberitahukan agar tidak double alert
            const notifiedOrders = <?= json_encode($initial_notified_ids) ?>;

            function checkNewOrders() {
                fetch('check_new_orders.php')
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.new_orders && data.new_orders.length > 0) {
                            // Saring order yang belum dinotifikasi
                            data.new_orders.forEach(order => {
                                const orderId = parseInt(order.id);
                                if (!notifiedOrders.includes(orderId)) {
                                    notifiedOrders.push(orderId);
                                    
                                    // Tampilkan modal notifikasi
                                    showNewOrderModal(order);
                                }
                            });
                        }
                    })
                    .catch(err => console.error("Error checking new orders:", err));
            }

            function showNewOrderModal(order) {
                // Mainkan suara chime
                playNotificationSound();
                
                // Atur judul dan subjudul berdasarkan metode pembayaran
                if (order.metode_bayar === 'tunai') {
                    $('#modal-title-text').text('🔔 Pesanan Baru Masuk!');
                    $('#modal-subtitle-text').text('Sistem mendeteksi pesanan baru dengan metode pembayaran Tunai');
                } else {
                    $('#modal-title-text').text('🔔 Pembayaran Baru Masuk!');
                    $('#modal-subtitle-text').text('Sistem mendeteksi transaksi QRIS yang perlu diverifikasi');
                }

                // Isi data ke modal
                $('#modal-order-id').text('#' + order.id);
                $('#modal-customer-name').text(order.customer_nama);
                $('#modal-order-items').text(order.item_list || '-');
                
                const formattedHarga = 'Rp ' + parseInt(order.total_harga).toLocaleString('id-ID');
                $('#modal-total-harga').text(formattedHarga);
                
                // Pasang event handler untuk tombol Lihat Detail di modal notifikasi
                $('#modal-view-detail-btn').off('click').on('click', function() {
                    closeNewOrderModal(false);
                    shouldReloadOnDetailClose = true; // Set flag to reload when detail modal is closed
                    
                    // Formulasi object details untuk showOrderDetail
                    const details = {
                        id: order.id,
                        tanggal: new Date(order.created_at).toLocaleDateString('id-ID', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        }),
                        nama: order.customer_nama,
                        telepon: order.customer_telepon,
                        alamat: order.customer_alamat || '-',
                        kategori: order.kategori_list || '-',
                        items: order.item_list || '-',
                        qty: order.total_qty || 0,
                        total: 'Rp ' + parseInt(order.total_harga).toLocaleString('id-ID'),
                        metode: order.metode_bayar === 'qris' ? '🔲 QRIS' : (order.metode_bayar === 'tunai' ? '💵 Tunai' : order.metode_bayar),
                        raw_metode: order.metode_bayar,
                        status_bayar: order.payment_status || 'Pending',
                        status_pesanan: order.status,
                        bukti_bayar: order.bukti_bayar
                    };
                    
                    showOrderDetail(details);
                });
                
                // Tampilkan overlay modal
                const modal = $('#new-order-modal');
                modal.removeClass('hidden');
                setTimeout(() => {
                    modal.removeClass('opacity-0');
                    modal.find('> div').removeClass('scale-95').addClass('scale-100');
                }, 50);
            }

            function closeNewOrderModal(shouldReload = false) {
                const modal = $('#new-order-modal');
                modal.addClass('opacity-0');
                modal.find('> div').removeClass('scale-100').addClass('scale-95');
                setTimeout(() => {
                    modal.addClass('hidden');
                    if (shouldReload) {
                        window.location.reload();
                    }
                }, 300);
            }

            function showOrderDetail(details) {
                // Populate details modal
                $('#detail-modal-title').text('Detail Pesanan #' + details.id);
                $('#detail-modal-date').text(details.tanggal);
                
                $('#detail-cust-name').text(details.nama);
                $('#detail-cust-phone').text(details.telepon);
                
                // WhatsApp Link
                let cleanPhone = String(details.telepon || '').replace(/[^0-9]/g, '');
                if (cleanPhone.startsWith('0')) {
                    cleanPhone = '62' + cleanPhone.substring(1);
                }
                const waMsg = `Halo ${details.nama}, kami dari Laughndry ingin mengonfirmasi pesanan #${details.id} Anda...`;
                $('#detail-cust-wa-btn').attr('href', `https://wa.me/${cleanPhone}?text=${encodeURIComponent(waMsg)}`);
                
                $('#detail-cust-address').text(details.alamat);
                $('#detail-order-kategori').text(details.kategori);
                $('#detail-order-items').text(details.items);
                $('#detail-order-metode').text(details.metode);
                
                // Format payment status badge
                const payStatusSpan = $('#detail-order-status-bayar');
                payStatusSpan.text(details.status_bayar);
                payStatusSpan.removeClass(); // clear classes
                payStatusSpan.addClass('text-xs font-bold px-2.5 py-1 rounded-full border');
                
                if (details.status_bayar === 'Paid') {
                    payStatusSpan.addClass('bg-green-100 text-green-800 border-green-200');
                } else if (details.status_bayar === 'Menunggu Verifikasi') {
                    payStatusSpan.addClass('bg-orange-100 text-orange-800 border-orange-200 animate-pulse');
                } else if (details.status_bayar === 'Ditolak') {
                    payStatusSpan.addClass('bg-red-100 text-red-800 border-red-200');
                } else {
                    payStatusSpan.addClass('bg-yellow-100 text-yellow-800 border-yellow-200');
                }
                
                // Format order status badge
                const orderStatusSpan = $('#detail-order-status-pesanan');
                orderStatusSpan.text(details.status_pesanan);
                orderStatusSpan.removeClass();
                orderStatusSpan.addClass('text-xs font-black px-2.5 py-1 rounded-full border uppercase');
                
                const statusColors = {
                    'pending': 'bg-yellow-100 text-yellow-800 border-yellow-200',
                    'menunggu verifikasi': 'bg-orange-100 text-orange-800 border-orange-200',
                    'ditolak': 'bg-red-100 text-red-800 border-red-200',
                    'diproses': 'bg-blue-100 text-blue-800 border-blue-200',
                    'cuci': 'bg-cyan-100 text-cyan-800 border-cyan-200',
                    'setrika': 'bg-amber-100 text-amber-800 border-amber-200',
                    'selesai': 'bg-green-100 text-green-800 border-green-200',
                    'siap diambil': 'bg-purple-100 text-purple-800 border-purple-200',
                    'sudah diambil': 'bg-gray-100 text-gray-600 border-gray-200',
                };
                orderStatusSpan.addClass(statusColors[details.status_pesanan] || 'bg-gray-100 text-gray-600 border-gray-200');
                
                $('#detail-order-total').text(details.total);
                
                // Tampilkan Bukti Pembayaran jika ada
                const proofSection = $('#detail-proof-section');
                if (details.bukti_bayar) {
                    let proofSrc = details.bukti_bayar.startsWith('http') ? details.bukti_bayar : '../uploads/bukti_pembayaran/' + details.bukti_bayar;
                    $('#detail-proof-img').attr('src', proofSrc);
                    $('#detail-proof-link').attr('href', proofSrc);
                    proofSection.show();
                } else {
                    proofSection.hide();
                }
                
                // Render Action Buttons in Modal Footer
                const actionsContainer = $('#detail-modal-actions');
                actionsContainer.empty();
                
                if (details.status_bayar === 'Menunggu Verifikasi' && details.raw_metode === 'qris') {
                    // Show Confirm & Reject buttons
                    actionsContainer.append(`
                        <div class="flex gap-2 w-full mt-2">
                            <form method="POST" action="admin.php" class="flex-1">
                                <input type="hidden" name="confirm_payment" value="1">
                                <input type="hidden" name="order_id" value="${details.id}">
                                <button type="submit" class="w-full py-3 bg-[#035D51] text-white font-bold rounded-full text-sm hover:scale-[1.02] active:scale-95 transition-all text-center">
                                    Konfirmasi Pembayaran
                                </button>
                            </form>
                            <form method="POST" action="admin.php" class="flex-1">
                                <input type="hidden" name="reject_payment" value="1">
                                <input type="hidden" name="order_id" value="${details.id}">
                                <button type="submit" class="w-full py-3 bg-red-600 text-white font-bold rounded-full text-sm hover:scale-[1.02] active:scale-95 transition-all text-center">
                                    Tolak Pembayaran
                                </button>
                            </form>
                        </div>
                        <button onclick="closeOrderDetailModal()" class="w-full py-2.5 bg-surface-container-high hover:bg-surface-variant text-on-surface-variant font-bold rounded-full text-xs transition-all text-center">
                            Tutup
                        </button>
                    `);
                } else {
                    // Just close button
                    actionsContainer.append(`
                        <button onclick="closeOrderDetailModal()" class="w-full py-3 bg-[#035D51] text-white font-bold rounded-full text-sm hover:scale-[1.02] active:scale-95 transition-all text-center">
                            Tutup
                        </button>
                    `);
                }
                
                // Show modal
                const modal = $('#order-detail-modal');
                modal.removeClass('hidden');
                setTimeout(() => {
                    modal.removeClass('opacity-0');
                    modal.find('> div').removeClass('scale-95').addClass('scale-100');
                }, 50);
            }

            let shouldReloadOnDetailClose = false;

            function closeOrderDetailModal() {
                const modal = $('#order-detail-modal');
                modal.addClass('opacity-0');
                modal.find('> div').removeClass('scale-100').addClass('scale-95');
                setTimeout(() => {
                    modal.addClass('hidden');
                    if (shouldReloadOnDetailClose) {
                        shouldReloadOnDetailClose = false;
                        window.location.reload();
                    }
                }, 300);
            }

            function refreshDashboard() {
                window.location.reload();
            }

            function playNotificationSound() {
                try {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    
                    // Nada ke-1
                    let osc1 = audioCtx.createOscillator();
                    let gain1 = audioCtx.createGain();
                    osc1.type = 'sine';
                    osc1.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
                    gain1.gain.setValueAtTime(0.15, audioCtx.currentTime);
                    gain1.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.6);
                    osc1.connect(gain1);
                    gain1.connect(audioCtx.destination);
                    osc1.start();
                    osc1.stop(audioCtx.currentTime + 0.6);

                    // Nada ke-2 (sedikit jeda)
                    setTimeout(() => {
                        let osc2 = audioCtx.createOscillator();
                        let gain2 = audioCtx.createGain();
                        osc2.type = 'sine';
                        osc2.frequency.setValueAtTime(880.00, audioCtx.currentTime); // A5
                        gain2.gain.setValueAtTime(0.15, audioCtx.currentTime);
                        gain2.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.8);
                        osc2.connect(gain2);
                        gain2.connect(audioCtx.destination);
                        osc2.start();
                        osc2.stop(audioCtx.currentTime + 0.8);
                    }, 120);
                } catch (e) {
                    console.error("Web Audio API not supported or error:", e);
                }
            }

            // Jalankan polling setiap 5 detik
            setInterval(checkNewOrders, 5000);

            // --- SCROLL & TAB RESTORATION ON REFRESH ---
            window.addEventListener('beforeunload', () => {
                let activeTab = 'dashboard';
                if (!$('#section-pesanan').hasClass('hidden')) activeTab = 'pesanan';
                else if (!$('#section-pelanggan').hasClass('hidden')) activeTab = 'pelanggan';
                
                sessionStorage.setItem('adminActiveTab', activeTab);
                sessionStorage.setItem('adminScrollPosition', window.scrollY);
            });

            $(document).ready(function() {
                const hash = window.location.hash.substring(1);
                const savedTab = sessionStorage.getItem('adminActiveTab');
                const savedScroll = sessionStorage.getItem('adminScrollPosition');

                let targetTab = 'dashboard';
                if (['dashboard', 'pesanan', 'pelanggan'].includes(hash)) {
                    targetTab = hash;
                } else if (savedTab && ['dashboard', 'pesanan', 'pelanggan'].includes(savedTab)) {
                    targetTab = savedTab;
                }

                switchTab(targetTab);
                sessionStorage.removeItem('adminActiveTab');

                if (savedScroll) {
                    setTimeout(() => {
                        window.scrollTo(0, parseInt(savedScroll));
                        sessionStorage.removeItem('adminScrollPosition');
                    }, 100);
                }
            });
        </script>
    <?php endif; ?>

</body>

</html>