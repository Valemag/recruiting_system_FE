<?php

    require("../../db/models/aziende.php");
    require("../../fileSystem/storage/storageAziende.php");

    session_Start();

    if(isset($_SESSION["azienda_id"]) && $_SESSION["azienda_id"] != ""){

        if(isset($_FILE["file"])){

            $storageAzienda = new StorageAziende();
            $azienda = new Aziende();

            $azienda -> connectToDatabase();

            $result = $azienda -> getAziendaById($_SESSION["azienda_id"]);

            if($result > 0){

                echo("impossibile connettersi al database");
                http_response_code(500);

            }


            $result = $storageAzienda -> uploadAziendaFile($_SESSION["id"], $_FILE["file"]);

            if($result > 0){

                $azienda -> closeConnectionToDatabase();
                echo("impossibile caricare il file");
                http_response_code(500);

            }

            $result = $azienda -> addDocumento($_FILE["file"]["name"]);

            if($result > 0){

                $azienda -> closeConnectionToDatabase();
                echo("impossibile caricare il file");
                http_response_code(500);

            }

            $azienda -> closeConnectionToDatabase();
            echo("successo");
            http_response_code(200);

        }
        else {

            $azienda -> closeConnectionToDatabase();
            echo("file mancante");
            http_response_code(403);

        }

    }
    else{

        $utente -> closeConnectionToDatabase();
        echo("non autenticato");
        http_response_code(403);

    }

?>