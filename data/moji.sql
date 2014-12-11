-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 10, 2014 at 06:52 PM
-- Server version: 5.6.16
-- PHP Version: 5.4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dev_moji_install`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  `account_plan_id` int(10) NOT NULL DEFAULT '0',
  `owner_id` int(10) DEFAULT NULL,
  `created_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `slug`, `account_plan_id`, `owner_id`, `created_date`, `last_modified`) VALUES
(1, 'install', 1, 1, '2014-05-24 00:00:00', '2014-05-24 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `account_plans`
--

CREATE TABLE IF NOT EXISTS `account_plans` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `project_limit` int(3) NOT NULL DEFAULT '0',
  `team_limit` int(3) NOT NULL DEFAULT '0',
  `space_limit` int(10) NOT NULL DEFAULT '0' COMMENT 'Megabytes!',
  `module_limit` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `account_plans`
--

INSERT INTO `account_plans` (`id`, `name`, `project_limit`, `team_limit`, `space_limit`, `module_limit`) VALUES
(1, 'Freelancer', 10, 1, 300, 1),
(2, 'Professional', 30, 5, 1024, 3),
(3, 'Agency', 50, 20, 10240, 10);

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `account_id` int(10) NOT NULL DEFAULT '1',
  `date` timestamp NULL DEFAULT NULL,
  `type` varchar(30) CHARACTER SET latin1 NOT NULL,
  `performed_by` int(10) NOT NULL,
  `stuff` text CHARACTER SET latin1 NOT NULL,
  `company_id` int(10) NOT NULL,
  `project_id` int(10) NOT NULL,
  `task_id` int(10) NOT NULL,
  `note_id` int(10) NOT NULL,
  `bookmark_id` int(10) NOT NULL,
  `file_id` int(10) NOT NULL DEFAULT '0',
  `file_rev_id` int(10) NOT NULL DEFAULT '0',
  `file_review_id` int(10) NOT NULL DEFAULT '0',
  `user_id` int(10) NOT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `project_id` (`project_id`),
  KEY `task_id` (`task_id`),
  KEY `note_id` (`note_id`),
  KEY `bookmark_id` (`bookmark_id`),
  KEY `file_id` (`file_id`),
  KEY `file_rev_id` (`file_rev_id`,`file_review_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bookmarks`
--

CREATE TABLE IF NOT EXISTS `bookmarks` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) NOT NULL DEFAULT '1',
  `company_id` int(10) NOT NULL DEFAULT '0',
  `project_id` int(10) NOT NULL DEFAULT '0',
  `task_id` int(10) NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `url` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `description` text CHARACTER SET latin1,
  `owner` int(10) NOT NULL DEFAULT '0',
  `creator` int(10) NOT NULL DEFAULT '0',
  `last_modified` timestamp NULL DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `project_id` (`project_id`),
  KEY `task_id` (`task_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE IF NOT EXISTS `companies` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `account_id` int(10) NOT NULL DEFAULT '1',
  `name` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `phone1` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `phone2` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `fax` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `address1` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `address2` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `city` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `state` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `zip` varchar(11) CHARACTER SET latin1 DEFAULT NULL,
  `primary_url` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `description` text CHARACTER SET latin1,
  `type` int(3) DEFAULT NULL,
  `client_language` varchar(20) NOT NULL DEFAULT 'en_US',
  `currency_code` varchar(20) NOT NULL DEFAULT 'USD',
  `default_hourly_rate` decimal(10,2) NOT NULL DEFAULT '0.00',
  `custom` longtext CHARACTER SET latin1,
  `active_projects` int(5) DEFAULT '0',
  `archived_projects` int(5) DEFAULT '0',
  `last_modified` timestamp NULL DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `account_id`, `name`, `phone1`, `phone2`, `fax`, `address1`, `address2`, `city`, `state`, `zip`, `primary_url`, `description`, `type`, `client_language`, `currency_code`, `default_hourly_rate`, `custom`, `active_projects`, `archived_projects`, `last_modified`, `created_date`) VALUES
(1, 1, 'mithra62', '310-739-3322', '', '', '824 N. Victory Blvd.', 'Second Floor', 'Burbank', 'CA', '91502', 'http://mithra62.com', '', 6, 'en_US', 'USD', '0.00', NULL, 0, 0, '2010-04-21 07:40:33', '2009-12-12 09:02:15');

-- --------------------------------------------------------

--
-- Table structure for table `company_contacts`
--

CREATE TABLE IF NOT EXISTS `company_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(10) NOT NULL DEFAULT '1',
  `company_id` int(10) NOT NULL DEFAULT '0',
  `job_title` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `first_name` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `last_name` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `title` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `email` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `email2` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `url` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `phone_home` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `phone2` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `fax` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `mobile` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `address1` varchar(60) CHARACTER SET latin1 DEFAULT NULL,
  `address2` varchar(60) CHARACTER SET latin1 DEFAULT NULL,
  `city` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `state` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `zip` varchar(11) CHARACTER SET latin1 DEFAULT NULL,
  `jabber` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `icq` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `msn` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `yahoo` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `aol` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `google_talk` varchar(20) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  `creator` int(10) unsigned DEFAULT '0',
  `created_date` timestamp NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `discuss_thread`
--

CREATE TABLE IF NOT EXISTS `discuss_thread` (
  `thread_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(1000) NOT NULL,
  `slug` varchar(255) NOT NULL,
  PRIMARY KEY (`thread_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) NOT NULL DEFAULT '1',
  `owner` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `creator` int(10) NOT NULL,
  `company_id` int(10) NOT NULL DEFAULT '0',
  `project_id` int(10) unsigned NOT NULL DEFAULT '0',
  `task_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `description` text CHARACTER SET latin1,
  `comments_approval` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `approver` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `date_approval` timestamp NULL DEFAULT NULL,
  `status` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `last_modified` timestamp NULL DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `project_id` (`project_id`),
  KEY `task_id` (`task_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `file_revisions`
--

CREATE TABLE IF NOT EXISTS `file_revisions` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) NOT NULL DEFAULT '1',
  `file_id` int(10) NOT NULL DEFAULT '0',
  `uploaded_by` int(10) NOT NULL,
  `file_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `stored_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `size` varchar(155) CHARACTER SET latin1 DEFAULT NULL,
  `extension` varchar(155) CHARACTER SET latin1 DEFAULT NULL,
  `mime_type` varchar(100) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  `approval_comment` text CHARACTER SET latin1,
  `approver` int(10) unsigned DEFAULT NULL,
  `date_approval` timestamp NULL DEFAULT NULL,
  `status` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `last_modified` timestamp NULL DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `file_id` (`file_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `account_id` int(10) NOT NULL DEFAULT '0',
  `company_id` int(10) NOT NULL DEFAULT '0',
  `creator` int(10) NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `status` varchar(10) NOT NULL,
  `date` date NOT NULL,
  `po_number` int(10) NOT NULL DEFAULT '0',
  `discount` float NOT NULL DEFAULT '0',
  `currency_code` varchar(5) NOT NULL DEFAULT '',
  `language` varchar(4) NOT NULL,
  `terms_conditions` text NOT NULL,
  `notes` text NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_date` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_line_items`
--

CREATE TABLE IF NOT EXISTS `invoice_line_items` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(10) NOT NULL DEFAULT '0',
  `time_id` int(10) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `unit_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity` int(10) NOT NULL DEFAULT '0',
  `last_modified` datetime NOT NULL,
  `created_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ips`
--

CREATE TABLE IF NOT EXISTS `ips` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `account_id` int(10) NOT NULL DEFAULT '1',
  `ip` int(11) NOT NULL,
  `ip_raw` char(20) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1,
  `creator` int(10) NOT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_raw` (`ip_raw`),
  KEY `ip` (`ip`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) NOT NULL DEFAULT '1',
  `project_id` int(10) unsigned NOT NULL DEFAULT '0',
  `task_id` int(10) DEFAULT '0',
  `company_id` int(10) NOT NULL DEFAULT '0',
  `creator` int(10) NOT NULL DEFAULT '0',
  `topic` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `subject` varchar(155) CHARACTER SET latin1 DEFAULT NULL,
  `description` text CHARACTER SET latin1,
  `published` char(1) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `date` datetime DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE IF NOT EXISTS `options` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `account_id` int(10) NOT NULL DEFAULT '1',
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `area` varchar(20) CHARACTER SET latin1 NOT NULL,
  `creator` int(10) NOT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `area` (`area`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`id`, `account_id`, `name`, `area`, `creator`, `created_date`, `last_modified`) VALUES
(2, 1, 'IT', 'project_type', 0, '2010-09-23 02:48:13', '2010-09-23 02:48:13'),
(3, 1, 'Programming', 'project_type', 0, '2010-09-23 02:48:13', '2010-09-23 02:48:13'),
(4, 1, 'Website', 'project_type', 0, '2010-09-23 02:48:13', '2010-09-23 02:48:13'),
(6, 1, 'Writing', 'project_type', 0, '2010-09-23 02:48:13', '2010-09-23 02:48:13'),
(7, 1, 'Reporting', 'project_type', 0, '2010-09-23 02:48:13', '2010-09-23 02:48:13'),
(8, 1, 'Research', 'project_type', 0, '2010-09-23 02:48:13', '2010-09-23 02:48:13'),
(9, 1, 'Quality Assurance', 'project_type', 0, '2010-09-23 02:48:13', '2010-09-23 02:48:13'),
(10, 1, 'Administrative', 'task_type', 0, '2010-09-23 02:48:13', '2010-09-29 17:30:57'),
(12, 1, 'Programming', 'task_type', 0, '2010-09-23 02:48:13', '2010-09-23 02:48:13'),
(13, 1, 'Website', 'task_type', 0, '2010-09-23 02:48:13', '2010-09-23 02:48:13'),
(14, 1, 'Proposal', 'task_type', 0, '2010-09-23 02:48:13', '2010-09-23 02:48:13'),
(15, 1, 'Writing', 'task_type', 0, '2010-09-23 02:48:13', '2010-09-23 02:48:13'),
(16, 1, 'Reporting', 'task_type', 0, '2010-09-23 02:48:13', '2010-09-23 02:48:13'),
(17, 1, 'Research', 'task_type', 0, '2010-09-23 02:48:13', '2010-09-23 02:48:13'),
(18, 1, 'Quality Assurance', 'task_type', 0, '2010-09-23 02:48:13', '2010-09-23 02:48:13'),
(19, 1, 'Conference Call', 'task_type', 1, '2010-10-08 05:09:01', '2010-10-08 05:09:01'),
(20, 1, 'Business Development', 'task_type', 1, '2010-10-10 22:28:09', '2010-10-10 22:28:09'),
(22, 1, 'Email Blast', 'project_type', 1, '2010-10-13 14:46:36', '2010-10-13 14:46:36'),
(23, 1, 'Tech Support', 'task_type', 1, '2011-04-21 21:56:56', '2011-04-21 21:56:56'),
(26, 1, 'My Test Option 4', 'project_type', 1, '2014-08-09 22:54:12', '2014-08-09 23:11:27');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(10) NOT NULL DEFAULT '1',
  `company_id` int(11) NOT NULL DEFAULT '0',
  `company_internal` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `start_year` int(4) DEFAULT NULL,
  `start_month` int(2) DEFAULT NULL,
  `start_day` int(2) DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `actual_end_date` timestamp NULL DEFAULT NULL,
  `hours_worked` float NOT NULL DEFAULT '0',
  `status` int(11) DEFAULT '0',
  `percent_complete` tinyint(4) DEFAULT '0',
  `description` text CHARACTER SET latin1,
  `target_budget` decimal(10,2) DEFAULT '0.00',
  `actual_budget` decimal(10,2) DEFAULT '0.00',
  `task_count` int(5) NOT NULL DEFAULT '0',
  `file_count` int(5) NOT NULL DEFAULT '0',
  `creator` int(11) DEFAULT '0',
  `contacts` char(100) CHARACTER SET latin1 DEFAULT NULL,
  `priority` tinyint(4) DEFAULT '0',
  `type` smallint(6) NOT NULL DEFAULT '0',
  `harvest_id` bigint(10) DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sdate` (`start_date`),
  KEY `idx_edate` (`end_date`),
  KEY `idx_proj1` (`company_id`),
  KEY `company_id` (`company_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `project_contacts`
--

CREATE TABLE IF NOT EXISTS `project_contacts` (
  `project_id` int(10) NOT NULL,
  `contact_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `project_teams`
--

CREATE TABLE IF NOT EXISTS `project_teams` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) NOT NULL DEFAULT '1',
  `project_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_date` timestamp NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`),
  KEY `project_id_2` (`project_id`,`user_id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `account_id` int(10) NOT NULL DEFAULT '1',
  `option_value` longtext CHARACTER SET latin1,
  `option_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_name` (`option_name`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `account_id`, `option_value`, `option_name`, `created_date`, `last_modified`) VALUES
(1, 1, '11', 'master_company', '2010-06-07 22:23:03', '2014-08-29 03:48:17'),
(2, 1, '0', 'enable_ip', '2010-06-07 22:23:03', '2014-08-29 03:48:17'),
(3, 1, 'America/Los_Angeles', 'timezone', '2011-02-25 12:22:16', '2011-03-01 06:40:14'),
(4, 1, 'F j, Y', 'date_format', '2011-02-25 12:22:16', '2011-03-01 06:40:14'),
(5, 1, '', 'date_format_custom', '2011-02-25 12:22:16', '2011-03-01 06:40:14'),
(6, 1, 'g:i A', 'time_format', '2011-02-25 12:22:16', '2011-03-01 06:40:14'),
(7, 1, '', 'time_format_custom', '2011-02-25 12:22:16', '2011-03-01 06:40:14'),
(8, 1, 'jpg,gif,png,txt,docx,doc,pdf,php,xls,xlsx,csv,psd,ppt,pptx,pot,potx,rar,zip,tar,gz,tgz,bz2,html,htm,avi,mov,fla,swf,asf,flv,sql,mp3', 'allowed_file_formats', '2011-05-03 09:45:48', '2011-05-03 09:45:48'),
(9, 1, '717cde7aa978c1c87e4f79c823572c77', 'freshbooks_auth_token', '2014-09-05 06:31:44', '2014-09-05 06:31:44'),
(10, 1, 'mithra62', 'freshbooks_account_url', '2014-09-05 06:31:44', '2014-09-05 06:31:44');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(10) NOT NULL DEFAULT '1',
  `name` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `parent` int(11) DEFAULT '0',
  `milestone` tinyint(1) DEFAULT '0',
  `project_id` int(11) NOT NULL DEFAULT '0',
  `company_id` int(5) NOT NULL DEFAULT '0',
  `assigned_to` int(10) NOT NULL DEFAULT '0',
  `start_date` timestamp NULL DEFAULT NULL,
  `start_year` int(4) DEFAULT NULL,
  `start_month` int(2) DEFAULT NULL,
  `start_day` int(2) DEFAULT NULL,
  `duration` float unsigned DEFAULT '0',
  `duration_type` int(11) NOT NULL DEFAULT '1',
  `hours_worked` float unsigned DEFAULT '0',
  `end_date` timestamp NULL DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `progress` int(3) NOT NULL DEFAULT '0',
  `priority` tinyint(4) DEFAULT '0',
  `percent_complete` tinyint(4) DEFAULT '0',
  `description` text CHARACTER SET latin1,
  `creator` int(11) NOT NULL DEFAULT '0',
  `client_publish` tinyint(1) NOT NULL DEFAULT '0',
  `access` int(11) NOT NULL DEFAULT '0',
  `notify` int(11) NOT NULL DEFAULT '0',
  `type` smallint(6) NOT NULL DEFAULT '0',
  `file_count` int(5) NOT NULL DEFAULT '0',
  `last_modified` timestamp NULL DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_task_parent` (`parent`),
  KEY `idx_task_project` (`project_id`),
  KEY `idx_task1` (`start_date`),
  KEY `idx_task2` (`end_date`),
  KEY `project_id` (`project_id`),
  KEY `company_id` (`company_id`),
  KEY `end_date` (`end_date`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `task_assignments`
--

CREATE TABLE IF NOT EXISTS `task_assignments` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(10) unsigned NOT NULL DEFAULT '0',
  `assigned_by` int(10) unsigned NOT NULL DEFAULT '0',
  `assigned_to` int(10) unsigned NOT NULL DEFAULT '0',
  `comments` text CHARACTER SET latin1,
  `created_date` timestamp NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `task_contacts`
--

CREATE TABLE IF NOT EXISTS `task_contacts` (
  `task_id` int(10) NOT NULL,
  `contact_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `task_dependencies`
--

CREATE TABLE IF NOT EXISTS `task_dependencies` (
  `task_id` int(11) NOT NULL,
  `req_task_id` int(11) NOT NULL,
  PRIMARY KEY (`task_id`,`req_task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `task_teams`
--

CREATE TABLE IF NOT EXISTS `task_teams` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_date` timestamp NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`),
  KEY `project_id_2` (`project_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `times`
--

CREATE TABLE IF NOT EXISTS `times` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `account_id` int(10) NOT NULL DEFAULT '1',
  `creator` int(10) NOT NULL,
  `company_id` int(10) NOT NULL,
  `project_id` int(10) DEFAULT NULL,
  `task_id` int(10) DEFAULT NULL,
  `user_id` int(10) NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  `month` int(2) NOT NULL DEFAULT '0',
  `day` int(2) NOT NULL DEFAULT '0',
  `year` int(4) NOT NULL DEFAULT '0',
  `hours` float NOT NULL,
  `description` text CHARACTER SET latin1 NOT NULL,
  `billable` int(1) NOT NULL DEFAULT '0',
  `bill_status` enum('','sent','paid') CHARACTER SET latin1 NOT NULL,
  `harvest_id` bigint(10) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user2role`
--

CREATE TABLE IF NOT EXISTS `user2role` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `account_id` int(10) NOT NULL DEFAULT '0',
  `role_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user2role`
--

INSERT INTO `user2role` (`user_id`, `account_id`, `role_id`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `password` varchar(128) CHARACTER SET latin1 NOT NULL,
  `first_name` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `last_name` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `phone_mobile` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `phone_home` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `phone_work` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `phone_fax` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `job_title` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `jabber` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `aol` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `yahoo` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `google_talk` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `msn` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `ichat` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `skype` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `description` text CHARACTER SET latin1,
  `user_status` enum('0','d') CHARACTER SET latin1 NOT NULL DEFAULT 'd',
  `last_login` timestamp NULL DEFAULT NULL,
  `hash` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `pw_forgotten` timestamp NULL DEFAULT NULL,
  `forgotten_hash` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `harvest_id` bigint(10) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `UserStatus` (`user_status`),
  KEY `Joined` (`created_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT AUTO_INCREMENT=3 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `phone_mobile`, `phone_home`, `phone_work`, `phone_fax`, `job_title`, `jabber`, `aol`, `yahoo`, `google_talk`, `msn`, `ichat`, `skype`, `description`, `user_status`, `last_login`, `hash`, `pw_forgotten`, `forgotten_hash`, `harvest_id`, `created_date`, `last_modified`) VALUES
(1, 'default@mojitrac.com', '1be4bef311581de40ed1b2ff31af49a6e77428a1116dcfc132ec69c95de138de76502644ddb5ee0369dd5761eba17ec722e3c46ba1e35815f712db8191060985', 'Default', 'Account', '', '', '', '', '', '', '', '', '', '', NULL, NULL, '', 'd', '2014-12-11 02:50:42', 'de144cbb6ca0ed43dce8bd86b5435639', '2014-09-03 12:16:01', '', NULL, '2006-08-18 07:05:36', '2014-12-11 02:50:42');

-- --------------------------------------------------------

--
-- Table structure for table `user_accounts`
--

CREATE TABLE IF NOT EXISTS `user_accounts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `account_id` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `user_accounts`
--

INSERT INTO `user_accounts` (`id`, `user_id`, `account_id`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_data`
--

CREATE TABLE IF NOT EXISTS `user_data` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `option_value` longtext CHARACTER SET latin1,
  `option_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33 ;

--
-- Dumping data for table `user_data`
--

INSERT INTO `user_data` (`id`, `user_id`, `option_value`, `option_name`, `created_date`, `last_modified`) VALUES
(1, 1, '0', 'noti_assigned_task', '2014-08-30 06:40:29', '2014-08-30 06:40:29'),
(2, 1, '0', 'noti_status_task', '2014-08-30 06:40:29', '2014-08-30 06:40:29'),
(3, 1, '1', 'noti_priority_task', '2014-08-30 06:40:29', '2014-08-30 06:40:29'),
(4, 1, '1', 'noti_daily_task_reminder', '2014-08-30 06:40:29', '2014-08-30 06:40:29'),
(5, 1, '1', 'noti_add_proj_team', '2014-08-30 06:40:29', '2014-08-30 06:40:29'),
(6, 1, '1', 'noti_remove_proj_team', '2014-08-30 06:40:29', '2014-08-30 06:40:29'),
(7, 1, '1', 'noti_file_uploaded', '2014-08-30 06:40:29', '2014-08-30 06:40:29'),
(8, 1, '1', 'noti_file_revision_uploaded', '2014-08-30 06:40:29', '2014-08-30 06:40:29'),
(9, 1, 'America/Los_Angeles', 'timezone', '2014-08-30 06:40:29', '2014-08-30 06:40:29'),
(10, 1, 'F j, Y', 'date_format', '2012-07-23 07:30:21', '2012-07-23 07:30:21'),
(11, 1, '', 'date_format_custom', '2012-07-23 07:30:21', '2012-07-23 07:30:21'),
(12, 1, 'g:i A', 'time_format', '2012-07-23 07:30:21', '2012-07-23 07:30:21'),
(13, 1, '', 'time_format_custom', '2012-07-23 07:30:21', '2012-07-23 07:30:21'),
(15, 1, '1', 'enable_rel_time', '2014-08-30 06:40:29', '2014-08-30 06:40:29'),
(16, 1, '0', 'enable_contextual_help', '2014-08-30 06:40:29', '2014-08-30 06:40:29'),
(17, 18, '1', 'noti_assigned_task', '2014-08-17 03:15:03', '2014-08-17 03:15:03'),
(18, 18, '1', 'noti_status_task', '2014-08-17 03:15:03', '2014-08-17 03:15:03'),
(19, 18, '1', 'noti_priority_task', '2014-08-17 03:15:03', '2014-08-17 03:15:03'),
(20, 18, '1', 'noti_daily_task_reminder', '2014-08-17 03:15:03', '2014-08-17 03:15:03'),
(21, 18, '1', 'noti_add_proj_team', '2014-08-17 03:15:03', '2014-08-17 03:15:03'),
(22, 18, '1', 'noti_remove_proj_team', '2014-08-17 03:15:03', '2014-08-17 03:15:03'),
(23, 18, '1', 'noti_file_uploaded', '2014-08-17 03:15:03', '2014-08-17 03:15:03'),
(24, 18, '1', 'noti_file_revision_uploaded', '2014-08-17 03:15:03', '2014-08-17 03:15:03'),
(25, 18, '1', 'enable_rel_time', '2014-08-17 03:15:03', '2014-08-17 03:15:03'),
(26, 18, 'America/Los_Angeles', 'timezone', '2014-08-17 03:15:03', '2014-08-17 03:15:03'),
(27, 18, '1', 'enable_contextual_help', '2014-08-17 03:15:03', '2014-08-17 03:15:03'),
(28, 18, 'en_US', 'locale', '2014-08-17 03:15:03', '2014-08-17 03:15:03'),
(29, 18, '{"project_id":"59","start_time":1408245720}', 'timer_data', '2014-08-17 03:22:00', '2014-08-17 03:22:00'),
(31, 1, '', 'timer_data', '2014-08-19 19:52:42', '2014-08-19 19:52:42'),
(32, 1, 'en_US', 'locale', '2014-08-30 06:40:29', '2014-08-30 06:40:29');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE IF NOT EXISTS `user_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(10) NOT NULL DEFAULT '1',
  `name` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `description` text CHARACTER SET latin1,
  `last_modified` timestamp NULL DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `account_id`, `name`, `description`, `last_modified`, `created_date`) VALUES
(1, 1, 'Super Admin', 'Moji System Administrators', '2014-09-10 08:34:19', '2010-02-20 02:19:20'),
(2, 1, 'User', '', '2010-07-14 06:33:35', '2010-02-24 21:05:03'),
(4, 1, 'Project Manager', '', '2010-02-24 03:26:07', '2010-02-24 02:20:31'),
(8, 1, 'Company Manager', '', '2014-08-29 06:41:51', '2010-02-24 04:10:31'),
(9, 1, 'Task Manager', '', '2010-02-24 04:11:29', '2010-02-24 04:11:29'),
(11, 1, 'User Manager', '', '2010-02-28 03:47:03', '2010-02-28 02:31:12'),
(12, 1, 'Freelance', 'Container for the default freelancer functionality', '2010-07-11 08:55:18', '2010-07-11 08:55:18'),
(15, 1, 'My Test Role', NULL, '2014-08-10 12:24:22', '2014-08-10 12:13:15'),
(16, 1, 'Account', '', '2014-09-15 18:37:45', '2014-09-15 18:37:45');

-- --------------------------------------------------------

--
-- Table structure for table `user_role_2_permissions`
--

CREATE TABLE IF NOT EXISTS `user_role_2_permissions` (
  `role_id` int(11) NOT NULL DEFAULT '0',
  `permission_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`role_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_role_2_permissions`
--

INSERT INTO `user_role_2_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(2, 1),
(2, 7),
(2, 9),
(2, 11),
(2, 12),
(4, 1),
(4, 2),
(4, 3),
(4, 5),
(4, 7),
(4, 8),
(4, 9),
(4, 11),
(4, 12),
(8, 3),
(8, 4),
(8, 5),
(8, 6),
(9, 1),
(9, 3),
(9, 5),
(9, 7),
(9, 8),
(9, 9),
(9, 11),
(9, 12),
(10, 1),
(10, 3),
(10, 5),
(10, 7),
(10, 9),
(10, 11),
(10, 12),
(11, 14),
(11, 15),
(12, 1),
(12, 7),
(12, 12),
(15, 1),
(15, 2),
(15, 11),
(16, 20),
(16, 21);

-- --------------------------------------------------------

--
-- Table structure for table `user_role_permissions`
--

CREATE TABLE IF NOT EXISTS `user_role_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `site_area` varchar(255) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `user_role_permissions`
--

INSERT INTO `user_role_permissions` (`id`, `name`, `site_area`) VALUES
(1, 'view_projects', 'projects'),
(2, 'manage_projects', 'projects'),
(3, 'view_companies', 'companies'),
(4, 'manage_companies', 'companies'),
(5, 'view_company_contacts', 'companies'),
(6, 'manage_company_contacts', 'companies'),
(7, 'view_tasks', 'tasks'),
(8, 'manage_tasks', 'tasks'),
(9, 'view_files', 'files'),
(10, 'manage_files', 'files'),
(11, 'view_time', 'time-tracker'),
(12, 'track_time', 'time-tracker'),
(13, 'manage_time', 'time-tracker'),
(14, 'admin_access', 'admin'),
(15, 'view_users_data', 'users'),
(16, 'manage_users', 'users'),
(17, 'manage_roles', 'roles'),
(18, 'manage_ips', 'ips'),
(19, 'manage_options', 'options'),
(20, 'view_invoices', 'companies'),
(21, 'manage_invoices', 'companies');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
