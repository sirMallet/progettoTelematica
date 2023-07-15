<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ValURL</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


  <script>
    var startTime;
    var elapsedTime;
    var urlRecent = [];

    function InviaInput() {
      const urlInput = document.getElementById('formURL');
      var tabella = document.getElementById("tabella-risultati");
      var buttonDropdown = document.getElementById("buttonDropdown");

      const url = urlInput.value;

      if (url == "") {
        $('#modalIncorrect').modal('show');
        tabella.innerHTML = "";

      } else {
        extractLinks();

        var okADD = true;

        for (i in urlRecent) {
          if (urlRecent[i] == url) {
            okADD = false;
          }
        }
        if ((urlRecent.length == 5) && (okADD)) {
          urlRecent.pop();
          urlRecent.unshift(url);
        } else if (okADD) {
          urlRecent.unshift(url);
        }
        if (urlRecent.length > 0) {
          buttonDropdown.setAttribute("data-bs-toggle", "dropdown")
        }
        createDropdown();
      }
    }

    function createDropdown() {
      var dropdown = document.getElementById("dropdownRecenti");

      var markup = "";
      for (var i = 0; i < urlRecent.length; i++) {
        if (urlRecent[i].length > 50) {
          urlRecent[i] = urlRecent[i].substring(0, 190) + "...";
        }
        markup += "<li><button class='dropdown-item' type='button' id=" + i + " >" + urlRecent[i] + "</button></li>";
      }

      dropdown.innerHTML = markup;
    }


    function extractLinks() {
      var tabella = document.getElementById("tabella-risultati");
      const urlText = document.getElementById('formURL').value;
      url = new URL(urlText);
      const domain = url.hostname;

      // Fetch the HTML content of the web page
      startTime = new Date().getTime(); // Registra il timestamp di inizio
      var status;

      fetch(url)
        .then(response => {
          var endTime = new Date().getTime(); // Registra il timestamp di fine
          elapsedTime = endTime - startTime; // Calcola il tempo trascorso

          status = response.status + response.statusText;

          return response.text();
        })
        .then(data => {
          // Parse the HTML using DOMParser
          const parser = new DOMParser();
          const doc = parser.parseFromString(data, 'text/html');

          // Extract all the <a> elements and their href attributes
          const links = Array.from(doc.getElementsByTagName("a")).map(link => link.href);

          // Replace the domain in the links with the one provided by the user
          const linkUrls = links.map(link => {
            const url = new URL(link);
            url.hostname = domain;
            return url.href;
          })

          var markup = "<td class='align-middle'>";
          if ((url.protocol == 'https:') ? markup += "HTTPS Supportato" : markup += "HTTPS non Supportato");

          markup += "; Load Time: " + elapsedTime + "ms; Status: " + status;

          markup += "</td>";


          markup += "<tr><th>Link Disponibili</th></tr>";
          markup += "<tr>";

          for (var i = 0; i < linkUrls.length; i++) {

            markup += "<tr>";
            markup += "<td class='align-left'> <button type='button' name='pulsanteEsaminaLink' class='btn' data-bs-toggle='modal' id='" + linkUrls[i] + "'>" + linkUrls[i] + "</button> </td>";
            markup += "</tr>";
          }

          tabella.innerHTML = markup;
        })
        .catch(error => {
          tabella.innerHTML = "<td class='align-middle'>Nessun link disponibile</td>";
        });
    }


    $(document).on('click', 'button[name="sendButton"]', function() {
      InviaInput();
    });

    $(document).on('click', 'button[class="dropdown-item"]', function() {
      var urlInput = document.getElementById('formURL');
      var link = $(this).id();

      urlInput.value = urlRecent[link];
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

      startTime = new Date().getTime(); // Registra il timestamp di inizio
      $.ajax({
        url: url,
        method: 'HEAD',
        success: function(data, textStatus, jqXHR) {
          var endTime = new Date().getTime(); // Registra il timestamp di fine
          var elapsedTimeElement = endTime - startTime; // Calcola il tempo trascorso
          $('#modaleInfoURL').modal('show');
          const status = jqXHR.status;
          if (jqXHR.statusText != "nocontent") {
            status += " " + jqXHR.statusText;
          }
          const headers = jqXHR.getAllResponseHeaders();
          const headersList = headers.split("\n");

          var markup = "<td class='align-middle'>";
          if ((url.protocol == 'https:') ? markup += "HTTPS Supportato" : markup += "HTTPS non Supportato");
          markup += "; Load Time: " + elapsedTimeElement + "ms; Status: " + status + "</td>";

          markup += "<tr><th>Headers</th></tr>";

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
  <div class="container-fluid" style="overflow: hidden;">
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
              <div class="input-group">
                <input type="url" autocomplete="off" class="form-control" id="formURL" name="formURL" placeholder="URL">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="buttonDropdown" aria-expanded="false">Recenti</button>
                <ul class="dropdown-menu" id="dropdownRecenti"></ul>
              </div>
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
              <div id="tabella" style="height: 70vh; overflow-y: auto;">
                <table class='table table-hover' id="tabella-risultati">
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>


      <footer class="footer text-dark" style="position: sticky; bottom: 0;">
        <div class="container">
          <div class="row">
            <hr class="border-0">
            <div class="col-md-12">
              <h5>ValURL Project</h5>
              <p>Progetto di Telematica realizzato da Alex Antonpio Notore [318861] e Riccardo Mazza[321655]</p>
            </div>
          </div>
        </div>
      </footer>


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