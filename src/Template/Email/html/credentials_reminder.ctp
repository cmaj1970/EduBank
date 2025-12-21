<?php
/**
 * EduBank - Credentials Reminder Email
 * HTML-Template im Design der Webseite (Dunkelblau)
 */
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBank - Ihre Zugangsdaten</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f8f9fa;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8f9fa;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1a365d 0%, #2d4a7c 100%); padding: 30px 40px; border-radius: 8px 8px 0 0; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: bold;">
                                EduBank
                            </h1>
                            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 14px;">
                                Banking-Simulation für Schulen
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="color: #1a365d; margin: 0 0 20px 0; font-size: 22px;">
                                Ihre Zugangsdaten
                            </h2>

                            <p style="color: #333; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                                Sie haben die Zugangsdaten für <strong><?= h($schoolName) ?></strong> angefordert.
                                Nachfolgend finden Sie Ihre Anmeldedaten:
                            </p>

                            <!-- Credentials Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f0f4f8; border-radius: 8px; border-left: 4px solid #1a365d; margin: 25px 0;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <span style="color: #666; font-size: 14px;">Benutzername:</span>
                                                </td>
                                                <td style="padding: 8px 0; text-align: right;">
                                                    <strong style="color: #1a365d; font-size: 16px; font-family: monospace; background-color: #fff; padding: 4px 10px; border-radius: 4px;"><?= h($username) ?></strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-top: 1px solid #dee2e6;">
                                                    <span style="color: #666; font-size: 14px;">Passwort:</span>
                                                </td>
                                                <td style="padding: 8px 0; text-align: right; border-top: 1px solid #dee2e6;">
                                                    <strong style="color: #1a365d; font-size: 16px; font-family: monospace; background-color: #fff; padding: 4px 10px; border-radius: 4px;"><?= h($password) ?></strong>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Login Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center" style="padding: 20px 0;">
                                        <a href="<?= h($loginUrl) ?>" style="display: inline-block; background: linear-gradient(135deg, #1a365d 0%, #2d4a7c 100%); color: #ffffff; text-decoration: none; padding: 14px 40px; border-radius: 6px; font-size: 16px; font-weight: bold;">
                                            Jetzt anmelden
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Info Box -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #fff3cd; border-radius: 6px; margin-top: 25px;">
                                <tr>
                                    <td style="padding: 15px 20px;">
                                        <p style="color: #856404; font-size: 14px; margin: 0; line-height: 1.5;">
                                            <strong>Hinweis:</strong> Falls Sie diese E-Mail nicht angefordert haben, können Sie sie ignorieren.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1a365d 0%, #2d4a7c 100%); padding: 25px 40px; border-radius: 0 0 8px 8px; text-align: center;">
                            <p style="color: rgba(255,255,255,0.8); font-size: 13px; margin: 0 0 5px 0;">
                                &copy; <?= date('Y') ?> EduBank - Banking-Simulation für Schulen
                            </p>
                            <p style="color: rgba(255,255,255,0.6); font-size: 12px; margin: 0;">
                                Diese E-Mail wurde automatisch generiert.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
