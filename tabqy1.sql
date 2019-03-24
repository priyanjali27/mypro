-- phpMyAdmin SQL Dump
-- version 4.7.9
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 24, 2019 at 06:28 PM
-- Server version: 10.1.31-MariaDB
-- PHP Version: 7.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tabqy1`
--

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_advertisement`
--

CREATE TABLE `tabqy_advertisement` (
  `advertisement_id` int(30) NOT NULL,
  `advertisement_company_id` int(30) NOT NULL,
  `advertisement_resturant_id` int(30) NOT NULL,
  `advertisement_branch_id` varchar(255) NOT NULL,
  `advertisement_image` varchar(255) CHARACTER SET utf8 NOT NULL,
  `advertisement_status` int(11) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_advertisement`
--

INSERT INTO `tabqy_advertisement` (`advertisement_id`, `advertisement_company_id`, `advertisement_resturant_id`, `advertisement_branch_id`, `advertisement_image`, `advertisement_status`, `from_date`, `to_date`) VALUES
(2, 3, 4, '11', '1527164646_testimonl1.jpg', 1, '2018-05-30', '2018-05-31'),
(3, 4, 18, '20', '1527595708_g6.jpg', 1, '2018-05-30', '2018-05-31');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_categories`
--

CREATE TABLE `tabqy_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `category_brand_id` int(11) NOT NULL,
  `category_image` varchar(255) NOT NULL,
  `category_status` int(11) NOT NULL,
  `category_created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_categories`
--

INSERT INTO `tabqy_categories` (`category_id`, `category_name`, `category_brand_id`, `category_image`, `category_status`, `category_created`) VALUES
(1, 'fastfood', 11, '1522135453_Desert.jpg', 1, '2018-03-27 07:21:35'),
(2, 'Salad', 11, '', 1, '2018-03-27 13:06:39'),
(3, 'Salad', 11, '', 1, '2018-03-27 13:07:29'),
(4, 'Salad', 11, '', 1, '2018-03-27 13:08:51'),
(5, 'Salad', 11, '', 1, '2018-03-27 13:09:07'),
(6, 'all day meals', 11, '1522487593_index.jpg', 1, '2018-03-31 09:13:13'),
(7, 'test1', 11, '1522826647_Hydrangeas.jpg', 1, '2018-04-04 07:24:07'),
(8, 'test1', 11, '1522826648_Hydrangeas.jpg', 1, '2018-04-04 07:24:08'),
(9, 'test1', 11, '1522826648_Hydrangeas.jpg', 1, '2018-04-04 07:24:08'),
(10, 'test1', 11, '1522826648_Hydrangeas.jpg', 1, '2018-04-04 07:24:08'),
(11, 'test1', 11, '1522826648_Hydrangeas.jpg', 1, '2018-04-04 07:24:08'),
(12, 'testttttttttttttt', 11, '1522935406_Chrysanthemum.jpg', 1, '2018-04-05 19:06:46'),
(13, 'testttt11', 26, '1523091692_1521898036635789.jpg', 1, '2018-04-07 14:31:32'),
(14, 'gdgdgdg', 26, '1523092032_1519907303438943_126358a162b168ea25364109c7c04d9d.jpg', 1, '2018-04-07 14:37:12'),
(16, 'bcbcvbc1111', 11, '1523092050_1519907303438943_126358a162b168ea25364109c7c04d9d.jpg', 1, '2018-04-07 14:37:30');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_cities`
--

CREATE TABLE `tabqy_cities` (
  `city_id` int(11) NOT NULL,
  `country_code` varchar(255) NOT NULL,
  `city_name` varchar(255) NOT NULL,
  `city_code` varchar(255) NOT NULL,
  `is_region` tinyint(1) NOT NULL,
  `city_status` int(11) NOT NULL,
  `city_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_cities`
--

INSERT INTO `tabqy_cities` (`city_id`, `country_code`, `city_name`, `city_code`, `is_region`, `city_status`, `city_created`) VALUES
(1, 'IN', 'Noida', 'NOI', 1, 1, '2018-05-03 07:03:29'),
(2, 'IN', 'Lucknow', 'LUK', 1, 1, '2018-05-03 07:03:29'),
(3, 'IN', 'Agra', 'AGR', 1, 1, '2018-05-03 07:03:29'),
(4, 'IN', 'Allahabad', 'ALD', 1, 1, '2018-05-03 07:05:23'),
(5, 'AUs', 'Sydney', 'SYD', 1, 1, '2018-05-03 07:05:51'),
(6, 'AUs', 'Melbourne', 'MELB', 1, 1, '2018-05-03 07:05:51'),
(7, 'AUs', 'Brisbane', 'BRIS', 1, 1, '2018-05-03 07:05:51'),
(8, 'AUs', 'Perth', 'PER', 1, 1, '2018-05-03 07:05:51'),
(9, 'IN', 'Kanpur', 'KNPR', 1, 1, '2018-05-03 07:03:29'),
(10, 'IN', 'Bareilly', 'BRLY', 1, 1, '2018-05-03 07:04:55'),
(11, 'IN', 'Delhi', 'DL', 1, 1, '2018-05-03 07:03:29'),
(12, 'IN', 'Chandigarh', 'CHD', 1, 1, '2018-05-03 07:03:29'),
(13, 'IN', 'Jaipur', 'JPR', 1, 1, '2018-05-03 07:03:29'),
(14, 'IN', 'Mumbai', 'MUM', 1, 1, '2018-05-03 07:05:23'),
(15, 'IN', 'Pune', 'PNE', 1, 1, '2018-05-03 07:05:23'),
(16, 'IN', 'Ahmedabad', 'AHMD', 1, 1, '2018-05-03 07:05:23'),
(17, 'IN', 'Goa', 'GOA', 1, 1, '2018-05-03 07:05:23'),
(18, 'IN', 'Surat', 'SUR', 1, 1, '2018-05-03 07:05:23'),
(19, 'IN', 'Nagpur', 'NAG', 1, 1, '2018-05-03 07:05:23'),
(20, 'SAU', 'Riyadh en', 'RYDH', 1, 1, '2018-05-29 11:57:15'),
(21, 'SAU', 'Mecca', 'MECA', 1, 1, '2018-05-29 11:58:40'),
(22, 'SAU', 'Dammnam', 'DAMM', 1, 1, '2018-05-22 07:34:26'),
(23, 'SAU', 'Jubail en', 'JUBAIL', 1, 1, '2018-05-29 11:58:40');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_cities_language`
--

CREATE TABLE `tabqy_cities_language` (
  `city_language_id` int(30) NOT NULL,
  `city_language_name` varchar(120) CHARACTER SET utf8 NOT NULL,
  `city_language_city_id` int(30) NOT NULL,
  `city_language_language_code` char(5) CHARACTER SET utf8 NOT NULL,
  `city_language_edit` enum('0','1') CHARACTER SET utf8 NOT NULL,
  `city_language_status` enum('0','1') CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_cities_language`
--

INSERT INTO `tabqy_cities_language` (`city_language_id`, `city_language_name`, `city_language_city_id`, `city_language_language_code`, `city_language_edit`, `city_language_status`) VALUES
(10, 'Sakaka', 29, 'ar', '0', '1'),
(11, 'Sakaka', 29, 'en', '1', '1'),
(12, 'Noida', 1, 'ar', '0', '1'),
(13, 'Noida', 1, 'en', '1', '1'),
(14, 'Lucknow', 2, 'ar', '0', '1'),
(15, 'Lucknow', 2, 'en', '1', '1'),
(16, 'Agra', 3, 'ar', '0', '1'),
(17, 'Agra', 3, 'en', '1', '1'),
(18, 'Allahabad', 4, 'ar', '0', '1'),
(19, 'Allahabad', 4, 'en', '1', '1'),
(20, 'Sydney', 5, 'ar', '0', '1'),
(21, 'Sydney', 5, 'en', '1', '1'),
(22, 'Melbourne', 6, 'ar', '0', '1'),
(23, 'Melbourne', 6, 'en', '1', '1'),
(24, 'Brisbane', 7, 'ar', '0', '1'),
(25, 'Brisbane', 7, 'en', '1', '1'),
(26, 'Perth', 8, 'ar', '0', '1'),
(27, 'Perth', 8, 'en', '1', '1'),
(28, 'Kanpur', 9, 'ar', '0', '1'),
(29, 'Kanpur', 9, 'en', '1', '1'),
(30, 'Bareilly', 10, 'ar', '0', '1'),
(31, 'Bareilly', 10, 'en', '1', '1'),
(32, 'Delhi', 11, 'ar', '0', '1'),
(33, 'Delhi', 11, 'en', '1', '1'),
(34, 'Chandigarh', 12, 'ar', '0', '1'),
(35, 'Chandigarh', 12, 'en', '1', '1'),
(36, 'Jaipur', 13, 'ar', '0', '1'),
(37, 'Jaipur', 13, 'en', '1', '1'),
(38, 'Mumbai', 14, 'ar', '0', '1'),
(39, 'Mumbai', 14, 'en', '1', '1'),
(40, 'Pune', 15, 'ar', '0', '1'),
(41, 'Pune', 15, 'en', '1', '1'),
(42, 'Ahmedabad', 16, 'ar', '0', '1'),
(43, 'Ahmedabad', 16, 'en', '1', '1'),
(44, 'Goa', 17, 'ar', '0', '1'),
(45, 'Goa', 17, 'en', '1', '1'),
(46, 'Surat', 18, 'ar', '0', '1'),
(47, 'Surat', 18, 'en', '1', '1'),
(48, 'Nagpur', 19, 'ar', '0', '1'),
(49, 'Nagpur', 19, 'en', '1', '1'),
(50, 'Riyadh ar', 20, 'ar', '1', '1'),
(51, 'Riyadh en', 20, 'en', '1', '1'),
(52, 'Mecca', 21, 'ar', '0', '1'),
(53, 'Mecca', 21, 'en', '1', '1'),
(54, 'Dammnam', 22, 'ar', '0', '1'),
(55, 'Dammnam', 22, 'en', '1', '1'),
(56, 'Jubail ar', 23, 'ar', '1', '1'),
(57, 'Jubail en', 23, 'en', '1', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_comboitem`
--

CREATE TABLE `tabqy_comboitem` (
  `comboitem_id` int(10) NOT NULL,
  `comboitem_comboitem_id` int(10) NOT NULL,
  `comboitem_item_id` int(10) NOT NULL,
  `comboitem_portion_id` int(10) NOT NULL,
  `comboitem_quantity` int(10) NOT NULL,
  `comboitem_status` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tabqy_comboitem`
--

INSERT INTO `tabqy_comboitem` (`comboitem_id`, `comboitem_comboitem_id`, `comboitem_item_id`, `comboitem_portion_id`, `comboitem_quantity`, `comboitem_status`) VALUES
(18, 6, 9, 2, 1, '1'),
(19, 3, 9, 0, 1, '1'),
(20, 2, 9, 0, 1, '1'),
(21, 9, 9, 0, 1, '1');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_commission`
--

CREATE TABLE `tabqy_commission` (
  `commission_id` int(11) NOT NULL,
  `commission_brand_id` varchar(255) NOT NULL,
  `commission_country_id` varchar(255) NOT NULL,
  `commission_type` varchar(255) NOT NULL,
  `commission_value` int(11) NOT NULL,
  `commission_status` int(11) NOT NULL,
  `commission_created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_commission`
--

INSERT INTO `tabqy_commission` (`commission_id`, `commission_brand_id`, `commission_country_id`, `commission_type`, `commission_value`, `commission_status`, `commission_created`) VALUES
(1, '16', 'IN', 'fixed', 56, 0, '2018-04-07 14:59:18'),
(2, '11', 'IN', 'fixed', 56, 1, '2018-04-07 14:59:18'),
(3, '5', 'IN', 'fixed', 56, 1, '2018-04-07 14:59:33'),
(4, '3', 'IN', 'fixed', 56, 1, '2018-04-07 14:59:33'),
(5, '16', 'IN', 'fixed', 561, 1, '2018-04-07 15:01:37'),
(6, '11', 'SAU', 'fixed', 15, 1, '2018-04-09 18:07:54'),
(7, '9', 'SAU', 'fixed', 15, 1, '2018-04-09 18:07:54'),
(8, '5', 'SAU', 'fixed', 15, 1, '2018-04-09 18:07:54'),
(9, '3', 'SAU', 'fixed', 15, 1, '2018-04-09 18:07:54'),
(10, '11', 'IN', 'percentage', 58, 1, '2018-04-20 19:15:15'),
(11, '1', 'IN', 'fixed', 1, 0, '2018-04-30 18:09:37'),
(12, '1', 'IN', 'fixed', 11, 1, '2018-04-30 18:10:09');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_company`
--

CREATE TABLE `tabqy_company` (
  `company_id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `company_address` varchar(1000) NOT NULL,
  `company_password` varchar(255) NOT NULL,
  `company_country` varchar(255) NOT NULL,
  `company_email` varchar(255) NOT NULL,
  `company_phone` varchar(50) NOT NULL,
  `company_code` varchar(255) NOT NULL,
  `company_cr_no` bigint(15) NOT NULL,
  `company_vat_no` varchar(255) NOT NULL,
  `company_status` int(11) NOT NULL,
  `company_created` datetime NOT NULL,
  `company_last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_company`
--

INSERT INTO `tabqy_company` (`company_id`, `company_name`, `company_address`, `company_password`, `company_country`, `company_email`, `company_phone`, `company_code`, `company_cr_no`, `company_vat_no`, `company_status`, `company_created`, `company_last_updated`) VALUES
(1, 'Food Comapny', 'damam', '', '', 'best@gmail.com', '2222255888', '00003', 55555522222, '11144477', 1, '2018-05-03 08:27:22', '2018-05-03 08:27:22'),
(2, 'Food corner', 'this is test address', '', 'SAU', 'test121@gmail.com', '54545455445', 'test0012', 123456789, '444664', 1, '2018-05-03 08:29:17', '2018-05-28 06:20:50'),
(3, 'International food co.', 'lfgkljkldgjkldfjglkdfjglkdfg', '', '', 'intcfood@gmail.com', '654654654', 'INTFOOD', 65544654, 'TF 545646546', 1, '2018-05-03 09:10:54', '2018-05-03 09:10:54'),
(4, 'MnH Technologies', 'Test address', '', 'IN', 'info@askonlinesolutions.com', '9910017727', 'MnH', 11111122211, 'VTS1123', 1, '2018-05-03 12:44:03', '2018-05-21 07:10:42'),
(5, 'Super company1', 'hgjkhdgkfdjgdfd', '', '', 'supercompany@gmail.com', '798798791546', 'SUPERCOMP', 7987878979798, '89797989jjhk', 1, '2018-05-03 13:42:38', '2018-05-03 13:44:30'),
(6, 'shalu', 'noida sec 6', '', '', 'shalu.askonline@gmail.com', '8005462213', '25', 5, '7', 1, '2018-05-04 05:30:56', '2018-05-04 05:30:56'),
(7, 'fxgf', 'fgbfch', '', '', 'fgfc@dfg.ffg', '567567567', 'fgf', 2, '56', 1, '2018-05-04 05:44:00', '2018-05-07 11:15:20'),
(8, 'Tabqy', 'test', '', 'IN', 'tabqy@gmail.com', '3453445345', 'Tabqy', 2533543535, '3453535', 1, '2018-05-07 11:15:29', '2018-05-21 07:10:37'),
(9, 'zad', 'Dammam', '', '', 'ass@gmail.com', '258458745', '00025', 1212125454, '1246987547', 1, '2018-05-09 08:09:08', '2018-05-09 08:09:08'),
(10, 'Global Food Company', 'test address of saudi arabia', '', 'SAU', 'globalfoodco@gmail.com', '564748564654', 'GLOBALFC', 7894561345654, '465489764165', 1, '2018-05-22 07:14:30', '2018-05-22 07:14:30'),
(11, 'companytest', 'noida', '2yXAS3ity9NOA', 'SAU', 'companytest@gmail.com', '123456789', 'companytest', 123456, '123456789', 1, '2018-05-22 12:29:17', '2018-05-28 05:00:38'),
(12, 'test125', 'kfdjhjkhjkh', '2yvFJsGDNnvAo', 'SAU', 'test125@gmail.com', '146546546', 'test125', 526, '645646546546', 1, '2018-05-28 05:25:53', '2018-05-28 05:25:53'),
(14, 'redchilli', 'test address adresssss', '2yXAS3ity9NOA', 'IN', 'redchilli@gmail.com', '4545454545', '1008564', 200758, '212121', 1, '2018-05-29 12:41:39', '2018-06-05 09:30:23');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_company_language`
--

CREATE TABLE `tabqy_company_language` (
  `company_language_id` int(11) NOT NULL,
  `company_language_company_id` int(11) NOT NULL,
  `company_language_language_code` varchar(255) NOT NULL,
  `company_language_name` varchar(255) NOT NULL,
  `company_language_address` varchar(1000) NOT NULL,
  `company_language_edit` enum('0','1') NOT NULL,
  `company_language_status` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_company_language`
--

INSERT INTO `tabqy_company_language` (`company_language_id`, `company_language_company_id`, `company_language_language_code`, `company_language_name`, `company_language_address`, `company_language_edit`, `company_language_status`) VALUES
(1, 1, 'ar', 'Ø´Ø±ÙƒØ© Ø£Ø·Ø¹Ù…Ø©', 'Ø§Ù„Ø¯Ù…Ø§Ù…', '1', '1'),
(2, 1, 'en', 'Food Comapny', 'damam', '1', '1'),
(3, 2, 'ar', 'Test', 'this is test address', '0', '1'),
(4, 2, 'en', 'Food corner', 'this is test address', '1', '1'),
(5, 3, 'ar', 'International food co. ar', 'lfgkljkldgjkldfjglkdfjglkdfg', '1', '1'),
(6, 3, 'en', 'International food co.', 'lfgkljkldgjkldfjglkdfjglkdfg', '1', '1'),
(7, 4, 'ar', 'MnH Technologies', 'Test address', '0', '1'),
(8, 4, 'en', 'MnH Technologies', 'Test address', '1', '1'),
(9, 5, 'ar', 'Super company ar', 'hgjkhdgkfdjgdfd', '1', '1'),
(10, 5, 'en', 'Super company1', 'hgjkhdgkfdjgdfd', '1', '1'),
(11, 6, 'ar', 'shalu', 'noida sec 6', '0', '1'),
(12, 6, 'en', 'shalu', 'noida sec 6', '1', '1'),
(13, 7, 'ar', 'fxgf', 'fgbfch', '0', '1'),
(14, 7, 'en', 'fxgf', 'fgbfch', '1', '1'),
(15, 8, 'ar', 'Tabqy', 'test', '0', '1'),
(16, 8, 'en', 'Tabqy', 'test', '1', '1'),
(17, 9, 'ar', 'zad', 'Dammam', '0', '1'),
(18, 9, 'en', 'zad', 'Dammam', '1', '1'),
(19, 10, 'ar', 'Global Food Company', 'test address of saudi arabia', '0', '1'),
(20, 10, 'en', 'Global Food Company', 'test address of saudi arabia', '1', '1'),
(21, 11, 'ar', 'companytestarabic', 'noida', '1', '1'),
(22, 11, 'en', 'companytest', 'noida', '1', '1'),
(23, 12, 'ar', 'test125', 'kfdjhjkhjkh', '0', '1'),
(24, 12, 'en', 'test125', 'kfdjhjkhjkh', '1', '1'),
(27, 14, 'ar', 'redchilli', 'test address', '0', '1'),
(28, 14, 'en', 'redchilli', 'test address adresssss', '1', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_company_plan_assign`
--

CREATE TABLE `tabqy_company_plan_assign` (
  `assign_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `no_of_days` int(11) NOT NULL,
  `assign_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_company_plan_assign`
--

INSERT INTO `tabqy_company_plan_assign` (`assign_id`, `company_id`, `plan_id`, `no_of_days`, `assign_created`) VALUES
(1, 1, 14, 20, '2018-05-03 08:28:05'),
(2, 2, 14, 2, '2018-05-03 08:29:30'),
(3, 3, 16, 90, '2018-05-03 09:11:34'),
(4, 4, 16, 100, '2018-05-03 12:45:02'),
(5, 5, 17, 78, '2018-05-03 13:43:24'),
(6, 7, 16, 5, '2018-05-04 05:53:17'),
(7, 8, 11, 600, '2018-05-07 11:15:43'),
(8, 10, 15, 45, '2018-05-22 07:14:45'),
(9, 11, 14, 123, '2018-05-22 12:29:29');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_controllers`
--

CREATE TABLE `tabqy_controllers` (
  `controllers_id` int(10) NOT NULL,
  `controllers_name` varchar(120) NOT NULL,
  `controllers_sidebar_type` enum('1','2') NOT NULL DEFAULT '1' COMMENT '1-cargates2-car company',
  `controllers_display_name` varchar(140) NOT NULL,
  `controllers_heading` varchar(150) NOT NULL,
  `controllers_status` enum('0','1') NOT NULL DEFAULT '1' COMMENT '1-active,0-dactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tabqy_controllers`
--

INSERT INTO `tabqy_controllers` (`controllers_id`, `controllers_name`, `controllers_sidebar_type`, `controllers_display_name`, `controllers_heading`, `controllers_status`) VALUES
(1, 'resturant', '1', 'Restaurant Management ', 'System', '1'),
(2, 'user', '1', 'User Management ', 'System', '1'),
(3, 'category', '1', 'Category Management', 'Menu ', '1'),
(4, 'country', '1', 'Country Management ', 'Location', '1'),
(5, 'region', '1', 'Region Management ', 'Location', '1'),
(6, 'city', '1', 'City Management ', 'Location', '1'),
(7, 'location', '1', 'Location Management ', 'Location', '1'),
(8, 'zone', '1', 'Zone Management ', 'Location', '1'),
(9, 'Company', '1', 'Company Management ', 'System', '1'),
(10, 'brands', '1', 'Brand Managment', 'System', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_controllersres`
--

CREATE TABLE `tabqy_controllersres` (
  `controllers_id` int(10) NOT NULL,
  `controllers_name` varchar(120) NOT NULL,
  `controllers_sidebar_type` enum('1','2') NOT NULL DEFAULT '1' COMMENT '1-cargates2-car company',
  `controllers_display_name` varchar(140) NOT NULL,
  `controllers_heading` varchar(150) NOT NULL,
  `controllers_status` enum('0','1') NOT NULL DEFAULT '1' COMMENT '1-active,0-dactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tabqy_controllersres`
--

INSERT INTO `tabqy_controllersres` (`controllers_id`, `controllers_name`, `controllers_sidebar_type`, `controllers_display_name`, `controllers_heading`, `controllers_status`) VALUES
(1, 'company', '1', 'Brand Management  ', 'System', '1'),
(2, 'staffrole', '1', 'Staff Management ', 'System', '1'),
(3, 'companytax', '1', 'Tax Management', 'System', '1'),
(4, 'plan', '1', 'Plan Management ', 'System', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_countries`
--

CREATE TABLE `tabqy_countries` (
  `country_id` int(11) NOT NULL,
  `country_name` varchar(255) NOT NULL,
  `country_code` varchar(10) NOT NULL,
  `country_phone_code` varchar(5) NOT NULL,
  `country_file` varchar(255) NOT NULL,
  `country_currency_code` varchar(15) NOT NULL,
  `country_language_assign` varchar(30) CHARACTER SET utf8 NOT NULL,
  `country_status` int(11) NOT NULL,
  `country_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_countries`
--

INSERT INTO `tabqy_countries` (`country_id`, `country_name`, `country_code`, `country_phone_code`, `country_file`, `country_currency_code`, `country_language_assign`, `country_status`, `country_created`) VALUES
(16, 'India en', 'IN', '+91', '1525330174_1200px-Flag_of_India.svg.png', 'INR', 'ar,en', 1, '2018-05-03 06:49:34'),
(17, 'Australia', 'AUs', '+961', '1525330218_australian_flag.jpg', 'AUD', 'ar,en,en', 1, '2018-05-03 16:20:18'),
(18, 'Saudi Arabia en', 'SAU', '+966', '1526973171_Flag_of_Saudi_Arabia.png', 'SAR', 'ar,en', 1, '2018-05-29 11:56:23'),
(19, 'United Arab Emirate ', 'UAE', '+55', '1528359869_255px-Flag_of_the_People\'s_Republic_of_China.svg.png', 'AED', 'ar,en,ar,en', 1, '2018-06-07 17:54:29');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_countries_language`
--

CREATE TABLE `tabqy_countries_language` (
  `country_language_id` int(30) NOT NULL,
  `country_language_country_id` int(30) NOT NULL,
  `country_language_language_code` varchar(30) NOT NULL,
  `country_language_country_name` varchar(100) NOT NULL,
  `country_language_edit` enum('0','1') NOT NULL,
  `country_language_status` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_countries_language`
--

INSERT INTO `tabqy_countries_language` (`country_language_id`, `country_language_country_id`, `country_language_language_code`, `country_language_country_name`, `country_language_edit`, `country_language_status`) VALUES
(12, 16, 'ar', 'India ar', '1', '1'),
(13, 16, 'en', 'India en', '1', '1'),
(14, 17, 'ar', 'Australia', '0', '1'),
(15, 17, 'en', 'Australia', '1', '1'),
(16, 18, 'ar', 'Saudi Arabia ar', '1', '1'),
(17, 18, 'en', 'Saudi Arabia en', '1', '1'),
(18, 19, 'CH', 'United Arab Emirate ', '0', '1'),
(19, 19, 'ar', 'United Arab Emirate ', '0', '1'),
(20, 19, 'en', 'United Arab Emirate ', '1', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_couponextend`
--

CREATE TABLE `tabqy_couponextend` (
  `add_id` int(11) NOT NULL,
  `discount_id` int(11) NOT NULL,
  `no_of_coupons` int(11) NOT NULL,
  `extend_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_coupons`
--

CREATE TABLE `tabqy_coupons` (
  `discount_id` int(11) NOT NULL,
  `discount_country_id` varchar(255) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `discount_name` varchar(255) NOT NULL,
  `discount_code` varchar(255) NOT NULL,
  `min_orderval` bigint(20) NOT NULL,
  `no_of_coupons` int(11) NOT NULL,
  `value_type` varchar(255) NOT NULL,
  `discount_value` varchar(255) NOT NULL,
  `use_type` varchar(255) NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `discount_status` tinyint(11) NOT NULL,
  `discount_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_coupons`
--

INSERT INTO `tabqy_coupons` (`discount_id`, `discount_country_id`, `restaurant_id`, `discount_name`, `discount_code`, `min_orderval`, `no_of_coupons`, `value_type`, `discount_value`, `use_type`, `from_date`, `to_date`, `discount_status`, `discount_created`) VALUES
(1, 'IN', 11, 'Diwali Dhamaka', '23CQmm02', 0, 700, 'percentage', '500', 'multi', '2018-03-31', '2018-05-16', 1, '2018-04-17 10:53:47'),
(2, 'IN', 11, 'new discount', 'sdghyhfadghf', 98, 200, 'fixed', '200', 'single', '2018-03-31', '2018-04-12', 1, '2018-04-17 10:54:47'),
(3, 'IN', 11, 'FDFDFD', 'FDFDFD', 500, 500, 'percentage', '200', 'single', '2018-03-31', '2018-04-19', 1, '2018-04-18 08:42:03'),
(5, 'IN', 0, 'vghgj', 'gl9FW6UO', 8854655646464, 60, 'fixed', '67', 'single', '2018-04-26', '2018-05-22', 1, '2018-04-17 10:54:52'),
(6, 'IN', 0, 'fgdg', 'Pcn9gXFL', 666, 56, 'fixed', '6', 'single', '2018-04-18', '2018-04-27', 1, '2018-04-17 10:54:55'),
(7, 'IN', 0, 'Bumper Sale last', 'BUMPERSALE', 500, 100000, 'fixed', '500', 'single', '2018-04-25', '2018-04-28', 1, '2018-04-17 10:54:57'),
(9, 'IN', 0, 'Christmas Sale', 'CHRISTMAS50', 1250, 500, 'fixed', '50', 'single', '2018-04-17', '2018-04-30', 1, '2018-04-17 20:26:53');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_cuisine`
--

CREATE TABLE `tabqy_cuisine` (
  `cuisine_id` int(15) NOT NULL,
  `cuisine_name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `cuisine_image` varchar(255) CHARACTER SET utf8 NOT NULL,
  `cuisine_status` int(15) NOT NULL,
  `cuisine_created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_cuisine`
--

INSERT INTO `tabqy_cuisine` (`cuisine_id`, `cuisine_name`, `cuisine_image`, `cuisine_status`, `cuisine_created`) VALUES
(1, 'item1', '1527164989_1-LH_Auberge_1071.jpg', 1, '2018-05-24 17:59:49');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_cuisine_language`
--

CREATE TABLE `tabqy_cuisine_language` (
  `cuisine_language_id` int(11) NOT NULL,
  `cuisine_language_cuisine_id` int(10) NOT NULL,
  `cuisine_language_language_code` char(3) NOT NULL,
  `cuisine_language_cuisine_name` varchar(255) NOT NULL,
  `cuisine_language_edit` enum('0','1') NOT NULL COMMENT '1-edited,0-not',
  `cuisine_language_status` enum('0','1') NOT NULL COMMENT '0-inactive,1-active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tabqy_cuisine_language`
--

INSERT INTO `tabqy_cuisine_language` (`cuisine_language_id`, `cuisine_language_cuisine_id`, `cuisine_language_language_code`, `cuisine_language_cuisine_name`, `cuisine_language_edit`, `cuisine_language_status`) VALUES
(1, 1, 'ar', 'item1', '1', '1'),
(2, 1, 'en', 'item1', '1', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_customer`
--

CREATE TABLE `tabqy_customer` (
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_resturant_id` int(11) NOT NULL,
  `customer_username` varchar(255) NOT NULL,
  `customer_phone` varchar(15) NOT NULL,
  `customer_gender` varchar(255) NOT NULL,
  `customer_address` varchar(255) NOT NULL,
  `customer_city` varchar(255) NOT NULL,
  `customer_zipcode` varchar(255) NOT NULL,
  `customer_password` varchar(255) NOT NULL,
  `customer_state` varchar(255) NOT NULL,
  `customer_country` varchar(255) NOT NULL,
  `customer_dob` date NOT NULL,
  `customer_doa` date NOT NULL,
  `customer_qr_code` varchar(500) NOT NULL,
  `customer_status` int(11) NOT NULL,
  `customer_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_customer`
--

INSERT INTO `tabqy_customer` (`customer_id`, `customer_name`, `customer_email`, `customer_resturant_id`, `customer_username`, `customer_phone`, `customer_gender`, `customer_address`, `customer_city`, `customer_zipcode`, `customer_password`, `customer_state`, `customer_country`, `customer_dob`, `customer_doa`, `customer_qr_code`, `customer_status`, `customer_created`) VALUES
(3, 'priya1', 'priya@gmail.com', 0, 'priya', '7894561236', 'Female', 'dsadsaad', 'agra', '282005', '', 'up', 'india', '2018-03-06', '2018-03-12', '', 1, '2018-04-07 08:45:36'),
(4, 'sfsdfsd', 'sfdsd@gmail.com', 0, 'fsdfs', '878415464654', 'Female', 'sdfdsf', 'sdfs', 'dtghyyt', '', 'fsgghf', 'dhtgf', '2018-03-20', '2018-03-26', '', 1, '2018-03-28 04:00:49'),
(5, 'Candy', 'candy@gmail.com', 0, 'Candy', '12345678952', 'Female', 'fdhgjhfghj', 'ghdfhj', 'dfggdfg', '', 'dfhjfgfhjgfhjfhj', 'dfd', '2018-03-20', '2018-03-19', '', 1, '2018-03-28 04:02:13'),
(6, 'Meenu', 'meenu@gmail.com', 11, 'sharmameenu', '53535', 'Male', 'test', 'test', '3453555', '', 'test', 'tesy', '2018-03-28', '0000-00-00', '', 1, '2018-04-02 08:43:20'),
(7, 'Meenu sharma', 'meenu.askonline@gmail.com', 11, 'meenusharma123', '54446456', 'Female', 'dfgdg', 'test', '4545646', '', 'test', 'test', '2018-03-23', '0000-00-00', '', 1, '2018-03-28 16:30:05'),
(9, 'sdds', 'edas@dsz.dshjg', 0, 'dsdsds', '778798789789', 'Female', 'gjhgjhgh', 'gjhgjhg', 'gjhghjg', '', 'gjhgjh', 'gjgjh', '2003-04-15', '2010-04-29', '', 1, '2018-04-07 12:24:48'),
(10, 'dummy`1111', 'dummy67@gmail.com', 0, 'dummy67', '7894565541', 'Female', 'dfjhfdhygkjsfhkj', 'jksdhfkjh', '7845125', '', 'hkjhfkj', 'hdjkfhj', '2018-04-03', '2011-04-28', '', 1, '2018-04-07 08:45:25'),
(11, 'hgfhf', 'fghfh@dgdg.fgg', 0, 'fghgfh', '4565464', 'Female', 'dfbgfgf', 'ghfh', '567567', '', 'fghfh', 'fghfh', '2018-04-06', '0000-00-00', '', 1, '2018-04-07 12:54:36'),
(12, 'cgdfgf', 'meenu@gmail.com1', 0, 'fgfhgfh', '567575', 'Male', 'bfghfh', 'fghfh', '5657', '', 'hgfh', 'fhfh', '2018-04-18', '0000-00-00', '', 1, '2018-04-19 05:56:14');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_customer_resturant_relation`
--

CREATE TABLE `tabqy_customer_resturant_relation` (
  `customer_resturant_relation_if` int(11) NOT NULL,
  `customer_resturant_id` int(11) NOT NULL,
  `customer_customer_id` int(11) NOT NULL,
  `customer_resturant_relation_type` int(11) NOT NULL,
  `customer_resturant_relation_status` int(11) NOT NULL,
  `customer_resturant_relation_created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_customer_resturant_relation`
--

INSERT INTO `tabqy_customer_resturant_relation` (`customer_resturant_relation_if`, `customer_resturant_id`, `customer_customer_id`, `customer_resturant_relation_type`, `customer_resturant_relation_status`, `customer_resturant_relation_created`) VALUES
(2, 11, 8, 0, 1, '2018-03-30 09:11:14');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_delivery`
--

CREATE TABLE `tabqy_delivery` (
  `delivery_id` int(11) NOT NULL,
  `delivery_country_id` varchar(255) NOT NULL,
  `delivery_brand_id` int(11) NOT NULL,
  `delivery_type` varchar(255) NOT NULL,
  `delivery_value` int(11) NOT NULL,
  `delivery_status` int(11) NOT NULL,
  `delivery_created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_delivery`
--

INSERT INTO `tabqy_delivery` (`delivery_id`, `delivery_country_id`, `delivery_brand_id`, `delivery_type`, `delivery_value`, `delivery_status`, `delivery_created`) VALUES
(1, 'SAU', 16, 'fixed', 23, 0, '2018-04-04 18:52:23'),
(2, 'SAU', 11, 'fixed', 56, 1, '2018-04-04 18:52:47'),
(3, 'SAU', 16, 'fixed', 67, 1, '2018-04-04 18:54:07'),
(4, 'IN', 9, 'percentage', 55, 1, '2018-04-07 15:02:44'),
(5, 'SAU', 14, '', 0, 1, '2018-06-06 11:02:29');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_deliveryarea`
--

CREATE TABLE `tabqy_deliveryarea` (
  `deliveryarea_id` int(11) NOT NULL,
  `deliveryarea_country_id` varchar(255) NOT NULL,
  `deliveryarea_restaurant_id` int(11) NOT NULL,
  `deliveryarea_distance` varchar(255) NOT NULL,
  `deliveryarea_status` int(11) NOT NULL,
  `deliveryarea_created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_deliveryarea`
--

INSERT INTO `tabqy_deliveryarea` (`deliveryarea_id`, `deliveryarea_country_id`, `deliveryarea_restaurant_id`, `deliveryarea_distance`, `deliveryarea_status`, `deliveryarea_created`) VALUES
(2, 'IN', 53, '6', 1, '2018-04-17 18:48:42');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_department`
--

CREATE TABLE `tabqy_department` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(255) NOT NULL,
  `department_status` tinyint(4) NOT NULL,
  `department_created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_department`
--

INSERT INTO `tabqy_department` (`department_id`, `department_name`, `department_status`, `department_created`) VALUES
(1, 'Sales ', 1, '2018-02-27 07:27:28'),
(2, 'Account', 1, '2018-02-27 07:31:31'),
(4, 'Test Department', 1, '2018-03-06 13:39:11'),
(7, 'Accountant', 1, '2018-05-02 11:42:31');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_department_language`
--

CREATE TABLE `tabqy_department_language` (
  `department_language_id` int(11) NOT NULL,
  `department_language_department_id` int(10) NOT NULL,
  `department_language_language_code` char(3) NOT NULL,
  `department_language_department_name` varchar(255) NOT NULL,
  `department_language_edit` enum('0','1') NOT NULL COMMENT '1-edited,0-not',
  `department_language_status` enum('0','1') NOT NULL COMMENT '0-inactive,1-active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tabqy_department_language`
--

INSERT INTO `tabqy_department_language` (`department_language_id`, `department_language_department_id`, `department_language_language_code`, `department_language_department_name`, `department_language_edit`, `department_language_status`) VALUES
(1, 7, 'ar', 'Ù…Ø­Ø§Ø³Ø¨', '1', '1'),
(2, 7, 'en', 'Accountant', '1', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_discount`
--

CREATE TABLE `tabqy_discount` (
  `discount_id` int(11) NOT NULL,
  `discount_item_id` int(10) NOT NULL,
  `discount_price` decimal(12,2) NOT NULL,
  `discount_from` date NOT NULL,
  `discount_to` date NOT NULL,
  `discount_status` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tabqy_discount`
--

INSERT INTO `tabqy_discount` (`discount_id`, `discount_item_id`, `discount_price`, `discount_from`, `discount_to`, `discount_status`) VALUES
(1, 2, '23.00', '2018-03-26', '2018-03-26', '1'),
(2, 3, '40.00', '2018-03-31', '2018-03-31', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_faq`
--

CREATE TABLE `tabqy_faq` (
  `faq_id` int(11) NOT NULL,
  `faq_que` varchar(500) NOT NULL,
  `faq_ans` varchar(500) NOT NULL,
  `faq_category` varchar(255) NOT NULL,
  `faq_status` tinyint(4) NOT NULL,
  `faq_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_faq_categories`
--

CREATE TABLE `tabqy_faq_categories` (
  `cat_id` int(11) NOT NULL,
  `cat_name` varchar(255) NOT NULL,
  `cat_status` int(11) NOT NULL,
  `cat_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_floor`
--

CREATE TABLE `tabqy_floor` (
  `floor_id` int(11) NOT NULL,
  `floor_name` varchar(255) CHARACTER SET utf16 NOT NULL,
  `floor_status` tinyint(4) NOT NULL,
  `floor_created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_floor`
--

INSERT INTO `tabqy_floor` (`floor_id`, `floor_name`, `floor_status`, `floor_created`) VALUES
(1, 'fourthh', 1, '2018-05-29 07:51:41');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_floor_language`
--

CREATE TABLE `tabqy_floor_language` (
  `floor_language_id` int(11) NOT NULL,
  `floor_language_floor_id` int(11) NOT NULL,
  `floor_language_language_code` char(3) CHARACTER SET utf8 NOT NULL,
  `floor_language_floor_name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `floor_language_edit` enum('0','1') CHARACTER SET utf8 NOT NULL,
  `floor_language_status` enum('0','1') CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_floor_language`
--

INSERT INTO `tabqy_floor_language` (`floor_language_id`, `floor_language_floor_id`, `floor_language_language_code`, `floor_language_floor_name`, `floor_language_edit`, `floor_language_status`) VALUES
(11, 1, 'ar', 'fifth', '1', '1'),
(12, 1, 'en', 'fourthh', '1', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_item`
--

CREATE TABLE `tabqy_item` (
  `item_id` int(10) NOT NULL,
  `item_brand_id` int(11) NOT NULL,
  `item_country_id` varchar(11) NOT NULL,
  `item_company_id` int(11) NOT NULL,
  `item_name` varchar(200) NOT NULL,
  `item_code` varchar(80) NOT NULL,
  `item_category_id` int(10) NOT NULL,
  `item_prepration_cost` decimal(12,2) NOT NULL,
  `item_defaultprice` decimal(12,2) NOT NULL,
  `item_branchprice` enum('0','1') NOT NULL COMMENT '0-no,1-yes',
  `item_image` varchar(255) NOT NULL,
  `item_available_from` time NOT NULL,
  `item_available_to` time NOT NULL,
  `item_type` enum('0','1') NOT NULL COMMENT '0-individual item ,1-combo item ',
  `item_description` text NOT NULL,
  `item_visiblity` enum('1','2','3') NOT NULL COMMENT '1-web ,2-pos,3-both ',
  `item_created` datetime NOT NULL,
  `item_updated` datetime NOT NULL,
  `item_status` enum('0','1') NOT NULL COMMENT '0-active , 1- inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tabqy_item`
--

INSERT INTO `tabqy_item` (`item_id`, `item_brand_id`, `item_country_id`, `item_company_id`, `item_name`, `item_code`, `item_category_id`, `item_prepration_cost`, `item_defaultprice`, `item_branchprice`, `item_image`, `item_available_from`, `item_available_to`, `item_type`, `item_description`, `item_visiblity`, `item_created`, `item_updated`, `item_status`) VALUES
(2, 11, '', 0, 'Noodles', '34noodles', 2, '0.00', '45.00', '1', '1522045605_index.jpg', '09:00:00', '17:00:00', '0', 'test', '3', '2018-03-26 06:26:45', '2018-03-26 06:26:45', '1'),
(3, 11, '', 0, 'noodles', 'NT0001', 2, '0.00', '60.00', '1', '1522045767_hqdefault.jpg', '09:00:00', '17:00:00', '0', 'Veg Noodles saute\' with cabbage, carrots, capsicum, spring onions, green chillies, mixed along with 4 sauces and seasoned with spices and herbs to offer you a mouth watering flavour, enjoy and relish this basic and yummy Veg Noodles recipe', '3', '2018-03-26 06:57:21', '2018-03-26 06:57:21', '1'),
(4, 11, '', 0, 'Chicken', '002', 2, '0.00', '25.00', '0', '1522066210_single-steakburger.png', '07:45:00', '17:00:00', '0', 'Chicken Burger with fresh salads ', '3', '2018-03-26 12:10:10', '2018-03-26 12:10:10', '1'),
(5, 11, '', 0, 'fdsfgdg', '0021', 5, '0.00', '66.00', '0', '1522241180_index.jpg', '09:00:00', '17:00:00', '0', 'dsfsfd', '3', '2018-03-28 12:46:20', '2018-03-28 12:46:20', '1'),
(6, 11, '', 0, 'Mac & Cheese', 'MacCheese', 1, '0.00', '150.00', '0', '1522487683_jjj.jpg', '09:00:00', '17:00:00', '0', 'test', '3', '2018-03-31 09:14:43', '2018-03-31 09:14:43', '1'),
(7, 11, '', 0, 'test', '345', 3, '0.00', '500.00', '0', '1522929379_Chrysanthemum.jpg', '09:00:00', '17:00:00', '0', 'fdfcdbv', '3', '2018-04-09 11:42:47', '2018-04-09 11:42:47', '1'),
(9, 11, '', 0, 'bv', 'vbb', 6, '0.00', '67.00', '0', '1522931405_Chrysanthemum.jpg', '09:00:00', '17:00:00', '1', 'fghh', '2', '2018-04-09 11:36:47', '2018-04-09 11:36:47', '1'),
(10, 26, '', 0, 'chinse combo', 'CHINSECOMBO', 15, '0.00', '67.00', '1', '1523092182_508_error.jpg', '09:00:00', '17:00:00', '0', 'gdgdg', '3', '2018-04-07 14:39:42', '2018-04-07 14:39:42', '1'),
(11, 26, '', 0, 'American Chopsey', 'AMCHPSY', 14, '0.00', '300.00', '0', '1523094145_Penguins.jpg', '00:00:00', '23:59:00', '0', 'American Chopsey with sauce,garlic and beans with cheese', '2', '2018-04-07 15:25:46', '2018-04-07 15:25:46', '1'),
(12, 26, '', 0, 'dgfdg', 'dgfdg', 13, '0.00', '555.00', '0', '1523094666_1519907303438943_126358a162b168ea25364109c7c04d9d.jpg', '00:00:00', '23:59:00', '0', 'dfgdgd', '3', '2018-04-07 15:31:16', '2018-04-07 15:31:16', '1'),
(13, 26, '', 0, 'dgdfgdf', 'dfgdfg', 14, '0.00', '6567.00', '0', '1523095112_1519907303438943_126358a162b168ea25364109c7c04d9d.jpg', '09:00:00', '17:00:00', '0', 'gfhf', '1', '2018-04-07 16:17:06', '2018-04-07 16:17:06', '1'),
(16, 11, '', 0, 'Soya Chaap', 'SOYACHAAP', 6, '0.00', '67.00', '0', '1523255525_1521898036635789.jpg', '09:00:00', '17:00:00', '0', 'fsfs', '3', '2018-04-09 12:54:04', '2018-04-09 12:54:04', '1'),
(17, 52, '', 0, 'dfgdfg', 'dfg56', 27, '65.00', '55.00', '0', '1525415451_user-image-with-black-background_318-34564.jpg', '09:00:00', '17:00:00', '0', 'fdgdfgdf', '3', '2018-05-04 12:00:51', '2018-05-04 12:00:51', '1'),
(18, 52, '', 0, 'cxvxcv', 'xcvxcv', 27, '12.00', '12.00', '0', '1525415535_user-image-with-black-background_318-34564.jpg', '09:00:00', '17:00:00', '0', 'dfgdgd', '3', '2018-05-04 12:02:15', '2018-05-04 12:02:15', '1'),
(19, 52, '', 0, 'xczxxczx', 'xczzxc', 27, '123.00', '123.00', '0', '1525416222_user-image-with-black-background_318-34564.jpg', '09:00:00', '17:00:00', '0', 'xzczxc', '3', '2018-05-04 12:13:42', '2018-05-04 12:13:42', '1'),
(20, 52, '', 0, 'shhjhh', '123456', 27, '20.00', '12333.00', '0', '1525416625_user-image-with-black-background_318-34564.jpg', '09:00:00', '17:00:00', '1', 'xzcxzcxzc', '2', '2018-05-04 12:26:39', '2018-05-04 12:26:39', '1'),
(21, 52, '', 0, 'szdfzsdf', 'weer', 27, '345.00', '345.00', '0', '1525417144_user-image-with-black-background_318-34564.jpg', '09:00:00', '17:00:00', '0', 'dfdfds', '3', '2018-05-04 12:29:04', '2018-05-04 12:29:04', '1'),
(22, 52, '', 0, 'fd', 'dfdfs', 27, '40.00', '54.00', '0', '1525417207_user-image-with-black-background_318-34564.jpg', '09:00:00', '17:00:00', '1', 'dsfsdf', '2', '2018-05-04 12:33:56', '2018-05-04 12:33:56', '1'),
(23, 52, '', 0, 'dfsdf', 'dsfdsf', 27, '1221.00', '1212.00', '0', '1525417485_user-image-with-black-background_318-34564.jpg', '09:00:00', '17:00:00', '1', 'dsfdsf', '3', '2018-05-04 12:34:45', '2018-05-04 12:34:45', '1'),
(24, 52, '', 0, 'dsfsdf', '435', 27, '35434.00', '4324.00', '0', '1525417812_user-image-with-black-background_318-34564.jpg', '09:00:00', '17:00:00', '0', 'ddfdf', '3', '2018-05-04 12:40:12', '2018-05-04 12:40:12', '1'),
(25, 52, '', 0, 'dsasd', 'ssddsad', 27, '12133.00', '12133.00', '0', '1525417877_user-image-with-black-background_318-34564.jpg', '09:00:00', '17:00:00', '0', 'sadsad', '3', '2018-05-04 12:41:36', '2018-05-04 12:41:36', '1'),
(26, 52, '', 0, 'dsfdsf', 'dsfdsfsadsd', 27, '11.00', '111.00', '0', '1525417974_user-image-with-black-background_318-34564.jpg', '09:00:00', '17:00:00', '1', 'sdfdsf', '2', '2018-05-04 12:43:16', '2018-05-04 12:43:16', '1'),
(27, 52, '', 0, 'cxvxcv', 'xcvxcv45', 27, '435.00', '45.00', '0', '1525418751_user-image-with-black-background_318-34564.jpg', '09:00:00', '17:00:00', '0', 'xvxcv', '3', '2018-05-04 12:55:51', '2018-05-04 12:55:51', '1'),
(28, 48, '', 0, 'dgdfg', 'dfgdfgvxcvx', 29, '11111.00', '11111.00', '0', '1525423566_user-image-with-black-background_318-34564.jpg', '09:00:00', '17:00:00', '0', 'dfgdfg', '3', '2018-05-04 14:16:06', '2018-05-04 14:16:06', '1'),
(29, 48, '34', 19, 'ssssssssssssss', 'sdsd', 29, '341.00', '324.00', '0', '1525424201_user-image-with-black-background_318-34564.jpg', '09:00:00', '17:00:00', '0', 'dssds', '3', '2018-05-04 14:56:59', '2018-05-04 14:56:59', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_itemavlableday`
--

CREATE TABLE `tabqy_itemavlableday` (
  `itemavlableday_id` int(10) NOT NULL,
  `itemavlableday_item_id` int(10) NOT NULL,
  `itemavlableday_day` varchar(45) NOT NULL,
  `itemavlableday_status` enum('0','1') NOT NULL COMMENT '0- inactivate , 1-active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tabqy_itemavlableday`
--

INSERT INTO `tabqy_itemavlableday` (`itemavlableday_id`, `itemavlableday_item_id`, `itemavlableday_day`, `itemavlableday_status`) VALUES
(5, 2, 'Tuesday', '1'),
(6, 2, 'Wednesday', '1'),
(7, 2, 'Thursday', '1'),
(17, 3, 'Monday', '1'),
(18, 3, 'Tuesday', '1'),
(19, 3, 'Wednesday', '1'),
(20, 5, 'Wednesday', '1'),
(21, 6, 'Friday', '1'),
(31, 10, 'Tuesday', '1'),
(32, 10, 'Friday', '1'),
(73, 11, 'Thursday', '1'),
(75, 12, 'Tuesday', '1'),
(76, 13, 'Tuesday', '1'),
(97, 9, 'Tuesday', '1'),
(100, 7, 'Friday', '1'),
(124, 16, 'Friday', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_itemportion`
--

CREATE TABLE `tabqy_itemportion` (
  `itemportion_id` int(10) NOT NULL,
  `itemportion_item_id` int(10) NOT NULL,
  `itemportion_portion_id` int(10) NOT NULL,
  `itemportion_price` decimal(12,2) NOT NULL,
  `itemportion_status` enum('0','1') NOT NULL COMMENT '1-active,0-inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tabqy_itemportion`
--

INSERT INTO `tabqy_itemportion` (`itemportion_id`, `itemportion_item_id`, `itemportion_portion_id`, `itemportion_price`, `itemportion_status`) VALUES
(3, 2, 2, '34.00', '1'),
(12, 3, 1, '80.00', '1'),
(13, 6, 2, '90.00', '1'),
(23, 10, 1, '77.00', '1'),
(41, 11, 3, '98798854.00', '1'),
(42, 11, 36, '3.00', '1'),
(43, 11, 37, '6.00', '1'),
(44, 11, 38, '9999999999.99', '1'),
(45, 11, 39, '9999999999.99', '1'),
(46, 11, 40, '9999999999.99', '1'),
(50, 12, 26, '6.00', '1'),
(51, 12, 27, '6.00', '1'),
(52, 13, 47, '56.00', '1'),
(53, 13, 48, '666.00', '1'),
(54, 13, 49, '666.00', '1'),
(106, 16, 2, '78.00', '1'),
(107, 16, 3, '89.00', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_itempriceofbranch`
--

CREATE TABLE `tabqy_itempriceofbranch` (
  `itempriceofbranch_id` int(10) NOT NULL,
  `itempriceofbranch_branch_id` int(10) NOT NULL,
  `itempriceofbranch_item_id` int(10) NOT NULL,
  `itempriceofbranch_price` decimal(12,2) NOT NULL,
  `itempriceofbranch_status` enum('0','1') NOT NULL COMMENT '1-active,0-inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tabqy_itempriceofbranch`
--

INSERT INTO `tabqy_itempriceofbranch` (`itempriceofbranch_id`, `itempriceofbranch_branch_id`, `itempriceofbranch_item_id`, `itempriceofbranch_price`, `itempriceofbranch_status`) VALUES
(3, 12, 2, '1.00', '1'),
(7, 12, 3, '50.00', '1'),
(8, 29, 10, '67.00', '1'),
(9, 28, 10, '77.00', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_item_associated`
--

CREATE TABLE `tabqy_item_associated` (
  `associated_id` int(10) NOT NULL,
  `associated_item_id` int(10) NOT NULL,
  `associated_name` varchar(180) NOT NULL,
  `associated_value` decimal(12,2) NOT NULL,
  `associated_preadded` enum('0','1') NOT NULL,
  `associated_status` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tabqy_item_associated`
--

INSERT INTO `tabqy_item_associated` (`associated_id`, `associated_item_id`, `associated_name`, `associated_value`, `associated_preadded`, `associated_status`) VALUES
(35, 16, 'test1', '56.00', '0', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_item_displayonbranch`
--

CREATE TABLE `tabqy_item_displayonbranch` (
  `displayonbranch_id` int(10) NOT NULL,
  `displayonbranch_branch_id` int(10) NOT NULL,
  `displayonbranch_item_id` int(10) NOT NULL,
  `displayonbranch_status` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0-no,1-yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tabqy_item_displayonbranch`
--

INSERT INTO `tabqy_item_displayonbranch` (`displayonbranch_id`, `displayonbranch_branch_id`, `displayonbranch_item_id`, `displayonbranch_status`) VALUES
(3, 12, 2, '1'),
(7, 12, 3, '1'),
(8, 12, 5, '1'),
(9, 12, 6, '1'),
(21, 29, 10, '1'),
(22, 28, 10, '1'),
(23, 27, 10, '1'),
(35, 27, 11, '1'),
(39, 29, 12, '1'),
(40, 28, 12, '1'),
(41, 27, 12, '1'),
(42, 29, 13, '1'),
(43, 28, 13, '1'),
(44, 27, 13, '1'),
(57, 12, 9, '1'),
(60, 12, 7, '1'),
(145, 25, 16, '1'),
(146, 12, 16, '1'),
(147, 6, 16, '1');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_language`
--

CREATE TABLE `tabqy_language` (
  `language_id` int(10) NOT NULL,
  `language_name` varchar(100) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `language_flag` varchar(100) NOT NULL,
  `language_status` enum('0','1') NOT NULL DEFAULT '1' COMMENT '1-activate,0-dactivate',
  `language_created` datetime NOT NULL,
  `language_updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tabqy_language`
--

INSERT INTO `tabqy_language` (`language_id`, `language_name`, `language_code`, `language_flag`, `language_status`, `language_created`, `language_updated`) VALUES
(1, 'English', 'en', 'index1.jpg', '1', '2018-02-26 11:46:03', '2018-02-26 11:46:03'),
(2, 'Arabic', 'ar', 'United-Arab-Emirates-icon.png', '1', '2018-02-26 11:46:22', '2018-02-26 11:46:22'),
(3, 'Chines ', 'CH', '255px-Flag_of_the_People\'s_Republic_of_China.svg.png', '1', '2018-06-07 08:12:12', '2018-06-07 08:12:12');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_locations`
--

CREATE TABLE `tabqy_locations` (
  `location_id` int(11) NOT NULL,
  `country_code` varchar(255) NOT NULL,
  `city_code` varchar(255) NOT NULL,
  `location_name` varchar(500) NOT NULL,
  `location_code` varchar(255) NOT NULL,
  `is_zone` tinyint(1) NOT NULL,
  `location_status` int(11) NOT NULL,
  `location_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_locations`
--

INSERT INTO `tabqy_locations` (`location_id`, `country_code`, `city_code`, `location_name`, `location_code`, `is_zone`, `location_status`, `location_created`) VALUES
(1, 'IN', 'NOI', 'Sector-16', 'SEC16', 1, 1, '2018-05-03 07:08:08'),
(2, 'IN', 'NOI', 'Sector-19', 'SEC19', 1, 1, '2018-05-03 07:08:08'),
(3, 'IN', 'NOI', 'Sector-18', 'SEC18', 1, 1, '2018-05-03 07:08:27'),
(4, 'IN', 'NOI', 'Mayur Vihar', 'MYRV', 1, 1, '2018-05-03 07:08:08'),
(5, 'AUs', 'BRIS', 'South Bank', 'STHBNK', 1, 1, '2018-05-03 07:08:59'),
(6, 'AUs', 'BRIS', 'Alexendra Hills', 'ALXHL', 1, 1, '2018-05-03 07:08:59'),
(7, 'IN', 'NOI', 'New Ashok Nagar', 'NAKN', 1, 1, '2018-05-03 07:08:08'),
(8, 'IN', 'NOI', 'Sector-25', 'SEC25', 1, 1, '2018-05-03 07:08:08'),
(9, 'IN', 'NOI', 'Sector-38', 'SEC38', 1, 1, '2018-05-03 07:08:08'),
(10, 'IN', 'NOI', 'Sector-62', 'SEC62', 1, 1, '2018-05-03 07:09:30'),
(11, 'IN', 'NOI', 'Sector-27', 'SEC27', 1, 1, '2018-05-03 07:09:30'),
(12, 'IN', 'NOI', 'Sector-61', 'SEC61', 1, 1, '2018-05-03 07:09:30'),
(13, 'IN', 'DL', 'Preet Vihar', 'PRTVR', 1, 1, '2018-05-03 07:10:27'),
(14, 'IN', 'DL', 'Defence Colony', 'DFCOL', 1, 1, '2018-05-03 07:09:48'),
(15, 'IN', 'DL', 'Kirti Nagar', 'KRTNAG', 1, 1, '2018-05-03 07:10:09'),
(16, 'IN', 'DL', 'Vivek Vihar', 'VVKVH', 1, 1, '2018-05-03 07:10:27'),
(17, 'IN', 'LUK', 'Charbagh', 'CHBG', 0, 1, '2018-05-03 16:31:56'),
(18, 'IN', 'DL', 'east delhi', '123456', 1, 1, '2018-05-28 12:36:39'),
(19, 'SAU', 'RYDH', 'Al Olaya', 'ALOLAYA', 1, 1, '2018-05-22 07:37:56'),
(20, 'SAU', 'RYDH', 'Al Sahafa en', 'ALSAHAFA', 1, 1, '2018-05-29 11:58:10'),
(21, 'SAU', 'RYDH', 'King Faisal en', 'KINGFAI', 1, 1, '2018-05-29 11:59:04'),
(22, 'IN', 'DL', 'peetampura', 'Peetam', 1, 1, '2018-05-28 12:36:39');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_locations_language`
--

CREATE TABLE `tabqy_locations_language` (
  `location_language_id` int(20) NOT NULL,
  `location_language_location_id` int(20) NOT NULL,
  `location_language_location_name` varchar(120) CHARACTER SET utf8 NOT NULL,
  `location_language_language_code` char(6) CHARACTER SET utf8 NOT NULL,
  `location_language_edit` enum('0','1') CHARACTER SET utf8 NOT NULL,
  `location_language_status` enum('0','1') CHARACTER SET utf32 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_locations_language`
--

INSERT INTO `tabqy_locations_language` (`location_language_id`, `location_language_location_id`, `location_language_location_name`, `location_language_language_code`, `location_language_edit`, `location_language_status`) VALUES
(7, 28, 'Al hamra', 'ar', '0', '1'),
(8, 28, 'Al hamra', 'en', '1', '1'),
(9, 1, 'Sector-16', 'ar', '0', '1'),
(10, 1, 'Sector-16', 'en', '1', '1'),
(11, 2, 'Sector-19', 'ar', '0', '1'),
(12, 2, 'Sector-19', 'en', '1', '1'),
(13, 3, 'Sector-18', 'ar', '0', '1'),
(14, 3, 'Sector-18', 'en', '1', '1'),
(15, 4, 'Mayur Vihar', 'ar', '0', '1'),
(16, 4, 'Mayur Vihar', 'en', '1', '1'),
(17, 5, 'South Bank', 'ar', '0', '1'),
(18, 5, 'South Bank', 'en', '1', '1'),
(19, 6, 'Alexendra Hills', 'ar', '0', '1'),
(20, 6, 'Alexendra Hills', 'en', '1', '1'),
(21, 7, 'New Ashok Nagar', 'ar', '0', '1'),
(22, 7, 'New Ashok Nagar', 'en', '1', '1'),
(23, 8, 'Sector-25', 'ar', '0', '1'),
(24, 8, 'Sector-25', 'en', '1', '1'),
(25, 9, 'Sector-38', 'ar', '0', '1'),
(26, 9, 'Sector-38', 'en', '1', '1'),
(27, 10, 'Sector-62', 'ar', '0', '1'),
(28, 10, 'Sector-62', 'en', '1', '1'),
(29, 11, 'Sector-27', 'ar', '0', '1'),
(30, 11, 'Sector-27', 'en', '1', '1'),
(31, 12, 'Sector-61', 'ar', '0', '1'),
(32, 12, 'Sector-61', 'en', '1', '1'),
(33, 13, 'Preet Vihar', 'ar', '0', '1'),
(34, 13, 'Preet Vihar', 'en', '1', '1'),
(35, 14, 'Defence Colony', 'ar', '0', '1'),
(36, 14, 'Defence Colony', 'en', '1', '1'),
(37, 15, 'Kirti Nagar', 'ar', '0', '1'),
(38, 15, 'Kirti Nagar', 'en', '1', '1'),
(39, 16, 'Vivek Vihar', 'ar', '0', '1'),
(40, 16, 'Vivek Vihar', 'en', '1', '1'),
(41, 17, 'Charbagh', 'ar', '0', '1'),
(42, 17, 'Charbagh', 'en', '1', '1'),
(43, 18, 'east delhi', 'ar', '0', '1'),
(44, 18, 'east delhi', 'en', '1', '1'),
(45, 19, 'Al Olaya', 'ar', '0', '1'),
(46, 19, 'Al Olaya', 'en', '1', '1'),
(47, 20, 'Al Sahafa ar', 'ar', '1', '1'),
(48, 20, 'Al Sahafa en', 'en', '1', '1'),
(49, 21, 'King Faisal ar', 'ar', '1', '1'),
(50, 21, 'King Faisal en', 'en', '1', '1'),
(51, 22, 'peetampura', 'ar', '0', '1'),
(52, 22, 'peetampura', 'en', '1', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_mindeliveryorder`
--

CREATE TABLE `tabqy_mindeliveryorder` (
  `mindeliveryorder_id` int(11) NOT NULL,
  `mindeliveryorder_country_id` varchar(255) NOT NULL,
  `mindeliveryorder_restaurant_id` int(11) NOT NULL,
  `mindeliveryorder_type` varchar(255) NOT NULL,
  `mindeliveryorder_percentage` int(11) NOT NULL,
  `mindeliveryorder_name` varchar(255) NOT NULL,
  `mindeliveryorder_status` int(11) NOT NULL,
  `mindeliveryorder_created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_mindeliveryorder`
--

INSERT INTO `tabqy_mindeliveryorder` (`mindeliveryorder_id`, `mindeliveryorder_country_id`, `mindeliveryorder_restaurant_id`, `mindeliveryorder_type`, `mindeliveryorder_percentage`, `mindeliveryorder_name`, `mindeliveryorder_status`, `mindeliveryorder_created`) VALUES
(1, 'IN', 50, '', 5, '56', 1, '2018-04-17 12:22:02'),
(2, 'IN', 11, '', 55, '55', 1, '2018-04-18 14:45:59');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_orderstatus`
--

CREATE TABLE `tabqy_orderstatus` (
  `orderstatus_id` int(11) NOT NULL,
  `orderstatus_name` varchar(255) NOT NULL,
  `orderstatus_status` int(11) NOT NULL,
  `orderstatus_created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_orderstatus`
--

INSERT INTO `tabqy_orderstatus` (`orderstatus_id`, `orderstatus_name`, `orderstatus_status`, `orderstatus_created`) VALUES
(2, 'Approved1', 1, '2018-04-07 14:58:46');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_ordertype`
--

CREATE TABLE `tabqy_ordertype` (
  `ordertype_id` int(11) NOT NULL,
  `ordertype_name` varchar(255) NOT NULL,
  `ordertype_orderby_id` int(11) NOT NULL,
  `ordertype_status` int(11) NOT NULL,
  `ordertype_created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_ordertype`
--

INSERT INTO `tabqy_ordertype` (`ordertype_id`, `ordertype_name`, `ordertype_orderby_id`, `ordertype_status`, `ordertype_created`) VALUES
(3, 'Pickup', 0, 1, '2018-04-03 16:35:08'),
(4, 'CRM', 3, 1, '2018-04-03 16:35:21'),
(5, 'Walkin', 3, 1, '2018-04-03 16:35:40'),
(6, 'Online Order', 3, 1, '2018-04-03 16:37:19'),
(7, 'on the way', 0, 1, '2018-04-07 14:56:00'),
(8, 'test', 3, 1, '2018-04-07 14:56:13');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_othersettings`
--

CREATE TABLE `tabqy_othersettings` (
  `othersettings_id` int(11) NOT NULL,
  `othersettings_key` varchar(255) NOT NULL,
  `othersettings_value` varchar(2000) NOT NULL,
  `othersettings_created` date NOT NULL,
  `othersettings_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_othersettings`
--

INSERT INTO `tabqy_othersettings` (`othersettings_id`, `othersettings_key`, `othersettings_value`, `othersettings_created`, `othersettings_updated`) VALUES
(1, 'site_logo', 'logo.png', '2018-06-01', '2018-06-01 11:26:21'),
(2, 'telephone', '258852556565465', '2018-06-01', '2018-06-01 11:26:05'),
(3, 'address', 'lorem ipsum', '2018-06-01', '2018-06-01 11:24:21'),
(4, 'plan_emails', 'priyanjali@askonlinesolutions.com,priyanjali.askonline@gmail.com,pravin@askonlinesolutions.com,shivank@askonlinesolutions.com', '2018-06-01', '2018-06-02 11:40:40'),
(5, 'first_rem_days', '30', '2018-06-01', '2018-06-01 12:00:49'),
(6, 'first_reminder_text', 'your subscription is near to expire.  To be connect with TABQY online system Please contact abc@tabqy.com or call to +99025155 to renewal your planâ€  after 30 days your plan will be expired.', '2018-06-01', '2018-06-01 11:55:11'),
(7, 'second_rem_days', '5', '2018-06-01', '2018-06-01 12:00:49'),
(8, 'second_reminder_text', 'your subscription is near to expire.  Please contact abc@tabqy.com or call to +99025155 to renewal your planâ€  after 5 days your plan will be expired.', '2018-06-01', '2018-06-01 11:55:11');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_page`
--

CREATE TABLE `tabqy_page` (
  `page_id` int(11) NOT NULL,
  `page_title` varchar(255) NOT NULL,
  `page_slug` varchar(255) NOT NULL,
  `page_desc` varchar(1000) NOT NULL,
  `page_banner_image` varchar(500) NOT NULL,
  `page_status` tinyint(4) NOT NULL,
  `page_meta_title` varchar(255) NOT NULL,
  `page_meta_key` varchar(255) NOT NULL,
  `page_meta_desc` varchar(500) NOT NULL,
  `page_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `page_last_updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_page`
--

INSERT INTO `tabqy_page` (`page_id`, `page_title`, `page_slug`, `page_desc`, `page_banner_image`, `page_status`, `page_meta_title`, `page_meta_key`, `page_meta_desc`, `page_created`, `page_last_updated`) VALUES
(3, 'About Us', 'about_us', '<p>lorem ipsum dolor sit amet</p>\r\n', 'Tulips.jpg', 1, 'lorem ipsum meta title', 'lorem ipsum meta key', '<p>page meta description lorem ipsum</p>\r\n', '2018-03-26 08:19:39', '2018-03-26 08:19:39'),
(4, 'Contact Us', 'contact_us', '<p>sadadsadas</p>\r\n', 'Penguins.jpg', 1, 'dasdasd', 'asdasd', '<p>asdasddas</p>\r\n', '2018-03-26 08:23:48', '2018-03-26 08:23:48'),
(5, 'Blogs', 'blogs', '<p>sdfsdfs</p>\r\n', 'Desert.jpg', 1, 'dfsdf', 'dsf', '<p>sdfdsf</p>\r\n', '2018-03-26 12:21:44', '2018-03-26 08:27:18');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_page_language`
--

CREATE TABLE `tabqy_page_language` (
  `page_language_id` int(11) NOT NULL,
  `page_language_page_id` int(11) NOT NULL,
  `page_language_language_code` varchar(255) NOT NULL,
  `page_language_page_title` text NOT NULL,
  `page_language_page_desc` text NOT NULL,
  `page_language_edit` enum('0','1') NOT NULL,
  `page_language_status` enum('0','1') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_paymentmethod`
--

CREATE TABLE `tabqy_paymentmethod` (
  `paymentmethod_id` int(11) NOT NULL,
  `paymentmethod_name` varchar(255) NOT NULL,
  `paymentmethod_status` int(11) NOT NULL,
  `paymentmethod_created` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_paymentmethod`
--

INSERT INTO `tabqy_paymentmethod` (`paymentmethod_id`, `paymentmethod_name`, `paymentmethod_status`, `paymentmethod_created`) VALUES
(5, 'test', 1, '2018-05-31 14:46:57'),
(3, 'Cash', 1, '2018-04-09 12:25:23'),
(4, 'Credit Card', 1, '2018-05-31 14:43:31');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_plan`
--

CREATE TABLE `tabqy_plan` (
  `plan_id` int(30) NOT NULL,
  `plan_name` varchar(120) CHARACTER SET utf8 NOT NULL,
  `plan_price` varchar(120) CHARACTER SET utf8 NOT NULL,
  `plan_description` text CHARACTER SET utf8 NOT NULL,
  `plan_status` enum('0','1') DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_plan`
--

INSERT INTO `tabqy_plan` (`plan_id`, `plan_name`, `plan_price`, `plan_description`, `plan_status`) VALUES
(10, 'custom plan', '70', 'lorem ipsum', '1'),
(11, 'gg', '678', 'ughjgh', '1'),
(12, 'ghfh', '67', 'ghg', '1'),
(13, 'demo', '25', 'test', '1'),
(14, 'ccd plan', '500', 'lorem ipsum ccd', '1'),
(15, 'Mr Brown plan', '900', 'lorem ipsum mr. brown', '1'),
(16, 'Plan Forever', '800', 'lorem ipsum', '1'),
(17, 'Super plan', '450', 'lorem ipsum', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_plan_detail`
--

CREATE TABLE `tabqy_plan_detail` (
  `plan_detail_id` int(30) NOT NULL,
  `plan_detail_plan_id` int(30) NOT NULL,
  `plan_detail_model_id` int(30) NOT NULL,
  `plan_detail_quantity` varchar(50) CHARACTER SET utf8 NOT NULL,
  `plan_detail_status` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_plan_detail`
--

INSERT INTO `tabqy_plan_detail` (`plan_detail_id`, `plan_detail_plan_id`, `plan_detail_model_id`, `plan_detail_quantity`, `plan_detail_status`) VALUES
(158, 11, 6, '2', '1'),
(159, 11, 7, '2', '1'),
(160, 11, 8, '2', '1'),
(161, 11, 9, '2', '1'),
(162, 11, 10, '2', '1'),
(163, 11, 11, '2', '1'),
(164, 11, 12, '2', '1'),
(165, 11, 13, '2', '1'),
(166, 11, 14, '2', '1'),
(167, 11, 15, '2', '1'),
(168, 11, 16, '2', '1'),
(169, 11, 17, '2', '1'),
(170, 11, 18, '2', '1'),
(171, 11, 19, '2', '1'),
(172, 11, 20, '2', '1'),
(173, 10, 6, '20', '1'),
(174, 10, 7, '22', '1'),
(175, 10, 8, '0', '1'),
(176, 10, 9, '0', '1'),
(177, 10, 10, '0', '1'),
(178, 10, 11, '0', '1'),
(179, 10, 12, '0', '1'),
(180, 10, 13, '0', '1'),
(181, 10, 14, '0', '1'),
(182, 10, 15, '0', '1'),
(183, 10, 16, '0', '1'),
(184, 10, 17, '0', '1'),
(185, 10, 18, '0', '1'),
(186, 10, 19, '0', '1'),
(187, 10, 20, '0', '1'),
(188, 14, 6, '4', '1'),
(189, 14, 7, '5', '1'),
(190, 15, 6, '6', '1'),
(191, 15, 7, '7', '1'),
(192, 16, 8, '3', '1'),
(193, 16, 9, '4', '1'),
(194, 16, 10, '5', '1'),
(195, 16, 19, '5', '1'),
(196, 16, 20, '5', '1'),
(197, 17, 6, '5', '1'),
(198, 17, 7, '6', '1'),
(199, 17, 8, '8', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_plan_model`
--

CREATE TABLE `tabqy_plan_model` (
  `plan_model_id` int(30) NOT NULL,
  `plan_model_name` varchar(120) CHARACTER SET utf8 NOT NULL,
  `plan_model_quantity` varchar(100) CHARACTER SET utf8 NOT NULL,
  `plan_model_price` varchar(100) CHARACTER SET utf8 NOT NULL,
  `plan_model_status` enum('0','1') NOT NULL DEFAULT '1',
  `plan_model_type` enum('0','1') DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_plan_model`
--

INSERT INTO `tabqy_plan_model` (`plan_model_id`, `plan_model_name`, `plan_model_quantity`, `plan_model_price`, `plan_model_status`, `plan_model_type`) VALUES
(6, 'Dashboard', '11', '88', '1', '0'),
(7, 'Walk-in Order', '77', '77', '1', '0'),
(8, 'CMR Order', '12', '1800', '1', '0'),
(9, 'Table Order', '100', '500', '1', '0'),
(10, 'Online Order', '', '555', '1', '0'),
(11, 'Order Statement', '', '', '1', '0'),
(12, 'Restaurant Admin', '55', '56', '1', '0'),
(13, 'Charity Modules  (free)', '', '', '1', '0'),
(14, 'Owner Modules', '', '', '1', '0'),
(15, 'Kitchen Modules', '', '', '1', '0'),
(16, 'Display module', '', '', '1', '0'),
(17, 'Waiter Modules', '', '', '1', '0'),
(18, 'Owner Drivers ', '', '13', '1', '0'),
(19, 'TABQY Drivers ', '', '', '1', '0'),
(20, 'Third party Drives', '44', '', '1', '0');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_quantity`
--

CREATE TABLE `tabqy_quantity` (
  `quantity_id` int(11) NOT NULL,
  `quantity_name` varchar(255) NOT NULL,
  `quantity_status` int(11) NOT NULL,
  `quantity_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_quantity`
--

INSERT INTO `tabqy_quantity` (`quantity_id`, `quantity_name`, `quantity_status`, `quantity_created`) VALUES
(1, 'Full', 1, '2018-04-19 13:34:57'),
(2, 'Half', 1, '2018-03-21 12:36:15'),
(3, '100ml', 1, '2018-04-09 05:09:07'),
(4, '500ml', 1, '2018-04-18 13:40:37');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_regions`
--

CREATE TABLE `tabqy_regions` (
  `region_id` int(11) NOT NULL,
  `country_code` varchar(255) NOT NULL,
  `city_code` varchar(255) NOT NULL,
  `region_name` varchar(255) NOT NULL,
  `region_code` varchar(255) NOT NULL,
  `region_status` int(11) NOT NULL,
  `region_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_regions`
--

INSERT INTO `tabqy_regions` (`region_id`, `country_code`, `city_code`, `region_name`, `region_code`, `region_status`, `region_created`) VALUES
(1, 'IN', 'AGR,CHD,DL,JPR,KNPR,LUK,NOI', 'North India', 'NOIND', 1, '2018-05-03 16:33:29'),
(2, 'IN', 'AHMD,MUM,NAG,PNE,SUR,ALD,GOA', 'West India', 'WEIND', 1, '2018-05-03 07:05:23'),
(3, 'IN', 'BRLY', 'EAST India', 'ESTIND', 1, '2018-05-03 16:34:55'),
(4, 'AUs', 'BRIS,MELB,PER,SYD', 'East Australia', 'ESTAUS', 1, '2018-05-03 16:35:51'),
(5, 'SAU', 'DAMM,RYDH', 'North Saudi', 'NOSAU', 1, '2018-05-22 17:04:26'),
(6, 'SAU', 'JUBAIL,MECA', 'West saudi en', 'WESTSAU', 1, '2018-05-29 11:58:26');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_regions_language`
--

CREATE TABLE `tabqy_regions_language` (
  `region_language_id` int(20) NOT NULL,
  `region_language_region_id` int(20) NOT NULL,
  `region_language_region_name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `region_language_language_code` char(8) CHARACTER SET utf8 NOT NULL,
  `region_language_edit` enum('0','1') CHARACTER SET utf8 NOT NULL,
  `region_language_status` enum('0','1') CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_regions_language`
--

INSERT INTO `tabqy_regions_language` (`region_language_id`, `region_language_region_id`, `region_language_region_name`, `region_language_language_code`, `region_language_edit`, `region_language_status`) VALUES
(9, 1, 'North India', 'ar', '0', '1'),
(10, 1, 'North India', 'en', '1', '1'),
(11, 2, 'West India', 'ar', '0', '1'),
(12, 2, 'West India', 'en', '1', '1'),
(13, 3, 'EAST India', 'ar', '0', '1'),
(14, 3, 'EAST India', 'en', '1', '1'),
(15, 4, 'East Australia', 'ar', '0', '1'),
(16, 4, 'East Australia', 'en', '1', '1'),
(17, 5, 'North Saudi', 'ar', '0', '1'),
(18, 5, 'North Saudi', 'en', '1', '1'),
(19, 6, 'West saudi ar', 'ar', '1', '1'),
(20, 6, 'West saudi en', 'en', '1', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_resturantbrand`
--

CREATE TABLE `tabqy_resturantbrand` (
  `resturantbrand_id` int(20) NOT NULL,
  `resturantbrand_type` int(11) NOT NULL,
  `resturantbrand_company_id` int(11) NOT NULL,
  `resturantbrand_name` varchar(255) NOT NULL,
  `resturantbrand_displayname` varchar(255) NOT NULL,
  `resturantbrand_country` varchar(255) NOT NULL,
  `resturantbrand_region` varchar(255) NOT NULL,
  `resturantbrand_city` varchar(255) NOT NULL,
  `resturantbrand_zone` varchar(255) NOT NULL,
  `resturantbrand_location` varchar(255) NOT NULL,
  `resturantbrand_address` text NOT NULL,
  `resturantbrand_email` varchar(255) NOT NULL,
  `resturantbrand_phoneno` varchar(255) NOT NULL,
  `resturantbrand_cuisine` int(11) NOT NULL,
  `resturantbrand_countusers` varchar(255) NOT NULL,
  `resturantbrand_countpos` varchar(255) NOT NULL,
  `resturantbrand_file` varchar(255) NOT NULL,
  `resturantbrand_created` datetime NOT NULL,
  `resturantbrand_status` enum('0','1') NOT NULL DEFAULT '1' COMMENT '0-deactive,1-active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tabqy_resturantbrand`
--

INSERT INTO `tabqy_resturantbrand` (`resturantbrand_id`, `resturantbrand_type`, `resturantbrand_company_id`, `resturantbrand_name`, `resturantbrand_displayname`, `resturantbrand_country`, `resturantbrand_region`, `resturantbrand_city`, `resturantbrand_zone`, `resturantbrand_location`, `resturantbrand_address`, `resturantbrand_email`, `resturantbrand_phoneno`, `resturantbrand_cuisine`, `resturantbrand_countusers`, `resturantbrand_countpos`, `resturantbrand_file`, `resturantbrand_created`, `resturantbrand_status`) VALUES
(4, 0, 3, 'new', 'nwe', 'AUs', 'ESTAUS', 'BRIS', 'BRISSOU', 'ALXHL', 'kjfhjkhjk', 'new56@gmail.com', '4654654', 0, '8', '9', '', '2018-05-03 10:04:53', '1'),
(7, 0, 3, 'oooo', 'oooo', 'IN', 'NOIND', 'NOI', 'STH', 'SEC18', 'fdjhh', 'oooo@gmail.com', '45455546546', 0, '5', '6', '1525343715_Lighthouse.jpg', '2018-05-03 10:35:15', '1'),
(10, 0, 2, 'bdcb', 'cbcb', 'AUs', 'ESTAUS', 'BRIS', 'BRISSOU', 'ALXHL', '45646', 'xcbcxb@fghfgh.fg', '5464646', 0, '56', '5', '1525416915_1519907303438943_126358a162b168ea25364109c7c04d9d.jpg', '2018-05-04 06:55:15', '1'),
(11, 4, 3, 'xcvf', 'dfgdg', 'AUs', 'ESTAUS', 'BRIS', 'BRISSOU', 'ALXHL', 'dfgdg', 'dgfdg@dgfhf.df', '45646', 0, '', '', '1525418747_508_error.jpg', '2018-05-04 07:25:47', '1'),
(12, 10, 2, 'Salumanya', 'Salmaniya', 'IN', 'NOIND', 'DL', 'STHDL', 'DFCOL', 'sss', 'salumanya@gmail.com', '0254587', 0, '', '', '1525691780_Twitter_logo_bird_transparent_png.png', '2018-05-07 11:16:20', '1'),
(13, 0, 10, 'Brand 1', 'Brand 1', 'SAU', 'NOSAU', 'RYDH', 'ZONE1RYD', 'ALOLAYA', 'dssdffds', 'brand1@gmail.com', '489498476', 0, '5', '6', '1526991258_brewmaster.jpg', '2018-05-22 12:14:18', '1'),
(14, 0, 11, 'brandtesten', 'brandtest', 'SAU', 'NOSAU', 'RYDH', 'ZONE1RYD', 'ALOLAYA', 'noida', 'brandtest@gmail.com', '123465678', 0, '123', '123', '1526992279_user-image-with-black-background_318-34564.jpg', '2018-05-22 12:31:19', '1'),
(15, 14, 11, 'branchtest en', 'branchtest', 'SAU', 'NOSAU', 'RYDH', 'ZONE1RYD', 'ALSAHAFA', 'noida', 'branchtest@gmail.comn', '1234567', 0, '', '', '1526992394_user-image-with-black-background_318-34564.jpg', '2018-05-22 12:33:14', '1'),
(16, 0, 2, 'KFC en', 'KFC', 'SAU', 'NOSAU', 'RYDH', 'ZONE1RYD', 'ALOLAYA', 'Al, Olaya, Riyadh', 'kfc@gmail.com', '05128475', 1, '12', '5', '1527061615_1200px-KFC_logo.svg.png', '2018-05-23 07:46:55', '1'),
(17, 0, 2, 'Saudi Brand 1 en', 'Saudi brand 1 en', 'SAU', 'NOSAU', 'RYDH', 'ZONE1RYD', 'ALSAHAFA', 'sadsadsad enn', 'saudibrand1@gmail.com', '541564561', 0, '312', '412', '1527484368_Lighthouse.jpg', '2018-05-28 05:12:48', '1'),
(18, 0, 4, 'MnH company Brand', 'MnH company Brand', 'IN', 'NOIND', 'NOI', 'NONR', 'SEC16', 'noida', 'mnhbrand@ask.com', '9636897526', 1, '100', '101', '1527508181_user-image-with-black-background_318-34564.jpg', '2018-05-28 11:49:41', '0'),
(19, 18, 4, 'MnH Branch', 'MnH Branch', 'IN', 'NOIND', 'DL', 'EDL', 'PRTVR', 'noida', 'mnhbranch@ask.com', '9632587412', 0, '', '', '1527510161_user-image-with-black-background_318-34564.jpg', '2018-05-28 12:22:41', '1'),
(20, 18, 4, 'branchtest', 'branchtest', 'IN', 'NOIND', 'DL', 'newzone', '123456', 'noida', 'mnhbranchtest@gmail.com', '96325874125', 0, '', '', '1527511103_user-image-with-black-background_318-34564.jpg', '2018-05-28 12:38:23', '1'),
(21, 0, 14, 'red chili brand', 'red chili brand', 'IN', 'NOIND', 'DL', 'WDL', 'KRTNAG', 'gfffg', 'admin@redchilli.com', '45454545', 1, '7', '5', '1527598203_Penguins.jpg', '2018-05-29 12:50:03', '1'),
(22, 0, 12, 'ghhghg', 'hghggh', 'SAU', 'NOSAU', 'RYDH', 'ZONE2RYD', 'KINGFAI', 'dfffdfd', 'hgh@gmail.com', '55455', 1, '44', '40', '1527598381_Koala.jpg', '2018-05-29 12:53:01', '1'),
(23, 0, 11, 'testbrand', 'testbrand', 'SAU', 'undefined', 'RYDH', 'undefined', 'ALSAHAFA', 'dfdsf', 'testbrand@gmail.com', '9897340789', 1, 'undefined', 'undefined', '1527774010_1523430729_Pizza_Hut_2012_logo.png', '2018-05-31 13:40:10', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_resturantbrand_language`
--

CREATE TABLE `tabqy_resturantbrand_language` (
  `resturantbrand_language_id` int(10) NOT NULL,
  `resturantbrand_language_resturantbrand_id` int(10) NOT NULL,
  `resturantbrand_language_language_code` char(3) NOT NULL,
  `resturantbrand_language_name` varchar(255) NOT NULL,
  `resturantbrand_language_displayname` varchar(255) NOT NULL,
  `resturantbrand_language_address` text NOT NULL,
  `resturantbrand_language_edit` enum('0','1') NOT NULL COMMENT '1-edited,0-not',
  `resturantbrand_language_status` enum('0','1') NOT NULL COMMENT '0-inactive,1-active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tabqy_resturantbrand_language`
--

INSERT INTO `tabqy_resturantbrand_language` (`resturantbrand_language_id`, `resturantbrand_language_resturantbrand_id`, `resturantbrand_language_language_code`, `resturantbrand_language_name`, `resturantbrand_language_displayname`, `resturantbrand_language_address`, `resturantbrand_language_edit`, `resturantbrand_language_status`) VALUES
(7, 4, 'ar', 'new', 'nwe', 'kjfhjkhjk', '0', '1'),
(8, 4, 'en', 'new', 'nwe', 'kjfhjkhjk', '1', '1'),
(13, 7, 'ar', 'oooo', 'oooo', 'fdjhh', '0', '1'),
(14, 7, 'en', 'oooo', 'oooo', 'fdjhh', '1', '1'),
(19, 10, 'ar', 'bdcb', 'cbcb', '45646', '0', '1'),
(20, 10, 'en', 'bdcb', 'cbcb', '45646', '1', '1'),
(21, 11, 'ar', 'xcvf', 'dfgdg', 'dfgdg', '0', '1'),
(22, 11, 'en', 'xcvf', 'dfgdg', 'dfgdg', '1', '1'),
(23, 12, 'ar', 'Salumanya', 'Salmaniya', 'sss', '0', '1'),
(24, 12, 'en', 'Salumanya', 'Salmaniya', 'sss', '1', '1'),
(25, 13, 'ar', 'Brand 1', 'Brand 1', 'dssdffds', '0', '1'),
(26, 13, 'en', 'Brand 1', 'Brand 1', 'dssdffds', '1', '1'),
(27, 14, 'ar', 'brandtestarabic', 'brandtest', 'noida', '1', '1'),
(28, 14, 'en', 'brandtesten', 'brandtest', 'noida', '1', '1'),
(29, 15, 'ar', 'branchtest ar', 'branchtest', 'noida', '1', '1'),
(30, 15, 'en', 'branchtest en', 'branchtest', 'noida', '1', '1'),
(31, 16, 'ar', 'KFC', 'KFC', 'Al, Olaya, Riyadh', '0', '1'),
(32, 16, 'en', 'KFC en', 'KFC', 'Al, Olaya, Riyadh', '1', '1'),
(33, 17, 'ar', 'Saudi Brand 1 ar', 'Saudi brand 1 ar', 'sadsadsad ar', '1', '1'),
(34, 17, 'en', 'Saudi Brand 1 en', 'Saudi brand 1 en', 'sadsadsad enn', '1', '1'),
(35, 18, 'ar', 'MnH company BrandAR', 'MnH company Brand', 'noida', '1', '1'),
(36, 18, 'en', 'MnH company Brand', 'MnH company Brand', 'noida', '1', '1'),
(37, 19, 'ar', 'MnH Branch', 'MnH Branch', 'noida', '0', '1'),
(38, 19, 'en', 'MnH Branch', 'MnH Branch', 'noida', '1', '1'),
(39, 20, 'ar', 'branchtest', 'branchtest', 'noida', '0', '1'),
(40, 20, 'en', 'branchtest', 'branchtest', 'noida', '1', '1'),
(41, 21, 'ar', 'red chili brand', 'red chili brand', 'gfffg', '0', '1'),
(42, 21, 'en', 'red chili brand', 'red chili brand', 'gfffg', '1', '1'),
(43, 22, 'ar', 'ghhghg', 'hghggh', 'dfffdfd', '0', '1'),
(44, 22, 'en', 'ghhghg', 'hghggh', 'dfffdfd', '1', '1'),
(45, 23, 'ar', 'testbrand', 'testbrand', 'dfdsf', '0', '1'),
(46, 23, 'en', 'testbrand', 'testbrand', 'dfdsf', '1', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_resuser_access`
--

CREATE TABLE `tabqy_resuser_access` (
  `res_access_id` int(11) NOT NULL,
  `res_user_id` int(11) NOT NULL,
  `res_company_id` varchar(255) NOT NULL,
  `res_brand_ids` varchar(255) NOT NULL,
  `res_access_status` int(11) NOT NULL,
  `res_assign_date` date NOT NULL,
  `res_last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_resuser_access`
--

INSERT INTO `tabqy_resuser_access` (`res_access_id`, `res_user_id`, `res_company_id`, `res_brand_ids`, `res_access_status`, `res_assign_date`, `res_last_updated`) VALUES
(1, 87, '2', '16', 1, '2018-05-24', '2018-05-24 07:32:36'),
(2, 88, '2', '16', 1, '2018-05-25', '2018-05-25 10:09:14'),
(3, 89, '2', '10', 1, '2018-05-25', '2018-05-25 13:49:45'),
(4, 92, '4', '18', 1, '2018-05-28', '2018-05-28 11:51:44'),
(5, 94, '4', '18', 1, '2018-05-29', '2018-05-29 12:06:59'),
(6, 98, '12', '22', 1, '2018-06-01', '2018-06-01 13:56:14');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_setpoint`
--

CREATE TABLE `tabqy_setpoint` (
  `setpoint_id` int(11) NOT NULL,
  `setpoint_country_id` varchar(255) NOT NULL,
  `setpoint_percent` int(11) NOT NULL,
  `setpoint_value` decimal(12,2) NOT NULL,
  `setpoint_resturant_id` int(11) NOT NULL,
  `setpoint_status` int(11) NOT NULL,
  `setpoint_created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_setpoint`
--

INSERT INTO `tabqy_setpoint` (`setpoint_id`, `setpoint_country_id`, `setpoint_percent`, `setpoint_value`, `setpoint_resturant_id`, `setpoint_status`, `setpoint_created`) VALUES
(3, '1', 100, '10.00', 11, 1, '2018-03-29 07:11:56'),
(5, '3', 45, '55.00', 11, 1, '2018-04-04 08:28:44'),
(6, '3', 54, '67.00', 11, 1, '2018-04-04 08:28:55'),
(7, 'BH', 454, '4.00', 0, 1, '2018-04-07 14:44:40'),
(8, 'IN', 500000, '1000000.56', 0, 1, '2018-04-07 15:54:38'),
(10, 'SAU', 500000, '2000000.26', 0, 1, '2018-04-07 15:57:13'),
(11, 'IN', 10, '0.50', 0, 1, '2018-04-09 18:12:22'),
(12, 'SAU', 77, '77.00', 0, 1, '2018-04-18 13:43:25');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_superadmin`
--

CREATE TABLE `tabqy_superadmin` (
  `superadmin_id` int(10) NOT NULL,
  `superadmin_company_id` int(10) NOT NULL,
  `superadmin_brand_id` int(10) NOT NULL,
  `superadmin_branch_id` int(10) NOT NULL,
  `superadmin_role_id` int(10) NOT NULL,
  `superadmin_status` enum('0','1') DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tabqy_superadmin`
--

INSERT INTO `tabqy_superadmin` (`superadmin_id`, `superadmin_company_id`, `superadmin_brand_id`, `superadmin_branch_id`, `superadmin_role_id`, `superadmin_status`) VALUES
(1, 17, 0, 0, 2, '1'),
(2, 17, 0, 0, 3, '0'),
(3, 1, 0, 0, 2, '1'),
(4, 2, 0, 0, 3, '0'),
(5, 2, 0, 0, 2, '0');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_table`
--

CREATE TABLE `tabqy_table` (
  `table_id` int(11) NOT NULL,
  `table_floor_id` int(11) NOT NULL,
  `table_company_id` int(11) NOT NULL,
  `table_resturant_id` int(11) NOT NULL,
  `table_branch_id` varchar(255) NOT NULL,
  `table_sheet` int(20) NOT NULL,
  `table_status` int(11) NOT NULL,
  `table_created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_table`
--

INSERT INTO `tabqy_table` (`table_id`, `table_floor_id`, `table_company_id`, `table_resturant_id`, `table_branch_id`, `table_sheet`, `table_status`, `table_created`) VALUES
(17, 1, 4, 18, '20', 5, 1, '2018-05-29 17:30:36');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_taxes`
--

CREATE TABLE `tabqy_taxes` (
  `tax_id` int(11) NOT NULL,
  `tax_name` varchar(255) NOT NULL,
  `tax_percent` int(11) NOT NULL,
  `tax_price` int(15) NOT NULL,
  `tax_reg_no` varchar(255) NOT NULL,
  `tax_company_id` int(11) NOT NULL,
  `tax_country` varchar(255) NOT NULL,
  `tax_resturant_id` int(11) NOT NULL,
  `taxes_set_admin` int(11) NOT NULL,
  `tax_status` int(11) NOT NULL,
  `tax_created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_taxes`
--

INSERT INTO `tabqy_taxes` (`tax_id`, `tax_name`, `tax_percent`, `tax_price`, `tax_reg_no`, `tax_company_id`, `tax_country`, `tax_resturant_id`, `taxes_set_admin`, `tax_status`, `tax_created`) VALUES
(3, 'weweeee1', 66, 0, '34534534535', 1, 'IN', 0, 0, 0, '2018-05-02 09:56:43'),
(4, 'weweeee1', 66, 0, '34534534535', 1, 'IN', 0, 0, 1, '2018-05-02 09:56:57'),
(7, 'MnH Tax', 20, 0, 'MnH123', 4, 'IN', 0, 0, 0, '2018-05-28 11:42:51'),
(8, 'MnH Tax', 25, 0, 'MnH123', 4, 'IN', 0, 0, 1, '2018-05-28 11:43:14'),
(9, 'Saudi Tax', 8, 0, '12345658798', 2, 'SAU', 0, 0, 0, '2018-05-28 11:45:23'),
(10, 'Saudi Tax1', 12, 0, '123456587981', 2, 'SAU', 0, 0, 0, '2018-05-28 11:45:40'),
(11, 'Saudi Tax new', 22, 0, '12345', 2, 'SAU', 0, 0, 0, '2018-05-28 11:46:17'),
(12, 'vat', 5, 0, '00112', 2, 'SAU', 0, 0, 1, '2018-05-29 12:25:13'),
(13, 'sauditax', 2, 25, '123', 11, 'SAU', 0, 0, 0, '2018-06-01 13:23:15'),
(14, 'sauditax', 2, 2, '123', 11, 'SAU', 0, 0, 1, '2018-06-01 13:23:26'),
(15, 'VAT', 5, 15, '512548755522', 12, 'SAU', 0, 0, 0, '2018-06-02 10:02:22'),
(16, 'VAT', 5, 20, '512548755522', 12, 'SAU', 0, 0, 1, '2018-06-04 13:05:06');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_user`
--

CREATE TABLE `tabqy_user` (
  `user_id` int(11) NOT NULL,
  `user_company_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_gender` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_username` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_phoneno` varchar(255) NOT NULL,
  `user_loginkey` varchar(255) NOT NULL,
  `user_role` int(11) NOT NULL,
  `user_staffrole` int(11) NOT NULL,
  `user_dob` date NOT NULL,
  `user_doa` date NOT NULL,
  `user_address` text NOT NULL,
  `user_city` varchar(255) NOT NULL,
  `user_zipcode` varchar(255) NOT NULL,
  `user_restaurant_id` int(11) NOT NULL,
  `user_department_id` int(11) NOT NULL,
  `user_default_country` varchar(255) NOT NULL,
  `user_status` tinyint(11) NOT NULL DEFAULT '1',
  `user_created` datetime NOT NULL,
  `user_created_by` varchar(255) NOT NULL,
  `user_lastlogin` datetime NOT NULL,
  `user_lastpasswordupdate` datetime NOT NULL,
  `user_updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_user`
--

INSERT INTO `tabqy_user` (`user_id`, `user_company_id`, `user_name`, `user_gender`, `user_email`, `user_username`, `user_password`, `user_phoneno`, `user_loginkey`, `user_role`, `user_staffrole`, `user_dob`, `user_doa`, `user_address`, `user_city`, `user_zipcode`, `user_restaurant_id`, `user_department_id`, `user_default_country`, `user_status`, `user_created`, `user_created_by`, `user_lastlogin`, `user_lastpasswordupdate`, `user_updated`) VALUES
(1, 2, 'Saleem22', 'Male', 'developertest62@gmail.com', 'superadmin', '2yvFJsGDNnvAo', '1222222', '', 0, 0, '0000-00-00', '0000-00-00', 'test add', '1222222', '1111111', 0, 0, 'SAU', 1, '2018-02-21 12:55:41', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(60, 2, 'Test 121', '', 'test121@gmail.com', 'test121', '2yvFJsGDNnvAo', '54545455445', '', 2, 0, '0000-00-00', '0000-00-00', 'lorem ipsum dolor sit amet', 'Riyadh', '123456', 0, 0, '', 1, '2018-05-03 08:29:17', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(61, 3, 'Admin', 'Female', 'intcfood@gmail.com', 'intcfood', '2yyLTglmKlbjA', '654654654', '', 1, 4, '2018-05-02', '0000-00-00', 'sdjfghjg', 'gjgjhgjh', '878978997', 0, 0, '', 1, '2018-05-03 09:10:54', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(62, 3, '', '', 'mrbrown@gmail.com', 'mrbrown', '2yn7Cq7alBMvQ', '87987979', '', 4, 0, '0000-00-00', '0000-00-00', 'Lorem ipsum', '', '', 1, 0, '', 1, '2018-05-03 09:39:40', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(63, 3, '', '', 'gergeer@gmail.com', 'gergeer', '2yjSYHRN21Dus', '84798', '', 4, 0, '0000-00-00', '0000-00-00', 'jkfjgfjhfg', '', '', 2, 0, '', 1, '2018-05-03 09:50:49', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(64, 3, '', '', 'gddfd@gmail.com', 'gddfd', '2yW83wPwYgx3g', '89789798', '', 4, 0, '0000-00-00', '0000-00-00', '489798789', '', '', 3, 0, '', 1, '2018-05-03 09:52:14', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(65, 3, '', '', 'new56@gmail.com', 'new56', '2yFzFfNGGhkMg', '4654654', '', 4, 0, '0000-00-00', '0000-00-00', 'kjfhjkhjk', '', '', 4, 0, '', 1, '2018-05-03 10:04:53', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(66, 3, '', '', 'ww@SSS.COM', 'ww', '2yq2cnZ8Sv5sA', '545454', '', 4, 0, '0000-00-00', '0000-00-00', 'JSDGJHGA', '', '', 5, 0, '', 1, '2018-05-03 10:14:06', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(67, 3, '', '', 'FHGF@FHFG.JHG', 'FHGF', '2ydUi/WBu6qZ6', '565576567', '', 4, 0, '0000-00-00', '0000-00-00', 'HGGHGGJHG', '', '', 6, 0, '', 1, '2018-05-03 10:17:29', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(68, 3, '', '', 'oooo@gmail.com', 'oooo', '2yEzWru8zMxxQ', '45455546546', '', 4, 0, '0000-00-00', '0000-00-00', 'fdjhh', '', '', 7, 0, '', 1, '2018-05-03 10:35:15', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(69, 3, '', '', 'tttt@gmail.com', 'tttt', '2yZOwAOJuqHA.', '5545465', '', 4, 0, '0000-00-00', '0000-00-00', 'fdhgfdkjh', '', '', 8, 0, '', 1, '2018-05-03 10:38:32', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(70, 3, '54gvhgfh', 'Female', 'ykhkj@gmail.com', 'ykhkj', '2yjY6h1VxF.V6', '654654654', '', 1, 3, '2018-05-01', '0000-00-00', 'gfhfghgfh', 'cbcvb', '6456456', 9, 0, '', 1, '2018-05-03 10:39:33', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(71, 4, '', '', 'info@askonlinesolutions.com', 'info', '2yaW0J7nFLb3w', '9910017727', '', 2, 0, '0000-00-00', '0000-00-00', '', '', '', 0, 0, '', 1, '2018-05-03 12:44:03', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(72, 5, '', '', 'supercompany@gmail.com', 'supercompany', '2ynKgxUccPrSI', '79879879', '', 2, 0, '0000-00-00', '0000-00-00', '', '', '', 0, 0, '', 1, '2018-05-03 13:42:38', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(73, 6, '', '', 'shalu.askonline@gmail.com', 'shalu.askonline', '2yl7NEEcZYR/s', '8005462213', '', 2, 0, '0000-00-00', '0000-00-00', '', '', '', 0, 0, '', 1, '2018-05-04 05:30:56', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(74, 7, '', '', 'fgfc@dfg.ffg', 'fgfc', '2yJxIlCNaVYrY', '567567567', '', 2, 0, '0000-00-00', '0000-00-00', '', '', '', 0, 0, '', 1, '2018-05-04 05:44:00', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(76, 0, '', '', 'dgfdg@dgfhf.df', 'dgfdg', '2yiQBAe29rhdY', '45646', '', 4, 0, '0000-00-00', '0000-00-00', 'dfgdg', '', '', 11, 0, '', 1, '2018-05-04 07:25:47', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(77, 8, '', '', 'tabqy@gmail.com', 'tabqy', '2yBq0JEJ1i2nA', '3453445345', '', 2, 0, '0000-00-00', '0000-00-00', '', '', '', 0, 0, '', 1, '2018-05-07 11:15:29', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(78, 2, '', '', 'salumanya@gmail.com', 'salumanya', '2y1fHIDYTp0W2', '0254587', '', 4, 0, '0000-00-00', '0000-00-00', 'sss', '', '', 12, 0, '', 1, '2018-05-07 11:16:20', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(79, 9, '', '', 'ass@gmail.com', 'ass', '2yEs8BLybHTy.', '258458745', '', 2, 0, '0000-00-00', '0000-00-00', '', '', '', 0, 0, '', 1, '2018-05-09 08:09:08', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(80, 0, 'testcompany', 'Male', 'testcompany@gmail.com', 'testcompany', '2yvFJsGDNnvAo', '32323232323', '', 1, 2, '2018-05-15', '0000-00-00', 'noida', 'noida', '1232456', 0, 0, '', 1, '2018-05-22 06:13:04', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(81, 0, 'testlocation', 'Male', 'testlocation@gmail.com', 'testlocation', '2yvFJsGDNnvAo', '123456789', '', 1, 2, '2018-05-16', '0000-00-00', 'noida', 'noida', '1213456', 0, 0, '', 1, '2018-05-22 06:20:55', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(82, 10, '', '', 'globalfoodco@gmail.com', 'globalfood', '2yvFJsGDNnvAo', '564748564654', '', 2, 0, '0000-00-00', '0000-00-00', '', '', '', 0, 0, '', 1, '2018-05-22 07:14:30', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(83, 10, '', '', 'brand1@gmail.com', 'brand1', '2ywL2CzxLO0js', '489498476', '', 4, 0, '0000-00-00', '0000-00-00', 'dssdffds', '', '', 13, 0, '', 1, '2018-05-22 12:14:18', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(84, 11, '', '', 'companytest@gmail.com', 'companytest', '2yXAS3ity9NOA', '123456789', '', 2, 0, '0000-00-00', '0000-00-00', '', '', '', 0, 0, '', 1, '2018-05-22 12:29:17', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(85, 11, '', '', 'brandtest@gmail.com', 'brandtest', '2y1kuz9FzOmNw', '123465678', '', 4, 0, '0000-00-00', '0000-00-00', 'noida', '', '', 14, 0, '', 1, '2018-05-22 12:31:19', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(86, 11, '', '', 'branchtest@gmail.comn', 'branchtest', '2yXzyj51pBdQ6', '1234567', '', 5, 0, '0000-00-00', '0000-00-00', 'noida', '', '', 15, 0, '', 1, '2018-05-22 12:33:14', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(87, 2, '', '', 'kfc@gmail.com', 'kfc', '2ysanSp3CBceE', '05128475', '', 4, 0, '0000-00-00', '0000-00-00', 'Al, Olaya, Riyadh', '', '', 16, 0, '', 1, '2018-05-23 07:46:55', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(88, 2, 'Driver User', 'Female', 'driveruser@gmail.com', 'driveruser', '2yvFJsGDNnvAo', '78945612365', '', 3, 3, '2018-05-04', '0000-00-00', 'gjhgjh', 'hjgjhghj', '5456465', 0, 0, '', 1, '2018-05-25 10:08:52', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(89, 2, 'Manager User', 'Female', 'manageruser@gmail.com', 'manageruser', '2yvFJsGDNnvAo', '654654561', '', 3, 2, '2003-05-21', '0000-00-00', 'Address of manager user', 'noida', '111111', 0, 0, '', 1, '2018-05-25 10:14:01', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(90, 2, '', '', 'saudibrand1@gmail.com', 'saudibrand1', '2yksCJ40.gjDY', '54156456', '', 4, 0, '0000-00-00', '0000-00-00', 'sadsadsad', '', '', 17, 0, '', 1, '2018-05-28 05:12:48', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(91, 12, '', '', 'test125@gmail.com', 'test125', '2yvFJsGDNnvAo', '146546546', '', 2, 0, '0000-00-00', '0000-00-00', '', '', '', 0, 0, '', 1, '2018-05-28 05:25:53', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(92, 4, '', '', 'mnhbrand@ask.com', 'mnhbrand', '2yz4.iCSZNze2', '9636897526', '', 4, 0, '0000-00-00', '0000-00-00', 'noida', '', '', 18, 0, '', 1, '2018-05-28 11:49:41', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(93, 4, '', '', 'mnhbranch@ask.com', 'mnhbranch', '2yRg/M5QOFGz.', '9632587412', '', 5, 0, '0000-00-00', '0000-00-00', 'noida', '', '', 19, 0, '', 1, '2018-05-28 12:22:41', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(94, 4, '', '', 'mnhbranchtest@gmail.com', 'mnhbranchtest', '2yiaZNKW2vHXI', '96325874125', '', 5, 0, '0000-00-00', '0000-00-00', 'noida', '', '', 20, 0, '', 1, '2018-05-28 12:38:23', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(95, 13, '', '', 'ask@gmail.com', 'ask', '2yf3FGLvoJBHE', '12345678', '', 2, 0, '0000-00-00', '0000-00-00', '', '', '', 0, 0, '', 1, '2018-05-29 09:30:33', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(96, 14, '', '', 'redchilli@gmail.com', 'redchilli', '2yXAS3ity9NOA', '4545454545', '', 2, 0, '0000-00-00', '0000-00-00', '', '', '', 0, 0, '', 1, '2018-05-29 12:41:39', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(97, 14, '', '', 'admin@redchilli.com', 'admin', '2yILovJ8rvhdw', '45454545', '', 4, 0, '0000-00-00', '0000-00-00', 'gfffg', '', '', 21, 0, '', 1, '2018-05-29 12:50:03', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(98, 12, '', '', 'hgh@gmail.com', 'hgh', '2y1yqDgRzCfBs', '55455', '', 4, 0, '0000-00-00', '0000-00-00', 'dfffdfd', '', '', 22, 0, '', 1, '2018-05-29 12:53:01', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(99, 11, '', '', 'testbrand@gmail.com', 'testbrand', '2ytrqmg5RlWiE', '9897340789', '', 4, 0, '0000-00-00', '0000-00-00', 'dfdsf', '', '', 23, 0, '', 1, '2018-05-31 13:40:10', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_userlocations`
--

CREATE TABLE `tabqy_userlocations` (
  `userlocation_id` int(11) NOT NULL,
  `userlocation_user_id` int(11) NOT NULL,
  `userlocation_country` varchar(255) NOT NULL,
  `userlocation_company_id` int(11) NOT NULL,
  `userlocation_region` varchar(255) NOT NULL,
  `userlocation_city` varchar(255) NOT NULL,
  `userlocation_zone` varchar(255) NOT NULL,
  `userlocation_location` varchar(255) NOT NULL,
  `userlocation_status` int(11) NOT NULL,
  `userlocation_created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_userlocations`
--

INSERT INTO `tabqy_userlocations` (`userlocation_id`, `userlocation_user_id`, `userlocation_country`, `userlocation_company_id`, `userlocation_region`, `userlocation_city`, `userlocation_zone`, `userlocation_location`, `userlocation_status`, `userlocation_created`) VALUES
(4, 21, 'IN', 0, 'TEST', 'NO', 'STH', 'SEC18', 1, '2018-04-06 10:41:59'),
(5, 74, 'IN', 0, 'NOIND', 'DL', 'WDL', 'KRTNAG', 1, '2018-04-24 06:42:40'),
(6, 40, 'SAU', 0, '', '', '', '', 1, '2018-05-02 09:46:56'),
(7, 41, 'IN', 0, 'NOIND,WEIND', '', '', '', 1, '2018-05-02 11:04:52');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_userprivilege`
--

CREATE TABLE `tabqy_userprivilege` (
  `userprivilege_id` int(11) NOT NULL,
  `userprivilege_add` enum('0','1') NOT NULL DEFAULT '0' COMMENT '1-yes,0-no',
  `userprivilege_edit` enum('0','1') NOT NULL DEFAULT '0' COMMENT '1-yes,0-no',
  `userprivilege_delete` enum('0','1') NOT NULL DEFAULT '0' COMMENT '1-yes,0-no',
  `userprivilege_view` enum('0','1') NOT NULL DEFAULT '0' COMMENT '1-yes,0-no',
  `userprivilege_controllers_id` int(11) NOT NULL,
  `userprivilege_role_id` int(20) NOT NULL,
  `userprivilege_status` enum('0','1') NOT NULL DEFAULT '1' COMMENT '1-active,0-deactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tabqy_userprivilege`
--

INSERT INTO `tabqy_userprivilege` (`userprivilege_id`, `userprivilege_add`, `userprivilege_edit`, `userprivilege_delete`, `userprivilege_view`, `userprivilege_controllers_id`, `userprivilege_role_id`, `userprivilege_status`) VALUES
(21, '1', '0', '0', '0', 3, 1, '1'),
(32, '1', '0', '0', '0', 4, 5, '1'),
(33, '1', '0', '0', '0', 5, 5, '1'),
(34, '1', '0', '0', '0', 6, 5, '1'),
(35, '1', '0', '0', '0', 7, 5, '1'),
(36, '1', '0', '0', '0', 8, 5, '1'),
(37, '1', '1', '1', '0', 4, 3, '1'),
(38, '1', '1', '0', '0', 1, 3, '1'),
(41, '1', '1', '0', '0', 9, 2, '1'),
(42, '1', '1', '0', '0', 10, 2, '1');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_userprivilegeres`
--

CREATE TABLE `tabqy_userprivilegeres` (
  `userprivilege_id` int(11) NOT NULL,
  `userprivilege_add` enum('0','1') NOT NULL DEFAULT '0' COMMENT '1-yes,0-no',
  `userprivilege_edit` enum('0','1') NOT NULL DEFAULT '0' COMMENT '1-yes,0-no',
  `userprivilege_delete` enum('0','1') NOT NULL DEFAULT '0' COMMENT '1-yes,0-no',
  `userprivilege_view` enum('0','1') NOT NULL DEFAULT '0' COMMENT '1-yes,0-no',
  `userprivilege_controllers_id` int(11) NOT NULL,
  `userprivilege_role_id` int(20) NOT NULL,
  `userprivilege_logintype` enum('1','2','3') NOT NULL COMMENT '1-company,2-brand,3-branch',
  `userprivilege_company_id` int(10) NOT NULL,
  `userprivilege_restaurent_id` int(10) NOT NULL,
  `userprivilege_status` enum('0','1') NOT NULL DEFAULT '1' COMMENT '1-active,0-deactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tabqy_userprivilegeres`
--

INSERT INTO `tabqy_userprivilegeres` (`userprivilege_id`, `userprivilege_add`, `userprivilege_edit`, `userprivilege_delete`, `userprivilege_view`, `userprivilege_controllers_id`, `userprivilege_role_id`, `userprivilege_logintype`, `userprivilege_company_id`, `userprivilege_restaurent_id`, `userprivilege_status`) VALUES
(20, '1', '1', '0', '0', 1, 3, '1', 2, 0, '1'),
(21, '1', '0', '0', '0', 1, 2, '1', 2, 0, '1'),
(22, '1', '0', '0', '0', 2, 2, '1', 2, 0, '1'),
(23, '1', '0', '0', '0', 3, 2, '1', 2, 0, '1'),
(24, '1', '0', '0', '0', 4, 2, '1', 2, 0, '1');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_usertype`
--

CREATE TABLE `tabqy_usertype` (
  `usertype_id` int(11) NOT NULL,
  `usertype_name` varchar(255) NOT NULL,
  `usertype_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `usertype_supperadmin` enum('0','1') NOT NULL COMMENT '0-no,1-yes',
  `usertype_company_id` int(11) NOT NULL,
  `usertype_restaurant_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_usertype`
--

INSERT INTO `tabqy_usertype` (`usertype_id`, `usertype_name`, `usertype_created`, `usertype_supperadmin`, `usertype_company_id`, `usertype_restaurant_id`) VALUES
(1, 'Waiter', '2018-04-24 07:29:36', '0', 0, 0),
(2, 'Manager', '2018-04-24 08:51:24', '0', 0, 0),
(3, 'Driver', '2018-04-07 14:28:55', '0', 0, 0),
(4, 'Super Admin', '2018-04-24 07:29:44', '0', 0, 0),
(5, 'New Role', '2018-05-02 14:29:13', '0', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_user_access`
--

CREATE TABLE `tabqy_user_access` (
  `access_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `companyid` varchar(255) DEFAULT NULL,
  `access_type` int(11) NOT NULL COMMENT '1-Location Wise,2-Company Wise',
  `country_code` varchar(255) DEFAULT NULL,
  `city_code` varchar(50) DEFAULT NULL,
  `location_code` varchar(255) DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1' COMMENT '1-Active,0-Inactive',
  `assigned_date` datetime NOT NULL,
  `access_last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_user_access`
--

INSERT INTO `tabqy_user_access` (`access_id`, `user_id`, `companyid`, `access_type`, `country_code`, `city_code`, `location_code`, `status`, `assigned_date`, `access_last_updated`) VALUES
(12, 98, '11,1,2,7,10', 2, NULL, NULL, NULL, '1', '2018-05-18 08:53:38', '2018-05-29 13:00:17'),
(7, 95, '3,8,15', 2, NULL, NULL, NULL, '1', '2018-05-17 14:06:00', '2018-05-17 12:06:16'),
(8, 96, NULL, 2, 'INNW', 'NOIDA', 'sfsd', '1', '2018-05-17 14:09:18', '2018-05-19 10:06:50'),
(5, 91, '1,5', 2, NULL, NULL, NULL, '1', '2018-05-17 14:02:40', '2018-05-17 12:02:40'),
(10, 93, NULL, 1, 'INNW', 'NOIDA', 'xddf,sfsd', '1', '2018-05-18 07:33:39', '2018-05-19 10:47:48'),
(11, 89, '11', 1, NULL, NULL, NULL, '1', '2018-05-18 07:34:09', '2018-06-02 09:36:23'),
(13, 99, NULL, 1, 'SAU', 'RYDH', 'ALOLAYA,ALSAHAFA', '1', '2018-05-18 08:53:54', '2018-06-01 13:51:15'),
(14, 101, NULL, 1, 'INNW', 'NOIDA', 'xddf,sfsd', '1', '2018-05-21 07:26:30', '2018-05-21 05:51:40'),
(15, 80, '1,2,4', 2, NULL, NULL, NULL, '1', '2018-05-22 06:13:27', '2018-06-02 12:33:03'),
(16, 81, NULL, 1, 'IN', 'DL', 'DFCOL,123456', '1', '2018-05-22 06:22:19', '2018-06-01 13:27:27'),
(17, 94, NULL, 1, 'SAU', 'RYDH', 'ALOLAYA,KINGFAI', '1', '2018-05-29 07:58:23', '2018-05-29 07:58:23'),
(18, 97, '1,2', 2, NULL, NULL, NULL, '1', '2018-05-29 13:05:06', '2018-05-29 13:05:06');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_user_role`
--

CREATE TABLE `tabqy_user_role` (
  `id` int(11) NOT NULL,
  `user_role_name` varchar(255) NOT NULL,
  `user_role_slug` varchar(255) NOT NULL,
  `user_role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_user_role`
--

INSERT INTO `tabqy_user_role` (`id`, `user_role_name`, `user_role_slug`, `user_role_id`) VALUES
(1, 'Super Admin', 'superadmin', 0),
(2, 'Tabqy Staff', 'staff', 1),
(3, 'Company Admin', 'companyAdmin', 2),
(4, 'Company Staff', 'companyStaff', 3),
(5, 'Brand Admin', 'brandAdmin', 4),
(6, 'Restaurant Admin', 'restaurantAdmin', 5),
(7, 'Brand Staff', 'brandStaff', 6),
(8, 'Restaurant Staff', 'restaurantStaff', 7);

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_zone`
--

CREATE TABLE `tabqy_zone` (
  `zone_id` int(11) NOT NULL,
  `country_code` varchar(255) NOT NULL,
  `city_code` varchar(255) NOT NULL,
  `location_code` varchar(255) NOT NULL,
  `zone_name` varchar(255) NOT NULL,
  `zone_code` varchar(255) NOT NULL,
  `zone_status` int(11) NOT NULL,
  `zone_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_zone`
--

INSERT INTO `tabqy_zone` (`zone_id`, `country_code`, `city_code`, `location_code`, `zone_name`, `zone_code`, `zone_status`, `zone_created`) VALUES
(1, 'IN', 'NOI', 'MYRV,NAKN,SEC16,SEC19,SEC25,SEC38', 'North Zone', 'NONR', 1, '2018-05-03 16:38:08'),
(2, 'IN', 'NOI', 'SEC18', 'South', 'STH', 1, '2018-05-03 16:38:27'),
(3, 'AUs', 'BRIS', 'ALXHL,STHBNK', 'Brisbane south', 'BRISSOU', 1, '2018-05-03 16:38:59'),
(4, 'IN', 'NOI', 'SEC27,SEC61,SEC62', 'North East', 'NENOI', 1, '2018-05-03 16:39:30'),
(5, 'IN', 'DL', 'DFCOL', 'South Delhi', 'STHDL', 1, '2018-05-03 16:39:48'),
(6, 'IN', 'DL', 'KRTNAG', 'West Delhi', 'WDL', 1, '2018-05-03 16:40:09'),
(7, 'IN', 'DL', 'PRTVR,VVKVH', 'East Delhi', 'EDL', 1, '2018-05-03 16:40:27'),
(8, 'SAU', 'RYDH', 'ALOLAYA,ALSAHAFA', 'Zone 1 riyadh', 'ZONE1RYD', 1, '2018-05-22 17:07:56'),
(9, 'SAU', 'RYDH', 'KINGFAI', 'Zone 2 Riyadh en', 'ZONE2RYD', 1, '2018-05-29 11:59:04'),
(10, 'IN', 'DL', '123456,Peetam', 'newzone', 'newzone', 1, '2018-05-28 22:06:39');

-- --------------------------------------------------------

--
-- Table structure for table `tabqy_zone_language`
--

CREATE TABLE `tabqy_zone_language` (
  `zone_language_id` int(11) NOT NULL,
  `zone_language_zone_id` int(11) NOT NULL,
  `zone_language_zone_name` varchar(255) NOT NULL,
  `zone_language_code` varchar(255) NOT NULL,
  `zone_language_edit` enum('0','1') NOT NULL,
  `zone_language_status` enum('0','1') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tabqy_zone_language`
--

INSERT INTO `tabqy_zone_language` (`zone_language_id`, `zone_language_zone_id`, `zone_language_zone_name`, `zone_language_code`, `zone_language_edit`, `zone_language_status`) VALUES
(1, 1, 'North Zone', 'ar', '0', '1'),
(2, 1, 'North Zone', 'en', '1', '1'),
(3, 2, 'South', 'ar', '0', '1'),
(4, 2, 'South', 'en', '1', '1'),
(5, 3, 'Brisbane south', 'ar', '0', '1'),
(6, 3, 'Brisbane south', 'en', '1', '1'),
(7, 4, 'North East', 'ar', '0', '1'),
(8, 4, 'North East', 'en', '1', '1'),
(9, 5, 'South Delhi', 'ar', '0', '1'),
(10, 5, 'South Delhi', 'en', '1', '1'),
(11, 6, 'West Delhi', 'ar', '0', '1'),
(12, 6, 'West Delhi', 'en', '1', '1'),
(13, 7, 'East Delhi', 'ar', '0', '1'),
(14, 7, 'East Delhi', 'en', '1', '1'),
(15, 8, 'Zone 1 riyadh', 'ar', '0', '1'),
(16, 8, 'Zone 1 riyadh', 'en', '1', '1'),
(17, 9, 'Zone 2 Riyadh ar', 'ar', '1', '1'),
(18, 9, 'Zone 2 Riyadh en', 'en', '1', '1'),
(19, 10, 'newzone', 'ar', '0', '1'),
(20, 10, 'newzone', 'en', '1', '1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tabqy_advertisement`
--
ALTER TABLE `tabqy_advertisement`
  ADD PRIMARY KEY (`advertisement_id`);

--
-- Indexes for table `tabqy_categories`
--
ALTER TABLE `tabqy_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `tabqy_cities`
--
ALTER TABLE `tabqy_cities`
  ADD PRIMARY KEY (`city_id`),
  ADD UNIQUE KEY `city_name` (`city_name`,`city_code`);

--
-- Indexes for table `tabqy_cities_language`
--
ALTER TABLE `tabqy_cities_language`
  ADD PRIMARY KEY (`city_language_id`);

--
-- Indexes for table `tabqy_comboitem`
--
ALTER TABLE `tabqy_comboitem`
  ADD PRIMARY KEY (`comboitem_id`);

--
-- Indexes for table `tabqy_commission`
--
ALTER TABLE `tabqy_commission`
  ADD PRIMARY KEY (`commission_id`);

--
-- Indexes for table `tabqy_company`
--
ALTER TABLE `tabqy_company`
  ADD PRIMARY KEY (`company_id`),
  ADD UNIQUE KEY `company_code` (`company_code`),
  ADD UNIQUE KEY `company_vat_no` (`company_vat_no`);

--
-- Indexes for table `tabqy_company_language`
--
ALTER TABLE `tabqy_company_language`
  ADD PRIMARY KEY (`company_language_id`);

--
-- Indexes for table `tabqy_company_plan_assign`
--
ALTER TABLE `tabqy_company_plan_assign`
  ADD PRIMARY KEY (`assign_id`);

--
-- Indexes for table `tabqy_controllers`
--
ALTER TABLE `tabqy_controllers`
  ADD PRIMARY KEY (`controllers_id`);

--
-- Indexes for table `tabqy_controllersres`
--
ALTER TABLE `tabqy_controllersres`
  ADD PRIMARY KEY (`controllers_id`);

--
-- Indexes for table `tabqy_countries`
--
ALTER TABLE `tabqy_countries`
  ADD PRIMARY KEY (`country_id`),
  ADD UNIQUE KEY `country_code` (`country_code`),
  ADD UNIQUE KEY `country_phone_code` (`country_phone_code`),
  ADD UNIQUE KEY `country_currency_code` (`country_currency_code`),
  ADD UNIQUE KEY `country_name` (`country_name`);

--
-- Indexes for table `tabqy_countries_language`
--
ALTER TABLE `tabqy_countries_language`
  ADD PRIMARY KEY (`country_language_id`);

--
-- Indexes for table `tabqy_couponextend`
--
ALTER TABLE `tabqy_couponextend`
  ADD PRIMARY KEY (`add_id`);

--
-- Indexes for table `tabqy_coupons`
--
ALTER TABLE `tabqy_coupons`
  ADD PRIMARY KEY (`discount_id`);

--
-- Indexes for table `tabqy_cuisine`
--
ALTER TABLE `tabqy_cuisine`
  ADD PRIMARY KEY (`cuisine_id`);

--
-- Indexes for table `tabqy_cuisine_language`
--
ALTER TABLE `tabqy_cuisine_language`
  ADD PRIMARY KEY (`cuisine_language_id`);

--
-- Indexes for table `tabqy_customer`
--
ALTER TABLE `tabqy_customer`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `tabqy_customer_resturant_relation`
--
ALTER TABLE `tabqy_customer_resturant_relation`
  ADD PRIMARY KEY (`customer_resturant_relation_if`);

--
-- Indexes for table `tabqy_delivery`
--
ALTER TABLE `tabqy_delivery`
  ADD PRIMARY KEY (`delivery_id`);

--
-- Indexes for table `tabqy_deliveryarea`
--
ALTER TABLE `tabqy_deliveryarea`
  ADD PRIMARY KEY (`deliveryarea_id`);

--
-- Indexes for table `tabqy_department`
--
ALTER TABLE `tabqy_department`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `tabqy_department_language`
--
ALTER TABLE `tabqy_department_language`
  ADD PRIMARY KEY (`department_language_id`);

--
-- Indexes for table `tabqy_discount`
--
ALTER TABLE `tabqy_discount`
  ADD PRIMARY KEY (`discount_id`);

--
-- Indexes for table `tabqy_faq`
--
ALTER TABLE `tabqy_faq`
  ADD PRIMARY KEY (`faq_id`);

--
-- Indexes for table `tabqy_faq_categories`
--
ALTER TABLE `tabqy_faq_categories`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `tabqy_floor`
--
ALTER TABLE `tabqy_floor`
  ADD PRIMARY KEY (`floor_id`);

--
-- Indexes for table `tabqy_floor_language`
--
ALTER TABLE `tabqy_floor_language`
  ADD PRIMARY KEY (`floor_language_id`);

--
-- Indexes for table `tabqy_item`
--
ALTER TABLE `tabqy_item`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `tabqy_itemavlableday`
--
ALTER TABLE `tabqy_itemavlableday`
  ADD PRIMARY KEY (`itemavlableday_id`);

--
-- Indexes for table `tabqy_itemportion`
--
ALTER TABLE `tabqy_itemportion`
  ADD PRIMARY KEY (`itemportion_id`);

--
-- Indexes for table `tabqy_itempriceofbranch`
--
ALTER TABLE `tabqy_itempriceofbranch`
  ADD PRIMARY KEY (`itempriceofbranch_id`);

--
-- Indexes for table `tabqy_item_associated`
--
ALTER TABLE `tabqy_item_associated`
  ADD PRIMARY KEY (`associated_id`);

--
-- Indexes for table `tabqy_item_displayonbranch`
--
ALTER TABLE `tabqy_item_displayonbranch`
  ADD PRIMARY KEY (`displayonbranch_id`);

--
-- Indexes for table `tabqy_language`
--
ALTER TABLE `tabqy_language`
  ADD PRIMARY KEY (`language_id`);

--
-- Indexes for table `tabqy_locations`
--
ALTER TABLE `tabqy_locations`
  ADD PRIMARY KEY (`location_id`),
  ADD UNIQUE KEY `location_code` (`location_code`),
  ADD UNIQUE KEY `location_name` (`location_name`);

--
-- Indexes for table `tabqy_locations_language`
--
ALTER TABLE `tabqy_locations_language`
  ADD PRIMARY KEY (`location_language_id`);

--
-- Indexes for table `tabqy_mindeliveryorder`
--
ALTER TABLE `tabqy_mindeliveryorder`
  ADD PRIMARY KEY (`mindeliveryorder_id`);

--
-- Indexes for table `tabqy_orderstatus`
--
ALTER TABLE `tabqy_orderstatus`
  ADD PRIMARY KEY (`orderstatus_id`);

--
-- Indexes for table `tabqy_ordertype`
--
ALTER TABLE `tabqy_ordertype`
  ADD PRIMARY KEY (`ordertype_id`);

--
-- Indexes for table `tabqy_othersettings`
--
ALTER TABLE `tabqy_othersettings`
  ADD PRIMARY KEY (`othersettings_id`);

--
-- Indexes for table `tabqy_page`
--
ALTER TABLE `tabqy_page`
  ADD PRIMARY KEY (`page_id`);

--
-- Indexes for table `tabqy_page_language`
--
ALTER TABLE `tabqy_page_language`
  ADD PRIMARY KEY (`page_language_id`);

--
-- Indexes for table `tabqy_paymentmethod`
--
ALTER TABLE `tabqy_paymentmethod`
  ADD PRIMARY KEY (`paymentmethod_id`);

--
-- Indexes for table `tabqy_plan`
--
ALTER TABLE `tabqy_plan`
  ADD PRIMARY KEY (`plan_id`);

--
-- Indexes for table `tabqy_plan_detail`
--
ALTER TABLE `tabqy_plan_detail`
  ADD PRIMARY KEY (`plan_detail_id`);

--
-- Indexes for table `tabqy_plan_model`
--
ALTER TABLE `tabqy_plan_model`
  ADD PRIMARY KEY (`plan_model_id`);

--
-- Indexes for table `tabqy_quantity`
--
ALTER TABLE `tabqy_quantity`
  ADD PRIMARY KEY (`quantity_id`);

--
-- Indexes for table `tabqy_regions`
--
ALTER TABLE `tabqy_regions`
  ADD PRIMARY KEY (`region_id`),
  ADD UNIQUE KEY `region_name` (`region_name`,`region_code`);

--
-- Indexes for table `tabqy_regions_language`
--
ALTER TABLE `tabqy_regions_language`
  ADD PRIMARY KEY (`region_language_id`);

--
-- Indexes for table `tabqy_resturantbrand`
--
ALTER TABLE `tabqy_resturantbrand`
  ADD PRIMARY KEY (`resturantbrand_id`);

--
-- Indexes for table `tabqy_resturantbrand_language`
--
ALTER TABLE `tabqy_resturantbrand_language`
  ADD PRIMARY KEY (`resturantbrand_language_id`);

--
-- Indexes for table `tabqy_resuser_access`
--
ALTER TABLE `tabqy_resuser_access`
  ADD PRIMARY KEY (`res_access_id`);

--
-- Indexes for table `tabqy_setpoint`
--
ALTER TABLE `tabqy_setpoint`
  ADD PRIMARY KEY (`setpoint_id`);

--
-- Indexes for table `tabqy_superadmin`
--
ALTER TABLE `tabqy_superadmin`
  ADD PRIMARY KEY (`superadmin_id`);

--
-- Indexes for table `tabqy_table`
--
ALTER TABLE `tabqy_table`
  ADD PRIMARY KEY (`table_id`);

--
-- Indexes for table `tabqy_taxes`
--
ALTER TABLE `tabqy_taxes`
  ADD PRIMARY KEY (`tax_id`);

--
-- Indexes for table `tabqy_user`
--
ALTER TABLE `tabqy_user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `user_restaurant_id` (`user_restaurant_id`);

--
-- Indexes for table `tabqy_userlocations`
--
ALTER TABLE `tabqy_userlocations`
  ADD PRIMARY KEY (`userlocation_id`);

--
-- Indexes for table `tabqy_userprivilege`
--
ALTER TABLE `tabqy_userprivilege`
  ADD PRIMARY KEY (`userprivilege_id`),
  ADD KEY `userprivilege_controllers_id` (`userprivilege_controllers_id`),
  ADD KEY `userprivilege_user_id` (`userprivilege_role_id`);

--
-- Indexes for table `tabqy_userprivilegeres`
--
ALTER TABLE `tabqy_userprivilegeres`
  ADD PRIMARY KEY (`userprivilege_id`);

--
-- Indexes for table `tabqy_usertype`
--
ALTER TABLE `tabqy_usertype`
  ADD PRIMARY KEY (`usertype_id`);

--
-- Indexes for table `tabqy_user_access`
--
ALTER TABLE `tabqy_user_access`
  ADD PRIMARY KEY (`access_id`);

--
-- Indexes for table `tabqy_user_role`
--
ALTER TABLE `tabqy_user_role`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_role_id` (`user_role_id`),
  ADD UNIQUE KEY `user_role_slug` (`user_role_slug`);

--
-- Indexes for table `tabqy_zone`
--
ALTER TABLE `tabqy_zone`
  ADD PRIMARY KEY (`zone_id`),
  ADD UNIQUE KEY `zone_name` (`zone_name`,`zone_code`);

--
-- Indexes for table `tabqy_zone_language`
--
ALTER TABLE `tabqy_zone_language`
  ADD PRIMARY KEY (`zone_language_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tabqy_advertisement`
--
ALTER TABLE `tabqy_advertisement`
  MODIFY `advertisement_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tabqy_categories`
--
ALTER TABLE `tabqy_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tabqy_cities`
--
ALTER TABLE `tabqy_cities`
  MODIFY `city_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `tabqy_cities_language`
--
ALTER TABLE `tabqy_cities_language`
  MODIFY `city_language_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `tabqy_comboitem`
--
ALTER TABLE `tabqy_comboitem`
  MODIFY `comboitem_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tabqy_commission`
--
ALTER TABLE `tabqy_commission`
  MODIFY `commission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tabqy_company`
--
ALTER TABLE `tabqy_company`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tabqy_company_language`
--
ALTER TABLE `tabqy_company_language`
  MODIFY `company_language_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `tabqy_company_plan_assign`
--
ALTER TABLE `tabqy_company_plan_assign`
  MODIFY `assign_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tabqy_controllers`
--
ALTER TABLE `tabqy_controllers`
  MODIFY `controllers_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tabqy_controllersres`
--
ALTER TABLE `tabqy_controllersres`
  MODIFY `controllers_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tabqy_countries`
--
ALTER TABLE `tabqy_countries`
  MODIFY `country_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `tabqy_countries_language`
--
ALTER TABLE `tabqy_countries_language`
  MODIFY `country_language_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tabqy_couponextend`
--
ALTER TABLE `tabqy_couponextend`
  MODIFY `add_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tabqy_coupons`
--
ALTER TABLE `tabqy_coupons`
  MODIFY `discount_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tabqy_cuisine`
--
ALTER TABLE `tabqy_cuisine`
  MODIFY `cuisine_id` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tabqy_cuisine_language`
--
ALTER TABLE `tabqy_cuisine_language`
  MODIFY `cuisine_language_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tabqy_customer`
--
ALTER TABLE `tabqy_customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tabqy_customer_resturant_relation`
--
ALTER TABLE `tabqy_customer_resturant_relation`
  MODIFY `customer_resturant_relation_if` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tabqy_delivery`
--
ALTER TABLE `tabqy_delivery`
  MODIFY `delivery_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tabqy_deliveryarea`
--
ALTER TABLE `tabqy_deliveryarea`
  MODIFY `deliveryarea_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tabqy_department`
--
ALTER TABLE `tabqy_department`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tabqy_department_language`
--
ALTER TABLE `tabqy_department_language`
  MODIFY `department_language_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tabqy_discount`
--
ALTER TABLE `tabqy_discount`
  MODIFY `discount_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tabqy_faq`
--
ALTER TABLE `tabqy_faq`
  MODIFY `faq_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tabqy_faq_categories`
--
ALTER TABLE `tabqy_faq_categories`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tabqy_floor`
--
ALTER TABLE `tabqy_floor`
  MODIFY `floor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tabqy_floor_language`
--
ALTER TABLE `tabqy_floor_language`
  MODIFY `floor_language_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tabqy_item`
--
ALTER TABLE `tabqy_item`
  MODIFY `item_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `tabqy_itemavlableday`
--
ALTER TABLE `tabqy_itemavlableday`
  MODIFY `itemavlableday_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `tabqy_itemportion`
--
ALTER TABLE `tabqy_itemportion`
  MODIFY `itemportion_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `tabqy_itempriceofbranch`
--
ALTER TABLE `tabqy_itempriceofbranch`
  MODIFY `itempriceofbranch_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tabqy_item_associated`
--
ALTER TABLE `tabqy_item_associated`
  MODIFY `associated_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `tabqy_item_displayonbranch`
--
ALTER TABLE `tabqy_item_displayonbranch`
  MODIFY `displayonbranch_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;

--
-- AUTO_INCREMENT for table `tabqy_language`
--
ALTER TABLE `tabqy_language`
  MODIFY `language_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tabqy_locations`
--
ALTER TABLE `tabqy_locations`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tabqy_locations_language`
--
ALTER TABLE `tabqy_locations_language`
  MODIFY `location_language_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `tabqy_mindeliveryorder`
--
ALTER TABLE `tabqy_mindeliveryorder`
  MODIFY `mindeliveryorder_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tabqy_orderstatus`
--
ALTER TABLE `tabqy_orderstatus`
  MODIFY `orderstatus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tabqy_ordertype`
--
ALTER TABLE `tabqy_ordertype`
  MODIFY `ordertype_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tabqy_othersettings`
--
ALTER TABLE `tabqy_othersettings`
  MODIFY `othersettings_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tabqy_page`
--
ALTER TABLE `tabqy_page`
  MODIFY `page_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tabqy_page_language`
--
ALTER TABLE `tabqy_page_language`
  MODIFY `page_language_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tabqy_paymentmethod`
--
ALTER TABLE `tabqy_paymentmethod`
  MODIFY `paymentmethod_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tabqy_plan`
--
ALTER TABLE `tabqy_plan`
  MODIFY `plan_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tabqy_plan_detail`
--
ALTER TABLE `tabqy_plan_detail`
  MODIFY `plan_detail_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT for table `tabqy_plan_model`
--
ALTER TABLE `tabqy_plan_model`
  MODIFY `plan_model_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tabqy_quantity`
--
ALTER TABLE `tabqy_quantity`
  MODIFY `quantity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tabqy_regions`
--
ALTER TABLE `tabqy_regions`
  MODIFY `region_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tabqy_regions_language`
--
ALTER TABLE `tabqy_regions_language`
  MODIFY `region_language_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tabqy_resturantbrand`
--
ALTER TABLE `tabqy_resturantbrand`
  MODIFY `resturantbrand_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `tabqy_resturantbrand_language`
--
ALTER TABLE `tabqy_resturantbrand_language`
  MODIFY `resturantbrand_language_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `tabqy_resuser_access`
--
ALTER TABLE `tabqy_resuser_access`
  MODIFY `res_access_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tabqy_setpoint`
--
ALTER TABLE `tabqy_setpoint`
  MODIFY `setpoint_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tabqy_superadmin`
--
ALTER TABLE `tabqy_superadmin`
  MODIFY `superadmin_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tabqy_table`
--
ALTER TABLE `tabqy_table`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tabqy_taxes`
--
ALTER TABLE `tabqy_taxes`
  MODIFY `tax_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tabqy_user`
--
ALTER TABLE `tabqy_user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `tabqy_userlocations`
--
ALTER TABLE `tabqy_userlocations`
  MODIFY `userlocation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tabqy_userprivilege`
--
ALTER TABLE `tabqy_userprivilege`
  MODIFY `userprivilege_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `tabqy_userprivilegeres`
--
ALTER TABLE `tabqy_userprivilegeres`
  MODIFY `userprivilege_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `tabqy_usertype`
--
ALTER TABLE `tabqy_usertype`
  MODIFY `usertype_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tabqy_user_access`
--
ALTER TABLE `tabqy_user_access`
  MODIFY `access_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `tabqy_user_role`
--
ALTER TABLE `tabqy_user_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tabqy_zone`
--
ALTER TABLE `tabqy_zone`
  MODIFY `zone_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tabqy_zone_language`
--
ALTER TABLE `tabqy_zone_language`
  MODIFY `zone_language_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
