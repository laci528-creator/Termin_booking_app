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
?>