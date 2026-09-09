-- ========================================================
-- BACKUP DATA PEMUDA MTA KABUPATEN SRAGEN
-- Waktu Pembuatan : 2026-09-09 19:19:21
-- Total Pemuda    : 3 data
-- Sistem          : Sistem Pendataan Pemuda MTA Sragen
-- ========================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET NAMES utf8mb4;

-- --------------------------------------------------------
-- Data untuk tabel `pemuda` (3 baris)
-- --------------------------------------------------------
INSERT INTO `pemuda` (`id`, `cabang_id`, `registration_number`, `name`, `gender`, `marital_status`, `birth_place`, `birth_date`, `blood_type`, `phone`, `email`, `status_verifikasi`, `status_data`, `mta_warga_uuid`, `mta_status_warga`, `mta_ayah_uuid`, `mta_ibu_uuid`, `mta_foto_url`, `mta_synced_at`, `created_by`, `created_at`, `updated_at`) VALUES
('4', '1', 'T1788956316560', 'pemuda uji coba reset', 'L', 'belum_menikah', NULL, '2000-01-01', NULL, '081234567899', NULL, 'pending', 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-09 19:18:36', NULL),
('5', '1', 'T1788956349315', 'pemuda uji coba reset', 'L', 'belum_menikah', NULL, '2000-01-01', NULL, '081234567899', NULL, 'pending', 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-09 19:19:09', NULL),
('6', '1', 'T1788956360952', 'pemuda uji coba reset', 'L', 'belum_menikah', NULL, '2000-01-01', NULL, '081234567899', NULL, 'pending', 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-09 19:19:20', NULL);

-- --------------------------------------------------------
-- Data untuk tabel `alamat` (3 baris)
-- --------------------------------------------------------
INSERT INTO `alamat` (`id`, `pemuda_id`, `province_id`, `regency_id`, `district_id`, `village_id`, `dusun`, `rt`, `rw`, `address_detail`, `created_at`, `updated_at`) VALUES
('3', '4', '33', '3314', '4', '6', NULL, NULL, NULL, 'alamat dummy test reset', '2026-09-09 19:18:36', NULL),
('4', '5', '33', '3314', '4', '6', NULL, NULL, NULL, 'alamat dummy test reset', '2026-09-09 19:19:09', NULL),
('5', '6', '33', '3314', '4', '6', NULL, NULL, NULL, 'alamat dummy test reset', '2026-09-09 19:19:20', NULL);

-- --------------------------------------------------------
-- Data untuk tabel `pendidikan` (0 baris)
-- --------------------------------------------------------
-- Tidak ada data pada tabel `pendidikan`

-- --------------------------------------------------------
-- Data untuk tabel `pekerjaan` (0 baris)
-- --------------------------------------------------------
-- Tidak ada data pada tabel `pekerjaan`

-- --------------------------------------------------------
-- Data untuk tabel `organisasi` (3 baris)
-- --------------------------------------------------------
INSERT INTO `organisasi` (`id`, `pemuda_id`, `organization_name`, `description`, `created_at`, `updated_at`) VALUES
('1', '4', 'satgas test', NULL, '2026-09-09 19:18:36', NULL),
('2', '5', 'satgas test', NULL, '2026-09-09 19:19:09', NULL),
('3', '6', 'satgas test', NULL, '2026-09-09 19:19:20', NULL);

-- --------------------------------------------------------
-- Data untuk tabel `pemuda_skills` (0 baris)
-- --------------------------------------------------------
-- Tidak ada data pada tabel `pemuda_skills`

-- --------------------------------------------------------
-- Data untuk tabel `pemuda_interests` (0 baris)
-- --------------------------------------------------------
-- Tidak ada data pada tabel `pemuda_interests`

-- --------------------------------------------------------
-- Data untuk tabel `mta_sync_queue` (0 baris)
-- --------------------------------------------------------
-- Tidak ada data pada tabel `mta_sync_queue`

SET FOREIGN_KEY_CHECKS = 1;
-- ========================================================
-- AKHIR DUMP DATA PEMUDA
-- ========================================================
