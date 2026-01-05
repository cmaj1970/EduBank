# EduBank Navigation Flows

Übersicht der Navigations-Pfade (Klickpfade) für die drei Benutzerrollen.

Stand: 2025-12-23

---

## Rollen-Übersicht

| Rolle | Username-Muster | Login-Redirect | Hauptbereich |
|-------|-----------------|----------------|--------------|
| Superadmin | `admin` | /schools | Schulen-Verwaltung |
| Schuladmin | `admin-*` | /users/dashboard | Übungsfirmen der Schule |
| Übungsfirma | `*` (ohne admin) | /accounts/view/{id} | Eigenes Konto |

---

## 1. Superadmin (username = "admin")

### Hauptnavigation

```
Login → /schools

/schools (Schulen-Liste)
  ├── "Ansehen" → /schools/view/{id}
  ├── "Bearbeiten" → /schools/edit/{id}
  ├── "Löschen" → /schools (nach Bestätigung)
  └── "Neue Schule" → /schools/add

/schools/edit/{id}
  ├── "Speichern" → /schools
  └── "Abbrechen" → /schools

/schools/add
  ├── "Speichern" → /schools
  └── "Abbrechen" → /schools
```

### Übungsfirmen-Verwaltung

```
/users (Übungsfirmen-Liste, alle Schulen)
  ├── "Details" → /users/view/{id}
  ├── "Anmelden als" → /accounts/view/{konto_id} (als Übungsfirma)
  ├── Filter: Schule (Dropdown)
  └── Filter: Textsuche

/users/view/{id}
  ├── "Bearbeiten" → /users/edit/{id}
  ├── "Löschen" → /users
  ├── "Anmelden als" → /accounts/view/{konto_id}
  ├── "Konto hinzufügen" → /accounts/add?user_id={id}
  ├── Konto "Bearbeiten" → /accounts/edit/{konto_id}
  └── "← Zurück zur Liste" → /users

/users/edit/{id}
  ├── "Speichern" → /users (⚠️ Superadmin → Liste)
  └── "Abbrechen" → /users
```

### Konten-Verwaltung

```
/accounts (Konten-Liste, alle)
  ├── "Ansehen" → /accounts/view/{id}
  ├── "Bearbeiten" → /accounts/edit/{id}
  └── Filter: Übungsfirma, Textsuche

/accounts/edit/{id}
  ├── "Speichern" → /accounts
  ├── "Abbrechen" → /accounts
  ├── "Leeren" → /accounts/edit/{id}
  └── "Mit Beispieldaten befüllen" → /accounts/edit/{id}

/accounts/add
  ├── "Konto erstellen" → /accounts
  └── "Abbrechen" → /accounts
```

---

## 2. Schuladmin (role=admin, username=admin-*)

### Hauptnavigation

```
Login → /users/dashboard

/users/dashboard
  └── Quick-Links zu häufigen Aktionen
```

### Übungsfirmen-Verwaltung

```
/users (Übungsfirmen der eigenen Schule)
  ├── "Details" → /users/view/{id}
  ├── "Anmelden als" → /accounts/view/{konto_id}
  └── "Neue Übungsfirma" → /users/add

/users/view/{id}
  ├── "Anmelden als" → /accounts/view/{konto_id}
  ├── "Bearbeiten" → /users/edit/{id}
  ├── "Löschen" → /users
  ├── "Konto hinzufügen" → /accounts/add?user_id={id}
  ├── Konto "Bearbeiten" (Stift-Icon) → /accounts/edit/{konto_id}?redirect_user_id={id}
  ├── Konto "Löschen" (Papierkorb-Icon) → /users/view/{id}
  └── "← Zurück zur Liste" → /users

/users/edit/{id}
  ├── "Speichern" → /users/view/{id} (✓ zurück zur Detailseite)
  └── "Abbrechen" → /users/view/{id}

/users/add
  ├── "Übungsfirma erstellen" → /users
  └── "Abbrechen" → /users
```

### Konten-Verwaltung (aus Übungsfirma-Kontext)

```
/accounts/add?user_id={id}
  ├── Übungsfirma: Vorbefüllt und nicht änderbar
  ├── Checkbox: "Mit Beispieltransaktionen befüllen"
  ├── "Konto erstellen" → /users/view/{id} (✓ zurück zur Übungsfirma)
  └── "Abbrechen" → /users/view/{id}

/accounts/edit/{id}?redirect_user_id={user_id}
  ├── "Speichern" → /users/view/{user_id} (✓ zurück zur Übungsfirma)
  ├── "Abbrechen" → /users/view/{user_id}
  ├── "Leeren" → /accounts/edit/{id}?redirect_user_id={user_id}
  └── "Mit Beispieldaten befüllen" → /accounts/edit/{id}?redirect_user_id={user_id}
```

