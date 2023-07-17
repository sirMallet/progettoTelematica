<!doctype html>
<html lang="it">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ValURL</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  <script>
    var startTime; // Timer variables
    var elapsedTime;
    var urlRecent = []; // Array that contains the last 5 URLs inserted

    function manageInput() // Function that manages the input 
    {
      const url = document.getElementById('urlText').value;
      const resultTable = document.getElementById("result-table");
      const buttonDropdown = document.getElementById("buttonDropdown");

      var okADD = true;
      var dropdown = document.getElementById("recentDropdown");
      var markup = "";

      // Check if the input is empty
      if (url == "") {
        $('#modalEmpty').modal('show');
        resultTable.innerHTML = "";

      } else {
        extractLinks();

        // Check if the URL is already in the array
        for (i in urlRecent) {
          if (urlRecent[i] == url) {
            okADD = false;
          }
        }

        // Add the URL to the array
        if ((urlRecent.length == 5) && (okADD)) {
          urlRecent.pop();
          urlRecent.unshift(url);
        } else if (okADD) {
          urlRecent.unshift(url);
        }

        // Enable the dropdown button if there are URLs in the array
        if (urlRecent.length > 0) {
          buttonDropdown.setAttribute("data-bs-toggle", "dropdown")

          // Create the dropdown menu
          for (var i = 0; i < urlRecent.length; i++) {
            if (urlRecent[i].length > 190) {
              markup += "<li><button class='dropdown-item' type='button' id=" + i + " >" + urlRecent[i].substring(0, 190) + "..." + "</button></li>";
            } else {
              markup += "<li><button class='dropdown-item' type='button' id=" + i + " >" + urlRecent[i] + "</button></li>";
            }
          }

          dropdown.innerHTML = markup;
        }
      }
    }

    function extractLinks() {
      var resultTable = document.getElementById("result-table");
      const urlText = document.getElementById('urlText').value;
      url = new URL(urlText);
      const domain = url.hostname;

      // Fetch the HTML content of the web page
      startTime = new Date().getTime();
      var statusCode;

      fetch(url)
        .then(response => {
          var endTime = new Date().getTime();
          elapsedTime = endTime - startTime; // Calc the elapsed time

          statusCode = response.status + " " + response.statusText; // Get the status code

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

          // Create the table
          var markup = "<td class='align-middle'>";
          if ((url.protocol == 'https:') ? markup += "HTTPS Supportato" : markup += "HTTPS non Supportato");

          markup += "; Load Time: " + elapsedTime + "ms; Status: " + statusCode;

          markup += "</td>";


          markup += "<tr><th>Link Disponibili</th></tr>";
          markup += "<tr>";

          for (var i = 0; i < linkUrls.length; i++) {

            markup += "<tr>";
            markup += "<td class='align-left'> <button type='button' name='examineButton' class='btn' data-bs-toggle='modal' id='" + linkUrls[i] + "'>" + linkUrls[i] + "</button> </td>";
            markup += "</tr>";
          }

          resultTable.innerHTML = markup;
          
        })
        .catch(error => {
          // If the fetch fails, show an error modal

          var modalNoGETBodyTextID = document.getElementById("modalNoGETBodyText");

          var markup = "";
          if ((error.status != undefined) && (error.statusText != undefined)) {
            modalNoGETBodyTextID.innerHTML = "Non è possibile accedere alle informazioni di questo link. Errore: " + error.status + " " + error.statusText;
          } else {
            modalNoGETBodyTextID.innerHTML = "Non è possibile accedere alle informazioni di questo link.";
          }

          resultTable.innerHTML = "";
          $('#modalNoGET').modal('show');
        });
    }


    // Event listener for the sendButton
    $(document).on('click', 'button[name="sendButton"]', function() {
      manageInput();
    });

    // Event listener for the send button when the user press enter
    document.addEventListener("keydown", function(event) {
      if (event.keyCode === 13) {
        event.preventDefault(); // Previene l'invio del modulo se presente
        manageInput()
      }
    });

    // Event listener for the dropdown menu (when the element is selected)
    $(document).on('click', 'button[class="dropdown-item"]', function() {
      var urlInput = document.getElementById('urlText');

      urlInput.value = urlRecent[$(this).attr('id')];
      manageInput();
    });

    // Event listener for links in the table to get more info
    $(document).on('click', 'button[name="examineButton"]', function() {
      var url = $(this).attr('id');
      var examineTitle = document.getElementById("modalExamineTitle");
      var headersTable = document.getElementById("headers-table");
      examineTitle.innerHTML = url;

      startTime = new Date().getTime();
      $.ajax({
        url: url,
        method: 'HEAD',
        success: function(data, textStatus, jqXHR) {
          var endTime = new Date().getTime();
          var elapsedTimeElement = endTime - startTime; // Calc the elapsed time

          $('#urlInfoModal').modal('show');

          // Get the status code
          var statusCode = jqXHR.status;
          if (jqXHR.statusText != "nocontent") {
            statusCode += " " + jqXHR.statusText;
          }

          // Get the available headers
          const headers = jqXHR.getAllResponseHeaders();
          const headersList = headers.split("\n");
          console.log(headersList);

          // Create the table
          var markup = "<td class='align-middle'>";
          if ((url.protocol == 'https:') ? markup += "HTTPS Supportato" : markup += "HTTPS non Supportato");
          markup += "; Load Time: " + elapsedTimeElement + "ms; Status: " + statusCode + "</td>";

          markup += "<tr><th>Headers</th></tr>";

          for (var i = 0; i < headersList.length; i++) {
            markup += "<tr>";
            markup += "<td class='align-middle'>" + headersList[i] + "</td>";
            markup += "</tr>";
          }

          headersTable.innerHTML = markup;
        },

        error: function(data, textStatus, jqXHR) {
          // If the ajax fails, show an error modal


          var modalNoGETBodyTextID = document.getElementById("modalNoGETBodyText");

          var markup = "";
          if ((jqXHR.status != undefined) && (jqXHR.statusText != undefined)) {
            modalNoGETBodyTextID.innerHTML = "Non è possibile accedere alle informazioni di questo link. Errore: " + error.status + " " + error.statusText;
          } else {
            modalNoGETBodyTextID.innerHTML = "Non è possibile accedere alle informazioni di questo link.";
          }

          $('#modalNoGET').modal('show');
        }
      });
    });

    // Event listener for the validate button in the modal
    $(document).on('click', 'button[name="validateThis"]', function() {
      var url = document.getElementById("modalExamineTitle").innerHTML;
      var urlInput = document.getElementById('urlText');
      $('#urlInfoModal').modal('hide');

      urlInput.value = url;
      manageInput();
    });
  </script>
