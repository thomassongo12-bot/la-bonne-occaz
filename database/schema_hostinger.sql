-- ============================================================
-- La Bonne Occaz - Schéma pour HOSTINGER
-- ⚠️  PAS de CREATE DATABASE ni USE — déjà sélectionnée dans phpMyAdmin
-- MySQL 5.7+ / 8.0+   Encodage : utf8mb4
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- TABLE : admins
-- ============================================================
CREATE TABLE IF NOT EXISTS `admins` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username`   VARCHAR(50)  NOT NULL UNIQUE,
    `email`      VARCHAR(150) NOT NULL UNIQUE,
    `password`   VARCHAR(255) NOT NULL,
    `role`       ENUM('super_admin','admin','editor') NOT NULL DEFAULT 'admin',
    `first_name` VARCHAR(80)  NOT NULL DEFAULT '',
    `last_name`  VARCHAR(80)  NOT NULL DEFAULT '',
    `is_active`  TINYINT(1)   NOT NULL DEFAULT 1,
    `last_login` DATETIME     NULL,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : brands
-- ============================================================
CREATE TABLE IF NOT EXISTS `brands` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`       VARCHAR(100) NOT NULL UNIQUE,
    `slug`       VARCHAR(120) NOT NULL UNIQUE,
    `logo_url`   VARCHAR(255) NULL,
    `is_active`  TINYINT(1)   NOT NULL DEFAULT 1,
    `sort_order` INT          NOT NULL DEFAULT 0,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : fuel_types
