<?php
    error_reporting(0);
    session_start();
    require_once "../lib/baza.php";
    require_once "../lib/grzejnik.php";

    $db = new myBaza();
    if ($db->CzySesja() != 0){
        $typ = $_POST['typ'];
        $grzejnik = new Grzejnik;
        if ($typ === 'widokproducenta'){
            header('Content-Type: application/json');
            $filtr = $_POST['filtr'];
            $poczatek = $_POST['poczatek'];
            $ile = $_POST['ile'];
            $wynik = $grzejnik->WidokProducentaGrzejnikow($filtr, $poczatek);
            echo json_encode($wynik);
        }
        elseif ($typ === 'zapiszproducentagrzejnika'){
            header('Content-Type: application/json');
            $idproducenta = $_POST['idproducenta'];
            $nazwa = $_POST['nazwa'];
            $wynik = $grzejnik->ZapiszProducentaGrzejnika($idproducenta, $nazwa);
            echo json_encode(array($wynik, $nazwa));
        }
        else if ($typ === 'czymoznausunacproducentagrzejnika'){
            header('Content-Type: application/json');
            $idproducenta = $_POST['idproducenta'];
            $wynik = $grzejnik->CzyMoznaUsunacProducentaGrzejnika($idproducenta);
            echo json_encode(array($wynik, $idproducenta));
        }
        elseif ($typ === 'usunproducentagrzejnika'){
            $idproducenta = $_POST['idproducenta'];
            $wynik = $grzejnik->UsunProducentaGrzejnika($idproducenta);
            echo $wynik;
        }
        elseif ($typ === 'widokrodzaju'){
            header('Content-Type: application/json');
            $filtr = $_POST['filtr'];
            $idproducenta = $_POST['idproducenta'];
            $poczatek = $_POST['poczatek'];
            $ile = $_POST['ile'];
            $wynik = $grzejnik->WidokRodzajowGrzejnikow($idproducenta, $filtr, $poczatek);
            echo json_encode($wynik);
        }
        elseif ($typ === 'zapiszrodzajgrzejnika'){
            $idrodzaju = $_POST['idrodzaju'];
            $idproducenta = $_POST['idproducenta'];
            $wymiar = $_POST['wymiar'];
            $kc =  $_POST['kc'];
            $kchf = $_POST['kchf'];
            $wynik = $grzejnik->ZapiszRodzajGrzejnika($idrodzaju, $idproducenta, $wymiar, $kc, $kchf);
            echo $wynik;
        }
        elseif ($typ === 'czymoznausunacrodzajgrzejnika'){
            header('Content-Type: application/json');
            $idrodzaju = $_POST['idrodzaju'];
            $wynik = $grzejnik->CzyMoznaUsunacRodzajGrzejnika($idrodzaju);
            echo json_encode(array($wynik, $idrodzaju));
        }
        elseif ($typ === 'usunrodzajgrzejnika'){
            $idrodzaju = $_POST['idrodzaju'];
            $wynik = $grzejnik->UsunRodzajGrzejnika($idrodzaju);
            echo $wynik;
        }
        elseif ($typ === 'widokwspolczynnika'){
            $idrodzajugrzejnika = $_POST['idrodzajugrzejnika'];
            $wynik = $grzejnik->WidokWspKQ($idrodzajugrzejnika);
            echo $wynik;
        }
        elseif ($typ === 'zapiszwspolczynnik'){
            $idrodzaju = $_POST['idrodzaju'];
            $idwspolczynnika = $_POST['idwspolczynnika'];
            $ilosczeberek = $_POST['ilosczeberek'];
            $wspolczynnik = $_POST['wspolczynnik'];
            $wynik = $grzejnik->ZapiszWspolczynnik($idwspolczynnika, $idrodzaju, $ilosczeberek, $wspolczynnik);
            echo $wynik;
        }
        elseif ($typ === 'czymoznausunacwspolczynnik'){
            header('Content-Type: application/json');
            $idwspolczynnika = $_POST['idwspolczynnika'];
            $wynik = $grzejnik->CzyMoznaUsunacWspolczynnik($idwspolczynnika);
            echo json_encode(array($wynik, $idwspolczynnika));
        }
        elseif ($typ === 'usunwspolczynnik'){
            $idwspolczynnika = $_POST['idwspolczynnika'];
            $wynik = $grzejnik->UsunWspolczynnik($idwspolczynnika);
            echo $wynik;
        }
    }

?>