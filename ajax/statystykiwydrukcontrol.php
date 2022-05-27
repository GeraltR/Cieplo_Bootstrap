<?php
    error_reporting(0);
    session_start();
    require_once "../lib/baza.php";
    require_once "../lib/statystyki.php";

    $db = new myBaza();
    if ($db->CzySesja() != 0){
        if (isset($_POST['idsezonu']))
            $idsezonu = $_POST['idsezonu'];
        else
            $idsezonu = 0;
        $nadzien = $_POST['nadzien'];
        $stat = new Statystyka();
        $wynik = $stat->DajStatystyke($idsezonu, $nadzien);
        echo $wynik;
    }

?>