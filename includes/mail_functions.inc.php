<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/mail_config.inc.php';
require_once __DIR__ . '/date_functions.inc.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/mail_config.inc.php';

function sendeTerminBestaetigung(
    string $email,
    string $name,
    string $datum,
    string $anfangZeit
): bool {

    $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();

        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = MAIL_PORT;

        $mail->CharSet = 'UTF-8';

        $mail->setFrom(
            MAIL_FROM_EMAIL,
            MAIL_FROM_NAME
        );

        $mail->addAddress(
            $email,
            $name
        );

        $mail->addEmbeddedImage(
            __DIR__ . '/../img/logo.png',
            'app_logo'
        );

        $mail->isHTML(true);

        $mail->Subject = 'Terminbestätigung - Portfolio projekt';

        $formatiertesDatum = formatiereDatumDeutsch($datum);
        $formatiertesZeit = substr($anfangZeit, 0, 5);

        $mail->Body = "
            <div style='font-family: Arial, sans-serif; color: #445651;'>

                <div style='text-align: left; margin-bottom: 24px;'>
                    <img
                        src='cid:app_logo'
                        width='52'
                        height='52'
                        alt='Terminbuchung-App'
                        style='vertical-align: middle;'
                    >

                    <span
                        style='
                            font-size: 20px;
                            font-weight: bold;
                            color: #445651;
                            vertical-align: middle;
                            margin-left: 8px;
                        '
                    >
                        Terminbuchung-App
                    </span>
                </div>

                <h2>Terminbestätigung</h2>

                <p>
                    Guten Tag "
                    . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') .
                ",</p>

                <p>Ihr Termin wurde erfolgreich gebucht.</p>

                <p>
                    <strong>Datum:</strong>
                    " . htmlspecialchars($formatiertesDatum, ENT_QUOTES, 'UTF-8') . "
                    <br>

                    <strong>Uhrzeit:</strong>
                    " . htmlspecialchars($formatiertesZeit, ENT_QUOTES, 'UTF-8') . " Uhr
                </p>

                <p>Vielen Dank für Ihre Buchung.</p>

            </div>
        ";

        $mail->AltBody =
            "Terminbestätigung\n\n" .
            "Guten Tag " . $name . ",\n\n" .
            "Ihr Termin wurde erfolgreich gebucht.\n\n" .
            "Datum: " . $formatiertesDatum . "\n" .
            "Uhrzeit: " . $formatiertesZeit . " Uhr\n\n" .
            "Vielen Dank für Ihre Buchung.";

        $mail->send();

        return true;

    } catch (Exception $e) {
        error_log(
            'E-Mail konnte nicht gesendet werden: ' .
            $mail->ErrorInfo
        );

        return false;
    }
}