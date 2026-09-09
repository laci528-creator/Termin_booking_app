# Appointment Booking App

**This README is available in English and German.**
**Diese README ist auf Englisch und Deutsch verfügbar.**

* [English](#english)
* [Deutsch](#deutsch)

---

## English

### Overview

Appointment Booking App is a small full-stack web application built with PHP and MySQL/MariaDB.

Users can select available appointment slots from a calendar and submit their booking data through a validated form. Administrators can log in to a protected admin area to view, edit, and delete bookings.

The project was created for learning and portfolio purposes. It demonstrates backend concepts such as form handling, server-side validation, PHP sessions, prepared statements, database transactions, CSRF protection, authentication, email notifications, and database constraints.

### Features

* Calendar-based appointment selection
* Database-managed ordination hours
* Automatic generation of available appointment slots
* Configurable appointment slot duration
* Booking form for customer data
* Server-side validation of customer and appointment data
* Email address validation
* Validation of selected appointments against configured ordination hours
* Booking limited to the allowed future booking period
* Protection against double bookings
* Database-level UNIQUE constraint for appointment slots
* Database transaction during the booking process
* CSRF protection for sensitive forms
* Success message after a successful booking
* Email confirmation after successful booking
* Admin login with hashed password verification
* Session-based admin authentication
* Protected admin area for viewing, editing, and deleting bookings

### Technologies

* PHP
* MySQL / MariaDB
* HTML5
* CSS3
* PHPMailer
* Composer
* PHP Sessions
* Prepared Statements
* Database Transactions
* XAMPP
* Git / GitHub

### Main Functionality

Users can select an available appointment from the calendar and submit their customer information through a validated booking form.

The selected appointment is validated on the server against the configured ordination hours before it is stored in the database.

The booking process uses prepared statements and a database transaction. If one part of the booking process fails, the transaction is rolled back.

A database-level UNIQUE constraint provides an additional layer of protection against duplicate bookings.

After a successful booking, the application redirects the user back to the main page and displays a confirmation message containing the booked date and start time.

The application also sends an email confirmation using PHPMailer.

Administrators can access a protected admin area where existing appointments can be viewed, edited, and deleted.

### Database

The application uses four main tables:

* `kunden` – stores customer data
* `gespeicherte_termin` – stores booked appointments
* `admin_users` – stores administrator accounts
* `ordination_zeiten` – stores configurable ordination hours and appointment slot durations

To prevent duplicate bookings, the appointment table contains a UNIQUE constraint:

```sql
ALTER TABLE gespeicherte_termin
ADD CONSTRAINT unique_termin UNIQUE (datum, anfang_zeit);
```

### Installation

#### 1. Clone the repository

```bash
git clone https://github.com/laci528-creator/Termin_booking_app.git
```

#### 2. Move the project

Move the project folder into the XAMPP `htdocs` directory.

For example:

```text
C:\xampp\htdocs\Termin_booking_app
```

#### 3. Install Composer dependencies

Open a terminal inside the project folder and run:

```bash
composer install
```

This installs the required PHP dependencies, including PHPMailer.

#### 4. Start the local server

Start:

* Apache
* MySQL

from the XAMPP Control Panel.

#### 5. Create the database

Create the required database and import:

```text
database.sql
```

#### 6. Import the default ordination hours

Import:

```text
seed.sql
```

This adds the default ordination hours and appointment slot configuration.

#### 7. Configure the database connection

Copy:

```text
includes/config.example.inc.php
```

to:

```text
includes/config.inc.php
```

Then enter your local database credentials.

Example:

```php
$dbHost = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "your_database_name";
```

Do not commit your local `config.inc.php` file containing private credentials.

#### 8. Configure email sending

Copy:

```text
includes/mail_config.example.inc.php
```

to:

```text
includes/mail_config.inc.php
```

Then enter your SMTP configuration.

The application uses PHPMailer to send booking confirmation emails.

Do not commit SMTP passwords or other private credentials to the repository.

#### 9. Create an administrator account

Administrator passwords are stored as secure password hashes.

You can generate a password hash with PHP:

```php
<?php

echo password_hash("your-password", PASSWORD_DEFAULT);
```

Copy the generated hash and insert the administrator into the `admin_users` table.

Example:

```sql
INSERT INTO admin_users (email, password_hash)
VALUES (
    'admin@example.com',
    'GENERATED_PASSWORD_HASH'
);
```

Replace the example email address and password hash with your own values.

Never store plain-text passwords in the database or repository.

#### 10. Open the application

Open:

```text
http://localhost/Termin_booking_app
```

in your browser.

### Current Status

The application is functional and includes the main features of a simple appointment booking system.

The current version supports appointment selection, booking validation, email confirmation, database-managed ordination hours, and administration of existing bookings.

### Live Demo

A live version of the application is available via the link in the repository description.

### Planned Improvements

* Add customer cancellation functionality
* Make ordination hours editable from the admin area
* Add support for holidays and exceptional opening hours
* Add automated tests

### What I Learned

During this project I practiced:

* working with PHP sessions
* building forms with server-side validation
* validating appointment data against database-managed business rules
* using prepared statements to reduce SQL injection risks
* handling database transactions
* implementing CSRF protection
* protecting an admin area with authentication
* storing and verifying hashed passwords
* preventing duplicate bookings with application logic and database constraints
* sending transactional emails with PHPMailer
* managing PHP dependencies with Composer
* structuring a small PHP project into reusable include files
* separating database, booking, validation, and mail-related logic into reusable functions

### Screenshots

#### Calendar View

![Calendar view](docs/screenshots/calendar-view.png)

#### Booking Form

![Booking form](docs/screenshots/booking-form.png)

#### Admin Area

![Admin area](docs/screenshots/admin-area.png)

### Further Documentation

For more details, see:

[Project Documentation](docs/project_documentation.md)

---

## Deutsch

### Überblick

Die Appointment Booking App ist eine kleine Full-Stack-Terminbuchungsanwendung, die mit PHP und MySQL/MariaDB entwickelt wurde.

Benutzer können über einen Kalender verfügbare Termine auswählen und ihre Buchungsdaten über ein validiertes Formular absenden. Administratoren können sich in einem geschützten Adminbereich anmelden, um Buchungen anzuzeigen, zu bearbeiten und zu löschen.

Das Projekt wurde zu Lern- und Portfoliozwecken erstellt. Es zeigt Backend-Konzepte wie Formularverarbeitung, serverseitige Validierung, PHP-Sessions, Prepared Statements, Datenbanktransaktionen, CSRF-Schutz, Authentifizierung, E-Mail-Benachrichtigungen und Datenbank-Constraints.

### Funktionen

* Terminauswahl über einen Kalender
* Datenbankgestützte Verwaltung der Ordinationszeiten
* Automatische Generierung verfügbarer Zeitfenster
* Konfigurierbare Termindauer
* Buchungsformular für Kundendaten
* Serverseitige Validierung der Kunden- und Termindaten
* Validierung der E-Mail-Adresse
* Prüfung des gewählten Termins anhand der hinterlegten Ordinationszeiten
* Begrenzung des buchbaren Zeitraums
* Schutz vor Doppelbuchungen
* Datenbankseitige UNIQUE-Constraint für Termine
* Datenbanktransaktion während des Buchungsvorgangs
* CSRF-Schutz für sensible Formulare
* Erfolgsmeldung nach erfolgreicher Buchung
* E-Mail-Bestätigung nach erfolgreicher Buchung
* Admin-Login mit Passwort-Hash-Prüfung
* Sessionbasierte Admin-Authentifizierung
* Geschützter Adminbereich zum Anzeigen, Bearbeiten und Löschen von Buchungen

### Technologien

* PHP
* MySQL / MariaDB
* HTML5
* CSS3
* PHPMailer
* Composer
* PHP Sessions
* Prepared Statements
* Datenbanktransaktionen
* XAMPP
* Git / GitHub

### Hauptfunktionalität

Benutzer können im Kalender einen verfügbaren Termin auswählen und anschließend ihre Kundendaten über ein validiertes Buchungsformular absenden.

Der ausgewählte Termin wird serverseitig anhand der in der Datenbank gespeicherten Ordinationszeiten überprüft, bevor die Buchung gespeichert wird.

Der Buchungsvorgang verwendet Prepared Statements und eine Datenbanktransaktion. Falls ein Teil des Buchungsvorgangs fehlschlägt, wird die Transaktion zurückgerollt.

Eine datenbankseitige UNIQUE-Constraint bietet zusätzlichen Schutz vor Doppelbuchungen.

Nach erfolgreicher Buchung wird der Benutzer zur Startseite zurückgeleitet und erhält eine Bestätigung mit Datum und Startzeit des gebuchten Termins.

Zusätzlich sendet die Anwendung über PHPMailer eine E-Mail-Bestätigung.

Administratoren können einen geschützten Adminbereich aufrufen, in dem vorhandene Termine angezeigt, bearbeitet und gelöscht werden können.

### Datenbank

Die Anwendung verwendet vier Haupttabellen:

* `kunden` – speichert Kundendaten
* `gespeicherte_termin` – speichert gebuchte Termine
* `admin_users` – speichert Administratorkonten
* `ordination_zeiten` – speichert konfigurierbare Ordinationszeiten und Terminintervalle

Um Doppelbuchungen zu verhindern, enthält die Termintabelle eine UNIQUE-Constraint:

```sql
ALTER TABLE gespeicherte_termin
ADD CONSTRAINT unique_termin UNIQUE (datum, anfang_zeit);
```

### Installation

#### 1. Repository klonen

```bash
git clone https://github.com/laci528-creator/Termin_booking_app.git
```

#### 2. Projekt verschieben

Den Projektordner in das XAMPP-Verzeichnis `htdocs` verschieben.

Zum Beispiel:

```text
C:\xampp\htdocs\Termin_booking_app
```

#### 3. Composer-Abhängigkeiten installieren

Ein Terminal im Projektordner öffnen und folgenden Befehl ausführen:

```bash
composer install
```

Dadurch werden die benötigten PHP-Abhängigkeiten einschließlich PHPMailer installiert.

#### 4. Lokalen Server starten

Im XAMPP Control Panel folgende Dienste starten:

* Apache
* MySQL

#### 5. Datenbank erstellen

Die benötigte Datenbank erstellen und folgende Datei importieren:

```text
database.sql
```

#### 6. Standard-Ordinationszeiten importieren

Folgende Datei importieren:

```text
seed.sql
```

Dadurch werden die standardmäßigen Ordinationszeiten und Terminintervalle hinzugefügt.

#### 7. Datenbankverbindung konfigurieren

Folgende Datei kopieren:

```text
includes/config.example.inc.php
```

und als:

```text
includes/config.inc.php
```

speichern.

Anschließend die lokalen Datenbankzugangsdaten eintragen.

Beispiel:

```php
$dbHost = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "your_database_name";
```

Die lokale `config.inc.php` mit privaten Zugangsdaten sollte nicht in das Repository committed werden.

#### 8. E-Mail-Versand konfigurieren

Folgende Datei kopieren:

```text
includes/mail_config.example.inc.php
```

und als:

```text
includes/mail_config.inc.php
```

speichern.

Anschließend die SMTP-Zugangsdaten eintragen.

Die Anwendung verwendet PHPMailer zum Versand der Buchungsbestätigungen.

SMTP-Passwörter und andere private Zugangsdaten dürfen nicht in das Repository committed werden.

#### 9. Administratorkonto erstellen

Administratorpasswörter werden als sichere Passwort-Hashes gespeichert.

Ein Passwort-Hash kann mit PHP erzeugt werden:

```php
<?php

echo password_hash("your-password", PASSWORD_DEFAULT);
```

Anschließend kann der erzeugte Hash zusammen mit der E-Mail-Adresse in die Tabelle `admin_users` eingefügt werden.

Beispiel:

```sql
INSERT INTO admin_users (email, password_hash)
VALUES (
    'admin@example.com',
    'GENERATED_PASSWORD_HASH'
);
```

Die Beispielwerte müssen durch eigene Werte ersetzt werden.

Passwörter sollten niemals im Klartext in der Datenbank oder im Repository gespeichert werden.

#### 10. Anwendung öffnen

Im Browser folgende Adresse öffnen:

```text
http://localhost/Termin_booking_app
```

### Aktueller Stand

Die Anwendung ist funktionsfähig und enthält die wichtigsten Funktionen eines einfachen Terminbuchungssystems.

Die aktuelle Version unterstützt die Terminauswahl, serverseitige Buchungsvalidierung, E-Mail-Bestätigungen, datenbankgestützte Ordinationszeiten und die Verwaltung bestehender Buchungen.

### Live Demo

Eine Live-Version der Anwendung ist über den Link in der Repository-Beschreibung verfügbar.

### Geplante Verbesserungen

* Stornierungsfunktion für Kunden hinzufügen
* Ordinationszeiten im Adminbereich bearbeitbar machen
* Unterstützung für Feiertage und Sonderöffnungszeiten hinzufügen
* Automatisierte Tests hinzufügen

### Was ich gelernt habe

Während dieses Projekts habe ich Folgendes geübt:

* Arbeiten mit PHP-Sessions
* Erstellen von Formularen mit serverseitiger Validierung
* Validierung von Terminen anhand datenbankgestützter Geschäftsregeln
* Verwendung von Prepared Statements zur Reduzierung von SQL-Injection-Risiken
* Umgang mit Datenbanktransaktionen
* Implementierung von CSRF-Schutz
* Schutz eines Adminbereichs durch Authentifizierung
* Speichern und Überprüfen gehashter Passwörter
* Vermeidung von Doppelbuchungen durch Programmlogik und Datenbank-Constraints
* Versand von transaktionalen E-Mails mit PHPMailer
* Verwaltung von PHP-Abhängigkeiten mit Composer
* Strukturierung eines kleinen PHP-Projekts mit wiederverwendbaren Include-Dateien
* Trennung von Datenbank-, Buchungs-, Validierungs- und E-Mail-Logik in wiederverwendbare Funktionen

### Screenshots

#### Kalenderansicht

![Kalenderansicht](docs/screenshots/calendar-view.png)

#### Buchungsformular

![Buchungsformular](docs/screenshots/booking-form.png)

#### Adminbereich

![Adminbereich](docs/screenshots/admin-area.png)

### Weitere Dokumentation

Weitere Details befinden sich hier:

[Projektdokumentation](docs/project_documentation.md)
