-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 08, 2025 at 01:11 PM
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
-- Database: `psuhm_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `category_list`
--

CREATE TABLE `category_list` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category_list`
--

INSERT INTO `category_list` (`id`, `name`, `description`, `status`, `delete_flag`, `date_created`, `date_updated`) VALUES
(10, 'Appetizers', 'Small, flavorful dishes served before the main course to stimulate the appetite. This selection includes seafood, meats, and vegetarian options, showcasing a variety of tastes and textures.', 1, 0, '2025-03-29 10:51:27', '2025-03-29 10:51:27'),
(11, 'Soups', 'Warm, comforting dishes that can be creamy or brothy, featuring seasonal ingredients. Soups provide a cozy start to the meal, highlighting local flavors and culinary traditions.', 1, 0, '2025-03-29 10:51:43', '2025-03-29 10:51:43'),
(12, 'Salads', 'Fresh and vibrant dishes made with greens, vegetables, fruits, and proteins, often dressed with various dressings. Salads offer a refreshing contrast to heavier courses and emphasize seasonal ingredients.', 1, 0, '2025-03-29 10:51:56', '2025-03-29 10:51:56'),
(13, 'Main Courses', 'The centerpiece of the meal, featuring proteins paired with sides like vegetables and grains. Main courses showcase diverse culinary techniques and flavors, reflecting the chef&#039;s style.', 1, 0, '2025-03-29 10:52:06', '2025-03-29 10:52:06'),
(14, 'Desserts', 'Sweet treats served at the end of the meal, ranging from rich cakes to light sorbets. Desserts provide a delightful conclusion, often highlighting seasonal fruits and creative presentations.', 1, 0, '2025-03-29 10:52:20', '2025-03-29 10:52:20'),
(15, 'Add ons', '', 1, 1, '2025-04-24 18:09:49', '2025-05-04 17:04:08');

-- --------------------------------------------------------

--
-- Table structure for table `discount_list`
--

CREATE TABLE `discount_list` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `delete_flag` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discount_list`
--

INSERT INTO `discount_list` (`id`, `name`, `percentage`, `date_updated`, `status`, `date_created`, `delete_flag`) VALUES
(9, 'Senior Citizen', 20.00, '2025-05-04 09:08:26', 1, '2025-05-04 17:08:26', 0),
(10, 'Employee', 20.00, '2025-05-04 09:08:42', 1, '2025-05-04 17:08:42', 0),
(11, 'PWD', 20.00, '2025-05-05 07:25:01', 1, '2025-05-05 15:25:01', 0);

-- --------------------------------------------------------

--
-- Table structure for table `menu_list`
--

CREATE TABLE `menu_list` (
  `id` int(30) NOT NULL,
  `category_id` int(30) NOT NULL,
  `code` varchar(100) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `price` float(12,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_list`
--

