<?php

function logout(){
    session_start();
    session_unset();
    session_destroy();

    echo "Logout effettuato";
    http_response_code(200);
    header('Location: /');
}

logout();
?>