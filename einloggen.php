
<?php
require("includes/config.inc.php");
require("includes/common.inc.php");
require("includes/db.inc.php");

//ta($_POST);
session_start();
$msg = '';

if (!empty($_SESSION["eingeloggt"])) {
    header("Location: admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	
		$conn = dbConnect();
		$email = trim($_POST["E"] ?? '');
		$pwd = trim($_POST["P"] ?? '');
		$datum = $_POST["D"] ?? '';

		if (empty($email) || empty($pwd) || empty($datum)) {
			$msg = '<p class="error">Bitte füllen Sie alle Felder aus.</p>';
		} else {
			$sql = "SELECT * FROM admin_users WHERE email = ? LIMIT 1";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("s", $email);
			$stmt->execute();
			$result = $stmt->get_result();

				if ($result->num_rows === 1) {
					$user = $result->fetch_assoc();

					if (password_verify($pwd, $user['password_hash'])) {
					$_SESSION["eingeloggt"] = true;
					$_SESSION["date"] = $datum;
					$_SESSION["admin_email"] = $user['email'];
					$_SESSION["admin_id"] = $user['id'];
					$_SESSION["admin_name"] = $user['name'];

					$stmt->close();
					$conn->close();

					header("Location: admin.php");
					exit;
				} else {
					$msg = '<p class="error">Leider waren die eingegebenen Daten nicht korrekt. Bitte versuchen Sie es erneut.</p>';	
				}
		} else {
			$msg = '<p class="error">Leider waren die eingegebenen Daten nicht korrekt. Bitte versuchen Sie es erneut.</p>';
		}
		$stmt->close();
		$conn->close();
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
			<input type="submit" value="Einloggen">
		</form>

        <h3>Hinweise zur Anmeldung</h3>
            <p>Die Anmeldung ist mit einem festen Benutzernamen  und Passwort geschützt. Bitte verwenden Sie die folgenden Anmeldedaten, um Zugriff auf die Admin-Seite zu erhalten:</p>
            <ul>
                <li><strong>Emailadresse: </strong>drjackbauer@beispielklinik.at</li>
                <li><strong>Passwort: </strong>test12345678</li>
            </ul>
                <p>Geben Sie diese Anmeldedaten in das Formular ein, um sich erfolgreich einzuloggen und Zugriff auf die geschützte Admin-Seite zu erhalten.</p>

		<h1>Zurück zur Indexseite</h1>
			<p>Wenn Sie zur Startseite zurückkehren möchten, klicken Sie bitte auf den folgenden Link:</p>
				<a href="index.php" class="button">Zurück zur Indexseite</a>
	</body>
</html>