INSERT INTO `menu_list` (`id`, `category_id`, `code`, `name`, `description`, `price`, `status`, `delete_flag`, `date_created`, `date_updated`, `image_path`) VALUES
(9, 10, 'A1', 'Tuna Tartare', 'Fresh tuna, avocado, and sesame oil served with a wasabi aioli.', 450.00, 1, 0, '2025-03-29 10:55:18', '2025-04-22 02:38:25', '1745260705_th.jpg'),
(10, 10, 'A2', 'Crispy Pork Belly', 'Slow-cooked pork belly with a crispy skin, served with a side of apple chutney.', 380.00, 1, 0, '2025-03-29 10:55:42', '2025-04-22 22:51:40', '1745333500_Crispy-Chinese-Pork-Belly.jpg'),
(11, 10, 'A3', 'Grilled Scallops', 'Seared scallops on a bed of garlic butter and herbs, garnished with microgreens.', 500.00, 1, 0, '2025-03-29 10:56:09', '2025-04-22 22:51:52', '1745333512_grilled_scalops.jpg'),
(12, 10, 'A4', 'Stuffed Mushrooms', 'Button mushrooms stuffed with cream cheese, herbs, and breadcrumbs, baked to perfection.', 350.00, 1, 0, '2025-03-29 10:56:38', '2025-04-22 22:52:07', '1745333527_stuffed-mushrooms-2.jpg'),
(13, 10, 'A5', 'Shrimp Cocktail', 'Chilled shrimp served with a tangy cocktail sauce and lemon wedges.', 400.00, 1, 0, '2025-03-29 10:57:04', '2025-04-22 22:52:22', '1745333542_shrimp_cocktail.jpg'),
(14, 11, 'S1', 'Miso Soup', 'Traditional Japanese miso soup with tofu, seaweed, and green onions.', 350.00, 1, 0, '2025-03-29 10:57:39', '2025-04-22 22:55:58', '1745333758_miso_soup.jpg'),
(15, 11, 'S2', 'Pumpkin Soup', 'Creamy pumpkin soup topped with roasted pumpkin seeds and a drizzle of truffle oil.', 500.00, 1, 0, '2025-03-29 10:58:00', '2025-04-22 22:56:10', '1745333770_Roasted-pumpkin-soup-8.jpg'),
(16, 11, 'S3', 'Tom Yum Soup', 'Spicy Thai soup with shrimp, lemongrass, and kaffir lime leaves.', 550.00, 1, 0, '2025-03-29 10:58:30', '2025-04-22 22:57:03', '1745333823_tom-yum-soup.jpg'),
(17, 11, 'S4', 'Sinigang na Baboy', 'Traditional Filipino sour soup with pork, tamarind, and assorted vegetables.', 400.00, 1, 0, '2025-03-29 10:58:50', '2025-04-22 22:57:18', '1745333838_sinigang-56a8a7083df78cf7729f6cf4.jpg'),
(18, 11, 'S5', 'Lobster Bisque', 'Rich and creamy lobster bisque garnished with fresh herbs and a splash of sherry.', 600.00, 1, 0, '2025-03-29 10:59:13', '2025-04-22 22:57:29', '1745333849_lobster-bisque-e0c612ad48905b3fc396e369.jpg'),
(19, 12, 'F1', 'Caesar Salad', 'Crisp romaine lettuce, croutons, and parmesan cheese tossed in Caesar dressing.', 450.00, 1, 0, '2025-03-29 10:59:52', '2025-04-22 22:53:32', '1745333612_ceasar_salad.jpg'),
(20, 12, 'F2', 'Greek Salad', 'Fresh tomatoes, cucumbers, olives, and feta cheese drizzled with olive oil.', 400.00, 1, 0, '2025-03-29 11:00:11', '2025-04-22 22:53:46', '1745333626_Greek-Salad-Recipe-recipe-card.jpg'),
(21, 12, 'F3', 'Mango and Avocado Salad', 'Sliced mango and avocado with mixed greens, topped with a citrus vinaigrette.', 550.00, 1, 0, '2025-03-29 11:00:38', '2025-04-22 22:54:03', '1745333643_mango_avocado_salad.jpg'),
(22, 12, 'F4', 'Beetroot Salad', 'Roasted beetroot with goat cheese, walnuts, and arugula, drizzled with balsamic glaze.', 500.00, 1, 0, '2025-03-29 11:01:00', '2025-04-22 22:54:14', '1745333654_beetroot_salad.jpg'),
(23, 12, 'F5', 'Quinoa Salad ', 'Quinoa mixed with cherry tomatoes, cucumber, and herbs, served with lemon', 400.00, 1, 0, '2025-03-29 11:01:17', '2025-04-22 22:54:25', '1745333665_quinoa_salad.jpg'),
(24, 13, 'M1', 'Grilled Ribeye Steak', '300g ribeye steak grilled to perfection, served with garlic mashed potatoes and seasonal vegetables.', 2000.00, 1, 0, '2025-03-29 11:01:43', '2025-04-22 22:54:39', '1745333679_Ribeye-Steak-Recipe09-1536x1024.webp'),
(25, 13, 'M2', 'Pan-Seared Salmon', 'Salmon fillet served with a lemon butter sauce, asparagus, and wild rice.', 1000.00, 1, 0, '2025-03-29 11:02:01', '2025-04-22 22:54:57', '1745333697_Pan-seared-salmon.jpg'),
(26, 13, 'M3', 'Chicken Roulade ', 'Chicken breast stuffed with spinach and cheese, served with a creamy mushroom sauce.', 1500.00, 1, 0, '2025-03-29 11:02:23', '2025-04-22 22:55:27', '1745333727_Chicken-Roulade-1-1.jpg'),
(27, 13, 'M4', 'Vegetable Paella', 'Saffron-infused rice with a medley of seasonal vegetables and herbs.', 1200.00, 1, 0, '2025-03-29 11:02:49', '2025-04-22 22:55:44', '1745333744_vege_paella.jpg'),
(28, 14, 'D1', 'Chocolate Lava Cake', 'Warm chocolate cake with a molten center, served with vanilla ice cream.', 500.00, 1, 0, '2025-03-29 11:03:09', '2025-04-22 22:52:37', '1745333557_WU0701H_Molten-Chocolate-Cakes_s4x3.jpg'),
(29, 14, 'D2', 'Tiramisu ', 'Classic Italian dessert made with coffee-soaked ladyfingers and mascarpone cheese.', 1200.00, 1, 0, '2025-03-29 11:03:37', '2025-04-22 22:52:48', '1745333568_tiramisu.jpg'),
(30, 14, 'D3', 'Pistachio Panna Cotta', 'Creamy panna cotta infused with pistachio, served with a berry compote.', 1200.00, 1, 0, '2025-03-29 11:03:57', '2025-04-22 22:53:19', '1745333599_pistachio_panna_cotta.jpg'),
(40, 14, '77', '77', '77', 777.00, 1, 1, '2025-04-22 02:28:18', '2025-04-22 16:38:57', NULL),
(41, 12, '999', '99', '99', 99.00, 1, 1, '2025-04-22 02:30:12', '2025-04-22 16:38:59', NULL),
(42, 13, '213', '12312', '312312', 13213.00, 1, 1, '2025-04-22 02:32:55', '2025-04-22 16:38:54', '1745260375_th.jpg'),
(43, 10, 'test', 'test', 'test', 311.00, 1, 1, '2025-04-22 02:35:43', '2025-05-04 17:04:25', '1745260543_2.jpg'),
(44, 11, '61', '61', '616', 16.00, 1, 1, '2025-04-22 16:39:18', '2025-05-04 17:04:15', '1745311158_logo.jpeg'),
(45, 11, '13123', '12321', '12312', 12312.00, 1, 1, '2025-04-22 17:00:39', '2025-05-04 17:04:13', '1745312439_th.jpg'),
(46, 14, 'D4', 'Ice Cream Sundae', 'yummy ice cream', 200.00, 1, 0, '2025-04-22 22:58:52', '2025-04-22 22:58:52', '1745333932_ice_cream.jpg'),
(47, 14, 'D5', 'Cakie', 'wowow', 200.00, 1, 1, '2025-04-24 12:22:11', '2025-05-04 17:04:20', '1745468531_ice_cream.jpg'),
(48, 15, 'AO1', 'Extra Egg', 'egg', 40.00, 1, 1, '2025-05-01 11:50:49', '2025-05-01 11:52:35', '1746071449_DALL__E_2025-03-29_09.05.53_-_A_luxurious_fine_dining_restaurant_logo_for__The_Golden_Lion_._The_logo_features_a_majestic_lion_in_gold_with_an_elegant__sophisticated_design._The_co.webp');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(30) NOT NULL,
  `menu_id` int(30) NOT NULL,
  `price` float(12,2) NOT NULL DEFAULT 0.00,
  `quantity` int(30) NOT NULL DEFAULT 0,
  `served` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'true=1,false=0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_id`, `price`, `quantity`, `served`) VALUES
