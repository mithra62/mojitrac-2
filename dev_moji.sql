-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 13, 2014 at 04:03 AM
-- Server version: 5.6.16
-- PHP Version: 5.4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dev_moji`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `date` timestamp NULL DEFAULT NULL,
  `type` varchar(30) NOT NULL,
  `performed_by` int(10) NOT NULL,
  `stuff` text NOT NULL,
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
  KEY `file_rev_id` (`file_rev_id`,`file_review_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bookmarks`
--

CREATE TABLE IF NOT EXISTS `bookmarks` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) NOT NULL DEFAULT '0',
  `project_id` int(10) NOT NULL DEFAULT '0',
  `task_id` int(10) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `description` text,
  `owner` int(10) NOT NULL DEFAULT '0',
  `creator` int(10) NOT NULL DEFAULT '0',
  `last_modified` timestamp NULL DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `project_id` (`project_id`),
  KEY `task_id` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE IF NOT EXISTS `companies` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `phone1` varchar(30) DEFAULT NULL,
  `phone2` varchar(30) DEFAULT NULL,
  `fax` varchar(30) DEFAULT NULL,
  `address1` varchar(50) DEFAULT NULL,
  `address2` varchar(50) DEFAULT NULL,
  `city` varchar(30) DEFAULT NULL,
  `state` varchar(30) DEFAULT NULL,
  `zip` varchar(11) DEFAULT NULL,
  `primary_url` varchar(255) DEFAULT NULL,
  `description` text,
  `type` int(3) DEFAULT NULL,
  `custom` longtext,
  `active_projects` int(5) DEFAULT '0',
  `archived_projects` int(5) DEFAULT '0',
  `harvest_id` bigint(10) DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `phone1`, `phone2`, `fax`, `address1`, `address2`, `city`, `state`, `zip`, `primary_url`, `description`, `type`, `custom`, `active_projects`, `archived_projects`, `harvest_id`, `last_modified`, `created_date`) VALUES
(1, 'mithra62', '310.776.5262', '', '', '14833 Friar St.', 'Apt. A', 'Van Nuys', 'CA', '91411', 'http://mithra62.com', 'Container for the mithra62 projects', 6, NULL, 0, 0, NULL, '2010-04-21 08:40:33', '2009-12-12 09:02:15');

-- --------------------------------------------------------

--
-- Table structure for table `company_contacts`
--

CREATE TABLE IF NOT EXISTS `company_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(10) NOT NULL DEFAULT '0',
  `job_title` varchar(100) DEFAULT NULL,
  `first_name` varchar(30) DEFAULT NULL,
  `last_name` varchar(30) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email2` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `phone_home` varchar(30) DEFAULT NULL,
  `phone2` varchar(30) DEFAULT NULL,
  `fax` varchar(30) DEFAULT NULL,
  `mobile` varchar(30) DEFAULT NULL,
  `address1` varchar(60) DEFAULT NULL,
  `address2` varchar(60) DEFAULT NULL,
  `city` varchar(30) DEFAULT NULL,
  `state` varchar(30) DEFAULT NULL,
  `zip` varchar(11) DEFAULT NULL,
  `jabber` varchar(255) DEFAULT NULL,
  `icq` varchar(20) DEFAULT NULL,
  `msn` varchar(255) DEFAULT NULL,
  `yahoo` varchar(255) DEFAULT NULL,
  `aol` varchar(30) DEFAULT NULL,
  `google_talk` varchar(20) NOT NULL,
  `description` text,
  `creator` int(10) unsigned DEFAULT '0',
  `created_date` timestamp NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `owner` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `creator` int(10) NOT NULL,
  `company_id` int(10) NOT NULL DEFAULT '0',
  `project_id` int(10) unsigned NOT NULL DEFAULT '0',
  `task_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `comments_approval` varchar(255) DEFAULT NULL,
  `approver` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `date_approval` timestamp NULL DEFAULT NULL,
  `status` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `last_modified` timestamp NULL DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `project_id` (`project_id`),
  KEY `task_id` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `file_reviews`
--

