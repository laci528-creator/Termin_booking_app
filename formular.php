<?php

require("includes/config.inc.php");
require("includes/common.inc.php");
require("includes/db.inc.php");
require("includes/termin_functions.inc.php");

session_start();

$conn = dbConnect();

// daten von der index.php übernehmen, die der Benutzer ausgewählt hat	
$selecteddatum = $_SESSION['selecteddatum'] ?? '';
$selectedtermin = $_SESSION['selectedtermin'] ?? '';
$terminende= date('H:i:s', strtotime($selectedtermin) + 30 * 60) ?? ''; // 30 Minuten hinzufügen, um die Endzeit zu berechnen

$msg = '';

	$nachname = trim($_POST['NN'] ?? '');
	$telefon = trim($_POST['TN'] ?? '');
	$email = trim($_POST['E'] ?? '');
	$datum = trim($_POST['VN'] ?? '');
	$anfang_zeit = trim($_POST['ANF'] ?? '');
	$ende_zeit = trim($_POST['GD'] ?? '');
	$bemerkung = trim($_POST['T'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
// prüfen, ob die Werte leer sind; wenn ja, Fehlermeldung zurückgeben; wenn nein, in die Datenbank einfügen
	if(!empty($nachname) && !empty($telefon) && !empty($email) && !empty($datum) && !empty($anfang_zeit) && !empty($ende_zeit)) {
		$istGebucht = pruefeTermin($conn, $datum, $anfang_zeit);

		if ($istGebucht === false) {

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
					unset($_SESSION['selecteddatum'], $_SESSION['selectedtermin']); // Session-Variablen zurücksetzen
					header("Location: index.php?success=1");
					exit;
				} 
				else {
						$msg = '<p class="error">Fehler beim Buchen des Termins.</p>';
						$stmt->close();
				}
			} 
			else {
				$msg = '<p class="error">Fehler beim Speichern der Kundendaten.</p>';
				$stmt->close();
			}
		}
		else {
			$msg = '<p class="error">Der ausgewählte Termin ist bereits gebucht. Bitte wählen Sie einen anderen Termin.</p>';
		}
	} 
	else {
			$msg = '<p class="error">Bitte füllen Sie alle erforderlichen Felder aus.</p>';
	}	
}
$conn->close();
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
					Endzeit:
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