(1, 1, 7, 75.00, 2, 0),
(2, 1, 4, 455.00, 2, 0),
(3, 2, 3, 145.00, 2, 0),
(4, 2, 2, 115.00, 1, 0),
(5, 2, 5, 25.00, 2, 0),
(6, 2, 6, 25.00, 1, 0),
(7, 3, 3, 145.00, 2, 0),
(8, 3, 2, 115.00, 1, 0),
(9, 3, 5, 25.00, 2, 0),
(10, 3, 6, 25.00, 1, 0),
(11, 4, 2, 115.00, 1, 0),
(12, 5, 5, 25.00, 2, 0),
(13, 5, 4, 455.00, 1, 0),
(14, 6, 1, 85.00, 5, 0),
(15, 7, 2, 115.00, 2, 0),
(16, 8, 4, 455.00, 1, 0),
(17, 9, 5, 25.00, 2, 0),
(18, 9, 4, 455.00, 1, 0),
(19, 9, 1, 85.00, 2, 0),
(20, 10, 5, 25.00, 2, 0),
(21, 10, 1, 85.00, 2, 0),
(22, 11, 4, 455.00, 1, 0),
(23, 12, 10, 380.00, 1, 0),
(24, 12, 13, 400.00, 1, 0),
(25, 13, 10, 380.00, 1, 0),
(26, 13, 9, 450.00, 1, 0),
(27, 13, 29, 1200.00, 1, 0),
(28, 13, 19, 450.00, 1, 0),
(29, 13, 17, 400.00, 1, 0),
(30, 13, 27, 1200.00, 1, 0),
(31, 14, 11, 500.00, 1, 0),
(32, 14, 13, 400.00, 2, 0),
(33, 15, 10, 380.00, 1, 0),
(34, 15, 24, 2000.00, 1, 0),
(35, 16, 10, 380.00, 1, 0),
(36, 16, 13, 400.00, 1, 0),
(37, 17, 11, 500.00, 1, 0),
(38, 17, 13, 400.00, 1, 0),
(39, 18, 25, 1000.00, 3, 0),
(40, 18, 24, 2000.00, 2, 0),
(41, 18, 26, 1500.00, 1, 0),
(42, 18, 27, 1200.00, 1, 0),
(43, 19, 11, 500.00, 1, 0),
(44, 19, 13, 400.00, 1, 0),
(45, 19, 10, 380.00, 1, 0),
(46, 20, 11, 500.00, 1, 0),
(47, 21, 13, 400.00, 1, 0),
(48, 22, 11, 500.00, 1, 0),
(49, 23, 11, 500.00, 1, 0),
(50, 24, 11, 500.00, 1, 0),
(51, 25, 11, 500.00, 1, 0),
(52, 25, 13, 400.00, 4, 0),
(53, 25, 10, 380.00, 1, 0),
(54, 25, 12, 350.00, 1, 0),
(55, 25, 9, 450.00, 1, 0),
(56, 25, 30, 1200.00, 2, 0),
(57, 26, 13, 400.00, 1, 0),
(58, 27, 13, 400.00, 1, 0),
(59, 28, 11, 500.00, 3, 0),
(60, 29, 10, 380.00, 1, 0),
(61, 29, 13, 400.00, 1, 0),
(62, 29, 20, 400.00, 1, 0),
(63, 30, 11, 500.00, 1, 0),
(64, 30, 13, 400.00, 1, 0),
(65, 30, 12, 350.00, 1, 0),
(66, 30, 9, 450.00, 3, 0),
(67, 31, 24, 2000.00, 1, 0),
(68, 31, 25, 1000.00, 3, 0),
(69, 32, 13, 400.00, 2, 0),
(70, 32, 11, 500.00, 1, 0),
(71, 33, 43, 311.00, 1, 0),
(72, 33, 9, 450.00, 1, 0),
(73, 34, 13, 400.00, 2, 0),
(74, 35, 13, 400.00, 1, 0),
(75, 35, 11, 500.00, 2, 0),
(76, 36, 11, 500.00, 1, 0),
(77, 36, 13, 400.00, 1, 0),
(78, 37, 10, 380.00, 1, 0),
(79, 37, 43, 311.00, 1, 0),
(80, 38, 11, 500.00, 1, 0),
(81, 38, 43, 311.00, 1, 0),
(82, 39, 9, 450.00, 1, 0),
(83, 40, 9, 450.00, 1, 0),
(84, 41, 9, 450.00, 1, 0),
(85, 42, 9, 450.00, 2, 0),
(86, 42, 13, 400.00, 1, 0),
(87, 43, 9, 450.00, 2, 0),
(88, 43, 13, 400.00, 1, 0),
(89, 44, 11, 500.00, 1, 0),
(90, 44, 13, 400.00, 1, 0),
(91, 45, 13, 400.00, 1, 0),
(92, 45, 11, 500.00, 1, 0),
(93, 46, 13, 400.00, 1, 0),
(94, 46, 11, 500.00, 1, 0),
(95, 47, 13, 400.00, 1, 0),
(96, 47, 11, 500.00, 1, 0),
(97, 48, 13, 400.00, 1, 0),
(98, 48, 11, 500.00, 1, 0),
(99, 49, 13, 400.00, 1, 0),
(100, 49, 11, 500.00, 1, 0),
(101, 50, 9, 450.00, 1, 0),
(102, 51, 9, 450.00, 1, 0),
(103, 52, 9, 450.00, 1, 0),
(104, 53, 9, 450.00, 1, 0),
(105, 54, 9, 450.00, 1, 0),
(106, 55, 9, 450.00, 1, 0),
(107, 56, 9, 450.00, 1, 0),
(108, 57, 9, 450.00, 1, 0),
(109, 58, 9, 450.00, 1, 0),
(110, 59, 9, 450.00, 1, 0),
(111, 60, 9, 450.00, 1, 0),
(112, 61, 9, 450.00, 1, 0),
(113, 62, 43, 311.00, 1, 0),
(114, 63, 9, 450.00, 1, 0),
(115, 64, 9, 450.00, 2, 0),
(116, 65, 43, 311.00, 1, 0),
(117, 66, 9, 450.00, 1, 0),
(118, 67, 9, 450.00, 1, 0),
(119, 68, 9, 450.00, 1, 0),
(120, 69, 9, 450.00, 1, 0),
(121, 70, 9, 450.00, 1, 0),
(122, 71, 43, 311.00, 1, 0),
(123, 72, 43, 311.00, 1, 0),
(124, 73, 43, 311.00, 1, 0),
(125, 73, 11, 500.00, 1, 0),
(126, 73, 10, 380.00, 1, 0),
(127, 73, 30, 1200.00, 1, 0),
(128, 74, 10, 380.00, 1, 0),
(129, 74, 11, 500.00, 2, 0),
(130, 74, 13, 400.00, 1, 0),
(131, 74, 9, 450.00, 1, 0),
(132, 75, 13, 400.00, 1, 0),
(133, 76, 13, 400.00, 1, 0),
(134, 76, 11, 500.00, 1, 0),
(135, 77, 9, 450.00, 1, 0),
(136, 78, 11, 500.00, 2, 0),
(137, 78, 26, 1500.00, 1, 0),
(138, 78, 24, 2000.00, 1, 0),
(139, 78, 25, 1000.00, 1, 0),
(140, 79, 43, 311.00, 1, 0),
(141, 80, 43, 311.00, 1, 0),
(142, 80, 12, 350.00, 1, 0),
(143, 81, 9, 450.00, 1, 0),
(144, 82, 11, 500.00, 1, 0),
(145, 83, 18, 600.00, 1, 0),
(146, 83, 15, 500.00, 1, 0),
(147, 83, 17, 400.00, 1, 0),
(148, 83, 14, 350.00, 1, 0),
(149, 83, 16, 550.00, 1, 0),
(150, 84, 12, 350.00, 1, 0),
(151, 84, 43, 311.00, 1, 0),
(152, 84, 11, 500.00, 1, 0),
(153, 84, 10, 380.00, 1, 0),
(154, 85, 20, 400.00, 1, 0),
(155, 86, 11, 500.00, 1, 0),
(156, 87, 10, 380.00, 1, 0),
(157, 88, 11, 500.00, 1, 0),
(158, 89, 10, 380.00, 3, 0),
(159, 90, 13, 400.00, 1, 0),
(160, 91, 12, 350.00, 1, 0),
(161, 92, 11, 500.00, 1, 0),
(162, 93, 43, 311.00, 1, 0),
(163, 93, 9, 450.00, 1, 0),
(164, 94, 11, 500.00, 1, 0),
(165, 94, 13, 400.00, 1, 0),
(166, 95, 12, 350.00, 1, 0),
(167, 96, 43, 311.00, 1, 0),
(168, 97, 12, 350.00, 1, 0),
(169, 98, 12, 350.00, 1, 0),
(170, 98, 43, 311.00, 1, 0),
(171, 98, 10, 380.00, 1, 0),
(172, 98, 9, 450.00, 1, 0),
(173, 99, 11, 500.00, 1, 0),
(174, 99, 13, 400.00, 1, 0),
(175, 100, 11, 500.00, 1, 0),
(176, 101, 11, 500.00, 1, 0),
(177, 102, 11, 500.00, 1, 0),
(178, 103, 10, 380.00, 1, 0),
(179, 104, 10, 380.00, 1, 0),
(180, 105, 11, 500.00, 1, 0),
(181, 106, 11, 500.00, 1, 0),
(182, 107, 11, 500.00, 1, 0),
(183, 108, 11, 500.00, 1, 0),
(184, 109, 11, 500.00, 1, 0),
(185, 110, 11, 500.00, 1, 0),
(186, 111, 11, 500.00, 1, 0),
(187, 112, 11, 500.00, 1, 0),
(188, 113, 11, 500.00, 1, 0),
(189, 114, 47, 200.00, 1, 0),
(190, 114, 28, 500.00, 1, 0),
(191, 115, 11, 500.00, 1, 0),
(192, 115, 10, 380.00, 1, 0),
(193, 115, 13, 400.00, 1, 0),
(194, 115, 43, 311.00, 1, 0),
(195, 115, 12, 350.00, 1, 0),
(196, 115, 27, 1200.00, 1, 0),
(197, 115, 26, 1500.00, 1, 0),
(198, 116, 10, 380.00, 1, 0),
(199, 116, 11, 500.00, 1, 0),
(200, 117, 11, 500.00, 1, 0),
(201, 117, 10, 380.00, 1, 0),
(202, 118, 13, 400.00, 1, 0),
(203, 118, 11, 500.00, 1, 0),
(204, 119, 11, 500.00, 1, 0),
(205, 119, 13, 400.00, 1, 0),
(206, 134, 11, 500.00, 2, 0),
(207, 134, 13, 400.00, 1, 0),
(208, 135, 47, 200.00, 1, 0),
(209, 135, 28, 500.00, 1, 0),
(210, 135, 46, 200.00, 1, 0),
(211, 136, 10, 380.00, 1, 0),
(212, 136, 13, 400.00, 1, 0),
(213, 136, 24, 2000.00, 1, 0),
(214, 136, 25, 1000.00, 1, 0),
(215, 137, 10, 380.00, 1, 0),
(216, 138, 10, 380.00, 1, 0),
(217, 138, 13, 400.00, 1, 0),
(218, 139, 10, 380.00, 1, 0),
(219, 139, 11, 500.00, 1, 0),
(220, 139, 13, 400.00, 1, 0),
(221, 140, 10, 380.00, 1, 0),
(222, 140, 11, 500.00, 1, 0),
(223, 140, 13, 400.00, 1, 0),
(224, 141, 25, 1000.00, 1, 0),
(225, 141, 24, 2000.00, 1, 0),
(226, 141, 26, 1500.00, 1, 0),
(227, 141, 27, 1200.00, 1, 0),
(228, 142, 11, 500.00, 1, 0),
(229, 142, 10, 380.00, 1, 0),
(230, 143, 10, 380.00, 1, 0),
(231, 143, 11, 500.00, 1, 0),
(232, 144, 9, 450.00, 1, 0),
(233, 144, 12, 350.00, 1, 0),
(234, 145, 13, 400.00, 1, 0),
(235, 146, 11, 500.00, 1, 0),
(236, 146, 13, 400.00, 1, 0),
(237, 147, 9, 450.00, 2, 0),
(238, 148, 10, 380.00, 1, 0),
(239, 148, 11, 500.00, 1, 0),
(240, 148, 13, 400.00, 1, 0),
(241, 149, 10, 380.00, 1, 0),
(242, 149, 11, 500.00, 1, 0),
(243, 149, 13, 400.00, 1, 0),
(244, 149, 12, 350.00, 1, 0),
(245, 150, 10, 380.00, 1, 0),
(246, 150, 11, 500.00, 1, 0),
(247, 150, 12, 350.00, 1, 0),
(248, 150, 9, 450.00, 1, 0),
(249, 151, 10, 380.00, 1, 0),
(250, 151, 11, 500.00, 1, 0),
(251, 151, 13, 400.00, 1, 0),
(252, 151, 9, 450.00, 1, 0),
(253, 151, 12, 350.00, 1, 0),
(254, 152, 10, 380.00, 1, 0),
(255, 152, 11, 500.00, 1, 0),
(256, 152, 13, 400.00, 1, 0),
(257, 152, 12, 350.00, 1, 0),
(258, 152, 26, 1500.00, 1, 0),
(259, 153, 10, 380.00, 1, 0),
(260, 153, 11, 500.00, 1, 0),
(261, 153, 13, 400.00, 1, 0),
(262, 153, 29, 1200.00, 1, 0),
(263, 153, 18, 600.00, 2, 0),
(264, 154, 10, 380.00, 1, 0),
(265, 154, 11, 500.00, 1, 0),
(266, 154, 13, 400.00, 1, 0),
(267, 154, 12, 350.00, 1, 0),
(268, 154, 9, 450.00, 1, 0),
(269, 154, 46, 200.00, 1, 0),
(270, 154, 46, 200.00, 1, 0),
(271, 155, 10, 380.00, 1, 0),
(272, 155, 11, 500.00, 1, 0),
(273, 155, 13, 400.00, 1, 0),
(274, 155, 11, 500.00, 1, 0),
(275, 156, 10, 380.00, 1, 0),
(276, 156, 11, 500.00, 1, 0),
(277, 156, 13, 400.00, 1, 0),
(278, 153, 20, 400.00, 1, 0),
(279, 157, 10, 380.00, 1, 0),
(280, 157, 11, 500.00, 1, 0),
(281, 157, 13, 400.00, 1, 0),
(282, 158, 10, 380.00, 1, 0),
(283, 158, 11, 500.00, 1, 0),
(284, 158, 13, 400.00, 1, 0),
(285, 158, 11, 500.00, 1, 0),
(286, 159, 10, 380.00, 1, 0),
(287, 159, 13, 400.00, 1, 0),
(288, 159, 11, 500.00, 1, 0),
(289, 159, 46, 200.00, 1, 0),
(290, 160, 10, 380.00, 1, 0),
(291, 160, 11, 500.00, 1, 0),
(292, 160, 13, 400.00, 1, 0),
(293, 161, 10, 380.00, 1, 0),
(294, 161, 11, 500.00, 1, 0),
(295, 161, 13, 400.00, 1, 0),
(296, 162, 10, 380.00, 1, 0),
(297, 162, 11, 500.00, 1, 0),
(298, 162, 13, 400.00, 1, 0),
(299, 163, 10, 380.00, 1, 0),
(300, 163, 11, 500.00, 1, 0),
(301, 163, 13, 400.00, 1, 0),
(302, 164, 10, 380.00, 1, 0),
(303, 164, 11, 500.00, 1, 0),
(304, 164, 13, 400.00, 1, 0),
(305, 165, 10, 380.00, 1, 0),
(306, 165, 11, 500.00, 1, 0),
(307, 165, 13, 400.00, 1, 0),
(308, 166, 10, 380.00, 1, 0),
(309, 166, 11, 500.00, 1, 0),
(310, 166, 13, 400.00, 1, 0),
(311, 167, 10, 380.00, 1, 0),
(312, 167, 11, 500.00, 1, 0),
(313, 167, 13, 400.00, 1, 0),
(314, 168, 10, 380.00, 1, 0),
(315, 168, 11, 500.00, 1, 0),
(316, 168, 13, 400.00, 1, 0),
(317, 169, 10, 380.00, 1, 0),
(318, 169, 11, 500.00, 1, 0),
(319, 169, 13, 400.00, 1, 0),
(320, 170, 10, 380.00, 1, 0),
(321, 170, 11, 500.00, 1, 0),
(322, 170, 13, 400.00, 1, 0),
(323, 171, 10, 380.00, 1, 0),
(324, 171, 11, 500.00, 1, 0),
(325, 171, 13, 400.00, 1, 0),
(326, 172, 10, 380.00, 1, 0),
(327, 172, 11, 500.00, 1, 0),
(328, 172, 13, 400.00, 1, 0),
(329, 173, 10, 380.00, 1, 0),
(330, 173, 11, 500.00, 1, 0),
(331, 173, 13, 400.00, 1, 0),
(332, 174, 10, 380.00, 1, 0),
(333, 174, 13, 400.00, 1, 0),
(334, 174, 11, 500.00, 1, 0),
(335, 175, 10, 380.00, 1, 0),
(336, 175, 11, 500.00, 1, 0),
(337, 175, 13, 400.00, 1, 0),
(338, 176, 10, 380.00, 1, 0),
(339, 176, 11, 500.00, 1, 0),
(340, 176, 13, 400.00, 1, 0),
(341, 177, 10, 380.00, 1, 0),
(342, 177, 11, 500.00, 1, 0),
(343, 177, 13, 400.00, 1, 0),
(344, 178, 10, 380.00, 1, 0),
(345, 178, 11, 500.00, 1, 0),
(346, 178, 13, 400.00, 1, 0),
(347, 179, 10, 380.00, 1, 0),
(348, 179, 11, 500.00, 1, 0),
(349, 179, 13, 400.00, 1, 0),
(350, 180, 10, 380.00, 1, 0),
(351, 180, 11, 500.00, 1, 0),
(352, 180, 13, 400.00, 1, 0),
(353, 181, 10, 380.00, 1, 0),
(354, 181, 11, 500.00, 1, 0),
(355, 181, 13, 400.00, 1, 0),
(356, 182, 10, 380.00, 1, 0),
(357, 182, 11, 500.00, 1, 0),
(358, 182, 13, 400.00, 1, 0),
(359, 183, 10, 380.00, 1, 0),
(360, 183, 11, 500.00, 1, 0),
(361, 183, 13, 400.00, 1, 0),
(362, 183, 18, 600.00, 1, 0),
(363, 184, 10, 380.00, 1, 0),
(364, 184, 11, 500.00, 1, 0),
(365, 184, 13, 400.00, 1, 0),
(366, 184, 29, 1200.00, 2, 0),
(367, 184, 46, 200.00, 1, 0),
(368, 185, 10, 380.00, 1, 0),
(369, 185, 11, 500.00, 1, 0),
(370, 185, 13, 400.00, 1, 0),
(371, 186, 10, 380.00, 1, 0),
(372, 186, 11, 500.00, 1, 0),
(373, 186, 13, 400.00, 1, 0),
(374, 187, 10, 380.00, 1, 0),
(375, 187, 11, 500.00, 1, 0),
(376, 187, 13, 400.00, 1, 0),
(377, 188, 10, 380.00, 1, 0),
(378, 188, 11, 500.00, 1, 0),
(379, 188, 13, 400.00, 1, 0),
(380, 189, 10, 380.00, 1, 0),
(381, 189, 11, 500.00, 1, 0),
(382, 189, 13, 400.00, 1, 0),
(383, 190, 10, 380.00, 1, 0),
(384, 190, 11, 500.00, 1, 0),
(385, 190, 13, 400.00, 1, 0),
(386, 191, 10, 380.00, 1, 0),
(387, 191, 11, 500.00, 1, 0),
(388, 191, 13, 400.00, 1, 0),
(389, 192, 10, 380.00, 1, 0),
(390, 192, 11, 500.00, 1, 0),
(391, 192, 13, 400.00, 1, 0),
(392, 193, 10, 380.00, 1, 0),
(393, 193, 11, 500.00, 1, 0),
(394, 193, 13, 400.00, 1, 0),
(395, 194, 10, 380.00, 1, 0),
(396, 194, 11, 500.00, 1, 0),
(397, 194, 13, 400.00, 1, 0),
(398, 195, 10, 380.00, 1, 0),
(399, 195, 11, 500.00, 1, 0),
(400, 195, 13, 400.00, 1, 0),
(401, 196, 11, 500.00, 1, 0),
(402, 196, 10, 380.00, 1, 0),
(403, 196, 13, 400.00, 1, 0),
(404, 197, 10, 380.00, 1, 0),
(405, 197, 11, 500.00, 1, 0),
(406, 197, 13, 400.00, 1, 0),
(407, 198, 10, 380.00, 1, 0),
(408, 198, 11, 500.00, 1, 0),
(409, 198, 13, 400.00, 1, 0),
(410, 199, 10, 380.00, 1, 0),
(411, 199, 11, 500.00, 1, 0),
(412, 199, 13, 400.00, 1, 0),
(413, 200, 10, 380.00, 1, 0),
(414, 200, 11, 500.00, 1, 0),
(415, 200, 13, 400.00, 1, 0),
(416, 201, 10, 380.00, 1, 0),
(417, 201, 11, 500.00, 1, 0),
(418, 201, 13, 400.00, 1, 0),
(419, 202, 10, 380.00, 1, 0),
(420, 202, 11, 500.00, 1, 0),
(421, 203, 11, 500.00, 1, 0),
(422, 203, 13, 400.00, 1, 0),
(423, 204, 10, 380.00, 1, 0),
(424, 204, 11, 500.00, 1, 0),
(425, 204, 13, 400.00, 1, 0),
(426, 205, 10, 380.00, 1, 0),
(427, 205, 11, 500.00, 1, 0),
(428, 205, 13, 400.00, 1, 0),
(429, 206, 13, 400.00, 1, 0),
(430, 206, 11, 500.00, 1, 0),
(431, 206, 10, 380.00, 1, 0),
(432, 207, 10, 380.00, 1, 0),
(433, 207, 11, 500.00, 1, 0),
(434, 207, 13, 400.00, 1, 0),
(435, 207, 12, 350.00, 1, 0),
(436, 207, 9, 450.00, 1, 0),
(437, 208, 10, 380.00, 1, 0),
(438, 209, 10, 380.00, 1, 0),
(439, 209, 11, 500.00, 1, 0),
(440, 210, 10, 380.00, 1, 0),
(441, 210, 11, 500.00, 1, 0),
(442, 210, 13, 400.00, 1, 0),
(443, 211, 11, 500.00, 1, 0),
(444, 211, 10, 380.00, 1, 0),
(445, 211, 13, 400.00, 1, 0),
(446, 212, 10, 380.00, 1, 0),
(447, 212, 13, 400.00, 1, 0),
(448, 212, 11, 500.00, 1, 0),
(449, 213, 10, 380.00, 1, 0),
(450, 213, 11, 500.00, 1, 0),
(451, 213, 13, 400.00, 1, 0),
(452, 213, 9, 450.00, 2, 0),
(453, 214, 10, 400.00, 2, 1),
(454, 215, 10, 400.00, 3, 0),
(455, 216, 11, 500.00, 1, 0),
(456, 216, 13, 400.00, 1, 0),
(457, 216, 10, 380.00, 1, 0),
(458, 215, 24, 2000.00, 1, 0),
(459, 217, 11, 500.00, 1, 1),
(460, 217, 13, 400.00, 1, 1),
(461, 216, 46, 200.00, 1, 0),
(462, 214, 43, 311.00, 1, 1),
(463, 214, 15, 500.00, 1, 1),
(464, 214, 15, 500.00, 1, 1),
(465, 214, 46, 200.00, 1, 1),
(466, 214, 18, 600.00, 1, 1),
(467, 214, 18, 600.00, 1, 1),
(468, 214, 18, 600.00, 1, 1),
(469, 214, 24, 2000.00, 1, 1),
(470, 214, 46, 200.00, 1, 1),
(471, 217, 11, 500.00, 1, 1),
(472, 217, 24, 2000.00, 1, 1),
(473, 218, 11, 500.00, 1, 1),
(474, 218, 13, 400.00, 1, 1),
(475, 217, 46, 200.00, 1, 1),
(476, 218, 46, 200.00, 1, 1),
(477, 218, 9, 450.00, 1, 1),
(478, 218, 11, 500.00, 1, 1),
(479, 218, 29, 1200.00, 1, 1),
(480, 219, 11, 500.00, 1, 1),
(481, 219, 13, 400.00, 1, 1),
(482, 219, 10, 380.00, 1, 1),
(483, 219, 18, 600.00, 1, 1),
(484, 219, 15, 500.00, 1, 1),
(485, 219, 17, 400.00, 1, 1),
(486, 220, 11, 500.00, 1, 1),
(487, 220, 13, 400.00, 1, 1),
(488, 220, 10, 380.00, 1, 1),
(489, 220, 18, 600.00, 1, 1),
(490, 220, 17, 400.00, 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=491;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- --------------------------------------------------------

--
-- Table structure for table `order_list`
--

CREATE TABLE `order_list` (
  `id` int(30) NOT NULL,
  `user_id` int(30) NOT NULL,
  `table_id` int(30) DEFAULT NULL,
  `code` varchar(100) NOT NULL,
  `queue` varchar(50) NOT NULL,
  `total_amount` float(12,2) NOT NULL DEFAULT 0.00,
  `discounted_amount` float(12,2) NOT NULL DEFAULT 0.00,
  `tendered_amount` float(12,2) NOT NULL DEFAULT 0.00,
  `change_amount` float(12,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = Queued,\r\n1 = Served,\r\n2 = Paid,\r\n3 = Billed Out',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `order_type` varchar(50) DEFAULT NULL,
  `discount_type` varchar(255) DEFAULT NULL,
  `discount_percent` int(11) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `reference_number` varchar(100) NOT NULL,
  `card_number` varchar(100) NOT NULL,
  `vat_amount` float(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_list`
--

INSERT INTO `order_list` (`id`, `user_id`, `table_id`, `code`, `queue`, `total_amount`, `discounted_amount`, `tendered_amount`, `change_amount`, `status`, `date_created`, `date_updated`, `order_type`, `discount_type`, `discount_percent`, `payment_method`, `reference_number`, `card_number`, `vat_amount`) VALUES
(192, 6, NULL, '2025071500001', '00001', 1280.00, 1433.60, 2000.00, 566.40, 2, '2025-07-15 21:09:23', '2025-07-16 11:13:51', 'Takeaway', 'N/A', 0, 'cash', '', '', 153.60),
(193, 6, NULL, '2025071500003', '00003', 1280.00, 1433.60, 2000.00, 566.40, 2, '2025-07-15 21:45:54', '2025-07-16 11:12:37', 'Dine-In', 'N/A', 0, 'cash', '', '', 153.60),
(194, 6, NULL, '2025071500004', '00004', 1280.00, 1433.60, 2000.00, 566.40, 2, '2025-07-15 21:46:21', '2025-07-16 11:09:21', 'Dine-In', 'N/A', 0, 'cash', '', '', 153.60),
(195, 1, NULL, '2025071500005', '00005', 1280.00, 1433.60, 2000.00, 566.40, 2, '2025-07-15 21:46:47', '2025-07-16 11:08:22', 'Dine-In', 'N/A', 0, 'cash', '', '', 153.60),
(196, 1, NULL, '2025071500006', '00006', 1280.00, 1024.00, 2000.00, 976.00, 2, '2025-07-15 21:47:52', '2025-07-16 11:06:31', 'Dine-In', 'Senior Citizen', 20, 'cash', '', '', 0.00),
(199, 1, 1, '2025071500009', '00009', 1280.00, 1433.60, 2000.00, 566.40, 2, '2025-07-15 22:12:23', '2025-07-16 11:04:59', 'Dine-In', 'N/A', 0, 'cash', '', '', 153.60),
(211, 1, 1, '2025071600001', '00001', 1280.00, 1433.60, 2000.00, 566.40, 2, '2025-07-16 11:14:12', '2025-07-16 11:14:29', 'Dine-In', 'N/A', 0, 'cash', '', '', 153.60),
(212, 1, 3, '2025073100001', '00001', 1280.00, 1024.00, 10000.00, 8976.00, 2, '2025-07-31 20:06:07', '2025-07-31 20:07:24', 'Dine-In', 'Senior Citizen', 20, 'cash', '', '', 0.00),
(213, 1, 1, '2025073100002', '00002', 2180.00, 2441.60, 10000.00, 7558.40, 2, '2025-07-31 20:06:32', '2025-07-31 20:07:49', 'Dine-In', 'N/A', 0, 'cash', '', '', 261.60),
(214, 1, 3, '2025073100003', '00003', 6311.00, 0.00, 0.00, 0.00, 1, '2025-07-31 22:20:39', '2025-09-08 19:08:16', 'Dine-In', 'N/A', NULL, NULL, '', '', 0.00),
(215, 1, 4, '2025073100004', '00004', 3200.00, 0.00, 0.00, 0.00, 3, '2025-07-31 22:20:56', '2025-09-03 11:39:02', 'Dine-In', 'N/A', NULL, NULL, '', '', 0.00),
(216, 1, 1, '2025090200001', '00001', 1480.00, 1657.60, 2000.00, 342.40, 2, '2025-09-02 23:11:22', '2025-09-03 11:46:07', 'Dine-In', 'N/A', 0, 'cash', '', '', 177.60),
(217, 1, 4, '2025090300001', '00001', 3600.00, 0.00, 0.00, 0.00, 0, '2025-09-03 11:44:51', '2025-09-08 19:09:54', 'Dine-In', 'N/A', NULL, NULL, '', '', 0.00),
(218, 1, 1, '2025090800001', '00001', 1550.00, 0.00, 0.00, 0.00, 1, '2025-09-08 19:09:36', '2025-09-08 19:10:43', 'Dine-In', 'N/A', NULL, NULL, '', '', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `system_info`
--

CREATE TABLE `system_info` (
  `id` int(30) NOT NULL,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_info`
--

INSERT INTO `system_info` (`id`, `meta_field`, `meta_value`) VALUES
(1, 'name', 'PSUHMPOS'),
(6, 'short_name', 'LION CASA 1979'),
(11, 'logo', 'uploads/logo.png?v=1745329649'),
(13, 'user_avatar', 'uploads/user_avatar.jpg'),
(14, 'cover', 'uploads/cover.png?v=1743212042'),
(17, 'phone', '456-987-1231'),
(18, 'mobile', '09123456987 / 094563212222 '),
(19, 'email', 'info@musicschool.com'),
(20, 'address', 'Here St, Down There City, Anywhere Here, 2306 -updated');

-- --------------------------------------------------------

--
-- Table structure for table `table_list`
--

CREATE TABLE `table_list` (
  `id` int(30) NOT NULL,
  `table_number` varchar(50) NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT 4,
  `location` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0=Out of Service, 1=Available, 2=Reserved',
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `table_list`
--

INSERT INTO `table_list` (`id`, `table_number`, `capacity`, `location`, `status`, `delete_flag`, `date_created`, `date_updated`) VALUES
(1, '1', 5, 'Main Hall', 1, 0, '2025-07-14 22:18:06', '2025-07-15 21:47:01'),
(3, '2', 4, 'Main Hall', 1, 0, '2025-07-15 20:18:15', '2025-07-15 20:38:31'),
(4, '3', 2, 'Main Hall', 1, 0, '2025-07-15 21:45:10', '2025-07-15 21:53:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(50) NOT NULL,
  `firstname` varchar(250) NOT NULL,
  `middlename` text DEFAULT NULL,
  `lastname` varchar(250) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `avatar` text DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='2';

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `middlename`, `lastname`, `username`, `password`, `avatar`, `last_login`, `type`, `date_added`, `date_updated`) VALUES
(1, 'Adminstrator', '', 'Admin', 'admin', '0192023a7bbd73250516f069df18b500', 'uploads/avatars/1.png?v=1744624199', NULL, 1, '2021-01-20 14:02:37', '2025-04-14 17:49:59'),
(3, 'Claire', 'C', 'Blake', 'cblake', '4744ddea876b11dcb1d169fadf494418', 'uploads/avatars/3.png?v=1653723045', NULL, 2, '2022-05-28 15:30:45', '2022-05-30 15:56:49'),
(5, 'Edward', 'Ompad', 'Fernandez', 'Edward', '0192023a7bbd73250516f069df18b500', 'uploads/avatars/5.png?v=1743217564', NULL, 2, '2025-03-29 11:06:04', '2025-03-29 11:06:04'),
(6, 'Renier', 'Gwapo', 'Fortin', 'Renier', '0192023a7bbd73250516f069df18b500', 'uploads/avatars/6.png?v=1743217611', NULL, 3, '2025-03-29 11:06:51', '2025-03-29 11:06:51'),
(7, 'Edward', 'Ompad', 'Fernandez', 'Edward', '0192023a7bbd73250516f069df18b500', 'uploads/avatars/7.png?v=1746351374', NULL, 3, '2025-05-04 17:36:14', '2025-05-04 17:36:14'),
(8, 'MIna', 'Myoui', 'Fernandez', 'Mina', '0192023a7bbd73250516f069df18b500', 'uploads/avatars/8.png?v=1746429973', NULL, 2, '2025-05-05 15:26:12', '2025-05-05 15:26:13'),
(9, 'John Bryan', 'Resuello', 'Tisado', 'Bryan', '0192023a7bbd73250516f069df18b500', NULL, NULL, 2, '2025-06-09 20:15:32', '2025-06-09 20:15:32'),
(10, 'Juan', 'Dela', 'Cruz', 'Juan', '0192023a7bbd73250516f069df18b500', NULL, NULL, 2, '2025-06-10 22:28:50', '2025-06-10 22:28:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category_list`
--
ALTER TABLE `category_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discount_list`
--
ALTER TABLE `discount_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_list`
--
ALTER TABLE `menu_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `order_list`
--
ALTER TABLE `order_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `system_info`
--
ALTER TABLE `system_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `table_list`
--
ALTER TABLE `table_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category_list`
--
ALTER TABLE `category_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `discount_list`
--
ALTER TABLE `discount_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `menu_list`
--
ALTER TABLE `menu_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `order_list`
--
ALTER TABLE `order_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=219;

--
-- AUTO_INCREMENT for table `system_info`
--
ALTER TABLE `system_info`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `table_list`
--
ALTER TABLE `table_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menu_list`
--
ALTER TABLE `menu_list`
  ADD CONSTRAINT `category_id_fk_ml` FOREIGN KEY (`category_id`) REFERENCES `category_list` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `order_list`
--
ALTER TABLE `order_list`
  ADD CONSTRAINT `user_id_fk_ol` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
