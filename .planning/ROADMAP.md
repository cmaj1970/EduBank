# Roadmap: EduBank Schuladmin-Bereich

## Overview

Schuladmin-Bereich auf Design-Niveau des Schueler-Bereichs bringen: Typo-Fixes, Dashboard-Layout, Beispieldaten-Vorbefuellung.

## Phases

- [x] **Phase 1: Quick Fixes** - Typo-Korrektur
- [x] **Phase 2: Layout Redesign** - Dashboard-Stil fuer Admin-Views
- [ ] **Phase 3: Sample Data** - Beispieltransaktionen vorbefuellen

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
- [ ] 03-01: System-Uebungsfirmen + Transaktions-Pool
- [ ] 03-02: Checkbox + Prefill-Logik in UsersController

## Progress

| Phase | Plans | Status | Completed |
|-------|-------|--------|-----------|
| 1. Quick Fixes | 1/1 | Complete | 2025-12-22 |
| 2. Layout Redesign | 3/3 | Complete | 2025-12-23 |
| 3. Sample Data | 0/2 | Not started | - |
