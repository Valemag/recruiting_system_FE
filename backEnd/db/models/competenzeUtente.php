<?php

require_once(__DIR__ . "/core/dbCore.php");

class CompetenzeUtente extends DataBaseCore {
    private $utente_id;
    private $competenza; // array di nomi competenze

    public function getUtenteId() {
        return $this->utente_id;
    }

    public function getCompetenza() {
        return $this->competenza;
    }

    // Metodo per popolare da array
    public function populateFromArray(array $data) {
        if (isset($data['utente_id'])) $this->utente_id = $data['utente_id'];
        if (isset($data['competenza'])) $this->competenza = $data['competenza'];
    }

    // Aggiunge una competenza per un utente, dato il nome della competenza
    public function aggiungiCompetenzaUtente($utenteId, $competenzaId) {
        if (!$this->isConnectedToDb) {
            return 2; // Connessione non attiva
        }

        // Inserisce la relazione nella tabella competenzaUtente
        $stmt = $this->conn->prepare("INSERT INTO competenzaUtente (utente_id, competenza_id) VALUES (?, ?)");
        if (!$stmt) return 1; // Errore nella preparazione

        $stmt->bind_param("ii", $utenteId, $competenzaId);

        if ($stmt->execute()) {
            return 0; // Inserimento riuscito
        } else {
            return 5; // Errore in esecuzione
        }
    }

}
