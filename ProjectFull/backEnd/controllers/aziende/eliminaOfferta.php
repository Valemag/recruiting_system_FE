<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("../../db/models/offerte.php");

function return_with_status($status_code) {
    http_response_code(response_code: $status_code);
    header('Location: ../../../frontEnd/azienda/paginaOfferte.php?id='.$_SESSION['azienda_id']);
}

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
    return_with_status(500);
    exit;
}

$offerta->setOffertaId($_POST['offerta_id']);
$result = $offerta -> deleteOfferta();

if($result != 0){

    echo "errore durante l'eliminazione del record";
    return_with_status(500);
    exit;

}

return_with_status(200);
exit;

?>