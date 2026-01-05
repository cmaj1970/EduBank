# EduBank Schuladmin-Bereich Verbesserungen

## Current State (Updated: 2025-12-22)

**Shipped:** Schueler-Bereich v1.0 (ELBA-Style Dashboard)
**Status:** Production (lokale Entwicklung)
**Codebase:**
- CakePHP 3.x
- Bootstrap 5 mit Custom Banking-Theme
- ELBA-aehnliches Dashboard fuer Schueler fertig

**Known Issues:**
- Typo "Konton" statt "Konten" in Users/index.ctp:133
- Admin-Views haben klassisches Tabellen-Layout (nicht Dashboard-Stil)
- Keine Moeglichkeit, Konten mit Beispieldaten zu befuellen

## v1.1 Goals

**Vision:** Schuladmin-Bereich auf gleiches Design-Niveau wie Schueler-Bereich bringen.

**Motivation:**
- Konsistente UX zwischen Schueler- und Admin-Bereich
- Lehrer sollen gleich professionelles Interface sehen
- Vorbefuellte Konten ermoeglichen sofortiges Ueben

**Scope (v1.1):**
- Typo-Fix ("Konton" -> "Konten")
- Layout-Redesign fuer Admin-Views (Dashboard-Stil)
- Konten-Vorbefuellung mit Beispieltransaktionen

**Success Criteria:**
- [ ] Keine Typos in UI-Texten
- [ ] Admin-Views nutzen Card-Layout wie Schueler-Dashboard
- [ ] Neue Konten koennen optional mit Beispieldaten erstellt werden
- [ ] Visuell konsistentes Erscheinungsbild

**Out of Scope:**
- Sichtbarkeits-System zwischen Schulen (spaeter)
- Kontexthilfe fuer Schuladmins (spaeter)
- Bulk-Aktionen (spaeter)

---

<details>
<summary>Original Vision (Schueler-Bereich - Completed)</summary>

**One-liner**: ELBA-aehnliches Banking-Dashboard fuer Uebungsfirmen an Schulen.

## Problem

Schueler brauchen eine realistische, aber vereinfachte Banking-Oberflaeche zum Ueben von Geschaeftsprozessen (Ueberweisungen, Kontoauszuege).

## Success Criteria

- [x] Dashboard mit Kontostand, Quick-Actions, Transaktionsliste
- [x] Ueberweisungen mit TAN-Bestaetigung
- [x] Druckbarer Kontoauszug
- [x] Kontexthilfe-System

## Constraints

- CakePHP 3.x (bestehendes Projekt)
- Bootstrap 5
- Deutsche Oberflaeche

</details>
