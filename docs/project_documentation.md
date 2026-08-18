# Termin Booking App

**This README is available in two languages: English and German.**
**Diese README ist in zwei Sprachen verfügbar: Englisch und Deutsch.**

* [English version](#english-version)
* [Deutsche Version](#deutsche-version)

---

# English Version

## Overview

Termin Booking App is a PHP and MySQL/MariaDB based appointment booking application with a simple admin area.
The project was created for learning and portfolio purposes and demonstrates basic full-stack web development with PHP, MySQL, sessions, form handling, validation, database operations, and protected admin functionality.

Users can select a date from a calendar, choose an available time slot, submit their personal data, and book an appointment. After a successful booking, a confirmation message with the selected appointment date and start time is displayed.

## Features

* Calendar-based date selection
* Display of available appointment slots
* Automatic generation of time slots
* Weekend and past dates are not bookable
* Booking limited to a defined future time range
* Booking form for customer data
* Server-side validation of appointment data
* Email validation
* Protection against double bookings
* Database-level unique constraint for appointments
* Prepared statements for booking and login
* Transaction handling during booking
* Rollback on booking errors
* Success message after a completed booking
* Admin login with password hashing and `password_verify()`
* Protected admin area
* Display of booked appointments
* Edit and delete functionality in the admin area
* Logout function
* Basic error handling
* Responsive table handling for the admin area

## Technologies Used

* PHP
* MySQL / MariaDB
* HTML
* CSS
* Sessions
* Prepared Statements
* Transactions
* phpMyAdmin
* XAMPP
* Git / GitHub

## Project Structure

```text
Termin_booking_app/
│
├── index.php
├── formular.php
├── einloggen.php
├── admin.php
├── README.md
│
├── includes/
│   ├── config.example.inc.php
│   ├── common.inc.php
│   ├── db.inc.php
│   └── termin_functions.inc.php
│
├── css/
│   └── common.css
│
├── errors/
│   └── error pages
│
└── public/
```

## Main Files

### `index.php`

The main page of the application.
It displays the calendar, available appointment slots, and the success message after a completed booking.

### `formular.php`

The booking form page.
It receives the selected appointment from the session, validates the input, checks whether the appointment is already booked, and stores the customer and appointment data in the database.

### `einloggen.php`

The admin login page.
It uses prepared statements and `password_verify()` to check the admin login data.

### `admin.php`

The protected admin area.
It displays booked appointments and provides functions for editing, deleting, and managing bookings.

### `includes/termin_functions.inc.php`

Contains helper functions for appointment validation and checking whether an appointment is already booked.

### `includes/db.inc.php`

Contains database helper functions for connecting to the database and executing queries.

## Database

The application uses four main tables:

- `kunden` – stores customer data
- `gespeicherte_termin` – stores booked appointments
- `admin_users` – stores administrator accounts
- `ordination_zeiten` – stores configurable ordination hours and appointment slot durations

The `gespeicherte_termin` table uses a unique constraint on `datum` and `anfang_zeit` to prevent duplicate bookings.

The `ordination_zeiten` table stores the weekday, start time, end time, slot duration and active status of each ordination period.

### Recommended Database Constraint

To prevent double bookings on database level, the appointment table should include a unique constraint for date and start time:

```sql
ALTER TABLE gespeicherte_termin
ADD CONSTRAINT unique_termin UNIQUE (datum, anfang_zeit);
```

## Installation

1. Clone the repository.
2. Move the project folder into the XAMPP `htdocs` directory.
3. Start Apache and MySQL.
4. Create a MySQL/MariaDB database.
5. Import `database.sql`.
6. Import `seed.sql` to add the default ordination hours.
7. Copy `includes/config.example.inc.php` to `includes/config.inc.php`.
8. Enter your local database credentials in `config.inc.php`.
9. Open the application in the browser.

## Example Workflow

1. The user selects a date from the calendar.
2. The application displays available time slots.
3. The user selects a free appointment.
4. The user fills out the booking form.
5. The application validates the data.
6. The application checks whether the appointment is still available.
7. The booking is saved using a database transaction.
8. A success message is displayed on the start page.

## Security and Validation

This project includes several basic security and validation measures:

* Prepared statements for database inserts and login
* Password verification with `password_verify()`
* Server-side validation of appointment data
* Email validation
* Session-based appointment selection
* Transaction handling for booking
* Rollback if saving the booking fails
* Unique database constraint against double bookings
* Basic CSRF protection in the admin area

## Current Status

The application is functional and includes the main features of a simple appointment booking system.
It is still a learning and portfolio project, but it already demonstrates several important backend concepts such as sessions, validation, database handling, prepared statements, transactions, and admin authentication.

## Possible Future Improvements

* Make ordination hours editable from the admin area
* Add email confirmation for bookings
* Add customer cancellation functionality
* Add support for holidays and exceptional opening hours
* Use prepared statements consistently in all admin queries
* Improve the admin panel layout
* Add Docker support

## Developer

**László Haraszti**

## Note

This project was created for learning and portfolio purposes.
It is intended to demonstrate practical PHP and MySQL skills in a small full-stack web application.

---

# Deutsche Version

## Überblick

Termin Booking App ist eine Terminbuchungsanwendung auf Basis von PHP und MySQL/MariaDB mit einem einfachen Adminbereich.
Das Projekt wurde zu Lern- und Portfoliozwecken erstellt und zeigt grundlegende Full-Stack-Webentwicklung mit PHP, MySQL, Sessions, Formularverarbeitung, Validierung, Datenbankoperationen und geschützter Admin-Funktionalität.

Benutzer können ein Datum im Kalender auswählen, einen freien Termin anklicken, ihre persönlichen Daten eingeben und den Termin buchen. Nach erfolgreicher Buchung wird eine Bestätigung mit Datum und Startzeit des gebuchten Termins angezeigt.

## Funktionen

* Datumsauswahl über einen Kalender
* Anzeige verfügbarer Termine
* Automatische Generierung von Zeitfenstern
* Wochenenden und vergangene Tage sind nicht buchbar
* Buchung nur innerhalb eines definierten zukünftigen Zeitraums
* Buchungsformular für Kundendaten
* Serverseitige Validierung der Termindaten
* E-Mail-Validierung
* Schutz vor Doppelbuchungen
* Datenbankseitige Unique Constraint für Termine
* Prepared Statements bei Buchung und Login
* Transaction Handling bei der Buchung
* Rollback bei Fehlern während der Buchung
* Erfolgsmeldung nach erfolgreicher Buchung
* Admin-Login mit Passwort-Hashing und `password_verify()`
* Geschützter Adminbereich
* Anzeige gebuchter Termine
* Bearbeiten und Löschen von Terminen im Adminbereich
* Logout-Funktion
* Einfache Fehlerbehandlung
* Verbesserte Tabellenanzeige im Adminbereich

## Verwendete Technologien

* PHP
* MySQL / MariaDB
* HTML
* CSS
* Sessions
* Prepared Statements
* Transactions
* phpMyAdmin
* XAMPP
* Git / GitHub

## Projektstruktur

```text
Termin_booking_app/
│
├── index.php
├── formular.php
├── einloggen.php
├── admin.php
├── README.md
│
├── includes/
│   ├── config.example.inc.php
│   ├── common.inc.php
│   ├── db.inc.php
│   └── termin_functions.inc.php
│
├── css/
│   └── common.css
│
├── errors/
│   └── Fehlerseiten
│
└── public/
```

## Wichtige Dateien

### `index.php`

Die Startseite der Anwendung.
Sie zeigt den Kalender, die verfügbaren Termine und die Erfolgsmeldung nach einer abgeschlossenen Buchung an.

### `formular.php`

Die Seite mit dem Buchungsformular.
Sie übernimmt den ausgewählten Termin aus der Session, validiert die Eingaben, prüft, ob der Termin bereits gebucht ist, und speichert die Kunden- und Termindaten in der Datenbank.

### `einloggen.php`

Die Login-Seite für den Adminbereich.
Sie verwendet Prepared Statements und `password_verify()`, um die Login-Daten zu prüfen.

### `admin.php`

Der geschützte Adminbereich.
Hier werden gebuchte Termine angezeigt. Außerdem können Termine bearbeitet und gelöscht werden.

### `includes/termin_functions.inc.php`

Enthält Hilfsfunktionen zur Terminvalidierung und zur Prüfung, ob ein Termin bereits gebucht ist.

### `includes/db.inc.php`

Enthält Hilfsfunktionen für die Datenbankverbindung und Datenbankabfragen.

## Datenbank

Die Anwendung verwendet vier Haupttabellen:

- `kunden` – speichert Kundendaten
- `gespeicherte_termin` – speichert gebuchte Termine
- `admin_users` – speichert Administratorkonten
- `ordination_zeiten` – speichert konfigurierbare Ordinationszeiten und Terminintervalle

Die Tabelle `gespeicherte_termin` verwendet eine Unique Constraint für `datum` und `anfang_zeit`, um Doppelbuchungen zu verhindern.

Die Tabelle `ordination_zeiten` speichert Wochentag, Startzeit, Endzeit, Termindauer und Aktivstatus der jeweiligen Ordinationszeit.

### Empfohlene Datenbank-Constraint

Um Doppelbuchungen auf Datenbankebene zu verhindern, sollte die Tabelle für gespeicherte Termine eine Unique Constraint für Datum und Startzeit enthalten:

```sql
ALTER TABLE gespeicherte_termin
ADD CONSTRAINT unique_termin UNIQUE (datum, anfang_zeit);
```

## Installation

1. Klonen Sie das Repository.
2. Verschieben Sie den Projektordner in das XAMPP-Verzeichnis `htdocs`.
3. Starten Sie Apache und MySQL.
4. Erstellen Sie eine MySQL-/MariaDB-Datenbank.
5. Importieren Sie `database.sql`.
6. Importieren Sie `seed.sql`, um die Standard-Ordinationszeiten hinzuzufügen.
7. Kopieren Sie `includes/config.example.inc.php` nach `includes/config.inc.php`.
8. Tragen Sie Ihre lokalen Datenbankzugangsdaten in `config.inc.php` ein.
9. Öffnen Sie die Anwendung im Browser.

## Beispielhafter Ablauf

1. Der Benutzer wählt ein Datum im Kalender aus.
2. Die Anwendung zeigt freie Zeitfenster an.
3. Der Benutzer wählt einen freien Termin.
4. Der Benutzer füllt das Buchungsformular aus.
5. Die Anwendung validiert die Eingaben.
6. Die Anwendung prüft, ob der Termin noch frei ist.
7. Die Buchung wird mit einer Datenbank-Transaction gespeichert.
8. Auf der Startseite erscheint eine Erfolgsmeldung.

## Sicherheit und Validierung

Das Projekt enthält mehrere grundlegende Sicherheits- und Validierungsmaßnahmen:

* Prepared Statements für Datenbankeinträge und Login
* Passwortprüfung mit `password_verify()`
* Serverseitige Validierung der Termindaten
* E-Mail-Validierung
* Sessionbasierte Terminübernahme
* Transaction Handling bei der Buchung
* Rollback, falls das Speichern fehlschlägt
* Unique Constraint gegen Doppelbuchungen
* Einfache CSRF-Absicherung im Adminbereich

## Aktueller Stand

Die Anwendung ist funktionsfähig und enthält die wichtigsten Funktionen eines einfachen Terminbuchungssystems.
Sie ist weiterhin ein Lern- und Portfolio-Projekt, zeigt aber bereits wichtige Backend-Konzepte wie Sessions, Validierung, Datenbankzugriffe, Prepared Statements, Transactions und Admin-Authentifizierung.

## Mögliche zukünftige Verbesserungen

* Ordinationszeiten im Adminbereich bearbeitbar machen
* E-Mail-Bestätigung für Buchungen hinzufügen
* Stornierungsfunktion für Kunden hinzufügen
* Unterstützung für Feiertage und Sonderöffnungszeiten hinzufügen
* Vorgefertigte Aussagen in allen Admin-Abfragen einheitlich verwenden
* Layout des Admin-Panels verbessern
* Docker-Unterstützung hinzufügen

## Entwickler

**László Haraszti**

## Hinweis

Dieses Projekt wurde zu Lern- und Portfoliozwecken erstellt.
Es soll praktische PHP- und MySQL-Kenntnisse in einer kleinen Full-Stack-Webanwendung demonstrieren.
