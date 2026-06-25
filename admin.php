<?php

require("includes/config.inc.php");
require("includes/common.inc.php");
require("includes/db.inc.php");


$conn = dbConnect();
session_start();

$msg = '';
if (empty($_SESSION["eingeloggt"])) {
    header("Location: einloggen.php");
    exit;
}

if (count($_POST) > 0) {
	if (isset($_POST["btnLogout"])) {
		
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
}

function pruefeTermin($conn, string $datum, string $anfang_zeit, int $termin_id): bool {
    $sql = "
        SELECT id
        FROM gespeicherte_termin
        WHERE datum = ?
          AND anfang_zeit = ?
          AND id <> ?
        LIMIT 1
    ";
    $stmt = $conn->prepare($sql);

	if (!$stmt) {
    die("SQL Fehler bei Terminprüfung: " . $conn->error);
	}
    $stmt->bind_param("ssi", $datum, $anfang_zeit, $termin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $istGebucht = $result->num_rows > 0;
	
    $stmt->close();

    return $istGebucht;
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

if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['delete'])) {
            $termin_id = (int)$_POST['delete'];
            $sql = "DELETE FROM gespeicherte_termin
                    WHERE (
                    id = '" . $conn->real_escape_string($termin_id) . "'
                    )
                    ";
            $result = dbQuery($conn, $sql);
            if ($result) {
                $msg = "<p class='success'>Termin erfolgreich gelöscht.</p>";
            } else {
                $msg = "<p class='error'>Fehler beim Löschen des Termins: " . $conn->error . "</p>";
            }
    }
    elseif (isset($_POST['update'])) {
        $termin_id = (int)$_POST['update'];
        $datum = $_POST['datum'][$termin_id] ?? '';
        $anfang_zeit = $_POST['anfang_zeit'][$termin_id] ?? '';
        $ende_zeit = $_POST['ende_zeit'][$termin_id] ?? '';
        $name = $_POST['name'][$termin_id] ?? '';
        $telefon = $_POST['telefon'][$termin_id] ?? '';
        $email = $_POST['email'][$termin_id] ?? '';

        $terminStatus = pruefeTermin($conn, $datum, $anfang_zeit, $termin_id);
        if ($terminStatus === true) {
            $msg = "<p class='error'>Der Termin ist bereits gebucht. Bitte wählen Sie einen anderen Termin.</p>";
        } else {

        $sql = "UPDATE gespeicherte_termin
                JOIN kunden ON gespeicherte_termin.kunden_id = kunden.id
                SET gespeicherte_termin.datum = '" . $conn->real_escape_string($datum) . "',
                    gespeicherte_termin.anfang_zeit = '" . $conn->real_escape_string($anfang_zeit) . "',
                    gespeicherte_termin.ende_zeit = '" . $conn->real_escape_string($ende_zeit) . "',
                    kunden.name = '" . $conn->real_escape_string($name) . "',
                    kunden.telefon = '" . $conn->real_escape_string($telefon) . "',
                    kunden.email = '" . $conn->real_escape_string($email) . "'
                WHERE gespeicherte_termin.id = '" . $conn->real_escape_string($termin_id) . "'";

        $result = dbQuery($conn, $sql);
        if ($result) {
            $msg = "<p class='success'>Termin erfolgreich aktualisiert.</p>";
        } else {
            $msg = "<p class='error'>Fehler beim Aktualisieren des Termins: " . $conn->error . "</p>";
        }
    }
}
}

?>
<!doctype html>
<html lang="de">
	<head>
		<title>Gebuchte Termine</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/dark.css">
        <link rel="stylesheet" href="css/common.css">
        <style>

            .table-wrapper {
                width: 1400px;
                overflow-x: auto;
            }

            table {
                min-width: 1100px;
                border-collapse: collapse;
            }

            td input {
                width: 100%;
                box-sizing: border-box;
            }
</style>
            
	</head>
	<body>
<h1>Gebuchte Termine</h1>
<?php echo $msg; ?>
<form method="post">
<div class="table-wrapper">
<table border="1" cellpadding="5" cellspacing="0">  
    <tr>
        <th>Datum</th>
        <th>Anfangszeit</th>
        <th>Endezeit</th>
        <th>Name</th>
        <th>Telefon</th>
        <th>Email</th>
        <th>Delete</th>
        <th>Update</th>
    </tr>   
<?php

$gefragtedatum = $_SESSION["date"] ?? '';
if ($gefragtedatum === '') {
    echo "<p>Kein Startdatum in der Sitzung gefunden.</p>";
    exit;
}

$alledate = zweiWochenDaten($gefragtedatum);


foreach($alledate as $datum) {
            $sql = "SELECT 
                        gespeicherte_termin.id,
                        gespeicherte_termin.kunden_id,
                        gespeicherte_termin.datum, 
                        gespeicherte_termin.anfang_zeit, 
                        gespeicherte_termin.ende_zeit,
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
                $id_termin = $data->id;
                $id_kunden = $data->kunden_id;
                echo "<tr>";
                echo "<td><input type='text' value='" . htmlspecialchars($data->datum, ENT_QUOTES, 'UTF-8') . "' name='datum[" . $id_termin . "]'></td>";
                echo "<td><input type='text' value='" . htmlspecialchars($data->anfang_zeit, ENT_QUOTES, 'UTF-8') . "' name='anfang_zeit[" . $id_termin . "]'></td>";
                echo "<td><input type='text' value='" . htmlspecialchars($data->ende_zeit, ENT_QUOTES, 'UTF-8') . "' name='ende_zeit[" . $id_termin . "]'></td>";
                echo "<td><input type='text' value='" . htmlspecialchars($data->name, ENT_QUOTES, 'UTF-8') . "' name='name[" . $id_termin . "]'></td>";
                echo "<td><input type='text' value='" . htmlspecialchars($data->telefon, ENT_QUOTES, 'UTF-8') . "' name='telefon[" . $id_termin . "]'></td>";
                echo "<td><input type='text' value='" . htmlspecialchars($data->email, ENT_QUOTES, 'UTF-8') . "' name='email[" . $id_termin . "]'></td>";
                echo "<td><button type='submit' name='delete' value='" . $id_termin . "'>X</button></td>";
                echo "<td><button type='submit' name='update' value='" . $id_termin . "'>Upd</button></td>";
                echo "</tr>";
            }
}
?>
</table>
</div>
</form>

<h1>Logout Button</h1>
    <form method="post">
		<input type="submit" value="Ausloggen" name="btnLogout">
	</form>

</body>
</html>
<?php $conn->close(); ?>
