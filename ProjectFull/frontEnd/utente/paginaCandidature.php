<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require("../../backEnd/controllers/utenti/getCandidature.php");

    $candidatureUtente = getCandidature();

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

        <style>
            .status {
                display: inline-flex;
                align-items: center;
                margin: 10px 0;
                font-family: sans-serif;
            }

            .dot {
                height: 12px;
                width: 12px;
                border-radius: 50%;
                margin-right: 8px;
            }

            .green { 
                background-color: green; 
            }

            .yellow { 
                background-color: gold; 
            }

            .red { 
                background-color: red; 
            }
        </style>
    </head>
    <body class="grey lighten-5">
        <nav>
            <div class="nav-wrapper light-blue darken-1">
                <a href="#" class="brand-logo center">Logo</a>
                <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                <ul id="nav-mobile" class="left hide-on-med-and-down">

                    <?php
                    
                        echo('<li><a href="paginaProfilo.php?id='.$_SESSION["utente_id"].'"><i class="material-icons left">home</i>Visualizza Profilo</a></li>');

                    ?>

                    <li><a href="offerteLavoro.php"><i class="material-icons left">business_center</i>Offerte di lavoro</a></li>
                </ul>
                <ul id="nav-mobile" class="right hide-on-med-and-down">
                    <li><a class="dropdown-trigger" href="#!" data-target="dropdownmenu"><i class="material-icons left">person</i>Nome Cognome<i class="material-icons right">arrow_drop_down</i></a></li>
                </ul>
                <ul id="dropdownmenu" class="dropdown-content light-blue darken-1">
                    <?php echo('<li><a href="paginaProfilo.php?id='.$_SESSION["utente_id"].'"><i class="material-icons left">assignment_ind</i>Visualizza Profilo</a></li>'); ?>
                    <li><a href="modificaProfilo.html"><i class="material-icons left">edit</i>Modifica Profilo</a></li>
                    <li class="divider"></li>
                    <li><a href="../../backEnd/controllers/utenti/logout.php"><i class="material-icons left">exit_to_app</i>Logout</a></li>
                </ul>
                <ul id="dropdownmenu_mobile" class="dropdown-content light-blue darken-2">
                    <?php echo('<li><a href="paginaProfilo.php?id='.$_SESSION["utente_id"].'"><i class="material-icons left">assignment_ind</i>Visualizza Profilo</a></li>'); ?>
                    <li><a href="modificaProfilo.html"><i class="material-icons left">edit</i>Modifica Profilo</a></li>
                    <li class="divider"></li>
                    <li><a href="../../backEnd/controllers/utenti/logout.php"><i class="material-icons left">exit_to_app</i>Logout</a></li>
                </ul>
                <ul class="sidenav light-blue darken-1" id="mobile-demo">
                    <li><a href="offerteLavoro.php"><i class="material-icons left">business_center</i>Offerte di lavoro</a></li>
                    <li><a class="dropdown-trigger" href="#!" data-target="dropdownmenu_mobile"><i class="material-icons left">person</i><i class="material-icons right">arrow_drop_down</i></a></li>
                </ul>
            </div>
        </nav>
        <div class="container">
            <div class="col s12 m8 offset-m2 l6 offset-l3">
                <div class="card-panel white z-depth-1">

                                <?php
                                
                                
                                if($candidatureUtente != NULL){

                                    echo('<h2 class="black-text center">Le mie candidature</h2>
                                            <table class="highlight black-text">
                                                <tbody>');

                                    foreach($candidatureUtente as $candidaturaSingola){
                                        echo('<tr>');
                                        echo('<td class="center">
                                                <h4 style="margin:0px;" class="black-text">'.$candidaturaSingola["nome_azienda"].'</h4>
                                                <br>
                                                '.$candidaturaSingola["titolo_offerta"].'
                                                <br>
                                                <form action="../../backEnd/controllers/utenti/deleteCandidature.php" method="post">
                                                    <input type="hidden" name="candidatura_id" value="'.$candidaturaSingola["candidatura_id"].'">
                                                    <input type="submit" value="Elimina">
                                                </form>
                                            </td>');

                                        if($candidaturaSingola["stato_candidatura"] == "In attesa"){

                                            echo('<td class="center">
                                                    <div class="status">
                                                        <div class="dot yellow"></div>
                                                        <span>In attesa</span>
                                                    </div>
                                                </td>');

                                        }
                                        else if($candidaturaSingola["stato_candidatura"] == "Accettata"){

                                            echo('<td class="center">
                                                    <div class="status center">
                                                        <div class="dot green"></div>
                                                        <span>Accettata</span>
                                                    </div>
                                                </td>');

                                        }
                                        else if($candidaturaSingola["stato_candidatura"] == "Rifuitata"){

                                            echo('<td class="center">
                                                    <div class="status">
                                                        <div class="dot red"></div>
                                                        <span>Rifiutata</span>
                                                    </div>
                                                </td>
                                                <td class="center">
                                                    <a class="btn-small light-blue darken-1" href="#">motivazione</a>
                                                </td>');

                                            echo('<div id="motivazioneModal" class="modal">
                                                    <div class="modal-content">
                                                        <h4>Motivazione del Rifiuto</h4>
                                                        <p id="motivazioneTesto">'.$candidaturaSingola["motivazione_risultato"].'</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <a href="#!" class="modal-close waves-effect waves-green btn-flat">Chiudi</a>
                                                    </div>
                                                </div>');

                                        }
    
                                        echo('</tr>');
        
                                    }

                                    echo('</tbody></table>;');
    
                                }
                                else{

                                    echo("Non hai ancora inviato delle candidature");

                                }
                                
                                
                                ?>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
            var modals = document.querySelectorAll('.modal');
            M.Modal.init(modals);

                // Event delegation per tutti i bottoni "motivazione"
                $(document).on('click', '.btn-motivazione', function() {
                    var motivazione = $(this).data('motivazione');
                    var instance = M.Modal.getInstance(document.getElementById('motivazioneModal'));
                    instance.open();
                });
            });
        </script>
                                
        <!--JavaScript at end of body for optimized loading-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
        <script type="text/javascript" src="../js/materialize.js"></script>
        <script type="text/javascript" src="../js/scripts.js"></script>
    </body>
</html>
