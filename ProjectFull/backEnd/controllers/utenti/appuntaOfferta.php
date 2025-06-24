<?php
require_once("../../db/models/utenti.php");

function appuntaOfferta(){

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verifica autenticazione
    if (!isset($_SESSION['utente_id'])) {
        http_response_code(401);
        echo "Utente non autenticato";
        exit;
    }

    // Verifica parametro offerta_id
    if (!isset($_POST['offerta_id']) || !is_numeric($_POST['offerta_id'])) {
        http_response_code(400);
        echo "Parametro offerta_id mancante o non valido";
        exit;
    }

    $utenteId = $_SESSION['utente_id'];
    $offertaId = intval($_POST['offerta_id']);

    // Istanzia e connette l'oggetto Utenti
    $utente = new Utenti();

    if ($utente->connectToDatabase() !== 0) {
        http_response_code(500);
        echo "Connessione al database fallita";
        exit;
    }

    // Appunta l'offerta
    if ($utente->appuntaOfferta($utenteId, $offertaId)) {
        http_response_code(200);
        echo "Offerta appuntata con successo";
    } else {
        http_response_code(500);
        echo "Errore durante l'appuntamento dell'offerta";
    }

}

?>
