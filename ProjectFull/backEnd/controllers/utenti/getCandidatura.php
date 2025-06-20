<?php

require_once("../../db/models/candidature.php");

function getCandidatura(){

    session_start();

    if (!isset($_SESSION['utente_id'])) {
        echo "non autenticato";
        http_response_code(401);
        exit;
    }
    
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        echo "id non valido";
        http_response_code(400);
        exit;
    }
    
    $candidaturaId = intval($_GET['id']);
    
    $candidatureObj = new Candidature();
    
    if (!$candidatureObj->connectToDatabase()) {
        echo "connessione al database fallita";
        http_response_code(500);
        exit;
    }
    
    $candidatura = $candidatureObj->getCandidaturaById($candidaturaId);
    
    header('Content-Type: application/json');
    echo json_encode($candidatura);

}

?>
