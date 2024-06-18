-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.24-log - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             11.2.0.6213
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for noisy
CREATE DATABASE IF NOT EXISTS `noisy` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `noisy`;

-- Dumping structure for table noisy.ci_sessions
CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  PRIMARY KEY (`id`,`ip_address`),
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table noisy.ci_sessions: ~0 rows (approximately)
/*!40000 ALTER TABLE `ci_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `ci_sessions` ENABLE KEYS */;

-- Dumping structure for table noisy.configs
CREATE TABLE IF NOT EXISTS `configs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) DEFAULT NULL,
  `value` varchar(250) DEFAULT NULL,
  `category` tinyint(4) DEFAULT NULL,
  `note` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

-- Dumping data for table noisy.configs: ~16 rows (approximately)
/*!40000 ALTER TABLE `configs` DISABLE KEYS */;
INSERT INTO `configs` (`id`, `key`, `value`, `category`, `note`) VALUES
	(1, 'APP_VERSION', '1.0', 5, 'string. versi aplikasi'),
	(2, 'APP_NAME', 'Whatsapp Notification System', 5, 'string. nama aplikasi'),
	(3, 'APP_SHORT_NAME', 'NOISY', 5, 'string. nama pendek aplikasi. [BACKUP - NANTI DIHAPUS]'),
	(4, 'SATKER_NAME', 'Pengadilan Agama ABC', 1, 'string. nama satker'),
	(5, 'SATKER_ADDRESS', 'Jalan ABC No. 123', 1, 'string. nama satker'),
	(6, 'DIALOGWA_API_URL', 'https://dialogwa.id/api', 2, 'string. url api dialogwa.id'),
	(7, 'DIALOGWA_TOKEN', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjY1ZjNiMjIyZWY1MmJjMzc4MDYxM2U1OSIsInVzZXJuYW1lIjoiY2hhbmRyYSIsImlhdCI6MTcxNzc0Nzc4NywiZXhwIjo0ODczNTA3Nzg3fQ.KIqEs7rELJzVj2hk6WJqCiYy0T0Mz7G5vbiy4gFLRQ0', 2, 'string. token dialogwa.id'),
	(8, 'DIALOGWA_SESSION', 'demo', 2, 'string. sesi online dialogwa.id'),
	(9, 'WA_TEST_TARGET', '', 2, 'string. nomor WA untuk tes penerima notifikasi'),
	(10, 'DAY_START_ANTRIAN', '1', 3, 'int. start hari sidang. sistem akan mencari jadwal sidang mulai tanggal ini'),
	(11, 'DAY_END_ANTRIAN', '1', 3, 'int. end hari sidang. sistem akan mencari jadwal sidang sampai tanggal ini'),
	(12, 'DAY_START_SIDANG', '2', 3, 'int. start hari sidang. sistem akan mencari jadwal sidang mulai tanggal ini'),
	(13, 'DAY_END_SIDANG', '6', 3, 'int. end hari sidang. sistem akan mencari jadwal sidang sampai tanggal ini'),
	(14, 'DAY_END_CALENDAR', '6', 3, 'int. end hari calendar. sistem akan mencari jadwal agenda sampai tanggal ini'),
	(15, 'DAY_START_JURNAL', '-30', 3, 'int. start hari jurnal. sistem akan mencari perkara putus sejak tanggal ini (NEGATIVE VALUE)'),
	(16, 'DAY_START_AC', '-30', 3, 'int. start hari ac. sistem akan mencari ac terbit sejak tanggal ini (NEGATIVE VALUE)');
/*!40000 ALTER TABLE `configs` ENABLE KEYS */;

-- Dumping structure for table noisy.trans_message_whatsapp
CREATE TABLE IF NOT EXISTS `trans_message_whatsapp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sent_time` datetime NOT NULL,
  `sent_by` varchar(50) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `type` varchar(50) NOT NULL,
  `reference` varchar(50) NOT NULL,
  `perkara_id` varchar(50) NOT NULL,
  `callback` varchar(50) NOT NULL,
  `text` text NOT NULL,
  `success` tinyint(1) NOT NULL DEFAULT '0',
  `note` varchar(100) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table noisy.trans_message_whatsapp: ~0 rows (approximately)
/*!40000 ALTER TABLE `trans_message_whatsapp` DISABLE KEYS */;
/*!40000 ALTER TABLE `trans_message_whatsapp` ENABLE KEYS */;

-- Dumping structure for table noisy.tref_menu
CREATE TABLE IF NOT EXISTS `tref_menu` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `label` varchar(50) NOT NULL,
  `parent` tinyint(2) DEFAULT NULL,
  `icon` varchar(25) DEFAULT NULL,
  `iconClass` varchar(25) DEFAULT NULL,
  `menuClass` varchar(25) DEFAULT NULL,
  `url` varchar(100) DEFAULT NULL,
  `order` tinyint(2) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Dumping data for table noisy.tref_menu: ~2 rows (approximately)
/*!40000 ALTER TABLE `tref_menu` DISABLE KEYS */;
INSERT INTO `tref_menu` (`id`, `label`, `parent`, `icon`, `iconClass`, `menuClass`, `url`, `order`, `status`) VALUES
	(1, 'Daftar Kirim', NULL, 'clock-o', NULL, NULL, 'whatsapp/next', 1, 1),
	(2, 'Riwayat Notifikasi', NULL, 'commenting-o', NULL, NULL, 'whatsapp', 2, 1);
/*!40000 ALTER TABLE `tref_menu` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
