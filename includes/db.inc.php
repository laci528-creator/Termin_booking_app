<?php
function dbConnect():mysqli {
	try {
		$conn_intern = new mysqli(DB["hostadresse"],DB["username"],DB["passwort"],DB["DBName"]);
	}
	catch(Exception $e) {
		if(TESTBETRIEB) {
			ta($e);
			die("Fehler im Verbindungsaufbau");
		}
		else {
			header("Location: errors/dbconnect.html");
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
		}
	}
	
	return $antwort_intern;
}

function dbFetch(mysqli_result $antwort_intern):Object|null {
	return $antwort_intern->fetch_object(); //fetch_array: gemischt-assoziatives Array | fetch_assoc: assoziatives Array
}

function pruefeAufLeer(string $in):string {
	if(strlen($in)>0) {
		$out = "'" . $in . "'";
	}
	else {
		$out = "NULL";
	}
	return $out;
}
?>