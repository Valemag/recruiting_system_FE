<?php
session_start();
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

$aziendaId = $_SESSION['azienda_id'];

if ($offerta -> connectToDatabase() != 0) {
    echo "errore durante la connessione al database";
    http_response_code(500);
    exit;
}


$result = $offerta -> addOfferta($aziendaId, 
                                $_POST["titolo"], 
                                $_POST["descrizione"], 
                                $_POST["requisiti"],
                                $_POST["sede_id"],
                                $_POST["categoria_id"]);
if($result != 0){

    echo "errore durante l'aggiunta del record";
    http_response_code(500);
    exit;

}

echo "successo";
http_response_code(200);
exit;

?>