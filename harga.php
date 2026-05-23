<?php
require_once __DIR__ . '/data.php';

// Render the header (doctype, head, navbar)
require_once __DIR__ . '/header.php';
?>

<!-- ═══════════════════════════ HEADER PAGE ═══════════════════════════ -->
<section class="pt-20 sm:pt-10 pb-12 bg-surface-container-low">
    <div class="max-w-7xl mx-auto px-4 sm:px-8 text-left mt-0 sm:mt-2">
        <!-- Tombol Kembali -->
        <div class="mb-8">
            <a href="index.php"
                class="inline-flex items-center gap-2 bg-primary text-on-primary px-6 py-3 rounded-full font-bold hover:scale-105 active:scale-95 transition-all shadow-lg hover:shadow-xl group">
                <span
                    class="material-symbols-outlined text-base group-hover:-translate-x-1 transition-transform">arrow_back</span>
                Kembali
            </a>
        </div>
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-primary leading-tight mb-4">Detail Lengkap<br>Daftar
            Harga Laughndry</h1>
        <p class="text-on-surface-variant text-base sm:text-lg max-w-2xl">Transparansi harga untuk kualitas premium.
            Temukan paket
            layanan laundry terbaik yang dirancang khusus untuk kebutuhan gaya hidup Anda yang dinamis.
        </p>
    </div>
</section>

<!-- Icon Keranjang Kuning dengan Notifikasi -->
<div class="icon-container fixed bottom-6 right-6 z-50">
    <a href="#" id="cart-link"
        class="relative w-16 h-16 bg-secondary-fixed flex items-center justify-center rounded-full shadow-lg hover:scale-110 active:scale-95 transition-all cursor-pointer border border-secondary-fixed-dim/20">
        <span class="material-symbols-outlined text-secondary text-3xl">shopping_cart</span>
        <span id="cart-count"
            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold w-6 h-6 flex items-center justify-center rounded-full shadow-md hidden">0</span>
    </a>
</div>

<script>
    // Simpan cart di sessionStorage dan redirect ke daftar-laundry.php dengan data
    document.addEventListener('DOMContentLoaded', () => {
        const cartCount = document.getElementById('cart-count');
        const items = document.querySelectorAll('.add-to-cart');
        const cartLink = document.getElementById('cart-link');
        let cart = JSON.parse(sessionStorage.getItem('cart')) || [];

        function updateCartUI() {
            let count = cart.length;
            if (count > 0) {
                cartCount.textContent = count;
                cartCount.classList.remove('hidden');
            } else {
                cartCount.classList.add('hidden');
            }

            items.forEach(item => {
                const name = item.getAttribute('data-name');
                const category = item.getAttribute('data-category');
                const row = item.closest('tr');

                const isSelected = cart.some(cartItem => cartItem.name === name && cartItem.category === category);

                if (isSelected) {
                    item.innerHTML = '<span class="material-symbols-outlined align-middle text-[18px] mr-1">check_circle</span>Terpilih';
                    item.classList.remove('bg-primary', 'text-white', 'hover:bg-primary-dark');
                    item.classList.add('bg-secondary-container', 'text-on-secondary-container', 'hover:opacity-90');
                    if (row) row.classList.add('bg-surface-variant/50');
                } else {
                    item.innerHTML = 'Pilih';
                    item.classList.remove('bg-secondary-container', 'text-on-secondary-container', 'hover:opacity-90');
                    item.classList.add('bg-primary', 'text-white', 'hover:bg-primary-dark');
                    if (row) row.classList.remove('bg-surface-variant/50');
                }
            });
        }

        // Inisialisasi UI saat dimuat
        updateCartUI();

        items.forEach(item => {
            item.addEventListener('click', function () {
                const name = this.getAttribute('data-name');
                const category = this.getAttribute('data-category');
                const price = this.getAttribute('data-price');
                const icon = this.getAttribute('data-icon') || 'local_laundry_service';

                const itemIndex = cart.findIndex(cartItem => cartItem.name === name && cartItem.category === category);

                if (itemIndex > -1) {
                    // Hapus jika sudah ada
                    cart.splice(itemIndex, 1);
                } else {
                    // Tambah jika belum ada
                    cart.push({ category, name, price, icon });
                }

                sessionStorage.setItem('cart', JSON.stringify(cart));
                updateCartUI();
            });
        });

        cartLink.addEventListener('click', function (e) {
            e.preventDefault();
            window.location.href = 'daftar-laundry.php';
        });
    });
</script>

<!-- ═══════════════════════════ PRICE LIST SECTION ═══════════════════════════ -->
<section class="py-12 bg-surface">
    <div class="max-w-7xl mx-auto px-4 sm:px-8 space-y-12">
        <?php foreach ($full_price_list as $category): ?>
            <div class="reveal" id="<?= isset($category['id']) ? $category['id'] : '' ?>" style="scroll-margin-top: 160px;">
                <!-- Category Title -->
                <div class="border-l-4 border-secondary-container pl-4 mb-6">
                    <h2 class="text-xl sm:text-2xl font-bold text-primary"><?= $category['category'] ?></h2>
                    <?php if (!empty($category['desc'])): ?>
                        <p class="text-xs sm:text-sm text-on-surface-variant mt-1"><?= $category['desc'] ?></p>
                    <?php endif; ?>
                </div>

                <!-- Category Items -->
                <div
                    class="bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/10 overflow-hidden">
                    <table class="w-full text-left bg-white">
                        <tbody class="divide-y divide-surface-variant">
                            <?php foreach ($category['items'] as $item):
                                $icon = 'local_laundry_service';
                                foreach ($services as $svc) {
                                    if (isset($category['id']) && isset($svc['id']) && $category['id'] === $svc['id']) {
                                        $icon = $svc['icon'];
                                        break;
                                    }
                                }
                                ?>
                                <tr class="hover:bg-surface-container transition-colors">
                                    <td class="py-3 px-4 sm:py-4 sm:px-6">
                                        <p class="text-sm sm:text-base font-medium text-primary"> <?= $item['name'] ?> </p>
                                    </td>
                                    <td
                                        class="py-3 px-4 sm:py-4 sm:px-6 text-right font-bold text-secondary-container whitespace-nowrap text-sm sm:text-base">
                                        <?= $item['price'] ?>
                                    </td>
                                    <td class="py-3 px-4 sm:py-4 sm:px-6 text-right">
                                        <button
                                            class="add-to-cart bg-primary text-white px-4 py-2 rounded-full hover:bg-primary-dark whitespace-nowrap inline-flex items-center justify-center"
                                            data-category="<?= htmlspecialchars($category['category']) ?>"
                                            data-name="<?= htmlspecialchars($item['name']) ?>"
                                            data-price="<?= htmlspecialchars($item['price']) ?>"
                                            data-icon="<?= htmlspecialchars($icon) ?>">Pilih</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="text-center pt-8 pb-12 reveal">
            <a href="#" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;"
                class="inline-flex items-center gap-3 bg-primary text-on-primary px-8 py-4 rounded-full font-bold hover:scale-105 active:scale-95 transition-all shadow-xl hover:shadow-2xl">
                <span class="material-symbols-outlined text-lg">arrow_upward</span>
                Kembali ke Atas
            </a>
        </div>
    </div>
</section>

<?php
// Render the footer
require_once __DIR__ . '/footer.php';
?>