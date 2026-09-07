<?php
require_once __DIR__ . "/termin_functions.inc.php";

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


function updateTermin(    
            mysqli $conn,
            int $termin_id,
            string $datum,
            string $anfang_zeit,
            string $name,
            string $telefon,
            string $email,
            string $bemerkung
        ): string {

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