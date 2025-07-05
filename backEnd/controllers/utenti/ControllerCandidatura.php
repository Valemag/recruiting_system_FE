<?php

require_once(__DIR__."/../GenericController.php");
require_once(__DIR__."/../../db/models/offerte.php");
require_once(__DIR__."/../../db/models/utenti.php");
require_once(__DIR__."/../../db/models/candidature.php");
require_once(__DIR__."/../../fileSystem/storage/storageUtenti.php");

class ControllerCandidatura extends GenericController
{
    public static function getOfferte(){
        $offerte = new Offerte();

        // Connessione al DB
        if ($offerte->connectToDatabase() != 0) {
            echo("connessione al database fallita");
            http_response_code(500);
            return NULL;
        }

        // Recupera le offerte dalla vista
        $risultato = $offerte->getUltimeOfferteConRequisiti($_SESSION["utente_id"]);
        $offerte->closeConnectionToDatabase();

        // Gestione errori o output
        if (!is_array($risultato)) {
            return NULL;
        } 
        return $risultato;
    }

    public static function getOfferteAppuntate(){
        $offerte = new Offerte();

        // Connessione al DB
        if ($offerte->connectToDatabase() != 0) {
            echo("connessione al database fallita");
            http_response_code(500);
            return NULL;
        }

        // Recupera le offerte dalla vista
        $risultato = $offerte->getOfferteAppuntateByUtenteId($_SESSION['utente_id']);
        $offerte->closeConnectionToDatabase();

        // Gestione errori o output
        if (!is_array($risultato)) {
            return NULL;
        } 
        return $risultato;
    }

    public static function getCandidature(){        
        $utente = new Utenti();
        
        if ($utente->connectToDatabase() != 0) {
            echo "connessione al database fallita";
            http_response_code(500);
            return NULL;
        }
        
        $utenteId = $_SESSION['utente_id'];
        
        if($utente -> getUtenteById($utenteId) != 0){
            $utente->closeConnectionToDatabase();
            http_response_code(500);
            return NULL;

        } 
        $risultato = $utente->getCandidatureWithInfo();
        $utente->closeConnectionToDatabase();
        
        if (!is_array($risultato)) {
            return NULL;
        }
        return $risultato;
    }

