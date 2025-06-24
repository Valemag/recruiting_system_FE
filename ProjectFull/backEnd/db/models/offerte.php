<?php

require_once("core/dbCore.php");

class Offerte extends DataBaseCore{

    private $offertaId;
    private $aziendaId;
    private $descrizione;
    private $titolo;
    private $tipoContrattoId;
    private $retribuzione;
    private $dataPubblicazione;
    private $sedeLavoroId;
    private $dataScadenza;

    private $candidature;


    // Getter per offertaId
    public function getOffertaId() {
        return $this->offertaId;
    }

    // Getter per aziendaId
    public function getAziendaId() {
        return $this->aziendaId;
    }

    // Getter per descrizione
    public function getDescrizione() {
        return $this->descrizione;
    }

    // Getter per titolo
    public function getTitolo() {
        return $this->titolo;
    }

    // Getter per tipoContrattoId
    public function getTipoContrattoId() {
        return $this->tipoContrattoId;
    }

    // Getter per retribuzione
    public function getRetribuzione() {
        return $this->retribuzione;
    }

    // Getter per dataPubblicazione
    public function getDataPubblicazione() {
        return $this->dataPubblicazione;
    }

    // Getter per sedeLavoroId
    public function getSedeLavoroId() {
        return $this->sedeLavoroId;
    }

    // Getter per dataScadenza
    public function getDataScadenza() {
        return $this->dataScadenza;
    }

    // Getter per candidature
    public function getCandidature() {
        return $this->dataScadenza;
    }

    // Metodo per trasferire i dati dall'array associativo agli attributi
    public function populateFromArray($data) {
        $this->offertaId = isset($data['offerta_id']) ? $data['offerta_id'] : null;
        $this->aziendaId = isset($data['azienda_id']) ? $data['azienda_id'] : null;
        $this->descrizione = isset($data['descrizione']) ? $data['descrizione'] : null;
        $this->titolo = isset($data['titolo']) ? $data['titolo'] : null;
        $this->tipoContrattoId = isset($data['tipo_contratto_id']) ? $data['tipo_contratto_id'] : null;
        $this->retribuzione = isset($data['retribuzione']) ? $data['retribuzione'] : null;
        $this->dataPubblicazione = isset($data['data_pubblicazione']) ? $data['data_pubblicazione'] : null;
        $this->sedeLavoroId = isset($data['sede_lavoro_id']) ? $data['sede_lavoro_id'] : null;
        $this->dataScadenza = isset($data['data_scadenza']) ? $data['data_scadenza'] : null;
    }

    // Metodo che restituisce un array associativo con i dati dell'oggetto
    public function toArray() {
        return [
            'offerta_id' => $this->offertaId,
            'azienda_id' => $this->aziendaId,
            'descrizione' => $this->descrizione,
            'titolo' => $this->titolo,
            'tipo_contratto_id' => $this->tipoContrattoId,
            'retribuzione' => $this->retribuzione,
            'data_pubblicazione' => $this->dataPubblicazione,
            'sede_lavoro_id' => $this->sedeLavoroId,
            'data_scadenza' => $this->dataScadenza
        ];
    }

    function addOfferta($aziendaId, $titolo, $descrizione, $requisiti, $sedeId, $retribuzione, $tipoContrattoId, $dataScadenza, $modalitaLavoro) {
        if (!$this->isConnectedToDb) {
            return 2; // Connessione non attiva
        }
    
        // Inizia la transazione
        $this->conn->begin_transaction();
    
        // 1. Inserimento offerta
        $stmt = $this->conn->prepare(
            "INSERT INTO offerte (azienda_id, titolo, descrizione, data_pubblicazione, retribuzione, sede_lavoro_id, tipo_contratto_id, data_scadenza modalita_lavoro_id)
             VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, ?)"
        );
    
        if (!$stmt) {
            $this->conn->rollback();
            return 1; // Errore nella preparazione
        }
    
        $stmt->bind_param("isssiisi", $aziendaId, $titolo, $descrizione, $retribuzione, $sedeId, $tipoContrattoId, $dataScadenza, $modalitaLavoro);
    
        if (!$stmt->execute()) {
            $this->conn->rollback();
            return 1; // Errore in esecuzione
        }
    
        // Ottieni l'ID dell'offerta appena inserita
        $offertaId = $this->conn->insert_id;
    
