-- SIMPESISIR: Sistem Informasi Pencatatan Pengamatan Ekosistem Pesisir

CREATE DATABASE IF NOT EXISTS db_simpesisir
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_simpesisir;

DROP TABLE IF EXISTS pengamatan;
DROP TABLE IF EXISTS lokasi;
DROP TABLE IF EXISTS ekosistem;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id_user       INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nama          VARCHAR(100) NOT NULL,
  username      VARCHAR(50)  NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role          ENUM('admin','petugas') NOT NULL DEFAULT 'petugas',
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_user),
  UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB;

CREATE TABLE ekosistem (
  id_ekosistem   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nama_ekosistem VARCHAR(50) NOT NULL,
  PRIMARY KEY (id_ekosistem),
  UNIQUE KEY uq_ekosistem_nama (nama_ekosistem)
) ENGINE=InnoDB;

CREATE TABLE lokasi (
  id_lokasi      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nama_lokasi    VARCHAR(100) NOT NULL,
  kabupaten_kota VARCHAR(100) NOT NULL,
  keterangan     VARCHAR(255) NULL,
  PRIMARY KEY (id_lokasi),
  UNIQUE KEY uq_lokasi (nama_lokasi, kabupaten_kota)
) ENGINE=InnoDB;

CREATE TABLE pengamatan (
  id_pengamatan  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_lokasi      INT UNSIGNED NOT NULL,
  id_ekosistem   INT UNSIGNED NOT NULL,
  id_user        INT UNSIGNED NOT NULL,
  tanggal        DATE NOT NULL,
  kondisi        ENUM('baik','sedang','rusak') NOT NULL,
  tutupan_persen DECIMAL(5,2) NOT NULL,
  catatan        VARCHAR(500) NULL,
  created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_pengamatan),
  CONSTRAINT chk_tutupan CHECK (tutupan_persen BETWEEN 0 AND 100),
  CONSTRAINT fk_pengamatan_lokasi    FOREIGN KEY (id_lokasi)    REFERENCES lokasi(id_lokasi)       ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_pengamatan_ekosistem FOREIGN KEY (id_ekosistem) REFERENCES ekosistem(id_ekosistem) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_pengamatan_user      FOREIGN KEY (id_user)      REFERENCES users(id_user)          ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- DATA AWAL
-- Password disimpan sebagai hash bcrypt (hasil password_hash PHP). Password awal disampaikan terpisah.
INSERT INTO users (nama, username, password_hash, role) VALUES
 ('Administrator',  'admin',   '$2y$10$W7FQ/G/NzNInOgvqfbz5pu5BQXQCtRIpsNKWTukSSqw56OebZXjNG', 'admin'),
 ('Petugas Lapangan','petugas1','$2y$10$IGoK0K6XYCGMJMHJHq2MSu6oM9e.mXFRBvNR/BEyJmWtpFWTRFHuq', 'petugas');

INSERT INTO ekosistem (nama_ekosistem) VALUES ('Mangrove'), ('Lamun'), ('Terumbu Karang');

INSERT INTO lokasi (nama_lokasi, kabupaten_kota, keterangan) VALUES
 ('Pulau Pari',      'Kepulauan Seribu', 'Stasiun pengamatan sisi utara'),
 ('Pantai Tanjung Lesung', 'Pandeglang', 'Transek sepanjang 50 m'),
 ('Teluk Banten',    'Serang',           NULL);

INSERT INTO pengamatan (id_lokasi, id_ekosistem, id_user, tanggal, kondisi, tutupan_persen, catatan) VALUES
 (1, 2, 2, '2026-10-05', 'baik',   72.50, 'Dominan Thalassia hemprichii'),
 (2, 1, 2, '2026-10-08', 'sedang', 48.00, 'Ada penebangan di sisi timur'),
 (3, 3, 2, '2026-10-12', 'rusak',  21.25, 'Indikasi pemutihan karang');
