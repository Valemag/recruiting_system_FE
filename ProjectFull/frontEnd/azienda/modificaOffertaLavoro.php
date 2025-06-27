
<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once("../../backEnd/controllers/aziende/ControllerOfferta.php");
    require_once("../../backEnd/controllers/getInfo.php");
    $aziendaData = getInfoAzienda();
    $dati = getOffertaForModifica();
    $offerta = $dati["offerta"];
    $competenze = $dati["competenze"];

    $tipiContrattoList = [
        "1" => "Tempo indeterminato",
        "2" => "Tempo determinato",
        "3" => "Stage",
        "4" => "Part-time",
        "5" => "Freelance",
        "6" => "Apprendistato",
        "7" => "Contratto a chiamata"
    ];

    $modalitaLavoroList = [
        "1" => "In sede",
        "2" => "Remoto",
        "3" => "Ibrido"
    ];

    $competenzeList = [
        "1"  => "Java",
        "2"  => "Python",
        "3"  => "SQL",
        "4"  => "Project Management",
        "5"  => "React",
        "6"  => "Machine Learning",
        "7"  => "Linux",
        "8"  => "Comunicazione efficace",
        "9"  => "AWS",
        "10" => "Cybersecurity"
    ];

    function generateHtmlOptions($staticOptions, $check): string {
        $result = "";
        foreach ($staticOptions as $key => $value) {
            $result .= '<option value="' . $key . '" ' . ($check($key) ? "selected" : "") . ">" . $value . "</option>";
        }
        return $result;
    }
?>


