<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__."/../../db/models/aziende.php");
require_once(__DIR__."/../../db/models/sediAziende.php");
require_once(__DIR__."/../../fileSystem/storage/storageAziende.php");

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

function getInfoAzienda(): array|null {
    $aziendaId = ExtractId($_GET["id"], '../../../frontEnd/login.html');
    $aziende = new Aziende();
    $storageAziende = new StorageAziende();

    if ($aziende->connectToDatabase() != 0) {
        http_response_code(500);
        return NULL;
    }
    
    $result = $aziende -> getAziendaById($aziendaId);

    if($result != 0){
        $aziende -> closeConnectionToDatabase();
        http_response_code(500);
        return NULL;
    }

    $result = $aziende -> fetchOfferteAzienda();

    if($result != 0){
        //echo("errore durante il fetching delle offerte");
    }

    $result = $aziende->fetchSediAzienda();

    if($result != 0){
        $aziende -> closeConnectionToDatabase();
        http_response_code(500);
        return NULL;
    }
    $aziendaData = $aziende -> toArray();

    $path = "../../bitByte/backEnd/fileSystem/";
    $path .= $storageAziende -> getUploadsPath();
    $path .= $storageAziende -> getAziendaFolderPlaceholder();
    $path .= $aziendaId."/";

    if($aziendaData["logo"] != NULL){
        $aziendaData["logo"] = $path . $aziendaData["logo"];
    }
    else{
        unset($aziendaData["logo"]);
    }
    $aziende -> closeConnectionToDatabase();

    return $aziendaData;
}

function updateGenericInfo() {
    $rollbackLocation = '../../../frontEnd/azienda/modificaProfilo.php?id='.$_SESSION['azienda_id'];

    $azienda = new Aziende();
    if ($azienda->connectToDatabase() != 0) {
        return_with_status(500, $rollbackLocation . '&update=failure');
        exit;
    }

    $azienda->populateFromArray($_POST);
    $azienda->setAziendaId($_SESSION['azienda_id']);
    $result = $azienda->updateAzienda();
    $azienda->closeConnectionToDatabase();


    if ($result != 0) {
        return_with_status(500, $rollbackLocation . '&update=failure');
        exit;
    }

    return_with_status(200, $rollbackLocation . '&update=success');
    exit;
}

function updatePassword() {
    $rollbackLocation = '../../../frontEnd/azienda/modificaProfilo.php?id='.$_SESSION['azienda_id'];

    $azienda = new Aziende();
    if ($azienda->connectToDatabase() != 0) {
        return_with_status(500, $rollbackLocation . '&update=failure');
        exit;
    }
    
    $azienda->setAziendaId($_SESSION['azienda_id']);

    $result = $azienda->updatePassword($_POST["password"]);
    $azienda->closeConnectionToDatabase();

    if ($result != 0) {
        return_with_status(500, $rollbackLocation . '&update=failure');
        exit;
    }

    return_with_status(200, $rollbackLocation . '&update=success');
    exit;
}

VerifyAuthentication();

// Handle multiple http methods.
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // If GET, do nothing by default.
        break;
    case 'POST':
        // Handle UPDATE info.
        switch ($_GET["op"]) {
            case "generic_info":
                updateGenericInfo();
                break;
            case "password":
                updatePassword();
                break;
            default:
                return_with_status(405, '../../../frontEnd/azienda/paginaProfilo.php?id=' . $_SESSION['azienda_id']);
                exit;
        }
        break;
    default:
        // http method not supported.
        return_with_status(405, '../../../frontEnd/azienda/paginaProfilo.php?id=' . $_SESSION['azienda_id']);
        exit;
}

?>