<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once("../../db/models/offerte.php");

function return_with_error($status_code) {
    http_response_code(response_code: $status_code);
    header('Location: ../../../frontEnd/azienda/nuovaOffertaLavoro.php?id='.$_SESSION['azienda_id']);
}

// Controllo sessione
if (!isset($_SESSION['azienda_id'])) {
    echo "non autenticato";
    http_response_code(401);
    header('Location: ../../../frontEnd/login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "metodo non supportato";
    return_with_error(405);
    exit;
}


$offerta = new Offerte();

$aziendaId = $_SESSION['azienda_id'];

if ($offerta -> connectToDatabase() != 0) {
    echo "errore durante la connessione al database";
    return_with_error(500);
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

if (empty($_GET["sedeId"])) {
    return_with_error(status_code: 400);
    exit;
}

$result = $offerta -> addOfferta($aziendaId, 
                                $_POST["titolo"], 
                                $_POST["descrizione"], 
                                $competenze,
                                $_GET["sedeId"],
                                $_POST["retribuzione"],
                                $_POST["tipo_contratto"],
                                $_POST["data_scadenza"],
                                $_POST["modalita_lavoro"]);
if($result != 0){

    echo "errore durante l'aggiunta del record";
    return_with_error(500);
    exit;

}

http_response_code(200);
header('Location: ../../../frontEnd/azienda/paginaOfferte.php?id='.$_SESSION['azienda_id']);
exit;

?>