CREATE TABLE IF NOT EXISTS `file_reviews` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `file_id` int(10) NOT NULL,
  `revision_id` int(10) NOT NULL,
  `reviewer_id` int(10) NOT NULL,
  `review` text NOT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `file_id` (`file_id`),
  KEY `revision_id` (`revision_id`),
  KEY `reviewer_id` (`reviewer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `file_revisions`
--

CREATE TABLE IF NOT EXISTS `file_revisions` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(10) NOT NULL DEFAULT '0',
  `uploaded_by` int(10) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `stored_name` varchar(255) NOT NULL,
  `size` varchar(155) DEFAULT NULL,
  `extension` varchar(155) DEFAULT NULL,
  `mime_type` varchar(100) NOT NULL,
  `description` text,
  `approval_comment` text,
  `approver` int(10) unsigned DEFAULT NULL,
  `date_approval` timestamp NULL DEFAULT NULL,
  `status` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `last_modified` timestamp NULL DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `file_id` (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE IF NOT EXISTS `invoices` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `project` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `header_note` text,
  `footer_note` text,
  `date_sent` varchar(10) DEFAULT NULL,
  `due_date` varchar(10) DEFAULT NULL,
  `total_ex_tax` float(10,2) NOT NULL DEFAULT '0.00',
  `tax_rate` float(10,2) NOT NULL DEFAULT '0.00',
  `tax_amount` float(10,2) NOT NULL DEFAULT '0.00',
  `total_inc_tax` float(10,2) NOT NULL DEFAULT '0.00',
  `status` char(1) NOT NULL DEFAULT '',
  `active` char(1) NOT NULL DEFAULT '',
  `created` varchar(16) DEFAULT NULL,
  `modified` varchar(16) DEFAULT NULL,
  `published` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `project`, `header_note`, `footer_note`, `date_sent`, `due_date`, `total_ex_tax`, `tax_rate`, `tax_amount`, `total_inc_tax`, `status`, `active`, `created`, `modified`, `published`) VALUES
(1, 1, NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, 0.00, '0', '1', '2009-11-29 16:51', NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `ips`
--

CREATE TABLE IF NOT EXISTS `ips` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ip` int(11) NOT NULL,
  `ip_raw` char(20) NOT NULL,
  `description` text,
  `creator` int(10) NOT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip_raw` (`ip_raw`),
  KEY `ip` (`ip`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `ips`
--

INSERT INTO `ips` (`id`, `ip`, `ip_raw`, `description`, `creator`, `created_date`, `last_modified`) VALUES
(1, 1263842118, '75.84.179.70', 'Ip Blocking Enabled', 1, '2010-07-06 03:01:33', '2010-07-06 03:01:33');

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL DEFAULT '0',
  `task_id` int(10) DEFAULT '0',
  `company_id` int(10) NOT NULL DEFAULT '0',
  `creator` int(10) NOT NULL DEFAULT '0',
  `topic` varchar(255) DEFAULT NULL,
  `subject` varchar(155) DEFAULT NULL,
  `description` text,
  `published` char(1) NOT NULL DEFAULT '',
  `date` datetime DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE IF NOT EXISTS `options` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `area` varchar(20) NOT NULL,
  `creator` int(10) NOT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `area` (`area`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`id`, `name`, `area`, `creator`, `created_date`, `last_modified`) VALUES
(2, 'IT', 'project_type', 0, '2010-09-23 03:48:13', '2010-09-23 03:48:13'),
(3, 'Programming', 'project_type', 0, '2010-09-23 03:48:13', '2010-09-23 03:48:13'),
(4, 'Website', 'project_type', 0, '2010-09-23 03:48:13', '2010-09-23 03:48:13'),
(6, 'Writing', 'project_type', 0, '2010-09-23 03:48:13', '2010-09-23 03:48:13'),
(7, 'Reporting', 'project_type', 0, '2010-09-23 03:48:13', '2010-09-23 03:48:13'),
(8, 'Research', 'project_type', 0, '2010-09-23 03:48:13', '2010-09-23 03:48:13'),
(9, 'Quality Assurance', 'project_type', 0, '2010-09-23 03:48:13', '2010-09-23 03:48:13'),
(10, 'Administrative', 'task_type', 0, '2010-09-23 03:48:13', '2010-09-29 18:30:57'),
(12, 'Programming', 'task_type', 0, '2010-09-23 03:48:13', '2010-09-23 03:48:13'),
(13, 'Website', 'task_type', 0, '2010-09-23 03:48:13', '2010-09-23 03:48:13'),
(14, 'Proposal', 'task_type', 0, '2010-09-23 03:48:13', '2010-09-23 03:48:13'),
(15, 'Writing', 'task_type', 0, '2010-09-23 03:48:13', '2010-09-23 03:48:13'),
(16, 'Reporting', 'task_type', 0, '2010-09-23 03:48:13', '2010-09-23 03:48:13'),
(17, 'Research', 'task_type', 0, '2010-09-23 03:48:13', '2010-09-23 03:48:13'),
(18, 'Quality Assurance', 'task_type', 0, '2010-09-23 03:48:13', '2010-09-23 03:48:13'),
(19, 'Conference Call', 'task_type', 1, '2010-10-08 06:09:01', '2010-10-08 06:09:01'),
(20, 'Business Development', 'task_type', 1, '2010-10-10 23:28:09', '2010-10-10 23:28:09'),
(22, 'Email Blast', 'project_type', 1, '2010-10-13 15:46:36', '2010-10-13 15:46:36'),
(23, 'Tech Support', 'task_type', 1, '2011-04-21 22:56:56', '2011-04-21 22:56:56');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL DEFAULT '0',
  `company_internal` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `start_date` timestamp NULL DEFAULT NULL,
  `start_year` int(4) DEFAULT NULL,
  `start_month` int(2) DEFAULT NULL,
  `start_day` int(2) DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `actual_end_date` timestamp NULL DEFAULT NULL,
  `hours_worked` float NOT NULL DEFAULT '0',
  `status` int(11) DEFAULT '0',
  `percent_complete` tinyint(4) DEFAULT '0',
  `description` text,
  `target_budget` decimal(10,2) DEFAULT '0.00',
  `actual_budget` decimal(10,2) DEFAULT '0.00',
  `task_count` int(5) NOT NULL DEFAULT '0',
  `file_count` int(5) NOT NULL DEFAULT '0',
  `creator` int(11) DEFAULT '0',
  `contacts` char(100) DEFAULT NULL,
  `priority` tinyint(4) DEFAULT '0',
  `type` smallint(6) NOT NULL DEFAULT '0',
  `harvest_id` bigint(10) DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sdate` (`start_date`),
  KEY `idx_edate` (`end_date`),
  KEY `idx_proj1` (`company_id`),
  KEY `company_id` (`company_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `project_contacts`
--

CREATE TABLE IF NOT EXISTS `project_contacts` (
  `project_id` int(10) NOT NULL,
  `contact_id` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_teams`
--

CREATE TABLE IF NOT EXISTS `project_teams` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_date` timestamp NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `user_id` (`user_id`),
  KEY `project_id_2` (`project_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `option_value` longtext,
  `option_name` varchar(255) NOT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_name` (`option_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `option_value`, `option_name`, `created_date`, `last_modified`) VALUES
(1, '1', 'master_company', '2010-06-07 23:23:03', '2014-04-12 16:44:50'),
(2, '0', 'enable_ip', '2010-06-07 23:23:03', '2014-04-12 16:44:50'),
(3, 'America/Los_Angeles', 'timezone', '2011-02-25 12:22:16', '2014-04-12 16:44:50'),
(4, 'F j, Y', 'date_format', '2011-02-25 12:22:16', '2011-03-01 06:40:14'),
(5, '', 'date_format_custom', '2011-02-25 12:22:16', '2011-03-01 06:40:14'),
(6, 'g:i A', 'time_format', '2011-02-25 12:22:16', '2011-03-01 06:40:14'),
(7, '', 'time_format_custom', '2011-02-25 12:22:16', '2011-03-01 06:40:14'),
(8, 'jpg,gif,png,txt,docx,doc,pdf,php,xls,xlsx,csv,psd,ppt,pptx,pot,potx,rar,zip,tar,gz,tgz,bz2,html,htm,avi,mov,fla,swf,asf,flv,sql,mp3', 'allowed_file_formats', '2011-05-03 10:45:48', '2014-04-12 16:44:50'),
(9, 'es_ES', 'locale', '2014-04-10 10:25:24', '2014-04-12 16:44:50');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
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
  `description` text,
  `creator` int(11) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  `client_publish` tinyint(1) NOT NULL DEFAULT '0',
  `dynamic` tinyint(1) NOT NULL DEFAULT '0',
  `access` int(11) NOT NULL DEFAULT '0',
  `notify` int(11) NOT NULL DEFAULT '0',
  `contacts` char(100) DEFAULT NULL,
  `custom` longtext,
  `type` smallint(6) NOT NULL DEFAULT '0',
  `file_count` int(5) NOT NULL DEFAULT '0',
  `harvest_id` bigint(10) DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_task_parent` (`parent`),
  KEY `idx_task_project` (`project_id`),
  KEY `idx_task_order` (`order`),
  KEY `idx_task1` (`start_date`),
  KEY `idx_task2` (`end_date`),
  KEY `project_id` (`project_id`),
  KEY `company_id` (`company_id`),
  KEY `end_date` (`end_date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `task_assignments`
--

CREATE TABLE IF NOT EXISTS `task_assignments` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(10) unsigned NOT NULL DEFAULT '0',
  `assigned_by` int(10) unsigned NOT NULL DEFAULT '0',
  `assigned_to` int(10) unsigned NOT NULL DEFAULT '0',
  `comments` text,
  `created_date` timestamp NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `task_contacts`
--

CREATE TABLE IF NOT EXISTS `task_contacts` (
  `task_id` int(10) NOT NULL,
  `contact_id` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `task_dependencies`
--

CREATE TABLE IF NOT EXISTS `task_dependencies` (
  `task_id` int(11) NOT NULL,
  `req_task_id` int(11) NOT NULL,
  PRIMARY KEY (`task_id`,`req_task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `times`
--

CREATE TABLE IF NOT EXISTS `times` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `creator` int(10) NOT NULL,
  `company_id` int(10) NOT NULL,
  `project_id` int(10) DEFAULT NULL,
  `task_id` int(10) DEFAULT NULL,
  `user_id` int(10) NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  `hours` float NOT NULL,
  `description` text NOT NULL,
  `billable` int(1) NOT NULL DEFAULT '0',
  `bill_status` enum('','sent','paid') NOT NULL,
  `harvest_id` bigint(10) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user2role`
--

CREATE TABLE IF NOT EXISTS `user2role` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `role_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user2role`
--

INSERT INTO `user2role` (`user_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(6, 1),
(6, 2),
(6, 4),
(6, 8),
(6, 9),
(6, 11),
(7, 2),
(8, 1),
(10, 2),
(10, 9),
(11, 2),
(12, 11),
(12, 12),
(13, 11),
(13, 12),
(14, 11),
(14, 12),
(15, 11),
(15, 12),
(16, 11),
(16, 12),
(17, 11),
(17, 12),
(18, 11),
(18, 12),
(19, 11),
(19, 12),
(20, 11),
(20, 12),
(21, 11),
(21, 12),
(22, 11),
(22, 12),
(23, 11),
(23, 12),
(24, 11),
(24, 12),
(25, 11),
(25, 12),
(26, 11),
(26, 12),
(27, 11),
(27, 12),
(28, 11),
(28, 12),
(29, 11),
(29, 12),
(30, 11),
(30, 12),
(31, 11),
(31, 12);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(128) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `phone_mobile` varchar(20) DEFAULT NULL,
  `phone_home` varchar(20) DEFAULT NULL,
  `phone_work` varchar(20) DEFAULT NULL,
  `phone_fax` varchar(20) DEFAULT NULL,
  `job_title` varchar(100) DEFAULT NULL,
  `jabber` varchar(50) DEFAULT NULL,
  `aol` varchar(50) DEFAULT NULL,
  `yahoo` varchar(50) DEFAULT NULL,
  `google_talk` varchar(50) DEFAULT NULL,
  `msn` varchar(50) DEFAULT NULL,
  `ichat` varchar(100) DEFAULT NULL,
  `skype` varchar(100) DEFAULT NULL,
  `description` text,
  `user_status` enum('0','d') NOT NULL DEFAULT 'd',
  `last_login` timestamp NULL DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `pw_forgotten` timestamp NULL DEFAULT NULL,
  `forgotten_hash` varchar(100) DEFAULT NULL,
  `harvest_id` bigint(10) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `UserStatus` (`user_status`),
  KEY `Joined` (`created_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT AUTO_INCREMENT=32 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `phone_mobile`, `phone_home`, `phone_work`, `phone_fax`, `job_title`, `jabber`, `aol`, `yahoo`, `google_talk`, `msn`, `ichat`, `skype`, `description`, `user_status`, `last_login`, `hash`, `pw_forgotten`, `forgotten_hash`, `harvest_id`, `created_date`, `last_modified`) VALUES
(1, 'eric@ericlamb.net', '5a54d20fd2f3b3964ad2667bb1ee2da2467e9e0c52f98b8aa1337ff013a1cf63187feccc639246b0f05a59ff404f3b8877eb1808dfe0978276ef0ae4b47037c9', 'Eric', 'Lamb', '310.739.3322', '', '', '', 'CEO / CTO', '', 'ericlamb62', '', 'eric@ericlamb.net', '', NULL, NULL, '', 'd', '2014-04-10 19:23:20', '5ee6450f22b6230f8f78e0ac5f09f71a', '2014-04-07 21:08:14', 'f9c23344-077d-b141-5cfa-c62f0e95942f', NULL, '2006-08-18 08:05:36', '2014-04-10 19:23:20'),
(6, 'awagoh16@yahoo.com', 'eef5464508f468fb5098d06ad6ba7c9a99442a2e3da550c3abc8f183d51da52661cf645279d7a8031f3ed0da889f8993d62da39238d97fff586f578415032618', 'Barika', 'Croom', '310.339.6001', '', '', '', 'CMO', '', '', '', '', '', NULL, NULL, '', '0', '2010-09-06 03:49:13', 'f9516a5d8144458d8d60a6d038957e97', '2010-07-13 19:40:56', '', NULL, '2010-03-24 04:42:04', '2010-09-06 03:49:13'),
(7, 'barika.croom@deiworldwide.com', '405fc27c275a34ea716e865f84716b806dffb9286dd237d6f6307d1fc810e46c9441606940196cba2133f037a2385633974727a29feed053452ac2ae28565701', 'Hillary', 'Ratner', '', '', '8187639065', '', 'Resident Blogger', '', '', '', '', '', '', '', '', '0', '2010-07-20 18:49:48', '9c6d645cc0c09e7760e94c97d0bcb1fa', NULL, NULL, NULL, '2010-07-13 20:16:25', '2010-07-20 18:49:48'),
(8, 'ckimrie@gmail.com', 'cbc7cc225cd4e298b242f708ca214a4627bf0edb636f23fcab51e83f4e9525eff3da92cc85af7e0c95df85f59ebfa8ab80f8c8f6564dfd22c80659c428549a13', 'Christopher', 'Imrie', '', '', '', '', 'Tea Sipper', '', '', '', '', '', '', '', '', 'd', '2013-08-22 13:51:16', '720da4362e37aee61cedfb45bce91526', '2013-08-19 23:45:23', '', NULL, '2013-08-19 11:47:41', '2013-08-22 13:51:16'),
(10, 'rusty@mithra62.com', '685adb999d43cb6f83b676f18c519d0540b306b427f02670d7f7cfa460ad657277d5c4fbffef258b116854d2e702aa79afb57b58cd4704006fb8e3089de4f4b5', 'Rusty', 'Shakleford', '', '', '', '', 'test', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'd', NULL, '5b21b75478713a459ad455842949cc35', NULL, NULL, NULL, NULL, '2014-04-03 00:17:44'),
(12, 'eric@mithra62.com2', 'e08dd7bc0225f51883c576356f4c4e17327ab4d7374e17692916d84392753de08d241447f950258e493b0374c834ca4b6733a142f963032f7d15219b421a8097', 'Eric', 'Lamb', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'd', NULL, '1bafa2b4711ccd4f2834e60f7eb78912', NULL, NULL, NULL, NULL, '2014-04-07 10:59:30'),
(13, 'eric@mithra62.com2', 'd9a25ec7040823184c96757f9a3ea504375bde92c087b69d4b4e011f4200556dee17a5c23998c66bb100440c3d1c315c87f929acdca2508346bb43db1330acc1', 'Eric', 'Lamb', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'd', NULL, '572d90420947f2ed49fdc22ca7b5e376', NULL, NULL, NULL, NULL, '2014-04-07 11:01:15'),
(15, 'eric@mithra62.com2', '7d38d9338bb87251791f7198eb7e0adb735d14bed7f9a81a493c81aef42492c655623cadb9b6343537915599de6d9dcb98c3cd9fd1fb5a302d7efcde38fe531c', 'Eric', 'Lamb', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'd', NULL, '8f5f377488c69d413e60b4d19566bfbb', NULL, NULL, NULL, NULL, '2014-04-07 11:08:17'),
(16, 'test@mithra62.com', 'f3794b3e2911f45362a44330735e0f059859b8604535a4a09265880efca73794709857129c4f672dad2acbcb59ee40caf493f89d4221e58cb730d25b3b046d0d', 'Eric', 'Lamb', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'd', NULL, '913382d3d8500b21729b67c81bc3bcd1', NULL, NULL, NULL, NULL, '2014-04-08 21:23:19'),
(17, 'eric@mithra62.com2', '20385983006e39cd409a4d2ad5c8e5027318f5f9ec9c086f0562e905d0c0c12f34b9e34aaf780d2278bb20383727bd96b6cd2666f0dfe756082798406c1fbd99', 'Eric', 'Lamb', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'd', NULL, '6495f58f6e13f06de967bd6deef088dc', NULL, NULL, NULL, NULL, '2014-04-07 11:09:14'),
(22, 'eric@mithra62.com2', '3ea06be5e2c830afd1f6dfd6fb35091e00612c320dfe6131406c4c720fe152df267ebeeedca9a28ae708819f124a21c233869f4311a5ce00273c224ebfcb035e', 'Eric', 'Lamb', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'd', NULL, 'e0d54ee1e21d8bc556733b5471bd55ae', NULL, NULL, NULL, NULL, '2014-04-07 11:24:37'),
(24, 'eric@mithra62.com2', 'fd4843149ea1caf37058b94a0575b8c8b19ffe98fa0f3c640a623ae8058f408e9fa10a46951866264f4426e2987f3168a7b2ae0d870a89e65b16d02710c20f83', 'Eric', 'Lamb', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'd', NULL, 'b53055ff17523bf8d4b17eb3eba550ea', NULL, NULL, NULL, NULL, '2014-04-07 11:33:51'),
(25, 'eric@mithra62.com2', '3ec29bd6a5530e45ded40e8a3efc267862e2066080a1050b555f05f6dbea6aa14dd6cdfdcbc8846306f983642948b79ca7f25bf5db949d253ee578a4eec1ae4b', 'Eric', 'Lamb', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'd', NULL, '0169f2b7347835e20b5e2457410ff06e', NULL, NULL, NULL, NULL, '2014-04-07 11:36:02'),
(26, 'eric@mithra62.com2', '0736ef049737be3eda914451966673829558e5bece36d721c86eab195f4574c372ecd5f677326d3efb923bfa385119404678dac01936f3c87648a1cbc075ddf1', 'Eric', 'Lamb', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'd', NULL, '2427e3b81748c64ffd37c765ecd1746c', NULL, NULL, NULL, NULL, '2014-04-07 11:37:23'),
(27, 'eric@mithra62.com2', 'c407ce1a01d60a35b083613d8c3b2e33de1e0348bc8c33c849b57ef7fcb8c61336f9e082dc4014695343fab4a0c7d07d653b966bf55b1fafcf5df486f61aa4b7', 'Eric', 'Lamb', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'd', NULL, 'e57d3e7a6919a960f93b8bcd21fd9734', NULL, NULL, NULL, NULL, '2014-04-07 11:56:42'),
(28, 'eric@mithra62.com2', '83721436a027f5dc00d9b538fa4a871204c2c17ddb49dd171301fc4d40200240c0652da9b4b5a6ab623b4f1ba738ffae11545ce5b09b5b78f7371683d626511c', 'Eric', 'Lamb', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'd', NULL, '93c3d49d093c07e729f05f5daf84700a', NULL, NULL, NULL, NULL, '2014-04-07 12:10:26'),
(29, 'eric@mithra62.com2', '8eddcf5e01a15b68b43f85510f8fdcf0d4c3f6e19cb2af6a9fbce469437a73f538e7f72bc8a59ac009854a2e26f95b073209022458222b5f3e9250dc73dc4c37', 'Eric', 'Lamb', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'd', NULL, '902df6ca0d8296e6075931913f032771', NULL, NULL, NULL, NULL, '2014-04-07 12:11:26'),
(30, 'eric@mithra62.com2', 'a4823dbd873658366f05d63f481dc48de9d0126dee2f9d56d698daeb5c10642c1efd0f68235a9e1365fdc211ae195bdcf3e8dd401b1695a5b7f2a47eec8157ab', 'Eric', 'Lamb', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'd', NULL, 'da62cbed1c62421a15628a9b84e5f06c', NULL, NULL, NULL, NULL, '2014-04-07 12:12:51'),
(31, 'eric@mithra62.com', 'c42aa02aa62121b6df40aedec9cd19f6618e263f8636ba006c0ac45e1050c848b46a79a9faab8eb5b8aa263313361a8be9a1fd7229749e71690658d3024c9404', 'Eric', 'Lamb', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', 'd', NULL, '6b8c4be62997dbe94b94871eba1232c7', NULL, NULL, NULL, NULL, '2014-04-07 12:13:49');

-- --------------------------------------------------------

--
-- Table structure for table `user_access_log`
--

CREATE TABLE IF NOT EXISTS `user_access_log` (
  `user_access_log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `user_ip` varchar(15) NOT NULL,
  `date_time_in` datetime DEFAULT '0000-00-00 00:00:00',
  `date_time_out` datetime DEFAULT '0000-00-00 00:00:00',
  `date_time_last_action` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`user_access_log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_data`
--

CREATE TABLE IF NOT EXISTS `user_data` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `option_value` longtext,
  `option_name` varchar(255) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE IF NOT EXISTS `user_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `name`, `description`, `last_modified`, `created_date`) VALUES
(1, 'Administrators', 'Site administrators', '2010-02-24 03:27:00', '2010-02-20 02:19:20'),
(2, 'User', '', '2010-07-14 07:33:35', '2010-02-24 21:05:03'),
(9, 'Task Manager', '', '2010-02-24 04:11:29', '2010-02-24 04:11:29'),
(8, 'Company Manager', '', '2010-02-24 04:10:31', '2010-02-24 04:10:31'),
(4, 'Project Manager', '', '2014-04-10 03:05:06', '2010-02-24 02:20:31'),
(11, 'User Manager', '', '2010-02-28 03:47:03', '2010-02-28 02:31:12'),
(12, 'Freelance', 'Container for the default freelancer functionality', '2010-07-11 09:55:18', '2010-07-11 09:55:18'),
(13, 'Test role', '', '2014-04-10 05:35:44', '2014-04-10 05:35:44');

-- --------------------------------------------------------

--
-- Table structure for table `user_role_2_permissions`
--

CREATE TABLE IF NOT EXISTS `user_role_2_permissions` (
  `role_id` int(11) NOT NULL DEFAULT '0',
  `permission_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`role_id`,`permission_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
(2, 1),
(2, 7),
(2, 9),
(2, 11),
(2, 12),
(4, 1),
(4, 2),
(4, 3),
(4, 6),
(4, 7),
(4, 8),
(4, 9),
(4, 10),
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
(12, 12);

-- --------------------------------------------------------

--
-- Table structure for table `user_role_permissions`
--

CREATE TABLE IF NOT EXISTS `user_role_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `site_area` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `user_role_permissions`
--

INSERT INTO `user_role_permissions` (`id`, `name`, `description`, `site_area`) VALUES
(1, 'view_projects', 'Can the user view projects?', 'projects'),
(2, 'manage_projects', 'Can the user manage projects? This includes the management of the "project team".', 'projects'),
(3, 'view_companies', 'Can the user view companies? Required for pretty much everything.', 'companies'),
(4, 'manage_companies', 'Can the user manage the companies?', 'companies'),
(5, 'view_company_contacts', 'Can the user view the company contacts?', 'companies'),
(6, 'manage_company_contacts', 'Can the user manage the company contacts?', 'companies'),
(7, 'view_tasks', 'Can the user view tasks?', 'tasks'),
(8, 'manage_tasks', 'Can the user manage tasks?', 'tasks'),
(9, 'view_files', 'Can the user view files?', 'files'),
(10, 'manage_files', 'Can the user manage files?', 'files'),
(11, 'view_time', 'Can the user view time tracker data?', 'time-tracker'),
(12, 'track_time', 'Can the user track their time?', 'time-tracker'),
(13, 'manage_time', 'Can the user manage the time of others?', 'time-tracker'),
(14, 'admin_access', 'Can the user access the admin area? Required for all admin modules.', 'admin'),
(15, 'view_users_data', 'Can the user view other users data?', 'users'),
(16, 'manage_users', 'Can the user manage other users?', 'users'),
(17, 'manage_roles', 'Can the user manage user roles and permissions?', 'roles'),
(18, 'manage_ips', 'Can the user manage allowable IP addresses?', 'ips'),
(19, 'manage_options', 'Can the user manage the available type options?', 'options');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
