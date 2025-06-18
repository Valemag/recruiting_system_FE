<?php

require_once(__DIR__."/../../db/models/candidature.php");

function getCandidature(){

    session_start();

    if (!isset($_SESSION['utente_id'])) {
        echo "non autenticato";
        http_response_code(401);
        header('Location: ../../../frontEnd/utente/login.html');
        return NULL;
    }
    
    $candidatureObj = new Candidature();
    
    if (!$candidatureObj->connectToDatabase()) {
        echo "connessione al database fallita";
        http_response_code(500);
        return NULL;
    }
    
    $utenteId = $_SESSION['utente_id'];
    
    $risultato = $candidatureObj->getCandidatureByUtenteId($utenteId);
    
    if (!is_array($risultato)) {
        
        return NULL;
    
    } else {
    
        return $risultato;
    
    }

}

?>

