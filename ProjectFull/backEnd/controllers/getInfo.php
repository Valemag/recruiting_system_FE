<?php


function getInfoUtente(){

    require_once(__DIR__."/../db/models/utenti.php");
    require_once(__DIR__."/../fileSystem/storage/storageUtenti.php");

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if(isset($_GET["id"]) && $_GET["id"]!=""){

        if(isset($_SESSION["utente_id"]) || isset($_SESSION["azienda_id"])){

            $utenti = new Utenti();
            $storageUtenti = new StorageUtenti();

            $userId = $_GET["id"];

            if ($utenti->connectToDatabase() != 0) {
                echo "Connessione al database fallita";
                http_response_code(500);
                return NULL;
            }
            
            $result = $utenti -> getUtenteById($userId);

            if($result != 0){

                $utenti -> closeConnectionToDatabase();
                echo("errore durante il fetching dell'utente");
                http_response_code(500);
                return NULL;

            }

            $result = $utenti -> fetchCompetenzeUtente();

            if($result != 0){

                //echo("errore durante il fetching delle competenze");

            }

            $result = $utenti -> fetchDocumentiUtente();

            if($result != 0){

                // echo("errore durante il fetching dei documenti");
                // http_response_code(500);
                // return NULL;

            }


            $userData = $utenti -> toArray();

            unset($userData["password"]);
            unset($userData["data_registrazione"]);

            $path = "../../bitByte/backEnd/fileSystem/";
            $path .= $storageUtenti -> getUploadsPath();
            $path .= $storageUtenti -> getUtenteFolderPlaceholder();
            $path .= $userId."/";

            if(isset($userData["documenti"])){

                foreach($userData["documenti"] as &$documento){

                    $nomeFile = $documento->getDocumento();
                    $documentoId = $documento->getDocumentoId();
    
                    $documento = [
                        "path" => $path . $nomeFile,
                        "nome" => $nomeFile,
                        "id" => $documentoId
                    ];
    
                }

            }

            if($userData["immagine_profilo"] != NULL){

                $userData["immagine_profilo"] = $path . $userData["immagine_profilo"];

            }
            else{

                unset($userData["immagine_profilo"]);

            }

            $utenti -> closeConnectionToDatabase();
            return $userData;

        }   
        else{

            echo("non autenticato");
            http_response_code(403);
            header('Location: ../../frontEnd/login.html');
            return NULL;
        }

    }
    else{

        echo("campi obbligatori mancanti");
        http_response_code(403);
        header('Location: ../../frontEnd/login.html');
        return NULL;

    }

}


function getInfoUtenteBySession(){

    require_once(__DIR__."/../db/models/utenti.php");
    require_once(__DIR__."/../fileSystem/storage/storageUtenti.php");

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if(isset($_SESSION["utente_id"]) || isset($_SESSION["azienda_id"])){

        $utenti = new Utenti();
        $storageUtenti = new StorageUtenti();

        $userId = $_SESSION["utente_id"];

        if ($utenti->connectToDatabase() != 0) {
            echo "Connessione al database fallita";
            http_response_code(500);
            return NULL;
        }
            
        $result = $utenti -> getUtenteById($userId);

        if($result != 0){

            $utenti -> closeConnectionToDatabase();
            echo("errore durante il fetching dell'utente");
            http_response_code(500);
            return NULL;

        }

        $result = $utenti -> fetchCompetenzeUtente();

        if($result != 0){

            //echo("errore durante il fetching delle competenze");

        }

        $result = $utenti -> fetchDocumentiUtente();

        if($result != 0){

            // echo("errore durante il fetching dei documenti");
            // http_response_code(500);
            // return NULL;

        }


        $userData = $utenti -> toArray();

        unset($userData["password"]);
        unset($userData["data_registrazione"]);

        $path = "../../bitByte/backEnd/fileSystem/";
        $path .= $storageUtenti -> getUploadsPath();
        $path .= $storageUtenti -> getUtenteFolderPlaceholder();
        $path .= $userId."/";

        if(isset($userData["documenti"])){

            foreach($userData["documenti"] as &$documento){

                $nomeFile = $documento->getDocumento();
                $documentoId = $documento->getDocumentoId();
    
                $documento = [
                    "path" => $path . $nomeFile,
                    "nome" => $nomeFile,
                    "id" => $documentoId
                ];
    
            }

        }

        if($userData["immagine_profilo"] != NULL){

            $userData["immagine_profilo"] = $path . $userData["immagine_profilo"];

        }
        else{

            unset($userData["immagine_profilo"]);

        }

        $utenti -> closeConnectionToDatabase();
        return $userData;

    }   
    else{

        echo("non autenticato");
        http_response_code(403);
        header('Location: ../../frontEnd/login.html');
        return NULL;
    }

}



