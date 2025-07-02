<?php

require_once(__DIR__."/../../db/models/candidature.php");
require_once(__DIR__."/../../db/models/utenti.php");
require_once(__DIR__."/../../db/models/offerte.php");
require_once(__DIR__."/../../fileSystem/storage/storageUtenti.php");

function createCandidatura(){

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }


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
    $offerta = new Offerte();
    $candidaturaObj = new Candidature();

    if ($offerta -> connectToDatabase() != 0) {
        echo "Impossibile connettersi al database 0";
        http_response_code(500);
        exit;
    }

    $competenzeOffertaResult = $offerta -> getCompetenzeRichiesteByOffertaId($_POST['offerta_id']);

    if(!is_array($competenzeOffertaResult)){
        $offerta->closeConnectionToDatabase();
        echo "Impossibile trovare le competenze richieste dall'offerta";
        http_response_code(500);
        exit;
    }

    $competenzeOfferta = [];
    foreach($competenzeOffertaResult as $c) {
        $competenzeOfferta[] = $c["competenza"];
    }

    $offerta->closeConnectionToDatabase();

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

    if ($utente->fetchCompetenzeUtente() != 0) {
        $utente->closeConnectionToDatabase();
        echo "Impossibile recuperare le competenze dell'utente";
        http_response_code(500);
        exit;
    }

    $competenzeUtenteResult = $utente->getCompetenze();

    $competenzeUtente = [];

    foreach($competenzeUtenteResult as $c){

        $competenzeUtente[] = $c->getCompetenza();

    }

    //controllo competenze necessarie

    $allPresent = array_diff($competenzeUtente, $competenzeOfferta);

    if ($allPresent) {
        echo("L'utente non ha le competenze necessarie per candidarsi");
        http_response_code(200);
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

    $offertaId = intval($_POST['offerta_id']);
    $cvDocumentoId = $utente -> getConn() -> insert_id;
    $note = isset($_POST['note']) ? trim($_POST['note']) : null;

    $utente->closeConnectionToDatabase();

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
        header('location: ../../../frontEnd/utente/paginaCandidature.php');

    }
}

createCandidatura();

?>
