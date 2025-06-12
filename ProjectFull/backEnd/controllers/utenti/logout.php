<?php
session_start();
session_unset();
session_destroy();

echo "logout effettuato";
http_response_code(200);

header('Location: ../../../frontEnd/utente/login.html');

?>