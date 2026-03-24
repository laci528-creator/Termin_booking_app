
<?php

require("includes/config.inc.php");
require("includes/common.inc.php");

//ta($_POST);
$msg = "";
if(count($_POST)>0) {
	//es wurden Formulardaten an das Dokument (den Server) über einen Request übergeben
	
	$email_korrekt = "drjackbauer@beispielklinik.at";
	$pwd_korrekt = "test12345678";
	
	if($_POST["E"]==$email_korrekt && $_POST["P"]==$pwd_korrekt) {
		//die eingegebenen Daten waren korrekt --> Meldung an den User und Weiterleitung auf eine "geschützte Seite"
		$msg = '<p class="success">Vielen Dank - Sie werden Kürze weitergeleitet.</p>';
        session_start();
        $_SESSION["eingeloggt"] = true;
        $_SESSION["date"] = $_POST["D"];

        header("Location: admin.php");
        exit;
	}
	else {
		//die eingegebenen Daten waren nicht korrekt --> Fehlermeldung an den User; beachte: sagen Sie dem User NIE, WAS nicht korrekt war
		$msg = '<p class="error">Leider waren die eingegebenen Daten nicht korrekt. Bitte versuchen Sie es erneut.</p>';
	}
}
?>
<!doctype html>
<html lang="de">
	<head>
		<title>Login</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/dark.css">
	</head>
		<body>
		<?php echo $msg; ?>
        <h1>Admin seite Einloggen</h1>
		<form method="post">
			<label>
				Emailadresse:
				<input type="email" name="E">
			</label>
			<label>
				Passwort:
				<input type="password" name="P">
			</label>
            <br>
            <label>
                Gefragte Datum und nachste zwei Wochen:
                <input type="date" name="D">
			</label>
			<input type="submit" value="einloggen">
		</form>

        <h3>Hints und Tipps auf anmeldung</h3>
            <p>Die Anmeldung ist mit einem festen Benutzernamen  und Passwort geschützt. Bitte verwenden Sie die folgenden Anmeldedaten, um Zugriff auf die Admin-Seite zu erhalten:</p>
            <ul>
                <li><strong>Emailadresse: </strong>drjackbauer@beispielklinik.at</li>
                <li><strong>Passwort: </strong>test12345678</li>
            </ul>
                <p>Geben Sie diese Anmeldedaten in das Formular ein, um sich erfolgreich einzuloggen und Zugriff auf die geschützte Admin-Seite zu erhalten.</p>

	</body>
</html>