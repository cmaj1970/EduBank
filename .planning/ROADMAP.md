# Roadmap: EduBank Schuladmin-Bereich

## Overview

Schuladmin-Bereich auf Design-Niveau des Schueler-Bereichs bringen: Typo-Fixes, Dashboard-Layout, Beispieldaten-Vorbefuellung.

## Phases

- [x] **Phase 1: Quick Fixes** - Typo-Korrektur
- [x] **Phase 2: Layout Redesign** - Dashboard-Stil fuer Admin-Views
- [x] **Phase 3: Sample Data** - Partnerunternehmen und Beispieltransaktionen
- [x] **Phase 4: UX Schuladmin** - Navigation, Terminologie, Help-Texte

## Phase Details

### Phase 1: Quick Fixes
**Goal**: Offensichtliche Bugs beheben
**Depends on**: Nothing
**Plans**: 1

Plans:
- [x] 01-01: Typo "Konton" -> "Konten" in Users/index.ctp

### Phase 2: Layout Redesign
**Goal**: Admin-Views im Dashboard-Stil wie Schueler-Ansicht
**Depends on**: Phase 1
**Plans**: 3

Plans:
- [x] 02-01: Users/index.ctp - Uebungsfirmen-Dashboard
- [x] 02-02: Users/view.ctp - Detailansicht Cards
- [x] 02-03: Schuladmin Dashboard + separate Views

### Phase 3: Sample Data
**Goal**: Partnerunternehmen fuer Ueberweisungen, Beispieltransaktionen vorbefuellen
**Depends on**: Phase 2
**Plans**: 4

Plans:
- [x] 03-01: Partnerunternehmen (eigene `partners`-Tabelle mit 25 Firmen in 5 Branchen)
- [x] 03-02: Konto-Reset mit Beispieldaten (85% Ausgaben, 15% Einnahmen)
- [x] 03-03: Umbenennung "Geschaeftspartner" -> "Partnerunternehmen" (inklusiv)
- [x] 03-04: CSV-Export fuer Excel, Info-Box fuer Admins

### Phase 4: UX Schuladmin
**Goal**: Benutzerfreundliche Navigation und Seitenstruktur fuer Schuladmins
**Depends on**: Phase 3
**Plans**: 4

Plans:
- [x] 04-01: Navigation-Konsistenz (Redirects nach Save/Cancel)
- [x] 04-02: Terminologie vereinheitlichen (Kennwort, Benutzername)
- [x] 04-03: Help-Text-System fuer alle Schuladmin-Seiten
- [x] 04-04: Help-Mode UX mit Highlight-Animation

## Progress

| Phase | Plans | Status | Completed |
|-------|-------|--------|-----------|
| 1. Quick Fixes | 1/1 | Complete | 2025-12-22 |
| 2. Layout Redesign | 3/3 | Complete | 2025-12-23 |
| 3. Sample Data | 4/4 | Complete | 2025-12-23 |
| 4. UX Schuladmin | 4/4 | Complete | 2025-12-23 |

## Resolved Issues

- **IBAN-Bug**: Behoben durch Generierung einzelner Ziffern statt mt_rand() mit grossen Zahlen.

- **Partnerunternehmen-Persistenz**: Geloest durch eigene `partners`-Tabelle.
  - Partnerunternehmen werden einmalig initial erstellt
  - IBANs sind stabil und aendern sich nie
  - Warnung im Admin-Bereich: Nach Setup nicht mehr loeschen/neu generieren

## Technische Details Phase 3

**Neue Dateien:**
- `db/migration_partners.sql` - Erstellt partners-Tabelle mit 25 Eintraegen
- `src/Model/Table/PartnersTable.php` - Model mit getGroupedByBranch(), findByIban()
- `src/Model/Entity/Partner.php` - Entity

**Geaenderte Dateien:**
- `AccountsController.php` - partners(), createPartners(), deletePartners(), exportPartnersCsv()
- `TransactionsController.php` - searchRecipients() und checkiban() nutzen partners-Tabelle
- `partners.ctp` - Admin-Info-Box, CSV-Download, Warnung
- `default.ctp` - Menu: "Partnerunternehmen" statt "Geschaeftspartner"

**5 Branchen:**
1. Buero & Ausstattung (5 Firmen)
2. Dienstleistungen (5 Firmen)
3. Marketing & Kommunikation (5 Firmen)
4. Versicherungen & Finanzen (5 Firmen)
5. Logistik & Infrastruktur (5 Firmen)
