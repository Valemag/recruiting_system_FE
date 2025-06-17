--dbms: mariaDB
--l'accesso al database è gestito in /backEnd/db/core/dbCoreFunctions.php

--aggiungere ad azienda partita iva e ragione sociale
--banner_profilo -> banner nello schema

CREATE USER 'user123'@'localhost' IDENTIFIED BY 'password123';

GRANT ALL PRIVILEGES ON *.* TO 'user123'@'localhost' WITH GRANT OPTION;

FLUSH PRIVILEGES;



create database bitByte;

use bitByte

CREATE TABLE utenti (
    utente_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    username VARCHAR(100) UNIQUE,
    nome VARCHAR(100),
    cognome VARCHAR(100),
    descrizione TEXT,
    telefono_contatto VARCHAR(10),
    data_registrazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    immagine_profilo TEXT
);

CREATE TABLE aziende (
    azienda_id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descrizione TEXT,
    sito_web VARCHAR(255),
    email_contatto VARCHAR(255),
    telefono_contatto VARCHAR(10),
    partita_iva VARCHAR(11),
    ragione_sociale TEXT,
    logo TEXT,
    password VARCHAR(255),
    email VARCHAR(255) UNIQUE
);

CREATE TABLE sediAziende (
    sede_id INT AUTO_INCREMENT PRIMARY KEY,
    azienda_id INT NOT NULL,
    paese VARCHAR(100),
    regione VARCHAR(100),
    citta VARCHAR(100),
    indirizzo TEXT,
    FOREIGN KEY (azienda_id) REFERENCES aziende(azienda_id) ON DELETE CASCADE
);

CREATE TABLE competenze (
    competenza_id INT AUTO_INCREMENT PRIMARY KEY,
    competenza VARCHAR(255) NOT NULL
);

CREATE TABLE tipoContratti (
    tipo_contratto_id INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(100) NOT NULL
);

CREATE TABLE statiCandidature (
    stato_id INT AUTO_INCREMENT PRIMARY KEY,
    stato VARCHAR(100) NOT NULL
);


CREATE TABLE competenzaUtente (
    competenza_utente_id INT AUTO_INCREMENT PRIMARY KEY,
    utente_id INT NOT NULL,
    competenza_id INT NOT NULL,
    FOREIGN KEY (utente_id) REFERENCES utenti(utente_id) ON DELETE CASCADE,
    FOREIGN KEY (competenza_id) REFERENCES competenze(competenza_id) ON DELETE CASCADE
);

CREATE TABLE documentiUtente (
    documento_id INT AUTO_INCREMENT PRIMARY KEY,
    utente_id INT NOT NULL,
    documento TEXT NOT NULL,
    FOREIGN KEY (utente_id) REFERENCES utenti(utente_id) ON DELETE CASCADE
);

CREATE TABLE documentiAzienda (
    documento_id INT AUTO_INCREMENT PRIMARY KEY,
    azienda_id INT NOT NULL,
    documento TEXT NOT NULL,
    FOREIGN KEY (azienda_id) REFERENCES aziende(azienda_id) ON DELETE CASCADE
);

CREATE TABLE offerte (
    offerta_id INT AUTO_INCREMENT PRIMARY KEY,
    azienda_id INT NOT NULL,
    descrizione TEXT,
    titolo VARCHAR(255) NOT NULL,
    tipo_contratto_id INT NOT NULL,
    retribuzione DECIMAL(10, 2),
    data_pubblicazione DATE,
    sede_lavoro_id INT,
    data_scadenza DATE,
    FOREIGN KEY (azienda_id) REFERENCES aziende(azienda_id) ON DELETE CASCADE,
    FOREIGN KEY (tipo_contratto_id) REFERENCES tipoContratti(tipo_contratto_id),
    FOREIGN KEY (sede_lavoro_id) REFERENCES sediAziende(sede_id)
);

CREATE TABLE requisitiCompetenzeOfferta (
    requisito_id INT AUTO_INCREMENT PRIMARY KEY,
    competenza_id INT NOT NULL,
    offerta_id INT NOT NULL,
    FOREIGN KEY (competenza_id) REFERENCES competenze(competenza_id) ON DELETE CASCADE,
    FOREIGN KEY (offerta_id) REFERENCES offerte(offerta_id) ON DELETE CASCADE
);

CREATE TABLE candidature (
    candidatura_id INT AUTO_INCREMENT PRIMARY KEY,
    offerta_id INT NOT NULL,
    utente_id INT NOT NULL,
    note TEXT,
    data_candidatura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    stato_id INT NOT NULL,
    motivazione_risultato TEXT,
    cv_documento_id INT,
    FOREIGN KEY (offerta_id) REFERENCES offerte(offerta_id) ON DELETE CASCADE,
    FOREIGN KEY (utente_id) REFERENCES utenti(utente_id) ON DELETE CASCADE,
    FOREIGN KEY (cv_documento_id) REFERENCES documentiUtente(documento_id) ON DELETE CASCADE,
    FOREIGN KEY (stato_id) REFERENCES statiCandidature(stato_id)
);


