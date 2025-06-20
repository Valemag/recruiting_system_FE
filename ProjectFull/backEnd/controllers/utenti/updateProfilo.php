<?php

require("../../db/models/utenti.php");

function updateProfilo(){

    session_start();

    // Controlla che l'utente sia autenticato
    if (!isset($_SESSION['utente_id'])) {
        http_response_code(401); // Unauthorized
        echo("non autenticato");
        exit;
    }

    // Connessione al database (usa i tuoi parametri

    $utenteId = $_SESSION['utente_id'];
    $campi = [];

    $utente = new Utenti();

    if($utente -> connectToDatabase() != 0){

        echo("connessione al database fallita");
        http_response_code(500);

    }


    if($utente->getUtenteById($utenteId) !=0){

        echo("impossibile recuperare le informazioni dell'utente");
        http_response_code(500);

    }

    // Filtra solo i campi ammessi
    $campiAmmessi = ['email', 'password', 'username', 'nome', 'cognome', 'descrizione', 'telefono_contatto'];
    $datiUtente = [];

    foreach ($campiAmmessi as $campo) {
        if (isset($_POST[$campo])) {
            $datiUtente[$campo] = $_POST[$campo];
        }
    }

    $utente->populateFromArray($datiUtente);

    if($utente -> updateUtente() != 0){

        echo("impossibile aggiornare le informazioni dell'utente");
        http_response_code(500);

    }
    else{

        echo("utente aggiornato");
        http_response_code(200);

    }

}

?>
