CREATE DATABASE laundry_db;
USE laundry_db;

-- Tabel pelanggan
CREATE TABLE pelanggan (
    id_pelanggan INT AUTO_INCREMENT PRIMARY KEY,
    nama_pelanggan VARCHAR(100) NOT NULL
);

-- Tabel jenis laundry
CREATE TABLE jenis_laundry (
    id_jenis INT AUTO_INCREMENT PRIMARY KEY,
    nama_jenis VARCHAR(50) NOT NULL,
    harga INT NOT NULL
);

-- Tabel transaksi
CREATE TABLE transaksi (
    id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
    id_pelanggan INT,
    tanggal_terima DATE,
    tanggal_selesai DATE,
    FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan)
);

-- Tabel detail transaksi
CREATE TABLE detail_transaksi (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_transaksi INT,
    id_jenis INT,
    jumlah INT,
    total INT,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi),
    FOREIGN KEY (id_jenis) REFERENCES jenis_laundry(id_jenis)
);

-- Data awal
INSERT INTO pelanggan (nama_pelanggan) VALUES ('Andi'), ('Budi'), ('Dodi'), ('Eka'), ('Fani');
INSERT INTO jenis_laundry (nama_jenis, harga) VALUES 
('Pakaian', 5000), 
('Seprai', 15000), 
('Selimut', 20000), 
('Jaket', 10000), 
('Karpet', 30000);