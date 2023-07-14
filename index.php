<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ValURL</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  <script>
    function InviaInput() {
      const urlInput = document.getElementById('formURL');
      var tabella = document.getElementById("tabella-risultati");

      const url = urlInput.value;

      if (url == "") {
        $('#modalIncorrect').modal('show');
        tabella.innerHTML = "";

      } else {
        extractLinks();
      }
    }

    function extractLinks() {
      const urlInput = document.getElementById('formURL');
      var tabella = document.getElementById("tabella-risultati");
      const url = urlInput.value;

      // Fetch the HTML content of the web page
      fetch(url)
        .then(response => response.text())
        .then(html => {
          // Parse the HTML using DOMParser
          const parser = new DOMParser();
          const doc = parser.parseFromString(html, 'text/html');

          // Extract all the <a> elements and their href attributes
          const links = doc.querySelectorAll('a');
          const linkUrls = Array.from(links).map((link) => {
            let href = link.href;
            // Remove the initial portion matching "http://127.0.0.1:5500" and replace it with urlInput
            if (href.startsWith("http://localhost")) {
              href =
                urlInput.value +
                href.substring("http://localhost".length);
            }
            return href;
          });


          var markup = "<tr><th>Link Disponibili</th></tr>";

          for (var i = 0; i < linkUrls.length; i++) {

            markup += "<tr>";

            /*
            Gestire condizione per il colore

            <span class="badge badge-pill badge-success"></span>
            <span class="badge badge-pill badge-danger"></span>
            <span class="badge badge-pill badge-warning"></span>
            */

            markup += "<td class='align-middle'> <button type='button' name='pulsanteEsaminaLink' class='btn' data-bs-toggle='modal' id='" + linkUrls[i] + "'>" + linkUrls[i] + "</button> </td>";
            markup += "</tr>";
          }

          tabella.innerHTML = markup;
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }

    function createTabella() {
      var tabella = document.getElementById("tabella-risultati");
      //var markup = "<tr><th></th><th>Link</th><th>Sicurezza</th><th>Supporto HTTPS</th></tr>";

      var markup = "<tr><th>Link Disponibili</th></tr>";

      for (var i = 0; i < linkUrls.length; i++) {
        markup += "<tr>";

        /*
        Gestire condizione per il colore

        <span class="badge badge-pill badge-success"></span>
        <span class="badge badge-pill badge-danger"></span>
        <span class="badge badge-pill badge-warning"></span>
        */

        markup += "<td class='align-middle'> <button type='button' name='pulsanteEsaminaLink' class='btn' data-bs-toggle='modal' id='" + linkUrls[i] + "'>" + linkUrls[i] + "</button> </td>";
        markup += "</tr>";
      }

      tabella.innerHTML = markup;
    }

    $(document).on('click', 'button[name="sendButton"]', function() {
      InviaInput();
    });

    document.addEventListener("keydown", function(event) {
      if (event.keyCode === 13) {
        event.preventDefault(); // Previene l'invio del modulo se presente
        InviaInput()
      }
    });

    $(document).on('click', 'button[name="pulsanteEsaminaLink"]', function() {
      var url = $(this).attr('id');
      var modaleInfoURLHeader = document.getElementById("modaleInfoURLHeader");
      var headersID = document.getElementById("tabella-headersLink");
      modaleInfoURLHeader.innerHTML = url;

      $.ajax({
        url: url,
        method: 'HEAD',
        success: function(data, textStatus, jqXHR) {
          $('#modaleInfoURL').modal('show');
          const headers = jqXHR.getAllResponseHeaders();
          const headersList = headers.split("\n");

          var markup = "<tr><th>Headers</th></tr>";

          for (var i = 0; i < headersList.length; i++) {
            markup += "<tr>";
            markup += "<td class='align-middle'>" + headersList[i] + "</td>";
            markup += "</tr>";
          }

          headersID.innerHTML = markup;
        },

        error: function(data, textStatus, jqXHR) {
          $('#modalCORSPolicy').modal('show');
        }
      });

    });
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
              <h2 class="card-title">ValURL</h2>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-11">
          <div class="card border-0">
            <div class="card-body">
              <input type="url" autocomplete="off" class="form-control" id="formURL" name="formURL" placeholder="URL">
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
      </div>


      <!-- Controls -->


      <div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" id="modaleInfoURL">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">

            <div class="modal-header">
              <label id="modaleInfoURLHeader"></label>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi finestra modale"></button>
            </div>

            <div class="modal-body">
              <div id="tabella">
                <table class='table table-hover' id="tabella-headersLink">
                </table>
              </div>
            </div>
            <div class="modal-footer">
            </div>
          </div>
        </div>
      </div>
    </div>


    <div class="modal fade popconfirm-modal" tabindex="-1" role="dialog" id="modalIncorrect">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">URL errato</h5>
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

    <div class="modal fade popconfirm-modal" tabindex="-1" role="dialog" id="modalCORSPolicy">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Errore</h5>
          </div>
          <div class="modal-body">
            <p>A causa della politica CORS, non è possibile accedere alle informazioni di questo link.</p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary btn-sm" type="button" data-bs-dismiss="modal">Ok</button>
          </div>
        </div>
      </div>
    </div>

  </div>
  </div>
</body>

</html>