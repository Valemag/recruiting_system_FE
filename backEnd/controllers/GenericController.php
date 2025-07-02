<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class GenericController {
    public static function return_with_status($status_code, $locationFile) {
        http_response_code(response_code: $status_code);
        header('Location: ' . $locationFile);
    }

    public static function VerifyAuthentication() {
        if (!isset($_SESSION['utente_id']) && !isset($_SESSION['azienda_id'])) {
            echo("non autenticato");
            GenericController::return_with_status(401, '../../frontEnd/login.html');
            exit;
        }
    }
}

GenericController::VerifyAuthentication();

?>