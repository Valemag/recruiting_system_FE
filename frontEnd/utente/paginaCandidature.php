<?php
    require_once("../../backEnd/controllers/utenti/ControllerCandidatura.php");
    require_once("../../backEnd/controllers/utenti/ControllerUtente.php");

    $datiUtente = ControllerUtente::getInfoUtenteBySession();
    $candidatureUtente = ControllerCandidatura::getCandidature();
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
                <a href="#" class="brand-logo center"><img class="responsive-img" src="../assets/logo.png" alt="Logo"></a>
                <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                <ul id="nav-mobile" class="left hide-on-med-and-down">

                    <?php
                    
                        echo('<li><a href="paginaProfilo.php?id='.$_SESSION["utente_id"].'"><i class="material-icons left">home</i>Visualizza Profilo</a></li>');

                    ?>

                    <li><a href="offerteLavoro.php"><i class="material-icons left">business_center</i>Offerte di lavoro</a></li>
                </ul>
                <ul id="nav-mobile" class="right hide-on-med-and-down">
                    <li><a class="dropdown-trigger" href="#!" data-target="dropdownmenu"><i class="material-icons left">person</i><?php echo ($datiUtente['username']) ?><i class="material-icons right">arrow_drop_down</i></a></li>
                </ul>
                <ul id="dropdownmenu" class="dropdown-content light-blue darken-1">
                    <?php echo('<li><a href="paginaProfilo.php?id='.$_SESSION["utente_id"].'"><i class="material-icons left">assignment_ind</i>Visualizza Profilo</a></li>'); ?>
                    <li><a href="modificaProfilo.php"><i class="material-icons left">edit</i>Modifica Profilo</a></li>
                    <li class="divider"></li>
                    <li><a href="../../backEnd/controllers/logout.php"><i class="material-icons left">exit_to_app</i>Logout</a></li>
                </ul>
                <ul id="dropdownmenu_mobile" class="dropdown-content light-blue darken-2">
                    <?php echo('<li><a href="paginaProfilo.php?id='.$_SESSION["utente_id"].'"><i class="material-icons left">assignment_ind</i>Visualizza Profilo</a></li>'); ?>
                    <li><a href="modificaProfilo.php"><i class="material-icons left">edit</i>Modifica Profilo</a></li>
                    <li class="divider"></li>
                    <li><a href="../../backEnd/controllers/logout.php"><i class="material-icons left">exit_to_app</i>Logout</a></li>
                </ul>
                <ul class="sidenav light-blue darken-1" id="mobile-demo">
                    <li><a href="offerteLavoro.php"><i class="material-icons left">business_center</i>Offerte di lavoro</a></li>
                    <li><a class="dropdown-trigger" href="#!" data-target="dropdownmenu_mobile"><i class="material-icons left">person</i><?php echo ($datiUtente['username']) ?><i class="material-icons right">arrow_drop_down</i></a></li>
                </ul>
            </div>
        </nav>
        <div class="container">
            <?php
                if($candidatureUtente != NULL){
                    echo('<h2 class="black-text center">Le mie candidature</h2>');

                    foreach($candidatureUtente as $candidaturaSingola){
                        echo('<div class="card white z-depth-2" style="margin-bottom: 20px; padding: 20px;">');
                        echo('<div class="card-content black-text center">
                                <h4 style="margin:0px;">'.$candidaturaSingola["nome_azienda"].'</h4>
                                <br>
                                '.$candidaturaSingola["titolo_offerta"].'
                                <br><br>
                                <form action="../../backEnd/controllers/utenti/ControllerCandidatura.php?op=delete_candidatura" method="post">
                                    <input type="hidden" name="candidatura_id" value="'.$candidaturaSingola["candidatura_id"].'">
                                    <input type="submit" value="Elimina" class="btn red lighten-1">
                                </form>
                            </div>');

                        echo('<div class="center" style="margin-top: 10px;">');
                        if($candidaturaSingola["stato_candidatura"] == "In attesa"){
                            echo('<div class="status"><div class="dot yellow"></div><span>In attesa</span></div>');
                        }
                        elseif($candidaturaSingola["stato_candidatura"] == "Accettato"){
                            echo('<div class="status"><div class="dot green"></div><span>Accettata</span></div>');
                        }
                        elseif($candidaturaSingola["stato_candidatura"] == "Rifiutato"){
                            echo('<div class="status"><div class="dot red"></div><span>Rifiutata</span></div>
                                <br>
                                Motivazione: <strong>'.$candidaturaSingola["motivazione_risultato"].'</strong>');
                        }
                        echo('</div></div>'); // chiusura card e card-action

                        // Inserimento modal per ogni rifiuto (volendo puoi anche gestirla con ID dinamico)
                        if($candidaturaSingola["stato_candidatura"] == "Rifuitata"){
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
                    }
                } else {
                    echo('<div class="card-panel white z-depth-1"><p class="center">Non hai ancora inviato delle candidature</p></div>');
                }
            ?>
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
