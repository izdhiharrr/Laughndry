<?php
require_once __DIR__ . '/data.php';

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
            <a href="https://wa.me/6285220966656?text=Halo%20Admin,%20Saya%20ingin%20bertanya%20mengenai%20layanan%20laundry" target="_blank"
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

    <div class="relative hero-image-animate w-11/12 sm:w-4/5 md:w-11/12 lg:w-3/4 xl:w-2/3 mx-auto lg:mr-0">
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
    </div>
</section>

<!-- ═══════════════════════════ TRACKING SECTION ═══════════════════════════ -->
<section class="py-16 sm:py-24 bg-surface border-t border-outline-variant/15 reveal" id="lacak">
    <div class="max-w-4xl mx-auto px-4 sm:px-8">
        <div class="text-center mb-12">
            <span class="text-secondary-container font-black tracking-[0.2em] text-sm mb-4 block">LACAK STATUS</span>
            <h2 class="text-3xl sm:text-4xl font-black text-primary">Lacak Cucian Anda</h2>
            <p class="text-on-surface-variant mt-2 max-w-md mx-auto text-sm sm:text-base">Pantau status laundry secara real-time hanya dengan memasukkan nomor telepon Anda.</p>
        </div>

        <!-- Form Lacak -->
        <div class="bg-surface-container-low p-6 sm:p-8 rounded-[2.5rem] border border-outline-variant/20 shadow-sm mb-12 max-w-2xl mx-auto">
            <form onsubmit="trackOrder(event)" class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1 relative">
                    <span class="material-symbols-outlined text-outline absolute left-5 top-1/2 -translate-y-1/2">phone</span>
                    <input type="tel" id="track-phone" placeholder="Contoh: 081234567890" required
                        class="w-full bg-surface-container-lowest border-2 border-outline-variant/30 rounded-full pl-12 pr-6 py-4 focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-base font-semibold text-on-surface">
                </div>
                <button type="submit" id="track-submit-btn"
                    class="bg-primary text-on-primary px-8 py-4 rounded-full font-black text-base hover:scale-[1.02] active:scale-95 transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">search</span> Lacak Pesanan
                </button>
            </form>
        </div>

        <!-- Container Hasil Lacak -->
        <div id="track-result" class="hidden max-w-2xl mx-auto">
            <!-- Result items will go here dynamically -->
        </div>

        <!-- CSS tambahan untuk menyembunyikan scrollbar pada filter tab -->
        <style>
            #order-tabs::-webkit-scrollbar {
                display: none;
            }
        </style>
    </div>
</section>

