<?php

require_once __DIR__ . "/includes/config.inc.php";
require_once __DIR__ . "/includes/common.inc.php";
require_once __DIR__ . "/includes/db.inc.php";
require_once __DIR__ . "/includes/termin_functions.inc.php";
require_once __DIR__ . "/includes/date_functions.inc.php";

session_start();
$conn = dbConnect();

$heute = new DateTime();

$aktualDate = $heute->format('Y-m-d');
$startGrenze = new DateTime($heute->format('Y-m-01'));   // aktuális hónap első napja
$endGrenze = (clone $startGrenze)->modify('+3 months');  // 3 hónappal később

$maxBuchbar = (clone $heute)
    ->modify('+3 months')
    ->format('Y-m-d');


// Validiert den ausgewählten Termin und speichert ihn in der Session.

if (isset($_GET['book_datum'], $_GET['book_termin'])) {
    $bookDatum = $_GET['book_datum'];
    $bookTermin = $_GET['book_termin'];

    $terminSlot = findeTerminSlot(
        $conn,
        $bookDatum,
        $bookTermin
    );

    if (
        $terminSlot !== null &&
        $bookDatum >= $aktualDate &&
        $bookDatum <= $maxBuchbar
    ) {
        $_SESSION['selecteddatum'] = $bookDatum;
        $_SESSION['selectedtermin'] = $bookTermin;

        header("Location: formular.php");
        exit;
    }

}

$success_msg = '';

if (isset($_SESSION['booking_success'])) {
    $datum = $_SESSION['booking_success']['datum'];
    $anfang_zeit = $_SESSION['booking_success']['anfang_zeit'];
    $emailGesendet = $_SESSION['booking_success']['email_gesendet'] ?? false;

    $success_msg = '<p class="success">Termin erfolgreich gebucht!<br>
    Wir heißen Sie herzlich willkommen am ' . htmlspecialchars(formatiereDatumDeutsch($datum)) . 
    ' um ' . htmlspecialchars(substr($anfang_zeit, 0, 5)) . ' Uhr.</p>';
    if ($emailGesendet) {
        $success_msg .= '<br>Eine Terminbestätigung wurde per E-Mail versendet.';
    }

    unset($_SESSION['booking_success']);
}

$jahr = isset($_GET['jahr']) ? (int)$_GET['jahr'] : (int)$heute->format('Y');
$monat = isset($_GET['monat']) ? (int)$_GET['monat'] : (int)$heute->format('m');

$angezeigterMonat = DateTime::createFromFormat('Y-n-j', "$jahr-$monat-1");

if (!$angezeigterMonat) {
    $angezeigterMonat = clone $startGrenze;
}

if ($angezeigterMonat < $startGrenze) {
    $angezeigterMonat = clone $startGrenze;
}

if ($angezeigterMonat > $endGrenze) {
    $angezeigterMonat = clone $endGrenze;
}

$jahr = (int)$angezeigterMonat->format('Y');
$monat = (int)$angezeigterMonat->format('n');


$ersteTag = mktime(0, 0, 0, $monat, 1, $jahr);
$nummerdesTages = date('t', $ersteTag);
$ersteTaginWoche = date('N', $ersteTag);

$vorherigerMonat = (clone $angezeigterMonat)->modify('-1 month');
$naechsterMonat = (clone $angezeigterMonat)->modify('+1 month');

$vorherigErlaubt = $vorherigerMonat >= $startGrenze;
$naechstErlaubt = $naechsterMonat <= $endGrenze;

// ausgewähltes Datum aus der URL abrufen, wenn vorhanden
$selecteddatum = $_GET['datum'] ?? '';  

$dt = null;

if ($selecteddatum !== '') {

    $dt = DateTime::createFromFormat('Y-m-d', $selecteddatum);

    if (
        !$dt ||
        $dt->format('Y-m-d') !== $selecteddatum ||
        $selecteddatum < $aktualDate ||
        $selecteddatum > $maxBuchbar
    ) {
        $dt = null;
        $selecteddatum = '';
    }
}

$ordinationZeiten = [];
$gebuchteTermine = [];

if ($dt !== null) {

    $wochentag = (int)$dt->format('N');

    $ordinationZeiten = holeOrdinationszeiten(
        $conn,
        $wochentag
    );

        $gebuchteTermine = holeGebuchteTermine(
        $conn,
        $selecteddatum
    );
}

