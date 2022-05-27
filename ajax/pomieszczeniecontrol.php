<?php
    error_reporting(0);
    session_start();
    require_once "../lib/baza.php";
    require_once "../lib/pomieszczenie.php";
    require_once "../lib/grzepodzwpom.php";

    $db = new myBaza();
    if ($db->CzySesja() != 0){
        $pomieszczenie = new pomieszczenie;
        $typ = $_POST['typ'];
        if ($typ === 'danepomieszczenia'){
            header('Content-Type: application/json');
            $idpomieszczenia = $_POST['idpomieszczenia'];
            $lista = $pomieszczenie->DajListeRodzajowPomieszczen();
            $wynik = $pomieszczenie->DajDanePomieszczenia($idpomieszczenia);
            echo json_encode(array($lista, $wynik));
        }
        elseif($typ === 'dajlisterodzajow'){
            header('Content-Type: application/json');
            $idlokalu = $_POST['idlokalu'];
            $idbudynku = $_POST['idbudynku'];
            $wynik = $pomieszczenie->DajListeRodzajowPomieszczen();
            echo json_encode(array($idlokalu, $idbudynku, $wynik));
        }
        elseif ($typ === 'zapisz'){
            $idlokalu = $_POST['idlokalu'];
            $idbudynku = $_POST['idbudynku'];
            $idrodzaju = $_POST['idrodzaju'];
            $powierzchnia = $_POST['powierzchnia'];
            $idpomieszczenia =  $_POST['idpomieszczenia'];
            if ($idpomieszczenia != 0)
                $idpomieszczenia = $pomieszczenie->Zapisz($idpomieszczenia, $idlokalu, $idbudynku, $idrodzaju, $powierzchnia);
            else {
                $datainstalacjigrze = $_POST['datainstalacjigrze'];
                $idkq = $_POST['idkq'];
                $typpodz = $_POST['typpodz'];
                $numerfab = $_POST['numerfab'];
                $wskazanie = $_POST['wskazanie'];
                $datainstalacjipodz = $_POST['datainstalacjipodz'];
                $idpomieszczenia = $pomieszczenie->Zapisz($idpomieszczenia, $idlokalu, $idbudynku, $idrodzaju, $powierzchnia);
                if ($idpomieszczenia != 0){
                    $grzejnik = new GrzePodzWPom();
                    $idgrzejnika = $grzejnik->ModyfikacjaGrzejnika($idpomieszczenia, 0, $idkq, $datainstalacjigrze);
                    $grzejnik->ModyfikacjaPodzielnika($idgrzejnika, 0, 0, $typpodz, $numerfab, $wskazanie, $datainstalacjipodz, $datainstalacjipodz);
                }
            }
            echo $idlokalu;
        }
        elseif ($typ === 'listapomieszczeń'){
            header('Content-Type: application/json');
            $idlokalu = $_POST['idlokalu'];
            $wynik = $pomieszczenie->ListaPomieszczen($idlokalu);
            echo json_encode(array($wynik, $idlokalu));
        }
        elseif ($typ === 'czymoznausunac'){
            header('Content-Type: application/json');
            $idpomieszczenia = $_POST['idpomieszczenia'];
            $idlokalu = $_POST['idlokalu'];
            $wynik = $pomieszczenie->CzyMoznaUsunacPomieszczenie($idpomieszczenia);
            echo json_encode(array($wynik, $idlokalu, $idpomieszczenia));
        }
        elseif ($typ === 'usuwaniepomieszczenia'){
            $idpomieszczenia = $_POST['idpomieszczenia'];
            $pomieszczenie->UsunPomieszczenie($idpomieszczenia);
            echo 0;
        }
    }
?>