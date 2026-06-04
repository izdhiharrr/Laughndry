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
    $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
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
    // Fetch pesanan dengan JOIN ke customers dan order_items
    $orders = $pdo->query("
        SELECT 
            o.id,
            c.nama AS customer_nama,
            c.id AS customer_id,
            c.telepon AS customer_telepon,
            o.total_harga,
            o.metode_bayar,
            o.status,
            o.created_at,
            GROUP_CONCAT(DISTINCT oi.kategori SEPARATOR ', ') AS kategori_list,
            GROUP_CONCAT(CONCAT(oi.nama_item, ' (x', oi.qty, ')') SEPARATOR ', ') AS item_list,
            SUM(oi.qty) AS total_qty
        FROM orders o
        JOIN customers c ON o.customer_id = c.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ")->fetchAll();

    // Fetch pelanggan dengan status pesanan terbaru
    $customers = $pdo->query("
        SELECT 
            c.*,
            (SELECT o.status FROM orders o WHERE o.customer_id = c.id ORDER BY o.created_at DESC LIMIT 1) AS latest_order_status
        FROM customers c
        ORDER BY c.id ASC
    ")->fetchAll();

    // Stats
    $total_orders = count($orders);
    $total_customers = count($customers);
    $total_revenue = $pdo->query("SELECT COALESCE(SUM(total_harga), 0) FROM orders")->fetchColumn();
    $pending_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();

    // Chart data: 4 minggu terakhir
    $chart_data = $pdo->query("
        SELECT 
            YEARWEEK(created_at, 1) AS minggu,
            MIN(DATE(created_at)) AS start_date,
            COUNT(*) AS total_pesanan,
            COALESCE(SUM(total_harga), 0) AS total_revenue
        FROM orders
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
        #orders-table thead th,
        #customers-table thead th {
            background-color: #eeeeee !important; /* surface-container */
            color: #00433a !important; /* primary */
            border-bottom: 2px solid rgba(111, 121, 118, 0.3) !important;
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
                    <div
                        class="w-20 h-20 bg-secondary-container rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg shadow-secondary-container/30">
                        <span class="material-symbols-outlined text-4xl text-on-secondary-fixed">admin_panel_settings</span>
                    </div>
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

                <div
                    class="flex flex-col md:flex-row md:items-end justify-between mb-12 sm:mb-16 gap-4 border-b border-outline-variant/20 pb-8">
                    <div>
                        <span
                            class="text-secondary-container font-black tracking-[0.2em] text-sm mb-4 block">DASHBOARD</span>
                        <h1 class="text-3xl sm:text-4xl font-black text-primary">Admin Panel</h1>
                        <p class="text-on-surface-variant mt-2 max-w-xl">Selamat datang,
                            <b><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></b>. Kelola pesanan dan data
                            pelanggan Laughndry.</p>
                    </div>
                    <a href="?logout=true"
                        class="inline-flex items-center justify-center gap-2 bg-error-container text-on-error-container px-6 py-3 rounded-full font-bold hover:bg-error hover:text-on-error transition-colors">
                        <span class="material-symbols-outlined text-xl">logout</span> Logout
                    </a>
                </div>
                <!-- ═══════════ NAVIGATION TABS ═══════════ -->
                <div class="flex gap-4 mb-8 border-b border-outline-variant/20 pb-4">
                    <button id="btn-tab-pesanan" onclick="switchTab('pesanan')" 
                        class="flex-1 sm:flex-initial justify-center px-6 py-3 font-bold rounded-full transition-all text-sm flex items-center gap-2 bg-primary text-on-primary shadow-lg shadow-primary/20">
                        <span class="material-symbols-outlined text-lg">receipt_long</span>
                        Pesanan
                    </button>
                    <button id="btn-tab-pelanggan" onclick="switchTab('pelanggan')" 
                        class="flex-1 sm:flex-initial justify-center px-6 py-3 font-bold rounded-full transition-all text-sm flex items-center gap-2 bg-surface-container-high text-on-surface-variant hover:bg-surface-variant">
                        <span class="material-symbols-outlined text-lg">group</span>
                        Pelanggan
                    </button>
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
                        <p class="text-2xl font-black text-secondary">Rp <?= number_format($total_revenue, 0, ',', '.') ?>
                        </p>
                    </div>
                </div>

                <!-- ═══════════ CHART CARDS ═══════════ -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-12">
                    <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-outline-variant/10">
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
                    <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-outline-variant/10">
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

                <!-- ═══════════ PESANAN TAB CONTENT ═══════════ -->
                <div id="tab-content-pesanan" class="tab-content transition-all duration-300">
                    <div class="mb-16">
                        <div class="text-center mb-8">
                            <h2 class="text-2xl sm:text-3xl font-black text-primary mb-2">Pesanan</h2>
                            <p class="text-on-surface-variant mb-6">Daftar pesanan dari pelanggan.</p>
                            <div class="flex justify-center">
                                <div class="relative inline-block text-left group" tabindex="0">
                                    <button
                                        class="inline-flex items-center gap-2 bg-primary-container text-on-primary px-6 py-2.5 rounded-full font-bold text-sm hover:scale-105 active:scale-95 transition-all shadow-md focus:outline-none focus:ring-4 focus:ring-primary/20">
                                        <span class="material-symbols-outlined text-lg">download</span> Download Laporan
                                        <span
                                            class="material-symbols-outlined text-lg transition-transform group-focus-within:rotate-180 group-hover:rotate-180">expand_more</span>
                                    </button>
                                    <div
                                        class="absolute left-1/2 -translate-x-1/2 mt-2 w-48 bg-surface-container-lowest border border-outline-variant/20 rounded-2xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible group-focus-within:opacity-100 group-focus-within:visible transition-all duration-200 z-50">
                                        <div class="py-2 flex flex-col text-center">
                                            <a href="export_pdf.php?period=monthly" target="_blank"
                                                class="px-4 py-2.5 text-sm text-on-surface-variant hover:bg-surface-container-low hover:text-primary font-bold transition-colors border-b border-outline-variant/10">Bulanan (PDF)</a>
                                            <a href="export_excel.php?period=monthly" target="_blank"
                                                class="px-4 py-2.5 text-sm text-on-surface-variant hover:bg-surface-container-low hover:text-secondary font-bold transition-colors border-b border-outline-variant/10">Bulanan (XLSX)</a>
                                            <a href="export_pdf.php?period=yearly" target="_blank"
                                                class="px-4 py-2.5 text-sm text-on-surface-variant hover:bg-surface-container-low hover:text-primary font-bold transition-colors border-b border-outline-variant/10">Tahunan (PDF)</a>
                                            <a href="export_excel.php?period=yearly" target="_blank"
                                                class="px-4 py-2.5 text-sm text-on-surface-variant hover:bg-surface-container-low hover:text-secondary font-bold transition-colors">Tahunan (XLSX)</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-surface-container-lowest rounded-[2rem] p-6 sm:p-8 shadow-md border border-outline-variant/10">
                            <!-- Table -->
                            <table id="orders-table" class="w-full text-left border-collapse">
                                    <thead class="sticky top-0 z-10">
                                        <tr
                                            class="bg-surface-container text-primary border-b-2 border-outline-variant/30 text-sm shadow-sm">
                                            <th class="p-4 font-bold whitespace-nowrap">ID</th>
                                            <th class="p-4 font-bold whitespace-nowrap">Tanggal</th>
                                            <th class="p-4 font-bold whitespace-nowrap">Pelanggan</th>
                                            <th class="p-4 font-bold whitespace-normal max-w-[110px]" style="width: 110px; min-width: 110px; max-width: 110px;">Jenis Layanan</th>
                                            <th class="p-4 font-bold whitespace-normal max-w-[160px]" style="width: 160px; min-width: 160px; max-width: 160px;">Detail Item</th>
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
                                                <td colspan="10"
                                                    class="p-8 text-center text-on-surface-variant bg-surface-container-low/30 italic">
                                                    Belum ada pesanan.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($orders as $order): ?>
                                                <?php
                                                // Status badge colors
                                                $status_colors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                    'diproses' => 'bg-blue-100 text-blue-800',
                                                    'dicuci' => 'bg-cyan-100 text-cyan-800',
                                                    'selesai' => 'bg-green-100 text-green-800',
                                                    'diambil' => 'bg-gray-100 text-gray-600',
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
                                                ?>
                                                <tr class="hover:bg-surface-container-low transition-colors">
                                                    <td class="p-4 font-bold text-primary">#<?= $order['id'] ?></td>
                                                    <td class="p-4 text-on-surface-variant text-sm whitespace-nowrap" data-order="<?= strtotime($order['created_at']) ?>">
                                                        <?= date('d M Y, H:i', strtotime($order['created_at'])) ?></td>
                                                    <td class="p-4 font-medium text-primary">
                                                        <?= htmlspecialchars($order['customer_nama']) ?>
                                                    </td>
                                                    <td class="p-4 text-on-surface-variant text-sm max-w-[110px] whitespace-normal break-words" style="width: 110px; min-width: 110px; max-width: 110px;">
                                                        <?= htmlspecialchars($order['kategori_list'] ?? '-') ?></td>
                                                    <td class="p-4 text-on-surface-variant text-sm max-w-[160px] whitespace-normal break-words" style="width: 160px; min-width: 160px; max-width: 160px;">
                                                        <?= htmlspecialchars($order['item_list'] ?? '-') ?></td>
                                                    <td class="p-4 text-on-surface-variant font-medium"><?= $order['total_qty'] ?? 0 ?>
                                                    </td>
                                                    <td class="p-4 font-bold text-secondary">Rp
                                                        <?= number_format($order['total_harga'], 0, ',', '.') ?></td>
                                                    <td class="p-4 text-sm"><?= $metode_label ?></td>
                                                    <td class="p-4">
                                                        <!-- Status Update Form -->
                                                        <form method="POST" action="admin.php" class="inline">
                                                            <input type="hidden" name="update_status" value="1">
                                                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                            <select name="new_status" onchange="this.form.submit()"
                                                                class="text-xs font-bold pl-4 pr-8 py-2 rounded-full border-0 cursor-pointer min-w-[120px] <?= $badge ?> focus:ring-2 focus:ring-primary/20">
                                                                <?php foreach (['pending', 'diproses', 'dicuci', 'selesai', 'diambil'] as $s): ?>
                                                                    <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>>
                                                                        <?= ucfirst($s) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </form>
                                                    </td>
                                                    <td class="p-4 text-center">
                                                         <button type="button"
                                                             onclick="deleteOrder(<?= $order['id'] ?>, this)"
                                                             class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-bold bg-surface border-2 border-outline-variant/30 text-on-surface-variant rounded-lg hover:bg-error hover:text-on-error hover:border-error transition-colors">
                                                             <span class="material-symbols-outlined text-base">delete</span>
                                                         </button>
                                                     </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                        </div>
                    </div>
                </div>

                <!-- ═══════════ PELANGGAN TAB CONTENT ═══════════ -->
                <div id="tab-content-pelanggan" class="tab-content hidden transition-all duration-300">
                    <div class="mb-16">
                        <div class="text-center mb-8">
                            <h2 class="text-2xl sm:text-3xl font-black text-primary mb-2">Profil Pelanggan</h2>
                            <p class="text-on-surface-variant mb-6">Daftar pelanggan yang terdaftar.</p>
                            <div class="flex justify-center">
                                <div class="relative inline-block text-left group" tabindex="0">
                                    <button
                                        class="inline-flex items-center gap-2 bg-primary-container text-on-primary px-6 py-2.5 rounded-full font-bold text-sm hover:scale-105 active:scale-95 transition-all shadow-md focus:outline-none focus:ring-4 focus:ring-primary/20">
                                        <span class="material-symbols-outlined text-lg">download</span> Download Laporan
                                        <span
                                            class="material-symbols-outlined text-lg transition-transform group-focus-within:rotate-180 group-hover:rotate-180">expand_more</span>
                                    </button>
                                    <div
                                        class="absolute left-1/2 -translate-x-1/2 mt-2 w-48 bg-surface-container-lowest border border-outline-variant/20 rounded-2xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible group-focus-within:opacity-100 group-focus-within:visible transition-all duration-200 z-50">
                                        <div class="py-2 flex flex-col text-center">
                                            <a href="export_customers_pdf.php?period=monthly" target="_blank"
                                                class="px-4 py-2.5 text-sm text-on-surface-variant hover:bg-surface-container-low hover:text-primary font-bold transition-colors border-b border-outline-variant/10">Bulanan (PDF)</a>
                                            <a href="export_customers_excel.php?period=monthly" target="_blank"
                                                class="px-4 py-2.5 text-sm text-on-surface-variant hover:bg-surface-container-low hover:text-secondary font-bold transition-colors border-b border-outline-variant/10">Bulanan (XLSX)</a>
                                            <a href="export_customers_pdf.php?period=yearly" target="_blank"
                                                class="px-4 py-2.5 text-sm text-on-surface-variant hover:bg-surface-container-low hover:text-primary font-bold transition-colors border-b border-outline-variant/10">Tahunan (PDF)</a>
                                            <a href="export_customers_excel.php?period=yearly" target="_blank"
                                                class="px-4 py-2.5 text-sm text-on-surface-variant hover:bg-surface-container-low hover:text-secondary font-bold transition-colors">Tahunan (XLSX)</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="bg-surface-container-lowest rounded-[2rem] p-6 sm:p-8 shadow-md border border-outline-variant/10">
                            <!-- Table -->
                            <table id="customers-table" class="w-full text-left border-collapse">
                                    <thead class="sticky top-0 z-10">
                                        <tr
                                            class="bg-surface-container text-primary border-b-2 border-outline-variant/30 text-sm shadow-sm">
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
                                                <td colspan="6"
                                                    class="p-8 text-center text-on-surface-variant bg-surface-container-low/30 italic">
                                                    Belum ada pelanggan terdaftar.
                                                </td>
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
                                                             } else {
                                                                 $wa_msg = "Pesanan laundry anda telah dibuat dan saat ini sedang diproses";
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
                                                    <td class="p-4 text-on-surface-variant text-sm whitespace-normal" style="width: 120px; min-width: 120px; max-width: 120px;" data-order="<?= strtotime($cust['created_at']) ?>">
                                                        <?= date('d M Y', strtotime($cust['created_at'])) ?></td>
                                                    <td class="p-4 text-center" style="width: 100px; min-width: 100px; max-width: 100px;">
                                                         <button type="button"
                                                             onclick="deleteCustomer(<?= $cust['id'] ?>, '<?= htmlspecialchars($cust['nama'], ENT_QUOTES) ?>', this)"
                                                             class="px-4 py-2 text-sm font-bold bg-surface border-2 border-outline-variant/30 text-on-surface-variant rounded-lg hover:bg-error hover:text-on-error hover:border-error transition-colors inline-flex items-center gap-1">
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
                </div>

            </div>
        </section>

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

            function switchTab(tabName) {
                // Sembunyikan semua konten tab
                $('.tab-content').addClass('hidden');
                
                // Tampilkan konten tab yang aktif
                $('#tab-content-' + tabName).removeClass('hidden');
                
                // Reset styling tombol navigasi
                $('#btn-tab-pesanan').removeClass('bg-primary text-on-primary shadow-lg shadow-primary/20')
                                     .addClass('bg-surface-container-high text-on-surface-variant hover:bg-surface-variant');
                $('#btn-tab-pelanggan').removeClass('bg-primary text-on-primary shadow-lg shadow-primary/20')
                                       .addClass('bg-surface-container-high text-on-surface-variant hover:bg-surface-variant');
                
                // Aktifkan styling tombol yang terpilih
                $('#btn-tab-' + tabName).removeClass('bg-surface-container-high text-on-surface-variant hover:bg-surface-variant')
                                         .addClass('bg-primary text-on-primary shadow-lg shadow-primary/20');
                
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
        </script>
    <?php endif; ?>

</body>

</html>