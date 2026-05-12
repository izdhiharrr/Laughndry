<?php
/**
 * data.php — Central data store for the Laughndry landing page.
 *
 * All dynamic content (services, pricing, testimonials, about cards, etc.)
 * lives here as simple PHP arrays. Edit these arrays to update the site
 * without touching any template code.
 */

// ─── About / Value-Proposition Cards ────────────────────────────────────────
$about_cards = [
    [
        'icon' => 'bolt',
        'title' => 'Praktis &amp; Cepat',
        'desc' => 'Mencuci & mengeringkan pakaian dengan Express! Tak perlu antri & menunggu lama.',
    ],
    [
        'icon' => 'schedule',
        'title' => 'Tersedia 24 Jam',
        'desc' => 'Nikmati kenyamanan mencuci kapan saja, sesuai dengan jadwal anda. Kami buka 24 jam setiap hari.',
    ],
    [
        'icon' => 'payments',
        'title' => 'Harga Terjangkau',
        'desc' => 'Hemat waktu & uang anda, cocok untuk kebutuhan harian.',
    ],
];

// ─── Service Cards ──────────────────────────────────────────────────────────
$services = [
    [
        'id' => 'self-service',
        'icon' => 'local_laundry_service',
        'title' => 'Self Service',
        'desc' => 'Cuci pakaian sendiri dengan mesin modern yang mudah digunakan. Praktis, hemat waktu, dan cocok untuk kebutuhan laundry harian Anda.',
    ],
    [
        'id' => 'cuci-lipat',
        'icon' => 'scale',
        'title' => 'Kiloan',
        'desc' => 'Layanan laundry berdasarkan berat dengan proses cepat dan hasil bersih maksimal. Cocok untuk pakaian sehari-hari agar tetap rapi dan segar.',
    ],
    [
        'id' => 'satuan-bedcover-seprei',
        'icon' => 'dry_cleaning',
        'title' => 'Satuan',
        'desc' => 'Perawatan khusus untuk pakaian tertentu seperti jas, gaun, jaket, atau item favorit Anda dengan penanganan lebih detail dan aman.',
    ],
];

// ─── FAQ ───────────────────────────────────────────────────────────────────
$faqs = [
    [
        'q' => 'Layanan apa saja yang tersedia di Laughndry?',
        'a' => 'Kami menyediakan berbagai pilihan, seperti:<br>· Self service: Layanan Cuci Pakaian sendiri dengan cepat dan praktis.<br>· Kiloan: Layanan berdasarkan berat dengan proses cepat dan hasil bersih maksimal.<br>· Satuan: Pilihan tepat untuk bahan pakaian yang sensitif.'
    ],
    [
        'q' => 'Berapa lama waktu pengerjaannya?',
        'a' => 'Tergantung dengan layanan yang dipilih, Untuk Self Service pengerjaan dapat selesai dalam hitungan jam. Untuk kiloan & Satuan selesai 1-2 hari kerja untuk hasil yang lebih maksimal.'
    ],
    [
        'q' => 'Apakah Laughndry menerima cuci bedcover atau gorden?',
        'a' => 'Tentu saja! Mesin kami memiliki kapasitas yang memadai untuk mencuci item besar seperti bedcover dan gorden.'
    ],
    [
        'q' => 'Apakah saya harus membawa deterjen sendiri untuk Coin Laundry?',
        'a' => 'Tidak perlu repot membawa deterjen sendiri, karena layanan kami sudah mencakup deterjen cair dan pewangi berkualitas tinggi.'
    ],
    [
        'q' => 'Apakah ada area tunggu yang nyaman?',
        'a' => 'Tentu saja ada! Kami menyediakan area tunggu yang dilengkapi dengan tempat duduk yang nyaman dan bersih.'
    ],
    [
        'q' => 'Bagaimana cara memesan layanan Laughndry?',
        'a' => 'Anda dapat memesan layanan kami melalui  Website, WhatsApp atau langsung datang ke outlet kami di Serpong.'
    ]
];

