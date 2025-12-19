# EduBank – Benutzerhandbuch für Lehrer:innen

## Übersicht

EduBank ist eine Banking-Simulation für den Unterricht. Schüler:innen können virtuelle Überweisungen durchführen und lernen dabei den Umgang mit Online-Banking.

---

## Benutzerrollen

| Rolle | Beschreibung | Login-Format |
|-------|--------------|--------------|
| **Superadmin** | Verwaltet alle Schulen | `admin` |
| **Schuladmin** | Verwaltet eine Schule | `admin-{schulkürzel}` |
| **Schüler:in** | Führt Überweisungen durch | `pts-{schule}{nummer}` |

---

## Für Schuladmins

### Anmeldung

1. Öffne EduBank im Browser
2. Gib deinen Benutzernamen ein (z.B. `admin-htl`)
3. Gib dein Passwort ein
4. Klicke auf "Anmelden"

### Konten für Schüler:innen verwalten

#### Neues Konto anlegen

1. Navigiere zu "Konten"
2. Klicke auf "Neues Konto"
3. Fülle das Formular aus:
   - **Vorname/Nachname:** Name der/des Schüler:in
   - **Benutzername:** Wird automatisch generiert
   - **Passwort:** Initiales Passwort
4. Klicke auf "Speichern"

Das Konto wird mit einem Startguthaben von **10.000,00** erstellt.

#### TANs generieren

Jede:r Schüler:in braucht TANs (Transaktionsnummern) für Überweisungen:

1. Öffne das Konto
2. Klicke auf "TANs generieren"
3. Drucke die TAN-Liste aus und verteile sie

**Wichtig:** Jede TAN kann nur einmal verwendet werden!

#### Kontoübersicht

Die Kontoübersicht zeigt:
- Aktueller Kontostand
- Alle Transaktionen (Eingang/Ausgang)
- Verwendete TANs

### Überweisungen stornieren

Falls jemand einen Fehler gemacht hat:

1. Öffne "Transaktionen"
2. Finde die fehlerhafte Überweisung
3. Klicke auf "Stornieren"

Der Betrag wird zurückgebucht.

---

## Für Schüler:innen

### Anmeldung

1. Öffne EduBank im Browser
2. Gib deinen Benutzernamen ein (von der Lehrperson erhalten)
3. Gib dein Passwort ein
4. Klicke auf "Anmelden"

### Kontostand anzeigen

Nach der Anmeldung siehst du:
- Deinen aktuellen Kontostand
- Deine letzten Transaktionen

### Überweisung durchführen

1. Klicke auf "Neue Überweisung"
2. Gib die Empfänger-IBAN ein (20 Stellen)
3. Gib den Betrag ein
4. Gib einen Verwendungszweck ein
5. Klicke auf "Weiter"
6. Gib eine TAN aus deiner TAN-Liste ein
7. Klicke auf "Überweisung bestätigen"

**Überweisungslimit:** Maximal 2.000,00 pro Überweisung

### TAN-Liste

- Du erhältst eine TAN-Liste von deiner Lehrperson
- Jede TAN kann nur einmal verwendet werden
- Streiche verwendete TANs durch
- Wenn die Liste leer ist, bitte um neue TANs

---

## Tipps für den Unterricht

### Einstieg (erste Stunde)

1. Konten für alle Schüler:innen anlegen
2. TAN-Listen ausdrucken und verteilen
3. Schüler:innen loggen sich ein und prüfen Kontostand
4. Erste Test-Überweisung zwischen zwei Personen

### Übungen

- **Gegenseitige Überweisungen:** Schüler:innen überweisen sich Beträge
- **Rechnungen bezahlen:** Lehrperson gibt Rechnungs-IBANs vor
- **Budgetplanung:** Schüler:innen müssen mit Startguthaben haushalten

### Häufige Fragen im Unterricht

**"Meine TAN funktioniert nicht"**
→ TAN wurde bereits verwendet, nächste TAN nehmen

**"Ich habe an die falsche IBAN überwiesen"**
→ Lehrperson kann die Überweisung stornieren

**"Mein Guthaben ist aufgebraucht"**
→ Lehrperson kann Konto zurücksetzen oder neues Guthaben buchen

---

## Support

Bei technischen Problemen:
- [GitHub Issues](https://github.com/cmaj1970/EduBank/issues)
- [GitHub Discussions](https://github.com/cmaj1970/EduBank/discussions)
