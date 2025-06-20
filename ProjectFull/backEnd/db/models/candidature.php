<?php

require_once("core/dbCore.php");

class Candidature extends DataBaseCore{

    private $candidaturaId;
    private $offertaId;
    private $utenteId;
    private $note;
    private $dataCandidatura;
    private $statoId;
    private $motivazioneRisultato;
    private $cvDocumentoId;

    // Getter per candidaturaId
    public function getCandidaturaId() {
        return $this->candidaturaId;
    }

    // Getter per cvDocumentoId
    public function getCvDocumentoId() {
        return $this->cvDocumentoId;
    }

    // Getter per offertaId
    public function getOffertaId() {
        return $this->offertaId;
    }

    // Getter per utenteId
    public function getUtenteId() {
        return $this->utenteId;
    }

    // Getter per note
    public function getNote() {
        return $this->note;
    }

    // Getter per dataCandidatura
    public function getDataCandidatura() {
        return $this->dataCandidatura;
    }

    // Getter per statoId
    public function getStatoId() {
        return $this->statoId;
    }

    // Getter per motivazioneRisultato
    public function getMotivazioneRisultato() {
        return $this->motivazioneRisultato;
    }

    // Metodo per trasferire i dati dall'array associativo agli attributi
    public function populateFromArray($data) {
        $this->candidaturaId = isset($data['candidatura_id']) ? $data['candidatura_id'] : null;
        $this->offertaId = isset($data['offerta_id']) ? $data['offerta_id'] : null;
        $this->utenteId = isset($data['utente_id']) ? $data['utente_id'] : null;
        $this->note = isset($data['note']) ? $data['note'] : null;
        $this->dataCandidatura = isset($data['data_candidatura']) ? $data['data_candidatura'] : null;
        $this->statoId = isset($data['stato_id']) ? $data['stato_id'] : null;
        $this->motivazioneRisultato = isset($data['motivazione_risultato']) ? $data['motivazione_risultato'] : null;
        $this->cvDocumentoId = isset($data['cv_documento_id']) ? $data['cv_documento_id'] : null;
    }

    // Metodo che restituisce un array associativo con i dati dell'oggetto
    public function toArray() {
        return [
            'candidatura_id' => $this->candidaturaId,
            'offerta_id' => $this->offertaId,
            'utente_id' => $this->utenteId,
            'note' => $this->note,
            'data_candidatura' => $this->dataCandidatura,
            'stato_id' => $this->statoId,
            'motivazione_risultato' => $this->motivazioneRisultato,
            'cv_documento_id' => $this->cvDocumentoId
        ];
    }

    function getCandidaturaById($id) {
        if (!$this->isConnectedToDb) {
            return 2; // Connessione non attiva
        }
    
        $id = intval($id); // Sicurezza base
    
        $query = "SELECT * FROM candidature WHERE candidatura_id = $id";
    
        $result = $conn->query($query);

        if (!$result) {
            return 1; // oppure puoi restituire $conn->error per debugging
        }

        populateFromArray($result);

        return 0;
    }


    function addCandidatura($offertaId, $cvId, $utenteId, $note = null) {
        if (!$this->isConnectedToDb) {
            return 2; // connessione non attiva
        }

        $stmt = $this->conn->prepare(
            "INSERT INTO candidature (offerta_id, utente_id, cv_documento_id, note, data_candidatura, stato_id)
             VALUES (?, ?, ?, ?, NOW(), 1)"
        );

        if (!$stmt) {
            return 1; // errore nella preparazione della query
        }

        $stmt->bind_param("iiis", $offertaId, $utenteId, $cvId, $note);

        if ($stmt->execute()) {
            return 0; // inserimento riuscito
        } else {
            return 1; // errore durante l'inserimento
        }
    }


    function deleteCandidatura() {
        if (!$this->isConnectedToDb) {
            return 2; // Connessione non attiva
        }

        $stmt = $this->conn->prepare("DELETE FROM candidature WHERE candidatura_id = ?");

        if (!$stmt) {
            return 1; // Errore nella preparazione della query
        }

        $stmt->bind_param("i", $this->candidaturaId);

        if ($stmt->execute()) {
            return 0; // Eliminazione riuscita
        } else {
            return 1; // Errore durante l'eliminazione
        }
    }


    function setStatoCandidatura($statoId, $motivazione = null) {
        if (!$this->isConnectedToDb) {
            return 2; // Connessione non attiva
        }

        $stmt = $this->conn->prepare(
            "UPDATE candidature SET stato_id = ?, motivazione_risultato = ? WHERE candidatura_id = ?"
        );

        if (!$stmt) {
            return 1; // Errore nella preparazione
        }

        $stmt->bind_param("isi", $statoId, $motivazione, $this->candidaturaId);

        if ($stmt->execute()) {
            return 0; // Aggiornamento riuscito
        } else {
            return 1; // Errore durante l'esecuzione
        }
    }

}
?>