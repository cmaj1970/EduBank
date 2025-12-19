# EduBank – Installationsanleitung

Diese Anleitung beschreibt die Installation von EduBank für den produktiven Einsatz.

## Voraussetzungen

- **Webserver:** Apache oder Nginx mit mod_rewrite
- **PHP:** 7.2 oder höher (7.4 empfohlen)
- **PHP-Erweiterungen:** intl, mbstring, pdo_mysql, simplexml
- **Datenbank:** MySQL 5.7+ oder MariaDB 10.2+
- **Composer:** [getcomposer.org](https://getcomposer.org)

## Option 1: Mit Docker (empfohlen)

Die einfachste Methode für Schulen ohne eigene Server-Expertise.

### Schritt 1: Docker installieren

- **Windows/Mac:** [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- **Linux:** `apt install docker.io docker-compose` (Ubuntu/Debian)

### Schritt 2: EduBank herunterladen

```bash
git clone https://github.com/cmaj1970/EduBank.git
cd EduBank
```

### Schritt 3: Konfiguration erstellen

```bash
cp config/app.default.php config/app.php
```

Die Docker-Datenbank-Credentials sind bereits vorkonfiguriert:
- Host: `db`
- User: `edubank`
- Passwort: `edubank`
- Datenbank: `edubank`

### Schritt 4: Container starten

```bash
docker-compose up -d
```

**Hinweis:** Der erste Start dauert einige Minuten, da PHP-Extensions und Composer-Dependencies installiert werden.

Fortschritt prüfen:
```bash
docker-compose logs -f web
```

### Schritt 5: Datenbank initialisieren

Warten bis der Container läuft, dann:

```bash
# Schema importieren
docker-compose exec web mysql -h db -u root -proot edubank < db/schema.sql

# Admin-User anlegen
docker-compose exec web mysql -h db -u root -proot edubank < db/seed.sql
```

### Schritt 6: Fertig!

- **EduBank:** http://localhost:8080
- **phpMyAdmin:** http://localhost:8081

---

## Option 2: Manuelle Installation

Für Schulen mit eigenem Webserver (Apache/Nginx).

### Schritt 1: Code herunterladen

```bash
git clone https://github.com/cmaj1970/EduBank.git
cd EduBank
```

### Schritt 2: Abhängigkeiten installieren

```bash
composer install --no-dev --optimize-autoloader
```

### Schritt 3: Konfiguration

Kopiere die Beispiel-Konfiguration:

```bash
cp config/app.default.php config/app.php
```

Bearbeite `config/app.php` und trage die Datenbank-Zugangsdaten ein:

```php
'Datasources' => [
    'default' => [
        'host' => 'localhost',
        'username' => 'edubank',
        'password' => 'DEIN_PASSWORT',
        'database' => 'edubank',
    ],
],
```

### Schritt 4: Datenbank erstellen

```sql
CREATE DATABASE edubank CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'edubank'@'localhost' IDENTIFIED BY 'DEIN_PASSWORT';
GRANT ALL PRIVILEGES ON edubank.* TO 'edubank'@'localhost';
FLUSH PRIVILEGES;
```

### Schritt 5: Tabellen und Admin-User anlegen

```bash
# Schema importieren
mysql -u edubank -p edubank < db/schema.sql

# Admin-User anlegen
mysql -u edubank -p edubank < db/seed.sql
```

### Schritt 6: Webserver konfigurieren

**Apache** – DocumentRoot auf `/pfad/zu/EduBank/webroot` setzen:

```apache
<VirtualHost *:80>
    ServerName edubank.meineschule.at
    DocumentRoot /var/www/EduBank/webroot

    <Directory /var/www/EduBank/webroot>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Nginx:**

```nginx
server {
    listen 80;
    server_name edubank.meineschule.at;
    root /var/www/EduBank/webroot;

    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php7.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Schritt 7: Berechtigungen setzen

```bash
chmod -R 775 logs tmp
chown -R www-data:www-data logs tmp
```

---

## Erste Schritte nach der Installation

### Admin-Login

Nach dem Import von `seed.sql` existiert ein Superadmin:

| Feld | Wert |
|------|------|
| **Benutzername** | `admin` |
| **Passwort** | `EduBank1234!` |

**Wichtig:** Ändere das Passwort nach dem ersten Login!

### Schule einrichten

1. Als Admin einloggen
2. Neue Schule anlegen (Name, Kurzname)
3. Schuladmin-Benutzer erstellen
4. Konten für Schüler:innen anlegen

---

## Fehlerbehebung

### "500 Internal Server Error"

- Prüfe die Logs: `tail -f logs/error.log`
- Prüfe Schreibrechte auf `logs/` und `tmp/`

### "Database connection failed"

- Prüfe Zugangsdaten in `config/app.php`
- Prüfe ob MySQL/MariaDB läuft

### Seite lädt, aber ohne Styles

- DocumentRoot muss auf `webroot/` zeigen, nicht auf das Hauptverzeichnis

### Docker: Container startet nicht (Apple Silicon)

Falls du einen Mac mit M1/M2/M3-Chip hast und Fehler wie "no matching manifest for linux/arm64" siehst:
- Die docker-compose.yml enthält bereits `platform: linux/amd64`
- Falls trotzdem Probleme: Docker Desktop → Settings → Features in Development → "Use Rosetta" aktivieren

---

## Hilfe

- [GitHub Issues](https://github.com/cmaj1970/EduBank/issues) – Probleme melden
- [GitHub Discussions](https://github.com/cmaj1970/EduBank/discussions) – Fragen stellen
