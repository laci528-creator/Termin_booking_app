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
│   ├── config.inc.php
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

The application uses the following main tables:

```text
kunden
gespeicherte_termin
admin_users
```

### Recommended Database Constraint

To prevent double bookings on database level, the appointment table should include a unique constraint for date and start time:

```sql
ALTER TABLE gespeicherte_termin
ADD CONSTRAINT unique_termin UNIQUE (datum, anfang_zeit);
```

## Installation

1. Clone the repository:

```bash
git clone https://github.com/laci528-creator/Termin_booking_app.git
```

2. Move the project folder into the XAMPP `htdocs` directory.

3. Start Apache and MySQL in XAMPP.

4. Create a database, for example:

```sql
CREATE DATABASE terminvereinbarung_db;
```

5. Create the required tables:

```text
kunden
gespeicherte_termin
admin_users
```

6. Configure the database connection in:

```text
includes/config.inc.php
```

7. Open the application in the browser:

```text
http://localhost/Termin_booking_app
```

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

* Improve the design of the user interface
* Add a dedicated SQL installation file
* Add screenshots to the README
* Improve the admin panel layout
* Use prepared statements consistently in all admin queries
* Add more detailed form validation
* Add email confirmation for bookings
* Add cancellation functionality for customers
* Add a better configuration structure with `config.example.inc.php`
* Add Docker support
* Add a live demo

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
│   ├── config.inc.php
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

Die Anwendung verwendet hauptsächlich folgende Tabellen:

```text
kunden
gespeicherte_termin
admin_users
```

### Empfohlene Datenbank-Constraint

Um Doppelbuchungen auf Datenbankebene zu verhindern, sollte die Tabelle für gespeicherte Termine eine Unique Constraint für Datum und Startzeit enthalten:

```sql
ALTER TABLE gespeicherte_termin
ADD CONSTRAINT unique_termin UNIQUE (datum, anfang_zeit);
```

## Installation

1. Repository klonen:

```bash
git clone https://github.com/laci528-creator/Termin_booking_app.git
```

2. Den Projektordner in den XAMPP-Ordner `htdocs` verschieben.

3. Apache und MySQL in XAMPP starten.

4. Eine Datenbank erstellen, zum Beispiel:

```sql
CREATE DATABASE terminvereinbarung_db;
```

5. Die benötigten Tabellen erstellen:

```text
kunden
gespeicherte_termin
admin_users
```

6. Die Datenbankverbindung in folgender Datei anpassen:

```text
includes/config.inc.php
```

7. Die Anwendung im Browser öffnen:

```text
http://localhost/Termin_booking_app
```

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

* Benutzeroberfläche weiter verbessern
* Eigene SQL-Installationsdatei hinzufügen
* Screenshots in die README einfügen
* Adminbereich optisch und strukturell verbessern
* Prepared Statements konsequent in allen Admin-Abfragen verwenden
* Formularvalidierung weiter ausbauen
* E-Mail-Bestätigung für Buchungen hinzufügen
* Stornofunktion für Kunden ergänzen
* Bessere Konfigurationsstruktur mit `config.example.inc.php`
* Docker-Unterstützung hinzufügen
* Live-Demo bereitstellen

## Entwickler

**László Haraszti**

## Hinweis

Dieses Projekt wurde zu Lern- und Portfoliozwecken erstellt.
Es soll praktische PHP- und MySQL-Kenntnisse in einer kleinen Full-Stack-Webanwendung demonstrieren.
