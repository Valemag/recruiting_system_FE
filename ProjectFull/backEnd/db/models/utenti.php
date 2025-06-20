<?php


//correzioni su classe competenze, costruttore e connessione al db
require_once("core/dbCore.php");
require_once("candidature.php");
require_once("competenzeUtente.php");
require_once("documentiUtente.php");

class Utenti extends DataBaseCore{

    private $utenteId;
    private $email;
    private $password;
    private $username;
    private $nome;
    private $cognome;
    private $descrizione;
    private $telefonoContatto;
    private $dataRegistrazione;
    private $immagineProfilo;

    private $candidature;
    private $competenze;
    private $documenti;
    
    // Getter per utenteId
    public function getUtenteId() {
        return $this->utenteId;
    }

    // Getter per email
    public function getEmail() {
        return $this->email;
    }

    // Getter per password
    public function getPassword() {
        return $this->password;
    }

    // Getter per username
    public function getUsername() {
        return $this->username;
    }

    // Getter per nome
    public function getNome() {
        return $this->nome;
    }

    // Getter per cognome
    public function getCognome() {
        return $this->cognome;
    }

    // Getter per descrizione
    public function getDescrizione() {
        return $this->descrizione;
    }

    // Getter per telefonoContatto
    public function getTelefonoContatto() {
        return $this->telefonoContatto;
    }

    // Getter per dataRegistrazione
    public function getDataRegistrazione() {
        return $this->dataRegistrazione;
    }

    // Getter per immagineProfilo
    public function getImmagineProfilo() {
        return $this->immagineProfilo;
    }

    public function getCandidature() {
        return $this->candidature;
    }

    public function getCompetenze() {
        return $this->competenze;
    }

    public function getDocumenti() {
        return $this->documenti;
    }

    // Metodo per trasferire i dati dall'array associativo agli attributi
    public function populateFromArray($data) {
        $this->utenteId = isset($data['utente_id']) ? $data['utente_id'] : $this->utenteId;
        $this->email = isset($data['email']) ? $data['email'] : $this->email;
        $this->password = isset($data['password']) ? $data['password'] : $this->password;
        $this->username = isset($data['username']) ? $data['username'] : $this->username;
        $this->nome = isset($data['nome']) ? $data['nome'] : $this->nome;
        $this->cognome = isset($data['cognome']) ? $data['cognome'] : $this->cognome;
        $this->descrizione = isset($data['descrizione']) ? $data['descrizione'] : $this->descrizione;
        $this->telefonoContatto = isset($data['telefono_contatto']) ? $data['telefono_contatto'] : $this->telefonoContatto;
        $this->dataRegistrazione = isset($data['data_registrazione']) ? $data['data_registrazione'] : $this->dataRegistrazione;
        $this->immagineProfilo = isset($data['immagine_profilo']) ? $data['immagine_profilo'] : $this->immagineProfilo;
    }

    // Metodo che restituisce un array associativo con i dati dell'oggetto
    public function toArray() {
        return [
            'utente_id' => $this->utenteId,
            'email' => $this->email,
            'password' => $this->password,
            'username' => $this->username,
            'nome' => $this->nome,
            'cognome' => $this->cognome,
            'descrizione' => $this->descrizione,
            'telefono_contatto' => $this->telefonoContatto,
            'data_registrazione' => $this->dataRegistrazione,
            'immagine_profilo' => $this->immagineProfilo,
            'candidature' => $this->candidature,
            'competenze' => $this->competenze,
            'documenti' => $this->documenti,
        ];
    }


    function addUtente($email, $password, $username, $nome, $cognome, $descrizione, $nTelefono)
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
        $stmt = $this->conn->prepare("INSERT INTO utenti (
            email, password, username, nome, cognome, descrizione, telefono_contatto, immagine_profilo
        ) VALUES (?, ?, ?, ?, ?, ?, ?, '')");
    
        $stmt->bind_param("sssssss", $email, $passwordHash, $username, $nome, $cognome, $descrizione, $nTelefono);
    
