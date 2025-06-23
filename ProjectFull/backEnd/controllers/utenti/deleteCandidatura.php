<?php

    require_once(__DIR__."/../../db/models/candidature.php");

    function deleteCandidatura(){

        session_start();


        if (!isset($_SESSION['utente_id'])) {
            http_response_code(401);
            echo "Utente non autenticato";
            exit;
        }


        if (
            !isset($_POST['candidatura_id']) &&
            !is_numeric($_POST['candidatura_id'])
        ) {
            http_response_code(400);
            echo "Parametri mancanti o non validi";
            exit;
        }

        $candidatura = new Candidature();

        if ($candidatura -> connectToDatabase() != 0) {
            echo "Impossibile connettersi al database";
            http_response_code(500);
            exit;
        }

        if ($candidatura -> getCandidaturaById($_POST['candidatura_id']) != 0) {
            $candidatura->closeConnectionToDatabase();
            echo "Impossibile trovare la candidatura";
            http_response_code(500);
            exit;
        }

        if ($candidatura -> deleteCandidatura() != 0) {
            $candidatura->closeConnectionToDatabase();
            echo "Impossibile eliminare la candidatura";
            http_response_code(500);
            exit;
        }

        header('Location: ../../frontEnd/utente/paginaCandidature.php');

        http_response_code(200);

    }

?>