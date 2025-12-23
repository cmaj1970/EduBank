# Roadmap: EduBank Schuladmin-Bereich

## Overview

Schuladmin-Bereich auf Design-Niveau des Schueler-Bereichs bringen: Typo-Fixes, Dashboard-Layout, Beispieldaten-Vorbefuellung.

## Phases

- [x] **Phase 1: Quick Fixes** - Typo-Korrektur
- [x] **Phase 2: Layout Redesign** - Dashboard-Stil fuer Admin-Views
- [x] **Phase 3: Sample Data** - Beispieltransaktionen vorbefuellen
- [ ] **Phase 4: UX Schuladmin** - Klickpfade und Seitenstruktur

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
**Goal**: Neue Konten mit Beispieltransaktionen vorbefuellen
**Depends on**: Phase 2
**Plans**: 2

Plans:
- [x] 03-01: System-Konten (Geschaeftspartner) + Prefill-Transaktionen
- [x] 03-02: Konto-Reset mit Beispieldaten

### Phase 4: UX Schuladmin
**Goal**: Benutzerfreundliche Navigation und Seitenstruktur fuer Schuladmins
**Depends on**: Phase 3
**Plans**: 3

Plans:
- [ ] 04-01: Klickpfade fuer Schul-Admins ausarbeiten
- [ ] 04-02: Sinnvolle Struktur der Schul-Admin-Seite ueberlegen
- [ ] 04-03: Diagramm zur Navigation erstellen

## Progress

| Phase | Plans | Status | Completed |
|-------|-------|--------|-----------|
| 1. Quick Fixes | 1/1 | Complete | 2025-12-22 |
| 2. Layout Redesign | 3/3 | Complete | 2025-12-23 |
| 3. Sample Data | 2/2 | Complete | 2025-12-23 |
| 4. UX Schuladmin | 0/3 | Not started | - |

## Known Issues

- **IBAN-Bug auf Produktiv**: Alle Geschaeftspartner haben dieselbe IBAN (SY000000000000000000) statt zufaelliger Nummern. Ursache: mt_rand() mit zu grossen Zahlen.

- **Geschaeftspartner-Persistenz**: Wenn System-Konten geloescht/neu erstellt werden, sind bestehende Ueberweisungen auf diese Konten nicht mehr sichtbar (IBAN existiert nicht mehr).

  **Loesungsoptionen:**
  - a) Geschaeftspartner einmalig anlegen, Loeschen nicht mehr erlauben
  - b) Ueberweisungen auch anzeigen wenn Zielkonto nicht mehr existiert (nur IBAN anzeigen statt Kontoname)

  **Empfehlung:** Option a) ist sauberer - System-Konten sind stabil, IBANs aendern sich nie.

  **Naechster Schritt:** 50 Geschaeftspartner statt 10 anlegen fuer mehr Auswahl.
