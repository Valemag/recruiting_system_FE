<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once(__DIR__."/../../backEnd/controllers/utenti/ControllerUtente.php");

    $utente = getInfoUtenteBySession();
    $competenzeId = $utente["competenzeId"];
    $competenzeObj = $utente["competenze"];

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

    function generateHtmlOptions($competenza): string {
        global $competenzeList;
        $result = "";
        foreach ($competenzeList as $key => $value) {
            $result .= '<option value="' . $key . '" ' . (($competenza == $key) ? "selected" : "") . ">" . $value . "</option>";
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
        <link rel="stylesheet" href="../css/custom.css">
        <style>
            #message {
                display:none;
                color: #fff;
                position: relative;
            }
            
            /* Add a green text color and a checkmark when the requirements are right */
            .valid {
                color: green;
            }

            .valid:before {
                position: relative;
                left: -35px;
            }

            /* Add a red text color and an "x" icon when the requirements are wrong */
            .invalid {
                color: red;
            }

            .invalid:before {
                position: relative;
                left: -35px;
            }
            .dropdown-content li > span {
                color: black !important;
            }
        </style>

        <!--Let browser know website is optimized for mobile-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <link rel="stylesheet" href="../css/custom.css">
    </head>
    <body class="grey lighten-5">
        <nav>
            <div class="nav-wrapper light-blue darken-1">
                <a href="#" class="brand-logo center"><img class="responsive-img" src="../assets/logo.png" alt="Logo"></a>
                <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                <ul id="nav-mobile" class="right hide-on-med-and-down">
                    <li><a class="dropdown-trigger" href="#!" data-target="dropdownmenu"><i class="material-icons left">person</i>
                        <?php echo ($utente["username"]) ?>
                    <i class="material-icons right">arrow_drop_down</i></a></li>
                </ul>
                <ul id="dropdownmenu" class="dropdown-content light-blue darken-1">
                    <li><a href="paginaProfilo.php?id=<?php echo ($_SESSION["utente_id"]) ?>"><i class="material-icons left">assignment_ind</i>Visualizza Profilo</a></li>
                    <li class="divider"></li>
                    <li><a href="../../backEnd/controllers/logout.php"><i class="material-icons left">exit_to_app</i>Logout</a></li>
                </ul>
                <ul id="dropdownmenu_mobile" class="dropdown-content light-blue darken-2">
                    <li><a href="paginaProfilo.php?id=<?php echo ($_SESSION["utente_id"]) ?>"><i class="material-icons left">assignment_ind</i>Visualizza Profilo</a></li>
                    <li class="divider"></li>
                    <li><a href="../../backEnd/controllers/logout.php"><i class="material-icons left">exit_to_app</i>Logout</a></li>
                </ul>
                <ul class="sidenav light-blue darken-1" id="mobile-demo">
                    <li><a class="dropdown-trigger" href="#!" data-target="dropdownmenu_mobile"><i class="material-icons left">person</i>
                        <?php echo ($utente["nome"]) ?>
                    <i class="material-icons right">arrow_drop_down</i></a></li>
                </ul>
            </div>
        </nav>
        <div class="container">

            <!-- MODIFICA INFO PRINCIPALI -->

            <div class="col s12 m8 offset-m2 l6 offset-l3">
                <div class="card-panel white z-depth-1">
                    <div class="row valign-wrapper">
                        <div class="col s12">
                            <h4 class="black-text">
                                Modifica Dati Personali
                            </h4>
                            <form method="POST" action="../../../backEnd/controllers/utenti/ControllerUtente.php?op=generic_info">
                                <div class="input-field col s6">
                                    <input id="nome" type="text" name="nome" value="<?php echo($utente["nome"]) ?>" class="validate black-text" required>
                                    <label for="nome">Nome</label>
                                </div>
                                <div class="input-field col s6">
                                    <input id="cognome" type="text" name="cognome" value="<?php echo($utente["cognome"]) ?>" class="validate black-text" required>
                                    <label for="cognome">Cognome</label>
                                </div>
                                <div class="input-field col s6">
                                    <input id="username" type="text" name="username" value="<?php echo($utente["username"]) ?>" class="validate black-text" required>
                                    <label for="username">Username</label>
                                </div>
                                <div class="input-field col s6">
                                    <input id="telefono_contatto" type="tel" name="telefono_contatto" value="<?php echo($utente["telefono_contatto"]) ?>" class="validate black-text" required>
                                    <label for="telefono_contatto">Telefono</label>
                                </div>
                                <div class="input-field col s6">
                                    <input id="email" type="email" name="email" value="<?php echo($utente["email"]) ?>" class="validate black-text" required>
                                    <label for="email">Email</label>
                                </div>
                                <div class="input-field col s12">
                                    <textarea id="descrizione" name="descrizione" class="materialize-textarea black-text"><?php echo($utente["descrizione"]) ?></textarea>
                                    <label for="descrizione">Descrizione</label>
                                </div>
                                <div class="col right">
                                    <input type="submit" class="light-blue darken-1 btn-small" value="Salva Modifiche">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CAMBIO PASSWORD -->

            <div class="col s12 m8 offset-m2 l6 offset-l3">
                <div class="card-panel white z-depth-1">
                    <div class="row valign-wrapper">
                        <div class="col s12">
                            <h4 class="black-text">
                                Modifica Password
                            </h4>
                            <form method="POST" action="../../../backEnd/controllers/utenti/ControllerUtente.php?op=password">
                                <div class="input-field col s12">
                                    <input type="password" class="black-text" id="password" name="password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Ci devono essere almeno 8 caratteri, di cui un numero, una lettera maiuscola ed una minuscola." required>
                                    <label for="password">Nuova Password</label>
                                    <div id="message">
                                        <p class="black-text">La password deve contenere:</p>
                                        <ul>
                                            <li id="letter" class="invalid">Almeno una lettera minuscola</li>
                                            <li id="capital" class="invalid">Almeno una lettera maiuscola</li>
                                            <li id="number" class="invalid">Almeno un numero</li>
                                            <li id="length" class="invalid">Minimo 8 caratteri</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col right">
                                    <input type="submit" class="light-blue darken-1 btn-small" value="Aggiorna Password">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CAMBIO FOTO PROFILO -->
            <div class="col s12 m8 offset-m2 l6 offset-l3">
                <div class="card-panel white z-depth-1">
                    <div class="row valign-wrapper">
                        <div class="col s12">
                            <h4 class="black-text">
                                Modifica Immagine Profilo
                            </h4>
                            <div class="col l2 square-container">
                                <?php 
                                    if(isset($utente["immagine_profilo"])){
                                        echo('<img id="propic" src="'.$utente["immagine_profilo"].'" class="circle responsive-img">');
                                    }
                                    else{
                                        echo('<img id="propic" src="../assets/defaultPropic.jpg" class="circle responsive-img">');
                                    }
                                ?>
                            </div>
                            <form method="POST" action="../../../backEnd/controllers/utenti/ControllerUtente.php?op=immagine_profilo" enctype="multipart/form-data">
                                <div class="input-field col s12">
                                    <div class="file-field input-field">
                                        <div class="btn light-blue darken-1">
                                            <span id="tastoImmagine">Nuova Immagine Azienda</span>
                                            <input type="file" id="immagine_profilo" name="immagine_profilo">
                                        </div>
                                        <div class="file-path-wrapper">
                                            <input class="file-path validate black-text" type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="col right">
                                    <input type="submit" class="light-blue darken-1 btn-small" value="Aggiorna Immagine">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col s12 m8 offset-m2 l6 offset-l3">
                <div class="card-panel white z-depth-1">
                    <div class="row valign-wrapper">
                        <div class="col s12">
                            <h4 class="black-text">
                                Modifica Competenze
                            </h4>
                            <form method="POST" action="../../../backEnd/controllers/utenti/ControllerUtente.php?op=competenze">
                                
                                <div class="row">
                                    <?php

                                        for ($i = 0; $i < 3; $i++) {
                                            echo ('<div class="input-field col s12 m4">');
                                            echo ('<select name="competenze'.($i+1).'" id="competenze'.($i+1).'" required>');

                                            if (!isset($competenzeObj[$i]) || $competenzeObj[$i]->getCompetenza() === "") {
                                                $result = '<option value="0" selected>seleziona competenza</option>';
                                                foreach ($staticOptions as $key => $value) {
                                                    $result .= '<option value="' . $key . '">' . $value . "</option>";
                                                }
                                                echo ($result);
                                            }
                                            else {
                                                echo(generateHtmlOptions($competenzeId[$i]));
                                            }
                                            echo ('</select>');
                                            echo ('<label for="competenze'.($i+1).'">Competenza '.($i+1).'</label>');
                                            echo ('</div>');
                                        }

                                    ?>
                                    <div class="col right">
                                        <input type="submit" class="light-blue darken-1 btn-small" value="Aggiorna Competenze">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!--JavaScript at end of body for optimized loading-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
        <script type="text/javascript" src="../js/materialize.js"></script>
        <script type="text/javascript" src="../js/scripts.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var elems = document.querySelectorAll('select');
                M.FormSelect.init(elems);
            });

            var myInput = document.getElementById("password");
            var letter = document.getElementById("letter");
            var capital = document.getElementById("capital");
            var number = document.getElementById("number");
            var length = document.getElementById("length");

            // When the user clicks on the password field, show the message box
            myInput.onfocus = function() {
            document.getElementById("message").style.display = "block";
            }

            // When the user clicks outside of the password field, hide the message box
            myInput.onblur = function() {
            document.getElementById("message").style.display = "none";
            }

            // When the user starts to type something inside the password field
            myInput.onkeyup = function() {
            // Validate lowercase letters
            var lowerCaseLetters = /[a-z]/g;
            if(myInput.value.match(lowerCaseLetters)) {
                letter.classList.remove("invalid");
                letter.classList.add("valid");
            } else {
                letter.classList.remove("valid");
                letter.classList.add("invalid");
            }

            // Validate capital letters
            var upperCaseLetters = /[A-Z]/g;
            if(myInput.value.match(upperCaseLetters)) {
                capital.classList.remove("invalid");
                capital.classList.add("valid");
            } else {
                capital.classList.remove("valid");
                capital.classList.add("invalid");
            }

            // Validate numbers
            var numbers = /[0-9]/g;
            if(myInput.value.match(numbers)) {
                number.classList.remove("invalid");
                number.classList.add("valid");
            } else {
                number.classList.remove("valid");
                number.classList.add("invalid");
            }

            // Validate length
            if(myInput.value.length >= 8) {
                length.classList.remove("invalid");
                length.classList.add("valid");
            } else {
                length.classList.remove("valid");
                length.classList.add("invalid");
            }
            }
        </script>

    </body>
</html>