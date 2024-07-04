-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               5.7.24 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for dbinact
CREATE DATABASE IF NOT EXISTS `dbinact` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `dbinact`;

-- Dumping structure for table dbinact.commadrequest
CREATE TABLE IF NOT EXISTS `commadrequest` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `command` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.data_finger
CREATE TABLE IF NOT EXISTS `data_finger` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `post_data` text,
  `get_data` text,
  `datecreated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=998 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.iasubscription
CREATE TABLE IF NOT EXISTS `iasubscription` (
  `iasubscription_id` double NOT NULL AUTO_INCREMENT,
  `appid` varchar(255) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `company_addr` text,
  `company_city` varchar(50) DEFAULT NULL,
  `company_province` varchar(50) DEFAULT NULL,
  `company_country` varchar(50) DEFAULT NULL,
  `company_telp` varchar(50) DEFAULT NULL,
  `company_email` varchar(50) DEFAULT NULL,
  `company_websiteurl` varchar(255) DEFAULT NULL,
  `company_size` varchar(15) DEFAULT NULL,
  `registration_date` datetime DEFAULT NULL,
  `active_date` datetime DEFAULT NULL,
  `status` enum('pending','active','suspend','stop') NOT NULL,
  PRIMARY KEY (`iasubscription_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.iauser
