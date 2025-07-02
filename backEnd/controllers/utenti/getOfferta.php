<?php

require_once("../../db/models/offerte.php");

function getOfferta(){

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verifica autenticazione
    if (!isset($_SESSION['utente_id'])) {
        echo("non autenticato");
        return NULL;
    }

    // Verifica che sia stato passato l'ID dell'offerta
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        echo("parametri mancanti o non validi");
        return NULL;
    }

    $offertaId = intval($_GET['id']);

    $offerte = new Offerte();

    if ($offerte->connectToDatabase() != 0) {
        echo("connessione al database fallita");
        return NULL;
    }

    $datiOfferta = $offerte->getOffertaConRequisitiById($offertaId);

    if (is_array($datiOfferta)) {
        return $datiOfferta;
    } else {
        return NULL;
    }

}

?>
