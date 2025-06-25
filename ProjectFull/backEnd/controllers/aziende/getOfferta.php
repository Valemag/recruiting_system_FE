<?php

require_once(__DIR__."/../../db/models/offerte.php");
require_once(__DIR__."/../../db/models/competenzeOfferta.php");

function return_with_error($status_code) {
    http_response_code(response_code: $status_code);
    header('Location: ../../../frontEnd/azienda/paginaOfferte.php?id='.$_SESSION['azienda_id']);
}

function getOffertaForModifica(): array|null {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verifica autenticazione
    if (!isset($_SESSION['utente_id']) && !isset($_SESSION['azienda_id'])) {
        echo("non autenticato");
        http_response_code(401);
        header('Location: ../../../frontEnd/login.html');
        exit;
    }
    $offertaId = 0;

    // Verifica che sia stato passato l'ID dell'offerta
    if (!isset($_GET['offerta']) || !is_numeric($_GET['offerta'])) {
        echo("parametri mancanti o non validi");
        return_with_error(400);
        exit;
    }

    $offertaId = intval($_GET['offerta']);
    $offerte = new Offerte();

    if ($offerte->connectToDatabase() != 0) {
        echo("connessione al database fallita");
        return_with_error(500);
        exit;
    }

    $result = $offerte->getOffertaById($offertaId);

    if ($result != 0) {
        echo("offerta non trovata");
        return_with_error(404);
        return NULL;
    } 

    $competenzeOfferta = new CompetenzeOfferta();
    if ($competenzeOfferta->connectToDatabase() != 0) {
        echo("connessione al database fallita");
        return_with_error(500);
        exit;
    }
    $arrayCompetenze = $competenzeOfferta->getCompetenzeByOffertaId($offertaId);

    if (!is_array($arrayCompetenze)) {
        echo("competenze non trovate");
        return_with_error(404);
        return NULL;
    }

    http_response_code(200);
    return [
        "offerta" => $offerte->toArray(),
        "competenze" => $arrayCompetenze
    ];
}

function getOfferta(): array|null {

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verifica autenticazione
    if (!isset($_SESSION['utente_id']) && !isset($_SESSION['azienda_id'])) {
        echo("non autenticato");
        http_response_code(401);
        header('Location: ../../../frontEnd/login.html');
        exit;
    }
    $offertaId = 0;

    // Verifica che sia stato passato l'ID dell'offerta
    if (!isset($_GET['offerta']) || !is_numeric($_GET['offerta'])) {
        echo("parametri mancanti o non validi");
        return_with_error(400);
        exit;
    }

    $offertaId = intval($_GET['offerta']);
    $offerte = new Offerte();

    if ($offerte->connectToDatabase() != 0) {
        echo("connessione al database fallita");
        return_with_error(500);
        exit;
    }

    $datiOfferta = $offerte->getOffertaConRequisitiById($offertaId);

    if (!is_array($datiOfferta)) {
        echo("offerta non trovata");
        return_with_error(404);
        return NULL;
    } 
    http_response_code(200);
    return $datiOfferta;

}

?>
