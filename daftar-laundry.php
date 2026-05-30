<?php require_once __DIR__ . '/config/midtrans.php'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Troli Laundry Anda</title>
    <link rel="icon" type="image/png" href="assets/gambar/LOGO.png?v=2">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <!-- Midtrans Snap JS -->
    <script src="<?= MIDTRANS_SNAP_JS_URL ?>" data-client-key="<?= MIDTRANS_CLIENT_KEY ?>"></script>
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
            padding: 0 8px 0 0;
            margin: 0 0 24px 0;
            max-height: 380px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .cart-list::-webkit-scrollbar {
            width: 6px;
        }

        .cart-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .cart-list::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 999px;
        }

        .cart-list::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
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
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
        }

        .form-input.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
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

        .payment-method-card input:checked+.card-content {
            border-color: #10b981;
            background: #ecfdf5;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1);
        }

        .payment-method-card .icon {
            font-size: 2.5rem;
            color: #9ca3af;
            transition: color 0.2s;
        }

        .payment-method-card input:checked+.card-content .icon {
            color: #10b981;
        }

        .payment-method-card .card-content span:last-child {
            font-weight: 600;
            color: #4b5563;
        }

        .payment-method-card input:checked+.card-content span:last-child {
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
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
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
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .success-btn:hover {
            background: #059669;
        }

        /* Mobile specific styles for Shopee-like cart layout */
        @media (max-width: 640px) {
            body {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100vw !important;
            }

            .cart-container {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                border-radius: 0 !important;
                padding: 24px 16px 24px 16px !important;
                box-shadow: none !important;
                box-sizing: border-box !important;
            }

            #cart-view:not([style*="display: none"]) {
                display: block !important;
            }

            #payment-view:not([style*="display: none"]) {
                display: block !important;
            }

            .payment-content-scroll {
                padding-bottom: 200px !important;
            }

            .cart-list {
                padding-bottom: 200px !important;
            }

            .cart-total {
                position: fixed !important;
                bottom: calc(124px + env(safe-area-inset-bottom)) !important;
                left: 0 !important;
                right: 0 !important;
                background: #fff !important;
                padding: 12px 20px !important;
                margin: 0 !important;
                border-top: 1px solid #f3f4f6 !important;
                z-index: 89 !important;
                box-sizing: border-box !important;
                text-align: right !important;
                box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.02) !important;
            }

            .btn-group {
                position: fixed !important;
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                background: #fff !important;
                box-shadow: 0 -8px 24px rgba(0, 0, 0, 0.05) !important;
                padding: 8px 20px calc(12px + env(safe-area-inset-bottom)) 20px !important;
                margin: 0 !important;
                z-index: 90 !important;
                display: flex !important;
                flex-direction: column !important;
                gap: 8px !important;
                box-sizing: border-box !important;
            }

            .btn-group .cart-btn {
                display: block !important;
                width: 100% !important;
                height: auto !important;
                padding: 14px 0 !important;
                font-size: 1.05rem !important;
                font-weight: bold !important;
                text-align: center !important;
                box-sizing: border-box !important;
                border-radius: 999px !important;
            }

            .cart-list li {
                display: grid;
                grid-template-columns: auto 1fr auto;
                grid-template-rows: auto auto;
                gap: 8px 12px;
                padding: 16px 0;
                align-items: start;
            }

            .cart-img {
                grid-row: 1 / span 2;
                align-self: center;
            }

            .cart-info {
                grid-row: 1 / span 2;
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
                gap: 4px;
                min-width: 0;
            }

            .cart-item-title {
                display: block;
                font-size: 0.95rem;
                font-weight: 600;
                color: #222;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 100%;
            }

            .cart-item-category {
                font-size: 0.75rem;
                color: #6b7280;
                margin-top: 0;
                font-weight: normal;
                line-height: 1.25;
            }

            .cart-price {
                font-size: 0.95rem;
                font-weight: bold;
                color: #f59e0b;
                margin-top: 2px;
            }

            .qty-control {
                grid-column: 3;
                grid-row: 1;
                margin-right: 0; /* Remove right margin on mobile */
                padding: 2px 6px;
                justify-self: end;
            }

            .cart-remove {
                grid-column: 3;
                grid-row: 2;
                padding: 2px;
                margin-top: 4px;
                justify-self: end;
                align-self: end;
            }
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
            <div class="payment-content-scroll">
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
                        <textarea class="form-input" id="customer-address" placeholder="Masukkan alamat lengkap Anda"
                            rows="3"></textarea>
                        <div class="input-error-msg" id="error-address">Alamat wajib diisi</div>
                    </div>
                </div>

                <!-- Pilih Metode Pembayaran -->
                <div class="customer-info-section">
                    <div class="customer-info-title">
                        <span class="material-symbols-outlined">payments</span>
                        Metode Pembayaran
                    </div>
                    <div class="payment-methods" style="grid-template-columns: 1fr 1fr;">
                        <label class="payment-method-card">
                            <input type="radio" name="checkout_method" value="online" checked>
                            <div class="card-content">
                                <span class="material-symbols-outlined icon">credit_card</span>
                                <span>Bayar Online</span>
                                <span style="font-size:0.7rem;color:#6b7280;font-weight:400;">QRIS, VA, GoPay, dll</span>
                            </div>
                        </label>
                        <label class="payment-method-card">
                            <input type="radio" name="checkout_method" value="tunai">
                            <div class="card-content">
                                <span class="material-symbols-outlined icon">store</span>
                                <span>Bayar Tunai</span>
                                <span style="font-size:0.7rem;color:#6b7280;font-weight:400;">Bayar di outlet</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div id="payment-summary" class="payment-summary">
                    <!-- Summary inserted via JS -->
                </div>
            </div>

            <div class="btn-group">
                <button id="btn-back" class="cart-btn btn-secondary">Kembali</button>
                <button id="btn-confirm" class="cart-btn btn-primary">Bayar Sekarang</button>
            </div>
            <!-- Loading indicator -->
            <div id="checkout-loading" style="display:none; text-align:center; margin-top:16px;">
                <div
                    style="display:inline-flex; align-items:center; gap:10px; background:#ecfdf5; padding:12px 24px; border-radius:12px;">
                    <div
                        style="width:20px;height:20px;border:3px solid #d1fae5;border-top-color:#10b981;border-radius:50%;animation:spin 0.8s linear infinite;">
                    </div>
                    <span style="color:#065f46;font-weight:600;">Memproses pembayaran...</span>
                </div>
            </div>
            <style>
                @keyframes spin {
                    to {
                        transform: rotate(360deg)
                    }
                }
            </style>
        </div>
    </div>



    <!-- ============ SUCCESS MODAL ============ -->
    <div class="modal-overlay" id="success-modal">
        <div class="success-modal">
            <div class="success-icon" id="success-icon">
                <span class="material-symbols-outlined" id="success-icon-symbol">check</span>
            </div>
            <div class="success-title" id="success-title">Pesanan Berhasil!</div>
            <div class="success-message" id="success-message-text">Terima kasih telah mempercayakan cucian Anda kepada
                Laughndry. Kurir kami akan segera menjemput pakaian Anda.</div>
            <button class="success-btn" id="success-btn" onclick="closeSuccessModal()">Kembali ke Beranda</button>
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
                                <span class="cart-item-title">${item.name}</span>
                                ${item.category ? `<span class="cart-item-category" style="display: block; font-size: 0.75rem; color: #6b7280; margin-top: 2px; font-weight: normal;">${item.category}</span>` : ''}
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
        });

        btnBack.addEventListener('click', () => {
            paymentView.style.display = 'none';
            cartView.style.display = 'block';
        });

        // ═══════════════════════════════════════════
        // CHECKOUT: Validate → Pilih metode → Proses
        // ═══════════════════════════════════════════
        btnConfirm.addEventListener('click', () => {
            const name = document.getElementById('customer-name');
            const phone = document.getElementById('customer-phone');
            const address = document.getElementById('customer-address');
            let valid = true;

            [name, phone, address].forEach(el => el.classList.remove('error'));
            document.querySelectorAll('.input-error-msg').forEach(el => el.classList.remove('show'));

            if (!name.value.trim()) { name.classList.add('error'); document.getElementById('error-name').classList.add('show'); valid = false; }
            if (!phone.value.trim()) { phone.classList.add('error'); document.getElementById('error-phone').classList.add('show'); valid = false; }
            if (!address.value.trim()) { address.classList.add('error'); document.getElementById('error-address').classList.add('show'); valid = false; }

            if (!valid) { document.querySelector('.form-input.error').focus(); return; }

            // Disable button & show loading
            btnConfirm.disabled = true;
            btnConfirm.textContent = 'Memproses...';
            document.getElementById('checkout-loading').style.display = 'block';

            const checkoutData = {
                nama: name.value.trim(),
                telepon: phone.value.trim(),
                alamat: address.value.trim(),
                items: cart
            };

            const method = document.querySelector('input[name="checkout_method"]:checked').value;

            if (method === 'tunai') {
                // ══════ TUNAI: Simpan langsung ke DB dengan status pending ══════
                fetch('api/checkout.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ...checkoutData, payment_type: 'tunai', is_tunai: true })
                })
                    .then(r => r.text())
                    .then(text => {
                        try {
                            const data = JSON.parse(text);
                            if (data.success) {
                                document.getElementById('success-title').innerText = 'Pesanan Sedang Diproses';
                                document.getElementById('success-message-text').innerText = 'Pesanan Anda telah kami catat dan masuk ke dalam antrean. Silakan bawa pakaian Anda ke outlet atau driver kami dan lakukan pembayaran tunai.';
                                document.getElementById('success-icon').style.background = '#FBAD48';
                                document.getElementById('success-icon').style.boxShadow = '0 0 0 10px rgba(251, 173, 72, 0.2)';
                                document.getElementById('success-icon-symbol').innerText = 'hourglass_empty';
                                document.getElementById('success-btn').style.background = '#FBAD48';
                                document.getElementById('success-modal').classList.add('show');
                            } else {
                                alert('Gagal menyimpan pesanan: ' + data.message);
                            }
                        } catch (e) {
                            console.error('Non-JSON response:', text);
                            alert('Server Error: ' + text.substring(0, 300));
                        }
                    })
                    .catch(err => {
                        console.error('Cash checkout error:', err);
                        alert('Terjadi kesalahan jaringan atau server. Coba lagi.');
                    })
                    .finally(() => {
                        btnConfirm.disabled = false;
                        btnConfirm.textContent = 'Bayar Sekarang';
                        document.getElementById('checkout-loading').style.display = 'none';
                    });
            } else {
                // ══════ ONLINE (MIDTRANS): Snap Token → Bayar → Simpan ke DB ══════
                fetch('api/snap_token.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(checkoutData)
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.snap_token) {
                            window.snap.pay(data.snap_token, {
                                onSuccess: function (result) {
                                    console.log('Payment success:', result);
                                    fetch('api/checkout.php', {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json' },
                                        body: JSON.stringify({
                                            ...checkoutData,
                                            payment_type: result.payment_type || 'midtrans',
                                            midtrans_order_id: data.midtrans_order_id
                                        })
                                    })
                                        .then(r => r.text())
                                        .then(text => {
                                            try {
                                                const saveResult = JSON.parse(text);
                                                if (saveResult.success) {
                                                    document.getElementById('success-message-text').innerText = 'Pembayaran online berhasil! Terima kasih telah mempercayakan cucian Anda kepada Laughndry. Kurir kami akan segera menjemput pakaian Anda.';
                                                    document.getElementById('success-modal').classList.add('show');
                                                } else {
                                                    alert('Pembayaran berhasil, tapi gagal menyimpan: ' + saveResult.message);
                                                }
                                            } catch (e) {
                                                console.error('Non-JSON response:', text);
                                                alert('Pembayaran berhasil, tetapi terjadi error server saat menyimpan: ' + text.substring(0, 300));
                                            }
                                        })
                                        .catch(err => {
                                            console.error('Save order error:', err);
                                            alert('Pembayaran berhasil, tetapi terjadi kesalahan jaringan saat menyimpan pesanan. Silakan hubungi admin.');
                                        });
                                },
                                onPending: function (result) {
                                    console.log('Payment pending:', result);
                                    alert('Pembayaran menunggu konfirmasi. Silakan selesaikan pembayaran Anda.');
                                },
                                onError: function (result) {
                                    console.error('Payment error:', result);
                                    alert('Pembayaran gagal. Silakan coba lagi.');
                                },
                                onClose: function () {
                                    console.log('Snap popup closed — tidak ada data disimpan');
                                }
                            });
                        } else {
                            alert('Gagal memproses: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(() => alert('Terjadi kesalahan jaringan. Coba lagi.'))
                    .finally(() => {
                        btnConfirm.disabled = false;
                        btnConfirm.textContent = 'Bayar Sekarang';
                        document.getElementById('checkout-loading').style.display = 'none';
                    });
            }
        });

        // ═══════════════════════════════════════════
        // Success Modal — Reset & Kembali
        // ═══════════════════════════════════════════
        function closeSuccessModal() {
            document.getElementById('success-modal').classList.remove('show');
            sessionStorage.removeItem('cart');
            cart = [];
            renderCart();
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