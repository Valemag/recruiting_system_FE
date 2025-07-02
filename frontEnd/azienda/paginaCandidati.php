<?php
    require_once("../../backEnd/controllers/aziende/ControllerOfferta.php");

    $aziendaData = ControllerOfferta::getInfoByAzienda();
    $offerta = ControllerOfferta::getOfferta();
    $candidati = ControllerOfferta::getCandidatiOfferta();
?>

<!DOCTYPE html>
<html>
    <head>
        <!--Import Google Icon Font-->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <!--Import materialize.css-->
        <link type="text/css" rel="stylesheet" href="../css/materialize.css"  media="screen,projection"/>

        <!--Let browser know website is optimized for mobile-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <link rel="stylesheet" href="../css/custom.css">
        <style>
            .card-equal-height {
                display: flex;
                flex-direction: column;
                height: 100%;
            }

            .card-content {
                flex-grow: 1;
            }

            .card-action {
                display: flex;
                justify-content: space-between; /* bottone sinistra e destra */
                align-items: center;
            }
            /* Sfondo scuro semitrasparente */
            .overlay {
                position: fixed;
                top: 0; left: 0;
                width: 100%; height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                display: none;
                justify-content: center;
                align-items: center;
                z-index: 999;
            }

            /* Dialog centrato */
            .dialog {
                background: white;
                padding: 20px;
                border-radius: 8px;
                max-width: 700px;
                width: 100%;
                box-shadow: 0 0 10px rgba(0,0,0,0.3);
            }

            .dialog h3 {
                margin-top: 0;
            }

            .dialog label {
                display: block;
                margin-top: 10px;
                font-weight: bold;
            }

            .dialog input[type="text"] {
                width: 100%;
                padding: 8px;
                margin-top: 5px;
                margin-bottom: 15px;
                box-sizing: border-box;
            }

            .dialog-buttons {
                text-align: right;
            }

            .dialog-buttons button {
                margin-left: 10px;
            }
        </style>
    </head>
    <body class="grey lighten-5">
        <nav>
            <div class="nav-wrapper light-blue darken-1">
                <a href="#" class="brand-logo center"><img class="responsive-img" src="../assets/logo.png" alt="Logo"></a>
                <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                <ul id="nav-mobile" class="right hide-on-med-and-down">
                    <li>
                        <a class="dropdown-trigger" href="#!" data-target="dropdownmenu">
                            <i class="material-icons left">person</i>
                            <?php echo($aziendaData["nome"]) ?>
                            <i class="material-icons right">arrow_drop_down</i>
                        </a>
                    </li>
                </ul>

                <ul id="dropdownmenu" class="dropdown-content light-blue darken-1">
                    <?php echo('<li><a href="paginaProfilo.php?id='.$_SESSION["azienda_id"].'"><i class="material-icons left">assignment_ind</i>Visualizza Profilo</a></li>'); ?>
                    <li class="divider"></li>
                    <li><a href="../../backEnd/controllers/logout.php"><i class="material-icons left">exit_to_app</i>Logout</a></li>
                    <li class="divider"></li>
                </ul>
                <ul id="dropdownmenu_mobile" class="dropdown-content light-blue darken-2">
                <?php echo('<li><a href="paginaProfilo.php?id='.$_SESSION["azienda_id"].'"><i class="material-icons left">assignment_ind</i>Visualizza Profilo</a></li>'); ?>
                    <li class="divider"></li>
                    <li><a href="../../backEnd/controllers/logout.php"><i class="material-icons left">exit_to_app</i>Logout</a></li>
                </ul>
                <ul class="sidenav light-blue darken-1" id="mobile-demo">
                    <li><a class="dropdown-trigger" href="#!" data-target="dropdownmenu_mobile"><i class="material-icons left">person</i>
                    <?php echo($aziendaData["nome"]) ?>
                    <i class="material-icons right">arrow_drop_down</i></a></li>
                </ul>
            </div>
        </nav>
        <div class="container">
            <div class="col s12 m8 offset-m2 l6 offset-l3">
                <div class="card-panel grey lighten-5 z-depth-1">    
                    <h2 class="black-text center">Candidati per offerta <strong><?php echo $offerta["titolo"] ?></strong></h2>
                    <p>Descrizione: <strong><?php echo $offerta['descrizione'] ?></strong></p>
                    <p>Competenze richieste: <strong><?php echo $offerta['competenze_richieste'] ?></strong></p>
                    <?php
                        if (! isset($candidati) || count($candidati) == 0) {
                            echo '<p>Nessun candidato disponibile al momento. Riprova più tardi...</p>';
                        }
                        else {
                            echo '<div class="row">';

                            foreach ($candidati as $candidato) {
                                echo '
                                    <div class="col s12 m6 l4">
                                        <div class="card white hoverable card-fixed-height">
                                            <div class="card-content black-text">
                                                <span class="card-title"><strong>' . $candidato["nome"] . ' ' . $candidato["cognome"] . '</strong></span>
                                                <p>' . $candidato["descrizione"] . '</p>
                                                <br>
                                                <strong>Username:</strong> ' . $candidato["username"] . '<br>
                                                <strong>Telefono:</strong> ' . $candidato["telefono_contatto"] . '<br>
                                                <strong>Email:</strong> ' . $candidato["email"] . '
                                            </div>
                                            <div class="card-action">
                                                <a class="btn-small light-blue darken-1" href="../utente/paginaProfilo.php?id='.$candidato["utente_id"].'">Visualizza</a>
                                                '.($candidato["stato_id"] == 2 
                                                    ? '✅ Approvato' 
                                                    : ($candidato["stato_id"] == 3
                                                        ? '❌ Rifiutato'
                                                        : ('
                                                            <form method="POST" action="../../../backEnd/controllers/aziende/ControllerOfferta.php?op=manageCandidatura&offerta='.$offerta["offerta_id"].'">
                                                                <input type="hidden" name="candidatura_id" value="'.$candidato["candidatura_id"].'">
                                                                <input type="hidden" name="stato_id" value="2">
                                                                <input type="submit" class="btn-small green darken-1" value="Approva">
                                                            </form>
                                                            <button class="btn-small red darken-1" onclick="apriDialogRifiutaCandidato(\''.$candidato["username"].'\', \''.$candidato["candidatura_id"].'\')">Rifiuta</button>
                                                        ')
                                                    )
                                                )
                                            .'</div>
                                        </div>
                                    </div>
                                ';
                            }

                            echo '</div>';
                        }
                    ?>
                </div>
            </div>
        </div>

        <!-- Overlay + Dialog -->
        <div class="overlay" id="overlay">
            <div class="dialog">
                <form method="POST" action="../../../backEnd/controllers/aziende/ControllerOfferta.php?op=manageCandidatura&motivazioneRequired=true&offerta=<?php echo $offerta['offerta_id'] ?>">
                    <h4 id="dialog-title">Vuoi rifiutare il candidato?</h4>
                    <label for="motivazione">Commenta rifiuto:</label>
                    <input type="text" id="motivazione" name="motivazione" placeholder="Scrivi il motivo...">
                    <div class="dialog-buttons">
                        <a class="btn-small red darken-1" onclick="chiudiDialog()">Annulla</a>
                        <input id="dialog_hidden_candidatura_id" type="hidden" name="candidatura_id" value="">
                        <input type="hidden" name="stato_id" value="3">
                        <input type="submit" class="btn-small green darken-1" value="Invia">
                    </div>
                </form>
            </div>
        </div>

        <!--JavaScript at end of body for optimized loading-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
        <script type="text/javascript" src="../js/materialize.js"></script>
        <script type="text/javascript" src="../js/scripts.js"></script>
        <script>
            function apriDialogRifiutaCandidato(candidatoUsername, candidatoId) {
                document.getElementById('dialog-title').textContent = "Vuoi rifiutare il candidato " + candidatoUsername + "?";
                document.getElementById('dialog_hidden_candidatura_id').value = candidatoId;
                document.getElementById('overlay').style.display = 'flex';
                document.getElementById('motivazione').value = ''; // reset input
            }

            function chiudiDialog() {
                document.getElementById('overlay').style.display = 'none';
                document.getElementById('dialog_hidden_candidatura_id').value = "";
            }
        </script>
    </body>
</html>