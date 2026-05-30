<?php
/**
 * header.php — Top of every page.
 *
 * Outputs <!DOCTYPE html>, <head>, opening <body>, and the sticky navbar.
 * Expects $nav_links to be available (included from data.php before this file).
 */
$is_harga = basename($_SERVER['PHP_SELF']) === 'harga.php';
$logo_href = $is_harga ? 'index.php#' : '#';
$order_href = $is_harga ? 'index.php#layanan' : '#layanan';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Laughndry — Laundry Coin Terbaik</title>
    <link rel="icon" type="image/png" href="assets/gambar/LOGO.png?v=2" />
    <?php
    $canonical_domain = "https://laughndry.my.id";
    $current_script = basename($_SERVER['SCRIPT_NAME']);
    $canonical_url = $canonical_domain . ($current_script === 'index.php' ? '/' : '/' . $current_script);
    ?>
    <link rel="canonical" href="<?= $canonical_url ?>" />
    <meta name="description"
        content="Laughndry, Laundry terdekat di Serpong yang buka 24 jam Setiap Hari untuk memenuhi kebutuhan Anda. Kami menawarkan proses pengerjaan cepat dengan hasil cucian yang dijamin bersih maksimal serta wangi tahan lama. Nikmati kepraktisan merawat pakaian Anda dengan harga yang terjangkau." />
    <meta name="google-site-verification" content="jrjv_8cdl7wdbPUNrrJ4XasTYkSmvF_5y_8pez5ppBY" />

    <!-- Tailwind CDN (with forms & container-queries plugins) -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <!-- Google Fonts: Inter + Material Symbols -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />

    <!-- Custom stylesheet -->
    <link href="style.css" rel="stylesheet" />

    <!-- Tailwind config (matches original colour tokens) -->
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

    <!-- ═══════════════════════════ NAVBAR ═══════════════════════════ -->
    <nav id="main-nav" class="bg-[#035D51] backdrop-blur-md fixed w-full top-0 z-50 transition-all duration-300">
        <div class="flex justify-between items-center w-full px-4 sm:px-8 py-2 sm:py-3 max-w-7xl mx-auto">

            <!-- Logo -->
            <a href="<?= $logo_href ?>" class="text-xl sm:text-2xl font-black text-emerald-900 flex items-center gap-2">
                <img src="assets/gambar/logo navbar.png" alt="Laughndry Logo" class="h-12 sm:h-14 w-auto" />
            </a>

            <!-- Desktop links -->
            <div class="hidden md:flex items-center gap-8">
                <?php foreach ($nav_links as $link):
                    $href = $link['href'];
                    if ($is_harga && $href !== 'harga.php') {
                        $href = 'index.php' . $href;
                    }
                    $is_active = ($is_harga && $link['label'] === 'Harga') || (!$is_harga && $link['active']);
                    ?>
                    <a href="<?= $href ?>" id="<?= $link['id'] ?>" class="nav-link-item hover:scale-105 transition-all duration-300 relative pb-1
                        <?= $is_active ? 'text-white font-bold active-link' : 'text-white/70 hover:text-white' ?>">
                        <?= $link['label'] ?>
                        <span
                            class="nav-indicator absolute left-0 bottom-0 w-full h-[2px] bg-white transition-transform origin-left duration-300 <?= $is_active ? 'scale-x-100' : 'scale-x-0' ?>"></span>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- CTA + hamburger -->
            <div class="flex items-center gap-3">
                <a href="<?= $order_href ?>"
                    class="hidden sm:inline-block bg-white text-[#035D51] px-6 py-2.5 rounded-full font-bold hover:scale-105 active:scale-95 transition-all">
                    Order Sekarang
                </a>
                <!-- Mobile hamburger -->
                <button id="mobile-menu-btn" class="md:hidden flex flex-col gap-[5px] p-2 group" aria-label="Menu">
                    <span
                        class="block w-6 h-[2.5px] bg-white rounded transition-all duration-300 origin-center group-[.open]:rotate-45 group-[.open]:translate-y-[7.5px]"></span>
                    <span
                        class="block w-6 h-[2.5px] bg-white rounded transition-all duration-300 group-[.open]:opacity-0"></span>
                    <span
                        class="block w-6 h-[2.5px] bg-white rounded transition-all duration-300 origin-center group-[.open]:-rotate-45 group-[.open]:-translate-y-[7.5px]"></span>
                </button>
            </div>
        </div>

        <!-- Mobile drawer -->
        <div id="mobile-menu"
            class="md:hidden max-h-0 overflow-hidden transition-all duration-500 ease-in-out bg-[#035D51]/95 backdrop-blur-lg">
            <div class="flex flex-col gap-2 px-6 py-4">
                <?php foreach ($nav_links as $link):
                    $href = $link['href'];
                    if ($is_harga && $href !== 'harga.php') {
                        $href = 'index.php' . $href;
                    }
                    $is_active = ($is_harga && $link['label'] === 'Harga') || (!$is_harga && $link['active']);
                    ?>
                    <a href="<?= $href ?>" class="mobile-nav-link py-3 px-4 rounded-xl text-lg font-semibold
                          <?= $is_active ? 'text-white bg-white/20' : 'text-white/70 hover:bg-white/10 hover:text-white' ?>
                          transition-colors duration-200">
                        <?= $link['label'] ?>
                    </a>
                <?php endforeach; ?>
                <a href="<?= $order_href ?>"
                    class="mt-2 text-center bg-white text-[#035D51] px-6 py-3 rounded-full font-bold">Order
                    Sekarang</a>
            </div>
        </div>
    </nav>

    <!-- Spacer to compensate for fixed nav -->
    <div class="h-[70px] sm:h-[80px]"></div>