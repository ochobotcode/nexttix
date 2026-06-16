-- ============================================================
--  NexTix — Concert Ticket Management System
--  Database Schema & Seed Data
--  Encoding: UTF-8
-- ============================================================

CREATE DATABASE IF NOT EXISTS nexttix CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nexttix;

-- ──────────────────────────────────────────────────────────
--  TABLE: admins  (admin & operator panel)
-- ──────────────────────────────────────────────────────────
CREATE TABLE admins (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50)  NOT NULL UNIQUE,
    email       VARCHAR(100) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    nama        VARCHAR(100) NOT NULL,
    role        ENUM('admin','operator') NOT NULL DEFAULT 'operator',
    foto        VARCHAR(255) DEFAULT NULL,
    is_active   TINYINT(1)  DEFAULT 1,
    last_login  DATETIME    DEFAULT NULL,
    created_at  TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP   DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ──────────────────────────────────────────────────────────
--  TABLE: users  (pelanggan / pembeli tiket)
-- ──────────────────────────────────────────────────────────
CREATE TABLE users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nama        VARCHAR(100) NOT NULL,
    email       VARCHAR(100) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    telepon     VARCHAR(20)  DEFAULT NULL,
    foto        VARCHAR(255) DEFAULT NULL,
    is_active   TINYINT(1)  DEFAULT 1,
    last_login  DATETIME    DEFAULT NULL,
    created_at  TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP   DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ──────────────────────────────────────────────────────────
