# EduBank - Berechtigungen & Workflow

Dieses Dokument beschreibt das Berechtigungssystem und den Workflow für alle Benutzerrollen im EduBank-System.

---

## Übersicht der Benutzerrollen

| Rolle | Username-Format | Rolle (DB) | Admin-Flag | Beschreibung |
|-------|-----------------|------------|------------|--------------|
| **Superadmin** | `admin` | `admin` | `1` | Vollzugriff auf das gesamte System |
| **Schuladmin** | `admin-{kurzname}` | `admin` | `0` | Verwaltet nur die eigene Schule |
| **Übungsfirma** | `{kurzname}-{nummer}` | `user` | `0` | Einfacher Benutzer mit eingeschränktem Zugriff |

---

## Datenmodell-Hierarchie

```
Schule (School)
    └── Benutzer (Users)
            └── Konten (Accounts)
                    └── Transaktionen (Transactions)
                            └── TANs
```

---

## 1. Superadmin

**Beispiel-Login:** `admin` / `EduBank1234!`

### Sichtbare Menüpunkte

| Menüpunkt | Aktion | Verfügbar |
|-----------|--------|-----------|
| Schulen anzeigen | `/schools` | ✅ |
| Schule hinzufügen | `/schools/add` | ✅ |
| Schule bearbeiten | `/schools/edit/{id}` | ✅ |
| Schule genehmigen/ablehnen | `/schools/approve`, `/schools/reject` | 🔧 (Backend vorhanden, UI fehlt) |
| Benutzer anzeigen | `/users` | ✅ |
| Benutzer hinzufügen | `/users/add` | ✅ |
| Benutzer bearbeiten | `/users/edit/{id}` | ✅ |
| Konten anzeigen | `/accounts` | ✅ |
| Konto hinzufügen | `/accounts/add` | ✅ |
| Konto bearbeiten | `/accounts/edit/{id}` | ✅ |
| Transaktionen anzeigen | `/transactions` | ✅ |
| Transaktion hinzufügen | `/transactions/add` | ✅ |
| Transaktion bearbeiten | `/transactions/edit/{id}` | ✅ |
| TAN-Verwaltung | `/tans` | ✅ |

### Sichtbare Datensätze

| Entität | Sichtbarkeit | Filter |
|---------|--------------|--------|
| **Schulen** | Alle Schulen (pending, approved, rejected) | Keine Einschränkung |
| **Benutzer** | Alle Benutzer aller Schulen | Keine Einschränkung |
| **Konten** | Alle Konten aller Übungsfirmen | Keine Einschränkung |
| **Transaktionen** | Alle Transaktionen aller Konten | Keine Einschränkung |
| **TANs** | Alle TANs aller Konten | Keine Einschränkung |

### Zusätzliche Berechtigungen

- Kann Schulen genehmigen (`approve`) oder ablehnen (`reject`)
- Kann Schuladmin-Benutzer erstellen (Rolle: `admin`)
- Kann alle Datensätze bearbeiten und löschen
- Kann Konten zurücksetzen (`reset`)

---

## 2. Schuladmin

**Beispiel-Login:** `admin-musterschule` / (vom System generiert)

### Sichtbare Menüpunkte

| Menüpunkt | Aktion | Verfügbar |
|-----------|--------|-----------|
| Schulen anzeigen | `/schools` | ✅ (nur eigene Schule) |
| Schule hinzufügen | `/schools/add` | ❌ |
| Schule bearbeiten | `/schools/edit/{id}` | ❌ |
| Schule genehmigen/ablehnen | `/schools/approve`, `/schools/reject` | ❌ |
| Benutzer anzeigen | `/users` | ✅ (nur eigene Schule) |
| Benutzer hinzufügen | `/users/add` | ✅ (nur `user`-Rolle) |
| Benutzer bearbeiten | `/users/edit/{id}` | ✅ (nur eigene Schule) |
| Konten anzeigen | `/accounts` | ✅ (nur eigene Schule) |
| Konto hinzufügen | `/accounts/add` | ✅ |
| Konto bearbeiten | `/accounts/edit/{id}` | ✅ (nur eigene Schule) |
| Transaktionen anzeigen | `/transactions` | ✅ (nur eigene Schule) |
| Transaktion hinzufügen | `/transactions/add` | ✅ |
| TAN-Verwaltung | `/tans` | ✅ |

