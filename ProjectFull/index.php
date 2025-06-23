<?php

function start() {
    session_start();

    http_response_code(response_code: 200);
    $isUser = isset($_SESSION["utente_id"]);
    if ($isUser || isset($_SESSION["azienda_id"])) {
        if ($isUser) {
            header('Location: ../../frontEnd/utente/paginaProfilo.php?id='.$_SESSION["utente_id"]);
        } 
        else {
            header('Location: ../../frontEnd/azienda/paginaProfilo.php?id='.$_SESSION["azienda_id"]);
        }
    } 
    else {
        header('Location: ../../frontEnd/Homepage.html');
    }
}

start();

?>