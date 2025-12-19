-- EduBank Datenbank-Initialisierung
-- Diese Datei wird beim ersten Start des Containers ausgeführt

-- Zeichensatz sicherstellen
ALTER DATABASE edubank CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Hinweis: Die Tabellen werden durch CakePHP Migrations erstellt
-- Führe nach dem Start aus: docker-compose exec web bin/cake migrations migrate
