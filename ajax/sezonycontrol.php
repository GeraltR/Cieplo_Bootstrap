<?php
    error_reporting(0);
    session_start();
    require_once "../lib/baza.php";
    require_once "../lib/sezony.php";

    $db = new myBaza();
    if ($db->CzySesja() != 0){
        $typ = $_POST['typ'];
        if (!empty($typ)){
            $sezon = new Sezon;
            if ($typ === 'lista'){
                header('Content-Type: application/json');
                $idbudynku = $_POST['idbudynku'];
                if (empty($idbudynku))
                    $idbudynku = 0;
                $wynik = $sezon->Lista($idbudynku);
                echo json_encode($wynik);
            }
            elseif ($typ === 'widok'){
                header('Content-Type: application/json');
                $idsezonu = $_POST['idsezonu'];
                $wynik = $sezon->WidokSezonow($idsezonu);
                echo json_encode($wynik);
            }
            elseif ($typ === 'update'){
                $idsezonu = $_POST['idsezonu'];
                $nazwa = $_POST['nazwa'];
                $poczatek = $_POST['poczatek'];
                $koniec = $_POST['koniec'];
                $wynik = $sezon->UpdateSezon($idsezonu, $nazwa, $poczatek, $koniec);
                echo $wynik;
            }
            elseif ($typ === 'czymoznausunac'){
                header('Content-Type: application/json');
                $idsezonu = $_POST['idsezonu'];
                $wynik = $sezon->CzyMoznaUsunac($idsezonu);
                echo json_encode(array($wynik, $idsezonu));
            }
            elseif ($typ === 'usunsezon'){
                $idsezonu = $_POST['idsezonu'];
                $wynik = $sezon->UsunSezon($idsezonu);
                echo $wynik;
            }
            elseif ($typ === 'usunbudynekzsezonu'){
                $idbudynku = $_POST['idbudynku'];
                $wynik = $sezon->UsunBudynekZSezonu($idbudynku);
                echo $wynik;
            }
            elseif ($typ === 'dodajbudynekdosezonu'){
                $idsezonu = $_POST['idsezonu'];
                $idbudynku = $_POST['idbudynku'];
                $wynik = $sezon->DodajBudynekDoSezonu($idsezonu, $idbudynku);
                echo $wynik;
            }
            elseif ($typ === 'formularzodczytow'){
                header('Content-Type: application/json');
                $idbudynku = $_POST['idbudynku'];
                $dataodczytu = $_POST['dataodczytu'];
                $wynik = $sezon->FormularzDoOdczytowPodzielnikow($idbudynku, $dataodczytu);
                $listadat = $sezon->DajDatyOdczytowPodzielnikow($idbudynku);
                array_push($wynik, $listadat);
                echo json_encode($wynik);
            }
            elseif ($typ === 'dataodczytu'){
                header('Content-Type: application/json');
                $idbudynku = $_POST['idbudynku'];
                $wynik = $sezon->DodajDateOdczytu($idbudynku);
                echo json_encode(array($wynik, $idbudynku));
            }
            elseif ($typ === 'zapiswartosciodczytu'){
                header('Content-Type: application/json');
                $idpodz= $_POST['idpodz'];
                $dataodczytu= $_POST['dataodczytu'];
                $bezodczytu= $_POST['bezodczytu'];
                $wartosc= $_POST['wartosc'];
                $numerpola= $_POST['numerpola'];
                $wynik = $sezon->ZapiszOdczyt($idpodz,$wartosc,$dataodczytu,$bezodczytu);
                echo json_encode(array($wynik, $numerpola));
            }
            elseif ($typ === 'pocztekkoniec'){
                header('Content-Type: application/json');
                $idsezonu = $_POST['idsezonu'];
                $wynik = array(DajPolskaDate($sezon->SezonPoczatek($idsezonu)), DajPolskaDate($sezon->SezonKoniec($idsezonu)));
                echo json_encode($wynik);
            }
            elseif ($typ === 'dajsezondlabudynku'){
                header('Content-Type: application/json');
                $idlokalu = $_POST['idlokalu'];
                $idbudynku = $_POST['idbudynku'];
                $wynik = $sezon->DajSezonDlaBudynku($idbudynku);
                echo json_encode(array($wynik, $idlokalu));
            }
            elseif ($typ === 'dajsezondlalokalu'){
                header('Content-Type: application/json');
                $idlokalu = $_POST['idlokalu'];
                $wynik = $sezon->DajSezonDlaLokalu($idlokalu);
                echo json_encode($wynik);
            }
            elseif ($typ === 'czyjestsezondlabudynku'){
                $idbudynku = $_POST['idbudynku'];
                $wynik = $sezon->DajSezonDlaBudynku($idbudynku);
                echo $wynik;
            }
        }
    }
?>