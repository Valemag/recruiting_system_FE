<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once("../../backEnd/controllers/aziende/ControllerOfferta.php");
    require_once("../../backEnd/controllers/getInfo.php");

    $aziendaData = getInfoAzienda();
    $offerte = getOfferte();
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
        <link rel="stylesheet" href="../../bitnami.css">
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
        </style>
    </head>
    <body class="grey lighten-5">
        <nav>
            <div class="nav-wrapper light-blue darken-1">
                <a href="#" class="brand-logo center"><img class="responsive-img" src="../assets/logo.png" alt="Logo"></a>
                <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                <ul id="nav-mobile" class="right hide-on-med-and-down">
                    <li>
                        <a class="btn white black-text" href="nuovaOffertaLavoro.php?id=<?php echo $_SESSION['azienda_id']; ?>">
                            <i class="material-icons left">add</i>Nuova Offerta
                        </a>
                    </li>
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
                    <h2 class="black-text center">Offerte di Lavoro</h2>

                    <?php
                        if ($offerte != NULL) {
                            echo '<div class="row">';

                            foreach ($offerte as $offerta) {
                                echo '
                                    <div class="col s12 m6 l4">
                                        <div class="card white hoverable card-fixed-height">
                                            <div class="card-content black-text">
                                                <span class="card-title"><strong>' . $offerta["titolo"] . '</strong></span>
                                                <p>' . $offerta["descrizione"] . '</p>
                                                <br>
                                                <strong>Retribuzione:</strong> ' . $offerta["retribuzione"] . '<br>
                                                <strong>Tipo contratto:</strong> ' . $offerta["tipo_contratto"] . '<br>
                                                <strong>Modalità di lavoro:</strong> ' . $offerta["modalita_lavoro"] . '<br>
                                                <strong>Competenze richieste:</strong> ' . $offerta["competenze_richieste"] . '
                                            </div>
                                            <div class="card-action">
                                                <a class="btn-small light-blue darken-1" href="modificaOffertaLavoro.php?id='.$_SESSION["azienda_id"].'&offerta='.$offerta["offerta_id"].'">Modifica</a>
                                                <form method="POST" action="../../backEnd/controllers/aziende/ControllerOfferta.php">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="offerta_id" value="'.$offerta["offerta_id"].'">
                                                    <input type="submit" class="btn-small red darken-1" value="Elimina">
                                                </form>
                                            </div>
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

        <!--JavaScript at end of body for optimized loading-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
        <script type="text/javascript" src="../js/materialize.js"></script>
        <script type="text/javascript" src="../js/scripts.js"></script>
    </body>
</html>