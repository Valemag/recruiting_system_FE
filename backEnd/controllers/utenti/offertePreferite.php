<?php
require_once("../../db/models/utenti.php");
ini_set('display_errors', 1);
error_reporting(E_ALL);


if (session_status() === PHP_SESSION_NONE) {
    session_start();

}
if (!isset($_SESSION['utente_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Utente non autenticato"]);
    exit;
}

$utente = new Utenti();
$utente->connectToDatabase();

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'get':
        header('Content-Type: application/json');
        $offerte = $utente->getOfferteAppuntate($_SESSION['utente_id']);
        echo json_encode($offerte);
        break;

    case 'remove':
        header('Content-Type: application/json');
        $offertaId = intval($_POST['offerta_id']);
        $success = $utente->rimuoviOffertaAppuntata($_SESSION['utente_id'], $offertaId);
        echo json_encode(['success' => $success]);
        break;

    case 'add':
        header('Content-Type: application/json');
        if (!isset($_POST['offerta_id'])) {
                echo json_encode(['success' => false, 'message' => 'ID offerta mancante']);
                break;
        }
        
        $offertaId = intval($_POST['offerta_id']);
        $success = $utente->appuntaOfferta($_SESSION['utente_id'], $offertaId);
        
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Offerta appuntata con successo']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Errore durante l\'appuntamento']);
        }
        break;
        

    default:
        http_response_code(400);
        echo json_encode(["error" => "Azione non valida"]);
}

?>