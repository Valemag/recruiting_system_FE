<?php

require_once("core/dbCore.php");

    class DocumentiAzienda extends DataBaseCore{

        private $documentoId;
        private $aziendaId;
        private $documento;

        public function getDocumentoId(){

            return $this -> documentoId;

        }

        public function getAziendaId(){

            return $this -> aziendaId;

        }
        
        public function getDocumento(){

            return $this -> documento;

        }

        public function addDocumento($aziendaId, $nomeDocumento){

            if (!$this->isConnectedToDb) {
                return 2;
            }

            $sql = "INSERT INTO documentiAzienda (azienda_id, documento) VALUES (:azienda_id, :documento)";
            $stmt = $conn->prepare($sql);

            // Associa i parametri
            $stmt->bindParam(':azienda_id', $aziendaId, PDO::PARAM_INT);
            $stmt->bindParam(':documento', $nomeDocumento, PDO::PARAM_STR);

            // Esegui l'inserimento
            if ($stmt->execute()) {
                return 1; // restituisce l'ID del nuovo record
            } else {
                return 0; // inserimento fallito
            }

        }

        public function deleteDocumento($documentoId) {
            if (!$this->isConnectedToDb) {
                return 2; // Connessione non attiva
            }
        
            $sql = "DELETE FROM documentiAzienda WHERE documento_id = :documento_id";
            $stmt = $this->conn->prepare($sql);
        
            if (!$stmt) {
                return 0; // Errore nella preparazione
            }
        
            $stmt->bindParam(':documento_id', $documentoId, PDO::PARAM_INT);
        
            if ($stmt->execute()) {
                return 1; // Eliminazione riuscita
            } else {
                return 0; // Errore durante l'eliminazione
            }
        }
        

    }

?>