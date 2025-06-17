<?php

require_once("../../db/models/offerte.php");
session_start();

// Verifica che l'utente sia autenticato
if (!isset($_SESSION['utente_id'])) {
    echo("non autorizzato");
    http_response_code(401); // Unauthorized
    exit;
}

$offerte = new Offerte();

// Connessione al DB
if ($offerte->connectToDatabase() != 0) {
    echo("connessione al database fallita");
    http_response_code(500);
    exit;
}

// Recupera le offerte dalla vista
$risultato = $offerte->getUltimeOfferteConRequisiti();

// Gestione errori o output
if (!is_array($risultato)) {
    http_response_code(500);
    echo("impossibile reperire le informazioni sulle offerte");
    exit;
} else {
    http_response_code(200);
    echo(json_encode($risultato));
}

?>
