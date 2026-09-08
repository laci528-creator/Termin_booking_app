<?php 

function generiereTerminSlots(string $anfang_zeit, string $ende_zeit, int $intervall): array { 
    if ($intervall <= 0) {
        throw new InvalidArgumentException('die Intervallzeit muss größer als 0 sein.');
    }

    $start = new DateTime($anfang_zeit);
    $end = new DateTime($ende_zeit);
    $step = new DateInterval('PT' . $intervall . 'M');

    $termine = [];

    while ($start < $end) {
        $termine[] = $start->format('H:i:s');
        $start->add($step);
    }

    return $termine;
}

function holeOrdinationszeiten(
    mysqli $conn,
    int $wochentag
): array {

    $sql = "
        SELECT
            start_zeit,
            ende_zeit,
            slot_dauer
        FROM ordination_zeiten
        WHERE
            wochentag = ?
            AND aktiv = 1
        ORDER BY start_zeit
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $wochentag);
    $stmt->execute();

    $result = $stmt->get_result();

    $zeiten = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();

    return $zeiten;
}

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

	 if (
        !$startObjekt || 
        !$endeObjekt ||
        $startObjekt->format('H:i:s') !== $anfang_zeit ||
        $endeObjekt->format('H:i:s') !== $ende_zeit
     ) {
        return "Die Uhrzeit ist ungültig.";
    }

    if ($anfang_zeit >= $ende_zeit) {
        return "Die Anfangszeit muss vor der Endzeit liegen.";
    }

    $heute = new DateTime('today');
    $maxDatum = (clone $heute)->modify('+3 months');

    if ($datumObjekt > $maxDatum) {
    return "Termine können maximal drei Monate im Voraus gebucht werden.";
    }

    if ($datumObjekt < $heute) {
        return "Termine in der Vergangenheit sind nicht erlaubt.";
    }

    $wochentag = (int)$datumObjekt->format('N');

    if ($wochentag >= 6) {
        return "Am Wochenende können keine Termine gebucht werden.";
    }

    return null;
}

function findeTerminSlot(
    mysqli $conn,
    string $datum,
    string $anfang_zeit
): ?array {

    $datumObjekt = DateTime::createFromFormat('Y-m-d', $datum);

    if (!$datumObjekt || $datumObjekt->format('Y-m-d') !== $datum) {
        return null;
    }

    $zeitObjekt = DateTime::createFromFormat('H:i:s', $anfang_zeit);

    if (!$zeitObjekt || $zeitObjekt->format('H:i:s') !== $anfang_zeit) {
        return null;
    }

    $wochentag = (int)$datumObjekt->format('N');

    $sql = "
        SELECT
            start_zeit,
            ende_zeit,
            slot_dauer
        FROM ordination_zeiten
        WHERE
            wochentag = ?
            AND aktiv = 1
        ORDER BY start_zeit
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new RuntimeException(
            "SQL Fehler bei Ordinationszeiten: " . $conn->error
        );
    }

    $stmt->bind_param("i", $wochentag);
    $stmt->execute();

    $result = $stmt->get_result();

    while ($ordination = $result->fetch_assoc()) {

        $start = strtotime($ordination["start_zeit"]);
        $ende = strtotime($ordination["ende_zeit"]);
        $termin = strtotime($anfang_zeit);

        $slotDauer = (int)$ordination["slot_dauer"];
        if ($slotDauer <= 0) {
            continue;
        }

        $slotSekunden = $slotDauer * 60;

        if (
            $termin >= $start &&
            $termin < $ende &&
            ($termin - $start) % $slotSekunden === 0 &&
            $termin + $slotSekunden <= $ende
        ) {

            $stmt->close();

            return [
                "slot_dauer" => $slotDauer,
                "ende_zeit" => date(
                    "H:i:s",
                    $termin + $slotSekunden
                )
            ];
        }
    }

    $stmt->close();

    return null;
}



?>