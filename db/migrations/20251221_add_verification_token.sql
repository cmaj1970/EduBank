-- Migration: Add email verification fields to schools table
-- Run this on existing databases to add verification functionality

ALTER TABLE `schools`
ADD COLUMN `verification_token` varchar(64) DEFAULT NULL AFTER `status`,
ADD COLUMN `verified_at` datetime DEFAULT NULL AFTER `verification_token`,
ADD KEY `idx_verification_token` (`verification_token`);

-- Set existing approved schools as verified
UPDATE `schools` SET `verified_at` = `created` WHERE `status` = 'approved';
