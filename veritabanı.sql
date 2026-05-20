CREATE DATABASE IF NOT EXISTS oy_sistemi DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE oy_sistemi;

CREATE TABLE IF NOT EXISTS adaylar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aday_adi VARCHAR(100) NOT NULL,
    oy_sayisi INT DEFAULT 0,
    olusturma_tarihi TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Test etmek için başlangıç verileri
INSERT INTO adaylar (aday_adi, oy_sayisi) VALUES ('Aday Ahmet', 5), ('Aday Ayşe', 8);