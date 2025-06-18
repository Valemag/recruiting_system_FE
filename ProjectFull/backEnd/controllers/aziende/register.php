<?php

    require("../db/models/aziende.php");
    require("../fileSystem/storage/storageAzienda.php");


    if((isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["email_contatto"]) && isset($_POST["nome"]) && 
        isset($_POST["sito_web"]) && isset($_POST["descrizione"]) && isset($_POST["telefono_contatto"])) &&
       ($_POST["email"] != "" && $_POST["password"] != "" && $_POST["email_contatto"]!= "" && $_POST["nome"] != "" && 
        $_POST["cognome"] != "" && $_POST["telefono_contatto"]!= "")
    ){

        $azienda = new Aziende();
        $fs = new StorageAziende();

        if($_POST["descrizione"] != ""){

            $descrizione = $_POST["descrizione"];

        }
        else{

            $descrizione = "";

        }

        if($azienda -> connectToDatabase() != 0){

            echo("errore durante la connessione al database");
            http_response_code(500);

        }

        $result = $azienda -> addAzienda($_POST["nome"], $descrizione, $_POST["sito_web"], $_POST["email_contatto"], $_POST["telefono_contatto"], $_POST["email"], $_POST["password"]);

        if($result != 0){

            $azienda ->  closeConnectionToDatabase();
            echo("errore durante la registrazione dell'azienda");
            http_response_code(500);

        }

        // Recupera l'azienda tramite email
        $result = $azienda->getAziendaByEmail($email);

        // Controllo esistenza azienda
        if ($result != 0) {
            echo "Azienda non trovata.";
            http_response_code(404);
            exit;
        }
                
        $fs -> createAziendaFolder($azienda->getAziendaId());

        if(isset($_FILE["logo"]) && $_FILE["logo"]["error"] == 0){

            $result = $fs -> uploadAziendaFile($azienda->getAziendaId(), $_FILE["logo"]);

            if($result != 0){

                $azienda -> closeConnectionToDatabase();
                echo("errore durante l'upload del file");
                http_response_code(500);

            }


            $result = $azienda -> setAziendaLogo($aziendaData["azienda_id"], $_FILE["logo"]["name"]);

            if($result != 0){
                            
                $azienda -> closeConnectionToDatabase();
                echo("errore durante l'upload dell'immagine profilo");
                http_response_code(500);

            }

        }

                    
        $azienda -> closeConnectionToDatabase();
        echo("registrazione eseguita con successo");
        http_response_code(200);
            

    }
    else{

        $azienda -> closeConnectionToDatabase();
        echo("bad request");
        http_response_code(400);

    }

?>