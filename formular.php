<?php

require_once __DIR__ . "/includes/config.inc.php";
require_once __DIR__ . "/includes/common.inc.php";
require_once __DIR__ . "/includes/db.inc.php";
require_once __DIR__ . "/includes/date_functions.inc.php";
require_once __DIR__ . "/includes/termin_functions.inc.php";
require_once __DIR__ . "/includes/mail_functions.inc.php";
require_once __DIR__ . "/includes/booking_functions.inc.php";

session_start();

$csrfToken = getCsrfToken();

$conn = dbConnect();

$selecteddatum = $_SESSION['selecteddatum'] ?? '';
$selectedtermin = $_SESSION['selectedtermin'] ?? '';

if (empty($selecteddatum) || empty($selectedtermin)) {
    header("Location: index.php");
    exit;
}

$terminSlot = findeTerminSlot(
        $conn,
        $selecteddatum,
        $selectedtermin
    );

if ($terminSlot === null || istTerminVergangen($selecteddatum, $selectedtermin)) {
    unset(
        $_SESSION['selecteddatum'],
        $_SESSION['selectedtermin']
    );

    header("Location: index.php");
    exit;
}

$terminende = $terminSlot["ende_zeit"];

$nachname = '';
$telefon = '';
$email = '';
$bemerkung = '';

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $csrfTokenPost = $_POST['csrf_token'] ?? '';

    if (!validiereCsrfToken($csrfTokenPost)) {
        http_response_code(403);
        die("Ungültige Anfrage.");
    }

    $nachname = trim($_POST['NN'] ?? '');
    $telefon = trim($_POST['TN'] ?? '');
    $email = trim($_POST['E'] ?? '');
    $bemerkung = trim($_POST['T'] ?? '');

    $datum = $selecteddatum;
    $anfang_zeit = $selectedtermin;

    if (
        !empty($nachname) &&
        !empty($telefon) &&
        !empty($email) &&
        !empty($datum) &&
        !empty($anfang_zeit)
    ) {

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

                $fehler = validiereTermin(
                    $datum,
                    $anfang_zeit,
                    $terminende
                );

                if ($fehler !== null) {

                    $msg = '<p class="error">'
                        . htmlspecialchars($fehler, ENT_QUOTES, 'UTF-8')
                        . '</p>';

                } elseif (!pruefeTermin($conn, $datum, $anfang_zeit)) {

                    try {      
                        speichereTerminBuchung(
                                    $conn,
                                    $nachname,
                                    $telefon,
                                    $email,
                                    $datum,
                                    $anfang_zeit,
                                    $terminende,
                                    $bemerkung
                        );

                        $emailGesendet = sendeTerminBestaetigung(
                            $email,
                            $nachname,
                            $datum,
                            $anfang_zeit
                        );

                        $_SESSION['booking_success'] = [
                            'datum' => $datum,
                            'anfang_zeit' => $anfang_zeit,
                            'email_gesendet' => $emailGesendet
                        ];

                        unset(
                            $_SESSION['selecteddatum'],
                            $_SESSION['selectedtermin']
                        );

                        header("Location: index.php");
                        exit;

                    } catch (Throwable $e) {


                        $msg = '<p class="error">
                            Fehler beim Buchen des Termins.
                        </p>';
                    }

                } else {

                    $msg = '<p class="error">
                        Der ausgewählte Termin ist bereits gebucht.
                        Bitte wählen Sie einen anderen Termin.
                    </p>';
                }

        } else {

            $msg = '<p class="error">
                Bitte geben Sie eine gültige E-Mail-Adresse ein.
            </p>';
        }

    } else {

        $msg = '<p class="error">
            Bitte füllen Sie alle erforderlichen Felder aus.
        </p>';
    }
}

$conn->close();
?>

    <?php require_once __DIR__ . "/includes/header.inc.php"; ?>
        <h1>Terminbuchung</h1>
		<?php echo $msg; // Fehlermeldung anzeigen, falls vorhanden ?>
        <h2>Formular</h2>
		<form method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES,'UTF-8'); ?>">
			<fieldset>
				<legend>Personaldaten</legend>
                <label>
					Name:
					<input type="text" name="NN" value="<?= htmlspecialchars($nachname, ENT_QUOTES, 'UTF-8') ?>" required>
				</label>
                <label>
                    Telefonnummer:
                    <input type="tel" name="TN" value="<?= htmlspecialchars($telefon, ENT_QUOTES, 'UTF-8') ?>" required>
                </label>
                    <label>
                        Emailadresse:
					<input type="email" name="E" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" required>
				</label>
			</fieldset>
			<fieldset>
				<legend>Termindaten</legend>
				<label>
					Datum:
					<input type="text" name="VN" value="<?= htmlspecialchars(formatiereDatumDeutsch($selecteddatum), ENT_QUOTES, 'UTF-8'); ?>" readonly>
				</label>
				<label>
					Anfangszeit:
					<input type="text" name="ANF" value="<?= htmlspecialchars(substr($selectedtermin, 0, 5), ENT_QUOTES, 'UTF-8'); ?>" readonly>
				</label>
				<label>
					Endzeit:
					<input type="text" name="GD" value="<?= htmlspecialchars(substr($terminende, 0, 5), ENT_QUOTES, 'UTF-8'); ?>" readonly>
				</label>
                <label>
                    Bemerkung für den Arzt:
                    <textarea name="T" rows="4" cols="50"><?= htmlspecialchars($bemerkung, ENT_QUOTES, 'UTF-8') ?></textarea>
                </label>
			</fieldset>
			<input type="submit" value="Termin buchen">
		</form>
    <?php require_once __DIR__ . "/includes/footer.inc.php"; ?>