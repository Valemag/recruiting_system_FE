<?php
session_start();
require_once("../../db/models/candidature.php");

if (!isset($_SESSION['utente_id'])) {
    echo "non autenticato";
    http_response_code(401);
    exit;
}

$candidatureObj = new Candidature();

if (!$candidatureObj->connectToDatabase()) {
    echo "connessione al database fallita";
    http_response_code(500);
    exit;
}

$utenteId = $_SESSION['utente_id'];

$candidature = $candidatureObj->getCandidatureByUtenteId($utenteId);

header('Content-Type: application/json');
echo json_encode($candidature);
