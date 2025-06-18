<?php

require_once("../../db/models/candidature.php");
require_once("../../db/models/utenti.php");

function createCandidatura(){

    session_start();


    if (!isset($_SESSION['utente_id'])) {
        http_response_code(401);
        echo "Utente non autenticato";
        exit;
    }

    $utenteId = $_SESSION['utente_id'];


    if (
        !isset($_POST['offerta_id']) ||
        !isset($_POST['cv_documento_id']) ||
        !is_numeric($_POST['offerta_id']) ||
        !is_numeric($_POST['cv_documento_id'])
    ) {
        http_response_code(400);
        echo "Parametri mancanti o non validi";
        exit;
    }

    $offertaId = intval($_POST['offerta_id']);
    $cvDocumentoId = intval($_POST['cv_documento_id']);
    $note = isset($_POST['note']) ? trim($_POST['note']) : null;

    $candidaturaObj = new Candidature();
    if ($candidaturaObj->connectToDatabase() !== 0) {
        http_response_code(500);
        echo "Errore di connessione alla tabella candidature";
        exit;
    }

    if ($candidaturaObj->addCandidatura($offertaId, $cvDocumentoId, $utenteId, $note) !== 0) {
        http_response_code(500);
        echo "Errore durante l'aggiunta della candidatura";
        exit;
    }
    else{

        http_response_code(200);
        echo "candidatura aggiunta con successo";

    }
}

?>
