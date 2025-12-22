/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 80041
 Source Host           : localhost:3306
 Source Schema         : dlqr

 Target Server Type    : MySQL
 Target Server Version : 80041
 File Encoding         : 65001

 Date: 22/12/2025 17:17:09
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for camps
-- ----------------------------
DROP TABLE IF EXISTS `camps`;
CREATE TABLE `camps`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of camps
-- ----------------------------
INSERT INTO `camps` VALUES (1, 'Combat School Diyathalawa', 'DLA');
INSERT INTO `camps` VALUES (2, 'Academy China Bay', 'CBY');
INSERT INTO `camps` VALUES (3, 'SLAF Station Palavi', 'PLV');
INSERT INTO `camps` VALUES (4, 'SLAF Station Ampara', 'AMP');
INSERT INTO `camps` VALUES (5, 'Trade Training School Ekala', 'EKA');
INSERT INTO `camps` VALUES (6, 'SLAF Headquarters', 'SJP');
INSERT INTO `camps` VALUES (7, 'SLAF Base Katunayake', 'KAT');
INSERT INTO `camps` VALUES (8, 'SLAF Base Ratmalana', 'RMA');

-- ----------------------------
-- Table structure for certificates
-- ----------------------------
DROP TABLE IF EXISTS `certificates`;
CREATE TABLE `certificates`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `certificate_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `date_of_issue` date NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `service_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `rank` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `passport_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nic_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `date_of_enlistment` date NULL DEFAULT NULL,
  `date_of_retirement` date NULL DEFAULT NULL,
  `total_service` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `experience` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `qualifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `issuing_authority_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `issuing_authority_rank` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `issuing_authority_appointment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `issuing_authority_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `issuing_authority_contact` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `verified_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `verified_date` date NULL DEFAULT NULL,
  `certificate_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `directorate_id` int NULL DEFAULT NULL,
  `camp_id` int NULL DEFAULT NULL,
  `institute_id` int NULL DEFAULT NULL,
  `verification_status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'pending',
  `verified_by_admin` int NULL DEFAULT NULL,
  `admin_verification_date` datetime(0) NULL DEFAULT NULL,
  `rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `admin_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `is_active` tinyint(1) NULL DEFAULT 1,
  `type` int NULL DEFAULT NULL,
  `course_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `course_duration` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `course_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `dt_approved` tinyint(1) NULL DEFAULT 0,
  `dt_approved_by` int NULL DEFAULT NULL,
  `dt_approval_date` datetime(0) NULL DEFAULT NULL,
  `dt_rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `directorate_id`(`directorate_id`) USING BTREE,
  INDEX `verified_by_admin`(`verified_by_admin`) USING BTREE,
  INDEX `certificate_id`(`certificate_id`) USING BTREE,
  INDEX `idx_dt_approved`(`dt_approved`) USING BTREE,
  INDEX `idx_type_dt`(`type`, `dt_approved`) USING BTREE,
  CONSTRAINT `certificates_ibfk_1` FOREIGN KEY (`directorate_id`) REFERENCES `directorates` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `certificates_ibfk_2` FOREIGN KEY (`verified_by_admin`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 437 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of certificates
-- ----------------------------
INSERT INTO `certificates` VALUES (441, '123456', '2024-11-21', '1.jpg', 'Wayne Meadows', 'Labore id officia q', 'Aircraftsman', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Britanni Hurley', 'Perspiciatis deseru', 'Sunt delectus labor', 'cezy@mailinator.com', 'Placeat ea reprehen', 'Quo aspernatur volup', '2010-07-21', '1bb1314b-ae7e-482f-8f96-3346d3839d7e', 8, 2, 4, 'approved', 42, '2025-12-22 16:51:45', '<br />\r\n<b>Deprecated</b>:  htmlspecialchars(): Passing null to parameter #1 ($string) of type string is deprecated in <b>F:\\xampp versions\\8.2\\htdocs\\verificationlog\\qr_admin\\verify-certificate.php</b> on line <b>351</b><br />\r\n', 'Forwarded to DT | DT Notes: Approval from DT                    ', 1, 1, 'Rama Bonner', 'Et corrupti sed et ', 'Voluptatibus molesti', 1, 42, '2025-12-22 17:05:51', NULL);
INSERT INTO `certificates` VALUES (442, '456456', '2022-05-18', '3.jpg', 'Zoe Hebert', 'Minim dolor eum magn', 'Squadron Leader', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Levi Garner', 'Animi modi enim ex ', 'Vitae porro beatae q', 'zovo@mailinator.com', 'Ut exercitation recu', 'Aut distinctio Hic ', '2018-10-30', '6bb7953c-00ba-40c3-8f8b-989f64e99d21', NULL, 8, 6, 'pending', NULL, NULL, NULL, NULL, 1, 2, 'Xanthus Cobb', 'Ipsum quia accusanti', 'Excepteur aut tempor', 0, NULL, NULL, NULL);
INSERT INTO `certificates` VALUES (443, '123456789', '1981-02-10', '4.jpg', 'Yetta Spears', 'Ad vero cum eligendi', 'Aircraftsman', 'Commodi omnis quaera', 'Similique rerum mini', '2022-10-24', '1999-08-26', 'Libero eiusmod eius ', 'Impedit rerum est ', 'Qui in dolorem facer', 'Lacota Koch', 'Non enim possimus e', 'Et officia pariatur', 'puwovufo@mailinator.com', 'Enim ut repellendus', 'Sit tenetur nesciun', '1984-10-07', '61fc83ef-b61d-492a-81b3-ea013a428c60', 1, 0, 0, 'approved', 38, '2025-12-22 17:15:31', '                                                                                        ', '                                                     ', 1, 3, NULL, NULL, NULL, 0, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for certificates_copy1
-- ----------------------------
DROP TABLE IF EXISTS `certificates_copy1`;
CREATE TABLE `certificates_copy1`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `certificate_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `date_of_issue` date NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `service_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `rank` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `passport_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `nic_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `date_of_enlistment` date NULL DEFAULT NULL,
  `date_of_retirement` date NULL DEFAULT NULL,
  `total_service` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `experience` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `qualifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `issuing_authority_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `issuing_authority_rank` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `issuing_authority_appointment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `issuing_authority_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `issuing_authority_contact` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `verified_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `verified_date` date NULL DEFAULT NULL,
  `certificate_uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `directorate_id` int NULL DEFAULT NULL,
  `camp_id` int NULL DEFAULT NULL,
  `institute_id` int NULL DEFAULT NULL,
  `verification_status` enum('pending','approved','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT 'pending',
  `verified_by_admin` int NULL DEFAULT NULL,
  `admin_verification_date` datetime(0) NULL DEFAULT NULL,
  `rejection_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `admin_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `is_active` tinyint(1) NULL DEFAULT 1,
  `type` int NULL DEFAULT NULL,
  `course_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `course_duration` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `course_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `directorate_id`(`directorate_id`) USING BTREE,
  INDEX `verified_by_admin`(`verified_by_admin`) USING BTREE,
  INDEX `certificate_id`(`certificate_id`) USING BTREE,
  CONSTRAINT `certificates_copy1_ibfk_1` FOREIGN KEY (`directorate_id`) REFERENCES `directorates` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `certificates_copy1_ibfk_2` FOREIGN KEY (`verified_by_admin`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 441 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of certificates_copy1
-- ----------------------------
INSERT INTO `certificates_copy1` VALUES (440, '123456', '2005-08-31', '1.jpg', 'Reagan Hinton', 'Laudantium voluptat', 'Warrant Officer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Jenette Stone', 'Eum nesciunt lorem ', 'Obcaecati eos adipi', 'zocunymo@mailinator.com', 'Duis labore at sed a', 'Et beatae qui aut si', '1984-11-01', '3dd46506-2e97-465a-8365-238246e837b6', NULL, 2, 4, 'pending', NULL, NULL, NULL, NULL, 1, 1, 'Calista Soto', 'Molestiae amet quae', 'Sit sunt aut aliqua');

-- ----------------------------
-- Table structure for certificates_jcsc
-- ----------------------------
DROP TABLE IF EXISTS `certificates_jcsc`;
CREATE TABLE `certificates_jcsc`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `certificate_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `certificate_uuid` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `date_of_issue` date NOT NULL,
  `recipient_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `course_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `course_dates` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `commanding_officer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `commanding_officer_rank` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `director_general_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `director_general_rank` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `verified_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `verified_date` date NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `updated_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of certificates_jcsc