// ─── Testimonials ───────────────────────────────────────────────────────────
$testimonials = [
    [
        'stars' => 5,
        'quote' => 'Pertama nyuci di sini dan hasilnya puas
banget... dari segi harga relatif terjangkau
lah,dari segi pelayanan,karyawan yg
incharge helpfull dan ramah bgt. untuk
orang yg baru pertama laundry coin kaya
gw di guidance kok sama petugas nya.',
        'initials' => 'RA',
        'name' => 'Rama Abil Clemira',
        'role' => '',
    ],
    [
        'stars' => 5,
        'quote' => 'Baru pertama kali ke sini, Alhamdulillah
pelayanan nya baik, gak terlalu ngantri
juga. Sekarang mah udah 4x bolak balik ke
sini. Di kala musim hujan, cucian numpuk,
tapi males nyuci pake tangan ya
Laughndry Coin solusinya',
        'initials' => 'RA',
        'name' => 'Rosita Anindya',
        'role' => '',
    ],
    [
        'stars' => 5,
        'quote' => 'Sudah buka 24Jam... Lama waktu untuk
Cuci 30menit. Lama waktu pengering
50menit. Jadi total waktu untuk cuci
sekitar 80 menit, belum termasuk antrian
mesin. Dapat GRATIS parfum sehabis cuci,
ambil di meja kasir.',
        'initials' => 'DH',
        'name' => 'Daniel Hendrato',
        'role' => '',
    ],
    [
        'stars' => 5,
        'quote' => 'Mesinnya banyak jadi antri pun ga kelamaan nunggu. Pelayanan sangat informatif dan ramah sekali. Parkiran luas.',
        'initials' => 'LL',
        'name' => 'Lulu Lutfia',
        'role' => '',
    ],
    [
        'stars' => 5,
        'quote' => 'laundry nya sangat bagus dan bersih, sering bolak balik dikarenakan murah dan deket dari rumah, sangat rekomendasi untuk anak kampus',
        'initials' => 'AW',
        'name' => 'Astri Wulandari',
        'role' => '',
    ],
    [
        'stars' => 5,
        'quote' => 'pelayanan bagus terus buka 24 jam lagi, jdinya kalau malem malem ada baju yang mau dipake besok bisa langsung di cuci dan cepet kering.',
        'initials' => 'NR',
        'name' => 'Nafizha Ramadhani',
        'role' => '',
    ],
];

// ─── Navigation Links ──────────────────────────────────────────────────────
$nav_links = [
    ['label' => 'Home', 'href' => '#', 'active' => true, 'id' => 'nav-home'],
    ['label' => 'Tentang', 'href' => '#tentang', 'active' => false, 'id' => 'nav-tentang'],
    ['label' => 'Layanan', 'href' => '#layanan', 'active' => false, 'id' => 'nav-layanan'],
    ['label' => 'Ulasan', 'href' => '#ulasan', 'active' => false, 'id' => 'nav-ulasan'],
    ['label' => 'Harga', 'href' => 'harga.php', 'active' => false, 'id' => 'nav-harga'],
];

