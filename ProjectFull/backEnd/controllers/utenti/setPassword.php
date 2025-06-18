<?php

require_once("../../db/models/utenti.php");

function setPassword(){

    session_start();

    // Controllo sessione
    if (!isset($_SESSION['utente_id'])) {
        echo "non autenticato";
        http_response_code(401);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo "metodo non supportato";
        http_response_code(405);
        exit;
    }

    // Recupero dati POST: password vecchia, nuova password e conferma nuova password
    $oldPassword = $_POST['old_password'] ?? null;
    $newPassword = $_POST['new_password'] ?? null;
    $confirmPassword = $_POST['confirm_password'] ?? null;

    if (!$oldPassword || !$newPassword || !$confirmPassword) {
        echo "dati mancanti";
        http_response_code(400);
        exit;
    }

    if ($newPassword !== $confirmPassword) {
        echo "le nuove password non corrispondono";
        http_response_code(400);
        exit;
    }

    $utenteId = $_SESSION['utente_id'];
    $utente = new Utenti();

    if ($utente->connectToDatabase() != 0) {
        echo "connessione al database fallita";
        http_response_code(500);
        exit;
    }

    if ($utente->getUtenteById($utenteId) != 0) {
        echo "utente non trovato";
        http_response_code(404);
        exit;
    }

    // Verifica password vecchia
    if (!password_verify($oldPassword, $utente->getPassword())) {
        echo "password vecchia errata";
        http_response_code(403);
        exit;
    }

    // Aggiorna con nuova password (hash)
    $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $utente->setPassword($newHashedPassword);

    if ($utente->updateUtente() != 0) {
        echo "impossibile aggiornare la password";
        http_response_code(500);
        exit;
    }

    echo "password aggiornata con successo";
    http_response_code(200);

}

?>