<?php
    require_once("../../backEnd/controllers/aziende/ControllerAzienda.php");

    $aziendaData = ControllerAzienda::getInfoAzienda();
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
                        <?php echo ($aziendaData["nome"]) ?>
                    <i class="material-icons right">arrow_drop_down</i></a></li>
                </ul>
                <ul id="dropdownmenu" class="dropdown-content light-blue darken-1">
                    <li><a href="paginaProfilo.php?id=<?php echo ($_SESSION["azienda_id"]) ?>"><i class="material-icons left">assignment_ind</i>Visualizza Profilo</a></li>
                    <li class="divider"></li>
                    <li><a href="../../backEnd/controllers/logout.php"><i class="material-icons left">exit_to_app</i>Logout</a></li>
                </ul>
                <ul id="dropdownmenu_mobile" class="dropdown-content light-blue darken-2">
                    <li><a href="paginaProfilo.php?id=<?php echo ($_SESSION["azienda_id"]) ?>"><i class="material-icons left">assignment_ind</i>Visualizza Profilo</a></li>
                    <li class="divider"></li>
                    <li><a href="../../backEnd/controllers/logout.php"><i class="material-icons left">exit_to_app</i>Logout</a></li>
                </ul>
                <ul class="sidenav light-blue darken-1" id="mobile-demo">
                    <li><a class="dropdown-trigger" href="#!" data-target="dropdownmenu_mobile"><i class="material-icons left">person</i>
                        <?php echo ($aziendaData["nome"]) ?>
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
                            <form method="POST" action="../../../backEnd/controllers/aziende/ControllerAzienda.php?op=generic_info">
                                <div class="input-field col s6">
                                    <input id="nome" type="text" name="nome" value="<?php echo($aziendaData["nome"]) ?>" class="validate black-text" required>
                                    <label for="nome">Nome Azienda</label>
                                </div>
                                <div class="input-field col s6">
                                    <input id="ragione_sociale" type="text" name="ragione_sociale" value="<?php echo($aziendaData["ragione_sociale"]) ?>" class="validate black-text" required>
                                    <label for="ragione_sociale">Ragione Sociale</label>
                                </div>
                                <div class="input-field col s6">
                                    <input id="partita_iva" type="text" name="partita_iva" value="<?php echo($aziendaData["partita_iva"]) ?>" class="validate black-text" required>
                                    <label for="partita_iva">Partita IVA</label>
                                </div>
                                <div class="input-field col s6">
                                    <input id="sito_web" type="text" name="sito_web" value="<?php echo($aziendaData["sito_web"]) ?>" class="validate black-text" required>
                                    <label for="sito_web">Link Sito Web</label>
                                </div>
                                <div class="input-field col s6">
                                    <input id="telefono_contatto" type="tel" name="telefono_contatto" value="<?php echo($aziendaData["telefono_contatto"]) ?>" class="validate black-text" required>
                                    <label for="telefono_contatto">Telefono</label>
                                </div>
                                <div class="input-field col s6">
                                    <input id="email" type="email" name="email" value="<?php echo($aziendaData["email"]) ?>" class="validate black-text" required>
                                    <label for="email">Email</label>
                                </div>
                                <div class="input-field col s12">
                                    <textarea id="descrizione" name="descrizione" class="materialize-textarea black-text"><?php echo($aziendaData["descrizione"]) ?></textarea>
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
                            <form method="POST" action="../../../backEnd/controllers/aziende/ControllerAzienda.php?op=password">
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
                                Modifica Immagine Azienda
                            </h4>
                            <div class="col l2 square-container">
                                <?php 
                                    if(isset($aziendaData["logo"])){
                                        echo('<img id="propic" src="'.$aziendaData["logo"].'" class="circle responsive-img">');
                                    }
                                    else{
                                        echo('<img id="propic" src="../assets/defaultPropic.jpg" class="circle responsive-img">');
                                    }
                                ?>
                            </div>
                            <form method="POST" action="../../../backEnd/controllers/aziende/ControllerAzienda.php?op=logo" enctype="multipart/form-data">
                                <div class="input-field col s12">
                                    <div class="file-field input-field">
                                        <div class="btn light-blue darken-1">
                                            <span id="tastoImmagine">Nuova Immagine Azienda</span>
                                            <input type="file" id="logo" name="logo">
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

           <!-- CAMBIO SEDI AZIENDA -->
            <div class="col s12 m8 offset-m2 l6 offset-l3">
                <div class="card-panel white z-depth-1">
                    <div class="row valign-wrapper">
                        <div class="col s12">
                            <h4 class="black-text">
                                Modifica Sede
                            </h4>
                            <form method="POST" action="../../../backEnd/controllers/aziende/ControllerAzienda.php?op=sede">
                                <div class="row">
                                    <div class="input-field col s12">
                                        <input id="paese" type="text" name="paese" value="<?php echo($aziendaData["sediAzienda"][0]->getPaese()) ?>" class="validate black-text" required>
                                        <label for="paese">Paese</label>
                                    </div>
                                    <div class="input-field col s12">
                                        <input id="regione" type="text" name="regione" value="<?php echo($aziendaData["sediAzienda"][0]->getRegione()) ?>" class="validate black-text" required>
                                        <label for="regione">Regione</label>
                                    </div>
                                    <div class="input-field col s12">
                                        <input id="citta" type="text" name="citta" value="<?php echo($aziendaData["sediAzienda"][0]->getCitta()) ?>" class="validate black-text" required>
                                        <label for="citta">Città</label>
                                    </div>
                                    <div class="input-field col s12">
                                        <input id="indirizzo" type="text" name="indirizzo" value="<?php echo($aziendaData["sediAzienda"][0]->getIndirizzo()) ?>" class="validate black-text" required>
                                        <label for="indirizzo">Indirizzo</label>
                                    </div>
                                    <div class="col right">
                                        <input type="submit" class="light-blue darken-1 btn-small" value="Aggiorna Sede Aziendale">
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