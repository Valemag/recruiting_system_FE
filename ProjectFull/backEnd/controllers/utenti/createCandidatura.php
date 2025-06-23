<?php

require_once(__DIR__."/../../db/models/candidature.php");
require_once(__DIR__."/../../db/models/utenti.php");
require_once(__DIR__."/../../fileSystem/storage/storageUtenti.php");

function createCandidatura(){

    session_start();


    if (!isset($_SESSION['utente_id'])) {
        http_response_code(401);
        echo "Utente non autenticato";
        exit;
    }


    if (
        !isset($_POST['offerta_id']) ||
        !isset($_FILES['file']) ||
        !is_numeric($_POST['offerta_id'])
    ) {
        http_response_code(400);
        echo "Parametri mancanti o non validi";
        exit;
    }

    $utenteId = $_SESSION['utente_id'];
    $file = $_FILES['file'];
    $storage = new StorageUtenti();
    $utente = new Utenti();
    $candidaturaObj = new Candidature();

    if ($utente -> connectToDatabase() != 0) {
        echo "Impossibile connettersi al database 1";
        http_response_code(500);
        exit;
    }

    if ($utente->getUtenteById($utenteId) != 0) {
        $utente->closeConnectionToDatabase();
        echo "Impossibile connettersi al database";
        http_response_code(500);
        exit;
    }

    if ($storage->uploadUtenteFile($utenteId, $file) != 0) {
        $utente->closeConnectionToDatabase();
        echo "Errore nel salvataggio del file";
        http_response_code(500);
        exit;
    }

    if ($utente->addDocumento($file["name"]) != 0) {
        $utente->closeConnectionToDatabase();
        echo "Errore nella registrazione del file nel database";
        http_response_code(500);
        exit;
    }

    $utente->closeConnectionToDatabase();

    $offertaId = intval($_POST['offerta_id']);
    $cvDocumentoId = $utente -> getConn() -> insert_id;
    $note = isset($_POST['note']) ? trim($_POST['note']) : null;

    if ($candidaturaObj->connectToDatabase() !== 0) {
        http_response_code(500);
        echo "Errore di connessione alla tabella candidature 2";
        exit;
    }

    if ($candidaturaObj->addCandidatura($offertaId, $cvDocumentoId, $utenteId, $note) !== 0) {
        $candidaturaObj->closeConnectionToDatabase();
        http_response_code(500);
        echo "Errore durante l'aggiunta della candidatura";
        exit;
    }
    else{

        http_response_code(200);
        echo "candidatura aggiunta con successo";

    }
}

createCandidatura();

?>
