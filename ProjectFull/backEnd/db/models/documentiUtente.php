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

        public function addDocumento($idUtente, $nomeDocumento){

            if (!$this->isConnectedToDb) {
                return 2;
            }

            $sql = "INSERT INTO documentiUtente (utente_id, documento) VALUES (:utente_id, :documento)";
            $stmt = $conn->prepare($sql);

            // Associa i parametri
            $stmt->bindParam(':utente_id', $idUtente, PDO::PARAM_INT);
            $stmt->bindParam(':documento', $nomeDocumento, PDO::PARAM_STR);

            // Esegui l'inserimento
            if ($stmt->execute()) {
                return 1; // restituisce l'ID del nuovo record
            } else {
                return 0; // inserimento fallito
            }

        }

    }

?>