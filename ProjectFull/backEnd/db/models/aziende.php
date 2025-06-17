<?php

require("../db/core/dbCore.php");
require("sediAziende.php");
require("offerte.php");
require("documentiAzienda.php");


class Aziende extends DataBaseCore{

    private $aziendaId;
    private $nome;
    private $descrizione;
    private $sitoWeb;
    private $emailContatto;
    private $telefonoContatto;
    private $partitaIva;
    private $ragioneSociale;
    private $logo;
    private $password;
    private $email;

    private $sediAzienda;
    private $offerte;
    private $documenti;

    // Getter per aziendaId
    public function getAziendaId() {
        return $this->aziendaId;
    }

    // Getter per nome
    public function getNome() {
        return $this->nome;
    }

    // Getter per descrizione
    public function getDescrizione() {
        return $this->descrizione;
    }

    // Getter per sitoWeb
    public function getSitoWeb() {
        return $this->sitoWeb;
    }

    // Getter per emailContatto
    public function getEmailContatto() {
        return $this->emailContatto;
    }

    // Getter per telefonoContatto
    public function getTelefonoContatto() {
        return $this->telefonoContatto;
    }

    // Getter per partitaIva
    public function getPartitaIva() {
        return $this->partitaIva;
    }

    // Getter per ragioneSociale
    public function getRagioneSociale() {
        return $this->ragioneSociale;
    }

    // Getter per logo
    public function getLogo() {
        return $this->logo;
    }

    // Getter per password
    public function getPassword() {
        return $this->password;
    }

    // Setter per password
    public function setPassword($passwd) {
        $this->password = password_hash($passwd, PASSWORD_DEFAULT);;
    }

    // Getter per email
    public function getEmail() {
        return $this->email;
    }

    public function getSediAzienda() {
        return $this->sediAzienda;
    }

    // Getter per documenti
    public function getDocumenti() {
        return $this->nome;
    }
    // Getter per offerte
    public function getOfferte() {
        return $this->offerte;
    }

    public function populateFromArray($data) {
        // Assegna i valori dell'array agli attributi corrispondenti
        $this->aziendaId = isset($data['azienda_id']) ? $data['azienda_id'] : $this->aziendaId;
        $this->nome = isset($data['nome']) ? $data['nome'] : $this->nome;
        $this->descrizione = isset($data['descrizione']) ? $data['descrizione'] : $this->descrizione;
        $this->sitoWeb = isset($data['sito_web']) ? $data['sito_web'] : $this->sitoWeb;
        $this->emailContatto = isset($data['email_contatto']) ? $data['email_contatto'] : $this->emailContatto;
        $this->telefonoContatto = isset($data['telefono_contatto']) ? $data['telefono_contatto'] : $this->telefonoContatto;
        $this->partitaIva = isset($data['partita_iva']) ? $data['partita_iva'] : $this->partitaIva;
        $this->ragioneSociale = isset($data['ragione_sociale']) ? $data['ragione_sociale'] : $this->ragioneSociale ;
        $this->logo = isset($data['logo']) ? $data['logo'] : $this->logo;
        $this->password = isset($data['password']) ? $data['password'] : $this->password;
        $this->email = isset($data['email']) ? $data['email'] : $this->email;
    }

    public function toArray() {
        return [
            'azienda_id' => $this->aziendaId,
            'nome' => $this->nome,
            'descrizione' => $this->descrizione,
            'sito_web' => $this->sitoWeb,
            'email_contatto' => $this->emailContatto,
            'telefono_contatto' => $this->telefonoContatto,
            'partita_iva' => $this->partitaIva,
            'ragione_sociale' => $this->ragioneSociale,
            'logo' => $this->logo,
            'password' => $this->password,
            'email' => $this->email
        ];
    }

