<?php
    error_reporting(0);
    session_start();
    require_once "../lib/baza.php";
    require_once "../lib/wydruki.php";

    $db = new myBaza();
    if ($db->CzySesja() != 0){
        $wydruk = new Wydruk;
        $typ = $_POST['typ'];
        if ($typ === 'protokolodczytu'){
        $idbudynku = $_POST['idbudynku'];
        $wynik = $wydruk->ProtokolyOdczytu($idbudynku, 0);
        echo $wynik;
        }
    }

?>