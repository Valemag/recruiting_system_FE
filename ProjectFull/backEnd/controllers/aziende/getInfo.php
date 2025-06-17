<?php
session_start();
require_once("../../db/models/aziende.php");

// Controllo autenticazione
if (!isset($_SESSION['azienda_id'])) {
    echo "non autenticato";
    http_response_code(401);
    exit;
}

// Verifica metodo
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo "metodo non supportato";
    http_response_code(405);
    exit;
}

$aziendaId = $_SESSION['azienda_id'];

$azienda = new Aziende();

// Connessione DB
if ($azienda->connectToDatabase() != 0) {
    echo "errore durante la connessione al database";
    http_response_code(500);
    exit;
}

// Caricamento dati azienda
if ($azienda->getAziendaById($aziendaId) != 0) {
    echo "errore durante la lettura dell'azienda";
    http_response_code(500);
    exit;
}

// Caricamento sedi
$azienda->fetchSediAzienda(); // Ignoriamo eventuali errori (sedi opzionali)

// Caricamento documenti
$azienda->fetchDocumentiAzienda(); // Ignoriamo eventuali errori (documenti opzionali)

$response = $azienda->toArray();

// Serializzazione oggetti secondari
$response["sedi"] = [];
if (is_array($azienda->getSediAzienda())) {
    foreach ($azienda->getSediAzienda() as $sede) {
        $response["sedi"][] = $sede->toArray();
    }
}

$response["documenti"] = [];
if (is_array($azienda->getDocumenti())) {
    foreach ($azienda->getDocumenti() as $doc) {
        $response["documenti"][] = $doc->toArray();
    }
}

echo json_encode($response);
http_response_code(200);
exit;
?>
