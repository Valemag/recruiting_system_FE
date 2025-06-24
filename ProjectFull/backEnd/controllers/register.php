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
        require_once("../db/models/utenti.php");
        require_once("../db/models/competenzeUtente.php");
        require_once("../fileSystem/storage/storageUtenti.php");

        if((isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["username"]) && isset($_POST["nome"]) && 
            isset($_POST["cognome"]) && isset($_POST["descrizione"]) && isset($_POST["nTelefono"]) &&
            isset($_POST["competenze1"]) && isset($_POST["competenze2"]) && isset($_POST["competenze3"])) &&
            ($_POST["email"] != "" && $_POST["password"] != "" && $_POST["username"]!= "" && $_POST["nome"] != "" && 
            $_POST["cognome"] != "" && $_POST["nTelefono"]!= "" && $_POST["competenze1"]!= "" && $_POST["competenze2"]!= "" 
            && $_POST["competenze3"]!= "")
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

                $result = $utente -> addCompetenzaUtente($_POST["competenze1"]);

                if($result != 0){

                    $utente -> closeConnectionToDatabase();
                    echo("errore durante l'aggiunta della competenza 1");
                    http_response_code(500);

                }

                $result = $utente -> addCompetenzaUtente($_POST["competenze2"]);

                if($result != 0){

                    $utente -> closeConnectionToDatabase();
                    echo("errore durante l'aggiunta della competenza 2");
                    http_response_code(500);

                }

                $result = $utente -> addCompetenzaUtente($_POST["competenze3"]);

                if($result != 0){

                    $utente -> closeConnectionToDatabase();
                    echo("errore durante l'aggiunta della competenza 3");
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

                header('Location: ../../frontEnd/login.html');

            }
            else{
                    
                $utente -> closeConnectionToDatabase();
                echo("registrazione eseguita con successo");
                header('Location: ../../frontEnd/login.html');
                http_response_code(200);

            }
            

        }
        else{

            echo("bad request");
            http_response_code(400);

        }
    }elseif ($tipo === "azienda") {
        require_once("../db/models/aziende.php");
        require_once("../fileSystem/storage/storageAziende.php");
    

        if((isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["email_contatto"]) && isset($_POST["nome"]) && 
            isset($_POST["sito_web"]) && isset($_POST["descrizione"]) && isset($_POST["telefono_contatto"])
            && isset($_POST["ragione_sociale"]) && isset($_POST["partita_iva"]) && isset($_POST["paese_sede"])
            && isset($_POST["regione_sede"]) && isset($_POST["citta_sede"]) && isset($_POST["indirizzo_sede"])) &&
            ($_POST["email"] != "" && $_POST["password"] != "" && $_POST["email_contatto"]!= "" && $_POST["nome"] != ""
            && $_POST["telefono_contatto"]!= "" && $_POST["partita_iva"] != "" && $_POST["ragione_sociale"] != ""
            && $_POST["paese_sede"] != "" && $_POST["regione_sede"]!= "" && $_POST["citta_sede"] != "" && $_POST["indirizzo_sede"] != "")
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
                exit;
            }

            $result = $azienda -> addAzienda($_POST["nome"], $descrizione, $_POST["sito_web"], $_POST["email_contatto"], $_POST["telefono_contatto"], $_POST["ragione_sociale"], $_POST["partita_iva"], $_POST["email"], $_POST["password"]);

            if($result != 0) {

                $azienda ->  closeConnectionToDatabase();
                echo("errore durante la registrazione dell'azienda");
                http_response_code(500);
                exit;
            }

            // Recupera l'azienda tramite email
            $result = $azienda->getAziendaByEmail($_POST["email"]);

            // Controllo esistenza azienda
            if ($result != 0) {
                echo "Azienda non trovata.";
                http_response_code(404);
                exit;
            }

            $result = $azienda -> addSedeAzienda($_POST["paese_sede"], $_POST["regione_sede"], $_POST["citta_sede"], $_POST["indirizzo_sede"]);

            if($result != 0){

                $azienda ->  closeConnectionToDatabase();
                echo("errore durante la registrazione della sede aziendale");
                http_response_code(500);
                exit;
            }
                
            $fs -> createAziendaFolder($azienda->getAziendaId());

            if(isset($_FILES["logo"]) && $_FILES["logo"]["error"] == 0){

                $result = $fs -> uploadAziendaFile($azienda->getAziendaId(), $_FILES["logo"]);

                if($result != 0){

                    $azienda -> closeConnectionToDatabase();
                    echo("errore durante l'upload del file");
                    http_response_code(500);
                    exit;
                }


                $result = $azienda -> setAziendaLogo($_FILES["logo"]["name"]);

                if($result != 0){
                            
                    $azienda -> closeConnectionToDatabase();
                    echo("errore durante l'upload dell'immagine profilo");
                    http_response_code(500);
                    exit;
                }

            }

                    
            $azienda -> closeConnectionToDatabase();
            echo("registrazione eseguita con successo");
            http_response_code(200);
            header('Location: ../../frontEnd/login.html');
            

        }
        else{

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