CREATE TABLE offerteAppuntate (
    appuntamento_id INT AUTO_INCREMENT PRIMARY KEY,
    utente_id INT NOT NULL,
    offerta_id INT NOT NULL,
    data_appuntamento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utente_id) REFERENCES utenti(utente_id) ON DELETE CASCADE,
    FOREIGN KEY (offerta_id) REFERENCES offerte(offerta_id) ON DELETE CASCADE,
    UNIQUE (utente_id, offerta_id) -- evita duplicati
);



--viste

CREATE OR REPLACE VIEW vista_offerte_azienda AS
SELECT 
    o.offerta_id,
    o.titolo,
    o.descrizione,
    o.data_pubblicazione,
    o.data_scadenza,
    o.retribuzione,
    tc.tipo AS tipo_contratto,
    a.azienda_id,
    a.nome AS nome_azienda
FROM offerte o
JOIN aziende a ON o.azienda_id = a.azienda_id
JOIN tipiContratti tc ON o.tipo_contratto_id = tc.tipo_contratto_id;


CREATE OR REPLACE VIEW vista_candidature_utente AS
SELECT 
    c.candidatura_id,
    c.utente_id,
    u.nome AS nome_utente,
    o.offerta_id,
    o.titolo AS titolo_offerta,
    s.stato AS stato_candidatura,
    c.data_candidatura,
    c.note,
    c.motivazione_risultato
FROM candidature c
JOIN utenti u ON c.utente_id = u.utente_id
JOIN offerte o ON c.offerta_id = o.offerta_id
JOIN statiCandidature s ON c.stato_id = s.stato_id;


CREATE OR REPLACE VIEW vista_documenti_utente AS
SELECT 
    du.utente_id,
    u.nome AS nome_utente,
    du.documento_id,
    du.documento
FROM documentiUtente du
JOIN utenti u ON du.utente_id = u.utente_id;


CREATE OR REPLACE VIEW vista_candidature_offerta AS
SELECT 
    c.offerta_id,
    o.titolo AS titolo_offerta,
    c.utente_id,
    u.nome AS nome_utente,
    c.data_candidatura,
    s.stato,
    c.note,
    c.motivazione_risultato
FROM candidature c
JOIN utenti u ON c.utente_id = u.utente_id
JOIN offerte o ON c.offerta_id = o.offerta_id
JOIN statiCandidature s ON c.stato_id = s.stato_id;



CREATE OR REPLACE VIEW vista_ultime_offerte_con_requisiti AS
SELECT 
    o.offerta_id,
    o.titolo,
    o.descrizione,
    o.data_pubblicazione,
    o.data_scadenza,
    o.retribuzione,
    a.nome AS nome_azienda,
    GROUP_CONCAT(c.competenza SEPARATOR ', ') AS competenze_richieste
FROM offerte o
JOIN aziende a ON o.azienda_id = a.azienda_id
LEFT JOIN requisitiCompetenzeOfferta rco ON o.offerta_id = rco.offerta_id
LEFT JOIN competenze c ON rco.competenza_id = c.competenza_id
GROUP BY o.offerta_id
ORDER BY o.data_pubblicazione DESC
LIMIT 10;


CREATE OR REPLACE VIEW vista_offerta_con_requisiti AS
SELECT 
    o.offerta_id,
    o.titolo,
    o.descrizione,
    o.data_pubblicazione,
    o.data_scadenza,
    o.retribuzione,
    a.nome AS nome_azienda,
    GROUP_CONCAT(c.competenza SEPARATOR ', ') AS competenze_richieste
FROM offerte o
JOIN aziende a ON o.azienda_id = a.azienda_id
LEFT JOIN requisitiCompetenzeOfferta r ON o.offerta_id = r.offerta_id
LEFT JOIN competenze c ON r.competenza_id = c.competenza_id
GROUP BY o.offerta_id;


--filler tabelle
INSERT INTO competenze (competenza) VALUES
('Java'),
('Python'),
('SQL'),
('Project Management'),
('React'),
('Machine Learning'),
('Linux'),
('Comunicazione efficace'),
('AWS'),
('Cybersecurity');


INSERT INTO tipoContratti (tipo) VALUES
('Tempo indeterminato'),
('Tempo determinato'),
('Stage'),
('Part-time'),
('Freelance'),
('Apprendistato'),
('Contratto a chiamata');


INSERT INTO statiCandidature (stato) VALUES
('Candidatura ricevuta'),
('In valutazione'),
('Offerta inviata'),
('Accettato'),
('Rifiutato'),
('Rinunciato');

INSERT INTO utenti (email, password, username, nome, cognome, telefono_contatto, descrizione) VALUES ('admin@admin.com', '$2y$10$wnSAqXD0QKhhiPHagJY7a.EH8kgr4VLipZI11yZ6vVEA.qWa8V3Du', 'admin_user', 'Nome Admin', 'Cognome Admin', '1234567890', 'Descrizione dell admin');
                            --password: admin