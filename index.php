<?php

require("includes/config.inc.php");
require("includes/common.inc.php");
require("includes/db.inc.php");


$conn = dbConnect();

/* a besz#räsi minta
INSERT INTO kunden (`name`,`telefon`,`email`)
VALUES ('Laszlo_Haraszti','06367685214','laci528@gmail.com');

INSERT INTO gespeicherte_termin (`kunden_id`, `datum`, `anfang_zeit`, `ende_zeit`, `bemerkung`) 
VALUES (LAST_INSERT_ID(),'2026-03-31','15:00:00','15:30:00','gdh fg fd dfd  dfgdf ad gdf ');


function termingenerator(string $anfang_zeit, string $ende_zeit, int $intervall):array {
    $termine = [];
    
    $start = strtotime($anfang_zeit);
    $end = strtotime($ende_zeit);
    
    while($start < $end) {
        $termine[] = date("Y-m-d H:i:s",$start);
        $start += $intervall*60;
    }
    
    return $termine;
}   
*/

function termingenerator(string $anfang_zeit, string $ende_zeit, int $intervall): array
{
    if ($intervall <= 0) {
        throw new InvalidArgumentException('Az intervallumnak 0-nál nagyobbnak kell lennie.');
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

function terminvalidator($conn, string $datum, string $anfang_zeit): string
{
    $sql = "
        SELECT anfang_zeit
        FROM gespeicherte_termin
        WHERE datum = '$datum'
          AND anfang_zeit = '$anfang_zeit'
        LIMIT 1
    ";

    $result = dbQuery($conn, $sql);
    $row = dbFetch($result);

    if ($row) {
        return 'Nicht buchbar';
    }

    return $anfang_zeit;
}

$ev = 2026;
$honap = 3;

$elsoNap = mktime(0, 0, 0, $honap, 1, $ev);
$napokSzama = date('t', $elsoNap);
$elsoNapHetiSorszama = date('N', $elsoNap);

$selecteddatum = $_GET['datum'] ?? '';
echo $selecteddatum . "<br>";

$dt = new DateTime($selecteddatum);
echo $dt->format('N') . "<br>";

const ORDINATION_ZEITEN = [
    1 => ["08:00", "12:00"],
    2 => ["13:00", "18:00"],
    3 => ["08:00", "12:00"],
    4 => ["13:00", "18:00"],
    5 => ["08:00", "12:00"]
];

if (isset(ORDINATION_ZEITEN[$dt->format('N')])) {
    $anfang_zeit = ORDINATION_ZEITEN[$dt->format('N')][0];
    $ende_zeit = ORDINATION_ZEITEN[$dt->format('N')][1];

    echo "Anfang: $anfang_zeit, Ende: $ende_zeit" . "<br>";
} else {
    echo "Diese Tageszeit hat keine Ordination.";
}

?>

<!doctype html>
<html lang="de">
	<head>
		<title>Terminvereinbarung</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/dark.css">
	</head>
	<body>
        <h1>Terminvereinbarung</h1>
        <h2>Kalender</h2>
            <table border="3" cellpadding="5" cellspacing="0">
                <tr>
                    <th>H</th><th>K</th><th>Sze</th><th>Cs</th><th>P</th><th>Szo</th><th>V</th>
                </tr>
                <tr>
                    <?php
                    for ($lehredate = 1; $lehredate < $elsoNapHetiSorszama; $lehredate++) {
                        echo "<td></td>";
                    }

                    $hetNap = $elsoNapHetiSorszama;

                    for ($nap = 1; $nap <= $napokSzama; $nap++, $hetNap++) {
                        $datum = sprintf('%04d-%02d-%02d', $ev, $honap, $nap);
                        echo "<td><a href='?datum=$datum'>$nap</a></td>";

                        if ($hetNap % 7 == 0 && $nap != $napokSzama) {
                            echo "</tr><tr>";
                        }
                    }
                    ?>
                </tr>
            </table>

            <h2>Frei Termine am <?php echo $selecteddatum; ?></h2>
            <?php   
            if (!isset($anfang_zeit) || !isset($ende_zeit)) {
                echo "Keine Termine verfügbar.";
            } else {    
            $alles = termingenerator($selecteddatum . " " . $anfang_zeit, $selecteddatum . " " . $ende_zeit, 30);


                foreach ($alles as $termin) {
                        $status = terminvalidator($conn, $selecteddatum, $termin);
                                if ($status === 'Nicht buchbar') {
                                    echo htmlspecialchars($status) . "<br>";
                                } else {
                                    echo "<a href='formular.php?datum=" . urlencode($selecteddatum) . "&termin=" . urlencode($termin) . "'>"
                                        . htmlspecialchars($status)
                                        . "</a><br>";
                                }
                }
            }
            
            ?>
            <h2>Admin seite</h2>
            <p>Um die Admin-Seite zu betreten, klicken Sie bitte auf den folgenden Link:</p>
            <button><a href="einloggen.php">Admin-Seite betreten</a></button>

    </body>     
</html>






