<?php

define("TESTBETRIEB", false);

define("DB", [
    "hostadresse" => "localhost",
    "username" => "your_username",
    "passwort" => "your_password",
    "DBName" => "terminvereinbarung_db"
]);

if (TESTBETRIEB) {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
} else {
    error_reporting(E_ALL);
    ini_set("display_errors", 0);
}