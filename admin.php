<?php

require_once __DIR__ . "/includes/config.inc.php";
require_once __DIR__ . "/includes/common.inc.php";
require_once __DIR__ . "/includes/db.inc.php";
require_once __DIR__ . "/includes/termin_functions.inc.php";
require_once __DIR__ . "/includes/admin_functions.inc.php";

$conn = dbConnect();
session_start();

$msg = '';
if (empty($_SESSION["eingeloggt"])) {
    header("Location: einloggen.php");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form_type = $_POST["form_type"] ?? '';

        if ($form_type === "logout") {
            logoutUser();
        }

        elseif ($form_type === 'datum_andern') {
            $datum = $_POST['datum_andern'] ?? '';

            if ($datum !== '') {
                $_SESSION["date"] = $datum;
            }
        }
        elseif ($form_type === 'termin_bearbeiten') {
            if (
                empty($_POST['csrf_token']) ||
                !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
            ) {
                die("Ungültige Anfrage.");
            }


            if (isset($_POST['delete'])) {
                $termin_id = (int)$_POST['delete'];
                $msg = deleteTermin($conn, $termin_id);
            
            }
            elseif (isset($_POST['update'])) {
                $termin_id = (int)$_POST['update'];
                $msg = updateTermin(    
                    $conn,
                    $termin_id,
                    $_POST['datum'][$termin_id] ?? '',
                    $_POST['anfang_zeit'][$termin_id] ?? '',
                    $_POST['name'][$termin_id] ?? '',
                    $_POST['telefon'][$termin_id] ?? '',
                    $_POST['email'][$termin_id] ?? '',
                    $_POST['bemerkung'][$termin_id] ?? ''
                );
            }
        }
}

$gefragtedatum = $_SESSION["date"] ?? '';
$terminCount = 0;

?>

<?php require_once __DIR__ . "/includes/header.inc.php"; ?>

<h1>Gebuchte Termine <br> von <?php echo htmlspecialchars($gefragtedatum, ENT_QUOTES, 'UTF-8');?> und naechste zwei woche</h1>
        <form method="post">
            <input type="hidden" name="form_type" value="datum_andern">
            <label>
                Startdatum für abfrage:
                <input type="date" name="datum_andern">
			</label><br>
			<button type="submit">Datum Andern</button>
		</form>
<?php echo $msg; ?>
<form method="post">
    <input type="hidden" name="form_type" value="termin_bearbeiten">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES,'UTF-8'); ?>">
<div class="table-wrapper">
    <table border="1" cellpadding="5" cellspacing="0">  
        <tr>
            <th>Datum</th>
            <th>Anfangszeit</th>
            <th>Endezeit</th>
            <th>Name</th>
            <th>Telefon</th>
            <th>Email</th>
            <th>Bemerkung</th>
            <th>Delete</th>
            <th>Update</th>
        </tr>   
<?php
if ($gefragtedatum === '') {
    echo '<tr><td colspan="9" class="error">Kein Startdatum in der Sitzung gefunden.</td></tr>';
}
else {
$alledate = zweiWochenDaten($gefragtedatum);


foreach($alledate as $datum) {
            $sql = "SELECT 
                        gespeicherte_termin.id,
                        gespeicherte_termin.datum, 
                        gespeicherte_termin.anfang_zeit, 
                        gespeicherte_termin.ende_zeit,
                        gespeicherte_termin.bemerkung,
                        kunden.name,
                        kunden.telefon,
                        kunden.email
                    FROM gespeicherte_termin
                    JOIN kunden ON gespeicherte_termin.kunden_id = kunden.id
                    WHERE gespeicherte_termin.datum = '" . $conn->real_escape_string($datum) . "'
                    ORDER BY gespeicherte_termin.datum, gespeicherte_termin.anfang_zeit ASC
            
            ";
            $result = dbQuery($conn, $sql);

            while($data = dbFetch($result)) {
                $terminCount++;

                $id_termin = $data->id;
                echo "<tr>";
                echo "<td><input type='text' value='" . htmlspecialchars($data->datum, ENT_QUOTES, 'UTF-8') . "' name='datum[" . $id_termin . "]'></td>";
                echo "<td><input type='text' value='" . htmlspecialchars($data->anfang_zeit, ENT_QUOTES, 'UTF-8') . "' name='anfang_zeit[" . $id_termin . "]'></td>";
                echo "<td>" . htmlspecialchars($data->ende_zeit, ENT_QUOTES, 'UTF-8') . "</td>";
                echo "<td><input type='text' value='" . htmlspecialchars($data->name, ENT_QUOTES, 'UTF-8') . "' name='name[" . $id_termin . "]'></td>";
                echo "<td><input type='text' value='" . htmlspecialchars($data->telefon, ENT_QUOTES, 'UTF-8') . "' name='telefon[" . $id_termin . "]'></td>";
                echo "<td><input type='text' value='" . htmlspecialchars($data->email, ENT_QUOTES, 'UTF-8') . "' name='email[" . $id_termin . "]'></td>";
                echo "<td><input type='text' value='" . htmlspecialchars($data->bemerkung ?? '', ENT_QUOTES, 'UTF-8') . "' name='bemerkung[" . $id_termin . "]'></td>";
                echo "<td><button type='submit' name='delete' value='" . $id_termin . "'>X</button></td>";
                echo "<td><button type='submit' name='update' value='" . $id_termin . "'>Upd</button></td>";
                echo "</tr>";
            }
        }
        if ($terminCount === 0) {
            echo '<tr><td colspan="9">Keine Termine gefunden.</td></tr>';
        }   
}
?>
        </table>
        <?php if ($gefragtedatum !== '') {
        echo '<h3>Insgesamt ' . ($terminCount) . ' Termine wurden in diesem Zeitraum gebucht.</h3>';
        }?>
    </div>
</form>
    <?php require_once __DIR__ . "/includes/footer.inc.php"; ?>
<?php $conn->close(); ?>
