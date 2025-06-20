<?php
// Verifica parametri minimi

function login(){
    if (
        !isset($_POST["email"]) || !isset($_POST["password"]) || !isset($_POST["tipo"]) ||
        $_POST["email"] === "" || $_POST["password"] === "" || $_POST["tipo"] === ""
    ) {
        echo "Parametri mancanti";
        http_response_code(400);
        exit;
    }

    $email = $_POST["email"];
    $password = $_POST["password"];
    $tipo = $_POST["tipo"]; // 'utente' oppure 'azienda'

    if ($tipo === "utente") {
        require_once(__DIR__ . "/../db/models/utenti.php");
        $account = new Utenti();
    } elseif ($tipo === "azienda") {
        require_once(__DIR__ . "/../db/models/aziende.php");
        $account = new Aziende();
    } else {
        echo "Tipo non valido. Deve essere 'utente' o 'azienda'";
        http_response_code(400);
        exit;
    }

    if ($account->connectToDatabase() != 0) {
        echo "Connessione al database fallita";
        $account -> closeConnectionToDatabase();
        http_response_code(500);
        exit;
    }

    // Recupero account tramite email
    $getByEmail = ($tipo === "utente") ? $account->getUtenteByEmail($email) : $account->getAziendaByEmail($email);

    if ($getByEmail != 0) {
        echo ucfirst($tipo) . " non trovato.";
        $account -> closeConnectionToDatabase();
        http_response_code(404);
        exit;
    }

    // Verifica password
    $getPassword = $account->getPassword();
    if (!password_verify($password, $getPassword)) {
        echo "Password errata.";
        $account -> closeConnectionToDatabase();
        http_response_code(401);
        exit;
    }

    $account -> closeConnectionToDatabase();

    // Login riuscito
    session_start();

    $_SESSION = $account -> toArray();

    unset($_SESSION["data_registrazione"]);

    http_response_code(200); //OK

    if ($tipo === "utente") {
        header('Location: ../../frontEnd/utente/paginaProfilo.php?id='.$_SESSION["utente_id"]);
    } elseif ($tipo === "azienda") {
        header('Location: ../../frontEnd/azienda/paginaProfilo.php?id='.$_SESSION["azienda_id"]);
    }

}

login();
?>
