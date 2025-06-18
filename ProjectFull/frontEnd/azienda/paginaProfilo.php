<!DOCTYPE html>
<html>
    <head>
        <!--Import Google Icon Font-->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <!--Import materialize.css-->
        <link type="text/css" rel="stylesheet" href="../css/materialize.css"  media="screen,projection"/>

        <!--Let browser know website is optimized for mobile-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <script>
            window.onload = function() {
                var aziendaId = 123;  // ID azienda
                var url = "http://127.0.0.1/backEnd/controllers/aziende/getInfo.php?id=" + aziendaId;
          
                var xhr = new XMLHttpRequest();
                xhr.open("GET", url, true);
          
                // Aggiungi il token nell'intestazione
                xhr.setRequestHeader("Authorization", "Bearer " + token);
          
                xhr.onload = function() {
                    if (xhr.status == 200) {
                        try {
                            var responseData = JSON.parse(xhr.responseText);
                            // Se ci sono dati, aggiorna l'interfaccia
                            if (responseData.error) {
                                console.error("Errore: " + responseData.error);
                            } else {
                                // Aggiorna l'interfaccia utente con i dati ricevuti
                                document.getElementById("email").textContent = responseData.email;
                                document.getElementById("nome").textContent = responseData.nome;
                                document.getElementById("descrizione").textContent = responseData.descrizione;
                                document.getElementById("telefono").textContent = responseData.telefono;
                                document.getElementById("logo").src = responseData.logo;
                            }
                        } catch (e) {
                            console.error("Errore nel parsing della risposta JSON:", e);
                        }
                    } else {
                        console.error("Errore nella richiesta:", xhr.status, xhr.statusText);
                    }
                };
          
                xhr.onerror = function() {
                    console.error("Errore di rete durante la richiesta.");
                };
          
                xhr.send();
            };
        </script>          
    </head>
    <body class="black">
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

            <!-- FOTO, NOME AZIENDA, USERNAME -->

            <div class="col s12 m8 offset-m2 l6 offset-l3">
                <div class="card-panel grey darken-4 z-depth-1">
                    <div class="row valign-wrapper">
                        <div class="col s4 l2 square-container">
                            <img id="logo" src="img/test.jpg" alt="" class="circle responsive-img">
                        </div>
                        <div class="col s8 l10">
                            <h4 id="nome" class="white-text">
                                Nome Azienda
                            </h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DESCRIZIONE -->

            <div class="col s12 m8 offset-m2 l6 offset-l3">
                <div class="card-panel grey darken-4 z-depth-1">
                    <div class="row valign-wrapper">
                        <div class="col s12">
                            <p id="descrizione" class="white-text">
                                Descrizione
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CONTATTI -->
             <div class="col s12 m8 offset-m2 l6 offset-l3">
                <div class="card-panel grey darken-4 z-depth-1">
                    <div class="row valign-wrapper">
                        <div class="col s12 white-text">
                            <p>
                                <h6>Contatti</h6>
                                <i id="telefono" class="material-icons prefix">phone</i>
                                <br>
                                <i id="email" class="material-icons prefix">email</i>
                                <h6>Sedi Azienda</h6>
                                <div id="sedi">
                                    <i class="material-icons prefix">location_on</i> Sede Azienda Principale
                                </div>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--JavaScript at end of body for optimized loading-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
        <script type="text/javascript" src="../js/materialize.js"></script>
        <script type="text/javascript" src="../js/scripts.js"></script>
    </body>
</html>