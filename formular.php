<?php

require("includes/config.inc.php");
require("includes/common.inc.php");
require("includes/db.inc.php");

function pruefeTermin($conn, string $datum, string $anfang_zeit): bool    
{
    $sql = "
        SELECT id
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
    $istGebucht = $result->num_rows > 0;
	
    $stmt->close();

    return $istGebucht;
}

$conn = dbConnect();

// daten von die index.php bekommen
$selecteddatum = $_GET['datum'] ?? '';
$selectedtermin = $_GET['termin'] ?? '';
$terminende= date('H:i:s', strtotime($selectedtermin) + 30 * 60);

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
// prüfen, ob die Werte leer sind; wenn ja, Fehlermeldung zurückgeben; wenn nein, in die Datenbank einfügen
	if(!empty($_POST['NN']) && !empty($_POST['TN']) && !empty($_POST['E']) && !empty($_POST['VN']) && !empty($_POST['ANF']) && !empty($_POST['GD'])) {
			$nachname = trim($_POST['NN']);
			$telefon = trim($_POST['TN']);
			$email = trim($_POST['E']);
			$datum = trim($_POST['VN']);
			$anfang_zeit = trim($_POST['ANF']);
			$ende_zeit = trim($_POST['GD']);
			$bemerkung = trim($_POST['T']);

			$terminStatus = pruefeTermin($conn, $datum, $anfang_zeit);
			if ($terminStatus === true) {
				$msg = "Der ausgewählte Termin ist bereits gebucht. Bitte wählen Sie einen anderen Termin.";
			} else {

				$sql_kunden = "INSERT INTO kunden (name, telefon, email) VALUES (?, ?, ?)";
				$stmt = $conn->prepare($sql_kunden);
				$stmt->bind_param("sss", $nachname, $telefon, $email);
				$stmt->execute();

				if ($stmt->affected_rows > 0) {
					$kunden_id = $conn->insert_id; // Letzte eingefügte ID abrufen
					$stmt->close();
					$sql_termin = "INSERT INTO gespeicherte_termin (kunden_id, datum, anfang_zeit, ende_zeit, bemerkung) 
											VALUES (?, ?, ?, ?, ?)";
					$stmt = $conn->prepare($sql_termin);
					$stmt->bind_param("issss", $kunden_id, $datum, $anfang_zeit, $ende_zeit, $bemerkung);
					$stmt->execute();
					if ($stmt->affected_rows > 0) {
						$stmt->close();
						header("Location: index.php?success=1");
						exit;
					} else {
						$msg = "Fehler beim Buchen des Termins.";
						$stmt->close();
					}
				} else {
					$msg = "Fehler beim Speichern der Kundendaten.";
					$stmt->close();
				}
			}

		} else {
			$msg = "Bitte füllen Sie alle erforderlichen Felder aus.";
	}	

}
?>

<!doctype html>
<html lang="de">
	<head>
		<title>DB: INSERT</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/dark.css">
	</head>
	<body>
        <h1>Terminbuchung</h1>
		<?php echo $msg; // Fehlermeldung anzeigen, falls vorhanden ?>
        <h2>Formular</h2>
		<form method="post">
			<fieldset>
				<legend>Personaldaten</legend>
                <label>
					Name:
					<input type="text" name="NN" required>
				</label>
                <label>
                    Telefonnummer:
                    <input type="tel" name="TN" required>
                </label>
                    <label>
                        Emailadresse:
					<input type="email" name="E" required>
				</label>
			</fieldset>
			<fieldset>
				<legend>Termindaten</legend>
				<label>
					Datum:
					<input type="text" name="VN" value="<?php echo htmlspecialchars($selecteddatum); ?>" readonly>
				</label>
				<label>
					Anfangszeit:
					<input type="text" name="ANF" value="<?php echo htmlspecialchars($selectedtermin); ?>" readonly>
				</label>
				<label>
					endezeit:
					<input type="text" name="GD" value="<?php echo htmlspecialchars($terminende); ?>" readonly>
				</label>
                <label>
                    Bemerkung für den Arzt:
                    <textarea name="T" rows="4" cols="50"></textarea>
                </label>
			</fieldset>
			<input type="submit" value="Termin buchen">
		</form>
        <h1>Zurück zur Indexseite</h1>
		<a href="index.php" class="button">Zurück zur Indexseite</a>
	</body>
</html>