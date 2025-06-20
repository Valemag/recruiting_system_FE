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

}

?>