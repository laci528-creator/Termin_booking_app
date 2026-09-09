<?php

require_once __DIR__ . "/includes/config.inc.php";
require_once __DIR__ . "/includes/common.inc.php";
require_once __DIR__ . "/includes/db.inc.php";
require_once __DIR__ . "/includes/termin_functions.inc.php";
require_once __DIR__ . "/includes/date_functions.inc.php";

session_start();
$conn = dbConnect();


$heute = new DateTimeImmutable('today'); 
$aktualDate = $heute->format('Y-m-d');
$startGrenze = $heute->modify('first day of this month');
$endGrenze = $startGrenze->modify('+3 months');
$maxBuchbar = $heute->modify('+3 months')->format('Y-m-d');


if (isset($_GET['book_datum'], $_GET['book_termin'])) {
    $bookDatum = $_GET['book_datum'];
    $bookTermin = $_GET['book_termin'];

    $terminSlot = findeTerminSlot($conn, $bookDatum, $bookTermin);

    if ($terminSlot !== null && 
        $bookDatum >= $aktualDate && 
        $bookDatum <= $maxBuchbar && 
        !istTerminVergangen($bookDatum, $bookTermin)
    ) {
        $_SESSION['selecteddatum'] = $bookDatum;
        $_SESSION['selectedtermin'] = $bookTermin;
        header("Location: formular.php");
        exit;
    }
}


$success_msg = '';
if (isset($_SESSION['booking_success'])) {
    $bs = $_SESSION['booking_success'];
    $zeit = htmlspecialchars(substr($bs['anfang_zeit'], 0, 5));
    $datumStr = htmlspecialchars(formatiereDatumDeutsch($bs['datum']));
    
    $success_msg .= "<p class='success'>Termin erfolgreich gebucht!<br>";
    $success_msg .= "Wir heißen Sie herzlich willkommen am {$datumStr} um {$zeit} Uhr.";
    if (!empty($bs['email_gesendet'])) {
        $success_msg .= "<br>Eine Terminbestätigung wurde per E-Mail versendet.";
    }
    $success_msg .= "</p>";
    unset($_SESSION['booking_success']);
}


$jahr = (int)($_GET['jahr'] ?? $heute->format('Y'));
$monat = (int)($_GET['monat'] ?? $heute->format('m'));

$angezeigterMonat = DateTimeImmutable::createFromFormat('!Y-n-j', "$jahr-$monat-1") ?: $startGrenze;

if ($angezeigterMonat < $startGrenze) {
    $angezeigterMonat = $startGrenze;
}

if ($angezeigterMonat > $endGrenze) {
    $angezeigterMonat = $endGrenze;
}

$jahr = (int)$angezeigterMonat->format('Y');
$monat = (int)$angezeigterMonat->format('n');
$tageImMonat = (int)$angezeigterMonat->format('t');
$ersterTagWochentag = (int)$angezeigterMonat->format('N'); // 1=Mo, 7=So

$vorherigerMonat = $angezeigterMonat->modify('-1 month');
$naechsterMonat = $angezeigterMonat->modify('+1 month');
$vorherigErlaubt = $vorherigerMonat >= $startGrenze;
$naechstErlaubt = $naechsterMonat <= $endGrenze;



$selecteddatum = $_GET['datum'] ?? '';
$ordinationZeiten = [];
$gebuchteTermine = [];

if ($selecteddatum) {
    $dt = DateTimeImmutable::createFromFormat('!Y-m-d', $selecteddatum);
    if ($dt && $dt->format('Y-m-d') === $selecteddatum && $selecteddatum >= $aktualDate && $selecteddatum <= $maxBuchbar) {
        $wochentag = (int)$dt->format('N');
        $ordinationZeiten = holeOrdinationszeiten($conn, $wochentag);
        $gebuchteTermine = holeGebuchteTermine($conn, $selecteddatum);
    } else {
        $selecteddatum = '';
    }
}
?>


<?php require_once __DIR__ . "/includes/header.inc.php"; ?>

<h1>Terminvereinbarung</h1>
<?= $success_msg ?>

