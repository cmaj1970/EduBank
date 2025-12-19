# EduBank - Initiale Seed-Daten
# Erstellt einen Superadmin-User für den ersten Login

# Superadmin-User
# Standard-Passwort: EduBank1234!
INSERT INTO `users` (`id`, `name`, `username`, `role`, `school_id`, `password`, `active`, `admin`, `created`, `modified`)
VALUES (1, 'Superadmin', 'admin', 'admin', 0, '$2y$10$N2H/YJe5s2qMTrMU17AuP.X5u4nlKXY5c/JZ9UoNDqEodDu7HEdQu', 1, 1, NOW(), NOW());

# Hinweis: Nach der Installation sollte das Passwort geändert werden!