### Transaktionen-Feed

```
/transactions (Live-Feed der Schule, 100 neueste)
  └── Read-only Ansicht
```

---

## 3. Übungsfirma (role=user)

### Hauptnavigation

```
Login → /accounts/view/{eigenes_konto}

/accounts/view/{id} (Kontoübersicht)
  ├── "Überweisung" → /transactions/add
  ├── "Kontoauszug" → /accounts/statement/{id}
  ├── "Auftragshistorie" → /accounts/history/{id}
  └── "Überweisungsverzeichnis" → /accounts/directory

/accounts (Index)
  └── Redirect → /accounts/view/{eigenes_konto}
```

### Überweisungen

```
/transactions/add
  ├── Nach TAN-Prüfung: "Speichern" → /accounts/view/{id}
  └── (Kein Abbrechen-Button, Browser-Zurück)

/accounts/history/{id}
  └── "Stornieren" (bei zukünftigen Aufträgen) → /accounts/history/{id}
```

### Verzeichnisse

```
/accounts/directory (Überweisungsverzeichnis)
  ├── Filter: Schule
  ├── Filter: Textsuche
  └── Zeigt alle Übungsfirmen mit IBAN

/accounts/partners (Partnerunternehmen)
  └── Zeigt System-Partner nach Branche gruppiert
```

---

## Redirect-Logik Zusammenfassung

### Accounts/add

| Parameter | Kontext | Nach Speichern | Abbrechen |
|-----------|---------|----------------|-----------|
| Ohne | Superadmin | /accounts | /accounts |
| `?user_id=X` | Schuladmin | /users/view/X | /users/view/X |

### Accounts/edit

| Parameter | Kontext | Nach Speichern | Abbrechen |
|-----------|---------|----------------|-----------|
| Ohne | Superadmin | /accounts | /accounts |
| `?redirect_user_id=X` | Schuladmin | /users/view/X | /users/view/X |

### Accounts/delete

| Parameter | Kontext | Nach Löschen |
|-----------|---------|--------------|
| Ohne | Superadmin | /accounts |
| `?redirect_user_id=X` | Schuladmin | /users/view/X |

### Users/edit

| Rolle | Nach Speichern | Abbrechen |
|-------|----------------|-----------|
| Superadmin | /users | /users |
| Schuladmin | /users/view/{id} | /users/view/{id} |

---

## Mermaid Diagramme

### Schuladmin Flow: Übungsfirma bearbeiten

```mermaid
flowchart TD
    A[/users] -->|"Details"| B[/users/view/X]
    B -->|"Bearbeiten"| C[/users/edit/X]
    C -->|"Speichern"| B
    C -->|"Abbrechen"| B
    B -->|"← Zurück"| A
```

### Schuladmin Flow: Konto hinzufügen

```mermaid
flowchart TD
    A[/users] -->|"Details"| B[/users/view/X]
    B -->|"Konto hinzufügen"| C[/accounts/add?user_id=X]
    C -->|"Konto erstellen"| B
    C -->|"Abbrechen"| B
```

### Schuladmin Flow: Konto bearbeiten

```mermaid
flowchart TD
    A[/users/view/X] -->|"Konto Bearbeiten"| B[/accounts/edit/Y?redirect_user_id=X]
    B -->|"Speichern"| A
    B -->|"Abbrechen"| A
```

### Übungsfirma Flow: Überweisung

```mermaid
flowchart TD
    A[/accounts/view/X] -->|"Überweisung"| B[/transactions/add]
    B -->|"Speichern + TAN OK"| A
    B -->|"TAN falsch"| B
    A -->|"Auftragshistorie"| C[/accounts/history/X]
    C -->|"Stornieren"| C
```

---

## Impersonation (Als Übungsfirma anmelden)

```mermaid
flowchart TD
    A[Schuladmin: /users] -->|"Anmelden als"| B[/users/impersonate/X]
    B --> C[Übungsfirma-Session: /accounts/view/Y]
    C --> D[Normales Übungsfirma-Verhalten]
    D -->|"Zurück zur Admin-Ansicht"| E[/users/stopImpersonating]
    E --> A
```

---

## Technische Details

### Query-Parameter für Redirects

| Parameter | Verwendet in | Zweck |
|-----------|--------------|-------|
| `user_id` | accounts/add | Übungsfirma vorbefüllen, nach Speichern zu /users/view zurück |
| `redirect_user_id` | accounts/edit, accounts/delete | Nach Aktion zu /users/view zurück |

### Session-Daten

| Key | Inhalt | Zweck |
|-----|--------|-------|
| `Auth.User` | Aktueller User | Authentifizierung |
| `Auth.OriginalAdmin` | Original-Admin bei Impersonation | Zurückkehren zum Admin |
