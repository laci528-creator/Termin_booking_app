# Appointment Booking App


**This README is available in English and German.**
**Diese README ist auf Englisch und Deutsch verfügbar.**

* [English](#english)
* [Deutsch](#deutsch)

---

## English

### Overview

Appointment Booking App is a small full-stack web application built with PHP and MySQL/MariaDB. Users can select available appointment slots from a calendar, submit booking data through a validated form, while administrators can log in to a protected admin area to view, edit and delete bookings.

The project was created for learning and portfolio purposes. It demonstrates basic backend concepts such as sessions, form handling, validation, prepared statements, transactions, and admin authentication.

### Features

* Calendar-based appointment selection
* Database-managed ordination hours
* Automatic generation of available time slots
* Configurable appointment slot duration
* Booking form for customer data
* Server-side validation of appointment data and available slots
* Booking limited to the allowed future booking period
* Protection against double bookings
* Database transaction during booking
* Success message after successful booking
* Admin login with hashed password verification
* Protected admin area for viewing, editing, and deleting bookings

### Technologies

* PHP
* MySQL / MariaDB
* HTML
* CSS
* Sessions
* Prepared Statements
* Transactions
* XAMPP
* Git / GitHub


### Main Functionality

Users can select available appointment slots from a calendar and submit booking data through a validated form. Administrators can log in to a protected admin area to view, edit, and delete bookings.

After a successful booking, the application redirects back to the main page and displays a confirmation message with the booked date and start time.

The booking process uses prepared statements and a database transaction. If one part of the booking fails, the transaction is rolled back.


### Database

The application uses four main tables:

- `kunden` – stores customer data
- `gespeicherte_termin` – stores booked appointments
- `admin_users` – stores administrator accounts
- `ordination_zeiten` – stores configurable opening hours and appointment slot durations

To prevent double bookings, the appointment table includes a unique constraint:

ALTER TABLE gespeicherte_termin
ADD CONSTRAINT unique_termin UNIQUE (datum, anfang_zeit);

### Installation

1. Clone the repository:

git clone https://github.com/laci528-creator/Termin_booking_app.git

2. Move the project folder into the XAMPP `htdocs` directory.
3. Start Apache and MySQL.
4. Create the database and import database.sql.
5. Import `seed.sql` to add the default ordination hours.
6. Copy `includes/config.example.inc.php` to `includes/config.inc.php` 
    and enter your local database credentials.
7. Open the project in the browser:

http://localhost/Termin_booking_app

## Current Status

The application is functional and includes the main features of a simple appointment booking system.

### Live Demo

A live version of the application is available via the link in the repository description.

## Planned Improvements

* Add email confirmation after successful booking
* Add customer cancellation functionality
* Make ordination hours editable from the admin area
* Add support for holidays and exceptional opening hours
* Add Docker support

## What I Learned

During this project I practiced:

* working with PHP sessions
* building forms with server-side validation
* using prepared statements to prevent SQL injection
* handling database transactions
* protecting an admin area with login authentication
* preventing double bookings with application logic and a database constraint
* structuring a small PHP project into reusable include files

## Screenshots

### Calendar View
![Calendar view](docs/screenshots/calendar-view.png)

### Booking Form
![Booking form](docs/screenshots/booking-form.png)

### Admin Area
![Admin area](docs/screenshots/admin-area.png)

## Further Documentation

For more details, see:
[Project Documentation](docs/project_documentation.md)

---

## Deutsch


### Überblick

Termin Booking App ist eine kleine Terminbuchungsanwendung mit PHP und MySQL/MariaDB und einem einfachen Adminbereich.

Das Projekt wurde zu Lern- und Portfoliozwecken erstellt. Es zeigt grundlegende Backend-Konzepte wie Sessions, Formularverarbeitung, Validierung, vorbereitete SQL-Abfragen, Datenbanktransaktion und Admin-Authentifizierung.

### Funktionen

* Terminauswahl über einen Kalender
* Datenbankgestützte Verwaltung der Ordinationszeiten
* Automatische Generierung freier Zeitfenster
* Konfigurierbare Termindauer
* Buchungsformular für Kundendaten
* Serverseitige Prüfung der gewählten Termine anhand der Ordinationszeiten
* Begrenzung des buchbaren Zeitraums
* E-Mail-Validierung
* Schutz vor Doppelbuchungen
* Datenbanktransaktion während der Buchung
* Erfolgsmeldung nach erfolgreicher Buchung
* Admin-Login mit Passwort-Hash-Prüfung
* Geschützter Adminbereich zum Anzeigen, Bearbeiten und Löschen von Buchungen

### Technologien

* PHP
* MySQL / MariaDB
* HTML
* CSS
* Sessions
* Prepared Statements
* Datenbanktransaktion
* XAMPP
* Git / GitHub


### Hauptfunktionalität

Benutzer können im Kalender ein Datum auswählen, einen freien Termin anklicken und das Buchungsformular ausfüllen.

Nach erfolgreicher Buchung wird der Benutzer zur Startseite weitergeleitet und erhält eine Bestätigung mit Datum und Startzeit des Termins.

Der Buchungsvorgang verwendet Prepared Statements und eine Datenbanktransaktion. Falls ein Teil der Buchung fehlschlägt, wird die Transaktion zurückgerollt.

### Datenbank

Die Anwendung verwendet vier Haupttabellen:

* `kunden` – speichert Kundendaten
* `gespeicherte_termin` – speichert gebuchte Termine
* `admin_users` – speichert Administratorkonten
* `ordination_zeiten` – speichert konfigurierbare Ordinationszeiten und Terminintervalle

Um Doppelbuchungen zu verhindern, die Termintabelle eine Unique Constraint enthalten:

ALTER TABLE gespeicherte_termin
ADD CONSTRAINT unique_termin UNIQUE (datum, anfang_zeit);

### Installation

1. Repository klonen:

git clone https://github.com/laci528-creator/Termin_booking_app.git

2. Projektordner in den XAMPP-Ordner `htdocs` verschieben.
3. Apache und MySQL starten.
4. Datenbank erstellen und database.sql importieren.
5. Importieren Sie `seed.sql`, um die Standard-Ordinationszeiten hinzuzufügen.
6. Kopieren Sie `includes/config.example.inc.php` nach `includes/config.inc.php`
    und geben Sie Ihre lokalen Datenbankzugangsdaten ein.
7. Projekt im Browser öffnen:

http://localhost/Termin_booking_app


## Aktueller Stand

Die Anwendung ist funktionsfähig und enthält die wichtigsten Funktionen eines einfachen Terminbuchungssystems.

## Geplante Verbesserungen

* E-Mail-Bestätigung nach erfolgreicher Buchung hinzufügen
* Stornierungsfunktion für Kunden hinzufügen
* Öffnungszeiten im Adminbereich bearbeitbar machen
* Unterstützung für Feiertage und Sonderöffnungszeiten hinzufügen
* Docker-Unterstützung hinzufügen

## Was ich gelernt habe

Während dieses Projekts habe ich Folgendes geübt:

* Arbeiten mit PHP-Sessions
* Erstellen von Formularen mit serverseitiger Validierung
* Verwendung von Prepared Statements zur Vermeidung von SQL-Injection
* Umgang mit Datenbanktransaktionen
* Schutz eines Adminbereichs durch Login-Authentifizierung
* Vermeidung von Doppelbuchungen durch Programmlogik und eine Datenbank-Constraint
* Strukturierung eines kleinen PHP-Projekts in wiederverwendbare Include-Dateien

## Screenshots

### Kalenderansicht

![Kalenderansicht](docs/screenshots/calendar-view.png)

### Buchungsformular

![Buchungsformular](docs/screenshots/booking-form.png)

### Adminbereich

![Adminbereich](docs/screenshots/admin-area.png)

## Weitere Dokumentation

Weitere Details befinden sich hier:

[Projektdokumentation](docs/project_documentation.md)