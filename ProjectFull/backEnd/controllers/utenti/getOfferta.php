<?php

require_once("../../db/models/offerte.php");

function getOfferta(){

    session_start();

    // Verifica autenticazione
    if (!isset($_SESSION['utente_id'])) {
        echo("non autenticato");
        http_response_code(500);
        exit;
    }

    // Verifica che sia stato passato l'ID dell'offerta
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        echo("parametri mancanti o non validi");
        http_response_code(400);
        exit;
    }

    $offertaId = intval($_GET['id']);

    $offerte = new Offerte();

    if ($offerte->connectToDatabase() != 0) {
        echo("connessione al database fallita");
        http_response_code(500);
        exit;
    }

    $datiOfferta = $offerte->getOffertaConRequisitiById($offertaId);

    if (is_array($datiOfferta)) {
        echo("offerta non trovata");
        http_response_code(404);
        exit;
    } else {
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode($datiOfferta);
    }

}

?>
