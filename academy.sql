-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 17, 2025 at 03:14 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `academy`
--

-- --------------------------------------------------------

--
-- Table structure for table `auth_groups_users`
--

CREATE TABLE `auth_groups_users` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auth_groups_users`
--

INSERT INTO `auth_groups_users` (`id`, `user_id`, `group`, `created_at`) VALUES
(1, 1, 'user', '2025-10-09 05:18:14');

-- --------------------------------------------------------

--
-- Table structure for table `auth_identities`
--

CREATE TABLE `auth_identities` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `secret` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `secret2` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `expires` datetime DEFAULT NULL,
  `extra` text COLLATE utf8mb4_general_ci,
  `force_reset` tinyint(1) NOT NULL DEFAULT '0',
  `last_used_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auth_identities`
--

INSERT INTO `auth_identities` (`id`, `user_id`, `type`, `name`, `secret`, `secret2`, `expires`, `extra`, `force_reset`, `last_used_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'email_password', NULL, 'keylacelestino@gmail.com', '$2y$12$etwJ5HqvY30lgh404H2YLuxjxWIEY5o7sb9dajkDrgUEffY97Qpai', NULL, NULL, 0, '2025-10-13 10:40:56', '2025-10-09 05:18:13', '2025-10-13 10:40:56'),
(15, 15, 'email_password', NULL, 'hemelaly02@gmail.com', '$2y$12$SctU77Nw567mZJZt6i/0NuXrTLGtcUA3nzeozXdvpUxE.VBQ5sVcG', NULL, NULL, 0, '2025-10-15 09:03:31', '2025-10-10 16:42:11', '2025-10-15 09:03:31'),
(16, 16, 'email_password', NULL, 'hemelwork02@gmail.com', '$2y$12$dLHO1ZIXerIyb6usUZqH4eKTo05BSLJt517weViG9Uw/OAP78o9Ou', NULL, NULL, 0, '2025-10-13 10:41:51', '2025-10-13 10:41:13', '2025-10-13 10:41:51');

-- --------------------------------------------------------

--
-- Table structure for table `auth_logins`
--

