<?php

    session_start();

    require(__DIR__."/../../backEnd/controllers/aziende/getOfferteAzienda.php");

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
    </head>
    <body class="grey lighten-5">
        <nav>
            <div class="nav-wrapper light-blue darken-1">
                <a href="#" class="brand-logo center">Logo</a>
                <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                <ul id="nav-mobile" class="right hide-on-med-and-down">
                    <li><a class="dropdown-trigger" href="#!" data-target="dropdownmenu"><i class="material-icons left">person</i>Nome Cognome<i class="material-icons right">arrow_drop_down</i></a></li>
                </ul>
                <ul id="dropdownmenu" class="dropdown-content light-blue darken-1">
                    <?php echo('<li><a href="paginaProfilo.php?id='.$_SESSION["azienda_id"].'"><i class="material-icons left">assignment_ind</i>Visualizza Profilo</a></li>'); ?>
                    <li class="divider"></li>
                    <li><a href="../../backEnd/controllers/logout.php"><i class="material-icons left">exit_to_app</i>Logout</a></li>
                    <li class="divider"></li>
                    <li><a href="nuovaOffertaLavoro.php"><i class="material-icons left">assignment_ind</i>Nuova offerta</a></li>
                </ul>
                <ul id="dropdownmenu_mobile" class="dropdown-content light-blue darken-2">
                <?php echo('<li><a href="paginaProfilo.php?id='.$_SESSION["azienda_id"].'"><i class="material-icons left">assignment_ind</i>Visualizza Profilo</a></li>'); ?>
                    <li class="divider"></li>
                    <li><a href="../../backEnd/controllers/logout.php"><i class="material-icons left">exit_to_app</i>Logout</a></li>
                </ul>
                <ul class="sidenav light-blue darken-1" id="mobile-demo">
                    <li><a class="dropdown-trigger" href="#!" data-target="dropdownmenu_mobile"><i class="material-icons left">person</i>Nome Cognome<i class="material-icons right">arrow_drop_down</i></a></li>
                </ul>
            </div>
        </nav>
        <div class="container">
            <div class="col s12 m8 offset-m2 l6 offset-l3">
                <div class="card-panel grey lighten-5 z-depth-1">    
                    <h2 class="black-text center">Offerte di Lavoro</h2>

                    <?php
                    
                    if ($offerte != NULL) {

                        foreach ($offerte as $index => $offerta) {

                            echo('<div class="row">
                                <div class="col s12 m4">
                                    <div class="card white">
                                        <div class="card-content black-text">
                                            <a class="btn-floating btn-small light-blue darken-1 right"><i class="material-icons right">check</i></a>
                                            <span class="card-title">Titolo offerta</span>
                                            '.$offerta["titolo"].'
                                            <br>
                                            '.$offerta["descrizione"].'
                                        </div>
                                        <div class="card-action">
                                            <a class="btn-small light-blue darken-1" href="#">Dettagli</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col s12 m4">
                                    <div class="card white">
                                        <div class="card-content black-text">
                                            <a class="btn-floating btn-small light-blue darken-1 right"><i class="material-icons right">check</i></a>
                                            <span class="card-title">Titolo offerta</span>
                                            retribuzione: '.$offerta["retribuzione"].'
                                            <br>
                                            tipo contratto: '.$offerta["tipo_contratto"].'
                                            <br>
                                            modalità di lavoro: '.$offerta["modalita_lavoro"].'
                                            <br>
                                            competenze richieste: '.$offerta["competenze_richieste"].'
                                        </div>
                                        <div class="card-action">
                                            <a class="btn-small light-blue darken-1" href="#">Dettagli</a>
                                        </div>
                                    </div>
                                </div>
                            </div>');

                        }

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