<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Troli Laundry Anda</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <style>
        body {
            background: #f8fafc;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        .cart-container {
            max-width: 540px;
            margin: 40px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            padding: 32px 24px 24px 24px;
        }

        .cart-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 2rem;
            font-weight: 900;
            color: #00433a;
            margin-bottom: 18px;
        }

        .cart-title .material-symbols-outlined {
            color: #fbad48;
        }

        .cart-list {
            list-style: none;
            padding: 0;
            margin: 0 0 24px 0;
        }

        .cart-list li {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 18px 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 1.1rem;
        }

        .cart-list li:last-child {
            border-bottom: none;
        }

        .cart-img {
            width: 56px;
            height: 56px;
            background: #f3f4f6;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #a3a3a3;
        }

        .cart-info {
            flex: 1;
        }

        .cart-name {
            font-weight: 500;
            color: #222;
        }

        .cart-price {
            color: #f59e0b;
            font-weight: bold;
            margin-top: 2px;
        }

        .cart-remove {
            background: none;
            border: none;
            color: #ef4444;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 4px;
            transition: color 0.2s;
        }

        .cart-remove:hover {
            color: #b91c1c;
        }

        .qty-control {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f3f4f6;
            padding: 4px 8px;
            border-radius: 8px;
            margin-right: 8px;
        }

        .qty-btn {
            background: #fff;
            border: 1px solid #e5e7eb;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: bold;
            color: #374151;
            transition: all 0.2s;
        }

        .qty-btn:hover {
            background: #e5e7eb;
        }

        .qty-value {
            font-weight: 600;
            min-width: 20px;
            text-align: center;
        }

        .cart-empty {
            text-align: center;
            color: #888;
            margin: 32px 0;
        }

        .cart-total {
            text-align: right;
            font-size: 1.2rem;
            font-weight: bold;
            color: #374151;
            margin-bottom: 24px;
        }

        .cart-btn {
            display: inline-block;
            background: #fbbf24;
            color: #fff;
            font-weight: bold;
            padding: 12px 32px;
            border-radius: 999px;
            text-decoration: none;
            transition: background 0.2s;
            box-shadow: 0 2px 8px rgba(251, 191, 36, 0.12);
        }

        .cart-btn:hover {
            background: #f59e0b;
        }

        .btn-group {
            display: flex;
            gap: 12px;
            margin-top: 16px;
        }

        .btn-group .cart-btn {
            flex: 1;
            text-align: center;
            border: none;
            cursor: pointer;
            font-family: inherit;
            font-size: 1rem;
            box-sizing: border-box;
        }

        .btn-primary {
            background: #00433a;
            box-shadow: 0 2px 8px rgba(0, 67, 58, 0.2);
        }

        .btn-primary:hover {
            background: #005046;
        }

        .btn-secondary {
            background: #9ca3af;
            box-shadow: 0 2px 8px rgba(156, 163, 175, 0.12);
        }

        .btn-secondary:hover {
            background: #6b7280;
        }

        .payment-summary {
            background: #f9fafb;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
        }

        .payment-summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            color: #4b5563;
        }

        .payment-summary-total {
            display: flex;
            justify-content: space-between;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #d1d5db;
            font-weight: bold;
            color: #111827;
            font-size: 1.1rem;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 12px;
            color: #374151;
        }

        .form-select {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            font-size: 1rem;
            font-family: inherit;
            background-color: white;
            box-sizing: border-box;
        }
        /* Customer Info Form */
        .customer-info-section {
            background: #f9fafb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            border: 1px solid #e5e7eb;
        }
        .customer-info-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 16px;
            font-size: 1rem;
        }
        .customer-info-title .material-symbols-outlined {
            color: #f59e0b;
            font-size: 22px;
        }
        .form-input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            font-size: 1rem;
            font-family: inherit;
            background-color: #fff;
            box-sizing: border-box;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .form-input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16,185,129,0.12);
        }
        .form-input.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239,68,68,0.1);
        }
        .form-input::placeholder {
            color: #9ca3af;
        }
        .input-group {
            margin-bottom: 14px;
        }
        .input-group:last-child {
            margin-bottom: 0;
        }
        .input-label {
            display: block;
            font-weight: 600;
            font-size: 0.85rem;
            color: #4b5563;
            margin-bottom: 6px;
        }
        .input-error-msg {
            color: #ef4444;
            font-size: 0.8rem;
            margin-top: 4px;
            display: none;
        }
        .input-error-msg.show {
            display: block;
        }
        textarea.form-input {
            resize: vertical;
            min-height: 70px;
        }
        /* New Payment Gateway Styles */
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 12px;
            margin-bottom: 24px;
        }
        .payment-method-card {
            cursor: pointer;
            position: relative;
        }
        .payment-method-card input {
            display: none;
        }
        .payment-method-card .card-content {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            background: #fff;
            text-align: center;
            height: 100%;
            box-sizing: border-box;
        }
        .payment-method-card input:checked + .card-content {
            border-color: #10b981;
            background: #ecfdf5;
            box-shadow: 0 4px 12px rgba(16,185,129,0.1);
        }
        .payment-method-card .icon {
            font-size: 2.5rem;
            color: #9ca3af;
            transition: color 0.2s;
        }
        .payment-method-card input:checked + .card-content .icon {
            color: #10b981;
        }
        .payment-method-card .card-content span:last-child {
            font-weight: 600;
            color: #4b5563;
        }
        .payment-method-card input:checked + .card-content span:last-child {
            color: #065f46;
        }

        .payment-details-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            text-align: center;
            display: none;
        }
        .payment-details-box.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .qr-placeholder {
            width: 200px;
            height: 200px;
            background: #fff;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            flex-direction: column;
            color: #64748b;
        }

        .va-number {
            font-size: 1.5rem;
            font-weight: bold;
            letter-spacing: 2px;
            color: #0f172a;
            background: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            margin: 12px 0;
            display: inline-block;
        }

        .copy-btn {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            color: #475569;
            font-size: 0.95rem;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
        }
        .copy-btn:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        /* Success Modal */
        .modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            backdrop-filter: blur(4px);
        }
        .modal-overlay.show {
            opacity: 1;
            pointer-events: auto;
        }
        .success-modal {
            background: #fff;
            border-radius: 24px;
            padding: 40px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            transform: scale(0.9);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .modal-overlay.show .success-modal {
            transform: scale(1);
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: #10b981;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin: 0 auto 24px;
            box-shadow: 0 0 0 10px #ecfdf5;
        }
        .success-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #111827;
            margin-bottom: 12px;
        }
        .success-message {
            color: #6b7280;
            margin-bottom: 32px;
            line-height: 1.5;
        }
        .success-btn {
            background: #10b981;
            color: #fff;
            border: none;
            padding: 14px 32px;
            border-radius: 999px;
            font-weight: bold;
            font-size: 1.1rem;
            cursor: pointer;
            width: 100%;
            transition: background 0.2s;
            box-shadow: 0 4px 12px rgba(16,185,129,0.2);
        }
        .success-btn:hover {
            background: #059669;
        }
    </style>
