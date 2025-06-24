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

$competenze = array();

if (!empty($_POST["competenze1"])) {
    $competenze[] = $_POST["competenze1"];
}

if (!empty($_POST["competenze2"])) {
    $competenze[] = $_POST["competenze2"];
}

if (!empty($_POST["competenze3"])) {
    $competenze[] = $_POST["competenze3"];
}

$result = $offerta -> addOfferta($aziendaId, 
                                $_POST["titolo"], 
                                $_POST["descrizione"], 
                                $competenze,
                                $_POST["sede"],
                                $_POST["retribuzione"],
                                $_POST["tipo_contratto"],
                                $_POST["data_scadenza"],
                                $_POST["modalita_lavoro"]);
if($result != 0){

    echo "errore durante l'aggiunta del record";
    http_response_code(500);
    exit;

}

echo "successo";
http_response_code(200);
exit;

?>