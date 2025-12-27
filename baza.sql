
CREATE DATABASE IF NOT EXISTS kupovina_ulaznica;
USE kupovina_ulaznica;

CREATE TABLE IF NOT EXISTS korisnici (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ime VARCHAR(100) NOT NULL,            
    email VARCHAR(255) NOT NULL UNIQUE,   
    lozinka VARCHAR(255) NOT NULL         
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS dogadjaji (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naziv VARCHAR(255) NOT NULL,          
    opis TEXT,                           
    datum DATETIME NOT NULL,             
    mesto VARCHAR(255) NOT NULL,          
    lokacija VARCHAR(255),                
    cena DECIMAL(10, 2) DEFAULT 0.00,    
    broj_karata INT DEFAULT 0,           
    dostupne_karata INT DEFAULT 0,        
    slika VARCHAR(255)                    
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ulaznice (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dogadjaj_id INT NOT NULL,            
    korisnik_id INT NOT NULL,            
    kolicina INT NOT NULL,            
    datum_kupovine TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dogadjaj_id) REFERENCES dogadjaji(id) ON DELETE CASCADE,
    FOREIGN KEY (korisnik_id) REFERENCES korisnici(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO korisnici (id, ime, email, lozinka) 
VALUES (1, 'Admin', 'admin@example.com', '$2y$10$8v8lT/V7B6e6f6z6z6z6zueO8G6G6G6G6G6G6G6G6G6G6G6G6G6');