
<?php

require("includes/config.inc.php");
require("includes/common.inc.php");

//ta($_POST);
session_start();
$msg = '';

if (count($_POST) > 0) {
	if (isset($_POST["btnLogout"])) {
		
		$_SESSION = [];
        header("Location: index.php");  
        exit;

	}
}

if (!empty($_SESSION["eingeloggt"])) {
    header("Location: admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	
	$email_korrekt = "drjackbauer@beispielklinik.at";
	$pwd_korrekt = "test12345678";
	$datum = $_POST["D"] ?? '';

	if ($datum === '') {
    $msg = '<p class="error">Bitte wählen Sie ein Startdatum aus.</p>';
	}
	
	elseif (trim($_POST["E"]) == $email_korrekt && trim($_POST["P"]) == $pwd_korrekt) {
		
        $_SESSION["eingeloggt"] = true;
        $_SESSION["date"] = $datum;

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
        <h1>In den Adminbereich einloggen</h1>
		<form method="post">
			<label>
				Emailadresse:
				<input type="email" name="E" required>
			</label>
			<label>
				Passwort:
				<input type="password" name="P" required>
			</label>
            <br>
            <label>
                Startdatum für die nächsten zwei Wochen:
                <input type="date" name="D" required>
			</label>
			<input type="submit" value="einloggen">
		</form>

        <h3>Hinweise zur Anmeldung</h3>
            <p>Die Anmeldung ist mit einem festen Benutzernamen  und Passwort geschützt. Bitte verwenden Sie die folgenden Anmeldedaten, um Zugriff auf die Admin-Seite zu erhalten:</p>
            <ul>
                <li><strong>Emailadresse: </strong>drjackbauer@beispielklinik.at</li>
                <li><strong>Passwort: </strong>test12345678</li>
            </ul>
                <p>Geben Sie diese Anmeldedaten in das Formular ein, um sich erfolgreich einzuloggen und Zugriff auf die geschützte Admin-Seite zu erhalten.</p>

				<h1>Zurück zur Indexseite</h1>
		<form method="post">
			<input type="submit" value="Indexseite" name="btnLogout">
		</form>

	</body>
</html>