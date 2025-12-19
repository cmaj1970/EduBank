# EduBank – Installation Guide

This guide describes how to install EduBank for production use.

## Requirements

- **Web Server:** Apache or Nginx with mod_rewrite
- **PHP:** 7.2 or higher (7.4 recommended)
- **PHP Extensions:** intl, mbstring, pdo_mysql, simplexml
- **Database:** MySQL 5.7+ or MariaDB 10.2+
- **Composer:** [getcomposer.org](https://getcomposer.org)

## Option 1: With Docker (recommended)

The easiest method for schools without server expertise.

### Step 1: Install Docker

- **Windows/Mac:** [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- **Linux:** `apt install docker.io docker-compose` (Ubuntu/Debian)

### Step 2: Download EduBank

```bash
git clone https://github.com/cmaj1970/EduBank.git
cd EduBank
```

### Step 3: Start containers

```bash
docker-compose up -d
```

### Step 4: Initialize database

```bash
# Import schema
docker-compose exec web mysql -h db -u root -proot edubank < db/schema.sql

# Create admin user
docker-compose exec web mysql -h db -u root -proot edubank < db/seed.sql
```

### Step 5: Done!

EduBank is available at: **http://localhost:8080**

---

## Option 2: Manual Installation

For schools with their own web server (Apache/Nginx).

### Step 1: Download code

```bash
git clone https://github.com/cmaj1970/EduBank.git
cd EduBank
```

### Step 2: Install dependencies

```bash
composer install --no-dev --optimize-autoloader
```

### Step 3: Configuration

Copy the example configuration:

```bash
cp config/app.default.php config/app.php
```

Edit `config/app.php` and enter your database credentials:

```php
'Datasources' => [
    'default' => [
        'host' => 'localhost',
        'username' => 'edubank',
        'password' => 'YOUR_PASSWORD',
        'database' => 'edubank',
    ],
],
```

### Step 4: Create database

```sql
CREATE DATABASE edubank CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'edubank'@'localhost' IDENTIFIED BY 'YOUR_PASSWORD';
GRANT ALL PRIVILEGES ON edubank.* TO 'edubank'@'localhost';
FLUSH PRIVILEGES;
```

### Step 5: Create tables and admin user

```bash
# Import schema
mysql -u edubank -p edubank < db/schema.sql

# Create admin user
mysql -u edubank -p edubank < db/seed.sql
```

### Step 6: Configure web server

**Apache** – Set DocumentRoot to `/path/to/EduBank/webroot`:

```apache
<VirtualHost *:80>
    ServerName edubank.myschool.com
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
    server_name edubank.myschool.com;
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

### Step 7: Set permissions

```bash
chmod -R 775 logs tmp
chown -R www-data:www-data logs tmp
```

---

## First Steps After Installation

### Admin Login

After importing `seed.sql`, a superadmin exists:

| Field | Value |
|-------|-------|
| **Username** | `admin` |
| **Password** | `EduBank1234!` |

**Important:** Change the password after first login!

### Set Up a School

1. Log in as admin
2. Create a new school (name, short name)
3. Create school admin user
4. Create student accounts

---

## Troubleshooting

### "500 Internal Server Error"

- Check logs: `tail -f logs/error.log`
- Check write permissions on `logs/` and `tmp/`

### "Database connection failed"

- Check credentials in `config/app.php`
- Check if MySQL/MariaDB is running

### Page loads but without styles

- DocumentRoot must point to `webroot/`, not the main directory

---

## Help

- [GitHub Issues](https://github.com/cmaj1970/EduBank/issues) – Report problems
- [GitHub Discussions](https://github.com/cmaj1970/EduBank/discussions) – Ask questions