-- ----------------------------
INSERT INTO `certificates_jcsc` VALUES (1, '123', '361ce8f6-f13f-4562-87b4-4f0497782ed3', '2025-11-27', 'Test', 'No.80 Junior Command and Staff Course', '2nd May 2026 to 11th August 2026', 'Test Test', 'Wing Commander', 'NHDN Dias', 'Air Vice Marshal', 'Test Verify', '2025-11-28', '2025-11-28 16:02:54', '2025-11-28 16:02:54');
INSERT INTO `certificates_jcsc` VALUES (2, '123345', 'b06d14f9-e931-4efe-b4ba-d7de14260dd2', '1981-03-26', 'Morgan Ballard', 'Kieran Davenport', '15-Apr-1981', 'Paki Wilkins', 'Qui exercitation per', 'Vivien Combs', 'Similique modi dolor', 'Accusamus illum fug', '2021-08-17', '2025-11-28 16:03:07', '2025-11-28 16:03:07');

-- ----------------------------
-- Table structure for directorates
-- ----------------------------
DROP TABLE IF EXISTS `directorates`;
CREATE TABLE `directorates`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `directorate_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of directorates
-- ----------------------------
INSERT INTO `directorates` VALUES (1, 'Directorate Of Logistics');
INSERT INTO `directorates` VALUES (2, 'Directorate of Health Services');
INSERT INTO `directorates` VALUES (3, 'Directorate of Air Operations');
INSERT INTO `directorates` VALUES (4, 'Directorate of Civil Engineering');
INSERT INTO `directorates` VALUES (5, 'Directorate of Electronics and Computer Engineering');
INSERT INTO `directorates` VALUES (6, 'Directorate of Administration');
INSERT INTO `directorates` VALUES (7, 'Flight Safety Inspectorate');
INSERT INTO `directorates` VALUES (8, 'Directorate of Training');
INSERT INTO `directorates` VALUES (10, 'Basic Trade Course TTS Ekala - Directorate of Training');
INSERT INTO `directorates` VALUES (11, 'Advanced Trade Course TTS Ekala - Directorate of Training');
INSERT INTO `directorates` VALUES (12, 'Administration');

-- ----------------------------
-- Table structure for directorates_copy1
-- ----------------------------
DROP TABLE IF EXISTS `directorates_copy1`;
CREATE TABLE `directorates_copy1`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `directorate_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of directorates_copy1
-- ----------------------------
INSERT INTO `directorates_copy1` VALUES (1, 'Directorate Of Logistics');
INSERT INTO `directorates_copy1` VALUES (2, 'Directorate of Health Services');
INSERT INTO `directorates_copy1` VALUES (3, 'Directorate of Air Operations');
INSERT INTO `directorates_copy1` VALUES (4, 'Directorate of Civil Engineering');
INSERT INTO `directorates_copy1` VALUES (5, 'Directorate of Electronics and Computer Engineering');
INSERT INTO `directorates_copy1` VALUES (6, 'Directorate of Administration');
INSERT INTO `directorates_copy1` VALUES (7, 'Flight Safety Inspectorate');
INSERT INTO `directorates_copy1` VALUES (8, 'Directorate of Training');
INSERT INTO `directorates_copy1` VALUES (10, 'Basic Trade Course TTS Ekala - Directorate of Training');
INSERT INTO `directorates_copy1` VALUES (11, 'Advanced Trade Course TTS Ekala - Directorate of Training');
INSERT INTO `directorates_copy1` VALUES (12, 'Administration');

