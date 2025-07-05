<?php

require_once("core/dbCore.php");

    class DocumentiUtente extends DataBaseCore{

        private $documentoId;
        private $utenteId;
        private $documento;

        public function getDocumentoId(){

            return $this -> documentoId;

        }

        public function getUtenteId(){

            return $this -> utenteId;

        }
        
        public function getDocumento(){

            return $this -> documento;

        }

        // Metodo per trasferire i dati dall'array associativo agli attributi
    public function populateFromArray($data) {
        $this->documentoId = isset($data['documento_id']) ? $data['documento_id'] : null;
        $this->utenteId = isset($data['utente_id']) ? $data['utente_id'] : null;
        $this->documento = isset($data['documento']) ? $data['documento'] : null;
    }

    // Metodo che restituisce un array associativo con i dati dell'oggetto
    public function toArray() {
        return [
            'documento_id' => $this->documentoId,
            'documento' => $this->documento,
            'utente_id' => $this->utenteId
        ];
    }

        public function addDocumento($idUtente, $nomeDocumento){

            if (!$this->isConnectedToDb) {
                return 2;
            }

            $sql = "INSERT INTO documentiUtente (utente_id, documento) VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);

            // Associa i parametri
            $result = $stmt->bind_param('is', $idUtente, $nomeDocumento);
            if (! $result) {
                return 3;
            }
            $result = $stmt->execute();
            $stmt->close();

            // Esegui l'inserimento
            if ($result) {
                return 1; // restituisce l'ID del nuovo record
            } 
            return 0; // inserimento fallito
        }

    }

?>