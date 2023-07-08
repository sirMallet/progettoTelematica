<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Web Validator</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

  <script>
    function createTabella(response) {
      var risultato = response;
      var tabella = document.getElementById("tabella-risultati");

      var markup = "<tr><th>Codice</th><th>Cliente</th><th>Data inizio</th>"
      markup += "<th>Indirizzo</th><th>Città</th><th></th><th></th><th></th><th></th></tr>";

      for (var i = 0; i < risultato.length; i++) {
        markup += "<tr>";
        markup += "<td class='align-middle'>" + risultato[i].codCantiere + "</td><td class='align-middle'>" + risultato[i].cliente + "</td><td class='align-middle'>" + risultato[i].dataInizio + "</td>";
        if (checkSwitch) {
          markup += "<td class='align-middle'>" + risultato[i].dataFine + "</td>";
        }
        markup += "<td class='align-middle'>" + risultato[i].indirizzo + "</td><td class='align-middle'>" + risultato[i].citta + "</td>";
        markup += "<td class='align-middle'> <button type='button' class='btn btn-warning' data-bs-toggle='modal' data-bs-target='#modaleInterventi' name='pulsanteInterventi' id='" + risultato[i].codCantiere + "' >Gestisci interventi</button> </td>";
        markup += "<td class='align-middle'> <button type='button' class='btn btn-warning' data-bs-toggle='modal' data-bs-target='#modaleMateriali' name='pulsanteMateriali' id='" + risultato[i].codCantiere + "' >Gestisci materiali</button> </td>";
        markup += "<td class='align-middle'> <button type='button' class='btn btn-warning' data-bs-toggle='modal' data-bs-target='#modaleOperai' name='pulsanteOperai' id='" + risultato[i].codCantiere + "' >Gestisci operai</button> </td>";
        markup += "<td class='align-middle'> <button type='button' class='btn btn-danger' data-bs-toggle='modal' data-bs-target='#modaleElimina' name='pulsanteElimina' id='" + risultato[i].codCantiere + "' >Elimina</button> </td>";
        markup += "</tr>";
      }

      tabella.innerHTML = markup;
    }
  </script>
</head>

<body>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>

  <div class="container-fluid">
    <div class="col">

      <!-- Title -->
      <div class="row">
        <div class="col">
          <div class="card border-0">
            <div class="card-body">
              <h2 class="card-title">Web Validator</h2>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-11">
          <div class="card border-0">
            <div class="card-body">
              <input type="url" autocomplete="off" class="form-control" id="formURL" name="formURL">
            </div>
          </div>
        </div>

        <div class="col-md-1 justify-content-end">
          <div class="card border-0">
            <div class="card-body">
              <button type="button" class="btn btn-primary" name="sendButton">Invia</button>
            </div>
          </div>
        </div>

      </div>

      <!-- Body -->
      <div class="row">
        <!-- Table -->
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <div id="tabella">
                <table class='table table-hover' id="tabella-risultati">
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Controls -->


        <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" id="modaleAggiungi">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h2 class="modal-title h5 " id="modaleAggiungi">Nuovo cantiere</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi finestra modale"></button>
              </div>

              <div class="modal-header">
                <h2 class="modal-title h5 " id="modaleAggiungi">Nuovo cantiere</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi finestra modale"></button>
              </div>

              <div class="modal-body">
                <form id="nuovoCantiere-form" method="POST">
                  <div class="mb-3">
                    <h5 class="card-subtitle mb-2 text-muted">Cliente</h5>
                    <input type="text" autocomplete="off" class="form-control" id="nomeClienteAdd" name="nomeClienteAdd" placeholder="">
                  </div>

                  <div class="mb-3">
                    <h5 class="card-subtitle mb-2 text-muted">Data inizio</h5>
                    <input type="date" id="dataInizioAdd" name="dataInizioAdd">
                  </div>

                  <div class="mb-3">
                    <h5 class="card-subtitle mb-2 text-muted">Indirizzo</h5>
                    <input type="text" autocomplete="off" class="form-control" id="indirizzoAdd" name="indirizzoAdd" placeholder="">
                  </div>

                  <div class="mb-3">
                    <h5 class="card-subtitle mb-2 text-muted">Citta</h5>
                    <input type="text" autocomplete="off" class="form-control" id="cittaAdd" name="cittaAdd" placeholder="">
                  </div>
              </div>
              <div class="modal-footer">
                <button class="btn btn-success" type="submit" name="Invia">Aggiungi</button>
              </div>
              </form>

              <?php
              if (isset($_POST['Invia'])) {
                $_POST['nomeClienteAdd'] = '';
                $_POST['dataInizioAdd'] = '';
                $_POST['indirizzoAdd'] = '';
                $_POST['cittaAdd'] = '';
              }
              ?>

            </div>
          </div>
        </div>

        <div class="modal fade popconfirm-modal" tabindex="-1" role="dialog" id="modalIncorrect">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Url errato</h5>
              </div>
              <div class="modal-body">
                <p>L'url inserito non è corretto</p>
              </div>
              <div class="modal-footer">
                <button class="btn btn-primary btn-sm" type="button" data-bs-dismiss="modal">Ok</button>
              </div>
            </div>
          </div>
        </div>
        
      </div>
    </div>
  </div>
</body>

</html>