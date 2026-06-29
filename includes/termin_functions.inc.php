<?php 
function pruefeTermin(mysqli $conn, string $datum, string $anfang_zeit, ?int $excludeId = null): bool
{
    if ($excludeId === null) {
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
    } else {
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

        $stmt->bind_param("ssi", $datum, $anfang_zeit, $excludeId);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $istGebucht = $result->num_rows > 0;

    $stmt->close();

    return $istGebucht;
}

function validiereTermin(string $datum, string $anfang_zeit, string $ende_zeit): ?string {
	    if (empty($datum) || empty($anfang_zeit) || empty($ende_zeit)) {
        return "Bitte wählen Sie einen gültigen Termin aus.";
    }

	$datumObjekt = DateTime::createFromFormat('Y-m-d', $datum);

    if (!$datumObjekt || $datumObjekt->format('Y-m-d') !== $datum) {
        return "Das Datum ist ungültig.";
    }

	$startObjekt = DateTime::createFromFormat('H:i:s', $anfang_zeit);
    $endeObjekt = DateTime::createFromFormat('H:i:s', $ende_zeit);

	 if (!$startObjekt || !$endeObjekt) {
        return "Die Uhrzeit ist ungültig.";
    }

    if ($anfang_zeit >= $ende_zeit) {
        return "Die Anfangszeit muss vor der Endzeit liegen.";
    }

    $heute = new DateTime('today');

    if ($datumObjekt < $heute) {
        return "Termine in der Vergangenheit sind nicht erlaubt.";
    }

    $wochentag = (int)$datumObjekt->format('N');

    if ($wochentag >= 6) {
        return "Am Wochenende können keine Termine gebucht werden.";
    }

    return null;
}

?>