CREATE TABLE `auth_logins` (
  `id` int UNSIGNED NOT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `identifier` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `date` datetime NOT NULL,
  `success` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auth_logins`
--

INSERT INTO `auth_logins` (`id`, `ip_address`, `user_agent`, `id_type`, `identifier`, `user_id`, `date`, `success`) VALUES
(1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'email_password', 'keylacelestino@gmail.com', NULL, '2025-10-09 08:18:16', 0),
(2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'email_password', 'keylacelestino@gmail.com', NULL, '2025-10-09 08:18:34', 0),
(3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'email_password', 'keylacelestino@gmail.com', 1, '2025-10-09 08:19:47', 1),
(4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'email_password', 'keylacelestino@gmail.com', NULL, '2025-10-09 09:24:39', 0),
(5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'email_password', 'keylacelestino@gmail.com', 1, '2025-10-09 09:24:48', 1),
(6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'email_password', 'hemelaly02@gmail.com', 13, '2025-10-09 10:42:25', 1),
(7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'email_password', 'hemelaly02@gmail.com', 13, '2025-10-09 13:33:08', 1),
(8, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'email_password', 'keylacelestino@gmail.com', 1, '2025-10-09 13:42:07', 1),
(9, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'email_password', 'hemelaly02@gmail.com', 13, '2025-10-09 13:56:12', 1),
(10, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'email_password', 'hemelaly02@gmail.com', 13, '2025-10-09 14:37:11', 1),
(11, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'email_password', 'hemelaly02@gmail.com', 13, '2025-10-09 14:45:12', 1),
(12, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'email_password', 'keylacelestino@gmail.com', NULL, '2025-10-10 09:25:25', 0),
(13, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'email_password', 'keylacelestino@gmail.com', 1, '2025-10-10 09:25:47', 1),
(14, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'email_password', 'keylacelestino@gmail.com', 1, '2025-10-10 16:41:58', 1),
(15, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'email_password', 'hemelaly02@gmail.com', 15, '2025-10-10 16:44:00', 1),
(16, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'email_password', 'hemelaly02@gmail.com', 15, '2025-10-13 08:09:06', 1),
(17, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'email_password', 'keylacelestino@gmail.com', NULL, '2025-10-13 08:18:59', 0),
(18, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'email_password', 'keylacelestino@gmail.com', NULL, '2025-10-13 08:19:08', 0),
(19, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'email_password', 'keylacelestino@gmail.com', 1, '2025-10-13 08:19:16', 1),
(20, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'email_password', 'hemelwork02@gmail.com', NULL, '2025-10-13 10:38:50', 0),
(21, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'email_password', 'keylacelestino@gmail.com', NULL, '2025-10-13 10:40:44', 0),
(22, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'email_password', 'keylacelestino@gmail.com', 1, '2025-10-13 10:40:56', 1),
(23, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'email_password', 'hemelwork02@gmail.com', 16, '2025-10-13 10:41:51', 1),
(24, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'email_password', 'hemelaly02@gmail.com', 15, '2025-10-15 09:03:31', 1);

-- --------------------------------------------------------

--
-- Table structure for table `auth_permissions_users`
--

CREATE TABLE `auth_permissions_users` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `permission` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_remember_tokens`
--

CREATE TABLE `auth_remember_tokens` (
  `id` int UNSIGNED NOT NULL,
  `selector` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `hashedValidator` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `expires` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `auth_remember_tokens`
--

INSERT INTO `auth_remember_tokens` (`id`, `selector`, `hashedValidator`, `user_id`, `expires`, `created_at`, `updated_at`) VALUES
(14, 'c366a5a22174b088a720afa4', '72d6e33edae525596f95b4c67c0fdcd39c5fe0831673324c9e0f9e77d2df0f74', 1, '2025-11-12 16:15:59', '2025-10-13 10:40:56', '2025-10-13 16:15:59'),
(16, '02249f51c37dc21aea601fe9', 'e62266240d54939372ceed24a7bd1aad9456a05398d2d99b65f498332f5d9a52', 15, '2025-11-16 07:01:54', '2025-10-15 09:03:31', '2025-10-17 07:01:54');

-- --------------------------------------------------------

--
-- Table structure for table `auth_token_logins`
--

CREATE TABLE `auth_token_logins` (
  `id` int UNSIGNED NOT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `identifier` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `date` datetime NOT NULL,
  `success` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id_course` int UNSIGNED NOT NULL,
  `title_course` varchar(150) NOT NULL,
  `subtitle_course` varchar(100) NOT NULL,
  `description_course` text,
  `image_course` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `price_course` decimal(10,2) DEFAULT '0.00',
  `id_instructor_course` int UNSIGNED NOT NULL,
  `level_course` enum('beginner','intermediate','advanced') DEFAULT 'beginner',
  `status_course` enum('Rascunho','Ativo','Arquivado') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'Rascunho',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id_course`, `title_course`, `subtitle_course`, `description_course`, `image_course`, `price_course`, `id_instructor_course`, `level_course`, `status_course`, `created_at`, `updated_at`) VALUES
(18, 'Excel Básico', 'Curso completo de excel', 'Curso completo de excel para todos', '1760110282_8db971107af0fef49902.png', 1580.00, 1, 'beginner', 'Rascunho', '2025-10-10 15:31:22', '2025-10-13 12:29:10');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id_enrollment` int UNSIGNED NOT NULL,
  `id_student_enrollment` int UNSIGNED NOT NULL,
  `id_course_enrollment` int UNSIGNED NOT NULL,
  `status_enrollment` enum('Ativa','Pendente','Cancelada') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'Ativa',
  `progress_enrollment` tinyint UNSIGNED DEFAULT '0',
  `completed_enrollment` datetime DEFAULT NULL,
  `enrolled_at_enrollment` date NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id_enrollment`, `id_student_enrollment`, `id_course_enrollment`, `status_enrollment`, `progress_enrollment`, `completed_enrollment`, `enrolled_at_enrollment`, `created_at`, `updated_at`) VALUES
(12, 15, 18, 'Ativa', 11, NULL, '2025-10-10', '2025-10-10 16:42:16', '2025-10-17 11:17:40'),
(13, 16, 18, 'Ativa', 0, NULL, '2025-10-13', '2025-10-13 10:41:18', '2025-10-13 10:41:18');

-- --------------------------------------------------------

--
-- Table structure for table `instructors`
--

CREATE TABLE `instructors` (
  `id_instructor` int UNSIGNED NOT NULL,
  `id_user_instructor` int UNSIGNED NOT NULL,
  `name_instructor` varchar(100) NOT NULL,
  `email_instructor` varchar(255) NOT NULL,
  `phone_instructor` varchar(50) DEFAULT NULL,
  `profile_image_instructor` varchar(255) DEFAULT NULL,
  `website_instructor` varchar(255) DEFAULT NULL,
  `facebook_instructor` varchar(255) DEFAULT NULL,
  `linkedin_instructor` varchar(255) DEFAULT NULL,
  `twitter_instructor` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `instructors`
--

INSERT INTO `instructors` (`id_instructor`, `id_user_instructor`, `name_instructor`, `email_instructor`, `phone_instructor`, `profile_image_instructor`, `website_instructor`, `facebook_instructor`, `linkedin_instructor`, `twitter_instructor`, `created_at`, `updated_at`) VALUES
(2, 1, 'keyla', 'keylacelestino@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-09 12:26:26', '2025-10-09 12:26:26');

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id_lesson` int UNSIGNED NOT NULL,
  `id_module_lesson` int UNSIGNED NOT NULL,
  `title_lesson` varchar(150) NOT NULL,
  `content_lesson` varchar(255) DEFAULT NULL,
  `type_lesson` varchar(255) DEFAULT NULL,
  `video_url_lesson` varchar(255) DEFAULT NULL,
  `duration_lesson` int DEFAULT NULL,
  `position_lesson` int DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`id_lesson`, `id_module_lesson`, `title_lesson`, `content_lesson`, `type_lesson`, `video_url_lesson`, `duration_lesson`, `position_lesson`, `created_at`, `updated_at`) VALUES
(150, 17, '01 - Introdução', NULL, 'video', 'https://vimeo.com/1125221290', 5, 1, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(151, 17, '02 - Objectos', NULL, 'video', 'https://vimeo.com/1125221511', 3, 2, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(152, 17, '03 - Workbook', NULL, 'video', 'https://vimeo.com/1125221622', 6, 3, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(153, 17, '04 - Objecto Worksheet', NULL, 'video', 'https://vimeo.com/1125227978', 4, 4, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(154, 17, '05 - Objecto Range', NULL, 'video', 'https://vimeo.com/1125221766', 2, 5, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(155, 17, '06 - Preenchimento automático - AutoFill', NULL, 'video', 'https://vimeo.com/1125221830', 5, 6, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(156, 17, '07 - Sequência de fibonacci', NULL, 'video', 'https://vimeo.com/1125221961', 3, 7, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(157, 17, '08 - Listas Personalizadas', NULL, 'video', 'https://vimeo.com/1125222084', 5, 8, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(158, 17, '09 - Comentários', NULL, 'video', 'https://vimeo.com/1125222235', 4, 9, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(159, 17, '10 - Copiar e Colocar', NULL, 'video', 'https://vimeo.com/1125222353', 6, 10, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(160, 17, '11 - Copiar, Colocar, Transpose e Format Painter', NULL, 'video', 'https://vimeo.com/1125222521', 5, 11, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(161, 17, '12 - Ocultar colunas e linhas', NULL, 'video', 'https://vimeo.com/1125222746', 7, 12, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(162, 17, '13 - Manipulação de colunas e linhas', NULL, 'video', 'https://vimeo.com/1125222963', 6, 13, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(163, 17, '14 - Mover colunas e linhas', NULL, 'video', 'https://vimeo.com/1125223062', 3, 14, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(164, 17, '15 - Preenchimento Relâmpago - Flash Fill', NULL, 'video', 'https://vimeo.com/1126152954?share=copy', 4, 15, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(165, 18, ' 16 - Tipos de Dados', NULL, 'video', 'https://vimeo.com/1125223285', 9, 1, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(166, 18, '17 - Operadores aritméticos', NULL, 'video', 'https://vimeo.com/1125223553', 3, 2, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(167, 18, '18 - Preenchimento Automático (AutoFill)', NULL, 'video', 'https://vimeo.com/1125223639', 5, 3, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(168, 18, '19 - Fórmulas e Funções', NULL, 'video', 'https://vimeo.com/1125223741', 5, 4, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(169, 18, '20 - Referências Relativas, Absolutas e Mistas', NULL, 'video', 'https://vimeo.com/1125223880', 5, 5, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(170, 18, '21 - Formatação de Número, Data e Texto', NULL, 'video', 'https://vimeo.com/1125223985', 10, 6, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(171, 18, '22 - Formatação para Fracções, Moedas, Textos e Números', NULL, 'video', 'https://vimeo.com/1125224140', 7, 7, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(172, 18, '23 - Formato Personalizado de Moedas e Números', NULL, 'video', 'https://vimeo.com/1125224473', 4, 8, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(173, 18, '24 - Formato Personalizado de  Texto com Números', NULL, 'video', 'https://vimeo.com/1125224576', 7, 9, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(174, 18, '25 - Formato Personalizado de Data', NULL, 'video', 'https://vimeo.com/1125224842', 4, 10, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(175, 18, '26 - Formatação de Celulas: Font, Alinhamento, Estilos e Temas', NULL, 'video', 'https://vimeo.com/1125224908', 12, 11, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(176, 18, '27 - Formatação de Celulas: Superescrito e SubescritoStrike', NULL, 'video', 'https://vimeo.com/1125225216', 4, 12, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(177, 18, '28 - Pratica: Formatação Parte 1', NULL, 'video', 'https://vimeo.com/1125225356', 6, 13, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(178, 18, '29 - Pratica: Formatação Parte 2', NULL, 'video', 'https://vimeo.com/1125225522', 7, 14, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(179, 19, '30 - Apresentação do Modulo', NULL, 'video', 'https://vimeo.com/1125225726', 3, 1, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(180, 19, '31 - Função SUM e Product (SOMA e PRODUTO)', NULL, 'video', 'https://vimeo.com/1125225851', 7, 2, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(181, 19, '32 - Funções Basicas - AVERAGE, MIN, MAX, COUNT, COUNTA, COUNTBLANK', NULL, 'video', 'https://vimeo.com/1125226350', 6, 3, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(182, 19, '33 - Funções Logicas - Compradores, AND(E) e OR (OU)', NULL, 'video', 'https://vimeo.com/1126191640', 6, 4, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(183, 19, '34 - Funções de Logicas Not(Não) e Tabela de Verdade', NULL, 'video', 'https://vimeo.com/1125226509', 10, 5, '2025-10-10 16:31:22', '2025-10-10 16:31:22');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint UNSIGNED NOT NULL,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2020-12-28-223112', 'CodeIgniter\\Shield\\Database\\Migrations\\CreateAuthTables', 'default', 'CodeIgniter\\Shield', 1759954055, 1),
(2, '2021-07-04-041948', 'CodeIgniter\\Settings\\Database\\Migrations\\CreateSettingsTable', 'default', 'CodeIgniter\\Settings', 1759954055, 1),
(3, '2021-11-14-143905', 'CodeIgniter\\Settings\\Database\\Migrations\\AddContextColumn', 'default', 'CodeIgniter\\Settings', 1759954055, 1),
(4, '2025-09-29-144753', 'App\\Database\\Migrations\\CreatePendingUsers', 'default', 'App', 1759954055, 1),
(5, '2025-10-15-132005', 'App\\Database\\Migrations\\CreateEnrollmentLessons', 'default', 'App', 1760535487, 2),
(6, '2025-10-15-142116', 'App\\Database\\Migrations\\AddIndexesModulesLessons', 'default', 'App', 1760538100, 3);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id_module` int UNSIGNED NOT NULL,
  `id_course_module` int UNSIGNED NOT NULL,
  `title_module` varchar(150) NOT NULL,
  `description_module` text,
  `position_module` int DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id_module`, `id_course_module`, `title_module`, `description_module`, `position_module`, `created_at`, `updated_at`) VALUES
(17, 18, 'Ponto de Partida: Núcleo Crítico do Excel', '', 1, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(18, 18, 'MÓDULO BÁSICO:  Funções de texto e data', '', 2, '2025-10-10 16:31:22', '2025-10-10 16:31:22'),
(19, 18, 'Formulas e Funções', '', 3, '2025-10-10 16:31:22', '2025-10-10 16:31:22');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id_payment` int UNSIGNED NOT NULL,
  `id_user_payment` int UNSIGNED NOT NULL,
  `id_course_payment` int UNSIGNED NOT NULL,
  `id_enrollment_payment` int UNSIGNED NOT NULL,
  `method_payment` enum('mpesa/emola','paypal','credit_card') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'mpesa/emola',
  `amount_payment` decimal(10,2) NOT NULL,
  `proof_file_payment` text,
  `status_payment` enum('Pendente','Aprovado','Rejeitado') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'Pendente',
  `reference_payment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `approved_by_payment` int UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id_payment`, `id_user_payment`, `id_course_payment`, `id_enrollment_payment`, `method_payment`, `amount_payment`, `proof_file_payment`, `status_payment`, `reference_payment`, `approved_by_payment`, `created_at`, `updated_at`) VALUES
(23, 15, 18, 0, 'mpesa/emola', 1580.00, 'assets/img/1760114481_93c0ac2d9c69637d2ee7.png', 'Aprovado', 'PAY-2025-000023', 1, '2025-10-10 16:41:21', '2025-10-10 17:42:16'),
(25, 16, 18, 0, 'mpesa/emola', 1580.00, 'assets/img/1760352019_6e8b564262a8d9186e0c.png', 'Aprovado', 'PAY-2025-000025', 1, '2025-10-13 10:40:19', '2025-10-13 12:41:18');

-- --------------------------------------------------------

--
-- Table structure for table `pending_users`
--

CREATE TABLE `pending_users` (
  `id` int UNSIGNED NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `course_id` int UNSIGNED NOT NULL,
  `status` enum('waiting_payment','paid') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'waiting_payment',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `progress`
--

CREATE TABLE `progress` (
  `id_progress` int UNSIGNED NOT NULL,
  `id_enrollment_progress` int UNSIGNED NOT NULL,
  `id_lesson_progress` int UNSIGNED NOT NULL,
  `completed_at_progress` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Progresso por aula dentro da matrícula';

--
-- Dumping data for table `progress`
--

INSERT INTO `progress` (`id_progress`, `id_enrollment_progress`, `id_lesson_progress`, `completed_at_progress`, `created_at`, `updated_at`) VALUES
(1, 12, 150, '2025-10-15 18:19:23', '2025-10-15 16:15:23', '2025-10-15 18:19:23'),
(2, 12, 151, '2025-10-15 17:25:20', '2025-10-15 16:21:57', '2025-10-15 17:25:20'),
(3, 12, 152, '2025-10-17 11:17:39', '2025-10-15 16:22:10', '2025-10-17 11:17:39'),
(6, 12, 153, '2025-10-17 11:17:40', '2025-10-15 16:23:18', '2025-10-17 11:17:40'),
(7, 12, 154, NULL, '2025-10-15 16:23:20', '2025-10-15 16:29:43'),
(8, 12, 155, NULL, '2025-10-15 16:23:21', '2025-10-15 16:29:42'),
(13, 12, 156, NULL, '2025-10-15 16:27:48', '2025-10-15 16:29:42');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `value` text COLLATE utf8mb4_general_ci,
  `type` varchar(31) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'string',
  `context` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id_student` int UNSIGNED NOT NULL,
  `id_user_student` int UNSIGNED NOT NULL,
  `name_student` varchar(100) NOT NULL,
  `email_student` varchar(255) NOT NULL,
  `enrollment_number_student` varchar(50) DEFAULT NULL,
  `bio_student` text,
  `avatar_student` varchar(255) DEFAULT NULL,
  `status_student` enum('active','inactive','banned') DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id_student`, `id_user_student`, `name_student`, `email_student`, `enrollment_number_student`, `bio_student`, `avatar_student`, `status_student`, `created_at`, `updated_at`) VALUES
(16, 15, 'Hemel Aly', 'hemelaly02@gmail.com', NULL, NULL, NULL, 'active', '2025-10-10 16:42:16', '2025-10-10 16:42:16'),
(17, 16, 'Hemel Work', 'hemelwork02@gmail.com', NULL, NULL, NULL, 'active', '2025-10-13 10:41:18', '2025-10-13 10:41:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `role` enum('admin','student','instructor','') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'student',
  `username` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status_message` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `last_active` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role`, `username`, `status`, `status_message`, `active`, `last_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'instructor', 'keyla', NULL, NULL, 1, NULL, '2025-10-09 05:18:13', '2025-10-09 05:18:14', NULL),
(15, 'student', 'Hemel Aly', NULL, NULL, 0, NULL, '2025-10-10 16:42:11', '2025-10-10 16:42:11', NULL),
(16, 'student', 'Hemel Work', NULL, NULL, 0, NULL, '2025-10-13 10:41:13', '2025-10-13 10:41:13', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auth_groups_users`
--
ALTER TABLE `auth_groups_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `auth_groups_users_user_id_foreign` (`user_id`);

--
-- Indexes for table `auth_identities`
--
ALTER TABLE `auth_identities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_secret` (`type`,`secret`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `auth_logins`
--
ALTER TABLE `auth_logins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_type_identifier` (`id_type`,`identifier`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `auth_permissions_users`
--
ALTER TABLE `auth_permissions_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `auth_permissions_users_user_id_foreign` (`user_id`);

--
-- Indexes for table `auth_remember_tokens`
--
ALTER TABLE `auth_remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `selector` (`selector`),
  ADD KEY `auth_remember_tokens_user_id_foreign` (`user_id`);

--
-- Indexes for table `auth_token_logins`
--
ALTER TABLE `auth_token_logins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_type_identifier` (`id_type`,`identifier`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id_course`),
  ADD KEY `id_instructor` (`id_instructor_course`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id_enrollment`),
  ADD KEY `id_student_enrollment` (`id_student_enrollment`),
  ADD KEY `id_course_enrollment` (`id_course_enrollment`);

--
-- Indexes for table `instructors`
--
ALTER TABLE `instructors`
  ADD PRIMARY KEY (`id_instructor`),
  ADD KEY `id_user_instructor` (`id_user_instructor`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id_lesson`),
  ADD KEY `idx_lessons_module` (`id_module_lesson`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id_module`),
  ADD KEY `idx_modules_course` (`id_course_module`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id_payment`),
  ADD KEY `payments_ibfk_3` (`id_enrollment_payment`),
  ADD KEY `payments_ibfk_2` (`id_course_payment`);

--
-- Indexes for table `pending_users`
--
ALTER TABLE `pending_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `progress`
--
ALTER TABLE `progress`
  ADD PRIMARY KEY (`id_progress`),
  ADD UNIQUE KEY `uq_el_enrollment_lesson` (`id_enrollment_progress`,`id_lesson_progress`),
  ADD KEY `idx_el_enrollment` (`id_enrollment_progress`),
  ADD KEY `idx_el_lesson` (`id_lesson_progress`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id_student`),
  ADD UNIQUE KEY `enrollment_number_student` (`enrollment_number_student`),
  ADD KEY `id_user_student` (`id_user_student`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auth_groups_users`
--
ALTER TABLE `auth_groups_users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `auth_identities`
--
ALTER TABLE `auth_identities`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `auth_logins`
--
ALTER TABLE `auth_logins`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `auth_permissions_users`
--
ALTER TABLE `auth_permissions_users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `auth_remember_tokens`
--
ALTER TABLE `auth_remember_tokens`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `auth_token_logins`
--
ALTER TABLE `auth_token_logins`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id_course` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id_enrollment` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `instructors`
--
ALTER TABLE `instructors`
  MODIFY `id_instructor` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id_lesson` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=422;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id_module` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id_payment` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `pending_users`
--
ALTER TABLE `pending_users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `progress`
--
ALTER TABLE `progress`
  MODIFY `id_progress` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id_student` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auth_groups_users`
--
ALTER TABLE `auth_groups_users`
  ADD CONSTRAINT `auth_groups_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `auth_identities`
--
ALTER TABLE `auth_identities`
  ADD CONSTRAINT `auth_identities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `auth_permissions_users`
--
ALTER TABLE `auth_permissions_users`
  ADD CONSTRAINT `auth_permissions_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `auth_remember_tokens`
--
ALTER TABLE `auth_remember_tokens`
  ADD CONSTRAINT `auth_remember_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`id_instructor_course`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`id_student_enrollment`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`id_course_enrollment`) REFERENCES `courses` (`id_course`) ON DELETE CASCADE;

--
-- Constraints for table `instructors`
--
ALTER TABLE `instructors`
  ADD CONSTRAINT `instructors_ibfk_1` FOREIGN KEY (`id_user_instructor`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`id_module_lesson`) REFERENCES `modules` (`id_module`) ON DELETE CASCADE;

--
-- Constraints for table `modules`
--
ALTER TABLE `modules`
  ADD CONSTRAINT `modules_ibfk_1` FOREIGN KEY (`id_course_module`) REFERENCES `courses` (`id_course`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`id_course_payment`) REFERENCES `courses` (`id_course`) ON DELETE CASCADE;

--
-- Constraints for table `progress`
--
ALTER TABLE `progress`
  ADD CONSTRAINT `progress_id_enrollment_progress_foreign` FOREIGN KEY (`id_enrollment_progress`) REFERENCES `enrollments` (`id_enrollment`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `progress_id_lesson_progress_foreign` FOREIGN KEY (`id_lesson_progress`) REFERENCES `lessons` (`id_lesson`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`id_user_student`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
