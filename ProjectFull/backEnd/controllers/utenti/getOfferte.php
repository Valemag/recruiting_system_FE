<?php

require_once(__DIR__."/../../db/models/offerte.php");

function getOfferte(){
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verifica che l'utente sia autenticato
    if (isset($_SESSION['utente_id'])) {

        $offerte = new Offerte();

        // Connessione al DB
        if ($offerte->connectToDatabase() != 0) {
            echo("connessione al database fallita");
            http_response_code(500);
            return NULL;
        }

        // Recupera le offerte dalla vista
        $risultato = $offerte->getUltimeOfferteConRequisiti();

        // Gestione errori o output
        if (!is_array($risultato)) {
    
            return NULL;

        } else {

            return $risultato;

        }
    }
    else{

        echo("non autorizzato");
        http_response_code(401); // Unauthorized
        header('Location: ../../../frontEnd/utente/login.html');
        return NULL;

    }

}


?>
