<?php

require_once(__DIR__ . "/core/dbCore.php");

class CompetenzeOfferta extends DataBaseCore {
    private $competenza_id;
    private $offerta_id;

    public function getCompetenzaId() {
        return $this->competenza_id;
    }

    public function getOffertaId() {
        return $this->offerta_id;
    }

    // Metodo per popolare da array
    public function populateFromArray(array $data) {
        if (isset($data['competenza_id'])) $this->competenza_id = $data['competenza_id'];
        if (isset($data['offerta_id'])) $this->offerta_id = $data['offerta_id'];
    }

    public function getCompetenzeByOffertaId($offertaId) {
        if (!$this->isConnectedToDb) {
            return 2; // Connessione non attiva
        }

        $stmt = $this->conn->prepare("SELECT competenza_id, offerta_id FROM requisiticompetenzeofferta WHERE offerta_id = ?");
        if (!$stmt) return 1; // Errore nella preparazione

        $stmt->bind_param("i", $offertaId);

        if (!$stmt->execute()) {
            return 5;
        } 
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();

        return $data;
    }

}