    public static function createCandidatura(){
        $rollbackLocation = '../../../frontEnd/utente/offerteLavoro.php';

        if (
            !isset($_POST['offerta_id']) ||
            !isset($_FILES['file']) ||
            !is_numeric($_POST['offerta_id'])
        ) {
            ControllerCandidatura::return_with_status(400, $rollbackLocation . '?msg=parametri mancanti');
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
            ControllerCandidatura::return_with_status(500, $rollbackLocation . '?msg=errore database');
            exit;
        }

        $competenzeOffertaResult = $offerta -> getCompetenzeRichiesteByOffertaId($_POST['offerta_id']);

        if(!is_array($competenzeOffertaResult)){
            $offerta->closeConnectionToDatabase();
            echo "Impossibile trovare le competenze richieste dall'offerta";
            ControllerCandidatura::return_with_status(500, $rollbackLocation . '?msg=competenze non trovate');
            exit;
        }

        $competenzeOfferta = [];
        foreach($competenzeOffertaResult as $c) {
            $competenzeOfferta[] = $c["competenza"];
        }
        $offerta->closeConnectionToDatabase();

        if ($utente -> connectToDatabase() != 0) {
            echo "Impossibile connettersi al database 1";
            ControllerCandidatura::return_with_status(500, $rollbackLocation . '?msg=errore database');
            exit;
        }

        if ($utente->getUtenteById($utenteId) != 0) {
            $utente->closeConnectionToDatabase();
            echo "Impossibile connettersi al database";
            ControllerCandidatura::return_with_status(500, $rollbackLocation . '?msg=errore database');
            exit;
        }

        if ($utente->fetchCompetenzeUtente() != 0) {
            $utente->closeConnectionToDatabase();
            echo "Impossibile recuperare le competenze dell'utente";
            ControllerCandidatura::return_with_status(500, $rollbackLocation . '?msg=competenze non trovate');
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
            ControllerCandidatura::return_with_status(200, $rollbackLocation);
            exit;
        }

        if ($storage->uploadUtenteFile($utenteId, $file) != 0) {
            $utente->closeConnectionToDatabase();
            echo "Errore nel salvataggio del file";
            ControllerCandidatura::return_with_status(500, $rollbackLocation . '?msg=upload file non riuscito');
            exit;
        }

        if ($utente->addDocumento($file["name"]) != 0) {
            $utente->closeConnectionToDatabase();
            echo "Errore nella registrazione del file nel database";
            ControllerCandidatura::return_with_status(500, $rollbackLocation . '?msg=upload file non riuscito');
            exit;
        }

        $offertaId = intval($_POST['offerta_id']);
        $cvDocumentoId = $utente -> getConn() -> insert_id;
        $note = isset($_POST['note']) ? trim($_POST['note']) : null;

        $utente->closeConnectionToDatabase();

        if ($candidaturaObj->connectToDatabase() !== 0) {
            ControllerCandidatura::return_with_status(500, $rollbackLocation . '?msg=errore database');
            echo "Errore di connessione alla tabella candidature 2";
            exit;
        }

        if ($candidaturaObj->addCandidatura($offertaId, $cvDocumentoId, $utenteId, $note) !== 0) {
            $candidaturaObj->closeConnectionToDatabase();
            ControllerCandidatura::return_with_status(500, $rollbackLocation . '?msg=insert candidatura non riuscito');
            echo "Errore durante l'aggiunta della candidatura";
            exit;
        }
        ControllerCandidatura::return_with_status(200, '../../../frontEnd/utente/paginaCandidature.php');
    }

    public static function deleteCandidatura() {
        $rollbackLocation = '../../../frontEnd/utente/paginaCandidature.php';

        if (
            !isset($_POST['candidatura_id']) &&
            !is_numeric($_POST['candidatura_id'])
        ) {
            ControllerCandidatura::return_with_status(400, $rollbackLocation . '?msg=parametri mancanti');
            echo "Parametri mancanti o non validi";
            exit;
        }

        $candidatura = new Candidature();

        if ($candidatura -> connectToDatabase() != 0) {
            echo "Impossibile connettersi al database";
            ControllerCandidatura::return_with_status(500, $rollbackLocation . '?msg=errore database');
            exit;
        }

        if ($candidatura -> getCandidaturaById($_POST['candidatura_id']) != 0) {
            $candidatura->closeConnectionToDatabase();
            echo "Impossibile trovare la candidatura";
            ControllerCandidatura::return_with_status(500, $rollbackLocation . '?msg=candidatura non trovata');
            exit;
        }

        if ($candidatura -> deleteCandidatura() != 0) {
            $candidatura->closeConnectionToDatabase();
            echo "Impossibile eliminare la candidatura";
            ControllerCandidatura::return_with_status(500, $rollbackLocation . '?msg=candidatura non eliminata');
            exit;
        }

        $storageUtenti = new StorageUtenti();
        $storageUtenti->deleteUtenteFile($_SESSION['utente_id'], $candidatura->getCvDocumentoFileName());

        ControllerCandidatura::return_with_status(200, $rollbackLocation);
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
            case "new_candidatura":
                ControllerCandidatura::createCandidatura();
                break;
            case "delete_candidatura":
                ControllerCandidatura::deleteCandidatura();
                break;
            default:
                ControllerCandidatura::return_with_status(405, '../../../frontEnd/utente/paginaProfilo.php?id=' . $_SESSION['utente_id']);
                exit;
        }
        break;
    default:
        // http method not supported.
        ControllerCandidatura::return_with_status(405, '../../../frontEnd/utente/paginaProfilo.php?id=' . $_SESSION['utente_id']);
        exit;
}

?>