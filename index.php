<?php

require_once __DIR__ . "/includes/config.inc.php";
require_once __DIR__ . "/includes/common.inc.php";
require_once __DIR__ . "/includes/db.inc.php";
require_once __DIR__ . "/includes/termin_functions.inc.php";

session_start();
$conn = dbConnect();

function termingenerator(string $anfang_zeit, string $ende_zeit, int $intervall): array { 
    if ($intervall <= 0) {
        throw new InvalidArgumentException('die Intervallzeit muss größer als 0 sein.');
    }

    $start = new DateTime($anfang_zeit);
    $end = new DateTime($ende_zeit);
    $step = new DateInterval('PT' . $intervall . 'M');

    $termine = [];

    while ($start < $end) {
        $termine[] = $start->format('H:i:s');
        $start->add($step);
    }

    return $termine;
}
// anfrage mysql, ob der Termin schon gebucht ist oder nicht; 

if (isset($_GET['book_datum'], $_GET['book_termin'])) {
    $_SESSION['selecteddatum'] = $_GET['book_datum'];
    $_SESSION['selectedtermin'] = $_GET['book_termin'];

    header("Location: formular.php");
    exit;
}

$monate_deutsch = [
    1 => 'Januar',
    2 => 'Februar',
    3 => 'März',
    4 => 'April',
    5 => 'Mai',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'August',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Dezember'
];

function formatiereDatumDeutsch(string $datum): string {
    $datumObjekt = DateTime::createFromFormat('Y-m-d', $datum);
    return $datumObjekt ? $datumObjekt->format('d.m.Y') : $datum;
}

$success_msg = '';

if (isset($_SESSION['booking_success'])) {
    $datum = $_SESSION['booking_success']['datum'];
    $anfang_zeit = $_SESSION['booking_success']['anfang_zeit'];

    $success_msg = '<p class="success">Termin erfolgreich gebucht!<br>
    Wir heißen Sie herzlich willkommen am ' . htmlspecialchars(formatiereDatumDeutsch($datum)) . 
    ' um ' . htmlspecialchars(substr($anfang_zeit, 0, 5)) . ' Uhr.</p>';

    unset($_SESSION['booking_success']);
}

$heute = new DateTime();
$startGrenze = new DateTime($heute->format('Y-m-01'));   // aktuális hónap első napja
$endGrenze = (clone $startGrenze)->modify('+3 months');  // 3 hónappal később

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

    if (!$dt || $dt->format('Y-m-d') !== $selecteddatum) {
        $dt = null;
    }
}

$ordinationZeiten = [];

if ($dt !== null) {

    $wochentag = (int)$dt->format('N');

    $sql = "
        SELECT
            start_zeit,
            ende_zeit,
            slot_dauer
        FROM ordination_zeiten
        WHERE
            wochentag = ?
            AND aktiv = 1
        ORDER BY start_zeit
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("SQL Fehler bei Ordinationszeiten: " . $conn->error);
    }

    $stmt->bind_param("i", $wochentag);
    $stmt->execute();

    $result = $stmt->get_result();

    $ordinationZeiten = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
}

?>

        <?php require_once __DIR__ . "/includes/header.inc.php"; ?>
        <main class="main-container">
        <h1>Terminvereinbarung</h1>
        <?php echo $success_msg; ?>
        <p>Willkommen auf unserer Terminvereinbarungsseite! Hier können Sie ganz einfach einen Termin für Ihre nächste Konsultation oder Behandlung vereinbaren. 
            Bitte wählen Sie ein Datum aus dem Kalender aus, um die verfügbaren Termine an diesem Tag zu sehen. Klicken Sie dann auf einen freien Termin, um Ihre Buchung abzuschließen. 
            Wir freuen uns darauf, Sie bald bei uns begrüßen zu dürfen!</p>
        <div class="calender">   
        <h2 >Kalender</h2>
                <div>
                    <strong><?php echo $monate_deutsch[$angezeigterMonat->format('n')] . ' ' . $angezeigterMonat->format('Y'); ?></strong>
                </div>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                <div>
                    <?php if ($vorherigErlaubt): ?>
                        <a href="?jahr=<?php echo $vorherigerMonat->format('Y'); ?>&monat=<?php echo $vorherigerMonat->format('n'); ?>">
                            &laquo; Vorheriges Monat
                        </a>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if ($naechstErlaubt): ?>
                        <a href="?jahr=<?php echo $naechsterMonat->format('Y'); ?>&monat=<?php echo $naechsterMonat->format('n'); ?>">
                            Nachste Monat &raquo;
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <table border="3" cellpadding="5" cellspacing="0">
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

                    // Erstellt für jeden Tag des Monats eine Zelle mit einem Datumslink.
                    $aktualDate = date('Y-m-d');
                    $maxBuchbar = (new DateTime())->modify('+3 months')->format('Y-m-d');
                    for ($tag = 1; $tag <= $nummerdesTages; $tag++, $siebenTag++) { 
                        $datum = sprintf('%04d-%02d-%02d', $jahr, $monat, $tag);
                        $wochentag = date('N', strtotime($datum));

                        if ($wochentag >= 6) { 
                            echo "<td style='color: gray;'>$tag</td>";
                        }
                        elseif ($datum < $aktualDate || $datum > $maxBuchbar) {
                            echo "<td style='color: gray;'>$tag</td>";
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
            </div>
            <h2>Freie Termine am <?php echo htmlspecialchars($selecteddatum); ?></h2>
            <?php
            
            // Überprüft, ob die Variablen $anfang_zeit und $ende_zeit gesetzt sind. 
            if ($selecteddatum === '') {
                echo "<p>Bitte wählen Sie ein Datum aus dem Kalender aus, um die verfügbaren Termine an diesem Tag zu sehen.</p>";
            } elseif (empty($ordinationZeiten)) {
                echo "<p>Für dieses Datum sind keine Termine verfügbar.</p>";            
            }
            else {
                
                    foreach ($ordinationZeiten as $ordinationZeit) {
            
            $alles = termingenerator(
                $selecteddatum . " " . $ordinationZeit["start_zeit"], 
                $selecteddatum . " " . $ordinationZeit["ende_zeit"], 
                (int)$ordinationZeit["slot_dauer"]
            );

                // Prüft für jeden Termin, ob er bereits gebucht ist.
                // Freie Termine werden als Link zum Buchungsformular angezeigt.
                foreach ($alles as $termin) {   
                    $istGebucht = pruefeTermin($conn, $selecteddatum, $termin);

                    if ($istGebucht) {
                        echo '<span class="gebucht">Nicht buchbar</span><br>';
                    } 
                    else {
                        echo "<a href='?jahr=" 
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
                                . htmlspecialchars($termin)
                                . "</a><br>";
                        }
                    }
                }
            }
            ?>
        <h2>Adminbereich</h2>
            <p>Um die Admin-Seite zu betreten, klicken Sie bitte auf den folgenden Link:</p>
                <a href="einloggen.php">Admin-Seite betreten</a>
<?php require_once __DIR__ . "/includes/footer.inc.php"; ?>
<?php $conn->close(); ?>