<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
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
    </head>
    <body class="grey lighten-5">
        <nav>
            <div class="nav-wrapper light-blue darken-1">
                <a href="#" class="brand-logo center">Logo</a>
                <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                <ul id="nav-mobile" class="left hide-on-med-and-down">
                    <li><a href="index.html"><i class="material-icons left">home</i>Home</a></li>
                    <li><a href="candidature.html"><i class="material-icons left">business_center</i>Candidature</a></li>
                    <li><a href="proposteLavoro.html"><i class="material-icons left">business</i>Proposte di Lavoro</a></li>
                </ul>
                <ul id="nav-mobile" class="right hide-on-med-and-down">
                    <li><a class="dropdown-trigger" href="#!" data-target="dropdownmenu"><i class="material-icons left">person</i>Nome Azienda<i class="material-icons right">arrow_drop_down</i></a></li>
                </ul>
                <ul id="dropdownmenu" class="dropdown-content light-blue darken-1">
                    <li><a href="paginaProfilo.html"><i class="material-icons left">assignment_ind</i>Visualizza Profilo</a></li>
                    <li><a href="modificaProfilo.html"><i class="material-icons left">edit</i>Modifica Profilo</a></li>
                    <li class="divider"></li>
                    <li><a href="logout.html"><i class="material-icons left">exit_to_app</i>Logout</a></li>
                </ul>
                <ul id="dropdownmenu_mobile" class="dropdown-content light-blue darken-2">
                    <li><a href="paginaProfilo.html"><i class="material-icons left">assignment_ind</i>Visualizza Profilo</a></li>
                    <li><a href="modificaProfilo.html"><i class="material-icons left">edit</i>Modifica Profilo</a></li>
                    <li class="divider"></li>
                    <li><a href="logout.html"><i class="material-icons left">exit_to_app</i>Logout</a></li>
                </ul>
                <ul class="sidenav light-blue darken-1" id="mobile-demo">
                    <li><a href="index.html"><i class="material-icons left">home</i>Home</a></li>
                    <li><a href="candidature.html"><i class="material-icons left">business_center</i>Candidature</a></li>
                    <li><a href="proposteLavoro.html"><i class="material-icons left">business</i>Proposte di Lavoro</a></li>
                    <li><a class="dropdown-trigger" href="#!" data-target="dropdownmenu_mobile"><i class="material-icons left">person</i>Nome Azienda<i class="material-icons right">arrow_drop_down</i></a></li>
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
                                Dati Personali
                            </h4>
                            <form method="POST" action="#">
                                <div class="input-field col s6">
                                    <input id="ragsoc" type="text" name="ragsoc" class="validate black-text" required>
                                    <label for="ragsoc">Nome Azienda</label>
                                </div>
                                <div class="input-field col s6">
                                    <input id="piva" type="text" name="piva" class="validate black-text" required>
                                    <label for="piva">Partita IVA</label>
                                </div>
                                <div class="input-field col s12">
                                    <input id="username" type="text" name="username" class="validate black-text" required>
                                    <label for="username">Username</label>
                                </div>
                                <div class="input-field col s6">
                                    <input id="phone" type="tel" name="phone" class="validate black-text" required>
                                    <label for="phone">Telefono</label>
                                </div>
                                <div class="input-field col s6">
                                    <input id="email" type="email" name="email" class="validate black-text" required>
                                    <label for="email">Email</label>
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
                            <form method="POST" action="">
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
                                Aggiorna Immagine Azienda
                            </h4>
                            <form method="POST" action="">
                                <div class="input-field col s12">
                                    <div class="file-field input-field">
                                        <div class="btn light-blue darken-1">
                                            <span id="tastoImmagine">Nuova Immagine Azienda</span>
                                            <input type="file" name="fotoProfilo">
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

            <!-- CAMBIO DESCRIZIONE -->
            <div class="col s12 m8 offset-m2 l6 offset-l3">
                <div class="card-panel white z-depth-1">
                    <div class="row valign-wrapper">
                        <div class="col s12">
                            <h4 class="black-text">
                                Modifica Descrizione
                            </h4>
                            <form method="POST" action="">
                                <div class="input-field col s12">
                                    <textarea id="description" name="description" class="materialize-textarea black-text"></textarea>
                                    <label for="description">Una Breve Descrizione della tua Azienda</label>
                                </div>
                                <div class="col right">
                                    <input type="submit" class="light-blue darken-1 btn-small" value="Aggiorna Descrizione Azienda">
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
                                Modifica Dati Sedi Azienda
                            </h4>
                            <form method="POST" action="">
                                <div class="input-field col s12">
                                    <textarea id="sediAzienda" name="sediAzienda" class="materialize-textarea black-text"></textarea>
                                    <label for="sediAzienda">Sedi Azienda</label>
                                </div>
                                <div class="col right">
                                    <input type="submit" class="light-blue darken-1 btn-small" value="Aggiorna Sedi Azienda">
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