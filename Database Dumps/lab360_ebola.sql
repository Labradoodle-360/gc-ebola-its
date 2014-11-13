-- phpMyAdmin SQL Dump
-- version 4.2.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 13, 2014 at 02:38 PM
-- Server version: 5.5.40-cll
-- PHP Version: 5.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `lab360_ebola`
--

-- --------------------------------------------------------

--
-- Table structure for table `smf_custom_fields`
--

CREATE TABLE IF NOT EXISTS `smf_custom_fields` (
`id_field` smallint(5) NOT NULL,
  `col_name` varchar(12) NOT NULL DEFAULT '',
  `field_name` varchar(40) NOT NULL DEFAULT '',
  `field_desc` varchar(255) NOT NULL DEFAULT '',
  `field_type` varchar(8) NOT NULL DEFAULT 'text',
  `field_length` smallint(5) NOT NULL DEFAULT '255',
  `field_options` text NOT NULL,
  `field_order` tinyint(3) NOT NULL DEFAULT '0',
  `mask` varchar(255) NOT NULL DEFAULT '',
  `show_reg` tinyint(3) NOT NULL DEFAULT '0',
  `show_display` tinyint(3) NOT NULL DEFAULT '0',
  `show_mlist` tinyint(3) NOT NULL DEFAULT '0',
  `show_profile` varchar(20) NOT NULL DEFAULT 'forumprofile',
  `private` tinyint(3) NOT NULL DEFAULT '0',
  `active` tinyint(3) NOT NULL DEFAULT '1',
  `bbc` tinyint(3) NOT NULL DEFAULT '0',
  `can_search` tinyint(3) NOT NULL DEFAULT '0',
  `default_value` varchar(255) NOT NULL DEFAULT '',
  `enclose` text NOT NULL,
  `placement` tinyint(3) NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `smf_custom_fields`
--

INSERT INTO `smf_custom_fields` (`id_field`, `col_name`, `field_name`, `field_desc`, `field_type`, `field_length`, `field_options`, `field_order`, `mask`, `show_reg`, `show_display`, `show_mlist`, `show_profile`, `private`, `active`, `bbc`, `can_search`, `default_value`, `enclose`, `placement`) VALUES
(3, 'cust_skype', 'Skype', 'Your Skype name', 'text', 32, '', 3, 'nohtml', 0, 1, 0, 'forumprofile', 0, 1, 0, 0, '', '<a href="skype:{INPUT}?call"><img src="{DEFAULT_IMAGES_URL}/skype.png" alt="{INPUT}" title="{INPUT}" /></a> ', 1),
(7, 'cust_greenv', 'Greenville College Student ID', 'Your Greenville College Student ID.', 'text', 255, '', 2, 'number', 2, 1, 1, 'account', 0, 1, 0, 1, '', '', 5),
(8, 'cust_firstn', 'Full Name', 'Your first and last name.', 'text', 255, '', 1, 'nohtml', 2, 1, 1, 'forumprofile', 0, 1, 0, 1, '', '', 5),
(5, 'cust_loca', 'Location', 'Geographic location.', 'text', 50, '', 4, 'nohtml', 0, 1, 0, 'forumprofile', 0, 1, 0, 0, '', '', 0),
(6, 'cust_gender', 'Gender', 'Your gender.', 'radio', 255, 'None,Male,Female', 5, 'nohtml', 0, 1, 0, 'forumprofile', 0, 1, 0, 0, 'None', '<span class=" generic_icons gender_{INPUT}" title="{INPUT}"></span>', 1);

-- --------------------------------------------------------

--
-- Table structure for table `smf_it_archive_questions`
--

CREATE TABLE IF NOT EXISTS `smf_it_archive_questions` (
`id` mediumint(8) unsigned NOT NULL,
  `quizzes_id` mediumint(8) unsigned NOT NULL,
  `quizzes_categories_id` mediumint(8) unsigned NOT NULL,
  `questions_id` mediumint(8) unsigned NOT NULL,
  `questions_type` tinyint(1) NOT NULL,
  `question_text` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  `question_prompt` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `source` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  `revisions` mediumint(8) NOT NULL,
  `date_added` mediumint(10) unsigned NOT NULL,
  `id_member` mediumint(8) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smf_it_log_questions_answers`
--

CREATE TABLE IF NOT EXISTS `smf_it_log_questions_answers` (
`id` mediumint(8) unsigned NOT NULL,
  `questions_id` mediumint(8) unsigned NOT NULL,
  `id_member` mediumint(8) unsigned NOT NULL,
  `action_type` tinyint(1) NOT NULL,
  `date_recorded` mediumint(10) NOT NULL,
  `archive_questions_id` mediumint(8) NOT NULL,
  `archive_questions_answers_id` mediumint(8) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumping data for table `smf_it_log_questions_answers`
--

INSERT INTO `smf_it_log_questions_answers` (`id`, `questions_id`, `id_member`, `action_type`, `date_recorded`, `archive_questions_id`, `archive_questions_answers_id`) VALUES
(1, 1, 1, 1, 11245, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `smf_it_questions`
--

CREATE TABLE IF NOT EXISTS `smf_it_questions` (
`id` mediumint(8) unsigned NOT NULL,
  `quizzes_id` mediumint(8) unsigned NOT NULL,
  `quizzes_categories_id` mediumint(8) unsigned NOT NULL,
  `question_type` tinyint(1) NOT NULL,
  `question_text` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  `question_prompt` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `source` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  `revisions` mediumint(8) NOT NULL,
  `date_added` int(10) unsigned NOT NULL,
  `id_member` mediumint(8) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumping data for table `smf_it_questions`
--

INSERT INTO `smf_it_questions` (`id`, `quizzes_id`, `quizzes_categories_id`, `question_type`, `question_text`, `question_prompt`, `source`, `revisions`, `date_added`, `id_member`) VALUES
(1, 2, 5, 1, 'Who is the professor that highly resembles Barney Fife from the Andy Griffith Show?', NULL, 'Matthew P. Kerle', 0, 1415840198, 3),
(2, 2, 5, 2, 'Test', NULL, 'Test', 0, 1415840382, 3),
(3, 2, 6, 2, 'Test', NULL, 'Test', 0, 1415840419, 3),
(4, 2, 7, 2, 'Test Text', 'Test Prompt', 'My head', 0, 1415917368, 3);

-- --------------------------------------------------------

--
-- Table structure for table `smf_it_questions_answers`
--

CREATE TABLE IF NOT EXISTS `smf_it_questions_answers` (
`questions_id` mediumint(8) unsigned NOT NULL,
  `multiple_choice_a` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  `multiple_choice_b` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  `multiple_choice_c` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  `multiple_choice_d` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  `multiple_choice_correct` tinyint(1) unsigned DEFAULT NULL,
  `true_false_answer` tinyint(1) unsigned DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumping data for table `smf_it_questions_answers`
--

INSERT INTO `smf_it_questions_answers` (`questions_id`, `multiple_choice_a`, `multiple_choice_b`, `multiple_choice_c`, `multiple_choice_d`, `multiple_choice_correct`, `true_false_answer`) VALUES
(1, 'Scott Giffen', 'Ivan Filby', 'Deloy Cole', '', 1, 0),
(2, '', '', '', '', 0, 1),
(3, '', '', '', '', 0, 2),
(4, '', '', '', '', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `smf_it_quizzes`
--

CREATE TABLE IF NOT EXISTS `smf_it_quizzes` (
`id` mediumint(8) unsigned NOT NULL,
  `quiz_name` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  `total_questions` mediumint(8) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumping data for table `smf_it_quizzes`
--

INSERT INTO `smf_it_quizzes` (`id`, `quiz_name`, `total_questions`) VALUES
(1, 'Know Your Ebola', 0),
(2, 'Greenville College Trivia', 4);

-- --------------------------------------------------------

--
-- Table structure for table `smf_it_quizzes_categories`
--

CREATE TABLE IF NOT EXISTS `smf_it_quizzes_categories` (
`id` mediumint(8) unsigned NOT NULL,
  `quizzes_id` mediumint(8) NOT NULL,
  `category_name` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  `total_questions` mediumint(8) NOT NULL DEFAULT '0',
  `date_added` int(10) unsigned NOT NULL,
  `id_member` mediumint(8) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Dumping data for table `smf_it_quizzes_categories`
--

INSERT INTO `smf_it_quizzes_categories` (`id`, `quizzes_id`, `category_name`, `total_questions`, `date_added`, `id_member`) VALUES
(1, 1, 'Symptoms', 0, 1415826234, 3),
(2, 1, 'Prevention', 0, 1415826234, 3),
(3, 1, 'Containment', 0, 1415826234, 3),
(4, 1, 'Delete This', 0, 1415826234, 3),
(5, 2, 'Test', 2, 1415840136, 3),
(6, 2, 'New Category', 1, 1415840395, 3),
(7, 2, 'hi', 1, 1415844042, 3),
(8, 2, 'Test 2', 0, 1415915514, 3);

-- --------------------------------------------------------

--
-- Table structure for table `smf_it_recycled_questions`
--

CREATE TABLE IF NOT EXISTS `smf_it_recycled_questions` (
`id` mediumint(8) unsigned NOT NULL,
  `quizzes_id` mediumint(8) unsigned NOT NULL,
  `source` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  `revisions` mediumint(8) NOT NULL,
  `date_added` mediumint(10) unsigned NOT NULL,
  `id_member` mediumint(8) unsigned NOT NULL,
  `date_deleted` mediumint(10) NOT NULL,
  `id_member_deleted` mediumint(8) NOT NULL,
  `question_text` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  `question_prompt` varchar(255) COLLATE utf8_swedish_ci DEFAULT NULL,
  `quizzes_categories_id` mediumint(8) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smf_it_recycled_questions_answers`
--

CREATE TABLE IF NOT EXISTS `smf_it_recycled_questions_answers` (
`recycled_questions_id` mediumint(8) unsigned NOT NULL,
  `questions_id` mediumint(8) unsigned NOT NULL,
  `multiplechoice_a` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  `multiplechoice_b` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  `multiplechoice_c` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  `multiplechoice_d` varchar(255) COLLATE utf8_swedish_ci NOT NULL,
  `true_false_answer` tinyint(1) unsigned NOT NULL,
  `multiple_choice_correct` tinyint(1) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `smf_custom_fields`
--
ALTER TABLE `smf_custom_fields`
 ADD PRIMARY KEY (`id_field`), ADD UNIQUE KEY `col_name` (`col_name`);

--
-- Indexes for table `smf_it_archive_questions`
--
ALTER TABLE `smf_it_archive_questions`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smf_it_log_questions_answers`
--
ALTER TABLE `smf_it_log_questions_answers`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smf_it_questions`
--
ALTER TABLE `smf_it_questions`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smf_it_questions_answers`
--
ALTER TABLE `smf_it_questions_answers`
 ADD PRIMARY KEY (`questions_id`);

--
-- Indexes for table `smf_it_quizzes`
--
ALTER TABLE `smf_it_quizzes`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smf_it_quizzes_categories`
--
ALTER TABLE `smf_it_quizzes_categories`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smf_it_recycled_questions`
--
ALTER TABLE `smf_it_recycled_questions`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smf_it_recycled_questions_answers`
--
ALTER TABLE `smf_it_recycled_questions_answers`
 ADD PRIMARY KEY (`recycled_questions_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `smf_custom_fields`
--
ALTER TABLE `smf_custom_fields`
MODIFY `id_field` smallint(5) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `smf_it_archive_questions`
--
ALTER TABLE `smf_it_archive_questions`
MODIFY `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `smf_it_log_questions_answers`
--
ALTER TABLE `smf_it_log_questions_answers`
MODIFY `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `smf_it_questions`
--
ALTER TABLE `smf_it_questions`
MODIFY `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `smf_it_questions_answers`
--
ALTER TABLE `smf_it_questions_answers`
MODIFY `questions_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `smf_it_quizzes`
--
ALTER TABLE `smf_it_quizzes`
MODIFY `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `smf_it_quizzes_categories`
--
ALTER TABLE `smf_it_quizzes_categories`
MODIFY `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `smf_it_recycled_questions`
--
ALTER TABLE `smf_it_recycled_questions`
MODIFY `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `smf_it_recycled_questions_answers`
--
ALTER TABLE `smf_it_recycled_questions_answers`
MODIFY `recycled_questions_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
