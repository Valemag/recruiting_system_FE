--dbms: mariaDB
--l'accesso al database è gestito in /backEnd/db/core/dbCoreFunctions.php

--aggiungere ad azienda partita iva e ragione sociale
--banner_profilo -> banner nello schema

CREATE USER 'user123'@'localhost' IDENTIFIED BY 'password123';

GRANT ALL PRIVILEGES ON *.* TO 'user123'@'localhost' WITH GRANT OPTION;

FLUSH PRIVILEGES;



create database bitByte;

use bitByte;

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
    competenza_id INT PRIMARY KEY,
    competenza VARCHAR(255) NOT NULL
);

CREATE TABLE modalitaLavoro (
    modalita_id INT PRIMARY KEY,
    modalita VARCHAR(255) NOT NULL
);


CREATE TABLE tipoContratti (
    tipo_contratto_id INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(100) NOT NULL
);

CREATE TABLE statiCandidature (
    stato_id INT PRIMARY KEY,
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

CREATE TABLE offerte (
    offerta_id INT AUTO_INCREMENT PRIMARY KEY,
    azienda_id INT NOT NULL,
    descrizione TEXT,
    titolo VARCHAR(255) NOT NULL,
    tipo_contratto_id INT NOT NULL,
    retribuzione TEXT NOT NULL,
    data_pubblicazione DATE,
    sede_lavoro_id INT,
    modalita_lavoro_id INT,
    data_scadenza DATE,
    FOREIGN KEY (azienda_id) REFERENCES aziende(azienda_id) ON DELETE CASCADE,
    FOREIGN KEY (tipo_contratto_id) REFERENCES tipoContratti(tipo_contratto_id),
    FOREIGN KEY (sede_lavoro_id) REFERENCES sediAziende(sede_id),
    FOREIGN KEY (modalita_lavoro_id) REFERENCES modalitaLavoro(modalita_id)
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
    offerta_appuntata_id INT AUTO_INCREMENT PRIMARY KEY,
    utente_id INT NOT NULL,
    offerta_id INT NOT NULL,
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
    ml.modalita AS modalita_lavoro,
    a.nome AS nome_azienda,
    GROUP_CONCAT(DISTINCT c.competenza SEPARATOR ', ') AS competenze_richieste
FROM offerte o
JOIN aziende a ON o.azienda_id = a.azienda_id
JOIN modalitaLavoro ml ON o.modalita_lavoro_id = ml.modalita_id
JOIN tipoContratti tc ON o.tipo_contratto_id = tc.tipo_contratto_id
LEFT JOIN requisitiCompetenzeOfferta rco ON o.offerta_id = rco.offerta_id
LEFT JOIN competenze c ON rco.competenza_id = c.competenza_id
GROUP BY o.offerta_id;


CREATE OR REPLACE VIEW vista_candidature_utente AS
SELECT 
    c.candidatura_id,
    c.utente_id,
    a.nome as nome_azienda,
    o.offerta_id,
    o.titolo AS titolo_offerta,
    s.stato AS stato_candidatura,
    c.data_candidatura,
    c.note,
    c.motivazione_risultato,
    tc.tipo AS tipo_contratto
FROM candidature c
JOIN utenti u ON c.utente_id = u.utente_id
JOIN offerte o ON c.offerta_id = o.offerta_id
JOIN aziende a on o.azienda_id = a.azienda_id
JOIN tipoContratti tc ON o.tipo_contratto_id = tc.tipo_contratto_id
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
    tc.tipo AS tipo_contratto,
    ml.modalita AS modalita_lavoro,
    a.nome AS nome_azienda,
    GROUP_CONCAT(c.competenza SEPARATOR ', ') AS competenze_richieste
FROM offerte o
JOIN aziende a ON o.azienda_id = a.azienda_id
JOIN modalitaLavoro ml ON o.modalita_lavoro_id = ml.modalita_id
JOIN tipoContratti tc ON o.tipo_contratto_id = tc.tipo_contratto_id
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
    tc.tipo AS tipo_contratto,
    ml.modalita AS modalita_lavoro,
    a.nome AS nome_azienda,
    GROUP_CONCAT(c.competenza SEPARATOR ', ') AS competenze_richieste
FROM offerte o
JOIN aziende a ON o.azienda_id = a.azienda_id
JOIN modalitaLavoro ml ON o.modalita_lavoro_id = ml.modalita_id
JOIN tipoContratti tc ON o.tipo_contratto_id = tc.tipo_contratto_id
LEFT JOIN requisitiCompetenzeOfferta r ON o.offerta_id = r.offerta_id
LEFT JOIN competenze c ON r.competenza_id = c.competenza_id
GROUP BY o.offerta_id;


CREATE OR REPLACE VIEW vista_offerte_appuntate_utente AS
SELECT 
    oa.offerta_appuntata_id,
    oa.utente_id,
    u.nome AS nome_utente,
    oa.offerta_id,
    o.titolo AS titolo_offerta,
    o.descrizione,
    o.data_pubblicazione,
    o.data_scadenza,
    o.retribuzione,
    tc.tipo AS tipo_contratto,
    ml.modalita AS modalita_lavoro,
    a.nome AS nome_azienda
FROM offerteAppuntate oa
JOIN utenti u ON oa.utente_id = u.utente_id
JOIN offerte o ON oa.offerta_id = o.offerta_id
JOIN tipoContratti tc ON o.tipo_contratto_id = tc.tipo_contratto_id
JOIN modalitaLavoro ml ON o.modalita_lavoro_id = ml.modalita_id
JOIN aziende a ON o.azienda_id = a.azienda_id;



--filler tabelle
INSERT INTO competenze (competenza_id, competenza) VALUES
(1, 'Java'),
(2, 'Python'),
(3, 'SQL'),
(4, 'Project Management'),
(5, 'React'),
(6, 'Machine Learning'),
(7, 'Linux'),
(8, 'Comunicazione efficace'),
(9, 'AWS'),
(10, 'Cybersecurity');


INSERT INTO tipoContratti (tipo) VALUES
('Tempo indeterminato'),
('Tempo determinato'),
('Stage'),
('Part-time'),
('Freelance'),
('Apprendistato'),
('Contratto a chiamata');


INSERT INTO statiCandidature (stato_id, stato) VALUES
(1, 'In attesa'),
(2, 'Accettato'),
(3, 'Rifiutato');

INSERT INTO modalitaLavoro (modalita_id, modalita) VALUES
(1, 'in sede'),
(2, 'smart working'),
(3, 'ibrido');

INSERT INTO utenti (email, password, username, nome, cognome, telefono_contatto, descrizione) VALUES ('admin@admin.com', '$2y$10$wnSAqXD0QKhhiPHagJY7a.EH8kgr4VLipZI11yZ6vVEA.qWa8V3Du', 'admin_user', 'Nome Admin', 'Cognome Admin', '1234567890', 'Descrizione dell admin');
                            --password: admin


--utenti per test password tutte uguali: utenteProva1234
INSERT INTO utenti (email, password, username, nome, cognome, telefono_contatto, descrizione) VALUES 
('mario.rossi@example.com', '$2y$10$8o4ZgbWClD8Odc.zqmYH0.u4E0BKcICf3U.c7sSJJQglQrvooJW12', 'mario_rossi', 'Mario', 'Rossi', '3312345678', 'Appassionato di tecnologia, in cerca di nuove opportunità professionali.'),
('giovanni.bianchi@example.com', '$2y$10$8o4ZgbWClD8Odc.zqmYH0.u4E0BKcICf3U.c7sSJJQglQrvooJW12', 'giovanni_bianchi', 'Giovanni', 'Bianchi', '3398765432', 'Esperto in sviluppo software e consulenze tecniche.'),
('lucia.verdi@example.com', '$2y$10$8o4ZgbWClD8Odc.zqmYH0.u4E0BKcICf3U.c7sSJJQglQrvooJW12', 'lucia_verdi', 'Lucia', 'Verdi', '3401234567', 'Sviluppatrice front-end con esperienza in React e Angular.');

--aziende per test password tutte uguali: utenteProva1234
INSERT INTO aziende (nome, descrizione, sito_web, email_contatto, telefono_contatto, partita_iva, ragione_sociale, logo, password, email) VALUES
('Tech Solutions Srl', 'Azienda leader nel settore tecnologico, specializzata in soluzioni software innovative.', 'https://www.techsolutions.com', 'contatti@techsolutions.com', '0234567890', 'IT12345678901', 'Tech Solutions Srl', 'tech_logo.png', '$2y$10$8o4ZgbWClD8Odc.zqmYH0.u4E0BKcICf3U.c7sSJJQglQrvooJW12', 'info@techsolutions.com'),
('Digital Innovators', 'Azienda che fornisce soluzioni digitali per l\'ottimizzazione dei processi aziendali.', 'https://www.digitalinnovators.com', 'support@digitalinnovators.com', '0222333445', 'IT98765432101', 'Digital Innovators Srl', 'digital_logo.png', '$2y$10$8o4ZgbWClD8Odc.zqmYH0.u4E0BKcICf3U.c7sSJJQglQrvooJW12', 'contact@digitalinnovators.com');

INSERT INTO offerte (azienda_id, descrizione, titolo, tipo_contratto_id, retribuzione, data_pubblicazione, sede_lavoro_id, modalita_lavoro_id, data_scadenza) VALUES
(1, 'Stiamo cercando un software developer con esperienza in Java e Python. Unisciti a un team innovativo!', 'Sviluppatore Software', 1, '€30.000/anno', '2025-06-25', NULL, 2, '2025-08-01'),
(2, 'Cerchiamo un esperto di marketing digitale per l\'espansione della nostra presenza online.', 'Marketing Manager', 2, '€25.000/anno', '2025-06-24', NULL, 1, '2025-07-30'),
(1, 'Posizione aperta per un ingegnere del software con esperienza in Machine Learning.', 'Ingegnere Software', 1, '€35.000/anno', '2025-06-23', NULL, 2, '2025-09-01');


INSERT INTO candidature (offerta_id, utente_id, note, stato_id, motivazione_risultato, cv_documento_id) VALUES
(1, 1, 'Sono entusiasta di questa posizione. Ho molta esperienza con Java e Python!', 2, 'In valutazione', NULL),
(2, 2, 'Ho esperienza nel settore del marketing digitale e sono interessato a contribuire al vostro successo online.', 1, 'Candidatura ricevuta', NULL),
(3, 3, 'Passionata di tecnologia e Machine Learning. Mi piacerebbe portare il mio contributo al vostro team.', 3, 'Offerta inviata', NULL);


INSERT INTO offerteAppuntate (utente_id, offerta_id) VALUES
(1, 1),
(2, 2),
(3, 3);

-- Offerta 1 (Sviluppatore Software)
INSERT INTO requisitiCompetenzeOfferta (competenza_id, offerta_id) VALUES
(1, 1), -- Java
(2, 1), -- Python
(3, 1); -- SQL

-- Offerta 2 (Marketing Manager)
INSERT INTO requisitiCompetenzeOfferta (competenza_id, offerta_id) VALUES
(4, 2), -- Project Management
(5, 2), -- React
(8, 2); -- Comunicazione efficace

-- Offerta 3 (Ingegnere Software)
INSERT INTO requisitiCompetenzeOfferta (competenza_id, offerta_id) VALUES
(6, 3), -- Machine Learning
(7, 3), -- Linux
(9, 3); -- AWS

-- Utente 1 (Mario Rossi)
INSERT INTO competenzaUtente (utente_id, competenza_id) VALUES
(2, 1), 
(2, 2), 
(2, 3); 

-- Utente 2 (Giovanni Bianchi)
INSERT INTO competenzaUtente (utente_id, competenza_id) VALUES
(3, 4), 
(3, 2), 
(3, 2); 

-- Utente 3 (Lucia Verdi)
INSERT INTO competenzaUtente (utente_id, competenza_id) VALUES
(4, 6), 
(4, 3), 
(4, 3); 

-- Sedi per Tech Solutions Srl (azienda_id = 1)
INSERT INTO sediAziende (azienda_id, paese, regione, citta, indirizzo) VALUES
(1, 'Italia', 'Lombardia', 'Milano', 'Via Monte Napoleone 10'),
(1, 'Italia', 'Lazio', 'Roma', 'Viale delle Milizie 20');

-- Sedi per Digital Innovators (azienda_id = 2)
INSERT INTO sediAziende (azienda_id, paese, regione, citta, indirizzo) VALUES
(2, 'Italia', 'Veneto', 'Venezia', 'Campo San Polo 5'),
(2, 'Italia', 'Emilia-Romagna', 'Bologna', 'Via Zamboni 33');
