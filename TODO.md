# EduBank Test-Installation - TODO

## Stand: 20.12.2025

### Erledigt in dieser Session
- [x] Bootstrap 5 Migration aller Templates
- [x] Professioneller Banking-Look (dunkle Navbar, Cards, Bootstrap Icons)
- [x] Navigation für Übungsfirmen vereinfacht (nur "Mein Konto")
- [x] Accounts/view: Unterschiedliche Layouts für Admin vs Übungsfirma
- [x] HTML5 Datepicker mit Default heute für Überweisungen
- [x] Terminologie: "Benutzer/Schüler" → "Übungsfirma/Übungsfirmen"
- [x] Dropdown nur noch "Abmelden" (ohne Rollen-Info)
- [x] Testdaten: 8 Transaktionen für test-1 angelegt

### In Haupt-App nachziehen (NICHT nur Design!)

Diese Änderungen betreffen Logik/Terminologie und müssen in der Original-App übernommen werden:

1. **Terminologie-Änderungen:**
   - "Benutzer" → "Übungsfirmen" in Navigation und Dashboard
   - "Schüler" → "Übungsfirma" in Kommentaren und Lösch-Dialogen
   - Rolle "user" wird als "Übungsfirma" angezeigt (nicht "Schüler")

2. **Controller-Änderungen:**
   - `SchoolsController`: School-Admin Logik (nur eigene Schule sehen/bearbeiten)
   - `UsersController`: School-Admin sieht nur Übungsfirmen der eigenen Schule

3. **UI-Logik:**
   - User-Dropdown nur "Abmelden" (keine Rolleninfo)
   - Navigation für Übungsfirmen: Nur "Mein Konto" (Überweisungen von dort)

### Offen
- [ ] Flash-Messages auf Bootstrap Alerts umstellen
- [ ] Error-Pages (400, 500) auf Bootstrap umstellen
- [ ] Print-Styles für Kontoauszüge optimieren
- [ ] Mobile-Ansicht testen und ggf. anpassen
