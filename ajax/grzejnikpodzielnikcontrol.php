<?php
    error_reporting(0);
    session_start();
    require_once "../lib/baza.php";
    require_once "../lib/grzepodzwpom.php";

    $db = new myBaza();
    if ($db->CzySesja() != 0){
        $typ  = $_POST ['typ'];
        $grzepodz = new GrzePodzWPom;
        if ($typ === 'grzejnik'){
            header('Content-Type: application/json');
            $idpomieszczenia = $_POST['idpomieszczenia'];
            $wynik = $grzepodz->WidokGrzejnikowWPom($idpomieszczenia);
            echo json_encode($wynik);
        }
        elseif ($typ === 'podzielnik'){
            header('Content-Type: application/json');
            $idgrzejnika = $_POST['idgrzejnika'];
            $wynik = $grzepodz->WidokPodzielnikow($idgrzejnika);
            echo json_encode($wynik);
        }
        elseif ($typ === 'listarodzajpodzielnika'){
            header('Content-Type: application/json');
            $idpodzielnika = $_POST['idpodzielnika'];
            $idodczytu = $_POST['idodczytu'];
            $wynik = $grzepodz->DajListeRodzPodz($idpodzielnika, $idodczytu);
            echo json_encode($wynik);
        }
        elseif ($typ === 'listarodzajpodzielnikapomieszcz'){
            header('Content-Type: application/json');
            $idlokalu = $_POST['idlokalu'];
            $idbudynku = $_POST['idbudynku'];
            $lista = $_POST['lista'];
            $listagrz = $_POST['listagrz'];
            $wynik = $grzepodz->DajListeRodzPodz(0, 0);
            echo json_encode(array($wynik, $idlokalu, $idbudynku, $lista, $listagrz));
        }
        elseif ($typ === 'update'){
            $idodczytu = $_POST['idodczytu'];
            $idpodzielnika = $_POST['idpodzielnika'];
            if (!isset($_POST['idgrzejnika']))
                $idgrzejnika = 0;
            else
                $idgrzejnika = $_POST['idgrzejnika'];
            if ($idgrzejnika == 0)
                $idgrzejnika = $wynik = $grzepodz->DajIdZG($idpodzielnika);
            $typpodz = $_POST['typpodz'];
            $numerfab = $_POST['numerfab'];
            $wskazanie = $_POST['wskazanie'];
            $wskazanie = str_replace(' ', '', str_replace(',', '.', $wskazanie));
            $datainstalacji = $_POST['datainstalacji'];
            $dataodczytu = $_POST['dataodczytu'];
            $grzepodz->ModyfikacjaPodzielnika($idgrzejnika, $idpodzielnika, $idodczytu, $typpodz, $numerfab, $wskazanie, $datainstalacji, $dataodczytu);
            echo $idgrzejnika;
        }
        elseif ($typ === 'demontaz'){
            $idpodzielnika = $_POST['idpodzielnika'];
            $wskazanie = $_POST['wskazanie'];
            $wskazanie = str_replace(' ', '', str_replace(',', '.', $wskazanie));
            $datazdjecia = $_POST['datazdjecia'];
            $dataodczytu = $_POST['dataodczytu'];
            $grzepodz->ZdjeciePodzielnika($idpodzielnika, $wskazanie, $datazdjecia, $dataodczytu);
            $wynik = $grzepodz->DajIdZG($idpodzielnika);
            echo $wynik;
        }
        elseif ($typ === 'listaproducentow'){
            header('Content-Type: application/json');
            $idpomieszczenia = $_POST['idpomieszczenia'];
            $idgrzejnika = $_POST['idgrzejnika'];
            $wynik = $grzepodz->ListaProducentowGrzejnika();
            $idproducenta = $grzepodz->DajIdProducenta($idgrzejnika);
            echo json_encode(array ($wynik, $idpomieszczenia, $idgrzejnika, $idproducenta));
        }
        elseif ($typ === 'listaproducentowpomieszcz'){
            header('Content-Type: application/json');
            $idlokalu = $_POST['idlokalu'];
            $idbudynku = $_POST['idbudynku'];
            $lista = $_POST['lista'];
            $wynik = $grzepodz->ListaProducentowGrzejnika();
            echo json_encode(array ($wynik, $idlokalu, $idbudynku, $lista));
        }
        elseif ($typ === 'listarodzajow'){
            header('Content-Type: application/json');
            $idproducenta = $_POST['idproducenta'];
            $idgrzejnika = $_POST['idgrzejnika'];
            if (!empty($idproducenta)){
                $wynik = $grzepodz->ListaRodzajowGrzejnika($idproducenta);
                $idrodzaju = $grzepodz->DajIdRodzajGrzej($idgrzejnika);
            }
            else {
                $wynik = "";
                $idrodzaju = 0;
            }
            echo json_encode(array($wynik,$idproducenta,$idgrzejnika,$idrodzaju));
        }
        elseif ($typ === 'listazeberek'){
            header('Content-Type: application/json');
            $idgrzejnika = $_POST['idgrzejnika'];
            $idrodzaju = $_POST['idrodzaju'];
            if (!empty($idrodzaju)){
                $wynik = $grzepodz->ListaIlosciZeberek($idrodzaju);
                $idkq = $grzepodz->DajId_KQ($idgrzejnika);
            }
            else {
                $wynik = "";
                $idkq = 0;
            }
            echo json_encode(array($wynik,$idgrzejnika,$idrodzaju,$idkq));
        }
        elseif ($typ === 'updateGrzejnik'){
            $idpomieszczenia = $_POST['idpomieszczenia'];
            $idgrzejnika = $_POST['idgrzejnika'];
            $idkq = $_POST['idkq'];
            $datainstalacji = $_POST['datainstalacji'];
            $wynik = $grzepodz->ModyfikacjaGrzejnika($idpomieszczenia, $idgrzejnika, $idkq, $datainstalacji);
            echo $idpomieszczenia;
        }
        elseif ($typ === 'zdemontujgrzejnik'){
            $idpomieszczenia = $_POST['idpomieszczenia'];
            $idgrzejnika = $_POST['idgrzejnika'];
            $datazdjecia = $_POST['datazdjecia'];
            $wynik = $grzepodz->ZdemontujGrze($idpomieszczenia, $idgrzejnika, $datazdjecia);
            echo $wynik;
        }
        elseif ($typ === 'zmianastatusuodczytu'){
            $idodczytu = $_POST['idodczytu'];
            $idgrzejnika = $_POST['idgrzejnika'];
            $wynik = $grzepodz->ZmianaStatusOdczytu($idodczytu);
            echo $idgrzejnika;
        }
    }
?>