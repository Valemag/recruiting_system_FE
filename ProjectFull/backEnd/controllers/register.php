<?php

function register(){

    // Verifica parametri comuni
    if (!isset($_POST["tipo"]) || $_POST["tipo"] === "") {
        echo "Parametro 'tipo' mancante.";
        http_response_code(400);
        exit;
    }

    $tipo = $_POST["tipo"]; // 'utente' o 'azienda'

    if ($tipo === "utente") {
        require("../db/models/utenti.php");
        require("../fileSystem/storage/storageUtenti.php");

        if((isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["username"]) && isset($_POST["nome"]) && 
            isset($_POST["cognome"]) && isset($_POST["descrizione"]) && isset($_POST["nTelefono"])) &&
            ($_POST["email"] != "" && $_POST["password"] != "" && $_POST["username"]!= "" && $_POST["nome"] != "" && 
            $_POST["cognome"] != "" && $_POST["nTelefono"]!= "")
        ){

            $utente = new Utenti();
            $fs = new StorageUtenti();

            if($_POST["descrizione"] != ""){

                $descrizione = $_POST["descrizione"];

            }
            else{

                $descrizione = "";

            }
            
            $result = $utente ->connectToDatabase();

            if($result != 0){

                echo("impossibile connettersi al database");
                http_response_code(500);

            }

            $result = $utente -> addUtente($_POST["email"], $_POST["password"], $_POST["username"], $_POST["nome"], $_POST["cognome"], $descrizione, $_POST["nTelefono"]);

            if($result != 0){

                $utente -> closeConnectionToDatabase();
                echo("errore durante la registrazione dell'utente");
                http_response_code(500);

            }

            $result = $utente -> getUtenteByEmail($_POST["email"]);

            if($result != 0){

                $utente -> closeConnectionToDatabase();
                echo("errore durante il fetching dell'utente");
                http_response_code(500);

            }
                
            $fs -> createUtenteFolder($utente -> getUtenteId());


            if(isset($_FILES["immagineProfilo"]) && $_FILES["immagineProfilo"]["error"] == 0){

                $result = $fs -> uploadUtenteFile($utente -> getUtenteId(), $_FILES["immagineProfilo"]);

                if($result != 0){

                    $utente -> closeConnectionToDatabase();
                    echo("errore durante l'upload del file");
                    http_response_code(500);

                }

                $result = $utente -> setUtenteProfileImage($_FILES["immagineProfilo"]["name"]);

                if($result != 0){
                            
                    $utente -> closeConnectionToDatabase();
                    echo("errore durante l'upload dell'immagine profilo");
                    http_response_code(500);

                }

                $utente -> closeConnectionToDatabase();
                echo("registrazione eseguita con successo");
                http_response_code(200);

                header('Location: ../../../frontEnd/utente/login.php');

            }
            else{
                    
                $utente -> closeConnectionToDatabase();
                echo("registrazione eseguita con successo");
                header('Location: ../../..frontEnd/utente/login.html');
                http_response_code(200);

            }
            

        }
        else{

            $utente -> closeConnectionToDatabase();
            echo("bad request");
            http_response_code(400);

        }
    }elseif ($tipo === "azienda") {
        require_once("../db/models/aziende.php");
        require_once("../fileSystem/storage/storageAziende.php");
    

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

            if(isset($_FILES["logo"]) && $_FILES["logo"]["error"] == 0){

                $result = $fs -> uploadAziendaFile($azienda->getAziendaId(), $_FILES["logo"]);

                if($result != 0){

                    $azienda -> closeConnectionToDatabase();
                    echo("errore durante l'upload del file");
                    http_response_code(500);

                }


                $result = $azienda -> setAziendaLogo($aziendaData["azienda_id"], $_FILES["logo"]["name"]);

                if($result != 0){
                            
                    $azienda -> closeConnectionToDatabase();
                    echo("errore durante l'upload dell'immagine profilo");
                    http_response_code(500);

                }

            }

                    
            $azienda -> closeConnectionToDatabase();
            echo("registrazione eseguita con successo");
            http_response_code(200);
            header('Location: ../../../frontEnd/azienda/login.php');
            

        }
        else{

            $azienda -> closeConnectionToDatabase();
            echo("bad request");
            http_response_code(400);

        }

    }   
    else {
        echo "Tipo non valido. Deve essere 'utente' o 'azienda'.";
        http_response_code(400);
    }

}

register();
?>