--  TABLE: konser
-- ──────────────────────────────────────────────────────────
CREATE TABLE konser (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    slug            VARCHAR(255) NOT NULL UNIQUE,
    nama            VARCHAR(200) NOT NULL,
    artis           VARCHAR(200) NOT NULL,
    deskripsi       TEXT,
    tanggal         DATE         NOT NULL,
    jam             TIME         NOT NULL,
    venue           VARCHAR(255) NOT NULL,
    alamat          TEXT,
    kota            VARCHAR(100) NOT NULL,
    kapasitas       INT          DEFAULT 0,
    poster          VARCHAR(255) DEFAULT NULL,
    status          ENUM('upcoming','ongoing','completed','cancelled') DEFAULT 'upcoming',
    featured        TINYINT(1)   DEFAULT 0,
    created_by      INT,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ──────────────────────────────────────────────────────────
--  TABLE: tiket  (kategori tiket per konser)
-- ──────────────────────────────────────────────────────────
CREATE TABLE tiket (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    konser_id   INT          NOT NULL,
    nama        VARCHAR(100) NOT NULL,
    deskripsi   TEXT,
    harga       DECIMAL(15,2) NOT NULL,
    stok        INT          NOT NULL DEFAULT 0,
    terjual     INT          DEFAULT 0,
    is_active   TINYINT(1)  DEFAULT 1,
    created_at  TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP   DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (konser_id) REFERENCES konser(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ──────────────────────────────────────────────────────────
--  TABLE: orders  (transaksi pembelian)
-- ──────────────────────────────────────────────────────────
CREATE TABLE orders (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    kode            VARCHAR(30)  NOT NULL UNIQUE,
    user_id         INT          NOT NULL,
    tiket_id        INT          NOT NULL,
    jumlah          INT          NOT NULL DEFAULT 1,
    harga_satuan    DECIMAL(15,2) NOT NULL,
    total           DECIMAL(15,2) NOT NULL,
    status          ENUM('pending','paid','cancelled','refunded') DEFAULT 'pending',
    metode_bayar    VARCHAR(50)  DEFAULT NULL,
    bukti_bayar     VARCHAR(255) DEFAULT NULL,
    tgl_bayar       DATETIME     DEFAULT NULL,
    catatan         TEXT,
    processed_by    INT          DEFAULT NULL,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)      REFERENCES users(id)  ON DELETE RESTRICT,
    FOREIGN KEY (tiket_id)     REFERENCES tiket(id)  ON DELETE RESTRICT,
    FOREIGN KEY (processed_by) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ──────────────────────────────────────────────────────────
--  TABLE: activity_log
-- ──────────────────────────────────────────────────────────
CREATE TABLE activity_log (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    admin_id    INT          DEFAULT NULL,
    aksi        VARCHAR(100) NOT NULL,
    detail      TEXT,
    ip          VARCHAR(45),
    created_at  TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ──────────────────────────────────────────────────────────
--  INDEXES
-- ──────────────────────────────────────────────────────────
CREATE INDEX idx_konser_status  ON konser(status);
CREATE INDEX idx_konser_tanggal ON konser(tanggal);
CREATE INDEX idx_konser_kota    ON konser(kota);
CREATE INDEX idx_tiket_konser   ON tiket(konser_id);
CREATE INDEX idx_orders_user    ON orders(user_id);
CREATE INDEX idx_orders_tiket   ON orders(tiket_id);
CREATE INDEX idx_orders_status  ON orders(status);

-- ──────────────────────────────────────────────────────────
--  SEED: admins
--  Password untuk semua akun: admin123
--  Hash dibuat dengan password_hash('admin123', PASSWORD_BCRYPT)
-- ──────────────────────────────────────────────────────────
INSERT INTO admins (username, email, password, nama, role) VALUES
('admin',    'admin@nexttix.id',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Admin',   'admin'),
('operator', 'operator@nexttix.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Operator Staff', 'operator');

-- Catatan: hash di atas adalah hash bcrypt untuk string "password"
-- Jalankan reset_password.php setelah import untuk set password ke admin123

-- ──────────────────────────────────────────────────────────
--  SEED: users (pelanggan)
--  Password: user123
-- ──────────────────────────────────────────────────────────
INSERT INTO users (nama, email, password, telepon) VALUES
('Budi Santoso',    'budi@gmail.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567890'),
('Siti Rahma',      'siti@gmail.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '082345678901'),
('Ahmad Yusuf',     'ahmad@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '083456789012');

-- ──────────────────────────────────────────────────────────
--  SEED: konser
-- ──────────────────────────────────────────────────────────
INSERT INTO konser (slug, nama, artis, deskripsi, tanggal, jam, venue, alamat, kota, kapasitas, status, featured, created_by) VALUES
('eras-tour-jakarta-2026',    'The Eras Tour Jakarta',      'Taylor Swift',   'Saksikan Taylor Swift tampil live membawakan lagu-lagu terbaik dari semua era musiknya. Konser spektakuler dengan produksi kelas dunia!',              '2026-10-20', '19:00:00', 'Gelora Bung Karno',      'Jl. Pintu Satu Senayan, Jakarta Pusat',        'Jakarta',   80000, 'upcoming',   1, 1),
('coldplay-music-spheres-2026',    'Music of the Spheres World Tour', 'Coldplay',  'Coldplay kembali ke Indonesia! Nikmati pertunjukan visual dan musikal yang memukau dengan sentuhan warna-warni yang ikonik.',                         '2026-11-05', '20:00:00', 'Jakarta International Stadium', 'Jl. Cendrawasih, Papanggo, Jakarta Utara',   'Jakarta',   75000, 'upcoming',   1, 1),
('raisa-live-2026',           'Raisa Live in Concert 2026', 'Raisa',          'Malam penuh romantisme bersama Raisa. Full band performance dengan set list terlengkap sepanjang karier sang bintang pop Indonesia.',                   '2026-09-15', '19:30:00', 'ICE BSD City',           'BSD City, Tangerang Selatan',                  'Tangerang', 15000, 'upcoming',   1, 1),
('java-jazz-2026',            'Java Jazz Festival 2026',    'Various Artists','Festival jazz terbesar di Asia Tenggara. Lebih dari 100 penampil dari berbagai genre: jazz, R&B, soul, dan banyak lagi!',                             '2026-08-01', '14:00:00', 'JIExpo Kemayoran',       'Jakarta International Expo, Kemayoran',        'Jakarta',   50000, 'upcoming',   0, 1),
('dewa19-reuni-2026',              'Dewa 19 Reunion Concert',   'Dewa 19',         'Reuni bersejarah Dewa 19 dengan seluruh personel asli! Malam penuh kenangan dengan lagu-lagu legendaris yang menemani perjalanan hidup Anda.',          '2026-09-12', '19:00:00', 'Istora Senayan',         'Jl. Pintu Satu Senayan, Jakarta',              'Jakarta',   12000, 'upcoming',   0, 1),
('noah-neverland-tour-2026',       'Neverland Tour 2026',       'NOAH',            'NOAH mengajak fans menjelajahi dunia Neverland melalui musik. Siapkan diri untuk malam yang penuh emosi dan nostalgia.',                               '2026-08-28', '20:00:00', 'Kota Kasablanka Hall', 'Mall Kota Kasablanka, Jakarta Selatan',         'Jakarta',   5000,  'upcoming',   0, 1);

-- ──────────────────────────────────────────────────────────
--  SEED: tiket
-- ──────────────────────────────────────────────────────────
INSERT INTO tiket (konser_id, nama, deskripsi, harga, stok, terjual) VALUES
-- Taylor Swift
(1, 'CAT 1 – Festival Standing', 'Area terdekat dari panggung, pengalaman terbaik', 3500000, 10000, 6200),
(1, 'CAT 2 – Tribune A',         'Kursi tribun dengan pandangan premium',           2500000, 20000, 14800),
(1, 'CAT 3 – Tribune B',         'Kursi tribun dengan pandangan baik',              1500000, 30000, 22100),
(1, 'VVIP – Early Bird',         'Paket VVIP termasuk merchandise eksklusif',       5000000,  2000,  1950),
-- Coldplay
(2, 'VVIP Lounge',               'Lounge premium + akses backstage area',           6000000,  3000,  2990),
(2, 'CAT A – Premium',           'Kursi terbaik dengan view sempurna',              4000000, 10000,  9200),
(2, 'CAT B – Regular',           'Kursi nyaman dengan view yang bagus',             2500000, 25000, 20000),
(2, 'Festival Standing',         'Area berdiri dekat panggung',                     2000000, 20000, 18500),
-- Raisa
(3, 'VVIP – Meet & Greet',       'Termasuk sesi meet & greet + foto bersama Raisa', 3000000,   200,   195),
(3, 'VIP',                       'Kursi baris depan, pengalaman terbaik',           1500000,  2000,  1600),
(3, 'Regular A',                 'Kursi dengan pandangan baik ke panggung',          750000,  6000,  4800),
(3, 'Regular B',                 'Kursi area belakang, tetap seru!',                500000,  6800,  4200),
-- Java Jazz
(4, 'Pass 3 Hari',               'Akses 3 hari penuh ke semua panggung',            1500000,  5000,  3500),
(4, 'Pass Harian',               'Akses 1 hari, pilih tanggal saat checkout',        600000, 30000, 21000),
-- Dewa 19
(5, 'VIP Gold',                  'Kursi premium paling dekat panggung',             1200000,  2000,  1800),
(5, 'Regular',                   'Kursi reguler dengan pandangan yang baik',         600000, 10000,  7200),
-- NOAH
(6, 'VVIP',                      'Kursi terdepan + eksklusif merchandise',          1500000,   500,   480),
(6, 'VIP',                       'Kursi premium dengan pandangan terbaik',          1000000,  1500,  1200),
(6, 'Regular',                   'Kursi reguler, enjoy the show!',                   500000,  3000,  2100);

-- ──────────────────────────────────────────────────────────
--  SEED: orders
-- ──────────────────────────────────────────────────────────
INSERT INTO orders (kode, user_id, tiket_id, jumlah, harga_satuan, total, status, metode_bayar, tgl_bayar, processed_by) VALUES
('NXT-20250101-001', 1, 1,  2, 3500000,  7000000, 'paid', 'Transfer Bank',  '2025-01-15 10:30:00', 1),
('NXT-20250101-002', 2, 3,  4, 1500000,  6000000, 'paid', 'QRIS',           '2025-01-16 14:20:00', 1),
('NXT-20250101-003', 3, 5,  2, 6000000, 12000000, 'paid', 'GoPay',          '2025-01-17 09:15:00', 1),
('NXT-20250102-001', 1, 9,  1, 3000000,  3000000, 'paid', 'OVO',            '2025-01-20 11:45:00', 1),
('NXT-20250102-002', 2, 13, 3, 1500000,  4500000, 'paid', 'Transfer Bank',  '2025-01-22 16:00:00', 1),
('NXT-20250103-001', 3, 6,  2, 4000000,  8000000, 'pending', NULL, NULL, NULL),
('NXT-20250103-002', 1, 15, 1,  600000,   600000, 'cancelled', NULL, NULL, 1),
('NXT-20250104-001', 2, 17, 2, 1500000,  3000000, 'paid', 'Dana',           '2025-02-01 09:00:00', 1),
('NXT-20250104-002', 3, 11, 3,  750000,  2250000, 'paid', 'QRIS',           '2025-02-03 13:30:00', 1),
('NXT-20250105-001', 1, 7,  2, 2500000,  5000000, 'paid', 'Transfer Bank',  '2025-02-05 10:00:00', 1);

-- ──────────────────────────────────────────────────────────
--  VIEW: v_order_detail
-- ──────────────────────────────────────────────────────────
CREATE OR REPLACE VIEW v_order_detail AS
SELECT
    o.id, o.kode, o.user_id, o.jumlah, o.harga_satuan, o.total,
    o.status, o.metode_bayar, o.tgl_bayar, o.created_at,
    u.nama  AS nama_pembeli,
    u.email AS email_pembeli,
    u.telepon,
    t.nama  AS nama_tiket,
    k.id    AS konser_id,
    k.slug  AS slug,
    k.nama  AS nama_konser,
    k.artis,
    k.tanggal AS tanggal_konser,
    k.jam   AS jam_konser,
    k.venue,
    k.kota
FROM orders o
JOIN users  u ON o.user_id  = u.id
JOIN tiket  t ON o.tiket_id = t.id
JOIN konser k ON t.konser_id = k.id;
