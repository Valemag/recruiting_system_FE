<?php
session_start();

require_once("../../db/models/utenti.php");
require_once("../../db/models/aziende.php");

// Verifica metodo
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Metodo non supportato";
    http_response_code(405);
    exit;
}

function updateProfilo(){

    // Identificazione attore (utente o azienda)
    if (isset($_SESSION['utente_id'])) {
        $id = $_SESSION['utente_id'];
        $account = new Utenti();
        $update = [$account, 'updateUtente'];
        $getById = [$account, 'getUtenteById'];
    } elseif (isset($_SESSION['azienda_id'])) {
        $id = $_SESSION['azienda_id'];
        $account = new Aziende();
        $update = [$account, 'updateAzienda'];
        $getById = [$account, 'getAziendaById'];
    } else {
        echo "Non autenticato";
        http_response_code(401);
        exit;
    }

    // Connessione al DB
    if ($account->connectToDatabase() != 0) {
        echo "Connessione al database fallita";
        http_response_code(500);
        exit;
    }

    // Recupero dati attuali
    if (call_user_func($getById, $id) != 0) {
        echo "Account non trovato";
        $account -> closeConnectionToDatabase();
        http_response_code(404);
        exit;
    }

    // Recupero nuovi dati dal body POST
    $data = $_POST; 
    if (!$data || !is_array($data)) {
        echo "Dati mancanti o non validi";
        $account -> closeConnectionToDatabase();
        http_response_code(400);
        exit;
    }

    $account->populateFromArray($data);

    // Esecuzione aggiornamento
    if (call_user_func($update) != 0) {
        echo "Errore durante l'aggiornamento del profilo";
        $account -> closeConnectionToDatabase();
        http_response_code(500);
        exit;
    }

    echo "Profilo aggiornato con successo";
    $account -> closeConnectionToDatabase();
    http_response_code(200);

}

updateProfilo();

?>