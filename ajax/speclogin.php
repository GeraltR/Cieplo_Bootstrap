<?php
    error_reporting(0);
    session_start();
    require_once '../lib/stale.php';
    require_once '../lib/baza.php';

    $db = new myBaza();
    if ($db->CzySesja() != 0){
        $typ = $_POST['typ'];
        if ($typ === 'login'){
            $stale = new Stale;
            $tmp = $_POST['haslo'];
            if (Weryfikacja hasła)
                $wynik = 1;
            else
                $wynik = 0;
            echo $wynik;
        }
        elseif ($typ === 'wykonajzdanie'){
            $zdanie = $_POST['zdanie'];
            $wynik = 0;
            if (strlen($zdanie) > 0){
                $db = new myBaza();
                $wynik = $db->Execute($zdanie);
            }
            echo $wynik;
        }
    }

?>