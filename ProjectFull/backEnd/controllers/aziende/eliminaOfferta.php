<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("../../db/models/offerte.php");

// Controllo sessione
if (!isset($_SESSION['azienda_id'])) {
    echo "non autenticato";
    http_response_code(401);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "metodo non supportato";
    http_response_code(405);
    exit;
}


$offerta = new Offerte();

if ($offerta -> connectToDatabase() != 0) {
    echo "errore durante la connessione al database";
    http_response_code(500);
    exit;
}

if ($offerta -> getOffertaById($_POST["offerta_id"]) != 0) {
    echo "errore la lettura dal database";
    http_response_code(500);
    exit;
}


$result = $offerta -> deleteOfferta();

if($result != 0){

    echo "errore durante l'eliminazione del record";
    http_response_code(500);
    exit;

}

echo "successo";
http_response_code(200);
exit;

?>