</head>

<body>
  <div class="container-fluid" style="overflow: hidden;">
    <div class="col">
      <!-- Header -->
      <div class="row">
        <div class="col">
          <div class="card border-0">
            <div class="card-body">
              <h2 class="card-title">ValURL</h2>
            </div>
          </div>
        </div>
      </div>

      <!-- Input -->
      <div class="row">
        <div class="col-md-11">
          <div class="card border-0">
            <div class="card-body">
              <div class="input-group">
                <input type="url" autocomplete="off" class="form-control" id="urlText" name="urlText" placeholder="URL">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="buttonDropdown" aria-expanded="false">Recenti</button>
                <ul class="dropdown-menu" id="recentDropdown"></ul>
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

      <!-- Table -->
      <div class="row">
        <div class="col">
          <div class="card">
            <div class="card-body">
              <div id="tabella" style="height: 70vh; overflow-y: auto;">
                <table class='table table-hover' id="result-table">
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <footer class="footer text-dark" style="position: sticky; bottom: 0;">
        <div class="container-fluid">
          <div class="row">
            <hr class="border-0">
            <div class="col-md-12">
              <h5>ValURL Project</h5>
              <p>Progetto di Telematica realizzato da Alex Antonpio Notore [318861] e Riccardo Mazza[321655]</p>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </div>
</body>

<!-- Modals -->
<div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" id="urlInfoModal">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">

      <div class="modal-header">
        <label id="modalExamineTitle"></label>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div id="tabella">
          <table class='table table-hover' id="headers-table">
          </table>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-primary" name="validateThis" type="button" data-bs-dismiss="modal">Valida</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade popconfirm-modal" tabindex="-1" role="dialog" id="modalEmpty">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Nessun URL inserito</h5>
      </div>
      <div class="modal-body">
        <p>Inserire un URL per effettuare la validazione</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" type="button" data-bs-dismiss="modal">Ok</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade popconfirm-modal" tabindex="-1" role="dialog" id="modalNoGET">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Errore</h5>
      </div>
      <div class="modal-body">
        <p id="modalNoGETBodyText"></p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" type="button" data-bs-dismiss="modal">Ok</button>
      </div>
    </div>
  </div>
</div>

</html>