?>

    <?php require_once __DIR__ . "/includes/header.inc.php"; ?>
        <h1>Terminvereinbarung</h1>
        <?php echo $success_msg; ?>
        <p>Willkommen auf unserer Terminvereinbarungsseite! Hier können Sie ganz einfach einen Termin für Ihre nächste Konsultation oder Behandlung vereinbaren. 
            Bitte wählen Sie ein Datum aus dem Kalender aus, um die verfügbaren Termine an diesem Tag zu sehen. Klicken Sie dann auf einen freien Termin, um Ihre Buchung abzuschließen. 
            Wir freuen uns darauf, Sie bald bei uns begrüßen zu dürfen!</p>
        <section class="calendar-container">   
        <h2 >Terminkalender</h2>
                <div>
                    <h3><strong><?= htmlspecialchars(
                            formatiereMonatJahrDeutsch($angezeigterMonat),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?></strong></h3>
                </div>
                <div class="calendar-navigation">
                    <?php if ($vorherigErlaubt): ?>
                        <a href="?jahr=<?= $vorherigerMonat->format('Y') ?>&monat=<?= $vorherigerMonat->format('n') ?>" class="calendar-nav-btn">
                            &laquo; Vorheriger Monat
                        </a>
                    <?php else: ?>
                        <span class="calendar-nav-btn disabled">&laquo; Vorheriger Monat</span>
                    <?php endif; ?>

                    <?php if ($naechstErlaubt): ?>
                        <a href="?jahr=<?= $naechsterMonat->format('Y') ?>&monat=<?= $naechsterMonat->format('n') ?>" class="calendar-nav-btn">
                            Nächster Monat &raquo;
                        </a>
                    <?php else: ?>
                        <span class="calendar-nav-btn disabled">Nächster Monat &raquo;</span>
                    <?php endif; ?>
                </div>
            <table class="calendar">
                <tr>
                    <th>Mo</th><th>Di</th><th>Mi</th><th>Do</th><th>Fr</th><th>Sa</th><th>So</th>
                </tr>
                <tr>
                    <?php
                    // fügt leere Zellen hinzu, damit der Kalender korrekt ausgerichtet ist.
                    for ($lehredate = 1; $lehredate < $ersteTaginWoche; $lehredate++) { 
                        echo "<td></td>";
                    }

                    $siebenTag = $ersteTaginWoche;

                    for ($tag = 1; $tag <= $nummerdesTages; $tag++, $siebenTag++) { 
                        $datum = sprintf('%04d-%02d-%02d', $jahr, $monat, $tag);
                        $wochentag = date('N', strtotime($datum));

                        if ($wochentag >= 6) { 
                            echo "<td class='calendar-disabled'>$tag</td>";
                        }
                        elseif ($datum < $aktualDate || $datum > $maxBuchbar) {
                            echo "<td class='calendar-disabled'>$tag</td>";
                        } else {
                            echo "<td><a href='?jahr=" . $jahr . "&monat=" . $monat . "&datum=" . urlencode($datum) . "'>$tag</a></td>";
                        }

                        // Es überprüft, ob die Anzahl der Tage seit dem letzten Zeilenumbruch durch 7 teilbar ist. 
                        // Wenn ja, wird eine neue Zeile gestartet. 
                        if ($siebenTag % 7 == 0 && $tag != $nummerdesTages) { 
                            echo "</tr><tr>";
                        }
                    }
                    ?>
                </tr>
            </table>
            </section>
            <section class="appointments-container">
                <?php if ($selecteddatum): ?>

                    <h2>
                        Freie Termine am
                        <?= htmlspecialchars(
                            formatiereDatumDeutschLang($selecteddatum),
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </h2>

        <div class="termin-list">
                
           <?php foreach ($ordinationZeiten as $ordinationZeit) {
            
            $alles = generiereTerminSlots(
                $selecteddatum . " " . $ordinationZeit["start_zeit"], 
                $selecteddatum . " " . $ordinationZeit["ende_zeit"], 
                (int)$ordinationZeit["slot_dauer"]
            );

                // Prüft für jeden Termin, ob er bereits gebucht ist.
                // Freie Termine werden als Link zum Buchungsformular angezeigt.
                foreach ($alles as $termin) {   
                    $istGebucht = isset($gebuchteTermine[$termin]);

                    if ($istGebucht) {
                    echo '<span class="termin-slot gebucht">'
                        . htmlspecialchars(substr($termin, 0, 5), ENT_QUOTES, 'UTF-8')
                        . '</span>';
                    } 
                    else {
                        echo "<a class='termin-slot buchbar' href='?jahr=" 
                                . urlencode($jahr) 
                                . "&monat=" 
                                . urlencode($monat) 
                                . "&datum=" 
                                . urlencode($selecteddatum) 
                                . "&book_datum=" 
                                . urlencode($selecteddatum) 
                                . "&book_termin=" 
                                . urlencode($termin) 
                                . "'>"
                                . htmlspecialchars(substr($termin, 0, 5), ENT_QUOTES, 'UTF-8')
                                . "</a>";
                        }
                    }
                }
            ?>
            </div>
                <?php else: ?>

                    <h2>Freie Termine</h2>

                    <p class="termin-empty">
                        Bitte wählen Sie ein Datum aus dem Kalender aus,
                        um die verfügbaren Termine an diesem Tag zu sehen.
                    </p>

                <?php endif; ?>

            </section>
<?php require_once __DIR__ . "/includes/footer.inc.php"; ?>
<?php $conn->close(); ?>