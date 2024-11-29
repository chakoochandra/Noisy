-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               5.7.24-log - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table noisy.tmst_web
CREATE TABLE IF NOT EXISTS `tmst_web` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `url` varchar(250) NOT NULL,
  `category` enum('Socmed','Lokal','Web','MA','Badilag','PTA Surabaya','Lain-lain') NOT NULL,
  `icon` varchar(250) DEFAULT NULL,
  `order` tinyint(2) DEFAULT NULL,
  `icon_width` int(11) DEFAULT NULL,
  `icon_height` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=latin1;

-- Dumping data for table noisy.tmst_web: ~97 rows (approximately)
/*!40000 ALTER TABLE `tmst_web` DISABLE KEYS */;
INSERT INTO `tmst_web` (`id`, `name`, `url`, `category`, `icon`, `order`, `icon_width`, `icon_height`, `is_active`) VALUES
	(1, 'Mahkamah Agung RI', 'https://mahkamahagung.go.id', 'MA', 'ma.png', 1, NULL, NULL, 1),
	(2, 'SIWAS MA-RI', 'https://siwas.mahkamahagung.go.id', 'MA', 'siwas.png', NULL, NULL, NULL, 1),
	(3, 'JDIH MA-RI', 'https://jdih.mahkamahagung.go.id', 'MA', 'jdih_ma.png', NULL, NULL, NULL, 1),
	(4, 'e-Court MA-RI', 'https://ecourt.mahkamahagung.go.id', 'MA', 'ecourt.png', NULL, NULL, NULL, 1),
	(5, 'Direktori Putusan Admin', 'https://putusan.mahkamahagung.go.id/admin/main', 'MA', 'dirput_v2.png', NULL, NULL, NULL, 1),
	(6, 'Direktori Putusan', 'https://putusan3.mahkamahagung.go.id', 'MA', 'dirput_v3.png', NULL, NULL, NULL, 1),
	(7, 'SIKEP', 'https://sikep.mahkamahagung.go.id', 'MA', 'sikep.png', NULL, NULL, NULL, 1),
	(8, 'SIMARI', 'https://simari.mahkamahagung.go.id', 'MA', 'simari.png', NULL, NULL, NULL, 1),
	(9, 'KOMDANAS', 'https://komdanas.mahkamahagung.go.id', 'MA', 'komdanas.png', NULL, NULL, NULL, 1),
	(10, 'e-SADEWA', 'https://e-sadewa.mahkamahagung.go.id', 'MA', 'esadewa.png', NULL, NULL, NULL, 1),
	(11, 'e-BIMA', 'https://e-bima.mahkamahagung.go.id', 'MA', 'ebima.png', NULL, NULL, NULL, 1),
	(12, 'e-IPLANS', 'https://eiplans.mahkamahagung.go.id', 'MA', 'eiplans.png', NULL, NULL, NULL, 1),
	(13, 'e-PRIMA', 'https://e-prima.mahkamahagung.go.id', 'MA', 'eprima.png', NULL, NULL, NULL, 1),
	(14, 'e-Learning MA-RI', 'https://elearning.mahkamahagung.go.id', 'MA', 'elearning.png', NULL, NULL, NULL, 1),
	(15, 'SIPP MA-RI', 'https://sipp-ma.mahkamahagung.go.id', 'MA', 'sipp_ma.png', NULL, NULL, NULL, 1),
	(16, 'Perpustakaan MA-RI', 'https://perpustakaan.mahkamahagung.go.id', 'MA', 'perpustakaan_ma.png', NULL, NULL, NULL, 1),
	(17, 'Email MA-RI', 'https://vmail.mahkamahagung.go.id', 'MA', 'email.png', NULL, NULL, NULL, 1),
	(18, 'Informasi Perkara', 'https://kepaniteraan.mahkamahagung.go.id/perkara/', 'MA', 'info_perkara.png', NULL, NULL, NULL, 1),
	(19, 'PPID MA-RI', 'https://eppid.mahkamahagung.go.id/web/beranda', 'MA', 'ppid.png', NULL, NULL, NULL, 1),
	(20, 'SAKIP MA-RI', 'https://www.mahkamahagung.go.id/id/sakip', 'MA', 'sakip.png', NULL, NULL, NULL, 1),
	(21, 'Badan Litbang Diklat MA-RI', 'https://bldk.mahkamahagung.go.id/id/', 'MA', 'bldk.png', NULL, NULL, NULL, 1),
	(22, 'Kepaniteraan MA-RI', 'https://kepaniteraan.mahkamahagung.go.id/', 'MA', 'kepaniteraan.png', NULL, NULL, NULL, 1),
	(23, 'Email MA-RI', 'https://mail.mahkamahagung.go.id', 'MA', 'email.png', NULL, NULL, NULL, 1),
	(24, 'Buku Puslitbang Kumdil', 'https://ebook.bldk.mahkamahagung.go.id/', 'MA', 'ebook.png', NULL, NULL, NULL, 1),
	(25, 'Library Of MA Corporate University', 'https://perpustakaan.bldk.mahkamahagung.go.id/', 'MA', 'corporate_university.png', NULL, NULL, NULL, 1),
	(26, 'Badilag', 'https://badilag.mahkamahagung.go.id', 'Badilag', 'badilag.png', 1, NULL, NULL, 1),
	(27, 'KINSATKER', 'https://kinsatker.badilag.net', 'Badilag', 'kinsatker.png', NULL, NULL, NULL, 1),
	(28, 'Gugatan Mandiri', 'https://gugatanmandiri.badilag.net/gugatan_mandiri', 'Badilag', '', NULL, NULL, NULL, 0),
	(29, 'ACO', 'https://cctv.badilag.net', 'Badilag', 'aco.png', NULL, NULL, NULL, 1),
	(30, 'SIMTALAK', 'https://simtalak.badilag.net', 'Badilag', 'simtalak.png', NULL, NULL, NULL, 1),
	(31, 'ABS', 'https://abs.badilag.net', 'Badilag', 'abs.png', NULL, NULL, NULL, 1),
	(32, 'SIMTEPA', 'https://simtepa.mahkamahagung.go.id', 'Badilag', 'simtepa.png', NULL, NULL, NULL, 1),
	(33, 'ELEMENT BADILAG', 'https://legalisasi.badilag.ne', 'Badilag', 'element.png', NULL, NULL, NULL, 1),
	(34, 'PTA Surabaya', 'https://pta-surabaya.go.id', 'PTA Surabaya', 'pta.png', 1, NULL, NULL, 1),
	(35, 'e-Kiper', 'https://ekiper.pta-surabaya.go.id', 'PTA Surabaya', 'ekiper.png', NULL, NULL, NULL, 1),
	(36, 'e-Bundling', 'https://data.pta-surabaya.go.id/ebundling/', 'PTA Surabaya', 'ebundling.png', NULL, NULL, NULL, 1),
	(37, 'SIAP', 'https://siap.pta-surabaya.go.id', 'PTA Surabaya', 'siap.png', NULL, NULL, NULL, 1),
	(38, 'Rekapitulasi Perkara', 'https://pta-surabaya.go.id/laporan/', 'PTA Surabaya', 'pelaporan_pta.png', NULL, NULL, NULL, 1),
	(39, 'Gandrung', 'https://gandrung.pta-surabaya.go.id', 'PTA Surabaya', 'gandrung.png', NULL, NULL, NULL, 1),
	(40, 'Info Perkara Banding', 'https://infoperkara.pta-surabaya.go.id', 'PTA Surabaya', 'info_perkara_pta.png', NULL, NULL, NULL, 1),
	(41, 'Berita PA Se-Jatim', 'https://pta-surabaya.go.id/main/pengadilan_berita', 'PTA Surabaya', 'siap.png', NULL, NULL, NULL, 1),
	(42, 'Sinkronisasi SIPP Banding', 'https://data.pta-surabaya.go.id', 'PTA Surabaya', 'status_sinkron_pta.png', NULL, NULL, NULL, 1),
	(43, 'Web PA Sidoarjo', 'https://pa-sidoarjo.go.id/', 'Web', 'web.png', NULL, NULL, NULL, 1),
	(44, 'SIASTER', 'https://sidang.pa-sidoarjo.go.id/', 'Web', 'siaster.png', NULL, NULL, NULL, 1),
	(45, 'SIPP WEB', 'https://sipp.pa-sidoarjo.go.id/', 'Web', 'sipp_web.png', NULL, NULL, NULL, 1),
	(46, 'SIPANDAWA', 'http://wa.me/6289529203020', 'Web', 'pengaduan.png', NULL, NULL, NULL, 1),
	(47, 'SIJANGKAR', 'https://ptsp.pa-sidoarjo.go.id', 'Web', 'sijangkar.png', NULL, NULL, NULL, 1),
	(48, 'SIWALI', 'http://wa.me/6281332902016', 'Web', 'pengaduan.png', NULL, NULL, NULL, 1),
	(49, 'Pengaduan', 'http://wa.me/6281332902016', 'Web', 'pengaduan.png', NULL, NULL, NULL, 1),
	(50, 'SIPP', 'http://192.168.1.14/sipp', 'Lokal', 'sipp.png', 1, NULL, NULL, 1),
	(51, 'Pendukung', 'http://192.168.1.14/pendukung', 'Lokal', 'pendukung.png', 3, NULL, NULL, 1),
	(53, 'Web Monitoring', 'https://www.mahkamahagung.go.id/id/webmon', 'MA', 'webmon.png', NULL, NULL, NULL, 1),
	(54, 'MyASN', 'https://myasn.bkn.go.id/', 'Lain-lain', 'myasn.png', NULL, NULL, NULL, 1),
	(55, 'SIASN', 'https://siasn.bkn.go.id/', 'Lain-lain', 'siasn.png', NULL, NULL, NULL, 1),
	(56, 'e-Kinerja', 'https://kinerja.bkn.go.id', 'Lain-lain', 'kinerja.png', NULL, NULL, NULL, 1),
	(57, 'SAKTI', 'https://sakti.kemenkeu.go.id', 'Lain-lain', 'sakti.png', NULL, NULL, NULL, 1),
	(58, 'SIRUP', 'https://sirup.lkpp.go.id', 'Lain-lain', 'sirup.png', NULL, NULL, NULL, 1),
	(59, 'DJP ONLINE', 'https://djponline.pajak.go.id', 'Lain-lain', 'djp.png', NULL, NULL, NULL, 1),
	(60, 'SIHARKA LHKPN / LHKASN', 'https://siharka.menpan.go.id', 'Lain-lain', 'siharka.png', NULL, NULL, NULL, 1),
	(61, 'Hukum Online', 'https://www.hukumonline.com', 'Lain-lain', 'hukum_online.png', NULL, NULL, NULL, 1),
	(62, 'Facebook', 'https://www.facebook.com/pengadilanagama.sidoarjo', 'Socmed', 'facebook-new.png', NULL, NULL, NULL, 1),
	(63, 'Instagram', 'https://instagram.com/pasidoarjo_', 'Socmed', 'instagram-new.png', NULL, NULL, NULL, 1),
	(64, 'X', 'https://twitter.com/PASidoarjo_', 'Socmed', 'x.png', NULL, NULL, NULL, 1),
	(65, 'Tiktok', 'https://www.tiktok.com/@pasidoarjo', 'Socmed', 'tik-tok.png', NULL, NULL, NULL, 1),
	(66, 'Youtube', 'https://youtube.com/channel/UCkkJVJezC4ZUXOdSsxmDqZw', 'Socmed', 'play-button-circled.png', NULL, NULL, NULL, 1),
	(67, 'Google Maps', 'https://g.page/r/CQRvpn0Z-orBEBE/review', 'Socmed', 'gmap.png', NULL, NULL, NULL, 1),
	(69, 'Dirput PA Sidoarjo', 'https://putusan3.mahkamahagung.go.id/pengadilan/profil/pengadilan/pa-sidoarjo.html', 'MA', '', NULL, NULL, NULL, 1),
	(70, 'APS Badilag', 'http://192.168.1.14/aps_badilag', 'Lokal', 'aps.png', NULL, NULL, NULL, 1),
	(76, 'LPSE', 'https://lpse.mahkamahagung.go.id/eproc4', 'MA', 'lpse.png', NULL, NULL, NULL, 1),
	(78, 'Peta e-Court', 'https://ecourt.mahkamahagung.go.id/mapecourt_agama', 'MA', 'map.png', NULL, NULL, NULL, 1),
	(82, 'Penilaian Triwulan', 'https://kinsatker.badilag.net/penilaiantriwulan', 'Badilag', 'triwulan.png', NULL, NULL, NULL, 1),
	(83, 'LLK', 'https://llk.mahkamahagung.go.id/', 'MA', 'llk.png', NULL, NULL, NULL, 1),
	(84, 'PNBP', 'https://pnbp.mahkamahagung.go.id/', 'MA', 'pnbp.png', NULL, NULL, NULL, 1),
	(85, 'TTE SIMARI', 'https://simari.mahkamahagung.go.id/tte', 'MA', 'tte.png', NULL, NULL, NULL, 1),
	(92, 'Jadwal Sidang', 'https://ptsp.pa-sidoarjo.go.id/daftar_antrian_sidang', 'Web', 'sijangkar.png', NULL, NULL, NULL, 1);
/*!40000 ALTER TABLE `tmst_web` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
