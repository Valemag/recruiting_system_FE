<?php
    require_once(__DIR__."/../../backEnd/controllers/utenti/ControllerCandidatura.php");
    require_once(__DIR__."/../../backEnd/controllers/utenti/ControllerUtente.php");

    $offerteLavoro = ControllerCandidatura::getOfferte();
    $datiUtente = ControllerUtente::getInfoUtenteBySession();
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
    </head>
    <body class="grey lighten-5">
        <nav>
            <div class="nav-wrapper light-blue darken-1">
                <a href="#" class="brand-logo center"><img class="responsive-img" src="../assets/logo.png" alt="Logo"></a>
                <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="material-icons">menu</i></a>
                <ul id="nav-mobile" class="left hide-on-med-and-down">
                    <li><a href="paginaCandidature.php"><i class="material-icons left">business_center</i>Candidature</a></li>
                </ul>
                <ul id="nav-mobile" class="right hide-on-med-and-down">
                    <li><a class="dropdown-trigger" href="#!" data-target="dropdownmenu"><i class="material-icons left">person</i><?php echo($datiUtente['username']) ?><i class="material-icons right">arrow_drop_down</i></a></li>
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
                    <li><a href="paginaCandidature.php"><i class="material-icons left">business_center</i>Candidature</a></li>
                    <li><a class="dropdown-trigger" href="#!" data-target="dropdownmenu_mobile"><i class="material-icons left">person</i><?php echo($datiUtente['username']) ?><i class="material-icons right">arrow_drop_down</i></a></li>
                </ul>
            </div>
        </nav>
        <div class="container">
            <div class="col s12 m8 offset-m2 l6 offset-l3">
                <div class="card-panel white z-depth-1">
        <div style="margin-bottom: 20px; text-align: right;">
            <button class="btn yellow darken-2 modal-trigger" data-target="modalAppuntate">
                <i class="material-icons left">star</i> Mostra offerte appuntate
        </button>
        </div>

        <?php
    if ($offerteLavoro == NULL || count($offerteLavoro) == 0) {
        echo ("Nessuna nuova offerta disponibile al momento.");
    } else {
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
                    <button class="btn light-blue darken-1 modal-trigger" href="#modal' . $index . '" style="margin-right: 10px;">
                        Mostra dettagli offerta
                    </button> 
                    <button class="btn yellow darken-2" onclick="appuntaOfferta(' . $offertaSingola["offerta_id"] . ')">
                        <i class="material-icons left">star</i> Appunta
                    </button>

                    <form method="POST" action="../../backEnd/controllers/utenti/ControllerCandidatura.php?op=new_candidatura" enctype="multipart/form-data" style="display: flex; gap: 10px; align-items: center;">
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

            echo('<br>');
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
<script>
document.querySelector('[data-target="modalAppuntate"]').addEventListener('click', function () {
    fetch('../../backEnd/controllers/utenti/ControllerOffertePreferite.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ action: 'get' })
    })
    .then(response => response.json()) 
    .then(offerte => {
        const lista = document.getElementById('listaAppuntate');
        lista.innerHTML = '';

        if (!Array.isArray(offerte) || offerte.length === 0) {
            lista.innerHTML = '<p>Nessuna offerta appuntata.</p>';
            return;
        }

        offerte.forEach(offerta => {
        const offertaDiv = document.createElement('div');
        offertaDiv.className = 'card-panel grey lighten-4 black-text';

        let contenuto = `
            <h5>${offerta.titolo || 'Offerta non disponibile'}</h5>
            <p><strong>Azienda:</strong> ${offerta.nome || 'N/A'}</p>
            <p><strong>Tipo contratto:</strong> ${offerta.tipo || 'N/A'}</p>
            <p><strong>Retribuzione:</strong> ${offerta.retribuzione || 'N/A'}</p>
            <button class="btn red remove-btn" data-id="${offerta.offerta_id}">
                <i class="material-icons left">delete</i>Rimuovi
            </button>
        `;

    offertaDiv.innerHTML = contenuto;
    lista.appendChild(offertaDiv);
});


        document.querySelectorAll('.remove-btn').forEach(button => {
            button.addEventListener('click', function () {
                const offertaId = this.getAttribute('data-id');
                fetch('../../backEnd/controllers/utenti/ControllerOffertePreferite.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=remove&offerta_id=${offertaId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        M.toast({html: 'Offerta rimossa con successo', classes: 'green'});
                        this.parentElement.remove();
                    } else {
                        M.toast({html: 'Errore durante la rimozione', classes: 'red'});
                    }
                });
            });
        });
    })
    .catch(error => {
        console.error('Errore nel fetch:', error);
        document.getElementById('listaAppuntate').innerHTML = '<p>Errore nel caricamento delle offerte.</p>';
    });
});
</script>


<script>
function appuntaOfferta(offertaId) {
    const params = new URLSearchParams();
    params.append('action', 'add');
    params.append('offerta_id', offertaId);

    fetch('../../backEnd/controllers/utenti/ControllerOffertePreferite.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: params
    })
    .then(response => response.json())
    .then(data => {
        const msg = data.message || 'Errore sconosciuto';
        const style = data.success ? 'green' : 'red';
        M.toast({html: msg, classes: 'rounded ' + style});
    })
    .catch(error => {
        console.error('Errore:', error);
        M.toast({html: 'Errore durante l\'appuntamento', classes: 'rounded red'});
    });
}
</script>


        <!-- Modale offerte appuntate -->
    <div id="modalAppuntate" class="modal">
        <div class="modal-content">
            <h4>Offerte Appuntate</h4>
        <div id="listaAppuntate"></div>
        </div>
        <div class="modal-footer">
        <a href="#!" class="modal-close waves-effect waves-green btn-flat">Chiudi</a>
        </div>
    </div>

    </body>
</html>