### Sichtbare Datensätze

| Entität | Sichtbarkeit | Filter |
|---------|--------------|--------|
| **Schulen** | Nur die eigene Schule | `id = eigene_schule_id` |
| **Benutzer** | Nur Übungsfirmen der eigenen Schule | `school_id = eigene_schule_id AND role = 'user'` |
| **Konten** | Nur Konten von Übungsfirmen der eigenen Schule | via `Users.school_id = eigene_schule_id` |
| **Transaktionen** | Nur Transaktionen von Konten der eigenen Schule | via `Accounts.Users.school_id` |
| **TANs** | Nur TANs von Konten der eigenen Schule | via `Accounts.Users.school_id` |

### Einschränkungen

- Kann keine anderen Schulen sehen oder bearbeiten
- Kann keine Schuladmin-Benutzer erstellen (nur `user`-Rolle)
- Kann keine Schulen genehmigen oder ablehnen
- Kann keine Benutzer anderer Schulen sehen

---

## 3. Übungsfirma (Einfacher Benutzer)

**Beispiel-Login:** `musterschule-1` / (vom System generiert)

### Sichtbare Menüpunkte

| Menüpunkt | Aktion | Verfügbar |
|-----------|--------|-----------|
| Schulen | `/schools` | ❌ |
| Benutzer | `/users` | ❌ |
| Konten anzeigen | `/accounts` | ✅ (nur eigene) |
| Konto hinzufügen | `/accounts/add` | ❌ |
| Konto bearbeiten | `/accounts/edit/{id}` | ❌ |
| Kontodetails | `/accounts/view/{id}` | ✅ (nur eigene) |
| **Umsätze (Kontoauszug)** | `/accounts/history/{id}` | ✅ **(eigene Transaktionen hier sichtbar)** |
| Transaktionsliste (Admin) | `/transactions` | ❌ (Admin-Übersicht) |
| Transaktion ansehen | `/transactions/view/{id}` | ✅ (nur eigene) |
| Transaktion hinzufügen | `/transactions/add` | ✅ |
| Transaktion stornieren | `/transactions/storno/{id}` | ✅ (nur eigene) |
| IBAN prüfen | `/transactions/checkiban` | ✅ |

### Sichtbare Datensätze

| Entität | Sichtbarkeit | Filter |
|---------|--------------|--------|
| **Schulen** | Keine | - |
| **Benutzer** | Keine (nur eigenes Profil) | `id = eigene_user_id` |
| **Konten** | Nur eigene Konten | `user_id = eigene_user_id` |
| **Transaktionen** | ✅ Nur eigene Transaktionen (via Kontohistorie) | `account.user_id = eigene_user_id` |
| **TANs** | Keine direkte Sicht | Nur zur Validierung bei Transaktionen |

### Verfügbare Aktionen

- Eigene Konten anzeigen (`view`)
- **Kontohistorie/Umsätze einsehen (`history`) → hier werden alle eigenen Transaktionen angezeigt**
- Einzelne Transaktion ansehen (`view`)
- Neue Überweisung erstellen (`add`)
- Eigene Transaktion stornieren (`storno`)
- IBAN validieren (`checkiban`)

### Einschränkungen

- Kein Zugriff auf Schulverwaltung
- Kein Zugriff auf Benutzerverwaltung
- Keine Bearbeitung von Kontodaten
- Kann keine Transaktionen bearbeiten (nur stornieren)
- Kein Zugriff auf die Admin-Transaktionsliste (`/transactions`), aber **eigene Transaktionen sind über Kontohistorie sichtbar**

---

## Workflow-Diagramm

