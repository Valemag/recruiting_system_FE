<?php

    require("../../db/models/utenti.php");
    require("../../fileSystem/storage/storageUtenti.php");

function uploadFile(){

    session_Start();

    if(isset($_SESSION["utente_id"]) && $_SESSION["utente_id"] != ""){

        if(isset($_FILE["file"])){

            $storageUtenti = new StorageUtenti();
            $utente = new Utenti();

            $utente -> connectToDatabase();

            $result = $utente -> getUtenteById($_SESSION["utente_id"]);

            if($result > 0){

                echo("impossibile connettersi al database");
                http_response_code(500);

            }


            $result = $storageUtenti -> uploadUtenteFile($_SESSION["id"], $_FILE["file"]);

            if($result > 0){

                $utente -> closeConnectionToDatabase();
                echo("impossibile caricare il file");
                http_response_code(500);

            }

            $result = $utente -> addDocumento($_FILE["file"]["name"]);

            if($result > 0){

                $utente -> closeConnectionToDatabase();
                echo("impossibile caricare il file");
                http_response_code(500);

            }

            $utente -> closeConnectionToDatabase();
            echo("successo");
            http_response_code(200);

        }
        else {

            $utente -> closeConnectionToDatabase();
            echo("file mancante");
            http_response_code(403);

        }

    }
    else{

        $utente -> closeConnectionToDatabase();
        echo("non autenticato");
        http_response_code(403);

    }
}

?>