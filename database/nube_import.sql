-- ================================================
-- Nube Database Import for MySQL/phpMyAdmin
-- Run this on a fresh database
-- ================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('superuser','admin','operatore') NOT NULL DEFAULT 'operatore',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password reset tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Chat Sections
CREATE TABLE IF NOT EXISTS `chat_sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chat_sections_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Chat Q&As
CREATE TABLE IF NOT EXISTS `chat_qas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `chat_section_id` bigint unsigned NOT NULL,
  `question` varchar(500) NOT NULL,
  `answer` text NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chat_qas_chat_section_id_foreign` (`chat_section_id`),
  CONSTRAINT `chat_qas_chat_section_id_foreign` FOREIGN KEY (`chat_section_id`) REFERENCES `chat_sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog Posts
CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title_en` varchar(255) NOT NULL,
  `title_it` varchar(255) NOT NULL,
  `slug_en` varchar(255) NOT NULL,
  `slug_it` varchar(255) NOT NULL,
  `text_en` text NOT NULL,
  `text_it` text NOT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `meta_description_en` varchar(500) DEFAULT NULL,
  `meta_description_it` varchar(500) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_posts_slug_en_unique` (`slug_en`),
  UNIQUE KEY `blog_posts_slug_it_unique` (`slug_it`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog Suggestions
CREATE TABLE IF NOT EXISTS `blog_suggestions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `source_url` varchar(255) NOT NULL,
  `source_name` varchar(255) NOT NULL,
  `original_title` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `title_it` varchar(255) NOT NULL,
  `text_en` text NOT NULL,
  `text_it` text NOT NULL,
  `meta_description_en` varchar(500) DEFAULT NULL,
  `meta_description_it` varchar(500) DEFAULT NULL,
  `cover_image_url` text DEFAULT NULL,
  `cover_image_path` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migrations table (so Laravel knows migrations are done)
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2024_01_01_000001_add_role_to_users_table', 2),
('2024_01_02_000001_create_chat_sections_table', 3),
('2024_01_02_000002_create_chat_qas_table', 3),
('2024_01_03_000001_create_blog_posts_table', 4),
('2024_01_04_000001_create_blog_suggestions_table', 5);

-- ================================================
-- Seed Data
-- ================================================

-- Super User (password: "password" - CHANGE THIS IN PRODUCTION!)
INSERT INTO `users` (`name`, `email`, `role`, `password`, `created_at`, `updated_at`) VALUES
('Super Admin', 'admin@nube.it', 'superuser', '$2y$12$sZhiMWPOqRGBCHDCaflyre1jBPCjEWxOEOJF4QLmSQibv4W2GXBHK', NOW(), NOW());

-- Chat Sections
INSERT INTO `chat_sections` (`name`, `slug`, `subtitle`, `sort_order`, `active`, `created_at`, `updated_at`) VALUES
('About Us', 'about-us', 'Learn about who we are and what we do', 1, 1, NOW(), NOW()),
('Portfolio', 'portfolio', 'Discover our projects and case studies', 2, 1, NOW(), NOW()),
('Contacts', 'contacts', 'Get in touch with our team', 3, 1, NOW(), NOW());

-- Chat Q&As - About Us
INSERT INTO `chat_qas` (`chat_section_id`, `question`, `answer`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Who we are', 'Nube is a partner that helps companies transform how they operate by deeply studying their business and identifying innovative solutions to address their needs, whether expressed or unexpressed. We approach each project with the goal of providing clear insights and developing technological solutions that revolutionize daily business operations.', 1, NOW(), NOW()),
(1, 'Our approach', 'At Nube, we begin by thoroughly studying the internal processes and workflows of our clients. We analyze how they operate, identify needs—both obvious and hidden—and propose innovative solutions. Once we have a deep understanding of the client''s reality, we develop tailored technologies to meet their needs and drive growth and efficiency in their business.', 2, NOW(), NOW()),
(1, 'What we do', 'At Nube, we tackle the technological challenges of companies by first gaining a deep understanding of their business. We collaborate with large companies like RDS, Eurobet, and Lux Holding, listening to their needs, studying their operational context, and proposing customized technological solutions. Thanks to this flexible approach, we continuously learn new languages and techniques to deliver concrete results, leveraging advanced technologies such as artificial intelligence and automation to anticipate future needs.', 3, NOW(), NOW()),
(1, 'Our values', 'Our work is based on attentive listening and in-depth analysis. We are partners to our clients, guiding them in discovering solutions that improve their work and optimize business processes. At Nube, every project stems from the belief that innovation and quality can radically transform the efficiency and potential of the companies we collaborate with.', 4, NOW(), NOW()),
(1, 'Our team', 'The Nube team is made up of expert professionals who work closely with clients to understand every aspect of their business. We study how their processes function, identify areas for improvement, and develop tailored technological solutions, ready to meet current challenges and support future growth.', 5, NOW(), NOW()),
(1, 'Our commitment to clients', 'At Nube, we are committed to being true allies to our clients. Every relationship begins with a phase of in-depth analysis and listening, during which we study business processes and operational dynamics. Only after this phase do we propose technological solutions that precisely meet the client''s needs and develop projects that help companies grow and confidently face the future.', 6, NOW(), NOW());

-- Chat Q&As - Portfolio
INSERT INTO `chat_qas` (`chat_section_id`, `question`, `answer`, `sort_order`, `created_at`, `updated_at`) VALUES
(2, 'Automated ticketing system for RDS', 'For RDS, one of the leading Italian radio stations, we developed an automated ticketing system for their Summer Festival. This event involved over 300,000 people across 6 different cities. We created a simple and intuitive interface for end users, with SSO login integration and immediate ticket generation with a QR code. The project also included the development of an app for the staff, which simplified and accelerated the check-in process at events, ensuring a smooth experience for both participants and the organization.', 1, NOW(), NOW()),
(2, 'Debugging and optimization for gaming platforms', 'A leading multinational in the online gaming sector entrusted us with the task of optimizing and debugging their slot machine platform, which serves over 300,000 active players monthly. We stress-tested the platform, identifying and resolving critical bugs that could have compromised the gaming experience and caused disruptions for customers. Our intervention ensured the platform operated smoothly, improving reliability and security for players.', 2, NOW(), NOW()),
(2, 'Custom CRM for Lux Holding', 'For Lux Holding, a multinational managing over 5 million tickets sold annually, we developed a custom CRM capable of centralizing data from various sources and analyzing market trends through integrated artificial intelligence. The system provided Lux Holding with a comprehensive and unified view of their operations, enabling them to make faster, more informed decisions. We also integrated a digital call center that allowed the team to manage communications from multiple platforms in a single interface.', 3, NOW(), NOW()),
(2, 'Mass WhatsApp messaging system for an internet service provider', 'A major internet service provider needed to efficiently communicate with its customers to inform them about connection updates. We developed a mass messaging system via WhatsApp, which achieved a 99% delivery rate and an 80% open rate—significantly higher than traditional SMS, which only achieved a 30% open rate. This solution allowed the provider to improve communication effectiveness, with over 80% of contacted users completing their connection upgrade in record time.', 4, NOW(), NOW()),
(2, 'Innovative call center platform for Enpaia', 'For Enpaia, the National Social Security Institution, we designed a VoIP call center platform that revolutionized the way the institution manages communications with its members. The platform allows operators to work remotely, handling calls and tickets from a cloud-based platform integrated with other features such as appointment scheduling via video calls. Thanks to our system, Enpaia improved operational flexibility and customer service quality, reducing wait times and optimizing resources.', 5, NOW(), NOW());

-- Chat Q&As - Contacts
INSERT INTO `chat_qas` (`chat_section_id`, `question`, `answer`, `sort_order`, `created_at`, `updated_at`) VALUES
(3, 'Contact us via mobile', 'You can reach us directly at +39 340 8538104. We are also available on Telegram and WhatsApp. Chat on WhatsApp: https://wa.me/393408538104 — Chat on Telegram: https://t.me/nubesoftware', 1, NOW(), NOW()),
(3, 'Contact us via email', 'For any inquiries or information, you can also send us an email at info@nubelab.it.', 2, NOW(), NOW()),
(3, 'Follow us on social media', 'Stay updated on our latest news and projects by following us on our social media channels: Instagram: https://www.instagram.com/nubesoftware — Facebook: https://www.facebook.com/nubesoftware', 3, NOW(), NOW());

SET FOREIGN_KEY_CHECKS = 1;
