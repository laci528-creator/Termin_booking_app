# Termin Booking App

A simple PHP/MySQL-based appointment booking application with an admin panel.

## Features

- date selection from a calendar
- generation of available appointment slots
- booking form
- storing customer data in the database
- storing appointments in the database
- admin login
- listing bookings on the admin page
- logout function

## Technologies Used

- PHP
- MySQL / MariaDB
- phpMyAdmin
- HTML / CSS
- XAMPP
- Git / GitHub

## Files

- index.php – calendar and appointment selection
- formular.php – booking form
- einloggen.php – admin login
- admin.php – admin panel
- includes/ – configuration, database, and helper functions

## Database

The project currently uses the following tables:

- kunden
- gespeicherte_termin
- admin_users

## Current Status

The project already has a working basic version.  
Booking, admin login, booking list display, and logout functionality have already been implemented.

## Planned Improvements

- rewriting SQL queries using prepared statements
- storing passwords as hashes and using password_verify()
- full database-based admin user management
- stronger protection against double bookings
- deleting and editing bookings from the admin panel
- improved validation and error handling
- cleaner user interface
- further improvements to the README and project structure

## Developer

László Haraszti

## Note

This project was created for learning and portfolio purposes and is still under development.

------------------------------------------------------------------------------------------------------------------

# Termin Booking App

Eine einfache PHP/MySQL-basierte Terminbuchungsanwendung mit Adminbereich.

## Funktionen

- Datumsauswahl über einen Kalender
- Generierung verfügbarer Termine
- Buchungsformular
- Speicherung der Kundendaten in der Datenbank
- Speicherung der Termine in der Datenbank
- Admin-Login
- Anzeige der Buchungen im Adminbereich
- Logout-Funktion

## Verwendete Technologien

- PHP
- MySQL / MariaDB
- phpMyAdmin
- HTML / CSS
- XAMPP
- Git / GitHub

## Dateien

- index.php – Kalender und Terminauswahl
- formular.php – Buchungsformular
- einloggen.php – Admin-Login
- admin.php – Adminbereich
- includes/ – Konfiguration, Datenbank und Hilfsfunktionen

## Datenbank

Das Projekt verwendet derzeit die folgenden Tabellen:

- kunden
- gespeicherte_termin
- admin_users

## Aktueller Stand

Das Projekt verfügt bereits über eine funktionierende Basisversion.  
Die Terminbuchung, der Admin-Login, die Anzeige der Buchungen und die Logout-Funktion sind bereits umgesetzt.

## Geplante Weiterentwicklungen

- Umstellung der SQL-Abfragen auf Prepared Statements
- Speicherung von Passwörtern als Hashes und Verwendung von password_verify()
- vollständige datenbankgestützte Verwaltung der Admin-Benutzer
- stärkerer Schutz gegen Doppelbuchungen
- Löschen und Bearbeiten von Buchungen im Adminbereich
- verbesserte Validierung und Fehlerbehandlung
- übersichtlichere Benutzeroberfläche
- weitere Verbesserungen an README und Projektstruktur

## Entwickler

László Haraszti

## Hinweis

Dieses Projekt wurde zu Lern- und Portfoliozwecken erstellt und wird derzeit weiterentwickelt.

