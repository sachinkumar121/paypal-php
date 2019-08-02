-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 20, 2016 at 05:07 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `paypal`
--

-- --------------------------------------------------------

--
-- Table structure for table `token_details`
--

CREATE TABLE IF NOT EXISTS `token_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_cancelled` varchar(2) NOT NULL,
  `is_activated` varchar(2) NOT NULL,
  `token` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `token_details`
--

INSERT INTO `token_details` (`id`, `is_cancelled`, `is_activated`, `token`, `user_id`) VALUES
(2, '1', '0', 'EC-5TL918988L271694M', 121),
(3, '0', '1', 'EC-26J02905EM5775704', 121),
(4, '0', '0', 'EC-9MV555522L757021U', 121),
(5, '0', '0', 'EC-8G222139UV605941S', 121),
(6, '0', '0', 'EC-90G7652999288201C', 121),
(7, '0', '0', 'EC-1Y118160CU727410T', 121),
(8, '0', '0', 'EC-4H066239237528326', 121),
(9, '0', '0', 'EC-8UV45761Y1363371A', 121),
(10, '0', '0', 'EC-2AN33011MU996201X', 121);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_details`
--

CREATE TABLE IF NOT EXISTS `transaction_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `profile_id` varchar(255) NOT NULL,
  `profile_status` varchar(255) NOT NULL,
  `correlation_id` varchar(255) NOT NULL,
  `payer_id` varchar(255) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_desc` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `profile_start_date` datetime NOT NULL,
  `next_payment_date` datetime NOT NULL,
  `frequency` varchar(255) NOT NULL,
  `period` varchar(255) NOT NULL,
  `amt` varchar(255) NOT NULL,
  `currency_code` varchar(255) NOT NULL,
  `country_code` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `transaction_details`
--

INSERT INTO `transaction_details` (`id`, `token`, `user_id`, `profile_id`, `profile_status`, `correlation_id`, `payer_id`, `item_name`, `item_desc`, `created_at`, `profile_start_date`, `next_payment_date`, `frequency`, `period`, `amt`, `currency_code`, `country_code`) VALUES
(1, 'EC-8UV45761Y1363371A', 121, 'I-0XAP3GW9TAVE', 'ActiveProfile', '3cdde787200d1', '5X8TPPP2XW2Z2', 'subscribe', 'Agree for 1 month', '2016-11-20 15:18:11', '2016-11-21 16:00:00', '2016-12-21 16:00:00', '1', 'month', '100', 'USD', 'US'),
(2, 'EC-2AN33011MU996201X', 121, 'I-S5CVC07JMRTH', 'ActiveProfile', '3089732c16b02', '5X8TPPP2XW2Z2', 'subscribe', 'Agree for 1 month', '2016-11-20 16:00:14', '2016-11-21 16:00:00', '2016-12-21 16:00:00', '1', 'month', '100', 'USD', 'US');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
