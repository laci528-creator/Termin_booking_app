<?php
define("TESTBETRIEB",true); //definiert eine Konstante namens TESTBETRIEB und weist dieser den Wert true/false zu

define("DB",[
	"hostadresse" => "localhost",
	"username" => "root",
	"passwort" => "",
	"DBName" => "terminvereinbarung_db"
]);

/* Errors:
   - Production-Betrieb (während der Entwicklung):
     - alle Fehler reporten
	 - alle Fehler anzeigen
   - Live-Betrieb:
     - ausgewählte/alle Fehler reporten
	 - keine Fehler anzeigen (das würde den User ansonsten abschrecken)
*/
if(TESTBETRIEB) {
	error_reporting(E_ALL);
	ini_set("display_errors",1);
}
else {
	error_reporting(E_ALL);
	ini_set("display_errors",0);
}
?>