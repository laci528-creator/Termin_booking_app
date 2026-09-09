<?php

function speichereTerminBuchung(
    mysqli $conn,
    string $name,
    string $telefon,
    string $email,
    string $datum,
    string $anfangZeit,
    string $endeZeit,
    string $bemerkung
): void {

    $conn->begin_transaction();

    try {
        $sqlKunde = "
            INSERT INTO kunden
                (name, telefon, email)
            VALUES
                (?, ?, ?)
        ";

        $stmt = $conn->prepare($sqlKunde);
        $stmt->bind_param(
            "sss",
            $name,
            $telefon,
            $email
        );
        $stmt->execute();

        $kundenId = $conn->insert_id;

        $stmt->close();

        $sqlTermin = "
            INSERT INTO gespeicherte_termin
                (
                    kunden_id,
                    datum,
                    anfang_zeit,
                    ende_zeit,
                    bemerkung
                )
            VALUES
                (?, ?, ?, ?, ?)
        ";

        $stmt = $conn->prepare($sqlTermin);
        $stmt->bind_param(
            "issss",
            $kundenId,
            $datum,
            $anfangZeit,
            $endeZeit,
            $bemerkung
        );
        $stmt->execute();

        $stmt->close();

        $conn->commit();

    } catch (Throwable $e) {
        $conn->rollback();
        throw $e;
    }
}