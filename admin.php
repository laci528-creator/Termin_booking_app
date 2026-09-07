<?php

require("includes/config.inc.php");
require("includes/common.inc.php");
require("includes/db.inc.php");
require("includes/termin_functions.inc.php");


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

function zweiWochenDaten(string $startdatum): array {
    $daten = [];
    $datum = new DateTime($startdatum);

    for ($i = 0; $i < 14; $i++) {
        $daten[] = $datum->format('Y-m-d');
        $datum->modify('+1 day');
    }
    return $daten;
}

function logoutUser(): void {
            $_SESSION = [];
		
		if(ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(
				session_name(),
				'',
				time()-86400,
				$params["path"],
				$params["domain"],
				$params["secure"],
				$params["httponly"]

			);
		}
		
		session_destroy();
        header("Location: einloggen.php");  
        exit;
}

function deleteTermin($conn, int $termin_id): string {
        $sql = "DELETE FROM gespeicherte_termin WHERE id = $termin_id ";

            $result = dbQuery($conn, $sql);

            if ($result) {
                return "<p class='success'>Termin erfolgreich gelöscht.</p>";
            }

        return "<p class='error'>Fehler beim Löschen des Termins: " . $conn->error . "</p>";
}


function updateTermin($conn, int $termin_id): string {
        $datum = $_POST['datum'][$termin_id] ?? '';
        $anfang_zeit = $_POST['anfang_zeit'][$termin_id] ?? '';

        $name = $_POST['name'][$termin_id] ?? '';
        $telefon = $_POST['telefon'][$termin_id] ?? '';
        $email = $_POST['email'][$termin_id] ?? '';
        $bemerkung = $_POST['bemerkung'][$termin_id] ?? '';

        $terminSlot = findeTerminSlot(
            $conn,
            $datum,
            $anfang_zeit
        );

        if ($terminSlot === null) {
            return "<p class='error'>
                Der gewählte Termin liegt außerhalb der Ordinationszeiten
                oder ist kein gültiger Termin-Slot.
            </p>";
        }

        $ende_zeit = $terminSlot["ende_zeit"];

        $terminStatus = pruefeTermin($conn, $datum, $anfang_zeit, $termin_id);
        if ($terminStatus === true) {
            return "<p class='error'>Der Termin ist bereits gebucht. Bitte wählen Sie einen anderen Termin.</p>";
        }

        $sql = "UPDATE gespeicherte_termin
                JOIN kunden ON gespeicherte_termin.kunden_id = kunden.id
                SET gespeicherte_termin.datum = '" . $conn->real_escape_string($datum) . "',
                    gespeicherte_termin.anfang_zeit = '" . $conn->real_escape_string($anfang_zeit) . "',
                    gespeicherte_termin.ende_zeit = '" . $conn->real_escape_string($ende_zeit) . "',
                    gespeicherte_termin.bemerkung = '" . $conn->real_escape_string($bemerkung) . "',
                    kunden.name = '" . $conn->real_escape_string($name) . "',
                    kunden.telefon = '" . $conn->real_escape_string($telefon) . "',
                    kunden.email = '" . $conn->real_escape_string($email) . "'
                WHERE gespeicherte_termin.id = '" . $conn->real_escape_string($termin_id) . "'";

        $result = dbQuery($conn, $sql);
        if ($result) {
            return "<p class='success'>Termin erfolgreich aktualisiert.</p>";
        }
        return "<p class='error'>Fehler beim Aktualisieren des Termins: " . $conn->error . "</p>";
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
                $msg = updateTermin($conn, $termin_id);
            }
        }
}

$gefragtedatum = $_SESSION["date"] ?? '';
$terminCount = 0;

?>
<!doctype html>
<html lang="de">
	<head>
		<title>Gebuchte Termine</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/dark.css">
        <link rel="stylesheet" href="css/common.css">
        <style>
            body {
                max-width: 1400px;
            }
            .table-wrapper {
                width: 100%;
                max-width: 100%;
                overflow-x: auto;
            }
            table {
                width: 100%;
                min-width: 1100px;
                border-collapse: collapse;
            }
            td input {
                width: 150px;
                box-sizing: border-box;
            }
            td input[name^="name"] {
                width: 195px;
            }

            td input[name^="email"] {
                width: 210px;
            }
            th:nth-of-type(4) {
                width: 200px;
            }
            th:nth-of-type(6) {
                width: 215px;
            }
            th:nth-of-type(7), th:nth-of-type(8) {
                width: 90px;
            }
</style>
            
	</head>
	<body>
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

    <h1>Logout Button</h1>
        <form method="post">
            <input type="hidden" name="form_type" value="logout">
		    <button type="submit">Ausloggen</button>
</form>
    </body>
</html>
<?php $conn->close(); ?>
