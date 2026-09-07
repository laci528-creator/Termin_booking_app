<?php

date_default_timezone_set('Europe/Vienna');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function dbConnect():mysqli {
	try {
		$conn_intern = new mysqli(DB["hostadresse"],DB["username"],DB["passwort"],DB["DBName"]);
		$conn_intern->set_charset("utf8mb4");
	}
	catch(Exception $e) {
		if(TESTBETRIEB) {
			ta($e);
			die("Fehler im Verbindungsaufbau");
		}
		else {
			header("Location: errors/dbconnect.html");
			exit;
		}
	}
	
	return $conn_intern;
}

function dbQuery(mysqli $conn_intern, string $sql_intern):mysqli_result|bool {
	try {
		$antwort_intern = $conn_intern->query($sql_intern);
	}
	catch(Exception $e) {
		if(TESTBETRIEB) {
			ta($e);
			die("Fehler in der Query");
		}
		else {
			header("Location: errors/dbquery.html");
			exit;
		}
	}
	
	return $antwort_intern;
}

function dbFetch(mysqli_result $antwort_intern):object|null {
		return $antwort_intern->fetch_object(); //fetch_array: gemischt-assoziatives Array | fetch_assoc: assoziatives Array
}

function pruefeAufLeer(mysqli $conn, string $in): string {
    $in = trim($in);
    if(strlen($in) > 0) {
        $out = "'" . $conn->real_escape_string($in) . "'";
    } else {
        $out = "NULL";
    }
    return $out;
}

