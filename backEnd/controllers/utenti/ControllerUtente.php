<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__."/../../db/models/utenti.php");
require_once(__DIR__."/../../fileSystem/storage/storageUtenti.php");

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

function getInfoUtenteBySession(){
    $utenti = new Utenti();
    $storageUtenti = new StorageUtenti();

    $userId = $_SESSION["utente_id"];

    if ($utenti->connectToDatabase() != 0) {
        echo "Connessione al database fallita";
        http_response_code(500);
        return NULL;
    }
        
    $result = $utenti -> getUtenteById($userId);

    if($result != 0){
        $utenti -> closeConnectionToDatabase();
        echo("errore durante il fetching dell'utente");
        http_response_code(500);
        return NULL;
    }

    $result = $utenti -> fetchCompetenzeUtente();

    if($result != 0){
        //echo("errore durante il fetching delle competenze");
    }

    $result = $utenti -> fetchDocumentiUtente();

    if($result != 0){
        // echo("errore durante il fetching dei documenti");
        // http_response_code(500);
        // return NULL;
    }

    $userData = $utenti -> toArray();

    unset($userData["password"]);
    unset($userData["data_registrazione"]);

    $path = "/backEnd/fileSystem/";
    $path .= $storageUtenti -> getUploadsPath();
    $path .= $storageUtenti -> getUtenteFolderPlaceholder();
    $path .= $userId."/";

    if(isset($userData["documenti"])){
        foreach($userData["documenti"] as &$documento){
            $nomeFile = $documento->getDocumento();
            $documentoId = $documento->getDocumentoId();

            $documento = [
                "path" => $path . $nomeFile,
                "nome" => $nomeFile,
                "id" => $documentoId
            ];
        }
    }

    if($userData["immagine_profilo"] != NULL){
        $userData["immagine_profilo"] = $path . $userData["immagine_profilo"];
    }
    else{
        unset($userData["immagine_profilo"]);
    }

    $utenti -> closeConnectionToDatabase();
    return $userData;
}

function updateGenericInfo() {
   $rollbackLocation = '../../../frontEnd/utente/modificaProfilo.php';

    $utente = new Utenti();
    if ($utente->connectToDatabase() != 0) {
        return_with_status(500, $rollbackLocation . '?update=failure');
        exit;
    }

    $utente->populateFromArray($_POST);
    $utente->setUtenteId($_SESSION['utente_id']);
    $result = $utente->updateUtente();
    $utente->closeConnectionToDatabase();


    if ($result != 0) {
        return_with_status(500, $rollbackLocation . '?update=failure');
        exit;
    }

    return_with_status(200, $rollbackLocation . '?update=success');
    exit;
}

function updateImmagineProfilo() {
    $rollbackLocation = '../../../frontEnd/utente/modificaProfilo.php';
    $rollbackLocationFailure = $rollbackLocation . '?update=failure';

    $storageUtenti = new StorageUtenti();
    if (!isset($_FILES["immagine_profilo"]) || $_FILES["immagine_profilo"]["error"] !== UPLOAD_ERR_OK) {
        return_with_status(400, $rollbackLocationFailure);
        exit;
    }
    $utente = new Utenti();
    if ($utente->connectToDatabase() != 0) {
        return_with_status(500, $rollbackLocationFailure);
        exit;
    }
    if(0 != $utente->getUtenteById($_SESSION["utente_id"])){
        $utente->closeConnectionToDatabase();
        return_with_status(500, $rollbackLocationFailure);
        exit;
    }
    $oldImg = $utente->getImmagineProfilo();

    if (0 != $storageUtenti->uploadUtenteFile($_SESSION['utente_id'], $_FILES["immagine_profilo"])) {
        $utente->closeConnectionToDatabase();
        return_with_status(400, $rollbackLocationFailure);
        exit;
    }

    $result = $utente->updateImmagineProfilo($_FILES["immagine_profilo"]["name"]);
    $utente->closeConnectionToDatabase();

    if ($result != 0) {
        $storageUtenti->deleteUtenteFile($_SESSION["utente_id"], $_FILES["immagine_profilo"]["name"]);
        return_with_status(500, $rollbackLocationFailure);
        exit;
    }

    $storageUtenti->deleteUtenteFile($_SESSION["utente_id"], $oldImg);
    return_with_status(200, $rollbackLocation  . '?update=success');
    exit;
}

function updateCompetenze() {
    $rollbackLocation = '../../../frontEnd/utente/modificaProfilo.php';

    $utente = new Utenti();
    if ($utente->connectToDatabase() != 0) {
        return_with_status(500, $rollbackLocation . '?update=failure');
        exit;
    }
    $utente->setUtenteId($_SESSION["utente_id"]);
    $result = $utente->updateCompetenze(initCompetenzeList());
    $utente->closeConnectionToDatabase();
    if (0 != $result) {
        return_with_status(500, $rollbackLocation . '?update=failure');
        exit;
    }

    return_with_status(200, $rollbackLocation . '?update=success');
    exit;
}

function updatePassword() {
    $rollbackLocation = '../../../frontEnd/utente/modificaProfilo.php';

    $utente = new Utenti();
    if ($utente->connectToDatabase() != 0) {
        return_with_status(500, $rollbackLocation . '?update=failure');
        exit;
    }    
    $utente->setUtenteId($_SESSION['utente_id']);

    if (!isset($_POST["password"]) || strlen($_POST["password"]) < 8 
        || trim($_POST["password"]) !== $_POST["password"]) {
        return_with_status(400, $rollbackLocation . '?update=failure');
        exit;
    }

    $result = $utente->updatePassword(password_hash($_POST["password"], PASSWORD_DEFAULT));
    $utente->closeConnectionToDatabase();

    if ($result != 0) {
        return_with_status(500, $rollbackLocation . '?update=failure');
        exit;
    }

    return_with_status(200, $rollbackLocation . '?update=success');
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
            case "competenze":
                updateCompetenze();
                break;
            case "immagine_profilo":
                updateImmagineProfilo();
                break;
            default:
                return_with_status(405, '../../../frontEnd/utente/paginaProfilo.php?id=' . $_SESSION['utente_id']);
                exit;
        }
        break;
    default:
        // http method not supported.
        return_with_status(405, '../../../frontEnd/utente/paginaProfilo.php?id=' . $_SESSION['utente_id']);
        exit;
}

?>