GUIDA ALL’USO

Per poter far funzionare questa applicazione è necessario disporre di Google Chrome, XAMPP con modulo Apache e un editor di testo, quindi seguire le seguenti istruzioni:

1.  Creare una cartella denominata my in C:\Users\nome-utente\Documents dove nome-utente è la cartella utente del PC utilizzato.

2.  All’interno della cartella my creare un’altra cartella denominata data

3.  Estrarre lo zip allegato all’interno della cartella C:\xampp\htdocs

4.  Modificare il file disable_security.bat, usando un editor di testo, sostituendo:

5.  Il percorso della riga a con il percorso della cartella dove risiede chrome.exe.
    Nella riga b il percorso alla cartella appena data creata

    ES:

    cd C:\Program Files\Google\Chrome\Application

    chrome.exe --user-data-dir="C:\Users\nome-utente\Documents\my\data" --disable-web-security

6.  Eseguire disable_security.bat

7.  Nella finestra Chrome che si apre, accedere a localhost/index.php nella barra indirizzi
