<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("../../db/models/candidature.php");

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


$candidatura = new Candidature();

if ($candidatura -> connectToDatabase() != 0) {
    echo "errore durante la connessione al database";
    http_response_code(500);
    exit;
}

if ($candidatura -> getCandidaturaById($_POST["candidatura_id"]) != 0) {
    echo "errore durante la lettura del database";
    http_response_code(500);
    exit;
}

$result = $candidatura -> setStatoCandidatura($_POST["stato_id"], $_POST["motivazione"]);
if($result != 0){

    echo "errore durante la modifica del record";
    http_response_code(500);
    exit;

}

echo "successo";
http_response_code(200);
exit;

?>