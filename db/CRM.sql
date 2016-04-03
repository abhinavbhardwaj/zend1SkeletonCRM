-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 03, 2016 at 08:51 PM
-- Server version: 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `CRM`
--

-- --------------------------------------------------------

--
-- Table structure for table `bal_admin_users`
--

CREATE TABLE IF NOT EXISTS `bal_admin_users` (
  `admin_user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `username` varchar(45) DEFAULT NULL,
  `password` varchar(200) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` enum('Y','N') DEFAULT NULL,
  `type` int(11) NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`admin_user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `bal_admin_users`
--

INSERT INTO `bal_admin_users` (`admin_user_id`, `name`, `username`, `password`, `email`, `phone`, `image`, `is_active`, `type`, `created_date`) VALUES
(1, 'superadmin', 'admin', 'e10adc3949ba59abbe56e057f20f883e', 'abhinav.bhardwaj@engineer.com', '', '0', 'Y', 1, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `bal_challan`
--

CREATE TABLE IF NOT EXISTS `bal_challan` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `payment_date` datetime NOT NULL,
  `client_id` int(50) NOT NULL,
  `order_no` int(50) NOT NULL COMMENT 'This is Purchase order id',
  `po_date` datetime NOT NULL COMMENT 'purchase order date',
  `bill_no` varchar(50) NOT NULL,
  `bill_date` datetime NOT NULL,
  `product_id` int(50) NOT NULL COMMENT 'this is that product id from product table whish has been ordered',
  `order_product_id` int(50) NOT NULL COMMENT 'This id is from order_product table',
  `quantity` int(50) NOT NULL COMMENT 'currently how much product we are going to give',
  `sub_total` decimal(10,2) NOT NULL,
  `vat` decimal(10,2) NOT NULL COMMENT 'its a constant ',
  `shipping` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL,
  `totalInWords` varchar(150) NOT NULL,
  `added_by` int(11) NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1009 ;

--
-- Dumping data for table `bal_challan`
--

INSERT INTO `bal_challan` (`id`, `payment_date`, `client_id`, `order_no`, `po_date`, `bill_no`, `bill_date`, `product_id`, `order_product_id`, `quantity`, `sub_total`, `vat`, `shipping`, `discount`, `total`, `totalInWords`, `added_by`, `created_date`, `modified_date`) VALUES
(1006, '2016-04-04 00:00:00', 1, 10001, '2016-04-04 09:00:00', '12333', '2016-04-04 00:00:00', 1, 10001, 56, '2800.00', '134.40', '0.00', '126.00', '2808.40', '', 1, '2016-04-02 12:36:00', '2016-04-02 12:36:00'),
(1007, '2016-04-04 00:00:00', 1, 10001, '2016-04-04 09:00:00', '12333e', '2016-04-04 00:00:00', 1, 10001, 20, '1000.00', '48.00', '0.00', '45.00', '1003.00', '', 1, '2016-04-02 13:05:04', '2016-04-02 13:05:04'),
(1008, '2016-04-04 00:00:00', 1, 10001, '2016-04-04 09:00:00', '12333', '2016-04-04 00:00:00', 1, 10001, 24, '1200.00', '57.60', '0.00', '54.00', '1203.60', 'one thousand two hundred three point six ', 1, '2016-04-02 13:43:24', '2016-04-02 13:43:24');

-- --------------------------------------------------------

--
-- Table structure for table `bal_clients`
--

CREATE TABLE IF NOT EXISTS `bal_clients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `address` varchar(50) NOT NULL,
  `company_name` varchar(50) NOT NULL,
  `phone` text NOT NULL COMMENT 'It will hold all type of phone number in searlized way',
  `city` varchar(50) NOT NULL,
  `state` varchar(50) NOT NULL,
  `country` int(11) NOT NULL,
  `zip` varchar(50) NOT NULL,
  `status` enum('t','f') NOT NULL DEFAULT 't' COMMENT 'If status is f that mean client is deactive else if T then its active',
  `added_by` int(11) NOT NULL,
  `server_ip` varchar(50) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `bal_clients`
--

INSERT INTO `bal_clients` (`id`, `name`, `address`, `company_name`, `phone`, `city`, `state`, `country`, `zip`, `status`, `added_by`, `server_ip`, `created_date`, `modified_date`) VALUES
(1, 'SPICY DESIGNS', 'J-52, Sitapura Industrial Area', 'SPICY DESIGNS', '2771025', 'Jaipur', 'Rajasthan', 94, '302022', 't', 1, '127.0.0.1', '2016-03-27 09:06:34', '0000-00-00 00:00:00'),
(2, 'Ashita Chemicals', 'J-52, Sitapura Industrial Area', 'Ashita Chemicals', '9205658989', 'Jaipur', 'Rajasthan', 94, '302022', 't', 1, '127.0.0.1', '2016-03-30 16:52:30', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `bal_countrycode`
--

CREATE TABLE IF NOT EXISTS `bal_countrycode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` varchar(255) NOT NULL,
  `short_code` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=233 ;

--
-- Dumping data for table `bal_countrycode`
--

INSERT INTO `bal_countrycode` (`id`, `country`, `short_code`, `code`) VALUES
(1, 'Afghanistan', 'AF', '93'),
(2, 'Albania', 'AL', '355'),
(3, 'Algeria', 'DZ', '213'),
(4, 'American Samoa', 'AS', '1684'),
(5, 'Andorra', 'AD', '376'),
(6, 'Angola', 'AO', '244'),
(7, 'Anguilla', 'AI', '1264'),
(8, 'Antarctica', 'AQ', '672'),
(9, 'Antigua and Barbuda', 'AG', '1268'),
(10, 'Argentina', 'AR', '54'),
(11, 'Armenia', 'AM', '374'),
(12, 'Aruba', 'AW', '297'),
(13, 'Australia', 'AU', '61'),
(14, 'Austria', 'AT', '43'),
(15, 'Azerbaijan', 'AZ', '994'),
(16, 'Bahamas', 'BS', '1242'),
(17, 'Bahrain', 'BH', '973'),
(18, 'Bangladesh', 'BD', '880'),
(19, 'Barbados', 'BB', '1246'),
(20, 'Belarus', 'BY', '375'),
(21, 'Belgium', 'BE', '32'),
(22, 'Belize', 'BZ', '501'),
(23, 'Benin', 'BJ', '229'),
(24, 'Bermuda', 'BM', '1441'),
(25, 'Bhutan', 'BT', '975'),
(26, 'Bolivia', 'BO', '591'),
(27, 'Bosnia and Herzegovina', 'BA', '387'),
(28, 'Botswana', 'BW', '267'),
(29, 'Brazil', 'BR', '55'),
(30, 'British Indian Ocean Territory', 'IO', '44'),
(31, 'British Virgin Islands', 'VG', '1284'),
(32, 'Brunei', 'BN', '673'),
(33, 'Bulgaria', 'BG', '359'),
(34, 'Burkina Faso', 'BF', '226'),
(35, 'Burma (Myanmar)', 'MM', '95'),
(36, 'Burundi', 'BI', '257'),
(37, 'Cambodia', 'KH', '855'),
(38, 'Cameroon', 'CM', '237'),
(39, 'Canada', 'CA', '1'),
(40, 'Cape Verde', 'CV', '238'),
(41, 'Cayman Islands', 'KY', '1345'),
(42, 'Central African Republic', 'CF', '236'),
(43, 'Chad', 'TD', '235'),
(44, 'Chile', 'CL', '56'),
(45, 'China', 'CN', '86'),
(46, 'Christmas Island', 'CX', '61'),
(47, 'Cocos (Keeling) Islands', 'CC', '61'),
(48, 'Colombia', 'CO', '57'),
(49, 'Comoros', 'KM', '269'),
(50, 'Cook Islands', 'CK', '682'),
(51, 'Costa Rica', 'CR', '506'),
(52, 'Croatia', 'HR', '385'),
(53, 'Cuba', 'CU', '53'),
(54, 'Cyprus', 'CY', '357'),
(55, 'Czech Republic', 'CZ', '420'),
(56, 'Democratic Republic of the Congo', 'CD', '243'),
(57, 'Denmark', 'DK', '45'),
(58, 'Djibouti', 'DJ', '253'),
(59, 'Dominica', 'DM', '1767'),
(60, 'Dominican Republic', 'DO', '1809'),
(61, 'Ecuador', 'EC', '593'),
(62, 'Egypt', 'EG', '20'),
(63, 'El Salvador', 'SV', '503'),
(64, 'Equatorial Guinea', 'GQ', '240'),
(65, 'Eritrea', 'ER', '291'),
(66, 'Estonia', 'EE', '372'),
(67, 'Ethiopia', 'ET', '251'),
(68, 'Falkland Islands', 'FK', '500'),
(69, 'Faroe Islands', 'FO', '298'),
(70, 'Fiji', 'FJ', '679'),
(71, 'Finland', 'FI', '358'),
(72, 'France', 'FR', '33'),
(73, 'French Polynesia', 'PF', '689'),
(74, 'Gabon', 'GA', '241'),
(75, 'Gambia', 'GM', '220'),
(76, 'Georgia', 'GE', '995'),
(77, 'Germany', 'DE', '49'),
(78, 'Ghana', 'GH', '233'),
(79, 'Gibraltar', 'GI', '350'),
(80, 'Greece', 'GR', '30'),
(81, 'Greenland', 'GL', '299'),
(82, 'Grenada', 'GD', '1473'),
(83, 'Guam', 'GU', '1671'),
(84, 'Guatemala', 'GT', '502'),
(85, 'Guinea', 'GN', '224'),
(86, 'Guinea-Bissau', 'GW', '245'),
(87, 'Guyana', 'GY', '592'),
(88, 'Haiti', 'HT', '509'),
(89, 'Holy See (Vatican City)', 'VA', '39'),
(90, 'Honduras', 'HN', '504'),
(91, 'Hong Kong', 'HK', '852'),
(92, 'Hungary', 'HU', '36'),
(93, 'Iceland', 'IS', '354'),
(94, 'India', 'IN', '91'),
(95, 'Indonesia', 'ID', '62'),
(96, 'Iran', 'IR', '98'),
(97, 'Iraq', 'IQ', '964'),
(98, 'Ireland', 'IE', '353'),
(99, 'Isle of Man', 'IM', '44'),
(100, 'Israel', 'IL', '972'),
(101, 'Italy', 'IT', '39'),
(102, 'Ivory Coast', 'CI', '225'),
(103, 'Jamaica', 'JM', '1876'),
(104, 'Japan', 'JP', '81'),
(105, 'Jersey', 'JE', '44'),
(106, 'Jordan', 'JO', '962'),
(107, 'Kazakhstan', 'KZ', '7'),
(108, 'Kenya', 'KE', '254'),
(109, 'Kiribati', 'KI', '686'),
(110, 'Kuwait', 'KW', '965'),
(111, 'Kyrgyzstan', 'KG', '996'),
(112, 'Laos', 'LA', '856'),
(113, 'Latvia', 'LV', '371'),
(114, 'Lebanon', 'LB', '961'),
(115, 'Lesotho', 'LS', '266'),
(116, 'Liberia', 'LR', '231'),
(117, 'Libya', 'LY', '218'),
(118, 'Liechtenstein', 'LI', '423'),
(119, 'Lithuania', 'LT', '370'),
(120, 'Luxembourg', 'LU', '352'),
(121, 'Macau', 'MO', '853'),
(122, 'Macedonia', 'MK', '389'),
(123, 'Madagascar', 'MG', '261'),
(124, 'Malawi', 'MW', '265'),
(125, 'Malaysia', 'MY', '60'),
(126, 'Maldives', 'MV', '960'),
(127, 'Mali', 'ML', '223'),
(128, 'Malta', 'MT', '356'),
(129, 'Marshall Islands', 'MH', '692'),
(130, 'Mauritania', 'MR', '222'),
(131, 'Mauritius', 'MU', '230'),
(132, 'Mayotte', 'YT', '262'),
(133, 'Mexico', 'MX', '52'),
(134, 'Micronesia', 'FM', '691'),
(135, 'Moldova', 'MD', '373'),
(136, 'Monaco', 'MC', '377'),
(137, 'Mongolia', 'MN', '976'),
(138, 'Montenegro', 'ME', '382'),
(139, 'Montserrat', 'MS', '1664'),
(140, 'Morocco', 'MA', '212'),
(141, 'Mozambique', 'MZ', '258'),
(142, 'Namibia', 'NA', '264'),
(143, 'Nauru', 'NR', '674'),
(144, 'Nepal', 'NP', '977'),
(145, 'Netherlands', 'NL', '31'),
(146, 'Netherlands Antilles', 'AN', '599'),
(147, 'New Caledonia', 'NC', '687'),
(148, 'New Zealand', 'NZ', '64'),
(149, 'Nicaragua', 'NI', '505'),
(150, 'Niger', 'NE', '227'),
(151, 'Nigeria', 'NG', '234'),
(152, 'Niue', 'NU', '683'),
(153, 'Norfolk Island', 'NFK', '672'),
(154, 'North Korea', 'KP', '850'),
(155, 'Northern Mariana Islands', 'MP', '1670'),
(156, 'Norway', 'NO', '47'),
(157, 'Oman', 'OM', '968'),
(158, 'Pakistan', 'PK', '92'),
(159, 'Palau', 'PW', '680'),
(160, 'Panama', 'PA', '507'),
(161, 'Papua New Guinea', 'PG', '675'),
(162, 'Paraguay', 'PY', '595'),
(163, 'Peru', 'PE', '51'),
(164, 'Philippines', 'PH', '63'),
(165, 'Pitcairn Islands', 'PN', '870'),
(166, 'Poland', 'PL', '48'),
(167, 'Portugal', 'PT', '351'),
(168, 'Puerto Rico', 'PR', '1'),
(169, 'Qatar', 'QA', '974'),
(170, 'Republic of the Congo', 'CG', '242'),
(171, 'Romania', 'RO', '40'),
(172, 'Russia', 'RU', '7'),
(173, 'Rwanda', 'RW', '250'),
(174, 'Saint Barthelemy', 'BL', '590'),
(175, 'Saint Helena', 'SH', '290'),
(176, 'Saint Kitts and Nevis', 'KN', '1869'),
(177, 'Saint Lucia', 'LC', '1758'),
(178, 'Saint Martin', 'MF', '1599'),
(179, 'Saint Pierre and Miquelon', 'PM', '508'),
(180, 'Saint Vincent and the Grenadines', 'VC', '1784'),
(181, 'Samoa', 'WS', '685'),
(182, 'San Marino', 'SM', '378'),
(183, 'Sao Tome and Principe', 'ST', '239'),
(184, 'Saudi Arabia', 'SA', '966'),
(185, 'Senegal', 'SN', '221'),
(186, 'Serbia', 'RS', '381'),
(187, 'Seychelles', 'SC', '248'),
(188, 'Sierra Leone', 'SL', '232'),
(189, 'Singapore', 'SG', '65'),
(190, 'Slovakia', 'SK', '421'),
(191, 'Slovenia', 'SI', '386'),
(192, 'Solomon Islands', 'SB', '677'),
(193, 'Somalia', 'SO', '252'),
(194, 'South Africa', 'ZA', '27'),
(195, 'South Korea', 'KR', '82'),
(196, 'Spain', 'ES', '34'),
(197, 'Sri Lanka', 'LK', '94'),
(198, 'Sudan', 'SD', '249'),
(199, 'Suriname', 'SR', '597'),
(200, 'Swaziland', 'SZ', '268'),
(201, 'Sweden', 'SE', '46'),
(202, 'Switzerland', 'CH', '41'),
(203, 'Syria', 'SY', '963'),
(204, 'Taiwan', 'TW', '886'),
(205, 'Tajikistan', 'TJ', '992'),
(206, 'Tanzania', 'TZ', '255'),
(207, 'Thailand', 'TH', '66'),
(208, 'Timor-Leste', 'TL', '670'),
(209, 'Togo', 'TG', '228'),
(210, 'Tokelau', 'TK', '690'),
(211, 'Tonga', 'TO', '676'),
(212, 'Trinidad and Tobago', 'TT', '1868'),
(213, 'Tunisia', 'TN', '216'),
(214, 'Turkey', 'TR', '90'),
(215, 'Turkmenistan', 'TM', '993'),
(216, 'Turks and Caicos Islands', 'TC', '1649'),
(217, 'Tuvalu', 'TV', '688'),
(218, 'Uganda', 'UG', '256'),
(219, 'Ukraine', 'UA', '380'),
(220, 'United Arab Emirates', 'AE', '971'),
(221, 'United Kingdom', 'GB', '44'),
(222, 'United States', 'US', '1'),
(223, 'Uruguay', 'UY', '598'),
(224, 'US Virgin Islands', 'VI', '1340'),
(225, 'Uzbekistan', 'UZ', '998'),
(226, 'Vanuatu', 'VU', '678'),
(227, 'Venezuela', 'VE', '58'),
(228, 'Vietnam', 'VN', '84'),
(229, 'Wallis and Futuna', 'WF', '681'),
(230, 'Yemen', 'YE', '967'),
(231, 'Zambia', 'ZM', '260'),
(232, 'Zimbabwe', 'ZW', '263');

-- --------------------------------------------------------

--
-- Table structure for table `bal_images`
--

CREATE TABLE IF NOT EXISTS `bal_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_title` varchar(100) DEFAULT NULL,
  `image_name` varchar(100) DEFAULT NULL,
  `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bal_invoice`
--

CREATE TABLE IF NOT EXISTS `bal_invoice` (
  `id` bigint(50) NOT NULL AUTO_INCREMENT,
  `payment_date` datetime NOT NULL,
  `client_id` int(50) NOT NULL,
  `order_no` int(50) NOT NULL COMMENT 'This is Purchase order id',
  `po_date` datetime NOT NULL COMMENT 'purchase order date',
  `gr_no` varchar(50) NOT NULL,
  `gr_date` datetime NOT NULL,
  `challan_ids` text CHARACTER SET latin1 NOT NULL COMMENT 'Here we will save comma seperated challan id',
  `sub_total` decimal(10,2) NOT NULL,
  `vat` decimal(10,2) NOT NULL COMMENT 'its a constant ',
  `shipping` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL,
  `totalInWords` varchar(150) CHARACTER SET latin1 NOT NULL,
  `added_by` int(11) NOT NULL DEFAULT '1',
  `created_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1000002 ;

--
-- Dumping data for table `bal_invoice`
--

INSERT INTO `bal_invoice` (`id`, `payment_date`, `client_id`, `order_no`, `po_date`, `gr_no`, `gr_date`, `challan_ids`, `sub_total`, `vat`, `shipping`, `discount`, `total`, `totalInWords`, `added_by`, `created_date`, `modified_date`) VALUES
(1000000, '2016-04-07 00:00:00', 1, 10001, '2016-04-04 09:00:00', '522555', '2016-04-04 00:00:00', '1006,1007,1008', '5000.00', '240.00', '0.00', '225.00', '5015.00', 'five thousand fifteen', 1, '2016-04-03 18:03:31', '2016-04-03 18:03:31'),
(1000001, '2016-04-06 00:00:00', 1, 10001, '2016-04-04 09:00:00', '522555', '2016-04-04 00:00:00', '1006,1007,1008', '5000.00', '240.00', '0.00', '225.00', '5015.00', 'five thousand fifteen', 1, '2016-04-03 18:15:32', '2016-04-03 18:15:32');

-- --------------------------------------------------------

--
-- Table structure for table `bal_ordered_product`
--

CREATE TABLE IF NOT EXISTS `bal_ordered_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `po_id` bigint(100) NOT NULL COMMENT 'This is Purchase order id from purchase order table',
  `product_id` int(11) NOT NULL COMMENT 'this is product id from product table',
  `ordered_quentity` int(50) NOT NULL,
  `given_quentity` int(50) NOT NULL DEFAULT '0',
  `rate` int(11) NOT NULL,
  `amount` int(50) NOT NULL,
  `remark` text NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10003 ;

--
-- Dumping data for table `bal_ordered_product`
--

INSERT INTO `bal_ordered_product` (`id`, `po_id`, `product_id`, `ordered_quentity`, `given_quentity`, `rate`, `amount`, `remark`, `created_date`, `modified_date`) VALUES
(10001, 10001, 1, 100, 100, 50, 5000, 'we want 30 kg per month', '2016-03-30 19:06:07', '2016-04-02 13:43:24'),
(10002, 10002, 5, 200, 0, 50, 10000, 'we want 30 kg per month', '2016-03-30 19:07:48', '2016-04-02 10:27:25');

-- --------------------------------------------------------

--
-- Table structure for table `bal_pages`
--

CREATE TABLE IF NOT EXISTS `bal_pages` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL,
  `description` text NOT NULL,
  `alias` varchar(30) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `bal_pages`
--

INSERT INTO `bal_pages` (`page_id`, `title`, `description`, `alias`, `created_date`, `modified_date`) VALUES
(1, 'About Us', '<!DOCTYPE html>\r\n<html>\r\n<head>\r\n</head>\r\n<body>\r\n<p><strong>Why Finny?</strong></p>\r\n<p><strong>Outside the Classroom</strong></p>\r\n<p style="text-align: justify;">We spend $1T on education in the US, more than any other country, and yet academic performance is declining every year. Clearly money is not the answer. The EdTech world has primarily been focused on ways to improve the classroom experience, providing better ways for teachers to interact with students and make learning more efficient. Even with all this innovation we are not seeing the desired results. We believe the classroom is only part of the overall picture. Our children now spend more time outside the classroom consuming media outside than they spend in a normal school day. Much of this time is spent on unproductive pursuits like social media and mobile games. We need to develop more creative ways to encourage learning outside the classroom</p>\r\n<p><strong>Cultural Literacy</strong></p>\r\n<p style="text-align: justify;">Aside from falling behind in math and science, we are also seeing a decline in the well rounded student. Non-traditional subjects like financial literacy, health &amp; nutrition, and current events are largely overlooked. Our children should take pride in simply "knowing stuff".</p>\r\n<p><strong>It''s about the Parents</strong></p>\r\n<p style="text-align: justify;">With almost unlimited access to mobile devices and technology, this wasted time has become increasingly difficult to monitor. We challenge parents to get engaged and finny provides them with a practical solution that can make a huge impact. Family involvement in education is the greatest predictor of success. A child''s first and most formitable teacher will always be the parent. <br />&nbsp;<br />If we confine learning to classrooms, teachers, and traditional academic subjects we are doing our children a disservice. In order to compete in the global marketplace we need to spark intelluctual curiosity and promote a culture that values education. This is our responsibility and will ultimately affect change and give our children a brighter future.</p>\r\n<p>&nbsp;</p>\r\n</body>\r\n</html>', 'About Us', '2014-10-14 08:42:46', '0000-00-00 00:00:00'),
(2, 'Privacy Policy', '<!DOCTYPE html>\r\n<html>\r\n<head>\r\n</head>\r\n<body>\r\n<p style="text-align: justify;">This Privacy Policy governs the manner in which Pacific Street Ventures collects, uses, maintains and discloses information collected from users (each, a "User") of the www.myfinny.com website ("Site"). This privacy policy applies to the Site and all products and services offered by Pacific Street Ventures.</p>\r\n<p style="text-align: justify;"><strong>Personal identification information</strong></p>\r\n<p style="text-align: justify;">We may collect personal identification information from Users in a variety of ways, including, but not limited to, when Users visit our site, register on the site, fill out a form, and in connection with other activities, services, features or resources we make available on our Site. Users may be asked for, as appropriate, name, email address, mailing address, phone number, credit card information. We will collect personal identification information from Users only if they voluntarily submit such information to us. Users can always refuse to supply personally identification information, except that it may prevent them from engaging in certain Site related activities.</p>\r\n<p style="text-align: justify;"><strong>Non-personal identification information</strong></p>\r\n<p style="text-align: justify;">We may collect non-personal identification information about Users whenever they interact with our Site. Non-personal identification information may include the browser name, the type of computer and technical information about Users means of connection to our Site, such as the operating system and the Internet service providers utilized and other similar information.</p>\r\n<p style="text-align: justify;"><strong>Web browser cookies</strong></p>\r\n<p style="text-align: justify;">Our Site may use "cookies" to enhance User experience. User''s web browser places cookies on their hard drive for record-keeping purposes and sometimes to track information about them. User may choose to set their web browser to refuse cookies, or to alert you when cookies are being sent. If they do so, note that some parts of the Site may not function properly.</p>\r\n<p style="text-align: justify;"><strong>How we use collected information</strong></p>\r\n<p style="text-align: justify;">Pacific Street Ventures may collect and use Users personal information for the following purposes:<br />- To improve customer service:&nbsp;Information you provide helps us respond to your customer service requests and support needs more efficiently.<br />- To personalize user experience:&nbsp;We may use information in the aggregate to understand how our Users as a group use the services and resources provided on our Site.<br />- To improve our Site:&nbsp;We may use feedback you provide to improve our products and services.<br />- To process payments:&nbsp;We may use the information Users provide about themselves when placing an order only to provide service to that order. We do not share this information with outside parties except to the extent necessary to provide the service.<br />- To run a promotion, contest, survey or other Site feature:&nbsp;To send Users information they agreed to receive about topics we think will be of interest to them.<br />- To send periodic emails:&nbsp;We may use the email address to send User information and updates pertaining to their order. It may also be used to respond to their inquiries, questions, and/or other requests. If User decides to opt-in to our mailing list, they will receive emails that may include company news, updates, related product or service information, etc. If at any time the User would like to unsubscribe from receiving future emails, they may do so by contacting us via our Site.</p>\r\n<p><strong>How we protect your information</strong></p>\r\n<p style="text-align: justify;">We adopt appropriate data collection, storage and processing practices and security measures to protect against unauthorized access, alteration, disclosure or destruction of your personal information, username, password, transaction information and data stored on our Site.</p>\r\n<p style="text-align: justify;"><strong>Sharing your personal information</strong></p>\r\n<p style="text-align: justify;">We do not sell, trade, or rent Users personal identification information to others. We may share generic aggregated demographic information not linked to any personal identification information regarding visitors and users with our business partners, trusted affiliates and advertisers for the purposes outlined above.We may use third party service providers to help us operate our business and the Site or administer activities on our behalf, such as sending out newsletters or surveys. We may share your information with these third parties for those limited purposes provided that you have given us your permission.</p>\r\n<p style="text-align: justify;"><strong>Third party websites</strong></p>\r\n<p style="text-align: justify;">Users may find advertising or other content on our Site that link to the sites and services of our partners, suppliers, advertisers, sponsors, licensors and other third parties. We do not control the content or links that appear on these sites and are not responsible for the practices employed by websites linked to or from our Site. In addition, these sites or services, including their content and links, may be constantly changing. These sites and services may have their own privacy policies and customer service policies. Browsing and interaction on any other website, including websites which have a link to our Site, is subject to that website''s own terms and policies.</p>\r\n<p style="text-align: justify;"><strong>Changes to this privacy policy</strong></p>\r\n<p style="text-align: justify;">Pacific Street Ventures has the discretion to update this privacy policy at any time. When we do, we will revise the updated date at the bottom of this page. We encourage Users to frequently check this page for any changes to stay informed about how we are helping to protect the personal information we collect. You acknowledge and agree that it is your responsibility to review this privacy policy periodically and become aware of modifications.</p>\r\n<p style="text-align: justify;"><strong>Your acceptance of these terms</strong></p>\r\n<p style="text-align: justify;">By using this Site, you signify your acceptance of this policy and terms of service. If you do not agree to this policy, please do not use our Site. Your continued use of the Site following the posting of changes to this policy will be deemed your acceptance of those changes.</p>\r\n<p style="text-align: justify;"><strong>Contacting us</strong></p>\r\n<p style="text-align: justify;">If you have any questions about this Privacy Policy, the practices of this site, or your dealings with this site, please contact us at:<br />Pacific Street Ventures (<a href="http://www.myfinny.com">www.myfinny.com</a>)<br />125 Pacific Street #3, Santa Monica, CA 90405<br />(408) 722-6180&nbsp;privacy@pacificstreetventures.com</p>\r\n<p>This document was last updated on December 19, 2013</p>\r\n<p>Privacy policy created by Generate Privacy Policy</p>\r\n</body>\r\n</html>', 'Privacy Policy', '2014-09-17 05:53:02', '0000-00-00 00:00:00'),
(3, 'Support', '<!DOCTYPE html>\r\n<html>\r\n<head>\r\n</head>\r\n<body>\r\n<p>For support please email support@pacificstreetventures.com.</p>\r\n</body>\r\n</html>', 'Support', '2014-09-17 05:53:15', '0000-00-00 00:00:00'),
(4, 'End User License Agreement', '<!DOCTYPE html>\r\n<html>\r\n<head>\r\n</head>\r\n<body>\r\n<p><strong>ANDROID LICENSED APPLICATION - END USER LICENSE AGREEMENT</strong></p>\r\n<p style="text-align: justify;">PLEASE READ CAREFULLY THE FOLLOWING LEGAL AGREEMENT (&ldquo;Agreement&rdquo;). THIS AGREEMENT CONSTITUTES A LEGAL, BINDING AND ENFORCEABLE AGREEMENT BETWEEN YOU AND <strong>Pacific Street Ventures (PSV)</strong>. (&ldquo;Licensor&rdquo;) REGARDING USE OF ANY SOFTWARE OR APPLICATION (&ldquo;myfinny.com or Myfinny app&rdquo;) PROVIDED BY PSV. BY INSTALLING THE APPLICATION, YOU ACCEPT AND AGREE TO BE BOUND BY AND BECOME A PARTY TO THIS AGREEMENT WITH SMARTLABS. IF YOU DO NOT AGREE TO ALL OF THE TERMS IN THIS AGREEMENT, DO NOT INSTALL OR USE THE APPLICATION.</p>\r\n<ol>\r\n<li style="text-align: justify;"><strong>Parties to the Agreement. </strong>This Agreement is between you and Licensor. &ldquo;Licensed Application&rdquo; as used herein refers to the Application that is subject to the license granted under this Agreement. The Licensed Application and any related documentation is licensed, not sold, to you, subject to the terms and conditions herein. Licensor reserves all rights in and to the Licensed Application not expressly granted to you under this Agreement.</li>\r\n<li style="text-align: justify;"><strong>Scope of License.</strong> Conditioned upon your compliance with the terms and conditions of this Agreement, Licensor grants to you a limited, revocable, non-transferable, non-exclusive, personal, non-sublicensable, non-assignable license to install and use the Licensed Application on a single mobile product running Android OS (&ldquo;Android Device&rdquo;) that you own or control.</li>\r\n<li style="text-align: justify;"><strong>Limitations of Use. </strong>This license does not allow you to use the Licensed Application on any Android Device that you do not own or control, and you may not distribute or make the Licensed Application available over a network where it could be used by multiple devices at the same time. You may not download or use the Licensed Application in violation of any applicable laws or regulations. You agree that you will not use the Licensed Application for any revenue-generating endeavor, commercial enterprise, or other purpose for which it is not designed or intended. Any information or data generated or stored by the Licensed Application is for general informational purposes only and should not be relied upon as investment or tax advice , including, but not limited to, use of any data for tax purposes. You may not give away, rent, lease, lend, sell, transfer, redistribute, or sublicense the Licensed Application and, if you sell your Android Device to a third party, you must remove the Licensed Application from the Android Device before doing so. You agree that the Licensed Application contains proprietary information and trade secrets belonging to Licensor. You may not copy (except as expressly permitted by this license), decompile, reverse-engineer, disassemble, attempt to derive the source code of, modify, or create derivative works of the Licensed Application, any updates, or any part thereof (except as and only to the extent that any foregoing restriction is prohibited by applicable law or to the extent as may be permitted by the licensing terms governing use of any open-sourced components included with the Licensed Application). Any attempt to do so is a violation of the rights of the Licensor and its licensors. If you breach this restriction, you may be subject to prosecution and damages; in addition, any derivative works, improvements, inventions or works developed by you based upon or relating to the Licensed Application involving a breach of this Agreement shall be owned by Licensor.</li>\r\n<li style="text-align: justify;"><strong>No Warranties. </strong>Licensor does not warrant that the Licensed Application or Services will be compatible or interoperable with your Android Device or any other piece of hardware, software, equipment or device installed on or used in connection with your Android Device. You acknowledge and agree that Licensor and its agents shall have no liability to you for any losses suffered resulting from or arising in connection with compatibility or interoperability problems.</li>\r\n<li style="text-align: justify;"><strong>Maintenance; Support; Updates and Upgrades</strong>. Licensor is not obligated to provide any maintenance or support services with respect to the Licensed Application, or to provide you with updates, fixes, modifications, upgrades or services related thereto. However, the terms of this Agreement will govern any updates, fixes, modifications, upgrades or services provided by Licensor in its sole discretion, unless such update, fix, modification, upgrade or service is accompanied by a separate agreement in which case the terms of that agreement will govern.</li>\r\n<li style="text-align: justify;"><strong>Consent to Use of Data.</strong> You agree that Licensor may collect and use technical data and related information&mdash;including but not limited to technical information about your device, system and application software, and peripherals&mdash;that is gathered periodically to facilitate the provision of software updates, product support, and other services to you (if any) related to the Licensed Application. Licensor may use this information, as long as it is in a form that does not personally identify you, to improve its products or to provide services or technologies to you.</li>\r\n<li style="text-align: justify;"><strong>User-Submitted Data</strong>. Certain areas on the Licensed Application may allow you to provide us or others with data, such as, but not limited to, commentaries, reviews, audio and video, feedback, posts, public and private messages or other potential content from you (hereinafter &ldquo;the User Materials&rdquo;). While we do not claim ownership of User Materials, by providing User Materials to us or others via the Licensed Application you are automatically granting us a perpetual, irrevocable, worldwide, paid up, non-exclusive license to use the User Materials or any of its elements for any type of use forever, including promotional and advertising purposes and in any media whether now known or hereafter devised, including the creation of derivative works that may include User Materials you provide. You agree that any User Materials you provide us may be used by us or our affiliates, and you are not entitled to any payment or other compensation for such use.</li>\r\n</ol>\r\n<p style="text-align: justify;">You hereby confirm: (a) your User Materials will be not be subject to any obligation, of confidence or otherwise, to you or any other person; (b) your posting of the content on or through the Licensed Application does not violate the privacy rights, publicity rights, copyrights or other rights of any other person; and (c) your posting is in accordance with this Agreement and that we shall not be liable for any use or disclosure of such User Materials. We reserve the right (but do not assume the obligation) in our sole discretion to reject, move, edit or remove any User Materials. You acknowledge that we do not verify, adopt, ratify, or sanction User Materials, and you agree that you must evaluate and bear all risks associated with our use of User Materials or our reliance on the accuracy, completeness, or usefulness of User Materials.</p>\r\n<ol start="8">\r\n<li style="text-align: justify;"><strong>Privacy.</strong> Licensor may collect certain personally identifiable information, including without limitation, your email address, phone numbers and other identifier or information that permits the physical, electronic or other means of contacting you, in connection with the use of the Licensed Application. Licensor&rsquo;s Privacy Policy (available at http://www.smarthome.com) contains information about Licensor&rsquo;s policies and procedures regarding the collection, use and disclosure of information Licensor receive from users of its products and services. Licensor will not sell or rent your personally identifiable information or share your personal information with nonaffiliated companies, except with your permission or under the following circumstances: (1) Licensor may provide the information to trusted partners and affiliates who work on our behalf and who may use your personally identifiable information to help Licensor communicate with you about information and offers relating to our products or services; however, such affiliates would not have an independent right to share your personally identifiable information; (2) Licensor may respond to subpoenas, court orders, or legal process, or to establish or exercise our legal rights or defend against legal claims; (3) Licensor may share personally identifiable information in order to investigate, prevent, or take action regarding illegal activities, suspected fraud, situations involving potential threats to the physical safety of any person, violations of terms of use, or as required by law; (4) Licensor may transfer personally identifiable information if it is acquired by or merges with another company; and (5) third parties may unlawfully intercept or access information or other confidential transmissions. If you believe that your legal or privacy rights have been violated while using the Licensed Application or Third Party Applications, you can report such matters to Licensor and Licensor will, at its discretion, examine your complaint and take commercially reasonable efforts to attempt to resolve the issue as part of Licensor&rsquo;s commitment to providing a positive user experience. You acknowledge that you are responsible for addressing any third party claims relating to your use or possession of the Licensed Application, and agree to notify Licensor of any third party claims relating to the Licensed Application of which you become aware. Furthermore, you hereby release Licensor from any liability resulting from your use or possession of the Licensed Application and any products provided by Licensor for use or associated therewith, including without limitation, the following: (i) product liability claims; (ii) any claim that the Licensed Application fails to conform to any applicable legal or regulatory requirement; and (iii) any claim arising under consumer protection or similar legislation.</li>\r\n<li style="text-align: justify;"><strong>Your Representations and Warranties</strong>. You represent and warrant that you are authorized to enter into this Agreement and comply with its terms. You further represent and warrant that you will at all times comply with your obligations hereunder and any applicable laws, regulations and policies, which may apply to the Licensed Application. You assume full and unlimited liability for any use contrary to this Agreement whether such use has been enacted or caused directly or indirectly by you. You agree to defend, indemnify and hold harmless Licensor from and against any and all liability, loss, costs, or expenses (including without limitation, attorneys&rsquo; fees) arising from, related to, or in any way connected with or incurred in connection with your violation or breach of this Agreement or applicable laws, regulations or policies, your use of the Licensed Application or Third Party Materials or Services. Licensor reserves the right to assume the exclusive defense and control of any matter subject to indemnification by you, which will not excuse your indemnity obligations under this section. You agree not to settle any claims against Licensor without the express written consent and approval of Licensor. The indemnity obligations contained herein shall survive the termination of this Agreement.</li>\r\n<li style="text-align: justify;"><strong>Termination</strong>. This Agreement is effective until terminated by you or Licensor. The license granted to you in this Agreement will terminate automatically without notice from the Licensor if you fail to comply with any term(s) of this Agreement. You may terminate this Agreement by destroying all copies of the Licensed Application in your possession together with any related documentation. Upon termination of the license, you shall cease all use of the Licensed Application and destroy all copies, full or partial, of the Licensed Application and any related documentation. Licensor reserves the right to seek any and all remedies available at law or in equity in connection with your breach of this Agreement in addition to termination of this Agreement. Sections 1, 3, 4, and 8 - 20 shall survive the termination of this Agreement.</li>\r\n<li style="text-align: justify;"><strong>Services; Third-Party Materials</strong>. The Licensed Application may enable access to Licensor&rsquo;s and/or third-party services and websites and may be used with certain products provided by Licensor (collectively and individually, "Services"). Use of the Services requires Internet access and use of certain Services requires you to accept additional terms.</li>\r\n</ol>\r\n<p style="text-align: justify;">You understand that by using any of the Services, you may encounter content that may be deemed offensive, indecent, or objectionable, which content may or may not be identified as having explicit language, and that the results of any search or entering of a particular URL may automatically and unintentionally generate links or references to objectionable material. Nevertheless, you agree to use the Services at your sole risk and that neither the Licensor nor its agents shall have any liability to you for content that may be found to be offensive, indecent, or objectionable.</p>\r\n<p style="text-align: justify;">Certain Services may display, include or make available content, data, information, applications or materials from third parties (&ldquo;Third Party Materials&rdquo;) or provide links to certain third party web sites. By using the Services, you acknowledge and agree that neither the Licensor nor its agents is responsible for examining or evaluating the content, accuracy, completeness, timeliness, validity, copyright compliance, legality, decency, quality or any other aspect of such Third Party Materials or web sites. Neither the Licensor nor its agents warrant or endorse and does not assume and will not have any liability or responsibility to you or any other person for any third-party services, Third Party Materials or web sites, or for any other materials, products, or services of third parties. Third Party Materials and links to other web sites are provided solely as a convenience to you.</p>\r\n<p style="text-align: justify;">Financial information displayed by any Services is for general informational purposes only and should not be relied upon as investment or tax advice. Before executing any securities transaction based upon information obtained through the Services, you should consult with a financial or securities professional who is legally qualified to give financial or securities advice in your country or region. Location data provided by any Services is for basic navigational purposes only and is not intended to be relied upon in situations where precise location information is needed or where erroneous, inaccurate, time-delayed or incomplete location data may lead to death, personal injury, property or environmental damage. Neither the Licensor nor its agents, nor any of its content providers, guarantees the availability, accuracy, completeness, reliability, or timeliness of stock information, location data or any other data displayed by any Services.</p>\r\n<p style="text-align: justify;">You agree that the Services contain proprietary content, information and material that is owned by Licensor and/or its agents or licensors, and is protected by applicable intellectual property and other laws, including but not limited to copyright, and that you will not use such proprietary content, information or materials in any way whatsoever except for permitted use of the Services or in any manner that is inconsistent with the terms of this Agreement or that infringes any intellectual property rights of a third party. No portion of the Services may be reproduced in any form or by any means. You agree not to modify, rent, lease, loan, sell, distribute, or create derivative works based on the Services, in any manner, and you shall not exploit the Services in any unauthorized way whatsoever, including but not limited to, using the Services to transmit any computer viruses, worms, trojan horses or other malware, or by trespass or burdening network capacity. You further agree not to use the Services in any manner to harass, abuse, stalk, threaten, defame or otherwise infringe or violate the rights of any other party, and that neither Licensor nor its agents is in any way responsible for any such use by you, nor for any harassing, threatening, defamatory, offensive, infringing or illegal messages or transmissions that you may receive as a result of using any of the Services.</p>\r\n<p style="text-align: justify;">In addition, Services and Third Party Materials that may be accessed from, displayed on or linked to from the Android Devices are not available in all languages or in all countries or regions. Licensor makes no representation that such Services and Materials are appropriate or available for use in any particular location. To the extent you choose to use or access such Services and Materials, you do so at your own initiative and are responsible for compliance with any applicable laws, including but not limited to applicable local laws. Licensor reserves the right to change, suspend, remove, or disable access to any Services at any time without notice. In no event will Licensor be liable for the removal of or disabling of access to any such Services. Licensor may also impose limits on the use of or access to certain Services, in any case and without notice or liability.</p>\r\n<ol start="2">\r\n<li style="text-align: justify;"><strong>Proprietary Rights.</strong> The Licensed Application is protected by U.S. and international copyright, trademark and other intellectual property rights, statutory and common laws and international treaties. Licensor owns and retains all right, title and interest in and to the Licensed Application and related documentation, including but not limited to all copyrights, patents, trade secrets, trademarks and other intellectual property rights therein. Your possession, installation, or use of the Licensed Application does not transfer to you any title to the intellectual property in the Licensed Application and you will not acquire any rights in the Licensed Application, except for the limited license expressly granted herein. You may not remove any proprietary notices or labels in connection with the Licensed Application or related documentation. In the event of any third party claim that the Licensed Application or your possession and use of the Licensed Application infringes that third party&rsquo;s intellectual property rights, you will be responsible for the investigation, defense, settlement or discharge of any such intellectual property infringement claim.</li>\r\n<li style="text-align: justify;"><strong>NO WARRANTY.</strong> YOU EXPRESSLY ACKNOWLEDGE AND AGREE THAT USE OF THE LICENSED APPLICATION, AND ANY PRODUCTS PROVIDED BY LICENSOR FOR USE OR ASSOCIATED THEREWITH, IS AT YOUR SOLE RISK AND THAT THE ENTIRE RISK AS TO SATISFACTORY QUALITY, PERFORMANCE, ACCURACY, AND EFFORT IS WITH YOU. TO THE MAXIMUM EXTENT PERMITTED BY APPLICABLE LAW, THE LICENSED APPLICATION AND ANY SERVICES PERFORMED OR PROVIDED BY THE LICENSED APPLICATION ARE PROVIDED "AS IS" AND &ldquo;AS AVAILABLE&rdquo;, WITH ALL FAULTS AND WITHOUT WARRANTY OF ANY KIND, AND LICENSOR HEREBY DISCLAIMS ALL WARRANTIES AND CONDITIONS WITH RESPECT TO THE LICENSED APPLICATION AND ANY SERVICES, EITHER EXPRESS, IMPLIED, OR STATUTORY, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES AND/OR CONDITIONS OF MERCHANTABILITY, OF SATISFACTORY QUALITY, OF FITNESS FOR A PARTICULAR PURPOSE, OF ACCURACY, OF QUIET ENJOYMENT, AND OF NON-INFRINGEMENT OF THIRD-PARTY RIGHTS. LICENSOR DOES NOT WARRANT AGAINST INTERFERENCE WITH YOUR ENJOYMENT OF THE LICENSED APPLICATION, THAT THE FUNCTIONS CONTAINED IN OR SERVICES PERFORMED OR PROVIDED BY THE LICENSED APPLICATION WILL MEET YOUR REQUIREMENTS, THAT THE OPERATION OF THE LICENSED APPLICATION OR SERVICES WILL BE UNINTERRUPTED OR ERROR-FREE, OR THAT DEFECTS IN THE LICENSED APPLICATION OR SERVICES WILL BE CORRECTED. LICENSOR MAY CHANGE OR DISCONTINUE ANY ASPECT OR FEATURE OF THE LICENSED APPLICATION OR THE USE OF ALL OR ANY FEATURES OR TECHNOLOGY IN THE LICENSED APPLICATION OR THE THIRD PARTY CONTENT AT ANY TIME WITHOUT PRIOR NOTICE TO YOU. YOUR ONLY RIGHT OR REMEDY WITH RESPECT TO ANY PROBLEMS OR DISSATISFACTION WITH THE LICENSED APPLICATION IS TO UNINSTALL AND CEASE USE OF THE LICENSED APPLICATION. NO ORAL OR WRITTEN INFORMATION OR ADVICE GIVEN BY LICENSOR OR ITS AUTHORIZED REPRESENTATIVE SHALL CREATE A WARRANTY. SHOULD THE LICENSED APPLICATION OR SERVICES PROVE DEFECTIVE, YOU ASSUME THE ENTIRE COST OF ALL NECESSARY SERVICING, REPAIR, OR CORRECTION. SOME JURISDICTIONS DO NOT ALLOW THE EXCLUSION OF IMPLIED WARRANTIES OR LIMITATIONS ON APPLICABLE STATUTORY RIGHTS OF A CONSUMER, SO THE ABOVE EXCLUSION AND LIMITATIONS MAY NOT APPLY TO YOU. YOU MAY ALSO HAVE OTHER RIGHTS THAT VARY FROM JURISDICTION TO JURISDICTION THAT MAY NOT BE LIMITED BY THESE TERMS, PROVIDED HOWEVER, THAT YOU AGREE AND ACKNOWLEDGE THAT TO THE EXTENT PERMISSIBLE UNDER APPLICABLE LAW, YOU WAIVE ANY SUCH STATUTORY RIGHTS WITH RESPECT TO IMPLIED WARRANTIES.</li>\r\n<li style="text-align: justify;"><strong>Limitation of Liability</strong>. TO THE EXTENT NOT PROHIBITED BY LAW, IN NO EVENT SHALL LICENSOR BE LIABLE FOR PERSONAL INJURY OR ANY INCIDENTAL, SPECIAL, INDIRECT, OR CONSEQUENTIAL DAMAGES WHATSOEVER, INCLUDING, WITHOUT LIMITATION, DAMAGES FOR LOSS OF PROFITS, LOSS OF DATA, BUSINESS INTERRUPTION, OR ANY OTHER COMMERCIAL DAMAGES OR LOSSES, ARISING OUT OF OR RELATED TO YOUR USE OR INABILITY TO USE THE LICENSED APPLICATION, HOWEVER CAUSED, REGARDLESS OF THE THEORY OF LIABILITY (CONTRACT, TORT, OR OTHERWISE) AND EVEN IF LICENSOR HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES. SOME JURISDICTIONS DO NOT ALLOW THE LIMITATION OF LIABILITY FOR PERSONAL INJURY, OR OF INCIDENTAL OR CONSEQUENTIAL DAMAGES, SO THIS LIMITATION MAY NOT APPLY TO YOU. In no event shall Licensor&rsquo;s total liability to you for all damages (other than as may be required by applicable law in cases involving personal injury) exceed the amount of fifty dollars ($50.00). The foregoing limitations will apply even if the above stated remedy fails its essential purpose.</li>\r\n<li style="text-align: justify;"><strong>High Risk Activities. </strong>The Licensed Application and any products provided by Licensor for use or associated therewith are not fault-tolerant and are not designed or intended for use in hazardous environments that require fail-safe performance, including without limitation, in the operation of nuclear facilities, aircraft navigation or communication systems, air traffic control, weapons systems, direct life-support machines, or any other application in which the failure of the Licensed Application or associated products could lead directly to death, personal injury or severe physical or property damage, including without limitation, security services, and Licensor expressly disclaims any express or implied warranty of fitness for all such activities.</li>\r\n<li style="text-align: justify;"><strong>Export. </strong>You may not use or otherwise export or re-export the Licensed Application except as authorized by United States law and the laws of the jurisdiction in which the Licensed Application was obtained. In particular, but without limitation, the Licensed Application may not be exported or re-exported (a) into any U.S.-embargoed countries or (b) to anyone on the U.S. Treasury Departments Specially Designated Nationals List or the U.S. Department of Commerce Denied Persons List or Entity List. By using the Licensed Application, you represent and warrant that you are not located in any such country or on any such list. You also agree that you will not use these products for any purposes prohibited by United States law, including, without limitation, the development, design, manufacture, or production of nuclear, missile, or chemical or biological weapons.</li>\r\n<li style="text-align: justify;"><strong>Commercial Items.</strong> The Licensed Application and related documentation are "Commercial Items", as that term is defined at 48 C.F.R. &sect;2.101, consisting of "Commercial Computer Software" and "Commercial Computer Software Documentation", as such terms are used in 48 C.F.R. &sect;12.212 or 48 C.F.R. &sect;227.7202, as applicable. Consistent with 48 C.F.R. &sect;12.212 or 48 C.F.R. &sect;227.7202-1 through 227.7202-4, as applicable, the Commercial Computer Software and Commercial Computer Software Documentation are being licensed to U.S. Government end users (a) only as Commercial Items and (b) with only those rights as are granted to all other end users pursuant to the terms and conditions herein. Unpublished-rights reserved under the copyright laws of the United States.</li>\r\n<li style="text-align: justify;"><strong>Governing Law</strong>. The laws of the State of California, excluding its conflicts of law rules, govern this license and your use of the Licensed Application. Your use of the Licensed Application may also be subject to other local, state, national, or international laws. All actions relating to this Agreement shall be brought exclusively in a competent court in Los Angeles or Orange County, California, and you agree to personal jurisdiction in such courts.</li>\r\n<li style="text-align: justify;"><strong>Miscellaneous.</strong> (a) This Agreement and all the policies referenced herein constitute the entire agreement between Licensor and you concerning the subject matter hereof, and it may only be modified by a written amendment signed by an authorized executive of Licensor. (b) The section titles in this Agreement are provided solely for convenience and have no legal or contractual significance. (c) The United Nations Convention on Contracts for the International Sale of Goods is expressly excluded. (d) The failure of either party to enforce any rights granted hereunder or to take action against the other party in the event of any breach hereunder shall not be deemed a waiver by that party as to subsequent enforcement of rights or subsequent actions in the event of future breaches. (e) If for any reason a court of competent jurisdiction finds any provision of this Agreement or portion thereof, to be unenforceable, that provision of this Agreement shall be enforced to the maximum extent permissible so as to affect the intent of the parties or as necessary shall be deemed severable from this Agreement, and the remainder of this Agreement shall continue in full force and effect. (f) You may not assign your rights under this Agreement to any party.</li>\r\n<li style="text-align: justify;">&nbsp;Licensor reserves the right to modify and/or change any of the terms and conditions of this Agreement at any time and without prior notice. If Licensor materially modifies this Agreement, it will post the updated Agreement as part of a drop down menu from the Licensed Application via a hyperlink or by other reasonable means now known or hereafter developed. Licensor will also update the &ldquo;Last Updated Date&rdquo; at the end of the Agreement. By continuing to use the Licensed Application after Licensor has posted a modification to the Agreement, you agree to be bound by the modified Agreement. If the modified Agreement is not acceptable to you, your only recourse is to cease using the Licensed Application.</li>\r\n<li style="text-align: justify;"><strong>Questions, Comments, and Contact Information</strong>. If you have any questions, complaints and/or claims, you may contact Licensor at:</li>\r\n</ol>\r\n<p>Pacific Street Ventures</p>\r\n<p>125 Pacific Street #3, Santa Monica, CA 90405</p>\r\n<p>E-mail: chris@pacificstreetventures.com</p>\r\n</body>\r\n</html>', 'EULA', '2014-09-17 05:53:22', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `bal_parent_notification`
--

CREATE TABLE IF NOT EXISTS `bal_parent_notification` (
  `notification_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `notification_type` enum('APPUN','APPAR','APPIN','APPADMRM','REDEEM','UNLOCK','TROPHY','WAGER','IMAGE','LOCK','WEEKLYGOAL','SENDCHALLENGE','PAIR','NEW_SUBJECT','REWARD') DEFAULT 'APPUN',
  `description` varchar(255) DEFAULT NULL,
  `seen_by_user` enum('Y','N') DEFAULT 'N',
  `deleted` enum('Y','N') DEFAULT 'N',
  `child_device_id` int(10) unsigned NOT NULL,
  `childe_name` varchar(150) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT NULL,
  `child_id` int(10) NOT NULL,
  PRIMARY KEY (`notification_id`),
  KEY `fk_bal_parent_notification_user_id` (`user_id`),
  KEY `fk_bal_parent_notification_child_device_id` (`child_device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bal_products`
--

CREATE TABLE IF NOT EXISTS `bal_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `stock` int(11) NOT NULL,
  `client_id` int(50) NOT NULL,
  `price` int(50) NOT NULL,
  `status` enum('t','f') NOT NULL DEFAULT 't',
  `added_by` int(11) NOT NULL,
  `server_ip` varchar(50) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `bal_products`
--

INSERT INTO `bal_products` (`id`, `name`, `unit`, `stock`, `client_id`, `price`, `status`, `added_by`, `server_ip`, `created_date`, `modified_date`) VALUES
(1, 'Synthetic Thinner(N.C)', 'Litre', 300, 2, 50, 't', 1, '127.0.0.1', '2016-03-30 16:55:42', '2016-03-30 16:55:42'),
(2, 'Industrial Thinner(N.D)', 'Litre', 500, 2, 50, 't', 1, '127.0.0.1', '2016-03-30 16:55:38', '2016-03-30 16:55:38'),
(3, 'Chemical N.C', 'Kg', 400, 2, 50, 't', 1, '127.0.0.1', '2016-03-30 16:55:34', '2016-03-30 16:55:34'),
(4, 'Chemical Avily.', 'Kg', 500, 2, 80, 't', 1, '127.0.0.1', '2016-03-30 16:55:48', '2016-03-30 16:55:48'),
(5, 'Rung Cat', 'Litre', 500, 2, 50, 't', 1, '127.0.0.1', '2016-03-30 16:53:19', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `bal_purchase_order`
--

CREATE TABLE IF NOT EXISTS `bal_purchase_order` (
  `id` bigint(100) NOT NULL AUTO_INCREMENT COMMENT 'This is Purchase order id ',
  `client_id` int(11) NOT NULL COMMENT 'this is relatd to client table where we have store all client related data',
  `payment_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'the date when client will give payment',
  `delivery_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Final delivery date',
  `term` text NOT NULL,
  `condition` text NOT NULL,
  `order_for` varchar(50) NOT NULL,
  `status` enum('open','in-progress','complete','payment-received','on-hold') NOT NULL DEFAULT 'open' COMMENT 'By default order will be open, when we start adding invoice we will mark it in progress , we can aslo set is on hold if we want , If order has been copletde we will set completed and finally once we received payemtn we change the status',
  `added_by` int(11) NOT NULL,
  `server_ip` varchar(50) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10003 ;

--
-- Dumping data for table `bal_purchase_order`
--

INSERT INTO `bal_purchase_order` (`id`, `client_id`, `payment_date`, `delivery_date`, `term`, `condition`, `order_for`, `status`, `added_by`, `server_ip`, `created_date`, `modified_date`) VALUES
(10001, 1, '2016-04-04 16:00:00', '2016-04-12 07:00:00', 'No Terms are avalaible', 'No conditian just chill dude', 'Your Godown', 'complete', 1, '127.0.0.1', '2016-03-30 19:06:07', '2016-04-02 13:43:24'),
(10002, 1, '2016-03-31 07:00:00', '2016-03-31 07:00:00', 'No specific terms', 'No conditions maje kr yara tu bhi ', 'Your Godown', 'open', 1, '127.0.0.1', '2016-03-30 19:07:47', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `bal_timezone`
--

CREATE TABLE IF NOT EXISTS `bal_timezone` (
  `timezone_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `value_php` varchar(255) NOT NULL,
  `value_mysql` varchar(255) NOT NULL,
  PRIMARY KEY (`timezone_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `bal_timezone`
--

INSERT INTO `bal_timezone` (`timezone_id`, `title`, `value_php`, `value_mysql`) VALUES
(1, 'Hawaii-Aleutian (UTC -10:00)', 'Pacific/Honolulu', '-10:00'),
(2, 'Alaska (UTC -9:00)', 'America/Anchorage', '-9:00'),
(3, 'Pacific (UTC -8:00)', 'America/Los_Angeles', '-8:00'),
(4, 'Mountain (UTC -7:00)', 'America/Denver', '-7:00'),
(5, 'Central (UTC -6:00)', 'America/Chicago', '-6:00'),
(6, 'Eastern (UTC -5:00)', 'America/New_York', '-5:00');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
