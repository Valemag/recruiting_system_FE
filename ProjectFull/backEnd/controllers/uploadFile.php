<?php

require_once("../../db/models/utenti.php");
require_once("../../db/models/aziende.php");
require_once("../../fileSystem/storage/storageUtenti.php");
require_once("../../fileSystem/storage/storageAziende.php");

session_start();

// Verifica sessione
$isUtente = isset($_SESSION["utente_id"]) && $_SESSION["utente_id"] != "";
$isAzienda = isset($_SESSION["azienda_id"]) && $_SESSION["azienda_id"] != "";

if (!$isUtente && !$isAzienda) {
    echo "Non autenticato";
    http_response_code(401);
    exit;
}

if (!isset($_FILES["file"])) {
    echo "Nessun file ricevuto";
    http_response_code(400);
    exit;
}

$file = $_FILES["file"];

function uploadFile(){

    if ($isUtente) {
        $utente = new Utenti();

        if ($utente->connectToDatabase() != 0) {
            echo "Connessione al database fallita";
            http_response_code(500);
            exit;
        }

        if ($utente->getUtenteById($_SESSION["utente_id"]) > 0) {
            echo "Impossibile connettersi al database";
            http_response_code(500);
            exit;
        }

        $storage = new StorageUtenti();
        if ($storage->uploadUtenteFile($_SESSION["utente_id"], $file) > 0) {
            $utente->closeConnectionToDatabase();
            echo "Errore nel salvataggio del file";
            http_response_code(500);
            exit;
        }

        if ($utente->addDocumento($file["name"]) > 0) {
            $utente->closeConnectionToDatabase();
            echo "Errore nella registrazione del file nel database";
            http_response_code(500);
            exit;
        }

        $utente->closeConnectionToDatabase();
        echo "File caricato con successo";
        http_response_code(200);
        exit;
    }

    if ($isAzienda) {
        $azienda = new Aziende();

        if ($azienda->connectToDatabase() != 0) {
            echo "Connessione al database fallita";
            http_response_code(500);
            exit;
        }

        if ($azienda->getAziendaById($_SESSION["azienda_id"]) > 0) {
            echo "Impossibile connettersi al database";
            http_response_code(500);
            exit;
        }

        $storage = new StorageAziende();
        if ($storage->uploadAziendaFile($_SESSION["azienda_id"], $file) > 0) {
            $azienda->closeConnectionToDatabase();
            echo "Errore nel salvataggio del file";
            http_response_code(500);
            exit;
        }

        if ($azienda->addDocumento($file["name"]) > 0) {
            $azienda->closeConnectionToDatabase();
            echo "Errore nella registrazione del file nel database";
            http_response_code(500);
            exit;
        }

        $azienda->closeConnectionToDatabase();
        echo "File caricato con successo";
        http_response_code(200);
        exit;
    }
}

uploadFile();
?>
