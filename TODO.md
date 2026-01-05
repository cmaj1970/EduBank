# EduBank v2.4 - Status

## Stand: 03.01.2026

### Abgeschlossene Arbeiten

**Phase 1-4 Schuladmin-Redesign (22.-23.12.2025)**
- [x] Typo-Fixes ("Konton" → "Konten")
- [x] Layout-Redesign (Dashboard-Stil)
- [x] Partnerunternehmen-Tabelle (25 Firmen, 5 Branchen)
- [x] Konto-Reset mit Beispieldaten
- [x] CSV-Export für Partner
- [x] Navigation-Konsistenz
- [x] Terminologie vereinheitlicht ("Kennwort", "Benutzername")
- [x] Help-Text-System für alle Seiten
- [x] Help-Mode UX mit Highlight-Animation

**Bootstrap 5 Migration (20.12.2025)**
- [x] Alle Templates auf Bootstrap 5 umgestellt
- [x] Banking-Look (dunkle Navbar, Cards, Icons)
- [x] Terminologie "Benutzer" → "Übungsfirmen"
- [x] HTML5 Datepicker für Überweisungen
- [x] Flash-Messages auf Bootstrap Alerts
- [x] Error-Pages (400, 500) Bootstrap-Style
- [x] Print-Styles für Kontoauszüge
- [x] Responsive Design (Mobile-optimiert)

**Security (erledigt)**
- [x] CSRF-Protection aktiviert (AppController.php)
- [x] Output-Escaping durchgehend (h() Funktion)

**Weitere Features**
- [x] Self-Service Schulanmeldung
- [x] IBAN-Bug behoben
- [x] Email-Verifikation für neue Schulen
- [x] Multi-Mandanten-System
- [x] TAN-Verwaltung
- [x] Landing Page vereinfacht (v2.4)
- [x] Logo-Update: PTS Banking → EduBank

---

### Offene Punkte (optional)

- [ ] Screenshots für Manuals (via Playwright MCP)

### Bereits live

- [x] Demo-Instanz: https://edubank.solidcode.at

---

### Notizen

Die Anwendung ist funktional vollständig und produktionsreif.
