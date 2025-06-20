<?php
session_start();

require_once("../../db/models/utenti.php");
require_once("../../db/models/aziende.php");

// Verifica metodo
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Metodo non supportato";
    http_response_code(405);
    exit;
}

function setPassword(){

    // Recupero dati
    $oldPassword = $_POST['old_password'] ?? null;
    $newPassword = $_POST['new_password'] ?? null;
    $confirmPassword = $_POST['confirm_password'] ?? null;

    if (!$oldPassword || !$newPassword || !$confirmPassword) {
        echo "Dati mancanti";
        http_response_code(400);
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        echo "Le nuove password non corrispondono";
        http_response_code(400);
        exit;
    }

    // Identificazione attore (utente o azienda)
    if (isset($_SESSION['utente_id'])) {
        $id = $_SESSION['utente_id'];
        $account = new Utenti();
        $getById = [$account, 'getUtenteById'];
        $setPassword = [$account, 'setPassword'];
    } elseif (isset($_SESSION['azienda_id'])) {
        $id = $_SESSION['azienda_id'];
        $account = new Aziende();
        $getById = [$account, 'getAziendaById'];
        $setPassword = [$account, 'setPassword'];
    } else {
        echo "Non autenticato";
        http_response_code(401);
        exit;
    }

    // Connessione al database
    if ($account->connectToDatabase() != 0) {
        echo "Connessione al database fallita";
        http_response_code(500);
        exit;
    }

    // Recupero account
    if (call_user_func($getById, $id) != 0) {
        echo "Account non trovato";
        $account -> closeConnectionToDatabase();
        http_response_code(404);
        exit;
    }

    // Verifica password
    if (!password_verify($oldPassword, $account->getPassword())) {
        echo "Vecchia password errata";
        $account -> closeConnectionToDatabase();
        http_response_code(401);
        exit;
    }

    // Imposta nuova password
    if (call_user_func($setPassword, password_hash($newPassword, PASSWORD_DEFAULT)) != 0) {
        echo "Errore durante l'aggiornamento della password";
        $account -> closeConnectionToDatabase();
        http_response_code(500);
        exit;
    }

    echo "Password aggiornata con successo";
    $account -> closeConnectionToDatabase();
    http_response_code(200);

}

?>