</head>

<body>
    <div class="cart-container">
        <!-- Main Cart View -->
        <div id="cart-view">
            <div class="cart-title">
                <span class="material-symbols-outlined" style="font-size:2.2rem;">shopping_cart</span>
                Troli Laundry Anda
            </div>
            <ul class="cart-list" id="cart-list"></ul>
            <div class="cart-empty" id="cart-empty">Belum ada laundry yang dipilih.</div>
            <div class="cart-total" id="cart-total"></div>
            <div class="btn-group">
                <a href="harga.php" class="cart-btn">Tambah Pesanan</a>
                <button id="btn-checkout" class="cart-btn btn-primary" style="display: none;">Checkout</button>
            </div>
        </div>

        <!-- Payment View (Hidden by default) -->
        <div id="payment-view" style="display: none;">
            <div class="cart-title">
                <span class="material-symbols-outlined" style="font-size:2.2rem;">payments</span>
                Checkout
            </div>
            <!-- Customer Info Form -->
            <div class="customer-info-section">
                <div class="customer-info-title">
                    <span class="material-symbols-outlined">person</span>
                    Data Pelanggan
                </div>
                <div class="input-group">
                    <label class="input-label" for="customer-name">Nama Lengkap</label>
                    <input type="text" class="form-input" id="customer-name" placeholder="Masukkan nama lengkap Anda">
                    <div class="input-error-msg" id="error-name">Nama wajib diisi</div>
                </div>
                <div class="input-group">
                    <label class="input-label" for="customer-phone">Nomor Telepon</label>
                    <input type="tel" class="form-input" id="customer-phone" placeholder="Contoh: 08123456789">
                    <div class="input-error-msg" id="error-phone">Nomor telepon wajib diisi</div>
                </div>
                <div class="input-group">
                    <label class="input-label" for="customer-address">Alamat</label>
                    <textarea class="form-input" id="customer-address" placeholder="Masukkan alamat lengkap Anda" rows="3"></textarea>
                    <div class="input-error-msg" id="error-address">Alamat wajib diisi</div>
                </div>
            </div>

            <div id="payment-summary" class="payment-summary">
                <!-- Summary inserted via JS -->
            </div>


            <div class="form-group">
                <label class="form-label">Pilih Metode Pembayaran</label>
                <div class="payment-methods">
                    <label class="payment-method-card">
                        <input type="radio" name="payment_method" value="qris" checked onchange="updatePaymentDetails()">
                        <div class="card-content">
                            <span class="material-symbols-outlined icon">qr_code_scanner</span>
                            <span>QRIS</span>
                        </div>
                    </label>
                    <label class="payment-method-card">
                        <input type="radio" name="payment_method" value="transfer" onchange="updatePaymentDetails()">
                        <div class="card-content">
                            <span class="material-symbols-outlined icon">account_balance</span>
                            <span>Transfer Bank</span>
                        </div>
                    </label>
                    <label class="payment-method-card">
                        <input type="radio" name="payment_method" value="tunai" onchange="updatePaymentDetails()">
                        <div class="card-content">
                            <span class="material-symbols-outlined icon">payments</span>
                            <span>Tunai</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Payment Details Sub-sections -->
            <div id="details-qris" class="payment-details-box active">
                <p style="margin-bottom: 16px; color: #4b5563; font-size: 0.95rem;">Scan QR Code di bawah ini menggunakan aplikasi e-wallet Anda.</p>
                <div style="background: white; padding: 16px; border-radius: 16px; display: inline-block; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin: 0 auto 16px; border: 1px solid #e2e8f0;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=Laughndry_Payment" alt="QRIS Barcode" style="width: 200px; height: 200px; display: block; border-radius: 8px;">
                    <div style="margin-top: 12px; font-weight: bold; color: #1e293b; font-size: 1.1rem; text-align: center; display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <span class="material-symbols-outlined" style="color: #10b981; font-size: 20px;">verified_user</span>
                        QRIS Laughndry
                    </div>
                </div>
                <p style="font-weight: bold; color: #10b981; font-size: 1.2rem;">Total: <span class="qris-total"></span></p>
            </div>

            <div id="details-transfer" class="payment-details-box">
                <select class="form-select" id="bank-selector" style="margin-bottom: 16px;" onchange="updateBankDetails()">
                    <option value="BCA">BCA Virtual Account</option>
                    <option value="Mandiri">Mandiri Virtual Account</option>
                    <option value="BRI">BRI Virtual Account</option>
                </select>
                <p style="color: #4b5563; margin-bottom: 8px; font-size: 0.95rem;">Nomor Virtual Account <span id="bank-name">BCA</span>:</p>
                <div class="va-number" id="va-number">8290 1234 5678</div>
                <br>
                <button class="copy-btn" onclick="copyVA()"><span class="material-symbols-outlined" style="font-size: 18px; margin-right: 6px;">content_copy</span>Salin Nomor VA</button>
            </div>

            <div id="details-tunai" class="payment-details-box">
                <span class="material-symbols-outlined" style="font-size: 48px; color: #f59e0b; margin-bottom: 12px;">info</span>
                <p style="color: #4b5563; line-height: 1.5;">Anda dapat membayar dengan uang tunai saat kurir mengambil cucian Anda atau saat Anda mengantarkannya ke outlet kami.</p>
            </div>

            <div class="btn-group">
                <button id="btn-back" class="cart-btn btn-secondary">Kembali</button>
                <button id="btn-confirm" class="cart-btn btn-primary">Bayar Sekarang</button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal-overlay" id="success-modal">
        <div class="success-modal">
            <div class="success-icon">
                <span class="material-symbols-outlined">check</span>
            </div>
            <div class="success-title">Pesanan Berhasil!</div>
            <div class="success-message">Terima kasih telah mempercayakan cucian Anda kepada Laughndry. Kurir kami akan segera menjemput pakaian Anda.</div>
            <button class="success-btn" onclick="closeSuccessModal()">Kembali ke Beranda</button>
        </div>
    </div>

    <script>
        // Ambil data keranjang dari sessionStorage
        let cart = JSON.parse(sessionStorage.getItem('cart')) || [];

        // Inisialisasi properti qty jika belum ada
        cart = cart.map(item => ({ ...item, qty: item.qty || 1 }));
        sessionStorage.setItem('cart', JSON.stringify(cart));

        const cartList = document.getElementById('cart-list');
        const cartEmpty = document.getElementById('cart-empty');
        const cartTotal = document.getElementById('cart-total');
        let total = 0;

        function renderCart() {
            cartList.innerHTML = '';
            total = 0;
            if (cart.length === 0) {
                cartList.style.display = 'none';
                cartTotal.style.display = 'none';
                cartEmpty.style.display = 'block';
                document.getElementById('btn-checkout').style.display = 'none';
            } else {
                cartList.style.display = 'block';
                cartTotal.style.display = 'block';
                cartEmpty.style.display = 'none';
                document.getElementById('btn-checkout').style.display = 'block';
                cart.forEach((item, idx) => {
                    let price = parseInt(item.price.replace(/[^0-9]/g, ''));
                    let qty = item.qty || 1;
                    let icon = item.icon || 'local_laundry_service';
                    let subtotal = price * qty;
                    total += subtotal;

                    const li = document.createElement('li');
                    li.innerHTML = `
                        <div class="cart-img">
                            <span class="material-symbols-outlined" style="color: #00433a;">${icon}</span>
                        </div>
                        <div class="cart-info">
                            <div class="cart-name">
                                ${item.name}
                                ${item.category ? `<span style="display: block; font-size: 0.75rem; color: #6b7280; margin-top: 2px; font-weight: normal;">${item.category}</span>` : ''}
                            </div>
                            <div class="cart-price">Rp ${price.toLocaleString('id-ID')}</div>
                        </div>
                        <div class="qty-control">
                            <button class="qty-btn qty-minus" data-idx="${idx}">-</button>
                            <span class="qty-value">${qty}</span>
                            <button class="qty-btn qty-plus" data-idx="${idx}">+</button>
                        </div>
                        <button class="cart-remove" title="Hapus" data-idx="${idx}"><span class="material-symbols-outlined">delete</span></button>
                    `;
                    cartList.appendChild(li);
                });
                cartTotal.textContent = `Total: Rp ${total.toLocaleString('id-ID')}`;
            }
        }

        // Event delegation untuk hapus barang dan kuantitas
        cartList.addEventListener('click', function (e) {
            const btnRemove = e.target.closest('.cart-remove');
            const btnMinus = e.target.closest('.qty-minus');
            const btnPlus = e.target.closest('.qty-plus');

            if (btnRemove) {
                const idx = btnRemove.getAttribute('data-idx');
                cart.splice(idx, 1);
                sessionStorage.setItem('cart', JSON.stringify(cart));
                renderCart();
            } else if (btnMinus) {
                const idx = btnMinus.getAttribute('data-idx');
                if (cart[idx].qty > 1) {
                    cart[idx].qty--;
                } else {
                    cart.splice(idx, 1);
                }
                sessionStorage.setItem('cart', JSON.stringify(cart));
                renderCart();
            } else if (btnPlus) {
                const idx = btnPlus.getAttribute('data-idx');
                cart[idx].qty = (cart[idx].qty || 1) + 1;
                sessionStorage.setItem('cart', JSON.stringify(cart));
                renderCart();
            }
        });

        renderCart();

        // Payment logic
        const cartView = document.getElementById('cart-view');
        const paymentView = document.getElementById('payment-view');
        const btnCheckout = document.getElementById('btn-checkout');
        const btnBack = document.getElementById('btn-back');
        const btnConfirm = document.getElementById('btn-confirm');
        const paymentSummary = document.getElementById('payment-summary');

        btnCheckout.addEventListener('click', () => {
            cartView.style.display = 'none';
            paymentView.style.display = 'block';
            renderPaymentSummary();
            updatePaymentDetails();
        });

        btnBack.addEventListener('click', () => {
            paymentView.style.display = 'none';
            cartView.style.display = 'block';
        });

        btnConfirm.addEventListener('click', () => {
            // Validate customer info
            const name = document.getElementById('customer-name');
            const phone = document.getElementById('customer-phone');
            const address = document.getElementById('customer-address');
            let valid = true;

            // Reset errors
            [name, phone, address].forEach(el => {
                el.classList.remove('error');
            });
            document.querySelectorAll('.input-error-msg').forEach(el => el.classList.remove('show'));

            if (!name.value.trim()) {
                name.classList.add('error');
                document.getElementById('error-name').classList.add('show');
                valid = false;
            }
            if (!phone.value.trim()) {
                phone.classList.add('error');
                document.getElementById('error-phone').classList.add('show');
                valid = false;
            }
            if (!address.value.trim()) {
                address.classList.add('error');
                document.getElementById('error-address').classList.add('show');
                valid = false;
            }

            if (!valid) {
                // Scroll to the first error
                document.querySelector('.form-input.error').focus();
                return;
            }

            // Disable tombol supaya tidak double-click
            btnConfirm.disabled = true;
            btnConfirm.textContent = 'Memproses...';

            // Ambil metode pembayaran
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const bankSelector = document.getElementById('bank-selector');
            const bank = (paymentMethod === 'transfer' && bankSelector) ? bankSelector.value : null;

            // Kirim data ke API
            const checkoutData = {
                nama: name.value.trim(),
                telepon: phone.value.trim(),
                alamat: address.value.trim(),
                metode_bayar: paymentMethod,
                bank: bank,
                items: cart
            };

            fetch('api/checkout.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(checkoutData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Berhasil! Tampilkan modal sukses
                    document.getElementById('success-modal').classList.add('show');
                } else {
                    alert('Gagal menyimpan pesanan: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan jaringan. Coba lagi.');
            })
            .finally(() => {
                btnConfirm.disabled = false;
                btnConfirm.textContent = 'Bayar Sekarang';
            });
        });

        function updatePaymentDetails() {
            const method = document.querySelector('input[name="payment_method"]:checked').value;
            document.querySelectorAll('.payment-details-box').forEach(el => el.classList.remove('active'));
            document.getElementById('details-' + method).classList.add('active');
            
            if(method === 'qris') {
                document.querySelector('.qris-total').textContent = `Rp ${total.toLocaleString('id-ID')}`;
            }
        }

        function updateBankDetails() {
            const bank = document.getElementById('bank-selector').value;
            document.getElementById('bank-name').textContent = bank;
            const vaMap = {
                'BCA': '8290 1234 5678',
                'Mandiri': '8950 8765 4321',
                'BRI': '8881 9999 0000'
            };
            document.getElementById('va-number').textContent = vaMap[bank];
        }

        function copyVA() {
            const va = document.getElementById('va-number').textContent;
            navigator.clipboard.writeText(va.replace(/\s/g, ''));
            
            const btn = document.querySelector('.copy-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = `<span class="material-symbols-outlined" style="font-size: 18px; margin-right: 6px;">check</span>Tersalin!`;
            btn.style.background = '#d1fae5';
            btn.style.color = '#065f46';
            btn.style.borderColor = '#10b981';
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.style = '';
            }, 2000);
        }

        function closeSuccessModal() {
            document.getElementById('success-modal').classList.remove('show');
            sessionStorage.removeItem('cart');
            cart = [];
            renderCart();
            // Reset customer form
            document.getElementById('customer-name').value = '';
            document.getElementById('customer-phone').value = '';
            document.getElementById('customer-address').value = '';
            document.getElementById('payment-view').style.display = 'none';
            document.getElementById('cart-view').style.display = 'block';
            window.location.href = 'index.php';
        }

        function renderPaymentSummary() {
            paymentSummary.innerHTML = '';
            let orderItems = '';
            cart.forEach(item => {
                let price = parseInt(item.price.replace(/[^0-9]/g, ''));
                let qty = item.qty || 1;
                let subtotal = price * qty;
                orderItems += `
                    <div class="payment-summary-item" style="align-items: flex-start;">
                        <div>
                            <div style="font-weight: 500;">${item.name} (x${qty})</div>
                            ${item.category ? `<div style="font-size: 0.75rem; color: #6b7280; margin-top: 2px;">${item.category}</div>` : ''}
                        </div>
                        <span style="white-space: nowrap; margin-left: 12px;">Rp ${subtotal.toLocaleString('id-ID')}</span>
                    </div>
                `;
            });
            paymentSummary.innerHTML = `
                ${orderItems}
                <div class="payment-summary-total">
                    <span>Total Tagihan</span>
                    <span>Rp ${total.toLocaleString('id-ID')}</span>
                </div>
            `;
        }
    </script>
</body>

</html>