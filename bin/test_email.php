#!/usr/bin/env php
<?php
/**
 * EduBank E-Mail Test Script
 * Testet die SMTP-Konfiguration
 *
 * Verwendung: php bin/test_email.php empfaenger@example.com
 */

require dirname(__DIR__) . '/vendor/autoload.php';

use Cake\Core\Configure;
use Cake\Mailer\Email;

// Bootstrap CakePHP
require dirname(__DIR__) . '/config/bootstrap.php';

// Get recipient from command line
$recipient = $argv[1] ?? null;

if (!$recipient) {
    echo "Verwendung: php bin/test_email.php empfaenger@example.com\n";
    exit(1);
}

echo "=== EduBank E-Mail Test ===\n\n";

// Check environment variables
echo "1. Umgebungsvariablen prüfen:\n";
echo "   EMAIL_FROM: " . (env('EMAIL_FROM') ?: '(nicht gesetzt, Standard: noreply@edubank.at)') . "\n";
echo "   SMTP_HOST:  " . (env('SMTP_HOST') ?: '(nicht gesetzt - PHP mail() wird verwendet)') . "\n";
echo "   SMTP_PORT:  " . (env('SMTP_PORT') ?: '(nicht gesetzt, Standard: 587)') . "\n";
echo "   SMTP_USER:  " . (env('SMTP_USER') ? '***gesetzt***' : '(nicht gesetzt)') . "\n";
echo "   SMTP_PASS:  " . (env('SMTP_PASS') ? '***gesetzt***' : '(nicht gesetzt)') . "\n";
echo "   SMTP_TLS:   " . (env('SMTP_TLS') !== null ? (env('SMTP_TLS') ? 'true' : 'false') : '(nicht gesetzt, Standard: true)') . "\n";
echo "\n";

// Check transport configuration
$transport = Configure::read('EmailTransport.default');
echo "2. Aktiver Transport:\n";
echo "   Klasse: " . ($transport['className'] ?? 'unbekannt') . "\n";
if (strpos($transport['className'] ?? '', 'Smtp') !== false) {
    echo "   Host: " . ($transport['host'] ?? '') . "\n";
    echo "   Port: " . ($transport['port'] ?? '') . "\n";
    echo "   TLS:  " . ($transport['tls'] ? 'Ja' : 'Nein') . "\n";
}
echo "\n";

// Try sending test email
echo "3. Test-E-Mail senden an: $recipient\n";

try {
    $email = new Email('default');
    $email
        ->setEmailFormat('text')
        ->setTo($recipient)
        ->setSubject('EduBank SMTP Test - ' . date('Y-m-d H:i:s'));

    $body = "Dies ist eine Test-E-Mail von EduBank.\n\n";
    $body .= "Zeitpunkt: " . date('Y-m-d H:i:s') . "\n";
    $body .= "Server: " . gethostname() . "\n";
    $body .= "Transport: " . ($transport['className'] ?? 'unbekannt') . "\n";

    if (strpos($transport['className'] ?? '', 'Smtp') !== false) {
        $body .= "SMTP Host: " . ($transport['host'] ?? '') . "\n";
    }

    $email->send($body);

    echo "\n   ✓ E-Mail wurde erfolgreich gesendet!\n";
    echo "   Prüfe den Posteingang von: $recipient\n";

} catch (\Exception $e) {
    echo "\n   ✗ FEHLER beim Senden:\n";
    echo "   " . $e->getMessage() . "\n";

    if (strpos($e->getMessage(), 'Connection refused') !== false) {
        echo "\n   Mögliche Ursachen:\n";
        echo "   - SMTP-Host nicht erreichbar\n";
        echo "   - Port blockiert (Firewall)\n";
        echo "   - Falscher Host/Port\n";
    } elseif (strpos($e->getMessage(), 'Authentication') !== false) {
        echo "\n   Mögliche Ursachen:\n";
        echo "   - Falscher SMTP-Benutzername\n";
        echo "   - Falsches SMTP-Passwort\n";
    } elseif (strpos($e->getMessage(), 'certificate') !== false) {
        echo "\n   Mögliche Ursachen:\n";
        echo "   - SSL/TLS-Zertifikatproblem\n";
        echo "   - Versuche SMTP_TLS=false\n";
    }

    exit(1);
}

echo "\n=== Test abgeschlossen ===\n";
