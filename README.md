# Termin Booking App

**This README is available in English and German.**
**Diese README ist auf Englisch und Deutsch verfügbar.**

* [English](#english)
* [Deutsch](#deutsch)

---

## English

### Overview

Termin Booking App is a small PHP and MySQL/MariaDB appointment booking application with a simple admin area.

The project was created for learning and portfolio purposes. It demonstrates basic backend concepts such as sessions, form handling, validation, prepared statements, transactions, and admin authentication.

### Features

* Calendar-based appointment selection
* Automatic generation of available time slots
* Booking form for customer data
* Server-side validation of appointment data
* Email validation
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

### Project Structure

Termin_booking_app/
├── index.php
├── formular.php
├── einloggen.php
├── admin.php
├── includes/
│   ├── config.inc.php
│   ├── common.inc.php
│   ├── db.inc.php
│   └── termin_functions.inc.php
├── css/
│   └── common.css
├── docs/
│   └── project_documentation.md
└── errors/

### Main Functionality

Users can select a date from the calendar, choose an available time slot, and complete the booking form.

After a successful booking, the application redirects back to the main page and displays a confirmation message with the booked date and start time.

The booking process uses prepared statements and a database transaction. If one part of the booking fails, the transaction is rolled back.

### Detailed Documentation

A more detailed project description is available here:

[Project Documentation](docs/project_documentation.md)

### Database

The application uses three main tables:

kunden
gespeicherte_termin
admin_users

To prevent double bookings, the appointment table should include a unique constraint:

ALTER TABLE gespeicherte_termin
ADD CONSTRAINT unique_termin UNIQUE (datum, anfang_zeit);

### Installation

1. Clone the repository:

git clone https://github.com/laci528-creator/Termin_booking_app.git

2. Move the project folder into the XAMPP `htdocs` directory.
3. Start Apache and MySQL.
4. Create the database and required tables.
5. Configure the database connection in `includes/config.inc.php`.
6. Open the project in the browser:

http://localhost/Termin_booking_app

### Possible Improvements

* Add a SQL installation file
* Add screenshots
* Improve the admin panel design
* Add email confirmation
* Add customer cancellation functionality
* Add Docker support
* Add a live demo

---

## Deutsch

### Überblick

Termin Booking App ist eine kleine Terminbuchungsanwendung mit PHP und MySQL/MariaDB und einem einfachen Adminbereich.

Das Projekt wurde zu Lern- und Portfoliozwecken erstellt. Es zeigt grundlegende Backend-Konzepte wie Sessions, Formularverarbeitung, Validierung, Prepared Statements, Transactions und Admin-Authentifizierung.

### Funktionen

* Terminauswahl über einen Kalender
* Automatische Generierung freier Zeitfenster
* Buchungsformular für Kundendaten
* Serverseitige Validierung der Termindaten
* E-Mail-Validierung
* Schutz vor Doppelbuchungen
* Datenbank-Transaction während der Buchung
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
* Transactions
* XAMPP
* Git / GitHub

### Projektstruktur

Termin_booking_app/
├── index.php
├── formular.php
├── einloggen.php
├── admin.php
├── includes/
│   ├── config.inc.php
│   ├── common.inc.php
│   ├── db.inc.php
│   └── termin_functions.inc.php
├── css/
│   └── common.css
├── docs/
│   └── project_documentation.md
└── errors/

### Hauptfunktionalität

Benutzer können im Kalender ein Datum auswählen, einen freien Termin anklicken und das Buchungsformular ausfüllen.

Nach erfolgreicher Buchung wird der Benutzer zur Startseite weitergeleitet und erhält eine Bestätigung mit Datum und Startzeit des Termins.

Der Buchungsvorgang verwendet Prepared Statements und eine Datenbank-Transaction. Wenn ein Teil der Buchung fehlschlägt, wird die Transaction zurückgerollt.

### Datenbank

Die Anwendung verwendet drei Haupttabellen:

kunden
gespeicherte_termin
admin_users

Um Doppelbuchungen zu verhindern, sollte die Termintabelle eine Unique Constraint enthalten:

ALTER TABLE gespeicherte_termin
ADD CONSTRAINT unique_termin UNIQUE (datum, anfang_zeit);

### Installation

1. Repository klonen:

git clone https://github.com/laci528-creator/Termin_booking_app.git

2. Projektordner in den XAMPP-Ordner `htdocs` verschieben.
3. Apache und MySQL starten.
4. Datenbank und benötigte Tabellen erstellen.
5. Datenbankverbindung in `includes/config.inc.php` anpassen.
6. Projekt im Browser öffnen:

http://localhost/Termin_booking_app

### Mögliche Verbesserungen

* SQL-Installationsdatei hinzufügen
* Screenshots ergänzen
* Adminbereich optisch verbessern
* E-Mail-Bestätigung hinzufügen
* Stornofunktion für Kunden ergänzen
* Docker-Unterstützung hinzufügen
* Live-Demo bereitstellen
