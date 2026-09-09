
<?php
require_once __DIR__ . "/includes/config.inc.php";
require_once __DIR__ . "/includes/common.inc.php";
require_once __DIR__ . "/includes/db.inc.php";

//ta($_POST);
session_start();

$csrfToken = getCsrfToken();

$msg = '';

if (!empty($_SESSION["eingeloggt"])) {
    header("Location: admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

		$csrfTokenPost = $_POST['csrf_token'] ?? '';

		if (!validiereCsrfToken($csrfTokenPost)) {
			http_response_code(403);
			die("Ungültige Anfrage.");
		}
	
		$email = trim($_POST["E"] ?? '');
		$pwd = $_POST["P"] ?? '';

		if ($email !== '' && $pwd !== '') {

			$conn = dbConnect();
		
			$sql = "SELECT * FROM admin_users WHERE email = ? LIMIT 1";
			$stmt = $conn->prepare($sql);

			if ($stmt) {

			$stmt->bind_param("s", $email);
			$stmt->execute();
			$result = $stmt->get_result();

				if ($result->num_rows === 1) {
					$user = $result->fetch_assoc();

					if (password_verify($pwd, $user['password_hash'])) {

					session_regenerate_id(true);

					$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

					$_SESSION["eingeloggt"] = true;
					$_SESSION['date'] = date('Y-m-d');
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
		} else {
			error_log("Login SQL error: " . $conn->error);

			$msg = '<p class="error">Fehler bei der Datenbankabfrage.</p>';
		}
		$conn->close();
	} else {
		$msg = '<p class="error">Bitte füllen Sie alle Felder aus.</p>';
	}
}
?>
	<?php require_once __DIR__ . "/includes/header.inc.php"; ?>
			<?php echo $msg; ?>
        <h1>In den Adminbereich einloggen</h1>
		<section class="login-form-container">
		<form method="post">
			<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES,'UTF-8'); ?>">
			<label>
				E-Mail-Adresse:
				<input type="email" name="E" autocomplete="username" required>
			</label>
			<label>
				Passwort:
				<input type="password" name="P" autocomplete="current-password" required>
			</label>
            <br>
			<input type="submit" value="Einloggen">
		</form>
		</section>
		<section class="login-info">
        <h3>Hinweise zur Anmeldung</h3>
            <p>Die Anmeldung ist mit einer festen E-Mail-Adresse und einem Passwort geschützt.. Bitte verwenden Sie die folgenden Anmeldedaten, um Zugriff auf die Admin-Seite zu erhalten:</p>
            <ul>
                <li><strong>Emailadresse: </strong>drjackbauer@beispielklinik.at</li>
                <li><strong>Passwort: </strong>test12345678</li>
            </ul>
                <p>Geben Sie diese Anmeldedaten in das Formular ein, um sich erfolgreich einzuloggen und Zugriff auf die geschützte Admin-Seite zu erhalten.</p>
		</section>
    <?php require_once __DIR__ . "/includes/footer.inc.php"; ?>