CREATE TABLE IF NOT EXISTS `iauser` (
  `userid` bigint(20) NOT NULL AUTO_INCREMENT,
  `appid` varchar(255) NOT NULL,
  `user_emailaddr` varchar(255) NOT NULL,
  `user_fullname` char(100) DEFAULT NULL,
  `user_phone` varchar(20) DEFAULT NULL,
  `user_datecreate` datetime DEFAULT NULL,
  `user_dateactive` datetime DEFAULT NULL,
  `user_islogin` int(1) NOT NULL DEFAULT '0',
  `user_isactive` int(1) NOT NULL DEFAULT '0',
  `user_isdel` int(1) NOT NULL DEFAULT '0',
  `user_parent` bigint(20) NOT NULL DEFAULT '0',
  `user_passw` varchar(100) DEFAULT NULL,
  `defaultlang` enum('english','indonesian') NOT NULL,
  `date_lastactivity` datetime DEFAULT NULL,
  `authkey` varchar(100) DEFAULT NULL,
  `user_access` text,
  `user_imgprofile` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.m_countries
CREATE TABLE IF NOT EXISTS `m_countries` (
  `id` int(11) NOT NULL,
  `country_code` varchar(2) NOT NULL DEFAULT '',
  `country_name` varchar(100) NOT NULL DEFAULT '',
  `countrycode` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.m_provinces
CREATE TABLE IF NOT EXISTS `m_provinces` (
  `id` char(2) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `country_id` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.m_regencies
CREATE TABLE IF NOT EXISTS `m_regencies` (
  `id` char(4) COLLATE utf8_unicode_ci NOT NULL,
  `province_id` char(2) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.systemaddons
CREATE TABLE IF NOT EXISTS `systemaddons` (
  `systemaddons_id` int(4) NOT NULL AUTO_INCREMENT,
  `addonscode` varchar(20) DEFAULT NULL,
  `systemaddons_code` varchar(20) DEFAULT NULL,
  `trial_quota` int(3) DEFAULT '0' COMMENT 'sementara digunakan untuk menyimpan jatah trial tiap addons karena ini belum diakomodir oleh mybilling',
  PRIMARY KEY (`systemaddons_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.tbarea
CREATE TABLE IF NOT EXISTS `tbarea` (
  `area_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `appid` varchar(255) NOT NULL,
  `area_code` varchar(50) DEFAULT NULL,
  `area_name` varchar(100) DEFAULT NULL,
  `area_keterangan` text,
  `area_total_cabang` int(4) NOT NULL DEFAULT '0',
  `area_total_emp` int(4) NOT NULL DEFAULT '0',
  `area_total_emp_pending_mutasi_masuk` int(4) NOT NULL DEFAULT '0',
  `area_total_emp_pending_mutasi_keluar` int(4) NOT NULL DEFAULT '0',
  `area_total_emp_pending_new` int(4) NOT NULL DEFAULT '0',
  `area_total_emp_pending_resign` int(4) NOT NULL DEFAULT '0',
  `area_total_device` int(4) NOT NULL DEFAULT '0',
  `area_user_add` int(6) DEFAULT '0',
  `area_date_create` datetime DEFAULT NULL,
  `area_user_modif` int(6) NOT NULL DEFAULT '0',
  `area_date_modif` datetime DEFAULT NULL,
  `area_jenis_modif` enum('','edit','delete') NOT NULL,
  `is_del` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`area_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.tbcabang
CREATE TABLE IF NOT EXISTS `tbcabang` (
  `cabang_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `appid` varchar(255) NOT NULL,
  `cabang_area_id` bigint(20) NOT NULL DEFAULT '0',
  `cabang_code` varchar(50) DEFAULT NULL,
  `cabang_timezone` varchar(255) DEFAULT NULL,
  `cabang_utc` varchar(30) DEFAULT NULL,
  `cabang_name` varchar(100) DEFAULT NULL,
  `cabang_address` text,
  `cabang_contactnumber` varchar(250) DEFAULT NULL,
  `cabang_keterangan` text,
  `cabang_total_emp` int(6) NOT NULL DEFAULT '0',
  `cabang_total_emp_pending_mutasi_masuk` int(6) NOT NULL DEFAULT '0',
  `cabang_total_emp_pending_mutasi_keluar` int(6) NOT NULL DEFAULT '0',
  `cabang_total_emp_pending_new` int(6) NOT NULL DEFAULT '0',
  `cabang_total_emp_pending_resign` int(6) NOT NULL DEFAULT '0',
  `cabang_total_device` int(6) NOT NULL DEFAULT '0',
  `cabang_user_add` int(6) NOT NULL DEFAULT '0',
  `cabang_date_create` datetime DEFAULT NULL,
  `cabang_user_modif` int(6) NOT NULL DEFAULT '0',
  `cabang_date_modif` datetime DEFAULT NULL,
  `cabang_jenis_modif` enum('','edit','delete') NOT NULL,
  `is_del` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cabang_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.tbcheckinout
CREATE TABLE IF NOT EXISTS `tbcheckinout` (
  `checkinout_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `appid` varchar(255) NOT NULL,
  `checkinout_employee_id` bigint(20) NOT NULL DEFAULT '0',
  `checkinout_datetime` datetime DEFAULT NULL,
  `checkinout_verification_mode` int(1) NOT NULL DEFAULT '0',
  `checkinout_code` int(1) NOT NULL DEFAULT '0',
  `checkinout_device_id` bigint(20) NOT NULL DEFAULT '0',
  `checkinout_SN` varchar(100) DEFAULT NULL,
  `checkinout_area_id` bigint(20) NOT NULL DEFAULT '0',
  `checkinout_cabang_id` bigint(20) DEFAULT '0',
  `checkinout_date_create` datetime DEFAULT NULL,
  `checkinout_flag_download` int(1) NOT NULL DEFAULT '0',
  `checkinout_employeecode` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`checkinout_id`)
) ENGINE=InnoDB AUTO_INCREMENT=171 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.tbdevice
CREATE TABLE IF NOT EXISTS `tbdevice` (
  `device_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `appid` varchar(255) NOT NULL,
  `device_area_id` bigint(20) NOT NULL DEFAULT '0',
  `device_cabang_id` bigint(20) NOT NULL DEFAULT '0',
  `device_SN` varchar(100) DEFAULT NULL,
  `device_code` varchar(50) DEFAULT NULL,
  `device_name` varchar(100) DEFAULT NULL,
  `device_ip` varchar(20) DEFAULT NULL,
  `device_count_user` int(5) NOT NULL DEFAULT '0' COMMENT 'belum bisa diimplementasikan',
  `device_count_FP_template` int(5) NOT NULL DEFAULT '0',
  `device_count_face_template` int(5) NOT NULL DEFAULT '0',
  `device_count_transaction_log` int(5) NOT NULL DEFAULT '0',
  `device_last_communication` datetime DEFAULT NULL,
  `device_last_activity` enum('','receive_log','broadcast_template','remove_template') NOT NULL,
  `device_user_add` int(6) DEFAULT '0',
  `device_date_create` datetime DEFAULT NULL,
  `device_user_modif` int(6) DEFAULT '0',
  `device_date_modif` datetime DEFAULT NULL,
  `device_jenis_modif` enum('','edit','delete') NOT NULL,
  `is_del` int(1) NOT NULL DEFAULT '0',
  `device_license` enum('active','notactive') NOT NULL,
  `alg_version` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`device_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.tbemployee
CREATE TABLE IF NOT EXISTS `tbemployee` (
  `employee_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `appid` varchar(255) NOT NULL,
  `employee_account_no` varchar(20) DEFAULT NULL,
  `employee_full_name` varchar(100) DEFAULT NULL,
  `employee_nick_name` varchar(100) DEFAULT NULL,
  `employee_join_date` datetime DEFAULT NULL,
  `employee_count_mutasi` int(3) NOT NULL DEFAULT '0',
  `employee_last_mutasi` datetime DEFAULT NULL,
  `employee_resign_date` datetime DEFAULT NULL,
  `resign_confirmed` enum('no','yes') NOT NULL COMMENT 'jika date resign sudah sesuai maka field ini isinya yes',
  `employee_user_add` int(6) NOT NULL DEFAULT '0',
  `employee_date_create` datetime DEFAULT NULL,
  `employee_user_modif` int(6) NOT NULL DEFAULT '0',
  `employee_date_modif` datetime DEFAULT NULL,
  `employee_jenis_modif` enum('','edit','delete') NOT NULL,
  `employee_is_active` int(1) NOT NULL DEFAULT '0',
  `is_del` int(1) NOT NULL DEFAULT '0',
  `employee_license` enum('active','notactive') NOT NULL,
  `employee_password` varchar(100) DEFAULT NULL,
  `employee_card` text,
  `image` varchar(255) DEFAULT NULL,
  `picture` text,
  PRIMARY KEY (`employee_id`),
  KEY `tbemployee_appid_idx` (`appid`,`employee_full_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.tbemployeeareacabang
CREATE TABLE IF NOT EXISTS `tbemployeeareacabang` (
  `employeeareacabang_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `appid` varchar(255) NOT NULL,
  `employeeareacabang_employee_id` bigint(20) NOT NULL DEFAULT '0',
  `employee_area_id` bigint(20) NOT NULL DEFAULT '0',
  `employee_cabang_id` bigint(20) NOT NULL DEFAULT '0',
  `employeeareacabang_effdt` datetime DEFAULT NULL,
  `employeeareacabang_date_create` datetime DEFAULT NULL,
  `employeeareacabang_user_add` bigint(20) NOT NULL DEFAULT '0',
  `status` enum('pending','active','archived') NOT NULL,
  `employeeareacabang_datearchive` datetime DEFAULT NULL,
  PRIMARY KEY (`employeeareacabang_id`),
  KEY `tbemployeeareacabang_appid_idx` (`appid`,`employeeareacabang_employee_id`,`employee_area_id`,`employee_cabang_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.tbemployeehistory
CREATE TABLE IF NOT EXISTS `tbemployeehistory` (
  `employeehistory_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `appid` varchar(255) NOT NULL,
  `employeehistory_employee_id` bigint(20) NOT NULL DEFAULT '0',
  `employeehistory_user_add` int(6) NOT NULL DEFAULT '0',
  `employeehistory_transaction_date` datetime DEFAULT NULL,
  `employeehistory_date_create` datetime DEFAULT NULL,
  `employeehistory_jenis_history` enum('add','edit','move','resign') NOT NULL,
  PRIMARY KEY (`employeehistory_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.tbemployeelocationdevice
CREATE TABLE IF NOT EXISTS `tbemployeelocationdevice` (
  `employeelocationdevice_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `employeeareacabang_id` bigint(20) NOT NULL DEFAULT '0',
  `employee_id` bigint(20) DEFAULT NULL,
  `device_id` bigint(20) NOT NULL DEFAULT '0',
  `need_update` enum('no','yes') DEFAULT 'no',
  `pic_need_update` enum('no','yes') DEFAULT 'no',
  PRIMARY KEY (`employeelocationdevice_id`),
  UNIQUE KEY `unik` (`employeeareacabang_id`,`employee_id`,`device_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.tbemployeelocationdevicetemplate
CREATE TABLE IF NOT EXISTS `tbemployeelocationdevicetemplate` (
  `employeelocationdevicetemplate_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `employeelocationdevice_id` bigint(20) NOT NULL,
  `employeetemplate_id` bigint(20) NOT NULL,
  `push_count` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`employeelocationdevicetemplate_id`),
  UNIQUE KEY `UNIK` (`employeelocationdevice_id`,`employeetemplate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=511 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.tbemployeemove
CREATE TABLE IF NOT EXISTS `tbemployeemove` (
  `employeemove_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `appid` varchar(255) NOT NULL,
  `employeemove_employee_id` bigint(20) DEFAULT '0',
  `employeemove_date_create` datetime DEFAULT NULL,
  `employeemove_user_add` int(6) NOT NULL DEFAULT '0',
  `employeemove_area_id_source` bigint(20) NOT NULL DEFAULT '0',
  `employeemove_cabang_id_source` bigint(20) NOT NULL DEFAULT '0',
  `employeemove_area_id_destination` bigint(20) NOT NULL DEFAULT '0',
  `employeemove_cabang_id_destination` bigint(20) NOT NULL DEFAULT '0',
  `employeemove_effdt` datetime DEFAULT NULL,
  `employeemove_status_move` enum('','pending','success','failed') NOT NULL,
  PRIMARY KEY (`employeemove_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.tbemployeemutation
CREATE TABLE IF NOT EXISTS `tbemployeemutation` (
  `employeemutation_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `appid` varchar(255) DEFAULT NULL,
  `employeemutation_employeeid` bigint(20) DEFAULT '0',
  `employeemutation_useradd` bigint(20) DEFAULT '0',
  `employeemutation_dateadd` datetime DEFAULT NULL,
  `employeemutation_status` enum('pending','success') DEFAULT NULL,
  `employeemutation_effdt` datetime DEFAULT NULL,
  PRIMARY KEY (`employeemutation_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.tbemployeemutation_c
CREATE TABLE IF NOT EXISTS `tbemployeemutation_c` (
  `employeemutation_c_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `employeemutation_id` bigint(20) DEFAULT NULL,
  `employeeareacabang_id` bigint(20) DEFAULT NULL,
  `child_status` enum('','source','destination') DEFAULT NULL,
  `area_id` bigint(20) DEFAULT '0',
  `cabang_id` bigint(20) DEFAULT '0',
  `transaction_status` enum('pending','success') DEFAULT 'pending',
  PRIMARY KEY (`employeemutation_c_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.tbemployeeresign
CREATE TABLE IF NOT EXISTS `tbemployeeresign` (
  `employeeresign_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `appid` varchar(255) NOT NULL,
  `employeeresign_employee_id` bigint(20) NOT NULL DEFAULT '0',
  `employeeresign_date_create` datetime DEFAULT NULL,
  `employeeresign_user_add` int(6) NOT NULL DEFAULT '0',
  `employeeresign_effdt` datetime DEFAULT NULL,
  `employeeresign_status_resign` enum('','pending','success','failed') NOT NULL,
  PRIMARY KEY (`employeeresign_id`),
  UNIQUE KEY `UNIK` (`employeeresign_employee_id`,`appid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.tbemployeetemplate
CREATE TABLE IF NOT EXISTS `tbemployeetemplate` (
  `employeetemplate_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `appid` varchar(255) NOT NULL,
  `employeetemplate_employee_id` bigint(20) NOT NULL DEFAULT '0',
  `employeetemplate_template` text,
  `employeetemplate_index` int(2) NOT NULL DEFAULT '0',
  `employeetemplate_jenis` enum('','fingerprint','face','card') NOT NULL,
  `need_update` enum('no','yes') DEFAULT 'no',
  PRIMARY KEY (`employeetemplate_id`),
  UNIQUE KEY `UNIK` (`employeetemplate_employee_id`,`employeetemplate_index`,`employeetemplate_jenis`)
) ENGINE=InnoDB AUTO_INCREMENT=207 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.tbhistorydownloadcheckinout
CREATE TABLE IF NOT EXISTS `tbhistorydownloadcheckinout` (
  `historydownloadcheckinout_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `appid` varchar(255) NOT NULL,
  `historydownloadcheckinout_date_create` datetime DEFAULT NULL,
  `historydownloadcheckinout_checkinout_id_min` bigint(20) NOT NULL DEFAULT '0',
  `historydownloadcheckinout_checkinout_id_max` bigint(20) NOT NULL DEFAULT '0',
  `historydownloadcheckinout_checkinout_count` int(9) NOT NULL DEFAULT '0',
  `historydownloadcheckinout_name_of_file` varchar(100) DEFAULT NULL,
  `historydownloadcheckinout_status` enum('pending','success') NOT NULL,
  PRIMARY KEY (`historydownloadcheckinout_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.tbmenu
CREATE TABLE IF NOT EXISTS `tbmenu` (
  `menuid` int(10) NOT NULL AUTO_INCREMENT,
  `menucaption` varchar(100) DEFAULT NULL,
  `parentid` int(10) NOT NULL DEFAULT '0',
  `link` text,
  `class_icon` varchar(50) DEFAULT NULL,
  `menulevel` int(5) NOT NULL DEFAULT '0',
  `urut` int(5) NOT NULL DEFAULT '0',
  `newTab` tinyint(4) NOT NULL DEFAULT '0',
  `tags` text,
  PRIMARY KEY (`menuid`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.tbotp
CREATE TABLE IF NOT EXISTS `tbotp` (
  `otp_id` double NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `otp` varchar(6) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `expired_date` datetime DEFAULT NULL,
  `type` enum('','register','forgot_password','reset_password') NOT NULL,
  `platform` enum('','email','sms') NOT NULL,
  `status` enum('','success','failed') NOT NULL,
  PRIMARY KEY (`otp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.tbtemplatesentlast
CREATE TABLE IF NOT EXISTS `tbtemplatesentlast` (
  `datetime` timestamp NULL DEFAULT NULL,
  `template` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

-- Dumping structure for table dbinact.user_activity
CREATE TABLE IF NOT EXISTS `user_activity` (
  `activity_id` double NOT NULL AUTO_INCREMENT,
  `appid` varchar(100) NOT NULL,
  `activity_timestamp` datetime DEFAULT NULL,
  `userid` double NOT NULL DEFAULT '0',
  `menu` varchar(100) DEFAULT NULL,
  `activity_type` enum('','add','edit','delete') NOT NULL,
  PRIMARY KEY (`activity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=319 DEFAULT CHARSET=latin1;

-- Data exporting was unselected.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
