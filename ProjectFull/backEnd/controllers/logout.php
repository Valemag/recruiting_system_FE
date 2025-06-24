<?php

function logout(){
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_unset();
    session_destroy();

    echo "Logout effettuato";
    http_response_code(200);
    header('Location: /index.php');
}

logout();
?>