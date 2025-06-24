<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require(__DIR__."/../../backEnd/controllers/utenti/getOfferte.php");
    require(__DIR__."/../../backEnd/controllers/getInfo.php");

    $offerteLavoro = getOfferte();
    $datiUtente = getInfoUtenteBySession();
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
                <ul id="nav-mobile" class="left hide-on-med-and-down">
                    <li><a href="candidature.php"><i class="material-icons left">business_center</i>Candidature</a></li>
                </ul>
                <ul id="nav-mobile" class="right hide-on-med-and-down">
                    <li><a class="dropdown-trigger" href="#!" data-target="dropdownmenu"><i class="material-icons left">person</i><i class="material-icons right">arrow_drop_down</i></a></li>
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
                    <li><a href="candidature.php"><i class="material-icons left">business_center</i>Candidature</a></li>
                    <li><a class="dropdown-trigger" href="#!" data-target="dropdownmenu_mobile"><i class="material-icons left">person</i><i class="material-icons right">arrow_drop_down</i></a></li>
                </ul>
            </div>
        </nav>
        <div class="container">
            <div class="col s12 m8 offset-m2 l6 offset-l3">
                <div class="card-panel white z-depth-1">
                    <?php
                        if ($offerteLavoro != NULL) {

                            foreach ($offerteLavoro as $index => $offertaSingola) {

                                echo('<table class="black-text"><tbody>');

                                echo('<tr>
                                    <td>
                                        <h4 style="margin:0px;" class="black-text">' . $offertaSingola["nome_azienda"] . '</h4>
                                    </td>
                                </tr>');

                                echo('<tr>
                                    <td style="color: #bdbdbd;" class="left">
                                        <h3 style="margin:0px;" class="black-text">' . $offertaSingola["titolo"] . '</h3>
                                    </td>
                                    <td>
                                        <h6 style="margin:0px;" class="black-text">' . $offertaSingola["retribuzione"] . '</h6>
                                    </td>
                                </tr>');

                                echo('<tr>
                                        <td>
                                            <h6 style="margin:0px;" class="black-text">Tipo contratto: ' . $offertaSingola["tipo_contratto"] . '</h6>
                                        </td>
                                        <td>
                                            <a class="btn-floating light-blue darken-1 modal-trigger" href="#modal' . $index . '"><i class="material-icons">info</i></a> 
                                            <form method="POST" action="../../backEnd/controllers/utenti/createCandidatura.php" enctype="multipart/form-data" style="display: flex; gap: 10px; align-items: center;">
                                                <input type="hidden" name="offerta_id" value="' . $offertaSingola["offerta_id"] . '">
                                                <input type="file" name="file" id="file">
                                                <button type="submit" class="btn light-blue darken-1">Invia</button>
                                            </form>
                                        </td>
                                </tr>');

                                echo('</tbody></table>');

                                // MODAL
                                echo('
                                    <div id="modal' . $index . '" class="modal">
                                        <div class="modal-content">
                                            <h4>' . $offertaSingola["titolo"] . '</h4>
                                            <p><strong>Descrizione:</strong><br>' . nl2br($offertaSingola["descrizione"]) . '</p>
                                            <p><strong>Modalità di lavoro:</strong><br>' . nl2br($offertaSingola["modalita_lavoro"]) . '</p>
                                            <p><strong>Competenze richieste:</strong><br>' . nl2br($offertaSingola["competenze_richieste"]) . '</p>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Chiudi</a>
                                        </div>
                                    </div>
                                ');

                                echo('</tbody></table>');
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
        <script>
            document.addEventListener('DOMContentLoaded', function () {
            var modals = document.querySelectorAll('.modal');
            M.Modal.init(modals);
        });
</script>
    </body>
</html>
