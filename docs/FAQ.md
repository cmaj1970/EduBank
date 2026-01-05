# EduBank – Häufige Fragen (FAQ)

## Allgemein

### Was ist EduBank?

EduBank ist eine kostenlose Open-Source-Software, mit der Schulen Online-Banking simulieren können. Schüler:innen lernen, wie Überweisungen funktionieren – ohne echtes Geld.

### Für welche Schulen ist EduBank geeignet?

Für alle Bildungseinrichtungen, die Finanzbildung im Unterricht anbieten.

### Was kostet EduBank?

Nichts. EduBank ist komplett kostenlos und Open Source (MIT-Lizenz).

### Kann ich EduBank für meine Schule anpassen?

Ja! Der Quellcode ist auf GitHub verfügbar. Du darfst ihn verändern und anpassen.

---

## Installation

### Brauche ich einen eigenen Server?

Es gibt zwei Möglichkeiten:
1. **Self-Hosted:** Du installierst EduBank auf deinem eigenen Server
2. **Demo-Instanz:** Du nutzt eine gehostete Version (falls verfügbar)

### Welche technischen Voraussetzungen gibt es?

- PHP 7.2 oder höher
- MySQL/MariaDB
- Webserver (Apache/Nginx)

Mit Docker ist die Installation am einfachsten.

### Die Installation funktioniert nicht. Was tun?

1. Prüfe die Logs: `logs/error.log`
2. Stelle sicher, dass PHP alle Erweiterungen hat (intl, pdo_mysql)
3. Prüfe die Datenbank-Verbindung
4. [Erstelle ein Issue auf GitHub](https://github.com/cmaj1970/EduBank/issues)

---

## Benutzung

### Wie lege ich Konten für Schüler:innen an?

1. Als Schuladmin einloggen
2. "Konten" → "Neues Konto"
3. Daten eingeben
4. TANs generieren und ausdrucken

### Was sind TANs?

TANs (Transaktionsnummern) sind Einmal-Codes zur Bestätigung von Überweisungen. Jede TAN kann nur einmal verwendet werden – wie beim echten Online-Banking.

### Jemand hat das Passwort vergessen

Als Schuladmin:
1. Öffne das Konto
2. Klicke auf "Bearbeiten"
3. Setze ein neues Passwort

### Jemand hat falsch überwiesen

Als Schuladmin:
1. Öffne "Transaktionen"
2. Finde die Überweisung
3. Klicke auf "Stornieren"

### Wie viel Startguthaben gibt es?

Standardmäßig 10.000,00 (virtuell). Das Überweisungslimit pro Transaktion ist 2.000,00.

---

## Sicherheit

### Werden echte Bankdaten verwendet?

Nein! Alle Konten und IBANs sind fiktiv. Es fließt kein echtes Geld.

### Sind die Daten sicher?

EduBank speichert nur minimale Daten (Name, Benutzername, Passwort-Hash). Passwörter werden verschlüsselt gespeichert.

### Wer hat Zugriff auf die Daten?

Nur Schuladmins können die Daten ihrer Schule sehen. Andere Schulen haben keinen Zugriff.

---

## Probleme & Support

### Wo melde ich Fehler?

Auf GitHub: [github.com/cmaj1970/EduBank/issues](https://github.com/cmaj1970/EduBank/issues)

### Wo kann ich Fragen stellen?

In den GitHub Discussions: [github.com/cmaj1970/EduBank/discussions](https://github.com/cmaj1970/EduBank/discussions)

### Wie kann ich bei der Entwicklung helfen?

Lies die [CONTRIBUTING.md](../CONTRIBUTING.md) für Details. Wir freuen uns über:
- Bug-Reports
- Feature-Vorschläge
- Code-Beiträge
- Dokumentations-Verbesserungen