        // 2. Inserimento requisiti (competenza_id -> offerta_id)
        if (!empty($requisiti) && is_array($requisiti)) {
            $stmtReq = $this->conn->prepare(
                "INSERT INTO requisitiCompetenzeOfferta (competenza_id, offerta_id) VALUES (?, ?)"
            );
    
            if (!$stmtReq) {
                $this->conn->rollback();
                return 1; // Errore nella preparazione dei requisiti
            }
    
            foreach ($requisiti as $competenzaId) {
                $stmtReq->bind_param("ii", $competenzaId, $offertaId);
                if (!$stmtReq->execute()) {
                    $this->conn->rollback();
                    return 1; // Errore nell'inserimento dei requisiti
                }
            }
        }
    
        // Commit finale
        $this->conn->commit();
        return 0; // Inserimento riuscito
    }
    
    function deleteOfferta() {
        if (!$this->isConnectedToDb) {
            return 2; // Connessione non attiva
        }
    
        $stmt = $this->conn->prepare("DELETE FROM offerte WHERE offerta_id = ?");
    
        if (!$stmt) {
            return 1; // Errore nella preparazione
        }
    
        $stmt->bind_param("i", $this -> offertaId);
    
        if ($stmt->execute()) {
            return 0; // Eliminazione riuscita
        } else {
            return 1; // Errore durante l'eliminazione
        }
    }
    
    function getOffertaById($id) {
        if (!$this->isConnectedToDb) {
            return 2; // Connessione non attiva
        }
    
        $id = intval($id); // Sicurezza base
    
        $query = "SELECT * FROM offerte WHERE offerta_id = $id";
    
        $result = $conn->query($query);

        if (!$result) {
            return 1; // oppure puoi restituire $conn->error per debugging
        }

        populateFromArray($result);

        return 0;
    }


    public function updateOfferta() {
        if (!$this->isConnectedToDb) {
            return 2; // Connessione non attiva
        }
    
        $fields = [];
        $values = [];
        $types = "";
    
        if (!empty($this->descrizione)) {
            $fields[] = "descrizione = ?";
            $values[] = $this->descrizione;
            $types .= "s";
        }
    
        if (!empty($this->titolo)) {
            $fields[] = "titolo = ?";
            $values[] = $this->titolo;
            $types .= "s";
        }
    
        if (!is_null($this->retribuzione)) {
            $fields[] = "retribuzione = ?";
            $values[] = $this->retribuzione;
            $types .= "d"; // double, puoi cambiare in "i" se intero
        }
    
        if (!empty($this->dataScadenza)) {
            $fields[] = "data_scadenza = ?";
            $values[] = $this->dataScadenza;
            $types .= "s";
        }
    
        if (empty($fields)) {
            return 3; // Nessun campo da aggiornare
        }
    
        $query = "UPDATE offerte SET " . implode(", ", $fields) . " WHERE offerta_id = ?";
        $stmt = $this->conn->prepare($query);
    
        if (!$stmt) {
            return 4; // Errore nella preparazione
        }
    
        $values[] = $this->offertaId;
        $types .= "i";
    
        $stmt->bind_param($types, ...$values);
    
        if ($stmt->execute()) {
            return 0; // Successo
        } else {
            return 1; // Errore durante l'update
        }
    }
    

    public function getUltimeOfferteConRequisiti() {
        if (!$this->isConnectedToDb) {
            return 2;
        }
    
        $query = "SELECT * FROM vista_ultime_offerte_con_requisiti";
        $result = $this->conn->query($query);
    
        if (!$result) {
            return 1;
        }
    
        $offerte = [];
    
        while ($row = $result->fetch_assoc()) {
            $offerte[] = $row;
        }
    
        return $offerte;
    }

    public function getOfferteAzienda($aziendaId) {
        if (!$this->isConnectedToDb) {
            return 2;
        }
    
        $query = "SELECT * FROM vista_offerte_azienda WHERE azienda_id = ".$aziendaId;
        $result = $this->conn->query($query);
    
        if (!$result) {
            return 1;
        }
    
        $offerte = [];
    
        while ($row = $result->fetch_assoc()) {
            $offerte[] = $row;
        }
    
        return $offerte;
    }


    public function getOffertaConRequisitiById($offertaId) {
        if (!$this->isConnectedToDb) {
            return 3;
        }
    
        $stmt = $this->conn->prepare("
            SELECT * FROM vista_offerta_con_requisiti
            WHERE offerta_id = ?
            LIMIT 1
        ");
    
        if (!$stmt) {
           return 2;
        }
    
        $stmt->bind_param("i", $offertaId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if (!$result || $result->num_rows === 0) {
            return 1;
        }
    
        $offerta = $result->fetch_assoc();
        return $offerta;
    }

    public function fetchCandidatureOfferta(){


        if (!$this->isConnectedToDb) {
            return 2;
        }

        $query = "select * from candidature where offerta_id = ".$this->offertaId;

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

}
?>