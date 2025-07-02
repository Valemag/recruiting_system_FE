<?php

require_once(__DIR__."/../GenericController.php");
require_once(__DIR__."/../../db/models/aziende.php");
require_once(__DIR__."/../../db/models/sediAziende.php");
require_once(__DIR__."/../../fileSystem/storage/storageAziende.php");

class ControllerAzienda extends GenericController 
{
    public static function ExtractId($var, $location): int {
        // Verifica che sia stato passato l'ID dell'offerta
        if (!isset($var) || !is_numeric($var)) {
            ControllerAzienda::return_with_status(400, $location);
            exit;
        }

        if (! ctype_digit($var)) {
            ControllerAzienda::return_with_status(400, $location);
            exit;
        }
        return intval($var);
    }

    public static function getInfoAzienda(): array|null {
        $aziendaId = ControllerAzienda::ExtractId($_GET["id"], '../../../frontEnd/login.html');
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

    public static function updateGenericInfo() {
        $rollbackLocation = '../../../frontEnd/azienda/modificaProfilo.php?id='.$_SESSION['azienda_id'];

        $azienda = new Aziende();
        if ($azienda->connectToDatabase() != 0) {
            ControllerAzienda::return_with_status(500, $rollbackLocation . '&update=failure');
            exit;
        }

        $azienda->populateFromArray($_POST);
        $azienda->setAziendaId($_SESSION['azienda_id']);
        $result = $azienda->updateAzienda();
        $azienda->closeConnectionToDatabase();


        if ($result != 0) {
            ControllerAzienda::return_with_status(500, $rollbackLocation . '&update=failure');
            exit;
        }

        ControllerAzienda::return_with_status(200, $rollbackLocation . '&update=success');
        exit;
    }

    public static function updateSedeAzienda() {
        $rollbackLocation = '../../../frontEnd/azienda/modificaProfilo.php?id='.$_SESSION['azienda_id'];

        $sedeAzienda = new SediAziende();
        if ($sedeAzienda->connectToDatabase() != 0) {
            ControllerAzienda::return_with_status(500, $rollbackLocation . '&update=failure');
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
            ControllerAzienda::return_with_status(500, $rollbackLocation . '&update=failure');
            exit;
        }

        ControllerAzienda::return_with_status(200, $rollbackLocation . '&update=success');
        exit;
    }

    public static function updateLogo() {
        $rollbackLocation = '../../../frontEnd/azienda/modificaProfilo.php?id='.$_SESSION['azienda_id'];
        $rollbackLocationFailure = $rollbackLocation . '&update=failure&message=';
        $rollbackLocationSuccess = $rollbackLocation . '&update=success';

        $storageAziende = new StorageAziende();
        if (!isset($_FILES["logo"]) || $_FILES["logo"]["error"] !== UPLOAD_ERR_OK) {
            ControllerAzienda::return_with_status(400, $rollbackLocationFailure . "image upload");
            exit;
        }
        $azienda = new Aziende();
        if ($azienda->connectToDatabase() != 0) {
            ControllerAzienda::return_with_status(500, $rollbackLocationFailure . "database connection");
            exit;
        }
        if(0 != $azienda->getAziendaById($_SESSION["azienda_id"])){
            $azienda->closeConnectionToDatabase();
            ControllerAzienda::return_with_status(500, $rollbackLocationFailure . "get azienda old logo");
            exit;
        }
        $oldLogo = $azienda->getLogo();

        if (0 != $storageAziende->uploadAziendaFile($_SESSION['azienda_id'], $_FILES["logo"])) {
            $azienda->closeConnectionToDatabase();
            ControllerAzienda::return_with_status(400, $rollbackLocationFailure . "update file");
            exit;
        }

        $result = $azienda->updateAziendaLogo($_FILES["logo"]["name"]);
        $azienda->closeConnectionToDatabase();

        if ($result != 0) {
            $storageAziende->deleteAziendaFile($_SESSION["azienda_id"], $_FILES["logo"]["name"]);
            ControllerAzienda::return_with_status(500, $rollbackLocationFailure . "update logo in database");
            exit;
        }

        $storageAziende->deleteAziendaFile($_SESSION["azienda_id"], $oldLogo);
        ControllerAzienda::return_with_status(200, $rollbackLocationSuccess);
        exit;
    }

    public static function updatePassword() {
        $rollbackLocation = '../../../frontEnd/azienda/modificaProfilo.php?id='.$_SESSION['azienda_id'];

        $azienda = new Aziende();
        if ($azienda->connectToDatabase() != 0) {
            ControllerAzienda::return_with_status(500, $rollbackLocation . '&update=failure');
            exit;
        }
        
        $azienda->setAziendaId($_SESSION['azienda_id']);

        if (!isset($_POST["password"]) || strlen($_POST["password"]) < 8 
            || trim($_POST["password"]) !== $_POST["password"]) {
            ControllerAzienda::return_with_status(400, $rollbackLocation . '&update=failure');
            exit;
        }

        $result = $azienda->updatePassword(password_hash($_POST["password"], PASSWORD_DEFAULT));
        $azienda->closeConnectionToDatabase();

        if ($result != 0) {
            ControllerAzienda::return_with_status(500, $rollbackLocation . '&update=failure');
            exit;
        }

        ControllerAzienda::return_with_status(200, $rollbackLocation . '&update=success');
        exit;
    }
}

// Handle multiple http methods.
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // If GET, do nothing by default.
        break;
    case 'POST':
        // Handle UPDATE info.
        switch ($_GET["op"]) {
            case "generic_info":
                ControllerAzienda::updateGenericInfo();
                break;
            case "password":
                ControllerAzienda::updatePassword();
                break;
            case "sede":
                ControllerAzienda::updateSedeAzienda();
                break;
            case "logo":
                ControllerAzienda::updateLogo();
                break;
            default:
                ControllerAzienda::return_with_status(405, '../../../frontEnd/azienda/paginaProfilo.php?id=' . $_SESSION['azienda_id']);
                exit;
        }
        break;
    default:
        // http method not supported.
        ControllerAzienda::return_with_status(405, '../../../frontEnd/azienda/paginaProfilo.php?id=' . $_SESSION['azienda_id']);
        exit;
}

?>