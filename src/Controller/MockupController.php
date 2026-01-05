<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\FrozenTime;

/**
 * Mockup Controller - nur fuer Design-Tests
 */
class MockupController extends AppController
{
    /**
     * Nur Admins haben Zugriff
     */
    public function isAuthorized($user)
    {
        # Nur Admins (inkl. Schuladmins)
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }
        return false;
    }

    /**
     * Schuladmin Master-Detail Layout Mockup
     * Generiert Demo-Daten fuer Layout-Tests
     */
    public function schuladmin()
    {
        # Demo-Schule
        $school = (object)[
            'id' => 1,
            'name' => 'PTS Musterstadt',
            'kurzname' => 'ptsmusterstadt'
        ];

        # 10 Demo-Uebungsfirmen mit Konten
        $firmenNamen = [
            'Handel 9', 'Nästube Susi', 'TechStore Plus', 'Café Sonnenschein',
            'AutoFit Service', 'BioMarkt Grün', 'SportCenter Max', 'Blumen Paradies',
            'Pizza Express', 'Mode & Style'
        ];

        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $userId = $i + 1;
            $firmaName = $firmenNamen[$i];
            $username = strtolower(str_replace([' ', 'ä', 'ö', 'ü'], ['', 'ae', 'oe', 'ue'], $firmaName));

            # Zufaelliger letzter Login (manche nie, manche vor Minuten, manche vor Tagen)
            $lastLogin = null;
            $loginChance = rand(0, 10);
            if ($loginChance > 2) {
                $minutesAgo = rand(1, 10000);
                $lastLogin = new FrozenTime("-{$minutesAgo} minutes");
            }

            # 1-2 Konten pro Uebungsfirma
            $accounts = [];
            $numAccounts = rand(1, 2);
            for ($a = 0; $a < $numAccounts; $a++) {
                $accountId = ($userId * 10) + $a;
                $accounts[] = (object)[
                    'id' => $accountId,
                    'user_id' => $userId,
                    'name' => $a == 0 ? 'Girokonto' : 'Sparkonto',
                    'iban' => 'AT' . str_pad(rand(10, 99), 2, '0') . ' ' .
                              str_pad(rand(1000, 9999), 4, '0') . ' ' .
                              str_pad(rand(1000, 9999), 4, '0') . ' ' .
                              str_pad(rand(1000, 9999), 4, '0') . ' ' .
                              str_pad(rand(1000, 9999), 4, '0'),
                    'bic' => 'EDUBATWWXXX',
                    'balance' => rand(-500, 5000) + (rand(0, 99) / 100),
                    'maxlimit' => rand(1, 10) * 100
                ];
            }

            $users[] = (object)[
                'id' => $userId,
                'name' => $firmaName,
                'username' => 'ptsmusterstadt-' . ($i + 1),
                'active' => rand(0, 10) > 1, # 90% aktiv
                'last_login' => $lastLogin,
                'school_id' => 1,
                'school' => $school,
                'accounts' => $accounts
            ];
        }

        # 100 Demo-Transaktionen
        $empfaenger = [
            'Lieferant GmbH', 'Großhandel AG', 'Stromversorger', 'Versicherung Plus',
            'Mietkosten KG', 'Werbung & Co', 'IT-Service', 'Reinigung Sauber',
            'Bürobedarf Shop', 'Telefon AG', 'Internet Provider', 'Steuerberater',
            'Bank Gebühren', 'Post & Versand', 'Werkstatt Meier'
        ];

        $verwendung = [
            'Warenlieferung', 'Monatsrechnung', 'Dienstleistung', 'Abonnement',
            'Reparatur', 'Bestellung #' . rand(1000, 9999), 'Gutschrift', 'Anzahlung',
            'Schlussrechnung', 'Teilzahlung'
        ];

        $transactions = [];
        for ($t = 0; $t < 100; $t++) {
            # Zufaellige Uebungsfirma und deren Konto
            $randomUser = $users[rand(0, 9)];
            $randomAccount = $randomUser->accounts[rand(0, count($randomUser->accounts) - 1)];

            # Zeitpunkt: letzte 7 Tage
            $minutesAgo = rand(1, 10080); # 7 Tage in Minuten
            $created = new FrozenTime("-{$minutesAgo} minutes");

            $transactions[] = (object)[
                'id' => $t + 1,
                'account_id' => $randomAccount->id,
                'empfaenger_name' => $empfaenger[rand(0, count($empfaenger) - 1)],
                'empfaenger_iban' => 'AT' . str_pad(rand(10, 99), 2, '0') . ' XXXX XXXX XXXX',
                'betrag' => rand(10, 500) + (rand(0, 99) / 100),
                'zahlungszweck' => $verwendung[rand(0, count($verwendung) - 1)],
                'created' => $created,
                'account' => (object)[
                    'id' => $randomAccount->id,
                    'iban' => $randomAccount->iban,
                    'user' => (object)[
                        'id' => $randomUser->id,
                        'name' => $randomUser->name,
                        'school' => $school
                    ]
                ]
            ];
        }

        # Nach Datum sortieren (neueste zuerst)
        usort($transactions, function($a, $b) {
            return $b->created->getTimestamp() - $a->created->getTimestamp();
        });

        $this->set(compact('school', 'users', 'transactions'));
    }
}
