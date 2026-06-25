<?php

require("includes/config.inc.php");
require("includes/common.inc.php");
require("includes/db.inc.php");

session_start();
$conn = dbConnect();

/**
 * Erzeugt verfügbare Zeitfenster zwischen Anfangs- und Endzeit.
 *
 * @param string $anfang_zeit
 * @param string $ende_zeit
 * @param int $intervall Intervall in Minuten
 * @return array
 */
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

function pruefeTermin($conn, string $datum, string $anfang_zeit): string {
    $sql = "
        SELECT anfang_zeit
        FROM gespeicherte_termin
        WHERE datum = ?
          AND anfang_zeit = ?
        LIMIT 1
    ";
    $stmt = $conn->prepare($sql);

	if (!$stmt) {
    die("SQL Fehler bei Terminprüfung: " . $conn->error);
	}
    $stmt->bind_param("ss", $datum, $anfang_zeit);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
	
    $stmt->close();

    return $row ? 'Nicht buchbar' : $anfang_zeit;

}
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

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success_msg = '<p class="success">Termin erfolgreich gebucht!</p>';
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
    $dt = new DateTime($selecteddatum);
}

const ORDINATION_ZEITEN = [
    1 => ["08:00", "12:00"],
    2 => ["13:00", "18:00"],
    3 => ["08:00", "12:00"],
    4 => ["13:00", "18:00"],
    5 => ["08:00", "12:00"]
];

//Ordinationszeiten für den ausgewählten Wochentag ermitteln.
if ($dt !== null && isset(ORDINATION_ZEITEN[$dt->format('N')])) {   
    $anfang_zeit = ORDINATION_ZEITEN[$dt->format('N')][0];
    $ende_zeit = ORDINATION_ZEITEN[$dt->format('N')][1];
}


?>
<!doctype html>
<html lang="de">
	<head>
		<title>Terminvereinbarung</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/dark.css">
        <link rel="stylesheet" href="css/common.css">
	</head>
	<body>
        <h1>Terminvereinbarung</h1>
        <?php if (isset($success_msg)) echo $success_msg; ?>
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
                    <th>H</th><th>K</th><th>Sze</th><th>Cs</th><th>P</th><th>Szo</th><th>V</th>
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
            } elseif (!isset($anfang_zeit) || !isset($ende_zeit)) {
                echo "<p>Für dieses Datum sind keine Termine verfügbar.</p>";            
            }
            else {
                $alles = termingenerator($selecteddatum . " " . $anfang_zeit, $selecteddatum . " " . $ende_zeit, 30);

                // Prüft für jeden Termin, ob er bereits gebucht ist.
                // Freie Termine werden als Link zum Buchungsformular angezeigt.
                foreach ($alles as $termin) {   
                    $status = pruefeTermin($conn, $selecteddatum, $termin);

                    if ($status === 'Nicht buchbar') {
                                    echo htmlspecialchars($status) . "<br>";
                    } 
                    else {
                        echo "<a href='?jahr=" . urlencode($jahr) . "&monat=" . urlencode($monat) . "&datum=" . urlencode($selecteddatum) . "&book_datum=" . urlencode($selecteddatum) . "&book_termin=" . urlencode($termin) . "'>"
                                        . htmlspecialchars($status)
                                        . "</a><br>";
                    }
                }
            }
            ?>
        <h2>Adminbereich</h2>
            <p>Um die Admin-Seite zu betreten, klicken Sie bitte auf den folgenden Link:</p>
                <a href="einloggen.php">Admin-Seite betreten</a>
    </body>    
</html>
<?php $conn->close(); ?>