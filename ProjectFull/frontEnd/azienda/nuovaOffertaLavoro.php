
<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once("../../backEnd/controllers/getInfo.php");

    $aziendaData = getInfoAzienda();
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
                  <h4 class="black-text center">Nuova Offerta di Lavoro</h4>

                    <form method="POST" action="../../../backEnd/controllers/aziende/ControllerOfferta.php?sedeId=<?php echo ($aziendaData["sediAzienda"][0]->getSedeId()) ?>">


                        <div class="input-field col s12">
                            <input id="titolo" type="text" name="titolo" class="validate black-text" required>
                            <label for="titolo">Titolo</label>
                        </div>
                        <div class="input-field col s12">
                            <textarea id="descrizione" name="descrizione" class="materialize-textarea black-text"></textarea>
                            <label for="descrizione">Descrizione</label>
                        </div>
                        <div class="input-field col s12">
                            <input id="retribuzione" type="text" name="retribuzione" class="validate black-text" required>
                            <label for="retribuzione">Retribuzione</label>
                        </div>
                        <div class="input-field col s12">
                            <input id="data_scadenza" type="date" name="data_scadenza" class="validate black-text" required>
                            <label for="data_scadenza">Scadenza</label>
                        </div>
                      <div class="input-field col s12">
                        <select name="tipo_contratto" id="contratto" required>
                            <option value="" disabled selected>Seleziona il tipo di contratto</option>
                            <option value="1">Tempo indeterminato</option>
                            <option value="2">Tempo determinato</option>
                            <option value="3">Stage</option>
                            <option value="4">Part-time</option>
                            <option value="5">Freelance</option>
                            <option value="6">Apprendistato</option>
                            <option value="7">Contratto a chiamata</option>
                        </select>
                        <label for="contratto">Tipo di contratto</label>
                    </div>
                    <div class="input-field col s12">
                        <select name="modalita_lavoro" id="modalita_lavoro" required>
                            <option value="" disabled selected>Seleziona la modalità di lavoro</option>
                            <option value="2">Remoto</option>
                            <option value="3">Ibrido</option>
                            <option value="1">In sede</option>
                        </select>
                        <label for="modalita_lavoro">Modalità di lavoro</label>
                    </div>
                     <div class="input-field col s12">
                            </h3  for="competenze">Seleziona tre competenze:</h3>  
                            <select name="competenze1" id="competenze1" required>
                                 <option value="0">seleziona competenza</option>
                                <option value="1">Java</option>
                                <option value="2">Python</option>
                                <option value="3">SQL</option>
                                <option value="4">Project Management</option>
                                <option value="5">React</option>
                                <option value="6">Machine Learning</option>
                                <option value="7">Linux</option>
                                <option value="8">Comunicazione efficace</option>
                                <option value="9">AWS</option>
                                <option value="10">Cybersecurity</option>
                            </select>
                            <div class="input-field col s12">
                            <select name="competenze2" id="competenze2" required>
                                <option value="0">seleziona competenza</option>
                                <option value="1">Java</option>
                                <option value="2">Python</option>
                                <option value="3">SQL</option>
                                <option value="4">Project Management</option>
                                <option value="5">React</option>
                                <option value="6">Machine Learning</option>
                                <option value="7">Linux</option>
                                <option value="8">Comunicazione efficace</option>
                                <option value="9">AWS</option>
                                <option value="10">Cybersecurity</option>
                            </select>
                            <div class="input-field col s12">
                            <select name="competenze3" id="competenze3" required>
                                <option value="0">seleziona competenza</option>
                                <option value="1">Java</option>
                                <option value="2">Python</option>
                                <option value="3">SQL</option>
                                <option value="4">Project Management</option>
                                <option value="5">React</option>
                                <option value="6">Machine Learning</option>
                                <option value="7">Linux</option>
                                <option value="8">Comunicazione efficace</option>
                                <option value="9">AWS</option>
                                <option value="10">Cybersecurity</option>
                            </select>
                        </div>
                        <div class="row center">
                            <input type="submit" class="light-blue darken-1 btn-large" value="Salva Offerta">
                        </div>
                    </form>
                </div>
            </div>
        </div>
            </form>
        <!--JavaScript at end of body for optimized loading-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
        <script type="text/javascript" src="../js/materialize.js"></script>
        <script type="text/javascript" src="../js/scripts.js"></script>
        <script>
    document.addEventListener('DOMContentLoaded', function() {
        var elems = document.querySelectorAll('select');
        M.FormSelect.init(elems);
    });
</script>


        </script>
    </body>
</html>
