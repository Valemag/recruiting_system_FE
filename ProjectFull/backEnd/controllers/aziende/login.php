<?php
require(__DIR__ . "/../../db/models/aziende.php"); // Adatta il percorso al tuo progetto

if (
    isset($_POST["email"]) && isset($_POST["password"]) &&
    $_POST["email"] != "" && $_POST["password"] != ""
) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $azienda = new Aziende();

    // Connessione al database
    if ($azienda->connectToDatabase() != 0) {
        echo "Impossibile connettersi al database.";
        http_response_code(500);
        exit;
    }

    // Recupera l'azienda tramite email
    $result = $azienda->getAziendaByEmail($email);

    // Controllo esistenza azienda
    if ($result != 0) {
        echo "Azienda non trovata.";
        http_response_code(404);
        exit;
    }

    // Verifica password
    if (!password_verify($password, $azienda->getPassword())) {
        echo "Password errata.";
        http_response_code(401);
        exit;
    }

    // Login riuscito
    session_start();
    $_SESSION = $azienda->toArray();

    unset($_SESSION["data_registrazione"]); // Rimuovi dati sensibili se presenti

    header("Location: /frontEnd/azienda/paginaProfilo.php?id=" . $_SESSION["azienda_id"]);
    http_response_code(200);
} else {
    echo "Campi obbligatori mancanti.";
    http_response_code(403);
}
?>