-- ============================================================
CREATE TABLE IF NOT EXISTS `fuel_types` (
    `id`   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(60) NOT NULL UNIQUE,
    `slug` VARCHAR(70) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : transmissions
-- ============================================================
CREATE TABLE IF NOT EXISTS `transmissions` (
    `id`   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(60) NOT NULL UNIQUE,
    `slug` VARCHAR(70) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : body_types
-- ============================================================
CREATE TABLE IF NOT EXISTS `body_types` (
    `id`   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(60) NOT NULL UNIQUE,
    `slug` VARCHAR(70) NOT NULL UNIQUE,
    `icon` VARCHAR(100) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : vehicles
-- ============================================================
CREATE TABLE IF NOT EXISTS `vehicles` (
    `id`                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `slug`                VARCHAR(255) NOT NULL UNIQUE,
    `brand_id`            INT UNSIGNED NOT NULL,
    `model`               VARCHAR(120) NOT NULL,
    `version`             VARCHAR(180) NULL,
    `year`                SMALLINT UNSIGNED NOT NULL,
    `mileage`             INT UNSIGNED NOT NULL DEFAULT 0,
    `fuel_type_id`        INT UNSIGNED NOT NULL,
    `transmission_id`     INT UNSIGNED NOT NULL,
    `body_type_id`        INT UNSIGNED NULL,
    `color`               VARCHAR(80)  NULL,
    `doors`               TINYINT UNSIGNED NULL,
    `seats`               TINYINT UNSIGNED NULL,
    `power_hp`            SMALLINT UNSIGNED NULL,
    `power_kw`            SMALLINT UNSIGNED NULL,
    `engine_cc`           SMALLINT UNSIGNED NULL,
    `co2_emission`        SMALLINT UNSIGNED NULL,
    `energy_rating`       CHAR(1)      NULL,
    `price`               DECIMAL(10,2) NOT NULL,
    `price_promo`         DECIMAL(10,2) NULL,
    `is_featured`         TINYINT(1)   NOT NULL DEFAULT 0,
    `is_new_badge`        TINYINT(1)   NOT NULL DEFAULT 0,
    `status`              ENUM('available','sold','reserved','pending','draft') NOT NULL DEFAULT 'draft',
    `condition_state`     ENUM('excellent','very_good','good','fair') NOT NULL DEFAULT 'good',
    `description`         TEXT         NULL,
    `has_service_history` TINYINT(1)   NOT NULL DEFAULT 0,
    `has_warranty`        TINYINT(1)   NOT NULL DEFAULT 0,
    `warranty_months`     TINYINT UNSIGNED NULL,
    `has_control_tech`    TINYINT(1)   NOT NULL DEFAULT 0,
    `registration_number` VARCHAR(20)  NULL,
    `vin`                 VARCHAR(50)  NULL,
    `first_registration`  DATE         NULL,
    `equipment`           JSON         NULL,
    `meta_title`          VARCHAR(160) NULL,
    `meta_description`    VARCHAR(320) NULL,
    `published_at`        DATETIME     NULL,
    `created_at`          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_vehicle_brand`        FOREIGN KEY (`brand_id`)        REFERENCES `brands`(`id`)        ON DELETE RESTRICT,
    CONSTRAINT `fk_vehicle_fuel`         FOREIGN KEY (`fuel_type_id`)    REFERENCES `fuel_types`(`id`)    ON DELETE RESTRICT,
    CONSTRAINT `fk_vehicle_transmission` FOREIGN KEY (`transmission_id`) REFERENCES `transmissions`(`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_vehicle_body`         FOREIGN KEY (`body_type_id`)    REFERENCES `body_types`(`id`)    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `idx_vehicles_status`   ON `vehicles`(`status`);
CREATE INDEX `idx_vehicles_brand`    ON `vehicles`(`brand_id`);
CREATE INDEX `idx_vehicles_year`     ON `vehicles`(`year`);
CREATE INDEX `idx_vehicles_price`    ON `vehicles`(`price`);
CREATE INDEX `idx_vehicles_mileage`  ON `vehicles`(`mileage`);
CREATE INDEX `idx_vehicles_featured` ON `vehicles`(`is_featured`);
CREATE INDEX `idx_vehicles_fuel`     ON `vehicles`(`fuel_type_id`);
CREATE INDEX `idx_vehicles_trans`    ON `vehicles`(`transmission_id`);

-- ============================================================
-- TABLE : vehicle_images
-- ============================================================
CREATE TABLE IF NOT EXISTS `vehicle_images` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `vehicle_id` INT UNSIGNED NOT NULL,
    `filename`   VARCHAR(255) NOT NULL,
    `alt_text`   VARCHAR(200) NULL,
    `is_main`    TINYINT(1)   NOT NULL DEFAULT 0,
    `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_image_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `idx_images_vehicle` ON `vehicle_images`(`vehicle_id`);
CREATE INDEX `idx_images_main`    ON `vehicle_images`(`is_main`);

-- ============================================================
-- TABLE : contact_requests
-- ============================================================
CREATE TABLE IF NOT EXISTS `contact_requests` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `type`       ENUM('contact','vehicle_info','appointment','financing') NOT NULL DEFAULT 'contact',
    `vehicle_id` INT UNSIGNED NULL,
    `first_name` VARCHAR(80)  NOT NULL,
    `last_name`  VARCHAR(80)  NOT NULL,
    `email`      VARCHAR(150) NOT NULL,
    `phone`      VARCHAR(25)  NULL,
    `message`    TEXT         NOT NULL,
    `status`     ENUM('new','processing','contacted','done','refused') NOT NULL DEFAULT 'new',
    `admin_note` TEXT         NULL,
    `ip_address` VARCHAR(45)  NULL,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_contact_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX `idx_contact_status` ON `contact_requests`(`status`);
CREATE INDEX `idx_contact_type`   ON `contact_requests`(`type`);

-- ============================================================
-- TABLE : selling_requests
-- ============================================================
CREATE TABLE IF NOT EXISTS `selling_requests` (
    `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `first_name`      VARCHAR(80)  NOT NULL,
    `last_name`       VARCHAR(80)  NOT NULL,
    `email`           VARCHAR(150) NOT NULL,
    `phone`           VARCHAR(25)  NOT NULL,
    `brand`           VARCHAR(100) NOT NULL,
    `model`           VARCHAR(120) NOT NULL,
    `version`         VARCHAR(180) NULL,
    `year`            SMALLINT UNSIGNED NOT NULL,
    `mileage`         INT UNSIGNED NOT NULL,
    `fuel_type`       VARCHAR(60)  NOT NULL,
    `transmission`    VARCHAR(60)  NOT NULL,
    `color`           VARCHAR(80)  NULL,
    `condition_state` VARCHAR(60)  NULL,
    `desired_price`   DECIMAL(10,2) NULL,
    `description`     TEXT         NULL,
    `status`          ENUM('new','evaluating','accepted','rejected','sold') NOT NULL DEFAULT 'new',
    `admin_note`      TEXT         NULL,
    `ip_address`      VARCHAR(45)  NULL,
    `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : selling_request_images
-- ============================================================
CREATE TABLE IF NOT EXISTS `selling_request_images` (
    `id`                 INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `selling_request_id` INT UNSIGNED NOT NULL,
    `filename`           VARCHAR(255) NOT NULL,
    `created_at`         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_sellimg_request` FOREIGN KEY (`selling_request_id`) REFERENCES `selling_requests`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : testimonials
-- ============================================================
CREATE TABLE IF NOT EXISTS `testimonials` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `author`      VARCHAR(100) NOT NULL,
    `photo_url`   VARCHAR(255) NULL,
    `location`    VARCHAR(100) NULL,
    `rating`      TINYINT UNSIGNED NOT NULL DEFAULT 5,
    `content`     TEXT NOT NULL,
    `vehicle_ref` VARCHAR(200) NULL,
    `is_active`   TINYINT(1)   NOT NULL DEFAULT 1,
    `sort_order`  INT          NOT NULL DEFAULT 0,
    `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : site_settings
-- ============================================================
CREATE TABLE IF NOT EXISTS `site_settings` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `setting_key`  VARCHAR(100) NOT NULL UNIQUE,
    `setting_val`  TEXT         NULL,
    `setting_type` ENUM('text','textarea','image','boolean','json') NOT NULL DEFAULT 'text',
    `label`        VARCHAR(200) NOT NULL,
    `group_name`   VARCHAR(80)  NOT NULL DEFAULT 'general',
    `updated_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : reservations
-- ============================================================
CREATE TABLE IF NOT EXISTS `reservations` (
    `id`                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `vehicle_id`           INT UNSIGNED NOT NULL,
    `first_name`           VARCHAR(80) NOT NULL,
    `last_name`            VARCHAR(80) NOT NULL,
    `email`                VARCHAR(150) NOT NULL,
    `phone`                VARCHAR(25) NOT NULL,
    `message`              TEXT NULL,
    `conditions_accepted`  TINYINT(1) NOT NULL DEFAULT 0,
    `status`               ENUM('new','confirmed','cancelled','completed') NOT NULL DEFAULT 'new',
    `admin_note`           TEXT NULL,
    `ip_address`           VARCHAR(45) NULL,
    `created_at`           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_res_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles`(`id`) ON DELETE CASCADE,
    INDEX `idx_res_status`  (`status`),
    INDEX `idx_res_vehicle` (`vehicle_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE : visitor_logs
-- ============================================================
CREATE TABLE IF NOT EXISTS `visitor_logs` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `visited_date` DATE NOT NULL,
    `ip_hash`      VARCHAR(64) NOT NULL,
    `page_url`     VARCHAR(255) NOT NULL DEFAULT '/',
    `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_vlog_date`    (`visited_date`),
    INDEX `idx_vlog_ip_date` (`ip_hash`, `visited_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
