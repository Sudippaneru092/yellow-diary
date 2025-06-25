-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2025 at 11:47 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `yellow_diarys`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `full_name`, `email`, `created_at`) VALUES
(1, 'admin', '$2y$10$gPQLmWZAqAyzdb0vlxCqXe670ogjW8oToEVx8A.1fIldtDz/dhhjm', 'Admin User', 'admin@example.com', '2025-06-09 07:42:52');

-- --------------------------------------------------------

--
-- Table structure for table `areas`
--

CREATE TABLE `areas` (
  `id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `areas`
--

INSERT INTO `areas` (`id`, `city_id`, `name`, `slug`, `created_at`) VALUES
(2, 52, 'hasanpur-5', 'hasanpur-5', '2025-06-14 01:09:22'),
(3, 1, 'Thamel', 'thamel', '2025-06-14 01:46:54'),
(4, 1, 'Patan', 'patan', '2025-06-14 01:46:54'),
(5, 2, 'Dharan', 'dharan', '2025-06-14 01:46:54'),
(6, 3, 'Biratnagar', 'biratnagar', '2025-06-14 01:46:54'),
(7, 1, 'Bhatbhateni', 'bhatbhateni', '2025-06-14 01:46:54'),
(8, 1, 'Putalisadak', 'putalisadak', '2025-06-14 01:46:54'),
(9, 1, 'Lalitpur', 'lalitpur', '2025-06-14 01:46:54'),
(10, 4, 'Balkot', 'balkot', '2025-06-14 01:46:54'),
(11, 5, 'Jhapa', 'jhapa', '2025-06-14 01:46:54'),
(12, 52, 'hasanpur-5', 'hasanpur-5', '2025-06-14 03:35:43');

-- --------------------------------------------------------

--
-- Table structure for table `businesses`
--

CREATE TABLE `businesses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `active_from` datetime DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `businesses`
--

INSERT INTO `businesses` (`id`, `name`, `category`, `location`, `description`, `image`, `url`, `email`, `phone`, `password`, `slug`, `status`, `created_at`, `active_from`, `latitude`, `longitude`, `last_login`, `reset_token`, `token_expiry`) VALUES
(1, 'nast', 'Education', 'dhangadhi', 'nast college', 'admin/uploads/business_images/1750171768_68518078e24a2.jpg', 'https://nast.edu.np/', 'nast@gmail.com', '9748258100', '$2y$10$qBAX8EFDPElscU1AMldie.ZMpbJhIZURXD9TGqmRfPx2myTCKeTBK', 'nast', 'approved', '2025-06-17 20:34:29', NULL, 0.00000000, 0.00000000, '2025-06-25 06:57:58', NULL, NULL),
(2, 'YS Digital Production', 'Gyms', 'Baitadi Municipality, 11', 'uururuurur', 'uploads/business_images/1750174421_591684987cb09f15e3e1b6faa54abf38.jpg', '', NULL, NULL, '', '', 'approved', '2025-06-17 21:18:41', NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'SPA', 'Education', 'dhangadhi', 'If you\'re aiming for a well-rounded academic experience in Dhangadhi—with strong TU affiliations, scholarships, startup support, and active community engagement—SPA College is an excellent choice.', 'admin/uploads/business_images/1750229004_6852600cd4fe8.png', 'https://spa.edu.np/', 'spa12@gmail.com', '+977‑91‑525785', '$2y$10$d9.D3KHar7qosQ1ofBdCNODVvOuSFtSrjYvBnv2IngzZ5nvj72Kg.', 'spa', 'approved', '2025-06-18 12:28:25', NULL, 0.00000000, 0.00000000, '2025-06-25 05:09:00', NULL, NULL),
(5, 'hhh', 'Automobiles', 'Bardibas Municipality, 9', 'gfhf', 'admin/uploads/business_images/1750829588_8d64c8d116fb90727c045661938951fe.jpg', 'https://chatgpt.com/c/685b864d-02e8-8004-bf8c-357917ce010f', NULL, NULL, '', 'hhh', 'approved', '2025-06-25 11:18:08', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Hotels', 'Listings related to hotel services', '2025-06-11 22:02:46'),
(2, 'Schools', 'Educational institutions', '2025-06-11 22:02:46'),
(3, 'Restaurants', 'Food and dining places', '2025-06-11 22:02:46'),
(4, 'Hospitals', 'Health care providers', '2025-06-11 22:02:46'),
(5, 'Shops', 'Local shops and retail businesses....', '2025-06-11 22:02:46'),
(7, 'Banks', 'Financial institutions and banks', '2025-06-11 22:02:46'),
(8, 'Pharmacies', 'Medicine and health supplies', '2025-06-11 22:02:46'),
(9, 'Gyms', 'Fitness centers and gyms', '2025-06-11 22:02:46'),
(10, 'Salons', 'Beauty and hair salons', '2025-06-11 22:02:46'),
(11, 'Tourism', 'Tourist attractions and travel services', '2025-06-11 22:02:46'),
(12, 'Transportation', 'Public transport and vehicle services', '2025-06-11 22:02:46'),
(13, 'Real Estate', 'Property sales and rentals', '2025-06-11 22:02:46'),
(14, 'Events', 'Events and event management', '2025-06-11 22:02:46'),
(15, 'Government Offices', 'Local government and administrative offices', '2025-06-11 22:02:46'),
(16, 'Electronics', 'Electronics shops and services', '2025-06-11 22:02:46'),
(17, 'Restaurants Fast Food', 'Fast food outlets and quick bites', '2025-06-11 22:02:46'),
(18, 'Automobiles', 'Car dealers and automobile services', '2025-06-11 22:02:46'),
(19, 'IT Services', 'Information technology and software companies', '2025-06-11 22:02:46'),
(20, 'Education Coaching', 'Coaching centers and tuition', '2025-06-11 22:02:46'),
(21, 'Clothing', 'Apparel and clothing stores', '2025-06-11 22:02:46'),
(22, 'Legal Services', 'Lawyers, advocates, and legal consultants', '2025-06-11 22:02:46'),
(23, 'Courier Services', 'Parcel and delivery services', '2025-06-11 22:02:46'),
(24, 'Photography', 'Photographers and studios', '2025-06-11 22:02:46'),
(25, 'Furniture', 'Furniture stores and carpentry services', '2025-06-11 22:02:46'),
(26, 'Cleaning Services', 'Home and office cleaning services', '2025-06-11 22:02:46'),
(27, 'Car Rentals', 'Vehicle rental and leasing services', '2025-06-11 22:02:46'),
(28, 'Hotels Budget', 'Affordable budget hotels and lodgings', '2025-06-11 22:02:46'),
(29, 'Travel Agencies', 'Travel and tour operators', '2025-06-11 22:02:46'),
(30, 'Jewelry', 'Jewelry shops and repair', '2025-06-11 22:02:46'),
(31, 'Restaurants Fine Dining', 'High-end dining restaurants', '2025-06-11 22:02:46'),
(32, 'Medical Clinics', 'Specialist clinics and diagnostic centers', '2025-06-11 22:02:46'),
(33, 'Crafts & Gifts', 'Handicrafts and gift shops', '2025-06-11 22:02:46'),
(34, 'Sports', 'Sports shops and facilities', '2025-06-11 22:02:46'),
(35, 'Mobile & Electronics Repair', 'Repair services for mobiles and electronics', '2025-06-11 22:02:46'),
(36, 'Restaurants Cafes', 'Coffee shops and cafes', '2025-06-11 22:02:46'),
(37, 'Education Universities', 'Universities and higher education', '2025-06-11 22:02:46'),
(38, 'Agriculture', 'Farming supplies and agriculture services', '2025-06-11 22:02:46'),
(39, 'Insurance', 'Insurance providers and agents', '2025-06-11 22:02:46'),
(40, 'IT Hardware', 'Computer and IT hardware stores', '2025-06-11 22:02:46'),
(41, 'Construction', 'Construction companies and contractors', '2025-06-11 22:02:46'),
(42, 'Beauty Clinics', 'Skin care and beauty clinics', '2025-06-11 22:02:46'),
(43, 'Laundry Services', 'Laundry and dry cleaning services', '2025-06-11 22:02:46'),
(44, 'Event Venues', 'Places to host events and parties', '2025-06-11 22:02:46'),
(45, 'Hotels Boutique', 'Boutique and luxury hotels', '2025-06-11 22:02:46'),
(46, 'Pet Services', 'Pet care, grooming, and veterinary', '2025-06-11 22:02:46'),
(47, 'Car Repair', 'Auto repair and servicing', '2025-06-11 22:02:46'),
(48, 'Printing Services', 'Printing and photocopy services', '2025-06-11 22:02:46'),
(49, 'Bookstores', 'Book shops and stationery', '2025-06-11 22:02:46'),
(50, 'Home Decor', 'Home decoration and interior design', '2025-06-11 22:02:46'),
(51, 'Transportation Services', 'Taxi, cab, and shuttle services', '2025-06-11 22:02:46'),
(52, 'Cafeterias', 'Casual dining and cafeterias', '2025-06-11 22:02:46'),
(53, 'Photography Studios', 'Professional photography studios', '2025-06-11 22:02:46'),
(54, 'Travel Gear', 'Travel accessories and gear shops', '2025-06-11 22:02:46'),
(55, 'Pharmaceuticals', 'Pharmaceutical companies and stores', '2025-06-11 22:02:46'),
(56, 'IT Support', 'Tech support and managed services', '2025-06-11 22:02:46'),
(57, 'Gardening Services', 'Landscaping and gardening', '2025-06-11 22:02:46'),
(58, 'Security Services', 'Private security and surveillance', '2025-06-11 22:02:46'),
(59, 'Financial Services', 'Accounting, auditing, and consulting', '2025-06-11 22:02:46'),
(60, 'Transportation Equipment', 'Vehicle and transportation equipment', '2025-06-11 22:02:46'),
(61, 'Wedding Services', 'Wedding planning and related services', '2025-06-11 22:02:46');

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `is_popular` tinyint(1) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `name`, `province`, `district`, `type`, `slug`, `is_popular`, `image`, `created_at`) VALUES
(1, 'Kathmandu Metropolitan City', 'Bagmati Province', 'Kathmandu', 'Metropolitan', 'kathmandu-metropolitan-city', 1, '', '2025-06-14 01:36:22'),
(2, 'Lalitpur Metropolitan City', 'Bagmati Province', 'Lalitpur', 'Metropolitan', 'lalitpur-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(3, 'Bharatpur Metropolitan City', 'Bagmati Province', 'Chitwan', 'Metropolitan', 'bharatpur-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(4, 'Pokhara Metropolitan City', 'Gandaki Province', 'Kaski', 'Metropolitan', 'pokhara-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(5, 'Biratnagar Metropolitan City', 'Koshi Province', 'Morang', 'Metropolitan', 'biratnagar-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(6, 'Dharan Sub-Metropolitan City', 'Koshi Province', 'Sunsari', 'Sub-Metropolitan', 'dharan-sub-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(7, 'Itahari Sub-Metropolitan City', 'Koshi Province', 'Sunsari', 'Sub-Metropolitan', 'itahari-sub-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(8, 'Janakpur Sub-Metropolitan City', 'Madhesh Province', 'Dhanusha', 'Sub-Metropolitan', 'janakpur-sub-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(9, 'Hetauda Sub-Metropolitan City', 'Bagmati Province', 'Makwanpur', 'Sub-Metropolitan', 'hetauda-sub-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(10, 'Butwal Sub-Metropolitan City', 'Lumbini Province', 'Rupandehi', 'Sub-Metropolitan', 'butwal-sub-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(11, 'Bhaktapur Municipality', 'Bagmati Province', 'Bhaktapur', 'Municipality', 'bhaktapur-municipality', 0, NULL, '2025-06-14 01:36:22'),
(12, 'Kavrepalanchok Municipality', 'Bagmati Province', 'Kavrepalanchok', 'Municipality', 'kavrepalanchok-municipality', 0, NULL, '2025-06-14 01:36:22'),
(13, 'Chitwan Municipality', 'Bagmati Province', 'Chitwan', 'Municipality', 'chitwan-municipality', 0, NULL, '2025-06-14 01:36:22'),
(14, 'Lamjung Municipality', 'Gandaki Province', 'Lamjung', 'Municipality', 'lamjung-municipality', 0, NULL, '2025-06-14 01:36:22'),
(15, 'Tanahun Municipality', 'Gandaki Province', 'Tanahun', 'Municipality', 'tanahun-municipality', 0, NULL, '2025-06-14 01:36:22'),
(16, 'Sunsari Municipality', 'Koshi Province', 'Sunsari', 'Municipality', 'sunsari-municipality', 0, NULL, '2025-06-14 01:36:22'),
(17, 'Morang Municipality', 'Koshi Province', 'Morang', 'Municipality', 'morang-municipality', 0, NULL, '2025-06-14 01:36:22'),
(18, 'Dhanusha Municipality', 'Madhesh Province', 'Dhanusha', 'Municipality', 'dhanusha-municipality', 0, NULL, '2025-06-14 01:36:22'),
(19, 'Rautahat Municipality', 'Madhesh Province', 'Rautahat', 'Municipality', 'rautahat-municipality', 0, NULL, '2025-06-14 01:36:22'),
(20, 'Kapilvastu Municipality', 'Lumbini Province', 'Kapilvastu', 'Municipality', 'kapilvastu-municipality', 0, NULL, '2025-06-14 01:36:22'),
(21, 'Palpa Municipality', 'Lumbini Province', 'Palpa', 'Municipality', 'palpa-municipality', 0, NULL, '2025-06-14 01:36:22'),
(22, 'Gulmi Municipality', 'Lumbini Province', 'Gulmi', 'Municipality', 'gulmi-municipality', 0, NULL, '2025-06-14 01:36:22'),
(23, 'Dang Municipality', 'Lumbini Province', 'Dang', 'Municipality', 'dang-municipality', 0, NULL, '2025-06-14 01:36:22'),
(24, 'Surkhet Municipality', 'Karnali Province', 'Surkhet', 'Municipality', 'surkhet-municipality', 0, NULL, '2025-06-14 01:36:22'),
(25, 'Jumla Municipality', 'Karnali Province', 'Jumla', 'Municipality', 'jumla-municipality', 0, NULL, '2025-06-14 01:36:22'),
(26, 'Dailekh Municipality', 'Karnali Province', 'Dailekh', 'Municipality', 'dailekh-municipality', 0, NULL, '2025-06-14 01:36:22'),
(27, 'Doti Municipality', 'Sudurpashchim Province', 'Doti', 'Municipality', 'doti-municipality', 0, NULL, '2025-06-14 01:36:22'),
(28, 'Kailali Municipality', 'Sudurpashchim Province', 'Kailali', 'Municipality', 'kailali-municipality', 0, NULL, '2025-06-14 01:36:22'),
(29, 'Bajhang Municipality', 'Sudurpashchim Province', 'Bajhang', 'Municipality', 'bajhang-municipality', 0, NULL, '2025-06-14 01:36:22'),
(30, 'Bajura Municipality', 'Sudurpashchim Province', 'Bajura', 'Municipality', 'bajura-municipality', 0, NULL, '2025-06-14 01:36:22'),
(31, 'Mahottari Municipality', 'Madhesh Province', 'Mahottari', 'Municipality', 'mahottari-municipality', 0, NULL, '2025-06-14 01:36:22'),
(32, 'Siraha Municipality', 'Madhesh Province', 'Siraha', 'Municipality', 'siraha-municipality', 0, NULL, '2025-06-14 01:36:22'),
(33, 'Saptari Municipality', 'Madhesh Province', 'Saptari', 'Municipality', 'saptari-municipality', 0, NULL, '2025-06-14 01:36:22'),
(34, 'Ramechhap Municipality', 'Bagmati Province', 'Ramechhap', 'Municipality', 'ramechhap-municipality', 0, NULL, '2025-06-14 01:36:22'),
(35, 'Sindhuli Municipality', 'Bagmati Province', 'Sindhuli', 'Municipality', 'sindhuli-municipality', 0, NULL, '2025-06-14 01:36:22'),
(36, 'Dolakha Municipality', 'Bagmati Province', 'Dolakha', 'Municipality', 'dolakha-municipality', 0, NULL, '2025-06-14 01:36:22'),
(37, 'Okhaldhunga Municipality', 'Koshi Province', 'Okhaldhunga', 'Municipality', 'okhaldhunga-municipality', 0, NULL, '2025-06-14 01:36:22'),
(38, 'Sankhuwasabha Municipality', 'Koshi Province', 'Sankhuwasabha', 'Municipality', 'sankhuwasabha-municipality', 0, NULL, '2025-06-14 01:36:22'),
(39, 'Terhathum Municipality', 'Koshi Province', 'Terhathum', 'Municipality', 'terhathum-municipality', 0, NULL, '2025-06-14 01:36:22'),
(40, 'Ilam Municipality', 'Koshi Province', 'Ilam', 'Municipality', 'ilam-municipality', 0, NULL, '2025-06-14 01:36:22'),
(41, 'Bardibas Municipality', 'Madhesh Province', 'Mahottari', 'Municipality', 'bardibas-municipality', 0, NULL, '2025-06-14 01:36:22'),
(42, 'Jaleshwor Municipality', 'Madhesh Province', 'Mahottari', 'Municipality', 'jaleshwor-municipality', 0, NULL, '2025-06-14 01:36:22'),
(43, 'Lahan Municipality', 'Madhesh Province', 'Siraha', 'Municipality', 'lahan-municipality', 0, NULL, '2025-06-14 01:36:22'),
(44, 'Rajbiraj Municipality', 'Madhesh Province', 'Saptari', 'Municipality', 'rajbiraj-municipality', 0, NULL, '2025-06-14 01:36:22'),
(45, 'Bhojpur Municipality', 'Koshi Province', 'Bhojpur', 'Municipality', 'bhojpur-municipality', 0, NULL, '2025-06-14 01:36:22'),
(46, 'Dharan Municipality', 'Koshi Province', 'Sunsari', 'Municipality', 'dharan-municipality', 0, NULL, '2025-06-14 01:36:22'),
(47, 'Birgunj Metropolitan City', 'Madhesh Province', 'Parsa', 'Metropolitan', 'birgunj-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(48, 'Kalaiya Sub-Metropolitan City', 'Madhesh Province', 'Bara', 'Sub-Metropolitan', 'kalaiya-sub-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(49, 'Gaur Municipality', 'Madhesh Province', 'Rautahat', 'Municipality', 'gaur-municipality', 0, NULL, '2025-06-14 01:36:22'),
(50, 'Rajapur Municipality', 'Sudurpashchim Province', 'Kailali', 'Municipality', 'rajapur-municipality', 0, NULL, '2025-06-14 01:36:22'),
(51, 'Tikapur Municipality', 'Sudurpashchim Province', 'Kailali', 'Municipality', 'tikapur-municipality', 0, NULL, '2025-06-14 01:36:22'),
(52, 'Dhangadhi Sub-Metropolitan City', 'Sudurpashchim Province', 'Kailali', 'Sub-Metropolitan', 'dhangadhi-sub-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(53, 'Mahendranagar Municipality', 'Sudurpashchim Province', 'Kanchanpur', 'Municipality', 'mahendranagar-municipality', 0, NULL, '2025-06-14 01:36:22'),
(54, 'Dadeldhura Municipality', 'Sudurpashchim Province', 'Dadeldhura', 'Municipality', 'dadeldhura-municipality', 0, NULL, '2025-06-14 01:36:22'),
(55, 'Baitadi Municipality', 'Sudurpashchim Province', 'Baitadi', 'Municipality', 'baitadi-municipality', 0, NULL, '2025-06-14 01:36:22'),
(56, 'Darchula Municipality', 'Sudurpashchim Province', 'Darchula', 'Municipality', 'darchula-municipality', 0, NULL, '2025-06-14 01:36:22'),
(57, 'Bhimdatta Municipality', 'Sudurpashchim Province', 'Kanchanpur', 'Municipality', 'bhimdatta-municipality', 0, NULL, '2025-06-14 01:36:22'),
(58, 'Jumla Municipality', 'Karnali Province', 'Jumla', 'Municipality', 'jumla-municipality', 0, NULL, '2025-06-14 01:36:22'),
(59, 'Chandannath Municipality', 'Karnali Province', 'Jajarkot', 'Municipality', 'chandannath-municipality', 0, NULL, '2025-06-14 01:36:22'),
(60, 'Surkhet Municipality', 'Karnali Province', 'Surkhet', 'Municipality', 'surkhet-municipality', 0, NULL, '2025-06-14 01:36:22'),
(61, 'Birendranagar Municipality', 'Karnali Province', 'Surkhet', 'Municipality', 'birendranagar-municipality', 0, NULL, '2025-06-14 01:36:22'),
(62, 'Tulsipur Sub-Metropolitan City', 'Lumbini Province', 'Dang', 'Sub-Metropolitan', 'tulsipur-sub-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(63, 'Ghorahi Sub-Metropolitan City', 'Lumbini Province', 'Dang', 'Sub-Metropolitan', 'ghorahi-sub-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(64, 'Nepalgunj Sub-Metropolitan City', 'Lumbini Province', 'Banke', 'Sub-Metropolitan', 'nepalgunj-sub-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(65, 'Kohalpur Municipality', 'Lumbini Province', 'Banke', 'Municipality', 'kohalpur-municipality', 0, NULL, '2025-06-14 01:36:22'),
(66, 'Bardiya Municipality', 'Lumbini Province', 'Bardiya', 'Municipality', 'bardiya-municipality', 0, NULL, '2025-06-14 01:36:22'),
(67, 'Rajapur Municipality', 'Lumbini Province', 'Bardiya', 'Municipality', 'rajapur-municipality', 0, NULL, '2025-06-14 01:36:22'),
(68, 'Banke Municipality', 'Lumbini Province', 'Banke', 'Municipality', 'banke-municipality', 0, NULL, '2025-06-14 01:36:22'),
(69, 'Salyan Municipality', 'Karnali Province', 'Salyan', 'Municipality', 'salyan-municipality', 0, NULL, '2025-06-14 01:36:22'),
(70, 'Rolpa Municipality', 'Lumbini Province', 'Rolpa', 'Municipality', 'rolpa-municipality', 0, NULL, '2025-06-14 01:36:22'),
(71, 'Rukum Municipality', 'Lumbini Province', 'Rukum', 'Municipality', 'rukum-municipality', 0, NULL, '2025-06-14 01:36:22'),
(72, 'Pyuthan Municipality', 'Lumbini Province', 'Pyuthan', 'Municipality', 'pyuthan-municipality', 0, NULL, '2025-06-14 01:36:22'),
(73, 'Gulmi Municipality', 'Lumbini Province', 'Gulmi', 'Municipality', 'gulmi-municipality', 0, NULL, '2025-06-14 01:36:22'),
(74, 'Palpa Municipality', 'Lumbini Province', 'Palpa', 'Municipality', 'palpa-municipality', 0, NULL, '2025-06-14 01:36:22'),
(75, 'Arghakhanchi Municipality', 'Lumbini Province', 'Arghakhanchi', 'Municipality', 'arghakhanchi-municipality', 0, NULL, '2025-06-14 01:36:22'),
(76, 'Kapilvastu Municipality', 'Lumbini Province', 'Kapilvastu', 'Municipality', 'kapilvastu-municipality', 0, NULL, '2025-06-14 01:36:22'),
(77, 'Dang Municipality', 'Lumbini Province', 'Dang', 'Municipality', 'dang-municipality', 0, NULL, '2025-06-14 01:36:22'),
(78, 'Nawalparasi Municipality', 'Lumbini Province', 'Nawalparasi', 'Municipality', 'nawalparasi-municipality', 0, NULL, '2025-06-14 01:36:22'),
(79, 'Rupandehi Municipality', 'Lumbini Province', 'Rupandehi', 'Municipality', 'rupandehi-municipality', 0, NULL, '2025-06-14 01:36:22'),
(80, 'Parsa Municipality', 'Madhesh Province', 'Parsa', 'Municipality', 'parsa-municipality', 0, NULL, '2025-06-14 01:36:22'),
(81, 'Sunsari Municipality', 'Koshi Province', 'Sunsari', 'Municipality', 'sunsari-municipality', 0, NULL, '2025-06-14 01:36:22'),
(82, 'Morang Municipality', 'Koshi Province', 'Morang', 'Municipality', 'morang-municipality', 0, NULL, '2025-06-14 01:36:22'),
(83, 'Dhankuta Municipality', 'Koshi Province', 'Dhankuta', 'Municipality', 'dhankuta-municipality', 0, NULL, '2025-06-14 01:36:22'),
(84, 'Sankhuwasabha Municipality', 'Koshi Province', 'Sankhuwasabha', 'Municipality', 'sankhuwasabha-municipality', 0, NULL, '2025-06-14 01:36:22'),
(85, 'Terhathum Municipality', 'Koshi Province', 'Terhathum', 'Municipality', 'terhathum-municipality', 0, NULL, '2025-06-14 01:36:22'),
(86, 'Ilam Municipality', 'Koshi Province', 'Ilam', 'Municipality', 'ilam-municipality', 0, NULL, '2025-06-14 01:36:22'),
(87, 'Jhapa Municipality', 'Koshi Province', 'Jhapa', 'Municipality', 'jhapa-municipality', 0, NULL, '2025-06-14 01:36:22'),
(88, 'Panchthar Municipality', 'Koshi Province', 'Panchthar', 'Municipality', 'panchthar-municipality', 0, NULL, '2025-06-14 01:36:22'),
(89, 'Taplejung Municipality', 'Koshi Province', 'Taplejung', 'Municipality', 'taplejung-municipality', 0, NULL, '2025-06-14 01:36:22'),
(90, 'Bhojpur Municipality', 'Koshi Province', 'Bhojpur', 'Municipality', 'bhojpur-municipality', 0, NULL, '2025-06-14 01:36:22'),
(91, 'Dhading Municipality', 'Bagmati Province', 'Dhading', 'Municipality', 'dhading-municipality', 0, NULL, '2025-06-14 01:36:22'),
(92, 'Rasuwa Municipality', 'Bagmati Province', 'Rasuwa', 'Municipality', 'rasuwa-municipality', 0, NULL, '2025-06-14 01:36:22'),
(93, 'Sindhupalchok Municipality', 'Bagmati Province', 'Sindhupalchok', 'Municipality', 'sindhupalchok-municipality', 0, NULL, '2025-06-14 01:36:22'),
(94, 'Nuwakot Municipality', 'Bagmati Province', 'Nuwakot', 'Municipality', 'nuwakot-municipality', 0, NULL, '2025-06-14 01:36:22'),
(95, 'Ramechhap Municipality', 'Bagmati Province', 'Ramechhap', 'Municipality', 'ramechhap-municipality', 0, NULL, '2025-06-14 01:36:22'),
(96, 'Sindhuli Municipality', 'Bagmati Province', 'Sindhuli', 'Municipality', 'sindhuli-municipality', 0, NULL, '2025-06-14 01:36:22'),
(97, 'Dolakha Municipality', 'Bagmati Province', 'Dolakha', 'Municipality', 'dolakha-municipality', 0, NULL, '2025-06-14 01:36:22'),
(98, 'Kavrepalanchok Municipality', 'Bagmati Province', 'Kavrepalanchok', 'Municipality', 'kavrepalanchok-municipality', 0, NULL, '2025-06-14 01:36:22'),
(99, 'Chitwan Municipality', 'Bagmati Province', 'Chitwan', 'Municipality', 'chitwan-municipality', 0, NULL, '2025-06-14 01:36:22'),
(100, 'Makwanpur Municipality', 'Bagmati Province', 'Makwanpur', 'Municipality', 'makwanpur-municipality', 0, NULL, '2025-06-14 01:36:22'),
(101, 'Gorkha Municipality', 'Gandaki Province', 'Gorkha', 'Municipality', 'gorkha-municipality', 0, NULL, '2025-06-14 01:36:22'),
(102, 'Lamjung Municipality', 'Gandaki Province', 'Lamjung', 'Municipality', 'lamjung-municipality', 0, NULL, '2025-06-14 01:36:22'),
(103, 'Kaski Municipality', 'Gandaki Province', 'Kaski', 'Municipality', 'kaski-municipality', 0, NULL, '2025-06-14 01:36:22'),
(104, 'Syangja Municipality', 'Gandaki Province', 'Syangja', 'Municipality', 'syangja-municipality', 0, NULL, '2025-06-14 01:36:22'),
(105, 'Tanahun Municipality', 'Gandaki Province', 'Tanahun', 'Municipality', 'tanahun-municipality', 0, NULL, '2025-06-14 01:36:22'),
(106, 'Parbat Municipality', 'Gandaki Province', 'Parbat', 'Municipality', 'parbat-municipality', 0, NULL, '2025-06-14 01:36:22'),
(107, 'Baglung Municipality', 'Gandaki Province', 'Baglung', 'Municipality', 'baglung-municipality', 0, NULL, '2025-06-14 01:36:22'),
(108, 'Myagdi Municipality', 'Gandaki Province', 'Myagdi', 'Municipality', 'myagdi-municipality', 0, NULL, '2025-06-14 01:36:22'),
(109, 'Mustang Municipality', 'Gandaki Province', 'Mustang', 'Municipality', 'mustang-municipality', 0, NULL, '2025-06-14 01:36:22'),
(110, 'Manang Municipality', 'Gandaki Province', 'Manang', 'Municipality', 'manang-municipality', 0, NULL, '2025-06-14 01:36:22'),
(111, 'Dolpa Municipality', 'Karnali Province', 'Dolpa', 'Municipality', 'dolpa-municipality', 0, NULL, '2025-06-14 01:36:22'),
(112, 'Jumla Municipality', 'Karnali Province', 'Jumla', 'Municipality', 'jumla-municipality', 0, NULL, '2025-06-14 01:36:22'),
(113, 'Kalikot Municipality', 'Karnali Province', 'Kalikot', 'Municipality', 'kalikot-municipality', 0, NULL, '2025-06-14 01:36:22'),
(114, 'Mugu Municipality', 'Karnali Province', 'Mugu', 'Municipality', 'mugu-municipality', 0, NULL, '2025-06-14 01:36:22'),
(115, 'Humla Municipality', 'Karnali Province', 'Humla', 'Municipality', 'humla-municipality', 0, NULL, '2025-06-14 01:36:22'),
(116, 'Surkhet Municipality', 'Karnali Province', 'Surkhet', 'Municipality', 'surkhet-municipality', 0, NULL, '2025-06-14 01:36:22'),
(117, 'Dailekh Municipality', 'Karnali Province', 'Dailekh', 'Municipality', 'dailekh-municipality', 0, NULL, '2025-06-14 01:36:22'),
(118, 'Jajarkot Municipality', 'Karnali Province', 'Jajarkot', 'Municipality', 'jajarkot-municipality', 0, NULL, '2025-06-14 01:36:22'),
(119, 'Rukum Municipality', 'Lumbini Province', 'Rukum', 'Municipality', 'rukum-municipality', 0, NULL, '2025-06-14 01:36:22'),
(120, 'Rolpa Municipality', 'Lumbini Province', 'Rolpa', 'Municipality', 'rolpa-municipality', 0, NULL, '2025-06-14 01:36:22'),
(121, 'Tansen Municipality', 'Lumbini Province', 'Palpa', 'Municipality', 'tansen-municipality', 0, NULL, '2025-06-14 01:36:22'),
(122, 'Butwal Sub-Metropolitan City', 'Lumbini Province', 'Rupandehi', 'Sub-Metropolitan', 'butwal-sub-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(123, 'Gaidakot Municipality', 'Lumbini Province', 'Nawalparasi', 'Municipality', 'gaidakot-municipality', 0, NULL, '2025-06-14 01:36:22'),
(124, 'Bhairahawa Municipality', 'Lumbini Province', 'Rupandehi', 'Municipality', 'bhairahawa-municipality', 0, NULL, '2025-06-14 01:36:22'),
(125, 'Kapilvastu Municipality', 'Lumbini Province', 'Kapilvastu', 'Municipality', 'kapilvastu-municipality', 0, NULL, '2025-06-14 01:36:22'),
(126, 'Bardaghat Municipality', 'Lumbini Province', 'Nawalparasi', 'Municipality', 'bardaghat-municipality', 0, NULL, '2025-06-14 01:36:22'),
(127, 'Hetauda Sub-Metropolitan City', 'Bagmati Province', 'Makwanpur', 'Sub-Metropolitan', 'hetauda-sub-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(128, 'Kathmandu Metropolitan City', 'Bagmati Province', 'Kathmandu', 'Metropolitan', 'kathmandu-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(129, 'Lalitpur Metropolitan City', 'Bagmati Province', 'Lalitpur', 'Metropolitan', 'lalitpur-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(130, 'Bhaktapur Municipality', 'Bagmati Province', 'Bhaktapur', 'Municipality', 'bhaktapur-municipality', 0, NULL, '2025-06-14 01:36:22'),
(131, 'Pokhara Metropolitan City', 'Gandaki Province', 'Kaski', 'Metropolitan', 'pokhara-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(132, 'Dhangadhi Sub-Metropolitan City', 'Sudurpashchim Province', 'Kailali', 'Sub-Metropolitan', 'dhangadhi-sub-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(133, 'Mahendranagar Municipality', 'Sudurpashchim Province', 'Kanchanpur', 'Municipality', 'mahendranagar-municipality', 0, NULL, '2025-06-14 01:36:22'),
(134, 'Birendranagar Municipality', 'Karnali Province', 'Surkhet', 'Municipality', 'birendranagar-municipality', 0, NULL, '2025-06-14 01:36:22'),
(135, 'Tulsipur Sub-Metropolitan City', 'Lumbini Province', 'Dang', 'Sub-Metropolitan', 'tulsipur-sub-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(136, 'Ghorahi Sub-Metropolitan City', 'Lumbini Province', 'Dang', 'Sub-Metropolitan', 'ghorahi-sub-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(137, 'Nepalgunj Sub-Metropolitan City', 'Lumbini Province', 'Banke', 'Sub-Metropolitan', 'nepalgunj-sub-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(138, 'Kohalpur Municipality', 'Lumbini Province', 'Banke', 'Municipality', 'kohalpur-municipality', 0, NULL, '2025-06-14 01:36:22'),
(139, 'Rajbiraj Municipality', 'Madhesh Province', 'Saptari', 'Municipality', 'rajbiraj-municipality', 0, NULL, '2025-06-14 01:36:22'),
(140, 'Birgunj Metropolitan City', 'Madhesh Province', 'Parsa', 'Metropolitan', 'birgunj-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(141, 'Kalaiya Sub-Metropolitan City', 'Madhesh Province', 'Bara', 'Sub-Metropolitan', 'kalaiya-sub-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(142, 'Gaur Municipality', 'Madhesh Province', 'Rautahat', 'Municipality', 'gaur-municipality', 0, NULL, '2025-06-14 01:36:22'),
(143, 'Bardibas Municipality', 'Madhesh Province', 'Mahottari', 'Municipality', 'bardibas-municipality', 0, NULL, '2025-06-14 01:36:22'),
(144, 'Jaleshwor Municipality', 'Madhesh Province', 'Mahottari', 'Municipality', 'jaleshwor-municipality', 0, NULL, '2025-06-14 01:36:22'),
(145, 'Lahan Municipality', 'Madhesh Province', 'Siraha', 'Municipality', 'lahan-municipality', 0, NULL, '2025-06-14 01:36:22'),
(146, 'Sarlahi Municipality', 'Madhesh Province', 'Sarlahi', 'Municipality', 'sarlahi-municipality', 0, NULL, '2025-06-14 01:36:22'),
(147, 'Malangwa Municipality', 'Madhesh Province', 'Sarlahi', 'Municipality', 'malangwa-municipality', 0, NULL, '2025-06-14 01:36:22'),
(148, 'Rajapur Municipality', 'Sudurpashchim Province', 'Kailali', 'Municipality', 'rajapur-municipality', 0, NULL, '2025-06-14 01:36:22'),
(149, 'Tikapur Municipality', 'Sudurpashchim Province', 'Kailali', 'Municipality', 'tikapur-municipality', 0, NULL, '2025-06-14 01:36:22'),
(150, 'Dadeldhura Municipality', 'Sudurpashchim Province', 'Dadeldhura', 'Municipality', 'dadeldhura-municipality', 0, NULL, '2025-06-14 01:36:22'),
(151, 'Baitadi Municipality', 'Sudurpashchim Province', 'Baitadi', 'Municipality', 'baitadi-municipality', 0, NULL, '2025-06-14 01:36:22'),
(152, 'Darchula Municipality', 'Sudurpashchim Province', 'Darchula', 'Municipality', 'darchula-municipality', 0, NULL, '2025-06-14 01:36:22'),
(153, 'Bhimdatta Municipality', 'Sudurpashchim Province', 'Kanchanpur', 'Municipality', 'bhimdatta-municipality', 0, NULL, '2025-06-14 01:36:22'),
(154, 'Jumla Municipality', 'Karnali Province', 'Jumla', 'Municipality', 'jumla-municipality', 0, NULL, '2025-06-14 01:36:22'),
(155, 'Chandannath Municipality', 'Karnali Province', 'Jajarkot', 'Municipality', 'chandannath-municipality', 0, NULL, '2025-06-14 01:36:22'),
(156, 'Surkhet Municipality', 'Karnali Province', 'Surkhet', 'Municipality', 'surkhet-municipality', 0, NULL, '2025-06-14 01:36:22'),
(157, 'Salyan Municipality', 'Karnali Province', 'Salyan', 'Municipality', 'salyan-municipality', 0, NULL, '2025-06-14 01:36:22'),
(158, 'Rolpa Municipality', 'Lumbini Province', 'Rolpa', 'Municipality', 'rolpa-municipality', 0, NULL, '2025-06-14 01:36:22'),
(159, 'Rukum Municipality', 'Lumbini Province', 'Rukum', 'Municipality', 'rukum-municipality', 0, NULL, '2025-06-14 01:36:22'),
(160, 'Pyuthan Municipality', 'Lumbini Province', 'Pyuthan', 'Municipality', 'pyuthan-municipality', 0, NULL, '2025-06-14 01:36:22'),
(161, 'Gulmi Municipality', 'Lumbini Province', 'Gulmi', 'Municipality', 'gulmi-municipality', 0, NULL, '2025-06-14 01:36:22'),
(162, 'Palpa Municipality', 'Lumbini Province', 'Palpa', 'Municipality', 'palpa-municipality', 0, NULL, '2025-06-14 01:36:22'),
(163, 'Arghakhanchi Municipality', 'Lumbini Province', 'Arghakhanchi', 'Municipality', 'arghakhanchi-municipality', 0, NULL, '2025-06-14 01:36:22'),
(164, 'Kapilvastu Municipality', 'Lumbini Province', 'Kapilvastu', 'Municipality', 'kapilvastu-municipality', 0, NULL, '2025-06-14 01:36:22'),
(165, 'Dang Municipality', 'Lumbini Province', 'Dang', 'Municipality', 'dang-municipality', 0, NULL, '2025-06-14 01:36:22'),
(166, 'Nawalparasi Municipality', 'Lumbini Province', 'Nawalparasi', 'Municipality', 'nawalparasi-municipality', 0, NULL, '2025-06-14 01:36:22'),
(167, 'Rupandehi Municipality', 'Lumbini Province', 'Rupandehi', 'Municipality', 'rupandehi-municipality', 0, NULL, '2025-06-14 01:36:22'),
(168, 'Parsa Municipality', 'Madhesh Province', 'Parsa', 'Municipality', 'parsa-municipality', 0, NULL, '2025-06-14 01:36:22'),
(169, 'Sunsari Municipality', 'Koshi Province', 'Sunsari', 'Municipality', 'sunsari-municipality', 0, NULL, '2025-06-14 01:36:22'),
(170, 'Morang Municipality', 'Koshi Province', 'Morang', 'Municipality', 'morang-municipality', 0, NULL, '2025-06-14 01:36:22'),
(171, 'Dhankuta Municipality', 'Koshi Province', 'Dhankuta', 'Municipality', 'dhankuta-municipality', 0, NULL, '2025-06-14 01:36:22'),
(172, 'Sankhuwasabha Municipality', 'Koshi Province', 'Sankhuwasabha', 'Municipality', 'sankhuwasabha-municipality', 0, NULL, '2025-06-14 01:36:22'),
(173, 'Terhathum Municipality', 'Koshi Province', 'Terhathum', 'Municipality', 'terhathum-municipality', 0, NULL, '2025-06-14 01:36:22'),
(174, 'Ilam Municipality', 'Koshi Province', 'Ilam', 'Municipality', 'ilam-municipality', 0, NULL, '2025-06-14 01:36:22'),
(175, 'Jhapa Municipality', 'Koshi Province', 'Jhapa', 'Municipality', 'jhapa-municipality', 0, NULL, '2025-06-14 01:36:22'),
(176, 'Panchthar Municipality', 'Koshi Province', 'Panchthar', 'Municipality', 'panchthar-municipality', 0, NULL, '2025-06-14 01:36:22'),
(177, 'Taplejung Municipality', 'Koshi Province', 'Taplejung', 'Municipality', 'taplejung-municipality', 0, NULL, '2025-06-14 01:36:22'),
(178, 'Bhojpur Municipality', 'Koshi Province', 'Bhojpur', 'Municipality', 'bhojpur-municipality', 0, NULL, '2025-06-14 01:36:22'),
(179, 'Dhading Municipality', 'Bagmati Province', 'Dhading', 'Municipality', 'dhading-municipality', 0, NULL, '2025-06-14 01:36:22'),
(180, 'Rasuwa Municipality', 'Bagmati Province', 'Rasuwa', 'Municipality', 'rasuwa-municipality', 0, NULL, '2025-06-14 01:36:22'),
(181, 'Sindhupalchok Municipality', 'Bagmati Province', 'Sindhupalchok', 'Municipality', 'sindhupalchok-municipality', 0, NULL, '2025-06-14 01:36:22'),
(182, 'Nuwakot Municipality', 'Bagmati Province', 'Nuwakot', 'Municipality', 'nuwakot-municipality', 0, NULL, '2025-06-14 01:36:22'),
(183, 'Ramechhap Municipality', 'Bagmati Province', 'Ramechhap', 'Municipality', 'ramechhap-municipality', 0, NULL, '2025-06-14 01:36:22'),
(184, 'Sindhuli Municipality', 'Bagmati Province', 'Sindhuli', 'Municipality', 'sindhuli-municipality', 0, NULL, '2025-06-14 01:36:22'),
(185, 'Dolakha Municipality', 'Bagmati Province', 'Dolakha', 'Municipality', 'dolakha-municipality', 0, NULL, '2025-06-14 01:36:22'),
(186, 'Kavrepalanchok Municipality', 'Bagmati Province', 'Kavrepalanchok', 'Municipality', 'kavrepalanchok-municipality', 0, NULL, '2025-06-14 01:36:22'),
(187, 'Chitwan Municipality', 'Bagmati Province', 'Chitwan', 'Municipality', 'chitwan-municipality', 0, NULL, '2025-06-14 01:36:22'),
(188, 'Makwanpur Municipality', 'Bagmati Province', 'Makwanpur', 'Municipality', 'makwanpur-municipality', 0, NULL, '2025-06-14 01:36:22'),
(189, 'Gorkha Municipality', 'Gandaki Province', 'Gorkha', 'Municipality', 'gorkha-municipality', 0, NULL, '2025-06-14 01:36:22'),
(190, 'Lamjung Municipality', 'Gandaki Province', 'Lamjung', 'Municipality', 'lamjung-municipality', 0, NULL, '2025-06-14 01:36:22'),
(191, 'Kaski Municipality', 'Gandaki Province', 'Kaski', 'Municipality', 'kaski-municipality', 0, NULL, '2025-06-14 01:36:22'),
(192, 'Syangja Municipality', 'Gandaki Province', 'Syangja', 'Municipality', 'syangja-municipality', 0, NULL, '2025-06-14 01:36:22'),
(193, 'Tanahun Municipality', 'Gandaki Province', 'Tanahun', 'Municipality', 'tanahun-municipality', 0, NULL, '2025-06-14 01:36:22'),
(194, 'Parbat Municipality', 'Gandaki Province', 'Parbat', 'Municipality', 'parbat-municipality', 0, NULL, '2025-06-14 01:36:22'),
(195, 'Baglung Municipality', 'Gandaki Province', 'Baglung', 'Municipality', 'baglung-municipality', 0, NULL, '2025-06-14 01:36:22'),
(196, 'Myagdi Municipality', 'Gandaki Province', 'Myagdi', 'Municipality', 'myagdi-municipality', 0, NULL, '2025-06-14 01:36:22'),
(197, 'Mustang Municipality', 'Gandaki Province', 'Mustang', 'Municipality', 'mustang-municipality', 0, NULL, '2025-06-14 01:36:22'),
(198, 'Manang Municipality', 'Gandaki Province', 'Manang', 'Municipality', 'manang-municipality', 0, NULL, '2025-06-14 01:36:22'),
(199, 'Dolpa Municipality', 'Karnali Province', 'Dolpa', 'Municipality', 'dolpa-municipality', 0, NULL, '2025-06-14 01:36:22'),
(200, 'Jumla Municipality', 'Karnali Province', 'Jumla', 'Municipality', 'jumla-municipality', 0, NULL, '2025-06-14 01:36:22'),
(201, 'Kalikot Municipality', 'Karnali Province', 'Kalikot', 'Municipality', 'kalikot-municipality', 0, NULL, '2025-06-14 01:36:22'),
(202, 'Mugu Municipality', 'Karnali Province', 'Mugu', 'Municipality', 'mugu-municipality', 0, NULL, '2025-06-14 01:36:22'),
(203, 'Humla Municipality', 'Karnali Province', 'Humla', 'Municipality', 'humla-municipality', 0, NULL, '2025-06-14 01:36:22'),
(204, 'Surkhet Municipality', 'Karnali Province', 'Surkhet', 'Municipality', 'surkhet-municipality', 0, NULL, '2025-06-14 01:36:22'),
(205, 'Dailekh Municipality', 'Karnali Province', 'Dailekh', 'Municipality', 'dailekh-municipality', 0, NULL, '2025-06-14 01:36:22'),
(206, 'Jajarkot Municipality', 'Karnali Province', 'Jajarkot', 'Municipality', 'jajarkot-municipality', 0, NULL, '2025-06-14 01:36:22'),
(207, 'Rukum East Municipality', 'Lumbini Province', 'Rukum East', 'Municipality', 'rukum-east-municipality', 0, NULL, '2025-06-14 01:36:22'),
(208, 'Rolpa Municipality', 'Lumbini Province', 'Rolpa', 'Municipality', 'rolpa-municipality', 0, NULL, '2025-06-14 01:36:22'),
(209, 'Pyuthan Municipality', 'Lumbini Province', 'Pyuthan', 'Municipality', 'pyuthan-municipality', 0, NULL, '2025-06-14 01:36:22'),
(210, 'Dang Municipality', 'Lumbini Province', 'Dang', 'Municipality', 'dang-municipality', 0, NULL, '2025-06-14 01:36:22'),
(211, 'Gulmi Municipality', 'Lumbini Province', 'Gulmi', 'Municipality', 'gulmi-municipality', 0, NULL, '2025-06-14 01:36:22'),
(212, 'Arghakhanchi Municipality', 'Lumbini Province', 'Arghakhanchi', 'Municipality', 'arghakhanchi-municipality', 0, NULL, '2025-06-14 01:36:22'),
(213, 'Palpa Municipality', 'Lumbini Province', 'Palpa', 'Municipality', 'palpa-municipality', 0, NULL, '2025-06-14 01:36:22'),
(214, 'Dang Sub-Metropolitan City', 'Lumbini Province', 'Dang', 'Sub-Metropolitan', 'dang-sub-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(215, 'Tulsipur Sub-Metropolitan City', 'Lumbini Province', 'Dang', 'Sub-Metropolitan', 'tulsipur-sub-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(216, 'Ghorahi Sub-Metropolitan City', 'Lumbini Province', 'Dang', 'Sub-Metropolitan', 'ghorahi-sub-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(217, 'Butwal Sub-Metropolitan City', 'Lumbini Province', 'Rupandehi', 'Sub-Metropolitan', 'butwal-sub-metropolitan-city', 1, '', '2025-06-14 01:36:22'),
(218, 'Nepalgunj Sub-Metropolitan City', 'Lumbini Province', 'Banke', 'Sub-Metropolitan', 'nepalgunj-sub-metropolitan-city', 0, NULL, '2025-06-14 01:36:22'),
(219, 'Dhangadhi Sub-Metropolitan City', 'Sudurpashchim Province', 'Kailali', 'Sub-Metropolitan', 'dhangadhi-sub-metropolitan-city', 1, '', '2025-06-14 01:36:22'),
(220, 'Mahendranagar Municipality', 'Sudurpashchim Province', 'Kanchanpur', 'Municipality', 'mahendranagar-municipality', 0, NULL, '2025-06-14 01:36:22');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT 1,
  `last_attempt` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `business_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `views` int(11) DEFAULT 0,
  `featured` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `business_id`, `title`, `category`, `location`, `description`, `image`, `status`, `created_at`, `views`, `featured`) VALUES
(3, 11, 'gello', 'Job Vacancy', 'dhangadhi', 'grfhjdshjfdsjfgjs', NULL, 'approved', '2025-06-17 12:08:12', 0, 0),
(4, 1, 'Admission open!', 'Education', 'dhangadhi', 'welcome to nast', '685182637ed9d.jpg', 'approved', '2025-06-17 14:57:39', 4, 0),
(5, 1, 'job vacancy ', 'Education', 'dhangadhi', 'we need two teacher', '68518301696cd.jpg', 'approved', '2025-06-17 15:00:17', 15, 0),
(6, 3, 'Spa dance championships ', 'Education', 'dhangadhi', 'we informed you about this dance program....', '6852610b97436.jpg', 'approved', '2025-06-18 06:47:39', 5, 0),
(7, 3, 'jpt', 'Education', 'dhangadhi', 'jptttttttttttttttttttttttttttt', '68526142387c6.jpg', 'approved', '2025-06-18 06:48:34', 7, 0),
(8, 1, 'holiday', 'event', 'dhangadhi', 'tomorrow is holi day', '685421837cbe8.png', 'approved', '2025-06-19 14:41:07', 4, 0),
(9, 1, 'Nast cricket league (NCL)', 'Others', 'dhangadhi', 'we are organising the college level cricket tournament.........', '6857cf1e343bf.webp', 'approved', '2025-06-22 09:38:38', 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(2, 'coder', 'editor@example.com', 'password123', 'admin', '2025-06-10 16:10:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `city_id` (`city_id`);

--
-- Indexes for table `businesses`
--
ALTER TABLE `businesses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `url` (`url`),
  ADD KEY `name` (`name`),
  ADD KEY `category` (`category`),
  ADD KEY `location` (`location`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip_address` (`ip_address`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `posts_ibfk_1` (`business_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `areas`
--
ALTER TABLE `areas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `businesses`
--
ALTER TABLE `businesses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=221;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `areas`
--
ALTER TABLE `areas`
  ADD CONSTRAINT `areas_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
