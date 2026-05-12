<?php
/**
 * ═══════════════════════════════════════════════════════════════
 * index.php — Laughndry Landing Page (Main Entry)
 * ═══════════════════════════════════════════════════════════════
 *
 * HOW TO RUN:
 * 1. Place the entire "Laughndry" folder inside your XAMPP htdocs directory.
 * 2. Start Apache from the XAMPP Control Panel.
 * 3. Open http://localhost/Laughndry/ in your browser.
 *
 * FILE STRUCTURE:
 *   Laughndry/
 *   ├── index.php      ← This file (main page)
 *   ├── header.php      ← Reusable <head>, navbar
 *   ├── footer.php      ← Reusable footer, CTA, scripts
 *   ├── data.php        ← All dynamic content arrays
 *   └── style.css       ← Custom animations & styles
 */

/*tes*/
/*tes*/


// Load all data arrays
require_once __DIR__ . '/data.php';

// Render the header (doctype, head, navbar)
require_once __DIR__ . '/header.php';
?>

<!-- ═══════════════════════════ HERO SECTION ═══════════════════════════ -->
<header
    class="relative overflow-hidden pt-20 sm:pt-16 md:pt-12 pb-16 sm:pb-24 px-4 sm:px-8 max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center">
    <div class="z-10">
        <span
            class="inline-block px-4 py-1.5 rounded-full bg-primary-fixed text-primary text-xs font-black tracking-widest mb-6 hero-text-animate">
            #LAUNDRY TERBAIK
        </span>

        <h1
            class="text-4xl sm:text-5xl md:text-6xl font-black text-primary leading-[1.1] tracking-tight mb-6 hero-text-animate-delay-1">
            Cuci numpuk? Ketawain aja, bereskan cucianmu di Laughndry!
        </h1>

        <p
            class="text-base sm:text-lg text-on-surface-variant mb-8 sm:mb-10 max-w-lg leading-relaxed hero-text-animate-delay-2">
            Cucian yang bikin stress jadi beres!! Nikmati waktu luangmu, biar kami yg urus sisanya.
        </p>

        <div class="flex flex-wrap items-center gap-4 hero-text-animate-delay-3">
            <a href="https://wa.me/6285220966656" target="_blank"
                class="inline-flex items-center gap-2 bg-[#035D51] text-white px-6 py-3 sm:px-7 sm:py-3.5 rounded-full font-black text-base shadow-xl shadow-[#035D51]/20 hover:scale-105 active:scale-95 transition-all">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                    <path
                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                </svg>
                Hubungi WhatsApp
            </a>
            <a href="harga.php"
                class="inline-flex items-center gap-2 bg-transparent border-2 border-primary text-primary px-6 py-2.5 sm:px-7 sm:py-3 rounded-full font-bold text-base hover:bg-primary/5 active:scale-95 transition-all">
                Lihat Harga <span class="material-symbols-outlined text-lg">arrow_forward_ios</span>
            </a>
        </div>
    </div>

    <div class="relative hero-image-animate">
        <div class="absolute inset-0 bg-primary-container rounded-[3rem] -rotate-3 z-0"></div>
        <img alt="Laughndry Experience"
            class="relative z-10 w-full aspect-[4/5] object-cover rounded-[2.5rem] shadow-2xl grayscale-[0.2] hover:grayscale-0 transition-all duration-700"
            src=" assets/gambar/mesin.png ">
    </div>
</header>


