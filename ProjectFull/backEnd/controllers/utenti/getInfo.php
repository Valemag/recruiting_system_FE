<?php

require(__DIR__."/../../db/models/utenti.php");
require(__DIR__."/../../fileSystem/storage/storageUtenti.php");

function getInfo(){

    if(isset($_GET["id"]) && $_GET["id"]!=""){

        session_start();

        if(isset($_SESSION["utente_id"])){

            $utenti = new Utenti();
            $storageUtenti = new StorageUtenti();

            $userId = $_GET["id"];

            $utenti -> connectToDatabase();
            
            $result = $utenti -> getUtenteById($userId);

            if($result != 0){

                echo("errore durante il fetching dell'utente");
                http_response_code(500);
                return NULL;

            }

            $result = $utenti -> fetchCompetenzeUtente();

            if($result != 0){

                echo("errore durante il fetching delle competenze");
                http_response_code(500);
                return NULL;

            }

            $result = $utenti -> fetchDocumentiUtente();

            if($result != 0){

                echo("errore durante il fetching delle competenze");
                http_response_code(500);
                return NULL;

            }


            $userData = $utenti -> toArray();

            unset($userData["password"]);
            unset($userData["data_registrazione"]);

            $path = $storageUtenti -> getFileSystemUrl();
            $path .= $storageUtenti -> getUploadsPath();
            $path .= $storageUtenti -> getUtenteFolderPlaceholder();
            $path .= $userId."/";

            foreach($userData["documenti"] as &$documento){

                $nomeFile = $documento->getDocumento();
                $documentoId = $documento->getDocumentoId();

                $documento = [
                    "path" => $path . $nomeFile,
                    "nome" => $nomeFile,
                    "id" => $documentoId
                ];

            }


            if($userData["immagine_profilo"] != NULL){

                $userData["immagine_profilo"] = $path . $userData["immagine_profilo"];

            }
            else{

                unset($userData["immagine_profilo"]);

            }

            return $userData;

        }
        else{

            echo("non autenticato");
            http_response_code(403);
            header('Location: ../../frontEnd/utente/login.html');
            return NULL;
        }

    }
    else{

        echo("campi obbligatori mancanti");
        http_response_code(403);
        header('Location: ../../frontEnd/utente/login.html');
        return NULL;

    }

}

?>