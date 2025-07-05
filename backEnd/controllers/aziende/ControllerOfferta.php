<?php

require_once(__DIR__."/../GenericController.php");
require_once(__DIR__."/../../db/models/offerte.php");
require_once(__DIR__."/../../db/models/competenzeOfferta.php");
require_once(__DIR__."/../../db/models/aziende.php");
require_once(__DIR__."/../../db/models/candidature.php");
require_once(__DIR__."/../../fileSystem/storage/storageAziende.php");

class ControllerOfferta extends GenericController 
{
    public static function ExtractId($var, $location): int {
        // Verifica che sia stato passato l'ID dell'offerta
        if (!isset($var) || !is_numeric($var)) {
            ControllerOfferta::return_with_status(400, $location);
            exit;
        }

        if (! ctype_digit($var)) {
            ControllerOfferta::return_with_status(400, $location);
            exit;
        }
        return intval($var);
    }

    private static function initCompetenzeList(): array {
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

    public static function addOfferta() {
        $rollbackLocation =  '../../../frontEnd/azienda/nuovaOffertaLavoro.php?id='.$_SESSION['azienda_id'];

        $offerta = new Offerte();
        $aziendaId = $_SESSION['azienda_id'];

        if ($offerta -> connectToDatabase() != 0) {
            ControllerOfferta::return_with_status(500, $rollbackLocation);
            exit;
        }

        $competenze = ControllerOfferta::initCompetenzeList();
        if (count($competenze) !== 3) {
            ControllerOfferta::return_with_status(400, $rollbackLocation);
            exit;
        }

        if (empty($_GET["sedeId"])) {
            ControllerOfferta::return_with_status(400, $rollbackLocation);
            exit;
        }

        $result = $offerta -> addOfferta($aziendaId, 
                                        $_POST["titolo"], 
                                        $_POST["descrizione"], 
                                        $competenze,
                                        $_GET["sedeId"],
                                        $_POST["retribuzione"],
                                        $_POST["tipo_contratto"],
                                        $_POST["modalita_lavoro"]);
        if($result != 0){
            ControllerOfferta::return_with_status(500, $rollbackLocation);
            exit;
        }

        ControllerOfferta::return_with_status(200, '../../../frontEnd/azienda/paginaOfferte.php?id='.$_SESSION['azienda_id']);
        exit;
    }

    public static function updateOfferta() {
        $rollbackLocation = '../../../frontEnd/azienda/modificaOffertaLavoro.php?id='.$_SESSION['azienda_id'] . '&offerta='.$_GET['offerta'];

        $offertaId = ControllerOfferta::ExtractId($_GET['offerta'], $rollbackLocation . '&update=failure');
        $offerte = new Offerte();

        $offerte->populateFromArray($_POST);
        $offerte->setOffertaId($offertaId);
        $competenze = ControllerOfferta::initCompetenzeList();
        if (count($competenze) !== 3) {
            ControllerOfferta::return_with_status(400, $rollbackLocation);
            exit;
        }

        if ($offerte->connectToDatabase() != 0) {
            ControllerOfferta::return_with_status(500, $rollbackLocation . '&update=failure');
            exit;
        }
        if ($offerte->updateOfferta($competenze) != 0) {
            ControllerOfferta::return_with_status(500, $rollbackLocation . '&update=failure');
            exit;
        }

        ControllerOfferta::return_with_status(200, $rollbackLocation . '&update=success');
        exit;
    }

    public static function deleteOfferta() {
        $rollbackLocation = '../../../frontEnd/azienda/paginaOfferte.php?id='.$_SESSION['azienda_id'];

        $offerta = new Offerte();

        if ($offerta->connectToDatabase() != 0) {
            ControllerOfferta::return_with_status(500, $rollbackLocation);
            exit;
        }

        $offertaId = ControllerOfferta::ExtractId($_POST['offerta_id'], $rollbackLocation);
        $offerta->setOffertaId($offertaId);
        $result = $offerta->deleteOfferta();

        if($result != 0){
            ControllerOfferta::return_with_status(500, $rollbackLocation);
            exit;
        }

        ControllerOfferta::return_with_status(200, $rollbackLocation);
        exit;
    }

    public static function getOffertaForModifica(): array|null {
        $rollbackLocation = '../../../frontEnd/azienda/paginaOfferte.php?id='.$_SESSION['azienda_id'];

        $offertaId = ControllerOfferta::ExtractId($_GET['offerta'], $rollbackLocation);
        $offerte = new Offerte();

        if ($offerte->connectToDatabase() != 0) {
            echo("connessione al database fallita");
            ControllerOfferta::return_with_status(500, $rollbackLocation);
            exit;
        }

        $result = $offerte->getOffertaById($offertaId);

        if ($result != 0) {
            echo("offerta non trovata");
            ControllerOfferta::return_with_status(404, $rollbackLocation);
            return NULL;
        } 

        $competenzeOfferta = new CompetenzeOfferta();
        if ($competenzeOfferta->connectToDatabase() != 0) {
            echo("connessione al database fallita");
            ControllerOfferta::return_with_status(500, $rollbackLocation);
            exit;
        }
        $arrayCompetenze = $competenzeOfferta->getCompetenzeByOffertaId($offertaId);

        if (!is_array($arrayCompetenze)) {
            echo("competenze non trovate");
            ControllerOfferta::return_with_status(404, $rollbackLocation);
            return NULL;
        }

        http_response_code(200);
        return [
            "offerta" => $offerte->toArray(),
            "competenze" => $arrayCompetenze
        ];
    }

    public static function getOfferta(): array|null {
        $rollbackLocation = '../../../frontEnd/azienda/paginaOfferte.php?id='.$_SESSION['azienda_id'];

        $offertaId = ControllerOfferta::ExtractId($_GET['offerta'], $rollbackLocation);
        $offerte = new Offerte();

        if ($offerte->connectToDatabase() != 0) {
            echo("connessione al database fallita");
            ControllerOfferta::return_with_status(500, $rollbackLocation);
            exit;
        }

        $datiOfferta = $offerte->getOffertaConRequisitiById($offertaId);

        if (!is_array($datiOfferta)) {
            echo("offerta non trovata");
            ControllerOfferta::return_with_status(404, $rollbackLocation);
            return NULL;
        } 
        http_response_code(200);
        return $datiOfferta;
    }

    public static function getInfoByAzienda(){
        $aziende = new Aziende();
        $storageAziende = new StorageAziende();

        $aziendaId = $_SESSION["azienda_id"];

        if ($aziende->connectToDatabase() != 0) {
            echo "Connessione al database fallita";
            http_response_code(500);
            return NULL;
        }
        
        $result = $aziende -> getAziendaById($aziendaId);

        if($result != 0){

            $aziende -> closeConnectionToDatabase();
            echo("errore durante il fetching dell'azienda");
            http_response_code(500);
            return NULL;

        }

        $result = $aziende -> fetchOfferteAzienda();

        if($result != 0){

            //echo("errore durante il fetching delle offerte");

        }

        $result = $aziende -> fetchSediAzienda();

        if($result != 0){

            $aziende -> closeConnectionToDatabase();
            echo("errore durante il fetching delle sedi: ".$result);
            http_response_code(500);
            return NULL;

        }

        $aziendaData = $aziende -> toArray();

        $path = "../fileSystem/";
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

    public static function getOfferte(){
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

    public static function getCandidatiOfferta(): array|null {
        $offerta = new Offerte();

        if ($offerta->connectToDatabase() != 0) {
            http_response_code(500);
            return NULL;
        }

        if (! isset($_GET["offerta"])) {
            $offerta->closeConnectionToDatabase();
            http_response_code(400);
            return NULL;
        }

        if ($offerta->getOffertaById($_GET["offerta"]) != 0) {
            $offerta->closeConnectionToDatabase();
            http_response_code(500);
            return NULL;
        }
        $candidati = $offerta->fetchCandidatureOfferta();
        $offerta->closeConnectionToDatabase();
        if ($candidati === false) {
            http_response_code(500);
            return NULL;
        }

        $storageUtenti = new StorageAziende();
        $size = count($candidati);
        for ($i = 0; $i < $size; $i++) {
            if ($candidati[$i]["documento"] == NULL || $candidati[$i]["documento"] === "") {
                continue;
            }
            $candidati[$i]["documento"] = '/backEnd/fileSystem/files/'
                         . $storageUtenti->getUtenteFolderPlaceholder()
                         . $candidati[$i]["utente_id"]
                         . '/' . $candidati[$i]["documento"];
        }

        return $candidati;
    }

    public static function manageCandidatura() {
        $rollbackLocation = '../../../frontEnd/azienda/paginaCandidati.php?id='. $_SESSION['azienda_id'] . '&offerta='.$_GET['offerta'];
        
        if (! isset($_POST["candidatura_id"]) || ! isset($_POST["stato_id"])) {
            ControllerOfferta::return_with_status(400, $rollbackLocation . '&error=missing params');
            exit;
        }
        if (isset($_GET["motivazioneRequired"]) && (!isset($_POST["motivazione"]) || empty($_POST["motivazione"]))) {
            ControllerOfferta::return_with_status(400, $rollbackLocation . '&error=missing motivation');
            exit;
        }

        $candidatura = new Candidature();
        if ($candidatura -> connectToDatabase() != 0) {
            echo "errore durante la connessione al database";
            ControllerOfferta::return_with_status(500, $rollbackLocation . '&error=db connection error');
            exit;
        }

        if ($candidatura->getCandidaturaById($_POST["candidatura_id"]) != 0) {
            $candidatura->closeConnectionToDatabase();
            ControllerOfferta::return_with_status(500, $rollbackLocation . '&error=db get error');
            exit;
        }

        $result = $candidatura->setStatoCandidatura($_POST["stato_id"], $_POST["motivazione"]);
        $candidatura->closeConnectionToDatabase();
        if($result != 0){
            ControllerOfferta::return_with_status(500, $rollbackLocation . '&error=db update error');
            exit;
        }

        ControllerOfferta::return_with_status(200, $rollbackLocation);
        exit;
    }
}

// Handle multiple http methods.
$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        // If GET, do nothing by default.
        break;
    case 'POST':
        switch ($_GET["op"]) {
            case "manageCandidatura":
                ControllerOfferta::manageCandidatura();
                break;
            case "addOfferta":
                // Handle ADD offerta.
                ControllerOfferta::addOfferta();
                break;
            default:
                // operation not supported.
                ControllerOfferta::return_with_status(400, '../../../frontEnd/azienda/paginaProfilo.php?id=' . $_SESSION['azienda_id']);
                exit;
        }
        break;
    case 'PUT':
        // Handle UPDATE offerta.
        ControllerOfferta::updateOfferta();
        break;
    case 'DELETE':
        // Handle DELETE offerta.
        ControllerOfferta::deleteOfferta();
        break;
    default:
        // http method not supported.
        ControllerOfferta::return_with_status(405, '../../../frontEnd/azienda/paginaProfilo.php?id=' . $_SESSION['azienda_id']);
        exit;
}

?>