```
┌─────────────────────────────────────────────────────────────────────────┐
│                           REGISTRIERUNG                                  │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
                    ┌───────────────────────────────┐
                    │  Schule registriert sich      │
                    │  (Status: "pending")          │
                    └───────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                          SUPERADMIN                                      │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │  Prüft Schulregistrierung                                        │   │
│  │  • Genehmigen → Status: "approved"                               │   │
│  │  • Ablehnen → Status: "rejected"                                 │   │
│  └─────────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                          (bei Genehmigung)
                                    ▼
                    ┌───────────────────────────────┐
                    │  Schuladmin wird automatisch  │
                    │  erstellt (admin-{kurzname})  │
                    └───────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                          SCHULADMIN                                      │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │  Verwaltet Übungsfirmen der Schule:                              │   │
│  │  • Benutzer anlegen ({kurzname}-1, {kurzname}-2, ...)           │   │
│  │  • Konten für Übungsfirmen erstellen                             │   │
│  │  • Transaktionen überwachen                                      │   │
│  └─────────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                         ÜBUNGSFIRMA                                      │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │  Nutzt eigene Bankkonten:                                        │   │
│  │  • Kontostand einsehen                                           │   │
│  │  • Überweisungen durchführen (mit TAN)                           │   │
│  │  • Transaktionen stornieren                                      │   │
│  │  • Kontohistorie einsehen                                        │   │
│  └─────────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## Datenzugriffs-Matrix

### Legende
- ✅ Vollzugriff (Lesen, Schreiben, Löschen)
- 📖 Nur Lesen
- 🔒 Eingeschränkt (nur eigene Datensätze)
- ❌ Kein Zugriff

| Datensatz | Superadmin | Schuladmin | Übungsfirma |
|-----------|------------|------------|-------------|
| **Alle Schulen** | ✅ | ❌ | ❌ |
| **Eigene Schule** | ✅ | 📖 | ❌ |
| **Alle Benutzer** | ✅ | ❌ | ❌ |
| **Benutzer der Schule** | ✅ | ✅ (nur `user`) | ❌ |
| **Eigenes Profil** | ✅ | ✅ | 📖 |
| **Alle Konten** | ✅ | ❌ | ❌ |
| **Konten der Schule** | ✅ | ✅ | ❌ |
| **Eigene Konten** | ✅ | ✅ | 🔒 |
| **Alle Transaktionen** | ✅ | ❌ | ❌ |
| **Transaktionen der Schule** | ✅ | ✅ | ❌ |
| **Eigene Transaktionen** | ✅ | ✅ | 🔒 (nur view/add/storno) |

---

## Technische Implementierung

### Backend-Autorisierung

Die Berechtigungsprüfung erfolgt in zwei Ebenen:

#### 1. Controller-Ebene (Aktionszugriff)

```php
// AppController.php - Basis-Autorisierung
public function isAuthorized($user) {
    if ($user['role'] === 'admin') {
        return true;  // Admins haben Grundzugriff
    }
    return false;  // Standardmäßig verweigern
}
```

#### 2. Daten-Ebene (Datensatz-Filter)

**Schuladmin-Filter (UsersController.php):**
```php
$this->paginate['conditions'] = [
    'school_id' => $this->school['id'],
    'role' => 'user'
];
```

**Übungsfirma-Filter (AccountsController.php):**
```php
if ($user['role'] !== 'admin' && $account->user_id != $user['id']) {
    return $this->redirect(['action' => 'index']);
}
```

### Frontend-Berechtigungen

Die Menüs und UI-Elemente werden über die `$authuser`-Variable gefiltert:

```php
<?php if($authuser['role'] == 'admin'): ?>
    <!-- Admin-spezifische Menüpunkte -->
<?php else: ?>
    <!-- Benutzer-spezifische Menüpunkte -->
<?php endif; ?>
```

---

## Relevante Code-Dateien

| Komponente | Dateipfad |
|------------|-----------|
| Basis-Autorisierung | `src/Controller/AppController.php` |
| Schulen-Controller | `src/Controller/SchoolsController.php` |
| Benutzer-Controller | `src/Controller/UsersController.php` |
| Konten-Controller | `src/Controller/AccountsController.php` |
| Transaktionen-Controller | `src/Controller/TransactionsController.php` |
| Navigation | `src/Template/Element/nav.ctp` |
| Datenbank-Schema | `db/schema.sql` |

---

## Sicherheitsregeln

1. **Schul-Isolation:** Jede Schule kann nur auf ihre eigenen Daten zugreifen
2. **Benutzer-Isolation:** Übungsfirmen können nur eigene Konten/Transaktionen sehen
3. **TAN-Validierung:** Transaktionen erfordern eine gültige TAN (divisible by 7)
4. **Session-basiert:** Authentifizierung über CakePHP Auth-Komponente
5. **Rollen-Check:** Jede Controller-Aktion prüft die Benutzerrolle

---

## TODO / Offene Punkte

- [ ] **Schulfreigabe-UI:** Approve/Reject-Buttons im Schul-Template für Superadmin hinzufügen (Backend-Logik bereits vorhanden in `SchoolsController.php:228-267`)
