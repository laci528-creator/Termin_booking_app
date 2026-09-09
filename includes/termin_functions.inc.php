<?php 



function generiereTerminSlots(string $anfang_zeit, string $ende_zeit, int $intervall): array { 

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




function holeOrdinationszeiten(mysqli $conn, int $wochentag): array {
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

        $stmt->bind_param("ssi", $datum, $anfang_zeit, $excludeId);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $istGebucht = $result->num_rows > 0;

    $stmt->close();

    return $istGebucht;
}




function holeGebuchteTermine(mysqli $conn, string $datum): array {

    $sql = "
        SELECT anfang_zeit
        FROM gespeicherte_termin
        WHERE datum = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $datum);
    $stmt->execute();

    $result = $stmt->get_result();

    $termine = [];

    while ($row = $result->fetch_assoc()) {
        $termine[$row['anfang_zeit']] = true;
    }

    $stmt->close();

    return $termine;
}



function validiereTermin(string $datum, string $anfang_zeit, string $ende_zeit): ?string {

	$datumObjekt = DateTime::createFromFormat('!Y-m-d', $datum);

    if (!$datumObjekt || $datumObjekt->format('Y-m-d') !== $datum) {
        return "Das Datum ist ungültig.";
    }

	$startObjekt = DateTime::createFromFormat('!H:i:s', $anfang_zeit);
    $endeObjekt = DateTime::createFromFormat('!H:i:s', $ende_zeit);

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

    $terminDatumZeit = DateTimeImmutable::createFromFormat(
    '!Y-m-d H:i:s',
    "$datum $anfang_zeit"
    );

    if (!$terminDatumZeit) {
        return "Der Termin ist ungültig.";
    }

    $heute = new DateTime('today');
    $jetzt = new DateTimeImmutable();
    $maxDatum = (clone $heute)->modify('+3 months');

    if ($datumObjekt > $maxDatum) {
    return "Termine können maximal drei Monate im Voraus gebucht werden.";
    }

    if ($terminDatumZeit < $jetzt) {
        return "Termine in der Vergangenheit sind nicht erlaubt.";
    }

    $wochentag = (int)$datumObjekt->format('N');

    if ($wochentag >= 6) {
        return "Am Wochenende können keine Termine gebucht werden.";
    }

    return null;
}




function findeTerminSlot(mysqli $conn, string $datum, string $anfang_zeit): ?array {

    $datumObjekt = DateTime::createFromFormat('Y-m-d', $datum);

    if (!$datumObjekt || $datumObjekt->format('Y-m-d') !== $datum) {
        return null;
    }

    $zeitObjekt = DateTime::createFromFormat('H:i:s', $anfang_zeit);

    if (!$zeitObjekt || $zeitObjekt->format('H:i:s') !== $anfang_zeit) {
        return null;
    }

$wochentag = (int)$datumObjekt->format('N');

$ordinationZeiten = holeOrdinationszeiten(
    $conn,
    $wochentag
);

foreach ($ordinationZeiten as $ordination) {

    $start = strtotime($ordination["start_zeit"]);
    $ende = strtotime($ordination["ende_zeit"]);
    $termin = strtotime($anfang_zeit);

    $slotDauer = (int)$ordination["slot_dauer"];

    $slotSekunden = $slotDauer * 60;

    if (
        $termin >= $start &&
        $termin < $ende &&
        ($termin - $start) % $slotSekunden === 0 &&
        $termin + $slotSekunden <= $ende
    ) {
        return [
            "slot_dauer" => $slotDauer,
            "ende_zeit" => date(
                "H:i:s",
                $termin + $slotSekunden
            )
        ];
    }
}

return null;
}



function istTerminVergangen(string $datum, string $anfang_zeit): bool {

    $terminDatumZeit = DateTimeImmutable::createFromFormat(
        '!Y-m-d H:i:s',
        "$datum $anfang_zeit"
    );

    if (!$terminDatumZeit) {
        return false;
    }

    return $terminDatumZeit < new DateTimeImmutable();
}