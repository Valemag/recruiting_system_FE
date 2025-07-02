<?php

require_once(__DIR__."/../../db/models/utenti.php");

function getCandidature(){

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['utente_id'])) {
        echo "non autenticato";
        http_response_code(401);
        header('Location: ../../../frontEnd/utente/login.html');
        return NULL;
    }
    
    $utente = new Utenti();
    
    if ($utente->connectToDatabase() != 0) {
        echo "connessione al database fallita";
        http_response_code(500);
        return NULL;
    }
    
    $utenteId = $_SESSION['utente_id'];
    
    if($utente -> getUtenteById($utenteId) != 0){

        echo "impossibile trovare l'utente";
        http_response_code(500);
        return NULL;

    } 

    $risultato = $utente->getCandidatureWithInfo();
    
    if (!is_array($risultato)) {
        
        return NULL;
    
    } else {
    
        return $risultato;
    
    }

}

?>

