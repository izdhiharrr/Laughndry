<?php
/**
 * admin.php — Halaman Admin
 * Hanya dapat diakses oleh admin (dengan mock login).
 */
session_start();

// Mock Login Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Hardcoded credentials for demonstration
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['is_admin'] = true;
    } else {
        $login_error = "Username atau password salah!";
    }
}

if (isset($_GET['logout'])) {
    unset($_SESSION['is_admin']);
    header("Location: admin.php");
    exit;
}

$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

// We no longer require header.php to avoid the navbar entirely.
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
                    <p class="text-on-surface-variant mt-2 max-w-xl">Kelola pesanan pelanggan dan data profil pelanggan Laughndry.</p>
                </div>
                <a href="?logout=true" class="inline-flex items-center justify-center gap-2 bg-error-container text-on-error-container px-6 py-3 rounded-full font-bold hover:bg-error hover:text-on-error transition-colors">
                    <span class="material-symbols-outlined text-xl">logout</span> Logout
                </a>
            </div>

            <!-- PESANAN TABLE -->
            <div class="mb-16">
                <div class="text-center mb-8">
                    <h2 class="text-2xl sm:text-3xl font-black text-primary mb-2">Pesanan</h2>
                    <p class="text-on-surface-variant">Daftar pesanan dari pelanggan.</p>
                </div>
                
                <div class="bg-surface-container-lowest rounded-[2rem] p-6 sm:p-8 shadow-md border border-outline-variant/10">
                    <!-- Table controls -->
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                        <div class="flex items-center gap-2 text-sm text-on-surface-variant font-medium">
                            Show 
                            <select class="bg-surface border-2 border-outline-variant/30 rounded-lg pl-3 pr-8 py-1.5 focus:outline-none focus:border-primary transition-colors cursor-pointer">
                                <option>10</option>
                                <option>25</option>
                                <option>50</option>
                            </select> 
                            entries
                        </div>
                        <div class="flex items-center gap-2 text-sm font-medium text-on-surface-variant w-full sm:w-auto">
                            Search: 
                            <input type="text" class="w-full sm:w-auto bg-surface border-2 border-outline-variant/30 rounded-lg px-4 py-1.5 focus:outline-none focus:border-primary transition-colors">
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto rounded-xl border border-outline-variant/20">
                        <table class="w-full text-left border-collapse min-w-[900px]">
                            <thead>
                                <tr class="bg-surface-container text-primary border-b-2 border-outline-variant/30 text-sm">
                                    <th class="p-4 font-bold whitespace-nowrap cursor-pointer hover:bg-surface-container-high transition-colors">ID User <span class="material-symbols-outlined text-[16px] align-text-bottom opacity-40">swap_vert</span></th>
                                    <th class="p-4 font-bold whitespace-nowrap cursor-pointer hover:bg-surface-container-high transition-colors">Jenis Layanan <span class="material-symbols-outlined text-[16px] align-text-bottom opacity-40">swap_vert</span></th>
                                    <th class="p-4 font-bold whitespace-nowrap cursor-pointer hover:bg-surface-container-high transition-colors">List Satuan <span class="material-symbols-outlined text-[16px] align-text-bottom opacity-40">swap_vert</span></th>
                                    <th class="p-4 font-bold whitespace-nowrap cursor-pointer hover:bg-surface-container-high transition-colors">Massa Barang <span class="material-symbols-outlined text-[16px] align-text-bottom opacity-40">swap_vert</span></th>
                                    <th class="p-4 font-bold whitespace-nowrap cursor-pointer hover:bg-surface-container-high transition-colors">Jumlah Barang <span class="material-symbols-outlined text-[16px] align-text-bottom opacity-40">swap_vert</span></th>
                                    <th class="p-4 font-bold whitespace-nowrap cursor-pointer hover:bg-surface-container-high transition-colors">Harga Total <span class="material-symbols-outlined text-[16px] align-text-bottom opacity-40">swap_vert</span></th>
                                    <th class="p-4 font-bold whitespace-nowrap cursor-pointer hover:bg-surface-container-high transition-colors">Status Pemesanan <span class="material-symbols-outlined text-[16px] align-text-bottom opacity-40">swap_vert</span></th>
                                    <th class="p-4 font-bold whitespace-nowrap cursor-pointer hover:bg-surface-container-high transition-colors text-center">Action <span class="material-symbols-outlined text-[16px] align-text-bottom opacity-40">swap_vert</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="8" class="p-8 text-center text-on-surface-variant bg-surface-container-low/30 italic">
                                        No data available in table
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4 text-sm text-on-surface-variant">
                        <div>Showing 0 to 0 of 0 entries</div>
                        <div class="flex gap-1">
                            <button class="px-4 py-2 rounded-lg border-2 border-outline-variant/30 text-outline-variant cursor-not-allowed bg-surface" disabled>Previous</button>
                            <button class="px-4 py-2 rounded-lg border-2 border-outline-variant/30 text-outline-variant cursor-not-allowed bg-surface" disabled>Next</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PROFIL PELANGGAN TABLE -->
            <div>
                <div class="text-center mb-8">
                    <h2 class="text-2xl sm:text-3xl font-black text-primary mb-2">Profil Pelanggan</h2>
                    <p class="text-on-surface-variant">Daftar pelanggan yang terdaftar.</p>
                </div>
                
                <div class="bg-surface-container-lowest rounded-[2rem] p-6 sm:p-8 shadow-md border border-outline-variant/10">
                    <!-- Table controls -->
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                        <div class="flex items-center gap-2 text-sm text-on-surface-variant font-medium">
                            Show 
                            <select class="bg-surface border-2 border-outline-variant/30 rounded-lg pl-3 pr-8 py-1.5 focus:outline-none focus:border-primary transition-colors cursor-pointer">
                                <option>10</option>
                                <option>25</option>
                                <option>50</option>
                            </select> 
                            entries
                        </div>
                        <div class="flex items-center gap-2 text-sm font-medium text-on-surface-variant w-full sm:w-auto">
                            Search: 
                            <input type="text" class="w-full sm:w-auto bg-surface border-2 border-outline-variant/30 rounded-lg px-4 py-1.5 focus:outline-none focus:border-primary transition-colors">
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto rounded-xl border border-outline-variant/20">
                        <table class="w-full text-left border-collapse min-w-[800px]">
                            <thead>
                                <tr class="bg-surface-container text-primary border-b-2 border-outline-variant/30 text-sm">
                                    <th class="p-4 font-bold whitespace-nowrap cursor-pointer hover:bg-surface-container-high transition-colors w-24">ID User <span class="material-symbols-outlined text-[16px] align-text-bottom opacity-40">swap_vert</span></th>
                                    <th class="p-4 font-bold whitespace-nowrap cursor-pointer hover:bg-surface-container-high transition-colors">Nama <span class="material-symbols-outlined text-[16px] align-text-bottom opacity-40">swap_vert</span></th>
                                    <th class="p-4 font-bold whitespace-nowrap cursor-pointer hover:bg-surface-container-high transition-colors">Alamat <span class="material-symbols-outlined text-[16px] align-text-bottom opacity-40">swap_vert</span></th>
                                    <th class="p-4 font-bold whitespace-nowrap cursor-pointer hover:bg-surface-container-high transition-colors">Nomor Telepon <span class="material-symbols-outlined text-[16px] align-text-bottom opacity-40">swap_vert</span></th>
                                    <th class="p-4 font-bold text-center w-24">Edit</th>
                                    <th class="p-4 font-bold text-center w-24">Delete</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/20">
                                <tr class="hover:bg-surface-container-low transition-colors group">
                                    <td class="p-4 text-on-surface-variant font-medium">1</td>
                                    <td class="p-4 font-bold text-primary">Administrator</td>
                                    <td class="p-4 text-on-surface-variant">Jl. Serpong Raya No. 1, Tangerang Selatan</td>
                                    <td class="p-4 text-on-surface-variant">081242133333</td>
                                    <td class="p-4 text-center">
                                        <button class="px-4 py-2 text-sm font-bold bg-secondary-container text-on-secondary-container rounded-lg hover:bg-secondary hover:text-on-secondary transition-colors">Edit</button>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button class="px-4 py-2 text-sm font-bold bg-surface border-2 border-outline-variant/30 text-on-surface-variant rounded-lg hover:bg-error hover:text-on-error hover:border-error transition-colors">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex flex-col sm:flex-row justify-between items-center mt-6 gap-4 text-sm text-on-surface-variant">
                        <div>Showing 1 to 1 of 1 entries</div>
                        <div class="flex gap-2">
                            <button class="px-4 py-2 rounded-lg border-2 border-outline-variant/30 text-on-surface-variant hover:bg-surface-container transition-colors">Previous</button>
                            <button class="px-4 py-2 rounded-lg bg-primary text-on-primary font-bold shadow-md shadow-primary/20">1</button>
                            <button class="px-4 py-2 rounded-lg border-2 border-outline-variant/30 text-on-surface-variant hover:bg-surface-container transition-colors">Next</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
<?php endif; ?>

</body>
</html>