// ─── Full Price List (harga.php) ───────────────────────────────────────────
$full_price_list = [
    [
        'id' => 'self-service',
        'category' => 'Self Service',
        'desc' => 'Cuci dan keringkan mandiri tanpa lipat',
        'items' => [
            ['name' => 'Mesin 4  Kg', 'price' => 'Rp 5.000'],
            ['name' => 'Mesin 8 Kg', 'price' => 'Rp 10.000'],
        ]
    ],
    [
        'id' => 'cuci-kering',
        'category' => 'Cuci Kering',
        'desc' => 'Pakaian kotor dicuci bersih lalu dikeringkan maksimal',
        'items' => [
            ['name' => 'Reguler (2 Hari) 4 Kg', 'price' => 'Rp 20.000'],
            ['name' => 'Reguler (2 Hari) 8 Kg', 'price' => 'Rp 30.000'],
            ['name' => 'Express (8 Jam) 4 Kg', 'price' => 'Rp 25.000'],
            ['name' => 'Express (8 Jam) 8 Kg', 'price' => 'Rp 35.000'],
        ]
    ],
    [
        'id' => 'cuci-lipat',
        'category' => 'Cuci Lipat 3 Kg',
        'desc' => 'Pakaian kotor dicuci bersih, kering secara maksimal dengan mesin yang besar, lalu pelipatan rapi.',
        'items' => [
            ['name' => 'Express 3 Jam', 'price' => 'Rp 13.000 /kg'],
            ['name' => 'Express 6 Jam', 'price' => 'Rp 10.000 /kg'],
            ['name' => 'SHS Satu Hari Saja (1 Hari)', 'price' => 'Rp 8.000 /kg'],
        ]
    ],
    [
        'id' => 'cuci-setrika',
        'category' => 'Cuci - Setrika Min 3 Kg',
        'desc' => 'Solusi lengkap untuk pakaian bersih, harum, dan rapi seketika. Minimal order 3kg.',
        'items' => [
            ['name' => 'Express 6 jam', 'price' => 'Rp 14.000 /kg'],
            ['name' => 'SHS Satu Hari Saja (1 Hari)', 'price' => 'Rp 9.500 /kg'],
            ['name' => 'Reguler (3 Hari)', 'price' => 'Rp 8.000 /kg'],
            ['name' => 'Paket Hemat 30kg / Bulan (Reguler)', 'price' => 'Rp 235.000'],
            ['name' => 'Paket Keluarga 50kg / Bulan (Reguler)', 'price' => 'Rp 385.000'],
            ['name' => 'Paket Premium 75kg / Bulan (Reguler)', 'price' => 'Rp 562.500'],
        ]
    ],
    [
        'id' => 'hanya-setrika',
        'category' => 'Hanya Setrika Min 3 Kg',
        'desc' => 'Hilangkan kerutan membandel dengan uap panas presisi. Minimal order 3kg.',
        'items' => [
            ['name' => 'Express 6 Jam', 'price' => 'Rp 10.000 /kg'],
            ['name' => 'Reguler (1 Hari)', 'price' => 'Rp 6.000 /kg'],
            ['name' => 'Paket 30kg / Bulan (Reguler)', 'price' => 'Rp 150.000'],
            ['name' => 'Paket 50kg / Bulan (Reguler)', 'price' => 'Rp 250.000'],
            ['name' => 'Paket 75kg / Bulan (Reguler)', 'price' => 'Rp 370.000'],
        ]
    ],
    [
        'id' => 'satuan-bedcover-seprei',
        'category' => 'Satuan Bedcover - Seprei',
        'desc' => 'Perawatan khusus yang detail',
        'items' => [
            ['name' => 'Bedcover Kecil', 'price' => 'Rp 30.000'],
            ['name' => 'Bedcover Besar', 'price' => 'Rp 35.000'],
            ['name' => 'Selimut Kecil', 'price' => 'Rp 15.000'],
            ['name' => 'Selimut Besar', 'price' => 'Rp 20.000'],
            ['name' => 'Sprei Set S (Bantal Guling 2)', 'price' => 'Rp 20.000'],
            ['name' => 'Sprei Set M (Bantal Guling 2)', 'price' => 'Rp 25.000'],
            ['name' => 'Sprei Set L (Bantal Guling 2)', 'price' => 'Rp 30.000'],
            ['name' => 'Penambahan Bantal 2', 'price' => 'Rp 5.000'],
            ['name' => 'Penambahan Guling 2', 'price' => 'Rp 5.000'],
        ]
    ],
    [
        'id' => 'satuan',
        'category' => 'Satuan',
        'desc' => 'Detail perawatan pakaian harian',
        'items' => [
            ['name' => 'Kebaya Biasa', 'price' => 'Rp 35.000'],
            ['name' => 'Kebaya Payet', 'price' => 'Rp 65.000'],
            ['name' => 'Stelan Kebaya Biasa', 'price' => 'Rp 50.000'],
            ['name' => 'Korset', 'price' => 'Rp 15.000'],
            ['name' => 'Kemben Luar', 'price' => 'Rp 20.000'],
            ['name' => 'Gamis Polos', 'price' => 'Rp 25.000'],
            ['name' => 'Gamis Mote', 'price' => 'Rp 40.000'],
            ['name' => 'Gaun Panjang', 'price' => 'Rp 40.000'],
            ['name' => 'Gaun Pendek', 'price' => 'Rp 35.000'],
            ['name' => 'Atasan', 'price' => 'Rp 15.000'],
            ['name' => 'Bawahan', 'price' => 'Rp 15.000'],
            ['name' => 'Jaket Biasa', 'price' => 'Rp 25.000'],
            ['name' => 'Jaket Tebal', 'price' => 'Rp 35.000'],
            ['name' => 'Jaket Kulit', 'price' => 'Rp 45.000'],
            ['name' => 'Jaket Tebal Bulu', 'price' => 'Rp 55.000'],
            ['name' => 'Hoodie', 'price' => 'Rp 25.000'],
            ['name' => 'Sweater', 'price' => 'Rp 30.000'],
            ['name' => 'Stelan Jas', 'price' => 'Rp 50.000'],
            ['name' => 'Jas', 'price' => 'Rp 30.000'],
            ['name' => 'Sendal', 'price' => 'Rp 10.000'],
            ['name' => 'Sepatu Sendal', 'price' => 'Rp 20.000'],
            ['name' => 'Sneakers', 'price' => 'Rp 45.000'],
            ['name' => 'Hordeng Tile / Bag Dalam /kg', 'price' => 'Rp 10.000'],
            ['name' => 'Hordeng Tipis /kg', 'price' => 'Rp 13.000'],
            ['name' => 'Hordeng Tebal /kg', 'price' => 'Rp 15.000'],
            ['name' => 'Boneka Mini', 'price' => 'Rp 20.000'],
            ['name' => 'Boneka Sedang', 'price' => 'Rp 30.000'],
            ['name' => 'Boneka Besar', 'price' => 'Rp 40.000'],
            ['name' => 'Slayer Outter Panjang', 'price' => 'Rp 15.000'],
            ['name' => 'Ciput Bulu', 'price' => 'Rp 10.000'],
            ['name' => 'Tas Kain Kecil', 'price' => 'Rp 20.000'],
            ['name' => 'Topi Rimba', 'price' => 'Rp 15.000'],
            ['name' => 'Bantal', 'price' => 'Rp 15.000'],
            ['name' => 'Guling', 'price' => 'Rp 15.000'],
            ['name' => 'Kemeja Panjang', 'price' => 'Rp 30.000'],
            ['name' => 'Kemeja Pendek', 'price' => 'Rp 25.000'],
        ]
    ],
    [
        'id' => 'dry-clean',
        'category' => 'Dry Clean',
        'desc' => 'Perawatan khusus tanpa air untuk pakaian bebas kerusakan',
        'items' => [
            ['name' => 'Kemeja Panjang', 'price' => 'Rp 35.000'],
            ['name' => 'Kemeja Pendek', 'price' => 'Rp 30.000'],
            ['name' => 'Celana Panjang', 'price' => 'Rp 35.000'],
        ]
    ]
];

// ─── Contact Info ───────────────────────────────────────────────────────────
$contact_info = [
    ['icon' => 'phone', 'text' => '+62 852-2096-6656', 'href' => 'https://wa.me/6285220966656'],
    ['icon' => 'mail', 'text' => 'laughndry@gmail.com', 'href' => ''],
    ['icon' => 'svg:instagram', 'text' => '@laughndry', 'href' => 'https://instagram.com/laughndry'],
    ['icon' => 'schedule', 'text' => 'Buka 24 Jam', 'href' => ''],
];