<script>
function trackOrder(event) {
    event.preventDefault();
    const phoneInput = document.getElementById('track-phone');
    const phoneVal = phoneInput.value.trim();
    const resultDiv = document.getElementById('track-result');
    const submitBtn = document.getElementById('track-submit-btn');

    if (!phoneVal) return;

    // Tampilkan loader
    resultDiv.classList.remove('hidden');
    resultDiv.innerHTML = `
        <div class="py-16 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto mb-4"></div>
            <p class="text-sm text-on-surface-variant font-bold">Sedang mencari data pesanan...</p>
        </div>
    `;

    // Disable button sementara
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-70', 'cursor-not-allowed');

    // Ambil data dari API
    fetch('api/track_order.php?telepon=' + encodeURIComponent(phoneVal))
        .then(res => res.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');

            if (!data.success) {
                resultDiv.innerHTML = `
                    <div class="bg-error-container text-on-error-container p-6 rounded-[2rem] border border-error/15 text-center font-bold">
                        <span class="material-symbols-outlined text-3xl mb-2 block">error</span>
                        ${data.message || 'Gagal melacak pesanan.'}
                    </div>
                `;
                return;
            }

            if (!data.orders || data.orders.length === 0) {
                resultDiv.innerHTML = `
                    <div class="bg-surface-container-low p-10 rounded-[2.5rem] border border-outline-variant/10 text-center">
                        <span class="material-symbols-outlined text-4xl text-on-surface-variant/80 mb-3 block">search_off</span>
                        <p class="font-black text-primary text-xl mb-1">Pesanan Tidak Ditemukan</p>
                        <p class="text-sm text-on-surface-variant leading-relaxed">Nomor telepon belum terdaftar atau Anda tidak memiliki riwayat pesanan.</p>
                    </div>
                `;
                return;
            }

            // Hitung jumlah pesanan per kategori status
            let countAll = data.orders.length;
            let countPending = data.orders.filter(o => o.status === 'pending').length;
            let countVerifikasi = data.orders.filter(o => o.status === 'menunggu verifikasi').length;
            let countDitolak = data.orders.filter(o => o.status === 'ditolak').length;
            let countDiproses = data.orders.filter(o => o.status === 'diproses').length;
            let countCuci = data.orders.filter(o => o.status === 'cuci').length;
            let countSetrika = data.orders.filter(o => o.status === 'setrika').length;
            let countSelesai = data.orders.filter(o => o.status === 'selesai').length;
            let countSiapDiambil = data.orders.filter(o => o.status === 'siap diambil').length;
            let countSudahDiambil = data.orders.filter(o => o.status === 'sudah diambil').length;

            // Tampilkan daftar pesanan dengan filter Tab
            let htmlContent = `
                <div class="flex justify-between items-center mb-4 px-2">
                    <h3 class="text-lg font-black text-primary flex items-center gap-2">
                        <span class="material-symbols-outlined">history</span> Riwayat Pesanan Anda
                    </h3>
                </div>
                
                <!-- Tab Filters (Horizontal Scrollable on Mobile) -->
                <div class="flex overflow-x-auto justify-start gap-2 mb-6 pb-2 w-full" id="order-tabs" style="scrollbar-width: none; -ms-overflow-style: none;">
                    <button onclick="filterOrders('all')" id="tab-all" class="px-4 py-2 rounded-full font-bold text-xs transition-all bg-[#035D51] text-white shadow-md shadow-[#035D51]/20 shrink-0">Semua (${countAll})</button>
                    <button onclick="filterOrders('pending')" id="tab-pending" class="px-4 py-2 rounded-full font-bold text-xs transition-all bg-surface-container-high text-on-surface-variant hover:bg-surface-variant shrink-0">Menunggu Pembayaran (${countPending})</button>
                    <button onclick="filterOrders('menunggu verifikasi')" id="tab-menunggu-verifikasi" class="px-4 py-2 rounded-full font-bold text-xs transition-all bg-surface-container-high text-on-surface-variant hover:bg-surface-variant shrink-0">Menunggu Verifikasi (${countVerifikasi})</button>
                    <button onclick="filterOrders('ditolak')" id="tab-ditolak" class="px-4 py-2 rounded-full font-bold text-xs transition-all bg-surface-container-high text-on-surface-variant hover:bg-surface-variant shrink-0">Ditolak (${countDitolak})</button>
                    <button onclick="filterOrders('diproses')" id="tab-diproses" class="px-4 py-2 rounded-full font-bold text-xs transition-all bg-surface-container-high text-on-surface-variant hover:bg-surface-variant shrink-0">Diproses (${countDiproses})</button>
                    <button onclick="filterOrders('cuci')" id="tab-cuci" class="px-4 py-2 rounded-full font-bold text-xs transition-all bg-surface-container-high text-on-surface-variant hover:bg-surface-variant shrink-0">Cuci (${countCuci})</button>
                    <button onclick="filterOrders('setrika')" id="tab-setrika" class="px-4 py-2 rounded-full font-bold text-xs transition-all bg-surface-container-high text-on-surface-variant hover:bg-surface-variant shrink-0">Setrika (${countSetrika})</button>
                    <button onclick="filterOrders('selesai')" id="tab-selesai" class="px-4 py-2 rounded-full font-bold text-xs transition-all bg-surface-container-high text-on-surface-variant hover:bg-surface-variant shrink-0">Selesai (${countSelesai})</button>
                    <button onclick="filterOrders('siap diambil')" id="tab-siap-diambil" class="px-4 py-2 rounded-full font-bold text-xs transition-all bg-surface-container-high text-on-surface-variant hover:bg-surface-variant shrink-0">Siap Diambil (${countSiapDiambil})</button>
                    <button onclick="filterOrders('sudah diambil')" id="tab-sudah-diambil" class="px-4 py-2 rounded-full font-bold text-xs transition-all bg-surface-container-high text-on-surface-variant hover:bg-surface-variant shrink-0">Sudah Diambil (${countSudahDiambil})</button>
                </div>
                
                <div class="flex flex-col gap-6">`;

            data.orders.forEach(order => {
                // Konfigurasi badge status
                const statusColors = {
                    'pending': 'bg-yellow-100 text-yellow-800 border-yellow-200',
                    'menunggu verifikasi': 'bg-orange-100 text-orange-800 border-orange-200',
                    'ditolak': 'bg-red-100 text-red-800 border-red-200',
                    'diproses': 'bg-blue-100 text-blue-800 border-blue-200',
                    'cuci': 'bg-cyan-100 text-cyan-800 border-cyan-200',
                    'setrika': 'bg-amber-100 text-amber-800 border-amber-200',
                    'selesai': 'bg-green-100 text-green-800 border-green-200',
                    'siap diambil': 'bg-purple-100 text-purple-800 border-purple-200',
                    'sudah diambil': 'bg-zinc-100 text-zinc-600 border-zinc-200',
                };
                const statusTexts = {
                    'pending': 'MENUNGGU PEMBAYARAN',
                    'menunggu verifikasi': 'MENUNGGU VERIFIKASI',
                    'ditolak': 'PESANAN DITOLAK',
                    'diproses': 'DIPROSES',
                    'cuci': 'CUCI',
                    'setrika': 'SETRIKA',
                    'selesai': 'SELESAI',
                    'siap diambil': 'SIAP DIAMBIL',
                    'sudah diambil': 'SUDAH DIAMBIL'
                };
                const badgeClass = statusColors[order.status] || 'bg-zinc-100 text-zinc-600 border-zinc-200';
                const statusText = statusTexts[order.status] || order.status.toUpperCase();
                
                // Formulasi harga rupiah
                const formattedHarga = 'Rp ' + parseInt(order.total_harga).toLocaleString('id-ID');
                
                // Formulasi tanggal Indonesia
                const orderDate = new Date(order.created_at);
                const formattedDate = orderDate.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                htmlContent += `
                    <div class="order-card bg-surface-container-lowest p-6 sm:p-8 rounded-[2.5rem] border border-outline-variant/10 shadow-sm flex flex-col gap-5 hover:shadow-md transition-all duration-300" data-group="${order.status}">
                        <!-- Header Card: ID Pesanan & Tanggal -->
                        <div class="flex flex-wrap justify-between items-center gap-2 pb-4 border-b border-outline-variant/10">
                            <div>
                                <span class="text-sm font-black text-primary block">ORDER ID #${order.id}</span>
                                <span class="text-xs text-on-surface-variant/70 font-semibold block mt-0.5">${formattedDate}</span>
                            </div>
                            <span class="text-xs font-black px-3.5 py-1.5 rounded-full border ${badgeClass} uppercase tracking-wider">
                                ${statusText}
                            </span>
                        </div>

                        <!-- Body Card: Pelanggan, Jenis Layanan & Detail Pesanan -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-on-surface-variant/60 uppercase tracking-wider">Pelanggan</span>
                                <span class="font-bold text-on-surface">${order.customer_nama}</span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-black text-on-surface-variant/60 uppercase tracking-wider">Jenis Layanan</span>
                                <span class="font-bold text-primary">${order.kategori_list || '-'}</span>
                            </div>
                            <div class="md:col-span-2 flex flex-col gap-1 mt-1">
                                <span class="text-[10px] font-black text-on-surface-variant/60 uppercase tracking-wider">Detail Item Pesanan</span>
                                <p class="text-on-surface-variant font-medium bg-surface-container-low/50 p-3.5 rounded-2xl border border-outline-variant/5 text-xs leading-relaxed">${order.item_list || '-'}</p>
                            </div>
                            ${order.status === 'ditolak' && order.alasan_tolak ? `
                             <div class="md:col-span-2 flex flex-col gap-1.5 mt-2 p-4 bg-red-50 text-red-800 rounded-2xl border border-red-200 text-xs">
                                 <span class="text-[10px] font-black uppercase tracking-wider text-red-600">Alasan Penolakan Admin</span>
                                 <p class="font-bold leading-relaxed">${order.alasan_tolak}</p>
                             </div>
                             ` : ''}
                        </div>

                        <!-- Footer Card: Qty & Total Harga -->
                        <div class="flex justify-between items-center pt-4 border-t border-outline-variant/10 mt-1">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-on-surface-variant/60 uppercase tracking-wider">Total Item (Qty)</span>
                                <span class="font-bold text-on-surface">${order.total_qty || 0} Pcs/Kg</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-black text-on-surface-variant/60 uppercase tracking-wider block">Harga Total</span>
                                <span class="text-lg font-black text-secondary">${formattedHarga}</span>
                            </div>
                        </div>
                    </div>
                `;
            });

            htmlContent += `
                </div>
                <!-- Empty state for tabs -->
                <div id="tab-empty-state" class="hidden bg-surface-container-low p-10 rounded-[2.5rem] border border-outline-variant/10 text-center my-6">
                    <span class="material-symbols-outlined text-4xl text-on-surface-variant/80 mb-3 block">history_toggle_off</span>
                    <p class="font-black text-primary text-xl mb-1">Tidak Ada Pesanan</p>
                    <p class="text-sm text-on-surface-variant leading-relaxed" id="tab-empty-text">Tidak ada pesanan di kategori ini.</p>
                </div>
            `;
            
            // Definisikan fungsi filter global
            window.filterOrders = function(group) {
                const tabs = ['all', 'pending', 'menunggu verifikasi', 'ditolak', 'diproses', 'cuci', 'setrika', 'selesai', 'siap diambil', 'sudah diambil'];
                
                // Reset styling semua tab
                tabs.forEach(t => {
                    const tabId = 'tab-' + t.replace(' ', '-');
                    const tab = document.getElementById(tabId);
                    if (tab) {
                        tab.className = "px-4 py-2 rounded-full font-bold text-xs transition-all bg-surface-container-high text-on-surface-variant hover:bg-surface-variant shrink-0";
                    }
                });
                
                // Set aktif styling tab terpilih
                const activeTabId = 'tab-' + group.replace(' ', '-');
                const activeTab = document.getElementById(activeTabId);
                if (activeTab) {
                    activeTab.className = "px-4 py-2 rounded-full font-bold text-xs transition-all bg-[#035D51] text-white shadow-md shadow-[#035D51]/20 shrink-0";
                }
                
                // Saring kartu pesanan
                let visibleCount = 0;
                document.querySelectorAll('.order-card').forEach(card => {
                    if (group === 'all' || card.getAttribute('data-group') === group) {
                        card.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        card.classList.add('hidden');
                    }
                });
                
                // Tampilkan pesan kosong jika tidak ada pesanan di tab tsb
                const emptyState = document.getElementById('tab-empty-state');
                if (visibleCount === 0) {
                    emptyState.classList.remove('hidden');
                    document.getElementById('tab-empty-text').innerText = "Tidak ada pesanan dengan status \"" + group + "\" saat ini.";
                } else {
                    emptyState.classList.add('hidden');
                }
            };

            resultDiv.innerHTML = htmlContent;
        })
        .catch(err => {
            console.error(err);
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
            resultDiv.innerHTML = `
                <div class="bg-error-container text-on-error-container p-6 rounded-[2rem] border border-error/15 text-center font-bold">
                    <span class="material-symbols-outlined text-3xl mb-2 block">error</span>
                    Terjadi kesalahan koneksi internet. Silakan coba lagi.
                </div>
            `;
        });
}
</script>

<?php
// Render the footer (CTA banner, footer content, scripts)
require_once __DIR__ . '/footer.php';
?>