<!-- ═══════════════════════════ TENTANG SECTION ═══════════════════════════ -->
<section class="py-16 sm:py-24 bg-surface-container-low" id="tentang">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">

        <div class="text-center mb-12 sm:mb-16 reveal">
            <span class="text-secondary-container font-black tracking-[0.2em] text-sm mb-4 block">TENTANG</span>
            <h2 class="text-3xl sm:text-4xl font-black text-primary">Lebih dari sekadar cuci baju</h2><br />
            <h1> "Berawal dari layanan coin laundry terpercaya, Laughndry kini hadir
                dengan layanan full service untuk menjawab semua kebutuhan pakaian
                bersih Anda. Dengan fasilitas mesin modern berkapasitas besar, area
                yang bersih, dan pelayanan yang ramah, kami berkomitmen
                menghadirkan solusi cuci yang cepat, wangi, rapi, dan tepat waktu.
                Serahkan masalah cucian Anda kepada kami." </h1>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 sm:gap-8 stagger-children">
            <?php foreach ($about_cards as $card): ?>
                <div class="bg-surface-container-lowest p-8 sm:p-10 rounded-3xl card-hover stagger-item">
                    <div
                        class="w-16 h-16 bg-secondary-fixed flex items-center justify-center rounded-full mb-8 icon-bounce">
                        <span class="material-symbols-outlined text-secondary text-3xl"><?= $card['icon'] ?></span>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-4"><?= $card['title'] ?></h3>
                    <p class="text-on-surface-variant leading-relaxed"><?= $card['desc'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ═══════════════════════════ LAYANAN SECTION ═══════════════════════════ -->
<section class="py-16 sm:py-24 bg-surface" id="layanan">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">

        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 sm:mb-16 gap-4 sm:gap-6 reveal">
            <div>
                <span class="text-secondary-container font-black tracking-[0.2em] text-sm mb-4 block">LAYANAN
                    KAMI</span>
                <h2 class="text-3xl sm:text-4xl font-black text-primary">Pilih kenyamananmu</h2>
            </div>
            <p class="text-on-surface-variant max-w-sm"></p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 stagger-children">
            <?php foreach ($services as $svc): ?>
                <a href="harga.php#<?= isset($svc['id']) ? $svc['id'] : '' ?>"
                    class="group service-card relative block bg-surface-container-lowest p-8 sm:p-10 rounded-3xl card-hover stagger-item cursor-pointer">
                    <div
                        class="w-16 h-16 bg-secondary-fixed flex items-center justify-center rounded-full mb-8 icon-bounce">
                        <span class="material-symbols-outlined text-secondary text-3xl"><?= $svc['icon'] ?></span>
                    </div>
                    <h3 class="text-xl font-bold text-primary mb-4"><?= $svc['title'] ?></h3>
                    <p class="text-on-surface-variant leading-relaxed"><?= $svc['desc'] ?></p>
                    <!-- Hover arrow indicator -->
                    <span
                        class="material-symbols-outlined absolute bottom-6 right-6 text-2xl text-primary opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300">arrow_forward</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══════════════════════════ ULASAN SECTION ═══════════════════════════ -->
<section class="py-16 sm:py-24 bg-surface" id="ulasan">
    <div class="max-w-7xl mx-auto px-4 sm:px-8">

        <div class="text-center mb-12 sm:mb-16 reveal">
            <span class="text-secondary-container font-black tracking-[0.2em] text-sm mb-4 block">KATA MEREKA</span>
            <h2 class="text-3xl sm:text-4xl font-black text-primary">Apa Kata Pelanggan Tentang Laughndry</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 stagger-children">
            <?php foreach ($testimonials as $t): ?>
                <div
                    class="bg-surface-container-lowest p-6 sm:p-8 rounded-3xl shadow-sm border border-outline-variant/10 testimonial-card stagger-item">
                    <!-- Stars -->
                    <div class="flex text-secondary-container mb-4">
                        <?php for ($i = 0; $i < $t['stars']; $i++): ?>
                            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1;">star</span>
                        <?php endfor; ?>
                    </div>

                    <p class="text-on-surface-variant mb-6 sm:mb-8 italic leading-relaxed">"<?= $t['quote'] ?>"</p>

                    <div class="flex items-center gap-4">
                        <div
                            class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-secondary-container flex items-center justify-center font-bold text-on-secondary-fixed text-sm">
                            <?= $t['initials'] ?>
                        </div>
                        <div>
                            <p class="font-bold text-primary"><?= $t['name'] ?></p>
                            <p class="text-xs text-on-surface-variant"><?= $t['role'] ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══════════════════════════ FAQ SECTION ═══════════════════════════ -->
<section class="py-16 sm:py-24 bg-surface-container-low" id="faq">
    <div class="max-w-4xl mx-auto px-4 sm:px-8">
        <div class="mb-12 sm:mb-16 reveal">
            <h2 class="text-3xl sm:text-4xl font-black text-primary">Frequently<br>Asked Questions</h2>
        </div>

        <div class="flex flex-col gap-4 stagger-children">
            <?php foreach ($faqs as $index => $faq): ?>
                <div class="bg-surface-container-lowest rounded-3xl shadow-sm overflow-hidden stagger-item faq-item"
                    data-index="<?= $index ?>">
                    <button
                        class="faq-btn w-full px-6 py-5 sm:px-8 sm:py-6 flex justify-between items-center text-left focus:outline-none">
                        <span class="font-bold text-primary text-base sm:text-lg pr-4"><?= $faq['q'] ?></span>
                        <div
                            class="w-8 h-8 rounded-full bg-surface-container flex items-center justify-center shrink-0 transition-transform duration-300 faq-icon text-secondary-container font-black">
                            +
                        </div>
                    </button>
                    <div class="faq-content h-0 overflow-hidden transition-all duration-300 px-6 sm:px-8">
                        <div class="pb-6 text-on-surface-variant leading-relaxed text-sm sm:text-base">
                            <?= $faq['a'] ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<?php
// Render the footer (CTA banner, footer content, scripts)
require_once __DIR__ . '/footer.php';
?>