<p>Willkommen auf unserer Terminvereinbarungsseite! Hier können Sie ganz einfach einen Termin für Ihre nächste Konsultation oder Behandlung vereinbaren. 
    Bitte wählen Sie ein Datum aus dem Kalender aus, um die verfügbaren Termine an diesem Tag zu sehen. Klicken Sie dann auf einen freien Termin, um Ihre Buchung abzuschließen. 
    Wir freuen uns darauf, Sie bald bei uns begrüßen zu dürfen!</p>

<section class="calendar-container">   
    <h2>Terminkalender</h2>
    <h3><strong><?= htmlspecialchars(formatiereMonatJahrDeutsch($angezeigterMonat)) ?></strong></h3>
    
    <div class="calendar-navigation">
        <?php if ($vorherigErlaubt): ?>
            <a href="?jahr=<?= $vorherigerMonat->format('Y') ?>&monat=<?= $vorherigerMonat->format('n') ?>" class="calendar-nav-btn">&laquo; Vorheriger Monat</a>
        <?php else: ?>
            <span class="calendar-nav-btn disabled">&laquo; Vorheriger Monat</span>
        <?php endif; ?>

        <?php if ($naechstErlaubt): ?>
            <a href="?jahr=<?= $naechsterMonat->format('Y') ?>&monat=<?= $naechsterMonat->format('n') ?>" class="calendar-nav-btn">Nächster Monat &raquo;</a>
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
            // lehre zeile 
            for ($i = 1; $i < $ersterTagWochentag; $i++) {
                echo "<td></td>";
            }

            $aktuellerWochentag = $ersterTagWochentag;


            for ($tag = 1; $tag <= $tageImMonat; $tag++) {
                $datumStr = sprintf('%04d-%02d-%02d', $jahr, $monat, $tag);
                $istWochenende = ($aktuellerWochentag >= 6);
                $istAusserhalb = ($datumStr < $aktualDate || $datumStr > $maxBuchbar);

                if ($istWochenende || $istAusserhalb) {
                    echo "<td class='calendar-disabled'>$tag</td>";
                } else {
                    $url = '?' . http_build_query(['jahr' => $jahr, 'monat' => $monat, 'datum' => $datumStr]);
                    $sichereUrl = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
                    echo "<td><a href='$sichereUrl'>$tag</a></td>";
                }

                if ($aktuellerWochentag == 7 && $tag != $tageImMonat) {
                    echo "</tr><tr>";
                    $aktuellerWochentag = 1;
                } else {
                    $aktuellerWochentag++;
                }
            }
            ?>
        </tr>
    </table>
</section>

<section class="appointments-container">
    <?php if ($selecteddatum): ?>
        <h2>Freie Termine am <?= htmlspecialchars(formatiereDatumDeutschLang($selecteddatum)) ?></h2>
        
        <div class="termin-list">
            <?php foreach ($ordinationZeiten as $ordinationZeit): 
                $slots = generiereTerminSlots(
                    $selecteddatum . " " . $ordinationZeit["start_zeit"], 
                    $selecteddatum . " " . $ordinationZeit["ende_zeit"], 
                    (int)$ordinationZeit["slot_dauer"]
                );

                foreach ($slots as $termin):
                    $terminAnzeige = htmlspecialchars(substr($termin, 0, 5));
                    $istGebucht = isset($gebuchteTermine[$termin]);
                    $istVergangen = istTerminVergangen($selecteddatum, $termin);

                    if ($istGebucht || $istVergangen): ?>
                        <span class="termin-slot gebucht"><?= $terminAnzeige ?></span>
                    <?php else: 
                        $buchungUrl = '?' . http_build_query([
                            'jahr' => $jahr, 'monat' => $monat, 'datum' => $selecteddatum,
                            'book_datum' => $selecteddatum, 'book_termin' => $termin
                        ]);
                    ?>
                        <a class='termin-slot buchbar' href='<?= htmlspecialchars($buchungUrl, ENT_QUOTES, 'UTF-8') ?>'><?= $terminAnzeige ?></a>
                    <?php endif; 
                endforeach;
            endforeach; ?>
        </div>
    <?php else: ?>
        <h2>Freie Termine</h2>
        <p class="termin-empty">Bitte wählen Sie ein Datum aus dem Kalender aus, um die verfügbaren Termine an diesem Tag zu sehen.</p>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . "/includes/footer.inc.php"; ?>
<?php $conn->close(); ?>