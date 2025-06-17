<?php

require("../../db/models/aziende.php");
session_start();

// Controlla che l'utente sia autenticato
if (!isset($_SESSION['azienda_id'])) {
    http_response_code(401); // Unauthorized
    echo("non autenticato");
    exit;
}

// Connessione al database (usa i tuoi parametri

$aziendaId = $_SESSION['azienda_id'];
$campi = [];

$azienda = new Aziende();

if($azienda -> connectToDatabase() != 0){

    echo("connessione al database fallita");
    http_response_code(500);

}


if($azienda->getAziendaById($aziendaId) !=0){

    echo("impossibile recuperare le informazioni dell'utente");
    http_response_code(500);

}

// Filtra solo i campi ammessi
$campiAmmessi = ['email_contatto', 'password', 'username', 'nome', 'sito_web', 'descrizione', 'telefono_contatto', 'partita_iva', 'ragione_sociale'];
$datiAzienda = [];

foreach ($campiAmmessi as $campo) {
    if (isset($_POST[$campo])) {
        $datiAzienda[$campo] = $_POST[$campo];
    }
}

$azienda->populateFromArray($datiAzienda);

if($azienda -> updateAzienda() != 0){

    echo("impossibile aggiornare le informazioni dell'utente");
    http_response_code(500);

}
else{

    echo("utente aggiornato");
    http_response_code(200);

}

?>
