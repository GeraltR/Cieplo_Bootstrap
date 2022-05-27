<?php
    error_reporting(0);
    session_start();
    require_once "../lib/baza.php";
    require_once "../lib/lokale.php";
    require_once "../lib/poziomepolozenie.php";
    require_once "../lib/pionowepolozenie.php";
    require_once "../lib/rozawiatrow.php";
    require_once "../lib/rodzajlokalu.php";
    require_once "../lib/uzytkownik.php";
    require_once "../lib/sezony.php";

    $db = new myBaza();
    if ($db->CzySesja() != 0){
        $tabela= "";
        $lokale = new Lokale;
        $typ = $_POST['typ'];
        if ($typ === 'widok'){
            $budynek = $_POST['budynek'];
            $filtr = $_POST['filtr'];
            if (empty($filtr))
            $filtr = '';
            if (isset($budynek)){
                $tabela = $lokale->WidokLokali($budynek, $filtr);
            }
            echo $tabela;
        }
        else if ($typ === 'widokpokoi') {
            header('Content-Type: application/json');
            $idlokalu = $_POST['idlokalu'];
            $tmp = $lokale->PomieszczeniaIGrzejnki($idlokalu);
            $wynik = array ($idlokalu, $tmp);
            echo json_encode($wynik);
        }
        elseif ($typ === 'katalogidlalokalu'){
            header('Content-Type: application/json');
            $idlokalu = $_POST['idlokalu'];
            $poziome = new PoziomePolozenie;
            $listapoziome = $poziome->ListaPoziomePolozenie();
            $pionowe = new PionowePolozenie;
            $listapionowe = $pionowe->ListaPionowePolozenie();
            $roza = new RozaWiatrow;
            $listaroza = $roza->ListaRozaWiatrow();
            $rodzajLokalu = new RodzajLokalu;
            $uzytkownik = new Uzytkownik;
            $listarodzajulokalu = $rodzajLokalu->ListaRodzajLoakalu();
            $idnieruchomosci = 0;
            $idpionowe = 0;
            $idpoziome = 0;
            $idrozy = 0;
            $idrodzaju = 0;
            $imie = '';
            $nazwisko = '';
            $nazwa = '';
            $listanieruchomosci = $lokale->DajNieruchomosci();
            if ($idlokalu !== 0){
                $idpionowe = $lokale->DajPionowePolozenie($idlokalu);
                $poziome = $lokale->DajPoziomePolozenie($idlokalu);
                $idrozy = $lokale->DajRozeWiatrow($idlokalu);
                $idrodzaju = $lokale->DajRodzajLokalu($idlokalu);
                $idnieruchomosci = $lokale->DajIdNieruch($idlokalu);
                $person = $uzytkownik->DajUzytkownikaLokalu($idlokalu);
                if (!empty($person)){
                    $imie = $person[0];
                    $nazwisko = $person[1];
                    $nazwa = $person[2];
                }
            }
            echo json_encode(array($idlokalu, $listapionowe, $idpionowe,  $listapoziome, $idpoziome, $listaroza,
                                    $idrozy, $listarodzajulokalu, $idrodzaju, $imie, $nazwisko, $nazwa,
                                    $listanieruchomosci, $idnieruchomosci));
        }
        elseif ($typ === 'zmienlokal'){
            $idbudynku = $_POST['idbudynku'];
            $idsezonu = $_POST['idsezonu'];
            $idlokalu = $_POST['idlokalu'];
            $pionowe = $_POST['pionowe'];
            $poziome = $_POST['poziome'];
            $roza = $_POST['roza'];
            $idrodzaju = $_POST['idrodzaju'];
            $numer = $_POST['numer'];
            $powierzchnia = $_POST['powierzchnia'];
            $kodsm = $_POST['kodsm'];
            $kodlok = $_POST['kodlok'];
            $imie = $_POST['imie'];
            $nazwisko = $_POST['nazwisko'];
            $nazwa = $_POST['nazwa'];
            $rmi = $_POST['rmi'];
            $idnieruchomosci = $_POST['idnieruchomosci'];
            $wynik = $lokale->ZmienLokal($idbudynku, $idlokalu, $pionowe, $poziome, $roza, $idrodzaju, $numer, $powierzchnia, $kodsm,
                        $kodlok, $imie, $nazwisko, $nazwa, $rmi, $idnieruchomosci,$idsezonu);
            echo $idbudynku;
        }
        elseif ($typ === 'czymnoznausunaclokal'){
            header('Content-Type: application/json');
            $idlokalu = $_POST['idlokalu'];
            $wynik = $lokale->CzyMoznaUsunac($idlokalu);
            $kodlok = $lokale->DajKodLokalu($idlokalu);
            echo json_encode(array($wynik, $kodlok)) ;
        }
        elseif ($typ === 'usunlokal'){
            $idlokalu = $_POST['idlokalu'];
            $wynik = $lokale->UsunLokal($idlokalu);
            echo $wynik;
        }
        elseif ($typ === 'sprawdzkodlokalu'){
            header('Content-Type: application/json');
            $idlokalu = $_POST['idlokalu'];
            $kodlok = $_POST['kodlok'];
            $wynik = $lokale->SprawdKodLokalu($kodlok, $idlokalu);
            echo json_encode(array($wynik, $idlokalu, $kodlok));
        }
    }
?>