<!DOCTYPE html>
<html>
    <head>
        <!--Import Google Icon Font-->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <!--Import materialize.css-->
        <link type="text/css" rel="stylesheet" href="../css/materialize.css"  media="screen,projection"/>
        <style>
            select, option {
                color: black !important;
            }
            .dropdown-content li > span {
                color: black !important;
            }
            .message {
                display: none;
                padding: 12px;
                margin: 20px 0;
                border-radius: 5px;
                font-family: Arial, sans-serif;
                font-size: 15px;
            }

            #success-message {
                background-color: #DFF2BF;
                border: 1px solid #4CAF50;
                color: #4CAF50;
            }

            #error-message {
                background-color: #FFBABA;
                border: 1px solid #D8000C;
                color: #D8000C;
            }
        </style>

        <!--Let browser know website is optimized for mobile-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <link rel="stylesheet" href="../../bitnami.css">
    </head>
    <body class="grey lighten-5">
        <nav>
            <div class="nav-wrapper light-blue darken-1">
                <a href="#" class="brand-logo center"><img class="responsive-img" src="../assets/logo.png" alt="Logo"></a>
                <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                <ul id="nav-mobile" class="left hide-on-med-and-down">
                    <?php echo('<li><a href="paginaOfferte.php?id='.$_SESSION["azienda_id"].'"><i class="material-icons left">business</i>Proposte di Lavoro</a></li>'); ?>
                </ul>
                <ul id="nav-mobile" class="right hide-on-med-and-down">
                    <li><a class="dropdown-trigger" href="#!" data-target="dropdownmenu"><i class="material-icons left">person</i><?php echo($aziendaData["nome"]) ?><i class="material-icons right">arrow_drop_down</i></a></li>
                </ul>
                <ul id="dropdownmenu" class="dropdown-content light-blue darken-1">
                    <?php echo('<li><a href="paginaProfilo.php?id='.$_SESSION["azienda_id"].'"><i class="material-icons left">assignment_ind</i>Visualizza Profilo</a></li>'); ?>
                    <li class="divider"></li>
                    <li><a href="../../backEnd/controllers/logout.php"><i class="material-icons left">exit_to_app</i>Logout</a></li>
                </ul>
                <ul id="dropdownmenu_mobile" class="dropdown-content light-blue darken-2">
                    <?php echo('<li><a href="paginaProfilo.php?id='.$_SESSION["azienda_id"].'"><i class="material-icons left">assignment_ind</i>Visualizza Profilo</a></li>'); ?>
                    <li class="divider"></li>
                    <li><a href="../../backEnd/controllers/logout.php"><i class="material-icons left">exit_to_app</i>Logout</a></li>
                </ul>
                <ul class="sidenav light-blue darken-1" id="mobile-demo">
                    <?php echo('<li><a href="paginaOfferte.php?id='.$_SESSION["azienda_id"].'"><i class="material-icons left">business</i>Proposte di Lavoro</a></li>'); ?>
                    <li><a class="dropdown-trigger" href="#!" data-target="dropdownmenu_mobile"><i class="material-icons left">person</i><i class="material-icons right">arrow_drop_down</i></a></li>
                </ul>
            </div>
        </nav>
        <div class="container">
            <div class="col s12 m8 offset-m2 l6 offset-l3">
                <div class="card-panel white z-depth-1">
                    <h4 class="black-text center">Modifica Offerta di Lavoro</h4>

                    <form method="POST" action="../../../backEnd/controllers/aziende/ControllerOfferta.php?offerta=<?php echo ($offerta["offerta_id"]) ?>">
                        <input type="hidden" name="_method" value="PUT">

                        <div class="input-field col s12">
                            <input id="titolo" type="text" name="titolo" value="<?php echo($offerta["titolo"]) ?>" class="validate black-text" required>
                            <label for="titolo">Titolo</label>
                        </div>
                        <div class="input-field col s12">
                            <textarea id="descrizione" name="descrizione" class="materialize-textarea black-text"><?php echo($offerta["descrizione"]) ?></textarea>
                            <label for="descrizione">Descrizione</label>
                        </div>
                        <div class="input-field col s12">
                            <input id="retribuzione" type="text" name="retribuzione" value="<?php echo($offerta["retribuzione"]) ?>" class="validate black-text" required>
                            <label for="retribuzione">Retribuzione</label>
                        </div>
                        <div class="input-field col s12">
                            <input id="data_scadenza" type="date" name="data_scadenza" value="<?php echo($offerta["data_scadenza"]) ?>" class="validate black-text" required>
                            <label for="data_scadenza">Scadenza</label>
                        </div>
                        <div class="input-field col s12">
                            <select name="tipo_contratto_id" id="contratto" required>
                                <?php echo(generateHtmlOptions($tipiContrattoList, function($key){global $offerta; return $offerta["tipo_contratto_id"] == $key;})); ?>
                            </select>
                            <label for="contratto">Tipo di contratto</label>
                        </div>
                        <div class="input-field col s12">
                            <select name="modalita_lavoro_id" id="modalita_lavoro_id" required>
                                <?php echo(generateHtmlOptions($modalitaLavoroList, function($key){global $offerta; return $offerta["modalita_lavoro_id"] == $key;})); ?>
                            </select>
                            <label for="modalita_lavoro_id">Modalità di lavoro</label>
                        </div>
                        <div class="input-field col s12">
                            </h3  for="competenze">Seleziona tre competenze:</h3>  
                            <?php 
                                for ($i = 0; $i < 3; $i++) {
                                    echo ('<div class="input-field col s12">');
                                    echo ('<select name="competenze'.($i+1).'" id="competenze'.($i+1).'" required>');

                                    if (!isset($competenze[$i]) || !isset($competenze[$i]["competenza_id"])) {
                                        $result = '<option value="0" selected>seleziona competenza</option>';
                                        foreach ($staticOptions as $key => $value) {
                                            $result .= '<option value="' . $key . '">' . $value . "</option>";
                                        }
                                        echo ($result);
                                    }
                                    else {
                                        echo(generateHtmlOptions($competenzeList, function($key){
                                            global $competenze; 
                                            global $i;
                                            return $competenze[$i]["competenza_id"] == $key;
                                        }));
                                    }
                                    echo ('</select>');
                                }
                            ?>
                            
                        </div>
                        <div class="row center">
                            <input type="submit" class="light-blue darken-1 btn-large" value="Salva Offerta">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="message" id="success-message">
            ✅ Salvataggio avvenuto con successo!
        </div>

        <div class="message" id="error-message">
            ❌ Si è verificato un errore durante il salvataggio.
        </div>

        <!--JavaScript at end of body for optimized loading-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
        <script type="text/javascript" src="../js/materialize.js"></script>
        <script type="text/javascript" src="../js/scripts.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var elems = document.querySelectorAll('select');
                M.FormSelect.init(elems);
            });

            function ShowMessage() {
                const params = new URLSearchParams(window.location.search);

                if (params.has('update')) {
                    const updateResult = params.get('update');
                    let msgBox;

                    if (updateResult === 'success') {
                        msgBox = document.getElementById('success-message');
                    }
                    else if (updateResult === 'failure') {
                        msgBox = document.getElementById('error-message');
                    }

                    if (msgBox) {
                        msgBox.style.display = 'block';
                        setTimeout(function() {
                            if (msgBox) msgBox.style.display = 'none';
                        }, 5000); // scompare dopo 5 secondi
                    }
                }
            }

            ShowMessage();

        </script>

    </body>
</html>
