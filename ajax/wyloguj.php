<?php
  session_start();
  require_once "../lib/baza.php";

  $db = new myBaza();
    if ($db->CzySesja() != 0)
      $db->Wyloguj($_SESSION['idkto']);

  unset($_SESSION['wynikLogowania']);
  unset($_SESSION['admin']);
  unset($_SESSION['kto']);
  unset($_SESSION['idkto']);
  session_destroy();

?>