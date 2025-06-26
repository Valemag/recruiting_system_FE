<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__."/../../db/models/offerte.php");
require_once(__DIR__."/../../db/models/competenzeOfferta.php");

function return_with_status($status_code, $locationFile) {
    http_response_code(response_code: $status_code);
    header('Location: ' . $locationFile);
}

function VerifyAuthentication() {
    if (!isset($_SESSION['utente_id']) && !isset($_SESSION['azienda_id'])) {
        echo("non autenticato");
        return_with_status(401, '../../../frontEnd/login.html');
        exit;
    }
}

function ExtractId($var, $location): int {
    // Verifica che sia stato passato l'ID dell'offerta
    if (!isset($var) || !is_numeric($var)) {
        return_with_status(400, $location);
        exit;
    }

    if (! ctype_digit($var)) {
        return_with_status(400, $location);
        exit;
    }
    return intval($var);
}

function initCompetenzeList(): array {
    $competenze = array();

    if (!empty($_POST["competenze1"])) {
        $competenze[] = $_POST["competenze1"];
    }

    if (!empty($_POST["competenze2"])) {
        $competenze[] = $_POST["competenze2"];
    }

    if (!empty($_POST["competenze3"])) {
        $competenze[] = $_POST["competenze3"];
    }

    return $competenze;
}

function addOfferta() {
    $rollbackLocation =  '../../../frontEnd/azienda/nuovaOffertaLavoro.php?id='.$_SESSION['azienda_id'];

    $offerta = new Offerte();
    $aziendaId = $_SESSION['azienda_id'];

    if ($offerta -> connectToDatabase() != 0) {
        return_with_status(500, $rollbackLocation);
        exit;
    }

    $competenze = initCompetenzeList();

    if (empty($_GET["sedeId"])) {
        return_with_status(400, $rollbackLocation);
        exit;
    }

    $result = $offerta -> addOfferta($aziendaId, 
                                    $_POST["titolo"], 
                                    $_POST["descrizione"], 
                                    $competenze,
                                    $_GET["sedeId"],
                                    $_POST["retribuzione"],
                                    $_POST["tipo_contratto"],
                                    $_POST["data_scadenza"],
                                    $_POST["modalita_lavoro"]);
    if($result != 0){
        return_with_status(500, $rollbackLocation);
        exit;
    }

    return_with_status(200, '../../../frontEnd/azienda/paginaOfferte.php?id='.$_SESSION['azienda_id']);
    exit;
}

function updateOfferta() {
    $rollbackLocation = '../../../frontEnd/azienda/modificaOffertaLavoro.php?id='.$_SESSION['azienda_id'] . '&offerta='.$_GET['offerta'];

    $offertaId = ExtractId($_GET['offerta'], $rollbackLocation . '&update=failure');
    $offerte = new Offerte();

    $offerte->populateFromArray($_POST);
    $offerte->setOffertaId($offertaId);
    $competenze = initCompetenzeList();

    if ($offerte->connectToDatabase() != 0) {
        return_with_status(500, $rollbackLocation . '&update=failure');
        exit;
    }
    if ($offerte->updateOfferta($competenze) != 0) {
       return_with_status(500, $rollbackLocation . '&update=failure');
       exit;
    }

    return_with_status(200, $rollbackLocation . '&update=success');
    exit;
}

function deleteOfferta() {
    $rollbackLocation = '../../../frontEnd/azienda/paginaOfferte.php?id='.$_SESSION['azienda_id'];

    $offerta = new Offerte();

    if ($offerta->connectToDatabase() != 0) {
        return_with_status(500, $rollbackLocation);
        exit;
    }

    $offertaId = ExtractId($_POST['offerta_id'], $rollbackLocation);
    $offerta->setOffertaId($offertaId);
    $result = $offerta->deleteOfferta();

    if($result != 0){
        return_with_status(500, $rollbackLocation);
        exit;
    }

    return_with_status(200, $rollbackLocation);
    exit;
}

function getOffertaForModifica(): array|null {
    $rollbackLocation = '../../../frontEnd/azienda/paginaOfferte.php?id='.$_SESSION['azienda_id'];

    $offertaId = ExtractId($_GET['offerta'], $rollbackLocation);
    $offerte = new Offerte();

    if ($offerte->connectToDatabase() != 0) {
        echo("connessione al database fallita");
        return_with_status(500, $rollbackLocation);
        exit;
    }

    $result = $offerte->getOffertaById($offertaId);

    if ($result != 0) {
        echo("offerta non trovata");
        return_with_status(404, $rollbackLocation);
        return NULL;
    } 

    $competenzeOfferta = new CompetenzeOfferta();
    if ($competenzeOfferta->connectToDatabase() != 0) {
        echo("connessione al database fallita");
        return_with_status(500, $rollbackLocation);
        exit;
    }
    $arrayCompetenze = $competenzeOfferta->getCompetenzeByOffertaId($offertaId);

    if (!is_array($arrayCompetenze)) {
        echo("competenze non trovate");
        return_with_status(404, $rollbackLocation);
        return NULL;
    }

    http_response_code(200);
    return [
        "offerta" => $offerte->toArray(),
        "competenze" => $arrayCompetenze
    ];
}

function getOfferta(): array|null {
    $rollbackLocation = '../../../frontEnd/azienda/paginaOfferte.php?id='.$_SESSION['azienda_id'];

    $offertaId = ExtractId($_GET['offerta'], $rollbackLocation);
    $offerte = new Offerte();

    if ($offerte->connectToDatabase() != 0) {
        echo("connessione al database fallita");
        return_with_status(500, $rollbackLocation);
        exit;
    }

    $datiOfferta = $offerte->getOffertaConRequisitiById($offertaId);

    if (!is_array($datiOfferta)) {
        echo("offerta non trovata");
        return_with_status(404, $rollbackLocation);
        return NULL;
    } 
    http_response_code(200);
    return $datiOfferta;
}

function getOfferte(){
    $offerte = new Offerte();

    // Connessione al DB
    if ($offerte->connectToDatabase() != 0) {
        echo("connessione al database fallita");
        http_response_code(500);
        return NULL;
    }

    // Recupera le offerte dalla vista
    $risultato = $offerte->getOfferteAzienda($_SESSION['azienda_id']);

    // Gestione errori o output
    if (!is_array($risultato)) {
        return NULL;
    } 
    else {
        return $risultato;
    }
}

VerifyAuthentication();

// Handle multiple http methods.
$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        // If GET, do nothing by default.
        break;
    case 'POST':
        // Handle ADD offerta.
        addOfferta();
        break;
    case 'PUT':
        // Handle UPDATE offerta.
        updateOfferta();
        break;
    case 'DELETE':
        // Handle DELETE offerta.
        deleteOfferta();
        break;
    default:
        // http method not supported.
        return_with_status(405, '../../../frontEnd/azienda/paginaProfilo.php?id=' . $_SESSION['azienda_id']);
        exit;
}

?>