    function addSedeAzienda($paese, $regione, $citta, $indirizzo) {
        global $conn, $isConnectedToDb;

        if (!$isConnectedToDb) {
            return 2;
        }

        // Prepara la query SQL
        $stmt = $conn->prepare("INSERT INTO sediAziende (azienda_id, paese, regione, citta, indirizzo) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $this->aziendaId, $paese, $regione, $citta, $indirizzo);

        // Esegui e controlla il risultato
        if ($stmt->execute()) {
            return 0; // Inserimento riuscito
        } else {
            return 1; // Errore (puoi loggare $stmt->error)
        }
    }

    function addAzienda($nome, $descrizione, $sitoWeb, $emailContatto, $telefonoContatto, $email, $password) {
        global $conn, $isConnectedToDb;

        if (!$isConnectedToDb) {
            return 2;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO utenti (nome, descrizione, sito_web, email_contatto, telefono_contatto, email, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss",$nome, $descrizione, $sitoWeb, $emailContatto, $telefonoContatto, $email, $passwordHash);

    
        if ($stmt->execute()) {
            return 0;
        } else {
            return 1;
        }
    }

    function setAziendaLogo($fileName){
        global $conn, $isConnectedToDb;

        if (!$isConnectedToDb) {
            return 2;
        }

        $stmt = $conn->prepare("UPDATE aziende SET logo = ? WHERE id = ?");
        $stmt->bind_param("si", $fileName, $this->aziendaId);

        if ($stmt->execute()) {
            return 0;
        } else {
            return 1;
        }

    }


    function getAziendaByEmail($email) {
        global $conn, $isConnectedToDb;

        if (!$isConnectedToDb) {
            return 2;
        }

        $query = "select * from aziende where email = '".$email."'";

        $result = $conn->query($query);

        if (!$result) {
            return 1; // oppure puoi restituire $conn->error per debugging
        }

        $result = $result->fetch_assoc();

        populateFromArray($result);

        return 0;

    }

    function getAziendaById($id) {
        global $conn, $isConnectedToDb;

        if (!$isConnectedToDb) {
            return 2;
        }

        $query = "select * from aziende where azienda_id = ".$id;

        $result = $conn->query($query);

        if (!$result) {
            return 1; // oppure puoi restituire $conn->error per debugging
        }

        $result = $result->fetch_assoc();

        populateFromArray($result);

        return 0;

    }


    function addDocumento($fileName){

        $documenti = new DocumentiAzienda();

        $result = $documenti->addDocumento($this->aziendaId, $fileName);

        return $result;

    }

    function deleteDocumento($id){

        $documenti = new DocumentiAzienda();

        $result = $documenti->deleteDocumento($id);

        return $result;

    }

    public function fetchSediAzienda(){

        global $conn, $isConnectedToDb;

        if (!$isConnectedToDb) {
            return 2;
        }

        $query = "select * from sediAziende where azienda_id = ".$this->aziendaId;

        $result = $conn->query($query);

        if (!$result) {
            return 1; // oppure puoi restituire $conn->error per debugging
        }

        if ($result->num_rows > 0) {
            // Elenco delle sedi da restituire
            $this->sediAzienda = [];
    
            // Itera su tutte le righe del risultato
            while ($row = $result->fetch_assoc()) {
                // Crea un oggetto SediAziende e popola con i dati
                $sede = new SediAziende();
                $sede->populateFromArray($row);  // Popola l'oggetto Sede
                // Aggiungi l'oggetto Sede all'array di sedi
                array_push($this->sediAzienda, $sede);
            }
    
            return 0; // Successo
        } else {
            return 3; // Nessuna sede trovata
        }

    }

    public function fetchOfferteAzienda(){

        global $conn, $isConnectedToDb;

        if (!$isConnectedToDb) {
            return 2;
        }

        $query = "select * from offerte where azienda_id = ".$this->aziendaId;

        $result = $conn->query($query);

        if (!$result) {
            return 1; // oppure puoi restituire $conn->error per debugging
        }

        if ($result->num_rows > 0) {
            // Elenco delle sedi da restituire
            $this->offerte = [];
    
            // Itera su tutte le righe del risultato
            while ($row = $result->fetch_assoc()) {
                // Crea un oggetto SediAziende e popola con i dati
                $offerta = new SediAziende();
                $offerta->populateFromArray($row);  // Popola l'oggetto Sede
                // Aggiungi l'oggetto Sede all'array di sedi
                array_push($this->offerte, $offerta);
            }
    
            return 0; // Successo
        } else {
            return 3; // Nessuna sede trovata
        }

    }

    public function fetchDocumentiAzienda(){

        if (!$this->isConnectedToDb) {
            return 2;
        }

        $query = "select * from documentiAzienda where utente_id = ".$this->aziendaId;

        $result = $this->conn->query($query);

        if (!$result) {
            return 1; // oppure puoi restituire $conn->error per debugging
        }

        if ($result->num_rows > 0) {
            // Elenco delle sedi da restituire
            $this->documenti = [];
    
            // Itera su tutte le righe del risultato
            while ($row = $result->fetch_assoc()) {
                // Crea un oggetto SediAziende e popola con i dati
                $documento = new DocumentiAzienda();
                $documento->populateFromArray($row);  // Popola l'oggetto Sede
                // Aggiungi l'oggetto Sede all'array di sedi
                array_push($this->documenti, $documento);
            }
    
            return 0; // Successo
        } else {
            return 3; // Nessuna sede trovata
        }

    }

    public function updateAzienda() {
        global $conn, $isConnectedToDb;
    
        if (!$isConnectedToDb) {
            return 2; // Connessione non attiva
        }
    
        $fields = [];
        $values = [];
        $types = "";
    
        if (!empty($this->nome)) {
            $fields[] = "nome = ?";
            $values[] = $this->nome;
            $types .= "s";
        }
    
        if (!empty($this->descrizione)) {
            $fields[] = "descrizione = ?";
            $values[] = $this->descrizione;
            $types .= "s";
        }
    
        if (!empty($this->sitoWeb)) {
            $fields[] = "sito_web = ?";
            $values[] = $this->sitoWeb;
            $types .= "s";
        }
    
        if (!empty($this->emailContatto)) {
            $fields[] = "email_contatto = ?";
            $values[] = $this->emailContatto;
            $types .= "s";
        }
    
        if (!empty($this->telefonoContatto)) {
            $fields[] = "telefono_contatto = ?";
            $values[] = $this->telefonoContatto;
            $types .= "s";
        }
    
        if (!empty($this->partitaIva)) {
            $fields[] = "partita_iva = ?";
            $values[] = $this->partitaIva;
            $types .= "s";
        }
    
        if (!empty($this->ragioneSociale)) {
            $fields[] = "ragione_sociale = ?";
            $values[] = $this->ragioneSociale;
            $types .= "s";
        }
    
        if (!empty($this->password)) {
            if (password_get_info($this->password)['algo'] === 0) {
                $this->password = password_hash($this->password, PASSWORD_DEFAULT);
            }
            $fields[] = "password = ?";
            $values[] = $this->password;
            $types .= "s";
        }
    
        if (empty($fields)) {
            return 3; // Nessun campo da aggiornare
        }
    
        $query = "UPDATE aziende SET " . implode(", ", $fields) . " WHERE azienda_id = ?";
        $stmt = $conn->prepare($query);
    
        if (!$stmt) {
            return 4; // Errore nella preparazione
        }
    
        $values[] = $this->aziendaId;
        $types .= "i";
    
        $stmt->bind_param($types, ...$values);
    
        if ($stmt->execute()) {
            return 0; // Successo
        } else {
            return 1; // Errore durante l'update
        }
    }
    

}

?>