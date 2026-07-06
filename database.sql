-- Database structure for the Termin Booking App
-- Import this file into a MySQL/MariaDB database before running the application.

CREATE TABLE IF NOT EXISTS kunden (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    telefon VARCHAR(50),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS gespeicherte_termin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kunden_id INT NOT NULL,
    datum DATE NOT NULL,
    anfang_zeit TIME NOT NULL,
    ende_zeit TIME NOT NULL,
    bemerkung VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY unique_termin_slot (datum, anfang_zeit),

    FOREIGN KEY (kunden_id) REFERENCES kunden(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(100),
    role VARCHAR(50) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;