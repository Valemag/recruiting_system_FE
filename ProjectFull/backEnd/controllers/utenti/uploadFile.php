<?php

function uploadFile(){

    session_start();

    // Verifica sessione
    $isAuth = isset($_SESSION["utente_id"]) && $_SESSION["utente_id"] != "";

    if (!$isAuth) {
        echo "Non autenticato";
        http_response_code(401);
        header('Location: ../../../frontEnd/utente/login.html');
        exit;
    }

    if (!isset($_FILES["file"])) {
        echo "Nessun file ricevuto";
        http_response_code(400);
        header('Location: ../../../frontEnd/utente/paginaProfilo.php?id='.$_SESSION["utente_id"]);
        exit;
    }  

    $file = $_FILES["file"];

    if ($isAuth) {
        require_once(__DIR__."/../../db/models/utenti.php");
        require_once(__DIR__."/../../fileSystem/storage/storageUtenti.php");
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
        if ($storage->uploadUtenteFile($_SESSION["utente_id"], $file) != 0) {
            $utente->closeConnectionToDatabase();
            echo "Errore nel salvataggio del file";
            http_response_code(500);
            exit;
        }

        if ($utente->addDocumento($file["name"]) != 0) {
            $utente->closeConnectionToDatabase();
            echo "Errore nella registrazione del file nel database";
            http_response_code(500);
            exit;
        }

        $utente->closeConnectionToDatabase();
        echo "File caricato con successo";
        http_response_code(200);

        header('Location: ../../../frontEnd/utente/paginaProfilo.php?id='.$_SESSION["utente_id"]);

        exit;
    }
}

uploadFile();
?>
