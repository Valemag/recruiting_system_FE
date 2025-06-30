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

    private $modalitaLavoroId;

    // Getter per offertaId
    public function getOffertaId() {
        return $this->offertaId;
    }

    public function setOffertaId($newOffertaId) {
        $this->offertaId = $newOffertaId;
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

    public function getModalitaLavoroId() {
        return $this->modalitaLavoroId;
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
        $this->modalitaLavoroId = isset($data['modalita_lavoro_id']) ? $data['modalita_lavoro_id'] : null;
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
            'data_scadenza' => $this->dataScadenza,
            'modalita_lavoro_id' => $this->modalitaLavoroId,
        ];
    }

    private function addCompetenze($competenze) {
        $stmt = $this->conn->prepare(
            "INSERT INTO requisitiCompetenzeOfferta (competenza_id, offerta_id) VALUES (?, ?)"
        );

        if (!$stmt) {
            return 1; // Errore nella preparazione dei requisiti
        }

        foreach ($competenze as $competenzaId) {
            $stmt->bind_param("ii", $competenzaId, $this->offertaId);
            if (!$stmt->execute()) {
                return 1; // Errore nell'inserimento dei requisiti
            }
        }

        $stmt->close();
        return 0;
    }

    public function addOfferta($aziendaId, $titolo, $descrizione, $requisiti, $sedeId, $retribuzione, $tipoContrattoId, $dataScadenza, $modalitaLavoro) {
        if (!$this->isConnectedToDb) {
            return 2; // Connessione non attiva
        }
    
        // Inizia la transazione
        $this->conn->begin_transaction();
    
        // 1. Inserimento offerta
        $stmt = $this->conn->prepare(
            "INSERT INTO offerte (azienda_id, titolo, descrizione, data_pubblicazione, retribuzione, sede_lavoro_id, tipo_contratto_id, data_scadenza, modalita_lavoro_id)
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
        $stmt->close();
    
        // Ottieni l'ID dell'offerta appena inserita
        $offertaId = $this->conn->insert_id;

        //aggiunta
        $this->offertaId = $offertaId;
    
        // 2. Inserimento requisiti (competenza_id -> offerta_id)
        if (!empty($requisiti) && is_array($requisiti)) {
            if ($this->addCompetenze($requisiti) != 0) {
                $this->conn->rollback();
                return 1;
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
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return 0; // Eliminazione riuscita
        } else {
            return 1; // Errore durante l'eliminazione
        }
    }
    
    public function getOffertaById($id) {
        if (!$this->isConnectedToDb) {
            return 2; // Connessione non attiva
        }
    
        $id = intval($id); // Sicurezza base
    
        $query = "SELECT * FROM offerte WHERE offerta_id = $id";
    
        $result = $this->conn->query($query);

        if (!$result) {
            return 1; // oppure puoi restituire $conn->error per debugging
        }

        $this->populateFromArray($result->fetch_assoc());
        $result->close();

        return 0;
    }


    public function updateOfferta($competenze) {
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

        if (!empty($this->modalitaLavoroId)) {
            $fields[] = "modalita_lavoro_id = ?";
            $values[] = $this->modalitaLavoroId;
            $types .= "i";
        }

        if (!empty($this->tipoContrattoId)) {
            $fields[] = "tipo_contratto_id = ?";
            $values[] = $this->tipoContrattoId;
            $types .= "i";
        }
    
        if (empty($fields)) {
            return 3; // Nessun campo da aggiornare
        }

        // Inizia la transazione
        $this->conn->begin_transaction();
    
        $query = "UPDATE offerte SET " . implode(", ", $fields) . " WHERE offerta_id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            $this->conn->rollback();
            return 4; // Errore nella preparazione
        }
    
        $values[] = $this->offertaId;
        $types .= "i";
    
        $stmt->bind_param($types, ...$values);
        $result = $stmt->execute();
        $stmt->close();
        if (!$result) { 
            $this->conn->rollback();
            return 1;
        }

        $query = "DELETE FROM requisiticompetenzeofferta WHERE offerta_id = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            $this->conn->rollback();
            return 4; // Errore nella preparazione
        }
        $stmt->bind_param("i", $this -> offertaId);
        $result = $stmt->execute();
        $stmt->close();
        if (!$result) { 
            $this->conn->rollback();
            return 1;
        }

        if ($this->addCompetenze($competenze) != 0) {
            $this->conn->rollback();
            return 1;
        }

        // Commit finale
        $this->conn->commit();

        return 0;
    }
    

    public function getUltimeOfferteConRequisiti($userId) {
        if (!$this->isConnectedToDb) {
            return 2;
        }
    
        $query = "
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
            WHERE o.offerta_id NOT IN (
                SELECT DISTINCT(o2.offerta_id) 
                FROM offerte o2 JOIN candidature ca ON ca.offerta_id = o2.offerta_id
                WHERE ca.utente_id = ?
            )
            GROUP BY o.offerta_id
            ORDER BY o.data_pubblicazione DESC
        ";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            $this->conn->rollback();
            return 4; // Errore nella preparazione
        }
        $stmt->bind_param("i", $userId);
        $result = $stmt->execute();
        if (!$result) {
            return 1;
        }
        $result = $stmt->get_result();
        $stmt->close();
        $offerte = [];
    
        while ($row = $result->fetch_assoc()) {
            $offerte[] = $row;
        }
        $result->close();
    
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
        $result->close();
    
        return $offerte;
    }


    public function getOffertaConRequisitiById($offertaId): array|int {
        if (!$this->isConnectedToDb) {
            return 3;
        }
    
        $stmt = $this->conn->prepare("SELECT * FROM vista_offerta_con_requisiti WHERE offerta_id = ?");
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
        $result->close();

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
        $finalResult = 1;

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
    
            $finalResult = 0; // Successo
        } else {
            $finalResult = 3; // Nessuna sede trovata
        }

        $result->close();
        return $finalResult;
    }

    public function getCompetenzeRichiesteByOffertaId($offertaId) {
        if (!$this->isConnectedToDb) {
            return 2; // Connessione non attiva
        }
    
        $query = "
            SELECT c.competenza AS competenza
            FROM requisitiCompetenzeOfferta r
            JOIN competenze c ON r.competenza_id = c.competenza_id
            WHERE r.offerta_id = ?
        ";
    
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return 1; // Errore nella preparazione
        }
    
        $stmt->bind_param("i", $offertaId);
        if (!$stmt->execute()) {
            return 1; // Errore in esecuzione
        }
    
        $result = $stmt->get_result();
        $stmt->close();
        $competenze = [];
    
        while ($row = $result->fetch_assoc()) {
            $competenze[] = $row;
        }
        $result->close();
    
        return $competenze; // Array di competenze
    }

    public function getOfferteAppuntateByUtenteId($utenteId) {
        $query = "
            SELECT *
            FROM vista_offerte_appuntate_utente
            WHERE oa.utente_id = ?";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $utenteId);
        $finalResult = [];
    
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $offerte = [];
            while ($row = $result->fetch_assoc()) {
                $offerte[] = $row;
            }
            $result->close();
            $finalResult = $offerte;
        }
        $stmt->close();
        
        return $finalResult;
    }

}
?>