# Mitmachen bei EduBank

Danke für dein Interesse an EduBank! Jeder Beitrag hilft, diese Lernsoftware besser zu machen.

## Wie kann ich beitragen?

### Fehler melden

1. Prüfe, ob der Fehler bereits als [Issue](https://github.com/cmaj1970/EduBank/issues) gemeldet wurde
2. Erstelle ein neues Issue mit dem Bug-Report-Template
3. Beschreibe genau, was passiert und was du erwartet hast

### Features vorschlagen

1. Öffne ein Issue mit dem Feature-Request-Template
2. Beschreibe das Feature und warum es nützlich wäre
3. Diskutiere mit der Community

### Code beitragen

1. Forke das Repository
2. Erstelle einen Feature-Branch (`git checkout -b feature/mein-feature`)
3. Committe deine Änderungen (`git commit -m 'Füge Feature hinzu'`)
4. Pushe den Branch (`git push origin feature/mein-feature`)
5. Öffne einen Pull Request

## Entwicklungsumgebung

### Voraussetzungen

- PHP 7.2+
- MySQL 5.7+
- Composer
- Optional: Docker/ddev

### Setup

```bash
git clone https://github.com/cmaj1970/EduBank.git
cd EduBank
composer install
cp config/app.default.php config/app.php
# Datenbank-Credentials in config/app.php eintragen
bin/cake migrations migrate
bin/cake server
```

## Code-Style

- PHP: PSR-12
- Einrückung: Tabs
- Kommentare: Deutsch
- Commit-Messages: Deutsch, kurz und prägnant

## Fragen?

- [GitHub Discussions](https://github.com/cmaj1970/EduBank/discussions) für allgemeine Fragen
- [GitHub Issues](https://github.com/cmaj1970/EduBank/issues) für Bugs und Features
