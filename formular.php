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

$terminende = '';

if (!empty($selectedtermin)) {
    $timestamp = strtotime($selectedtermin);
	if ($timestamp !== false) {
        $terminende = date('H:i:s', $timestamp + 30 * 60);
    }
} // 30 Minuten hinzufügen, um die Endzeit zu berechnen

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$nachname = trim($_POST['NN'] ?? '');
	$telefon = trim($_POST['TN'] ?? '');
	$email = trim($_POST['E'] ?? '');
	$bemerkung = trim($_POST['T'] ?? '');
	$datum = $selecteddatum;
	$anfang_zeit = $selectedtermin;
	$ende_zeit = $terminende;
	
	// prüfen, ob die Werte leer sind; wenn ja, Fehlermeldung zurückgeben; wenn nein, in die Datenbank einfügen
	if(!empty($nachname) && !empty($telefon) && !empty($email) && !empty($datum) && !empty($anfang_zeit) && !empty($ende_zeit)) {
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		
			$fehler = validiereTermin($datum, $anfang_zeit, $ende_zeit);
			if ($fehler !== null) {
				$msg = '<p class="error">' . htmlspecialchars($fehler) . '</p>';
			}
			elseif (!pruefeTermin($conn, $datum, $anfang_zeit)) {
				$stmt = null;
				$conn->begin_transaction();

				try {
					$sql_kunden = "INSERT INTO kunden (name, telefon, email) VALUES (?, ?, ?)";
					$stmt = $conn->prepare($sql_kunden);
				
					if (!$stmt) {
						throw new Exception("SQL Fehler bei Kundendaten: " . $conn->error);
					}

					$stmt->bind_param("sss", $nachname, $telefon, $email);
					$stmt->execute();

					if ($stmt->affected_rows <= 0) {
						throw new Exception("Kundendaten konnten nicht gespeichert werden.");
					}
					$kunden_id = $conn->insert_id; // Letzte eingefügte ID abrufen
					$stmt->close();
					$stmt = null;
					$sql_termin = "INSERT INTO gespeicherte_termin (kunden_id, datum, anfang_zeit, ende_zeit, bemerkung) 
										VALUES (?, ?, ?, ?, ?)";
					$stmt = $conn->prepare($sql_termin);
					
					if (!$stmt) {
						throw new Exception("SQL Fehler beim Termin: " . $conn->error);
					}

					$stmt->bind_param("issss", $kunden_id, $datum, $anfang_zeit, $ende_zeit, $bemerkung);
					$stmt->execute();


					if ($stmt->affected_rows <= 0) {
						throw new Exception("Termin konnte nicht gespeichert werden.");
					}

					$stmt->close();
					$stmt = null;

					$conn->commit();

					$_SESSION['booking_success'] = [
					'datum' => $datum,
					'anfang_zeit' => $anfang_zeit
					];
					
					unset($_SESSION['selecteddatum'], $_SESSION['selectedtermin']); // Session-Variablen zurücksetzen
					header("Location: index.php");
					exit;

				} catch (Exception $e) {
					$conn->rollback();


					if ($stmt instanceof mysqli_stmt) {
            			$stmt->close();
        			}
					$msg = '<p class="error">Fehler beim Buchen des Termins.</p>';

				}

			}
			else {
				$msg = '<p class="error">Der ausgewählte Termin ist bereits gebucht. Bitte wählen Sie einen anderen Termin.</p>';
			}
		}
		else {
			$msg = '<p class="error">Bitte geben Sie eine gültige E-Mail-Adresse ein.</p>';
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