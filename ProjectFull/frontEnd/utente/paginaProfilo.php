<?php

    require("../../backEnd/controllers/getInfo.php");

    $userData = getInfoUtente();

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
    <body class="black">
        <nav>
            <div class="nav-wrapper light-blue darken-1">
                <a href="#" class="brand-logo center">Logo</a>

                <?php 
                
                    if(isset($_SESSION["utente_id"]) && $_SESSION["utente_id"] == $_GET["id"]){
                        echo('<a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                    <ul id="nav-mobile" class="left hide-on-med-and-down">
                    <li><a href="paginaCandidature.php"><i class="material-icons left">business_center</i>Candidature</a></li>
                    <li><a href="offerteLavoro.php"><i class="material-icons left">business_center</i>offerte</a></li>
                </ul>
                <ul id="nav-mobile" class="right hide-on-med-and-down">
                    <li><a class="dropdown-trigger" href="#!" data-target="dropdownmenu"><i class="material-icons left">person</i><i class="material-icons right">arrow_drop_down</i></a></li>
                </ul>
                <ul id="dropdownmenu" class="dropdown-content light-blue darken-1">
                    <li><a href="modificaProfilo.html"><i class="material-icons left">edit</i>Modifica Profilo</a></li>
                    <li class="divider"></li>
                    <li><a href="../../backEnd/controllers/logout.php"><i class="material-icons left">exit_to_app</i>Logout</a></li>
                </ul>
                <ul id="dropdownmenu_mobile" class="dropdown-content light-blue darken-2">
                    <li><a href="modificaProfilo.html"><i class="material-icons left">edit</i>Modifica Profilo</a></li>
                    <li class="divider"></li>
                    <li><a href="../../backEnd/controllers/logout.php"><i class="material-icons left">exit_to_app</i>Logout</a></li>
                </ul>
                <ul class="sidenav light-blue darken-1" id="mobile-demo">
                    <li><a href="paginaCandidature.php"><i class="material-icons left">business_center</i>Candidature</a></li>
                    <li><a class="dropdown-trigger" href="#!" data-target="dropdownmenu_mobile"><i class="material-icons left">person</i>i class="material-icons right">arrow_drop_down</i></a></li>
                </ul>');
                    }

                ?>
               
            </div>
        </nav>
        <div class="container">

            <!-- FOTO, NOME, COGNOME, USERNAME -->

            <div class="col s12 m8 offset-m2 l6 offset-l3">
                <div class="card-panel grey darken-4 z-depth-1">
                    <div class="row valign-wrapper">
                        <div class="col s4 l2 square-container">
                            <?php 

                                if(isset($userData["immagine_profilo"])){
                                    echo('<img id="propic" src="'.$userData["immagine_profilo"].'" class="circle responsive-img">');
                                }
                                else{
                                    echo('<img id="propic" src="../assets/defaultPropic.jpg" class="circle responsive-img">');
                                }
                                
                            ?>
                        </div>
                        <div class="col s8 l10">
                            <h4 id="nomeCognome" class="white-text">
                                <?php echo($userData["nome"]." ".$userData["cognome"])?>
                            </h4>
                            <p id="username" class="white-text">
                            <?php echo($userData["username"])?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- DESCRIZIONE, COMPETENZE -->

            <div class="col s12 m8 offset-m2 l6 offset-l3">
                <div class="card-panel grey darken-4 z-depth-1">
                    <div class="row valign-wrapper">
                        <div class="col s12">
                            <p id="descrizione" class="white-text">
                            <?php echo($userData["descrizione"])?>
                            </p>
                            <?php

                                if(isset($userData["competenze"])){

                                    echo("<p>
                                    Competenze
                                    <br>");

                                    foreach($userData["competenze"] as $competenza){

                                        echo($competenza -> getCompetenza() . "<br>");

                                    }
                            
                                    echo("</p>");

                                }

                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col s12 m8 offset-m2 l6 offset-l3">
                <div class="card-panel grey darken-4 z-depth-1">
                    <div class="row valign-wrapper">
                        <div class="col s12">
                            <?php

                                if(isset($userData["documenti"])){

                                    echo("<p>
                                    Documenti
                                    <br>");

                                    foreach($userData["documenti"] as $documento){

                                        echo("<a href='".$documento["path"]."'>".$documento["nome"]."</a><br>");

                                    }
                            
                                    echo("</p>");
                                }

                            ?>
                        </div>
                        <?php
                        
                            if(isset($_SESSION["utente_id"]) && $_SESSION["utente_id"] == $_GET["id"]){

                                echo('Carica un documento:
                                    <form action="../../backEnd/controllers/utenti/uploadFile.php" method="post" enctype="multipart/form-data">

                                        <input type="file" name="file" id="file">
                                        <input type="submit" value="Carica">

                                    </form>');
                            }

                        ?>

                    </div>
                </div>
            </div>

            <!-- CONTATTI, CV -->
            <div class="col s12 m8 offset-m2 l6 offset-l3">
                <div class="card-panel grey darken-4 z-depth-1">
                    <div class="row valign-wrapper">
                        <div class="col s12">
                            <p class="white-text">
                                Contatti
                                <br>
                                telefono: <?php echo($userData["telefono_contatto"])?>
                                <br>
                                email: <?php echo($userData["email"])?>
                            </p>
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