-- EduBank Database Initialization
-- This file is executed on first container start

-- Ensure character set
ALTER DATABASE edubank CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Note: Tables and seed data are imported manually via:
-- docker-compose exec web mysql -h db -u root -proot edubank < db/schema.sql
-- docker-compose exec web mysql -h db -u root -proot edubank < db/seed.sql
