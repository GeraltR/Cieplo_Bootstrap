<?php
    error_reporting(0);
    session_start();
    require_once "../lib/baza.php";
    require_once "../lib/pracownicy.php";

    $db = new myBaza();
    if ($db->CzySesja() != 0){
        $typ = $_POST['typ'];
        $pracownicy = new Pracownicy();
        if ($typ === 'sprawdz') {
            $haslo = $_POST['haslo'];
            $kto = $_SESSION['pracownik'];
            if (strlen($kto) > 1){
                $id = $db->Zaloguj($kto, $haslo);
                if ($id == 0)
                    $wynik = 1;
                elseif ($id = $_SESSION['idkto'])
                    $wynik = 0;
                else $wynik = 3;
            }
            else
                $wynik = 2;
            echo $wynik;
        }
        elseif ($typ === 'zmienhaslo'){
            $haslo = $_POST['haslo'];
            $idkto = $_SESSION['idkto'];
            $wynik = 0;
            if (strlen($haslo) > 6){
                $wynik = $db->ZmienHaslo($idkto, $haslo);
            }
            echo $wynik;
        }
        elseif ($typ === 'widokpracownikow'){
        $wynik = $pracownicy->WidokPracownikow();
        echo $wynik;
        }
        elseif ($typ === 'dajpracownika'){
            header('Content-Type: application/json');
            $idpracownika = $_POST['idpracownika'];
            $wynik = $pracownicy->DajDanePracownika($idpracownika);
            echo json_encode($wynik);
        }
        elseif ($typ === 'sprawdzpracownika'){
            $login = $_POST['login'];
            $imie = $_POST['imie'];
            $nazwisko = $_POST['nazwisko'];
            $haslo = $_POST['haslo'];
            $czyadmin = $_POST['czyadmin'];
            if (!isset($imie))
                $imie = '';
            if (!isset($nazwisko))
                $nazwisko = '';
            $wynik = $pracownicy->SprawdzPracownika($login);
            header('Content-Type: application/json');
            echo json_encode(array($wynik, $login, $imie, $nazwisko, $haslo, $czyadmin));
        }
        elseif ($typ === 'zapiszpracownika'){
            $idpracownika = $_POST['idpracownika'];
            $login = $_POST['login'];
            $imie = $_POST['imie'];
            $nazwisko = $_POST['nazwisko'];
            $haslo = $_POST['haslo'];
            $czyadmin = $_POST['czyadmin'];
            if (!isset($imie))
                $imie = '';
            if (!isset($nazwisko))
                $nazwisko = '';
            $wynik = $pracownicy->ZapiszPracownika($idpracownika, $login, $imie, $nazwisko, $haslo, $czyadmin);
            echo $wynik;
        }
        elseif ($typ === 'usunpracownika'){
            $idpracownika = $_POST['idpracownika'];
            if ($idpracownika != $_SESSION['idkto']){
                $pracownicy->UsunPracownika($idpracownika);
            }
            echo 0;
        }
    }
?>