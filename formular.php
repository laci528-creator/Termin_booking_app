<?php

require("includes/config.inc.php");
require("includes/common.inc.php");
require("includes/db.inc.php");


$conn = dbConnect();




if(isset($_POST['NN'], $_POST['TN'], $_POST['E'], $_POST['VN'], $_POST['ANF'], $_POST['GD'], $_POST['T'])) {
    $nachname = pruefeAufLeer($_POST['NN']);
    $telefon = pruefeAufLeer($_POST['TN']);
    $email = pruefeAufLeer($_POST['E']);
    $datum = pruefeAufLeer($_POST['VN']);
    $anfang_zeit = pruefeAufLeer($_POST['ANF']);
    $ende_zeit = pruefeAufLeer($_POST['GD']);
    $bemerkung = pruefeAufLeer($_POST['T']);

    $sql_kunden = "INSERT INTO kunden (name, telefon, email) VALUES ($nachname, $telefon, $email)";
    
    if (dbQuery($conn, $sql_kunden)) {
        $kunden_id = $conn->insert_id; // Letzte eingefügte ID abrufen
        $sql_termin = "INSERT INTO gespeicherte_termin (kunden_id, datum, anfang_zeit, ende_zeit, bemerkung) 
                        VALUES ($kunden_id, $datum, $anfang_zeit, $ende_zeit, $bemerkung)";
        
        if (dbQuery($conn, $sql_termin)) {
            echo "Termin erfolgreich gebucht!";
        } else {
            echo "Fehler beim Buchen des Termins.";
        }
    } else {
        echo "Fehler beim Speichern der Kundendaten.";
    }
}



$selecteddatum = $_GET['datum'] ?? '';
$selectedtermin = $_GET['termin'] ?? '';
$terminende= date('H:i:s', strtotime($selectedtermin) + 30 * 60);


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
        <h2>Formular</h2>
		<form method="post">
			<fieldset>
				<legend>Pflichtangaben</legend>
                <label>
					Nachname:
					<input type="text" name="NN">
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
				<legend>Freiwillige Angaben</legend>
				<label>
					Datum:
					<input type="text" name="VN" value="<?php echo htmlspecialchars($selecteddatum); ?>">
				</label>
				<label>
					Anfangszeit:
					<input type="text" name="ANF" value="<?php echo htmlspecialchars($selectedtermin); ?>">
				</label>
				<label>
					endezeit:
					<input type="text" name="GD" value="<?php echo htmlspecialchars($terminende); ?>">
				</label>
                <label>
                    Termin_bemerkung:
                    <input type="text" name="T">    
                </label>
			</fieldset>
			<input type="submit" value="registrieren">
		</form>
	</body>
</html>