function getInfoAzienda(){

    require_once(__DIR__."/../db/models/aziende.php");
    require_once(__DIR__."/../fileSystem/storage/storageAziende.php");

    if(isset($_GET["id"]) && $_GET["id"]!=""){

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if(isset($_SESSION["azienda_id"]) || isset($_SESSION["utente_id"])){

            $aziende = new Aziende();
            $storageAziende = new StorageAziende();

            $aziendaId = $_GET["id"];

            if ($aziende->connectToDatabase() != 0) {
                echo "Connessione al database fallita";
                http_response_code(500);
                return NULL;
            }
            
            $result = $aziende -> getAziendaById($aziendaId);

            if($result != 0){

                $aziende -> closeConnectionToDatabase();
                echo("errore durante il fetching dell'azienda");
                http_response_code(500);
                return NULL;

            }

            $result = $aziende -> fetchOfferteAzienda();

            if($result != 0){

                //echo("errore durante il fetching delle offerte");

            }

            $result = $aziende -> fetchSediAzienda();

            if($result != 0){

                $aziende -> closeConnectionToDatabase();
                echo("errore durante il fetching delle sedi: ".$result);
                http_response_code(500);
                return NULL;

            }

            $aziendaData = $aziende -> toArray();

            $path = "../../bitByte/backEnd/fileSystem/";
            $path .= $storageAziende -> getUploadsPath();
            $path .= $storageAziende -> getAziendaFolderPlaceholder();
            $path .= $aziendaId."/";

            if($aziendaData["logo"] != NULL){

                $aziendaData["logo"] = $path . $aziendaData["logo"];

            }
            else{

                unset($aziendaData["logo"]);

            }

            $aziende -> closeConnectionToDatabase();

            return $aziendaData;

        }
        else{

            echo("non autenticato");
            http_response_code(403);
            header('Location: ../../frontEnd/login.html');
            return NULL;
        }
    }
    else{

        echo("campi obbligatori mancanti");
        http_response_code(403);
        header('Location: ../../frontEnd/login.html');
        return NULL;

    }

}

function getInfoAziendaBySession(){

    require_once(__DIR__."/../db/models/aziende.php");
    require_once(__DIR__."/../fileSystem/storage/storageAziende.php");

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if(isset($_SESSION["azienda_id"]) || isset($_SESSION["utente_id"])){

        $aziende = new Aziende();
        $storageAziende = new StorageAziende();

        $aziendaId = $_GET["id"];

        if ($aziende->connectToDatabase() != 0) {
            echo "Connessione al database fallita";
            http_response_code(500);
            return NULL;
        }
            
        $result = $aziende -> getAziendaById($aziendaId);

        if($result != 0){

            $aziende -> closeConnectionToDatabase();
            echo("errore durante il fetching dell'azienda");
            http_response_code(500);
            return NULL;

        }

        $result = $aziende -> fetchOfferteAzienda();

        if($result != 0){

            //echo("errore durante il fetching delle offerte");

        }

        $result = $aziende -> fetchSediAzienda();

        if($result != 0){

            $aziende -> closeConnectionToDatabase();
            echo("errore durante il fetching delle sedi");
            http_response_code(500);
            return NULL;

        }

        $aziendaData = $aziende -> toArray();

        $path = "../../bitByte/backEnd/fileSystem/";
        $path .= $storageAziende -> getUploadsPath();
        $path .= $storageAziende -> getAziendaFolderPlaceholder();
        $path .= $aziendaId."/";

        if($aziendaData["logo"] != NULL){

            $aziendaData["logo"] = $path . $aziendaData["logo"];

        }
        else{

            unset($aziendaData["logo"]);

        }

        $aziende -> closeConnectionToDatabase();

        return $aziendaData;

    }
    else{

        echo("non autenticato");
        http_response_code(403);
        header('Location: ../../frontEnd/login.html');
        return NULL;
    }

}

?>