-- ----------------------------
-- Table structure for training_institutes
-- ----------------------------
DROP TABLE IF EXISTS `training_institutes`;
CREATE TABLE `training_institutes`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `related_camp` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `related_camp`(`related_camp`) USING BTREE,
  CONSTRAINT `related_camp` FOREIGN KEY (`related_camp`) REFERENCES `camps` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of training_institutes
-- ----------------------------
INSERT INTO `training_institutes` VALUES (1, 'Ground Training Wing', 'GTW', 2);
INSERT INTO `training_institutes` VALUES (2, 'Flying Training Wing', 'FTW', 2);
INSERT INTO `training_institutes` VALUES (3, 'Trade Training School', 'TTS EKA', 5);
INSERT INTO `training_institutes` VALUES (4, 'Junior Command & Staff College', 'JCSC', 2);
INSERT INTO `training_institutes` VALUES (5, 'Combat Training School', 'CTS', 1);
INSERT INTO `training_institutes` VALUES (6, 'Flight Safety Inspectorate', 'FSI', 8);

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `training_institute` int NULL DEFAULT NULL,
  `camp` int NULL DEFAULT NULL,
  `directorate` int NULL DEFAULT NULL,
  `created_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `type` int NULL DEFAULT NULL,
  `role` enum('user','admin','verifier','super_admin','dt_admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'user',
  `is_active` tinyint(1) NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 41 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'qr_admin', '$2y$10$6rHldtv3a1ySA8mzPiG9OeSkQ2zgjGRm7XdYgiH7DD7f7hdEXCC/O', 0, 0, 0, '2024-08-29 16:47:01', 0, 'super_admin', 1);
INSERT INTO `users` VALUES (2, 'dhs_admin', '$2y$10$Edmmk8yZ.8a7lsCO66vg0e0Da1PS0MBnNyt6T43dyhTBIX6rO0KG6', NULL, NULL, NULL, '2024-09-05 13:05:27', NULL, 'user', 1);
INSERT INTO `users` VALUES (4, 'dl_admin', '$2y$10$kzBxNhYDuzvwgTzC.Z9ZpuNE06fv0jbKiLJ8g5JolZA8wddLMkUpK', NULL, NULL, NULL, '2024-09-05 13:10:53', NULL, 'user', 1);
INSERT INTO `users` VALUES (5, 'da_admin', '$2y$10$8x.URKkyQNNKXVQ3t9bYHuT.6.gVuBXOV42eF8UVzKefKRVm0s1VS', NULL, NULL, NULL, '2024-09-14 06:52:40', NULL, 'user', 1);
INSERT INTO `users` VALUES (12, 'fsi_admin', '$2y$10$0orpejDkj5KXhL4qjrb41.9puAcd0WMsMQSL9XEpz3fUW68ozT6vS', NULL, NULL, NULL, '2024-10-29 13:16:56', NULL, 'user', 1);
INSERT INTO `users` VALUES (13, 'dt_admin', '$2y$10$2gNP8VYyXcaWudLDC5LKjOxeN/o2VkiPCUhiFepu8RLSnxCc3BOaW', NULL, NULL, NULL, '2025-06-04 07:29:51', NULL, 'user', 1);
INSERT INTO `users` VALUES (14, 'dt_user', '$2y$10$eKwjLxiOLDV6Zi8CO/g5Q.nUwnvcrBSolVBu00VIXHFYsMK4pz0pG', NULL, NULL, NULL, '2025-06-04 07:37:36', NULL, 'user', 1);
INSERT INTO `users` VALUES (17, 'dt_admin_qr', '$2y$10$4GYyvQZua47UwKA9nHDdguzDMDXN28/iBz7F1bsZmmZ5QzFYCDnrC', NULL, NULL, NULL, '2025-08-25 06:59:58', NULL, 'user', 1);
INSERT INTO `users` VALUES (19, 'tts_basic_qr', '$2y$10$Q.Qo2ur6pY9Bfpenm0buEO7Kh6Nw8WAMymfsQEky/gcpgPN7ftvjC', NULL, NULL, NULL, '2025-09-15 06:43:48', NULL, 'user', 1);
INSERT INTO `users` VALUES (20, 'tts_advanced_qr', '$2y$10$BGjhfJF75yUHCN9l5kl0dO4xWSLJe0bj8uU.Ul10EGrR0qFnG4H3W', NULL, NULL, NULL, '2025-09-15 06:45:19', NULL, 'user', 1);
INSERT INTO `users` VALUES (35, 'dao user', '$2y$10$LwFhc8jO6XmlnjQ4EMtjoOc8r1k6EX5LgGkUwQeJ23eTGi0z24t26', 2, 2, NULL, '2025-12-18 05:25:22', 1, 'user', 1);
INSERT INTO `users` VALUES (36, 'leave user', '$2y$10$3H.CKrmSaa7jC8pnyQ8hvO506ln3skY.FxfNmIFTnK1laRimlVfM2', 0, 0, 1, '2025-12-18 05:47:19', 3, 'user', 1);
INSERT INTO `users` VALUES (38, 'leave admin', '$2y$10$IhzOOcQTMZ3EwKYoofCdeOg0dIWj.gh4Zd0j//0ooQ62Kx/JdqWia', 0, 0, 1, '2025-12-18 09:06:22', 3, 'admin', 1);
INSERT INTO `users` VALUES (39, 'underdt admin', '$2y$10$ETM5BZCRBQ1xkJ1ddSTkfOXUJN45R5iWFh3kfJlN0WQWLQ/VRP3ym', 4, 2, NULL, '2025-12-18 09:22:47', 1, 'admin', 1);
INSERT INTO `users` VALUES (40, 'underdt user', '$2y$10$qVEsp2rnqq2Mn78DeuwDaepofki1BpYRdWm.aSigoZsmHl3eH/Jry', 4, 2, NULL, '2025-12-18 09:23:06', 1, 'user', 1);
INSERT INTO `users` VALUES (41, 'notunderdt', '$2y$10$AyadRBS/Beh6OD3nmYWgn.fXkw5U9hNURIw8VpfkUmeS8aRXOVPdO', 6, 8, NULL, '2025-12-22 06:30:17', 2, 'user', 1);
INSERT INTO `users` VALUES (42, 'DT Admin', '$2y$10$3wqTzsQcuzKhUzMUCDQ8leU7yvpYiVrqBGrk24wZ/ZcTHMgO5GUCq', 0, 0, 8, '2025-12-22 11:18:26', 1, 'dt_admin', 1);

-- ----------------------------
-- Table structure for users_copy1
-- ----------------------------
DROP TABLE IF EXISTS `users_copy1`;
CREATE TABLE `users_copy1`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `training_institute` int NULL DEFAULT NULL,
  `camp` int NULL DEFAULT NULL,
  `directorate` int NOT NULL,
  `created_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `role` enum('user','admin','verifier','super_admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'user',
  `is_active` tinyint(1) NULL DEFAULT 1,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 28 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of users_copy1
-- ----------------------------
INSERT INTO `users_copy1` VALUES (1, 'qr_admin', '$2y$10$6rHldtv3a1ySA8mzPiG9OeSkQ2zgjGRm7XdYgiH7DD7f7hdEXCC/O', NULL, NULL, 12, '2024-08-29 16:47:01', 'admin', 1);
INSERT INTO `users_copy1` VALUES (2, 'dhs_admin', '$2y$10$Edmmk8yZ.8a7lsCO66vg0e0Da1PS0MBnNyt6T43dyhTBIX6rO0KG6', NULL, NULL, 2, '2024-09-05 13:05:27', 'user', 1);
INSERT INTO `users_copy1` VALUES (4, 'dl_admin', '$2y$10$kzBxNhYDuzvwgTzC.Z9ZpuNE06fv0jbKiLJ8g5JolZA8wddLMkUpK', NULL, NULL, 1, '2024-09-05 13:10:53', 'user', 1);
INSERT INTO `users_copy1` VALUES (5, 'da_admin', '$2y$10$8x.URKkyQNNKXVQ3t9bYHuT.6.gVuBXOV42eF8UVzKefKRVm0s1VS', NULL, NULL, 6, '2024-09-14 06:52:40', 'user', 1);
INSERT INTO `users_copy1` VALUES (12, 'fsi_admin', '$2y$10$0orpejDkj5KXhL4qjrb41.9puAcd0WMsMQSL9XEpz3fUW68ozT6vS', NULL, NULL, 7, '2024-10-29 13:16:56', 'user', 1);
INSERT INTO `users_copy1` VALUES (13, 'dt_admin', '$2y$10$2gNP8VYyXcaWudLDC5LKjOxeN/o2VkiPCUhiFepu8RLSnxCc3BOaW', NULL, NULL, 8, '2025-06-04 07:29:51', 'user', 1);
INSERT INTO `users_copy1` VALUES (14, 'dt_user', '$2y$10$eKwjLxiOLDV6Zi8CO/g5Q.nUwnvcrBSolVBu00VIXHFYsMK4pz0pG', NULL, NULL, 8, '2025-06-04 07:37:36', 'user', 1);
INSERT INTO `users_copy1` VALUES (17, 'dt_admin_qr', '$2y$10$4GYyvQZua47UwKA9nHDdguzDMDXN28/iBz7F1bsZmmZ5QzFYCDnrC', NULL, NULL, 8, '2025-08-25 06:59:58', 'user', 1);
INSERT INTO `users_copy1` VALUES (19, 'tts_basic_qr', '$2y$10$Q.Qo2ur6pY9Bfpenm0buEO7Kh6Nw8WAMymfsQEky/gcpgPN7ftvjC', NULL, NULL, 10, '2025-09-15 06:43:48', 'user', 1);
INSERT INTO `users_copy1` VALUES (20, 'tts_advanced_qr', '$2y$10$BGjhfJF75yUHCN9l5kl0dO4xWSLJe0bj8uU.Ul10EGrR0qFnG4H3W', NULL, NULL, 11, '2025-09-15 06:45:19', 'user', 1);
INSERT INTO `users_copy1` VALUES (27, 'test_admin', '$2y$10$RiOCj0wBNN/d13TVEjJmWuddSFLbmAJGbWUNhIT3M.qstBxKFc5OK', NULL, NULL, 6, '2025-11-26 08:38:46', 'user', 0);

-- ----------------------------
-- Table structure for verification_logs
-- ----------------------------
DROP TABLE IF EXISTS `verification_logs`;
CREATE TABLE `verification_logs`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `certificate_id` int NOT NULL,
  `admin_id` int NOT NULL,
  `action` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `created_at` datetime(0) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_certificate_id`(`certificate_id`) USING BTREE,
  INDEX `idx_admin_id`(`admin_id`) USING BTREE,
  INDEX `idx_created_at`(`created_at`) USING BTREE,
  CONSTRAINT `verification_logs_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of verification_logs
-- ----------------------------
INSERT INTO `verification_logs` VALUES (1, 438, 39, 'Certificate APPROVED', '::1', 'Forwarded for DT approval', '2025-12-22 15:16:14');
INSERT INTO `verification_logs` VALUES (2, 441, 39, 'Certificate APPROVED', '::1', 'Forwarded to DT', '2025-12-22 16:27:08');
INSERT INTO `verification_logs` VALUES (3, 441, 42, 'Certificate APPROVED', '::1', 'Forwarded to DT', '2025-12-22 16:51:45');
INSERT INTO `verification_logs` VALUES (4, 441, 42, 'DT Approval: APPROVED', '::1', 'Approval from DT                    ', '2025-12-22 17:05:51');
INSERT INTO `verification_logs` VALUES (5, 443, 38, 'Verification: PENDING', '::1', '                                            ', '2025-12-22 17:14:00');
INSERT INTO `verification_logs` VALUES (6, 443, 36, 'Certificate PENDING', '::1', '         ', '2025-12-22 17:14:47');
INSERT INTO `verification_logs` VALUES (7, 443, 38, 'Verification: APPROVED', '::1', '                                                     ', '2025-12-22 17:15:31');

SET FOREIGN_KEY_CHECKS = 1;
