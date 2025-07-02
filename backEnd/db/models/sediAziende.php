<?php

require_once("core/dbCore.php");

class SediAziende extends DataBaseCore{

    private $sedeId;
    private $aziendaId;
    private $paese;
    private $regione;
    private $citta;
    private $indirizzo;

    // Getter per sedeId
    public function getSedeId() {
        return $this->sedeId;
    }

    // Getter per aziendaId
    public function getAziendaId() {
        return $this->aziendaId;
    }

    // Getter per paese
    public function getPaese() {
        return $this->paese;
    }

    // Getter per regione
    public function getRegione() {
        return $this->regione;
    }

    // Getter per citta
    public function getCitta() {
        return $this->citta;
    }

    // Getter per indirizzo
    public function getIndirizzo() {
        return $this->indirizzo;
    }

    // Metodo per trasferire i dati dall'array associativo agli attributi
    public function populateFromArray($data) {
        $this->sedeId = isset($data['sede_id']) ? $data['sede_id'] : null;
        $this->aziendaId = isset($data['azienda_id']) ? $data['azienda_id'] : null;
        $this->paese = isset($data['paese']) ? $data['paese'] : null;
        $this->regione = isset($data['regione']) ? $data['regione'] : null;
        $this->citta = isset($data['citta']) ? $data['citta'] : null;
        $this->indirizzo = isset($data['indirizzo']) ? $data['indirizzo'] : null;
    }

    // Metodo che restituisce un array associativo con i dati dell'oggetto
    public function toArray() {
        return [
            'sede_id' => $this->sedeId,
            'azienda_id' => $this->aziendaId,
            'paese' => $this->paese,
            'regione' => $this->regione,
            'citta' => $this->citta,
            'indirizzo' => $this->indirizzo
        ];
    }

    public function updateSedeAziendaById($aziendaId, $paese, $regione, $citta, $indirizzo) {
        if (!$this->isConnectedToDb) return 2;
        
        $this->conn->begin_transaction();

        if ($this->getSedeAziendaById($aziendaId) !== 0) {
            $this->conn->rollback();
            return 1;
        }

        $fields = [];
        $values = [];
        $types = "";
    
        if (!empty($paese)) {
            $fields[] = "paese = ?";
            $values[] = $paese;
            $types .= "s";
        }

        if (!empty($regione)) {
            $fields[] = "regione = ?";
            $values[] = $regione;
            $types .= "s";
        }

        if (!empty($citta)) {
            $fields[] = "citta = ?";
            $values[] = $citta;
            $types .= "s";
        }

        if (!empty($indirizzo)) {
            $fields[] = "indirizzo = ?";
            $values[] = $indirizzo;
            $types .= "s";
        }

        if (empty($fields)) {
            return 3; // Nessun campo da aggiornare
        }

        $stmt = $this -> conn->prepare("UPDATE sediaziende SET " . implode(", ", $fields) . " WHERE azienda_id = ? AND sede_id = ?");
    
        if (!$stmt) {
            return 4; // Errore nella preparazione
        }

        $values[] = $this->aziendaId;
        $values[] = $this->sedeId;
        $types .= "ii";
    
        $stmt->bind_param($types, ...$values);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            $this->conn->commit();
            return 0; // Successo
        } 
        else {
            return 1; // Errore durante l'update
        }
    }

    public function getSedeAziendaById($aziendaId) {
        if (!$this->isConnectedToDb) return 2;

        $query = "SELECT * FROM sediaziende WHERE azienda_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $aziendaId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if (!$result || $result->num_rows === 0) {
            return 1;
        }
    
        $row = $result->fetch_assoc();
        if ($row === null || $row === false) {
            return 3;
        }

        $result->close();
        
        $this->populateFromArray($row); 

        return 0;
    }

}

?>