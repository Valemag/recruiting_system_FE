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

    $path = "/backEnd/fileSystem/";
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

function updateSedeAzienda() {
    $rollbackLocation = '../../../frontEnd/azienda/modificaProfilo.php?id='.$_SESSION['azienda_id'];

    $sedeAzienda = new SediAziende();
    if ($sedeAzienda->connectToDatabase() != 0) {
        return_with_status(500, $rollbackLocation . '&update=failure');
        exit;
    }

    $result = $sedeAzienda->updateSedeAziendaById(
        $_SESSION["azienda_id"], 
        $_POST["paese"], 
        $_POST["regione"], 
        $_POST["citta"], 
        $_POST["indirizzo"]
    );
    $sedeAzienda->closeConnectionToDatabase();

    if ($result !== 0) {
        return_with_status(500, $rollbackLocation . '&update=failure');
        exit;
    }

    return_with_status(200, $rollbackLocation . '&update=success');
    exit;
}

function updateLogo() {
    $rollbackLocation = '../../../frontEnd/azienda/modificaProfilo.php?id='.$_SESSION['azienda_id'];
    $rollbackLocationFailure = $rollbackLocation . '&update=failure&message=';
    $rollbackLocationSuccess = $rollbackLocation . '&update=success';

    $storageAziende = new StorageAziende();
    if (!isset($_FILES["logo"]) || $_FILES["logo"]["error"] !== UPLOAD_ERR_OK) {
        return_with_status(400, $rollbackLocationFailure . "image upload");
        exit;
    }
    $azienda = new Aziende();
    if ($azienda->connectToDatabase() != 0) {
        return_with_status(500, $rollbackLocationFailure . "database connection");
        exit;
    }
    if(0 != $azienda->getAziendaById($_SESSION["azienda_id"])){
        $azienda->closeConnectionToDatabase();
        return_with_status(500, $rollbackLocationFailure . "get azienda old logo");
        exit;
    }
    $oldLogo = $azienda->getLogo();

    if (0 != $storageAziende->uploadAziendaFile($_SESSION['azienda_id'], $_FILES["logo"])) {
        $azienda->closeConnectionToDatabase();
        return_with_status(400, $rollbackLocationFailure . "update file");
        exit;
    }

    $result = $azienda->updateAziendaLogo($_FILES["logo"]["name"]);
    $azienda->closeConnectionToDatabase();

    if ($result != 0) {
        $storageAziende->deleteAziendaFile($_SESSION["azienda_id"], $_FILES["logo"]["name"]);
        return_with_status(500, $rollbackLocationFailure . "update logo in database");
        exit;
    }

    $storageAziende->deleteAziendaFile($_SESSION["azienda_id"], $oldLogo);
    return_with_status(200, $rollbackLocationSuccess);
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

    if (!isset($_POST["password"]) || strlen($_POST["password"]) < 8 
        || trim($_POST["password"]) !== $_POST["password"]) {
        return_with_status(400, $rollbackLocation . '&update=failure');
        exit;
    }

    $result = $azienda->updatePassword(password_hash($_POST["password"], PASSWORD_DEFAULT));
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
            case "sede":
                updateSedeAzienda();
                break;
            case "logo":
                updateLogo();
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