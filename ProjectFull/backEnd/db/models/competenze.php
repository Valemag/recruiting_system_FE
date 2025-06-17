<?php

// Includi la classe base per la connessione al database
require_once(__DIR__ . "/core/dbCore.php");

class CompetenzeUtente extends DataBaseCore {
    private $candidatura_id;
    private $offerta_id;
    private $utente_id;
    private $note;
    private $data_candidatura;
    private $stato_id;
    private $motivazione_risultato;

    // Getters
    public function getCandidaturaId()
    {
        return $this->candidatura_id;
    }

    public function getOffertaId()
    {
        return $this->offerta_id;
    }

    public function getUtenteId()
    {
        return $this->utente_id;
    }

    public function getNote()
    {
        return $this->note;
    }

    public function getDataCandidatura()
    {
        return $this->data_candidatura;
    }

    public function getStatoId()
    {
        return $this->stato_id;
    }

    public function getMotivazioneRisultato()
    {
        return $this->motivazione_risultato;
    }

    // Metodo per esportare i dati come array associativo
    public function toArray()
    {
        return [
            'candidatura_id' => $this->candidatura_id,
            'offerta_id' => $this->offerta_id,
            'utente_id' => $this->utente_id,
            'note' => $this->note,
            'data_candidatura' => $this->data_candidatura,
            'stato_id' => $this->stato_id,
            'motivazione_risultato' => $this->motivazione_risultato
        ];
    }

    // Metodo per impostare i valori dagli array associativi
    public function populateFromArray(array $data)
    {
        if (isset($data['candidatura_id'])) $this->candidatura_id = $data['candidatura_id'];
        if (isset($data['offerta_id'])) $this->offerta_id = $data['offerta_id'];
        if (isset($data['utente_id'])) $this->utente_id = $data['utente_id'];
        if (isset($data['note'])) $this->note = $data['note'];
        if (isset($data['data_candidatura'])) $this->data_candidatura = $data['data_candidatura'];
        if (isset($data['stato_id'])) $this->stato_id = $data['stato_id'];
        if (isset($data['motivazione_risultato'])) $this->motivazione_risultato = $data['motivazione_risultato'];
    }

    public function getCompetenzaById($id) {

        if (!$this->isConnectedToDb) {
            return 1;
        }

        $query = "select * from competenze where utente_id = '".$id."'";

        $result = $this->conn->query($query);

        if (!$result) {
            return 2; // oppure puoi restituire $conn->error per debugging
        }

        $result = $result->fetch_assoc();

        $this->populateFromArray($result);

        return 0;
    }

    // Metodo per aggiungere una competenza a un utente
    function aggiungiCompetenzaUtente($utenteId, $competenzaId) {
        // Verifica connessione attiva
        if (!$this->isConnectedToDb) {
            return 2; // Connessione non attiva
        }

        // Prepara l'istruzione SQL per l'inserimento
        $stmt = $this->conn->prepare(
            "INSERT INTO competenzaUtente (utente_id, competenza_id) VALUES (?, ?)"
        );

        if (!$stmt) {
            return 1; // Errore nella preparazione
        }

        // Associa i parametri e esegui
        $stmt->bind_param("ii", $utenteId, $competenzaId);

        if ($stmt->execute()) {
            return 0; // Inserimento riuscito
        } else {
            return 1; // Errore in esecuzione
        }
    }
}
