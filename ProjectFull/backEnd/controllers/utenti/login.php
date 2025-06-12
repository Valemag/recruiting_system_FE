<?php
//in questi file ci sono le funzioni per comunicare al database
require(__DIR__."/../../db/models/utenti.php");

//Verifica che siano stati inviati l'email e la password
if(
    isset($_POST["email"]) && isset($_POST["password"]) &&
    $_POST["email"] != "" && $_POST["email"] != ""
) {
    $email = $_POST["email"];
    $password = $_POST["password"];

        $utenti = new Utenti();

        //Connessione al Database e recupero utente dal database (via email)
        
        if ($utenti -> connectToDatabase() != 0){
            echo "impossibile connettersi al database";
            http_response_code(500); //utente inesistente 
        }

        $result = $utenti -> getUtenteByEmail($email);

        //Verifica dell'esistenza dell'utente
        if ($result != 0){
            echo("Utente non trovato. ".$result);
            http_response_code(404); //utente inesistente 
        }
        //Verifica della password
        elseif (!password_verify($password, $utenti -> getPassword())) {
            echo "Password errata.";
            http_response_code(401); //Password sbagliata
        }
        //se è tutto corretto
        else{

            session_start();
            $_SESSION = $utenti -> toArray();

            unset($_SESSION["data_registrazione"]);

            http_response_code(200); //OK

            header('Location: ../../../frontEnd/utente/paginaProfilo.php?id='.$_SESSION["utente_id"]);
        }
    } else {
        //Se mancano email o password
        echo "Campi obbligatori mancanti.";
        http_response_code(403);
    }
?>