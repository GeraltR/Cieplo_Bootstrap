<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../img/logo.ico">
    <title>Rozliczenie energii cieplnej - wydruk</title>
    <meta name="author" content="Paweł Mętelski and Bootstrap contributors">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link href="../css/printer.css" rel="stylesheet">
    <link href="../css/rozliczeniewglokali.css" rel="stylesheet">
</head>
<body class="fill" style="margin-bottom: 20px;">
    <!-- **********  KOMUNIKAT BŁĄD *************-->
    <div class="modal fade" id="komunikatBlad" tabindex="-1" role="dialog" aria-labelledby="komunikatBladTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header modal-red" id="modalBlad">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="komunikatBladLongTitle">Błąd</h4>
                </div>
                <div class="modal-body" id="komunikatTekstBody">
                    <p id="konunikatTekstBlad-p">Wystąpił błąd.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
                </div>
            </div>
        </div>
    </div>
    <div id="spinnerwaitborder" class="spinnerborder">
        <div class="d-flex justify-content-center">
            <div class="spinner-border text-light" role="status" id="spinnerWait">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>

    <main role="main" class="container" id="mainDiv">


    </main>

    <script src="../js/jquery.js" type="text/javascript"></script>
    <script src="../js/ajax/jquery.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <script src="../js/rozliczeniezbiorowka.js"></script>

</body>
</html>