        if ($stmt->execute()) {
            return 0; // OK
        } else {
            return -1; // errore
        }
    }
    

    function addDocumento($fileName){

        if (!$this->isConnectedToDb) {
            return 2;
        }

        $sql = "INSERT INTO documentiUtente (utente_id, documento) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);

        // Associa i parametri
        $stmt->bind_param('is', $this -> utenteId, $fileName);

        // Esegui l'inserimento
        if ($stmt->execute()) {
            return 0;
        } else {
            return 1; // inserimento fallito
        }

    }

    public function addCompetenzaUtente($competenzaId) {
        if (!$this->isConnectedToDb) {
            return 2; // Connessione non attiva
        }

        // Inserisce la relazione nella tabella competenzaUtente
        $stmt = $this->conn->prepare("INSERT INTO competenzaUtente (utente_id, competenza_id) VALUES (?, ?)");
        if (!$stmt) return 1; // Errore nella preparazione

        $stmt->bind_param("ii", $this -> utenteId, $competenzaId);

        if ($stmt->execute()) {
            return 0; // Inserimento riuscito
        } else {
            return 5; // Errore in esecuzione
        }
    }

    public function setUtenteProfileImage($fileName){

        if (!$this->isConnectedToDb) {
            return 2;
        }

            $stmt = $this->conn->prepare("UPDATE utenti SET immagine_profilo = ? WHERE utente_id = ?");
        $stmt->bind_param("si", $fileName, $this->utenteId);

        if ($stmt->execute()) {
            return 0;
        } else {
            return 1;
        }

    }

    public function getUtenteByEmail($email) {

        if (!$this->isConnectedToDb) {
            return 1;
        }

        $query = "select * from utenti where email = '".$email."'";

        $result = $this->conn->query($query);

        if (!$result) {
            return 2; // oppure puoi restituire $conn->error per debugging
        }

        $result = $result->fetch_assoc();

        $this->populateFromArray($result);

        return 0;
    }

    public function getUtenteById($id) {

        if (!$this->isConnectedToDb) {
            return 1;
        }

        $query = "select * from utenti where utente_id = '".$id."'";

        $result = $this->conn->query($query);

        if (!$result) {
            return 2; // oppure puoi restituire $conn->error per debugging
        }

        $result = $result->fetch_assoc();

        $this->populateFromArray($result);

        return 0;
    }

    public function fetchCandidatureUtente(){


        if (!$this->isConnectedToDb) {
            return 2;
        }

        $query = "select * from candidature where utente_id = ".$this->utenteId;

        $result = $this->conn->query($query);

        if (!$result) {
            return 1; // oppure puoi restituire $conn->error per debugging
        }

        if ($result->num_rows > 0) {
            // Elenco delle sedi da restituire
            $this->candidature = [];
    
            // Itera su tutte le righe del risultato
            while ($row = $result->fetch_assoc()) {
                // Crea un oggetto SediAziende e popola con i dati
                $candidatura = new SediAziende();
                $candidatura->populateFromArray($row);  // Popola l'oggetto Sede
                // Aggiungi l'oggetto Sede all'array di sedi
                array_push($this->candidature, $candidatura);
            }
    
            return 0; // Successo
        } else {
            return 3; // Nessuna sede trovata
        }

    }
    
    public function fetchCompetenzeUtente(){


        if (!$this->isConnectedToDb) {
            return 2;
        }

        $query = "SELECT u.utente_id as utente_id, c.competenza as competenza
                    FROM utenti u
                    JOIN competenzaUtente cu ON u.utente_id = cu.utente_id
                    JOIN competenze c ON cu.competenza_id = c.competenza_id
                    WHERE u.utente_id = ".$this->utenteId;

        $result = $this->conn->query($query);

        if (!$result) {
            return 1; // oppure puoi restituire $conn->error per debugging
        }

        if ($result->num_rows > 0) {
            // Elenco delle sedi da restituire
            $this->competenze = [];
    
            // Itera su tutte le righe del risultato
            while ($row = $result->fetch_assoc()) {
                // Crea un oggetto SediAziende e popola con i dati
                $competenza = new Competenze();
                $competenza->populateFromArray($row);  // Popola l'oggetto Sede
                // Aggiungi l'oggetto Sede all'array di sedi
                array_push($this->competenze, $competenza);
            }
    
            return 0; // Successo
        } else {
            return 3; // Nessuna sede trovata
        }

    }

    public function fetchDocumentiUtente(){

        if (!$this->isConnectedToDb) {
            return 2;
        }

        $query = "select * from documentiUtente where utente_id = ".$this->utenteId;

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
                $documento = new DocumentiUtente();
                $documento->populateFromArray($row);  // Popola l'oggetto Sede
                // Aggiungi l'oggetto Sede all'array di sedi
                array_push($this->documenti, $documento);
            }
    
            return 0; // Successo
        } else {
            return 3; // Nessuna sede trovata
        }

    }

    public function getCandidatureWithInfo() {
        if (!$this->isConnectedToDb) {
            return 3;
        }

        $stmt = $this->conn->prepare("
            SELECT * FROM vista_candidature_utente
            WHERE utente_id = ?
            ORDER BY data_candidatura DESC
        ");

        if (!$stmt) {
            return 2;
        }

        $stmt->bind_param("i", $this -> utenteId);
        $stmt->execute();

        $result = $stmt->get_result();
        if (!$result) {
            return 1;
        }

        $candidature = [];
        while ($row = $result->fetch_assoc()) {
            $candidature[] = $row;
        }

        return $candidature;
    }

    // Metodo per ottenere una singola candidatura per candidatura_id
    public function getCandidaturaWithInfoById($candidaturaId) {
        if (!$this->isConnectedToDb) {
            return 3;
        }

        $stmt = $this->conn->prepare("
            SELECT * FROM vista_candidature_utente
            WHERE candidatura_id = ?
            LIMIT 1
        ");

        if (!$stmt) {
            return 2;
        }

        $stmt->bind_param("i", $candidaturaId);
        $stmt->execute();

        $result = $stmt->get_result();
        if (!$result || $result->num_rows === 0) {
            return 1;
        }

        return $result->fetch_assoc();
    }

    public function setPassword($hashedPassword) {
        $this->password = $hashedPassword;
        return $this->updateUtente();
    }
    
    public function updateUtente() {

        if (!$this->isConnectedToDb) {
            return 2; // Connessione non attiva
        }
    
        $fields = [];
        $values = [];
        $types = "";
    
        if (!empty($this->email)) {
            $fields[] = "email = ?";
            $values[] = $this->email;
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
    
        if (!empty($this->username)) {
            $fields[] = "username = ?";
            $values[] = $this->username;
            $types .= "s";
        }
    
        if (!empty($this->nome)) {
            $fields[] = "nome = ?";
            $values[] = $this->nome;
            $types .= "s";
        }
    
        if (!empty($this->cognome)) {
            $fields[] = "cognome = ?";
            $values[] = $this->cognome;
            $types .= "s";
        }
    
        if (!empty($this->descrizione)) {
            $fields[] = "descrizione = ?";
            $values[] = $this->descrizione;
            $types .= "s";
        }
    
        if (!empty($this->telefonoContatto)) {
            $fields[] = "telefono_contatto = ?";
            $values[] = $this->telefonoContatto;
            $types .= "s";
        }
    
        if (empty($fields)) {
            return 3; // Nessun campo da aggiornare
        }
    
        $query = "UPDATE utenti SET " . implode(", ", $fields) . " WHERE utente_id = ?";
        $stmt = $this->conn->prepare($query);
    
        if (!$stmt) {
            return 4; // Errore nella preparazione
        }
    
        // Aggiungiamo utente_id alla fine per la clausola WHERE
        $values[] = $this->utenteId;
        $types .= "i";
    
        $stmt->bind_param($types, ...$values);
    
        if ($stmt->execute()) {
            return 0; // Successo
        } else {
            return 1; // Errore durante l'update
        }
    }

    public function getOfferteAppuntateByUtenteId($utenteId) {
        $query = "
            SELECT 
                o.offerta_id,
                o.titolo,
                o.descrizione,
                o.data_pubblicazione,
                o.data_scadenza,
                o.retribuzione,
                a.nome AS nome_azienda
            FROM offerteAppuntate oa
            JOIN offerte o ON oa.offerta_id = o.offerta_id
            JOIN aziende a ON o.azienda_id = a.azienda_id
            WHERE oa.utente_id = ?
            ORDER BY oa.data_appuntamento DESC
        ";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $utenteId);
    
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $offerte = [];
            while ($row = $result->fetch_assoc()) {
                $offerte[] = $row;
            }
            return $offerte;
        } else {
            return []; // oppure false se vuoi segnalare un errore
        }
    }
    
    public function appuntaOfferta($utenteId, $offertaId) {
        $query = "
            INSERT INTO offerteAppuntate (utente_id, offerta_id)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE data_appuntamento = CURRENT_TIMESTAMP
        ";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $utenteId, $offertaId);
    
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }    
    

}

?>