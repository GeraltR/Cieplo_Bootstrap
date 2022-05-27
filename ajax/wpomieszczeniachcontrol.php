<?php
    error_reporting(0);
    session_start();
    require_once "../lib/baza.php";
    require_once "../lib/wpomieszczenieniach.php";

    $db = new myBaza();
    if ($db->CzySesja() != 0){
        $typ = $_POST['typ'];
        $wpomieszcz = new WPomieszczeniach;
        if ($typ === 'grzejniki'){
            header('Content-Type: application/json');
            $filtrA = $_POST['filtrA'];
            $filtrB = $_POST['filtrB'];
            $poczatek = $_POST['poczatek'];
            $ile = $_POST['ile'];
            $wynik = $wpomieszcz->WidokGrejnikow($filtrA, $filtrB, $poczatek, $ile);
            $reccount = $wpomieszcz->IleGrzejnikow($filtrA, $filtrB);
            echo json_encode(array($wynik, $reccount));
        }
        elseif ($typ === 'podzielniki'){
            header('Content-Type: application/json');
            $filtr = $_POST['filtr'];
            $poczatek = $_POST['poczatek'];
            $ile = $_POST['ile'];
            $wynik = $wpomieszcz->WidokPodzielnikow($filtr, $poczatek, $ile);
            $reccount = $wpomieszcz->IlePodzielnikow($filtr);
            echo json_encode(array($wynik, $reccount));
        }
        elseif ($typ === 'szukajwezla'){
            header('Content-Type: application/json');
            $idlokalu = $_POST['idlokalu'];
            $wynik = $wpomieszcz->SzukajWezla($idlokalu);
            echo json_encode($wynik);
        }
    }


?>