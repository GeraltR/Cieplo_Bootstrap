<?php

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Import danych</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
  </head>
  <body>
    <div id="spinnerwaitborder" class="spinnerborder">
        <div class="d-flex justify-content-center">
            <div class="spinner-border text-light" role="status" id="spinnerWait">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
      <div class="row">
            <h4 style="margin: 0 auto 0 auto;">Import danych z Starego programu do Ciepło</h4>
        </div>
        <div class="row">
            <button type="button" class="btn btn-outline-secondary" onclick="start(1)" style="margin: 0 auto 0 auto;">Import</button>
            <button type="button" class="btn btn-outline-secondary" onclick="start(0)" style="margin: 0 auto 0 auto;">Odtwórz dane</button>

        </div>
    <div id="tabele">

    </div>
    <div id="import">

    </div>
    <div id="klucze">

    </div>
    <div id="dodatki">

    </div>
    <script src="../js/jquery.js" type="text/javascript"></script>
    <script src="../js/ajax/jquery.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <script src="import.js" type="text/javascript"></script>
</body>
</html>