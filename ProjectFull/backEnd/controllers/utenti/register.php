<?php

    require("../../db/models/utenti.php");
    require("../../fileSystem/storage/storageUtenti.php");


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

        $result = $utente -> addUtente($_POST["username"], $_POST["nome"], $_POST["cognome"], $descrizione, $_POST["nTelefono"], $_POST["email"], $_POST["password"]);

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

        if(isset($_FILE["immagineProfilo"]) && $_FILE["immagineProfilo"]["error"] == 0){

            $result = $fs -> uploadUtenteFile($_FILE["immagineProfilo"]);

            if($result != 0){

                $utente -> closeConnectionToDatabase();
                echo("errore durante l'upload del file");
                http_response_code(500);

            }



            $result = $utente -> setUtenteProfileImage($_FILE["immagineProfilo"]["name"]);

            if($result != 0){
                            
                $utente -> closeConnectionToDatabase();
                echo("errore durante l'upload dell'immagine profilo");
                http_response_code(500);

            }

            $utente -> closeConnectionToDatabase();
            echo("registrazione eseguita con successo");
            http_response_code(200);

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

?>