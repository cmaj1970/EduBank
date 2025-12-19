# EduBank

**Open-Source Banking-Simulation für Schulen**

EduBank ist eine Lernsoftware, die Schüler:innen an Bildungseinrichtungen den Umgang mit Online-Banking näherbringt. Die Software simuliert reale Banking-Prozesse in einer sicheren Lernumgebung.

## Features

- **Virtuelle Konten** – Jede:r Schüler:in erhält ein eigenes Konto mit Startguthaben
- **Überweisungen** – Geld zwischen Konten transferieren
- **TAN-Authentifizierung** – Transaktionen mit TAN-Codes bestätigen
- **Multi-Mandanten** – Mehrere Schulen auf einer Instanz
- **Responsive Design** – Funktioniert auf Desktop und Mobilgeräten

## Für wen ist EduBank?

- **Lehrer:innen** – Unterrichtsmaterial für Finanzbildung
- **Schüler:innen** – Praxisnahe Übung ohne echtes Geld
- **Schulen** – Kostenlose, einfach zu installierende Lösung

## Installation

### Voraussetzungen

- PHP 7.2 oder höher
- MySQL 5.7 oder höher
- Composer

### Mit Docker (empfohlen)

```bash
git clone https://github.com/cmaj1970/EduBank.git
cd EduBank
docker-compose up -d
```

Dann: http://localhost:8080

### Manuell

```bash
git clone https://github.com/cmaj1970/EduBank.git
cd EduBank
composer install
```

Datenbank konfigurieren in `config/app.php`, dann:

```bash
bin/cake migrations migrate
bin/cake server
```

## Technologie

- **Backend:** CakePHP 3.6
- **Frontend:** Eigenes CSS (responsive)
- **Datenbank:** MySQL

## Lizenz

MIT License – siehe [LICENSE](LICENSE)

## Unterstützung

EduBank ist ein ehrenamtliches Projekt. Entwicklung und Betrieb kosten Zeit und Geld.

Wenn EduBank deiner Schule hilft, freue ich mich über eine freiwillige Unterstützung:

**[→ Jetzt unterstützen via PayPal](https://www.paypal.com/paypalme/carlmajneri)**

Danke!

## Mitmachen

Beiträge sind willkommen! Siehe [CONTRIBUTING.md](CONTRIBUTING.md) für Details.

## Support

- **Issues:** [GitHub Issues](https://github.com/cmaj1970/EduBank/issues)
- **Diskussion:** [GitHub Discussions](https://github.com/cmaj1970/EduBank/discussions)
